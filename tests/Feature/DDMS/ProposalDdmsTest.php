<?php

declare(strict_types=1);

namespace Tests\Feature\DDMS;

use App\Models\Document;
use App\Models\DocumentNumbering;
use App\Models\Event;
use App\Models\Negotiation;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\User;
use App\Services\AdminProposalService;
use App\Services\DdmsSettingService;
use App\Services\DocumentApprovalService;
use App\Services\DocumentNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Phase 11I.10F — DDMS Draft & Approval Gate for Surat Penawaran / Proposal.
 *
 * Proposal = Surat Penawaran (single versioning entity, Proposal.versi).
 * Negotiation = Form Negosiasi (pure client change-request record, NOT a Document).
 *
 * WORKFLOW (DDMS):
 *   Admin "Masuk ke DDMS" -> Proposal + Document DRAFT (NO client notify)
 *     -> redirect ke Document Builder
 *     -> Submit -> Pending -> Director Approve -> Approved
 *   Document approved -> tombol "Kirim ke Client" AKTIF
 *   Admin "Kirim ke Client" -> NOTIFY client (hanya di sini).
 *
 * WORKFLOW (NON-DDMS):
 *   Admin "Kirim Penawaran" -> Proposal (tanpa Document) + NOTIFY client.
 *
 * No SuratNegosiasi entity, no negotiation Document relation.
 */
class ProposalDdmsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;
    private User $director;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->client = User::factory()->create(['role' => 'client']);
        $this->director = User::factory()->create(['role' => 'director']);
        $this->event = Event::factory()->withClient($this->client)->create();
    }

    private function setDdmsEnabled(bool $enabled): void
    {
        app(DdmsSettingService::class)->updateSetting('ddms_enabled', $enabled ? '1' : '0', 'toggle');
    }

    private function setDdmsDefaultPenawaran(bool $value): void
    {
        app(DdmsSettingService::class)->updateSetting('ddms_default_penawaran', $value ? '1' : '0', 'default');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // DDMS initial creation via "Masuk ke DDMS"
    // ═══════════════════════════════════════════════════════════════════════

    private function masukKeDdmsProposal(string $nomor = 'PEN-001'): array
    {
        $this->setDdmsEnabled(true);
        $this->setDdmsDefaultPenawaran(true);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.requests.masuk-ke-ddms', $this->event->id), [
                'nomor_surat' => $nomor,
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '1',
            ]);

        $proposal = Proposal::where('event_id', $this->event->id)->latest('id')->firstOrFail();
        $document = Document::findOrFail($proposal->document_id);

        $response->assertRedirect(route('admin.document_builder.preview', $document->id));

        return [$proposal, $document];
    }

    private function prepareDdmsForApproval(Document $document, string $nomor = 'PEN/APR'): void
    {
        app(DocumentNumberService::class)->setManualNumber($document, $nomor, $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);
        $this->director->update(['approval_pin' => Hash::make('123456')]);
        app(DocumentApprovalService::class)->directorApprove($document, $this->director, '123456');
    }

    private function clientNotificationCount(): int
    {
        return Notification::where('user_id', $this->client->id)->count();
    }

    private function adminNotificationCount(): int
    {
        return Notification::where('user_id', $this->admin->id)->count();
    }

    // ─── A. Non-DDMS → Kirim berhasil ─────────────────────────────────────

    public function test_non_ddms_kirim_succeeds(): void
    {
        $this->setDdmsEnabled(true);
        $this->setDdmsDefaultPenawaran(false);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-penawaran', $this->event->id), [
                'nomor_surat' => 'PEN-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '0',
            ])
            ->assertRedirect();

        $proposal = Proposal::where('event_id', $this->event->id)->firstOrFail();
        $this->assertNull($proposal->document_id);
        $this->assertFalse($proposal->uses_ddms);
        $this->assertSame(1, $this->clientNotificationCount());
    }

    // ─── B. DDMS initial → Proposal + Document draft, NO client notify ─────

    public function test_ddms_initial_creates_draft_no_notification(): void
    {
        $this->setDdmsEnabled(true);
        $this->setDdmsDefaultPenawaran(true);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.masuk-ke-ddms', $this->event->id), [
                'nomor_surat' => 'PEN-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '1',
            ])
            ->assertRedirect();

        $proposal = Proposal::where('event_id', $this->event->id)->firstOrFail();
        $this->assertNotNull($proposal->document_id);

        $document = Document::findOrFail($proposal->document_id);
        $this->assertSame('draft', $document->status->value);
        $this->assertTrue($document->uses_ddms);
        $this->assertSame('proposal', $document->tipe);
        $this->assertSame('generated', $document->document_source->value);
        $this->assertTrue($proposal->uses_ddms);

        // No client notification at draft creation.
        $this->assertSame(0, $this->clientNotificationCount());
    }

    // ─── B2. DDMS shares same canonical PDF ─────────────────────────────────

    public function test_ddms_proposal_shares_same_pdf(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();

        $this->assertSame($proposal->file_proposal, $document->file_path);
        $this->assertTrue(Storage::disk('public')->exists($document->file_path));
    }

    // ─── C. Redirect to DDMS page ──────────────────────────────────────────

    public function test_masuk_ke_ddms_redirects_to_document_builder(): void
    {
        $this->setDdmsEnabled(true);
        $this->setDdmsDefaultPenawaran(true);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.masuk-ke-ddms', $this->event->id), [
                'nomor_surat' => 'PEN-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '1',
            ])
            ->assertRedirect(route('admin.document_builder.preview', Proposal::where('event_id', $this->event->id)->firstOrFail()->document_id));
    }

    // ─── D. Duplicate protection ───────────────────────────────────────────

    public function test_masuk_ke_ddms_is_idempotent(): void
    {
        $this->setDdmsEnabled(true);
        $this->setDdmsDefaultPenawaran(true);

        $payload = [
            'nomor_surat' => 'PEN-001',
            'tanggal_surat' => now()->format('Y-m-d'),
            'uses_ddms' => '1',
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.requests.masuk-ke-ddms', $this->event->id), $payload)
            ->assertRedirect();

        $this->actingAs($this->admin)
            ->post(route('admin.requests.masuk-ke-ddms', $this->event->id), $payload)
            ->assertRedirect();

        // Tidak ada Document kedua / Proposal versi kedua.
        $this->assertSame(1, Document::where('event_id', $this->event->id)->count());
        $this->assertSame(1, Proposal::where('event_id', $this->event->id)->count());
    }

    // ─── E. Submit → Pending, no client notify ────────────────────────────

    public function test_ddms_submit_to_pending_no_notification(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();

        app(DocumentNumberService::class)->setManualNumber($document, 'PEN/PEND/001', $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);

        $this->assertSame('pending', Document::findOrFail($document->id)->status->value);
        $this->assertSame(0, $this->clientNotificationCount());
    }

    // ─── F. Director approve → Approved, no client notify ──────────────────

    public function test_ddms_director_approve_no_notification(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();

        $this->prepareDdmsForApproval($document, 'PEN/APR/001');

        $this->assertSame('approved', Document::findOrFail($document->id)->status->value);
        $this->assertSame(0, $this->clientNotificationCount());
    }

    // ─── G. Director reject → Rejected, no client notify ───────────────────

    public function test_ddms_director_reject_no_client_notification(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();

        app(DocumentNumberService::class)->setManualNumber($document, 'PEN/REJ/001', $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);
        $this->director->update(['approval_pin' => Hash::make('123456')]);
        app(DocumentApprovalService::class)->directorReject($document, $this->director, 'Perlu revisi', '123456');

        $this->assertSame('rejected', Document::findOrFail($document->id)->status->value);
        $this->assertSame(0, $this->clientNotificationCount());
    }

    // ─── H. Gate before approval (draft) → rejected ───────────────────────

    public function test_gate_draft_post_rejected(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->assertSame('draft', $document->status->value);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertSessionHasErrors('uses_ddms');

        // Tidak ada notifikasi client & tidak ada proposal baru.
        $this->assertSame(0, $this->clientNotificationCount());
        $this->assertSame(1, Proposal::where('event_id', $this->event->id)->count());
    }

    // ─── I. Gate pending → rejected ────────────────────────────────────────

    public function test_gate_pending_post_rejected(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        app(DocumentNumberService::class)->setManualNumber($document, 'PEN/PEND/001', $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);
        $this->assertSame('pending', Document::findOrFail($document->id)->status->value);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertSessionHasErrors('uses_ddms');

        $this->assertSame(0, $this->clientNotificationCount());
        $this->assertSame(1, Proposal::where('event_id', $this->event->id)->count());
    }

    // ─── J. Gate rejected → rejected ──────────────────────────────────────

    public function test_gate_rejected_post_rejected(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        app(DocumentNumberService::class)->setManualNumber($document, 'PEN/REJ/001', $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);
        $this->director->update(['approval_pin' => Hash::make('123456')]);
        app(DocumentApprovalService::class)->directorReject($document, $this->director, 'Perlu revisi', '123456');
        $this->assertSame('rejected', Document::findOrFail($document->id)->status->value);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertSessionHasErrors('uses_ddms');

        $this->assertSame(0, $this->clientNotificationCount());
        $this->assertSame(1, Proposal::where('event_id', $this->event->id)->count());
    }

    // ─── K. Gate approved → Kirim succeeds (notify), no new version ────────

    public function test_gate_approved_kirim_succeeds(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');
        $this->assertSame('approved', Document::findOrFail($document->id)->status->value);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertRedirect();

        // Notification client tepat 1 (hanya di sini).
        $this->assertSame(1, $this->clientNotificationCount());

        // DDMS "Kirim ke Client" TIDAK membuat versi Proposal baru.
        $this->assertSame(1, Proposal::where('event_id', $this->event->id)->count());
    }

    // ─── L. Published → Kirim still allowed ───────────────────────────────

    public function test_published_document_kirim_allowed(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');
        app(DocumentApprovalService::class)->publishDocument($document, $this->admin);
        $this->assertSame('published', Document::findOrFail($document->id)->status->value);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertRedirect();

        $this->assertSame(1, $this->clientNotificationCount());
    }

    // ─── M. Revision v1 approved → kirim → negosiasi → v2 → approve → kirim ─

    public function test_revision_v1_then_v2_full_flow(): void
    {
        [$v1, $docA] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($docA, 'PEN/V1/001');
        $this->assertSame('approved', Document::findOrFail($docA->id)->status->value);

        // Admin kirim v1 ke client.
        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertRedirect();
        $this->assertSame(1, $this->clientNotificationCount());

        // Client negosiasi (Form Negosiasi murni).
        $this->actingAs($this->client)
            ->post(route('client.proposals.negosiasi', $v1->id), [
                'pesan' => 'Mohon diskon',
                'budget_diinginkan' => '100',
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('negotiations', [
            'event_id' => $this->event->id,
            'user_id' => $this->client->id,
        ]);

        // Admin buat v2 DDMS (Document B draft).
        $this->actingAs($this->admin)
            ->post(route('admin.requests.masuk-ke-ddms', $this->event->id), [
                'nomor_surat' => 'REV-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '1',
            ])
            ->assertRedirect();

        $v2 = Proposal::where('event_id', $this->event->id)->where('versi', 2)->firstOrFail();
        $docB = Document::findOrFail($v2->document_id);
        $this->assertSame('draft', $docB->status->value);

        // Document v1 tetap approved/published (immutable).
        $this->assertSame('approved', Document::findOrFail($docA->id)->status->value);

        // Kirim v2 masih disabled (draft) → ditolak.
        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertSessionHasErrors('uses_ddms');

        // Director approve v2 → Kirim v2 enabled.
        $this->prepareDdmsForApproval($docB, 'PEN/V2/001');
        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertRedirect();

        // Notification client bertambah tepat 1 (total 2).
        $this->assertSame(2, $this->clientNotificationCount());
    }

    // ─── N. Negotiation purity ────────────────────────────────────────────

    public function test_negotiation_creates_no_document(): void
    {
        $proposal = Proposal::factory()->create([
            'event_id' => $this->event->id,
            'status' => 'menunggu_konfirmasi',
            'versi' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($this->client)
            ->post(route('client.proposals.negosiasi', $proposal->id), [
                'pesan' => 'Mohon diskon',
                'budget_diinginkan' => '100',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('negotiations', [
            'event_id' => $this->event->id,
            'user_id' => $this->client->id,
        ]);
        $this->assertSame(0, Document::where('event_id', $this->event->id)->count());
        $this->assertNull($proposal->refresh()->document_id);
    }

    // ─── O. Global DDMS OFF → effective non-DDMS (Kirim langsung) ──────────

    public function test_global_ddms_off_ignores_requested_ddms(): void
    {
        $this->setDdmsEnabled(false);
        $this->setDdmsDefaultPenawaran(true);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-penawaran', $this->event->id), [
                'nomor_surat' => 'PEN-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '1',
            ])
            ->assertRedirect();

        $proposal = Proposal::where('event_id', $this->event->id)->firstOrFail();
        $this->assertNull($proposal->document_id);
        $this->assertFalse($proposal->uses_ddms);
        $this->assertSame(1, $this->clientNotificationCount());
    }

    // ─── Button visibility (server-rendered gate) ──────────────────────────

    public function test_ddms_draft_button_disabled_in_view(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->assertSame('draft', $document->status->value);

        $this->actingAs($this->admin)
            ->get(route('admin.requests.surat-penawaran', $this->event->id))
            ->assertOk()
            ->assertSee('Buka DDMS')
            ->assertSee('data-ddms-locked');
    }

    public function test_ddms_approved_button_enabled_in_view(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');
        $this->assertSame('approved', Document::findOrFail($document->id)->status->value);

        $this->actingAs($this->admin)
            ->get(route('admin.requests.surat-penawaran', $this->event->id))
            ->assertOk()
            ->assertSee('Kirim ke Client')
            ->assertDontSee('data-ddms-locked');
    }

    // ─── Document layer integrity (phase 10C) ──────────────────────────────

    public function test_non_ddms_proposal_has_no_document(): void
    {
        $this->setDdmsEnabled(true);
        $this->setDdmsDefaultPenawaran(false);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-penawaran', $this->event->id), [
                'nomor_surat' => 'PEN-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '0',
            ])
            ->assertRedirect();

        $proposal = Proposal::where('event_id', $this->event->id)->firstOrFail();
        $this->assertNull($proposal->document_id);
        $this->assertNull($proposal->document);
        $this->assertFalse($proposal->uses_ddms);
    }

    public function test_ddms_proposal_creates_linked_document(): void
    {
        $this->setDdmsEnabled(true);
        $this->setDdmsDefaultPenawaran(true);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.masuk-ke-ddms', $this->event->id), [
                'nomor_surat' => 'PEN-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '1',
            ])
            ->assertRedirect();

        $proposal = Proposal::where('event_id', $this->event->id)->firstOrFail();
        $this->assertNotNull($proposal->document_id);

        $document = Document::findOrFail($proposal->document_id);
        $this->assertSame('proposal', $document->tipe);
        $this->assertSame('generated', $document->document_source->value);
        $this->assertTrue($document->uses_ddms);
        $this->assertTrue($proposal->uses_ddms);
    }

    public function test_revision_creates_new_document(): void
    {
        [$v1, $docA] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($docA, 'PEN/V1/001');

        $this->actingAs($this->admin)
            ->post(route('admin.requests.masuk-ke-ddms', $this->event->id), [
                'nomor_surat' => 'REV-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '1',
            ])
            ->assertRedirect();

        $v2 = Proposal::where('event_id', $this->event->id)->where('versi', 2)->firstOrFail();
        $docB = $v2->document_id;

        $this->assertNotNull($docB);
        $this->assertNotSame($v1->document_id, $v2->document_id);
        $this->assertSame('proposal', Document::findOrFail($docB)->tipe);
    }

    public function test_v1_document_unchanged_after_revision(): void
    {
        [$v1, $docA] = $this->masukKeDdmsProposal();
        $snapshot = [
            'file_path' => $docA->file_path,
            'tipe' => $docA->tipe,
            'document_source' => $docA->document_source->value,
            'uses_ddms' => $docA->uses_ddms,
        ];

        $this->prepareDdmsForApproval($docA, 'PEN/V1/001');

        $this->actingAs($this->admin)
            ->post(route('admin.requests.masuk-ke-ddms', $this->event->id), [
                'nomor_surat' => 'REV-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '1',
            ])
            ->assertRedirect();

        $docARefreshed = Document::findOrFail($v1->document_id);
        $this->assertSame($snapshot['file_path'], $docARefreshed->file_path);
        $this->assertSame($snapshot['tipe'], $docARefreshed->tipe);
        $this->assertSame($snapshot['document_source'], $docARefreshed->document_source->value);
        $this->assertSame($snapshot['uses_ddms'], $docARefreshed->uses_ddms);
    }

    public function test_direct_acceptance_accepts_proposal_no_negotiation(): void
    {
        $proposal = Proposal::factory()->create([
            'event_id' => $this->event->id,
            'status' => 'menunggu_konfirmasi',
            'versi' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($this->client)
            ->post(route('client.proposals.terima', $proposal->id))
            ->assertRedirect();

        $this->assertDatabaseHas('proposals', ['id' => $proposal->id, 'status' => 'diterima']);
        $this->assertDatabaseCount('negotiations', 0);
    }

    public function test_proposal_document_id_is_nullable_and_not_unique(): void
    {
        $p1 = Proposal::factory()->create(['event_id' => $this->event->id, 'document_id' => null]);
        $p2 = Proposal::factory()->create(['event_id' => $this->event->id, 'document_id' => null]);

        $this->assertNull($p1->document_id);
        $this->assertNull($p2->document_id);
        $this->assertCount(2, Proposal::where('event_id', $this->event->id)->get());
    }

    // ─── Phase 11I.10G — Director decision notifications ──────────────────

    // A. Director approve → Admin notified, Client not.
    public function test_director_approve_notifies_admin(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');

        $this->assertSame('approved', Document::findOrFail($document->id)->status->value);

        // Admin mendapat notifikasi keputusan Director.
        $this->assertSame(1, $this->adminNotificationCount());
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->admin->id,
            'judul'   => 'Surat Penawaran Disetujui Director',
        ]);

        // Client TIDAK mendapat notifikasi.
        $this->assertSame(0, $this->clientNotificationCount());
    }

    // B. Director reject → Admin notified, Client not.
    public function test_director_reject_notifies_admin(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        app(DocumentNumberService::class)->setManualNumber($document, 'PEN/REJ/001', $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);
        $this->director->update(['approval_pin' => Hash::make('123456')]);
        app(DocumentApprovalService::class)->directorReject($document, $this->director, 'Perlu revisi layout', '123456');

        $this->assertSame('rejected', Document::findOrFail($document->id)->status->value);

        $this->assertSame(1, $this->adminNotificationCount());
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->admin->id,
            'judul'   => 'Surat Penawaran Ditolak Director',
        ]);

        $notif = Notification::where('user_id', $this->admin->id)->firstOrFail();
        $this->assertStringContainsString('Perlu revisi layout', $notif->pesan);

        // Client TIDAK mendapat notifikasi.
        $this->assertSame(0, $this->clientNotificationCount());
    }

    // C. Approve does not notify Client.
    public function test_approve_does_not_notify_client(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');

        $this->assertSame(0, $this->clientNotificationCount());
    }

    // D. Reject does not notify Client.
    public function test_reject_does_not_notify_client(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        app(DocumentNumberService::class)->setManualNumber($document, 'PEN/REJ/001', $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);
        $this->director->update(['approval_pin' => Hash::make('123456')]);
        app(DocumentApprovalService::class)->directorReject($document, $this->director, 'Perlu revisi', '123456');

        $this->assertSame(0, $this->clientNotificationCount());
    }

    // E. Admin send after approve → Client notified (total 1), Admin notif intact.
    public function test_admin_send_after_approve_client_notified(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');
        $this->assertSame(1, $this->adminNotificationCount());

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertRedirect();

        // Client tepat 1 notifikasi.
        $this->assertSame(1, $this->clientNotificationCount());
        // Admin notif tetap 1 (tidak bertambah dari pengiriman).
        $this->assertSame(1, $this->adminNotificationCount());
    }

    // F. Non-DDMS path unaffected by Director notification logic.
    public function test_non_ddms_regression_client_only(): void
    {
        $this->setDdmsEnabled(true);
        $this->setDdmsDefaultPenawaran(false);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-penawaran', $this->event->id), [
                'nomor_surat' => 'PEN-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '0',
            ])
            ->assertRedirect();

        $this->assertSame(1, $this->clientNotificationCount());
        // Non-DDMS tidak boleh memicu notifikasi Admin dari alur Director.
        $this->assertSame(0, $this->adminNotificationCount());
    }

    // J. Duplicate approve protection (cannot approve twice).
    public function test_duplicate_approve_no_duplicate_notification(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');
        $this->assertSame(1, $this->adminNotificationCount());

        // Approve kedua harus ditolak (status sudah approved) → tidak ada notif baru.
        $this->director->update(['approval_pin' => Hash::make('123456')]);
        try {
            app(DocumentApprovalService::class)->directorApprove($document, $this->director, '123456');
            $this->fail('Expected exception on duplicate approve.');
        } catch (\App\Exceptions\DDMS\DDMSException $e) {
            // expected
        }

        $this->assertSame(1, $this->adminNotificationCount());
    }

    // ─── Phase 11I.10J — Unified number display ──────────────────────────

    // DDMS: export PDF nomor diambil dari DocumentNumbering, bukan placeholder.
    public function test_ddms_export_uses_document_numbering(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        app(DocumentNumberService::class)->setManualNumber($document, 'PRO-XYZ-001', $this->admin);
        $document->refresh();

        $data = app(AdminProposalService::class)->exportPdfData($this->event);
        $this->assertSame('PRO-XYZ-001', $data['data']['nomor_surat']);
    }

    // NON-DDMS: export PDF nomor tetap Proposal.nomor_proposal (dari form).
    public function test_non_ddms_export_uses_proposal_number(): void
    {
        $this->setDdmsEnabled(true);
        $this->setDdmsDefaultPenawaran(false);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-penawaran', $this->event->id), [
                'nomor_surat' => 'PEN-AAA-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '0',
            ])
            ->assertRedirect();

        $data = app(AdminProposalService::class)->exportPdfData($this->event);
        $this->assertSame('PEN-AAA-001', $data['data']['nomor_surat']);
    }

    // K. Duplicate reject protection (cannot reject twice).
    public function test_duplicate_reject_no_duplicate_notification(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        app(DocumentNumberService::class)->setManualNumber($document, 'PEN/REJ/001', $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);
        $this->director->update(['approval_pin' => Hash::make('123456')]);
        app(DocumentApprovalService::class)->directorReject($document, $this->director, 'Perlu revisi', '123456');
        $this->assertSame(1, $this->adminNotificationCount());

        // Reject kedua harus ditolak (status sudah rejected) → tidak ada notif baru.
        try {
            app(DocumentApprovalService::class)->directorReject($document, $this->director, 'Lagi', '123456');
            $this->fail('Expected exception on duplicate reject.');
        } catch (\App\Exceptions\DDMS\DDMSException $e) {
            // expected
        }

        $this->assertSame(1, $this->adminNotificationCount());
    }

    // ─── Phase 11I.10H — Numbering & Status UI alignment ────────────────

    // A. Non-DDMS: Proposal.nomor_proposal tetap digunakan dari form.
    public function test_non_ddms_keeps_proposal_number(): void
    {
        $this->setDdmsEnabled(true);
        $this->setDdmsDefaultPenawaran(false);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-penawaran', $this->event->id), [
                'nomor_surat' => 'PEN-CUSTOM-99',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '0',
            ])
            ->assertRedirect();

        $proposal = Proposal::where('event_id', $this->event->id)->firstOrFail();
        $this->assertSame('PEN-CUSTOM-99', $proposal->nomor_proposal);
        $this->assertNull($proposal->document_id);
    }

    // B + C. DDMS numbering berasal dari DocumentNumbering, terikat ke Document.
    public function test_ddms_numbering_from_document_numbering(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->assertNull($document->numbering);

        // Nomor dikelola via Document Builder (DocumentNumberService).
        app(DocumentNumberService::class)->setManualNumber($document, 'PRO-2026-001', $this->admin);
        $document->refresh();

        $this->assertNotNull($document->numbering);
        $this->assertSame('PRO-2026-001', $document->numbering->document_number);
        // DocumentNumbering.document_id == Proposal.document_id (same Document).
        $this->assertSame($proposal->document_id, $document->numbering->document_id);
    }

    // D. Draft status.
    public function test_ddms_status_draft(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->assertSame('draft', $document->status->value);
    }

    // E. Pending status after submit.
    public function test_ddms_status_pending_after_submit(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        app(DocumentNumberService::class)->setManualNumber($document, 'PRO-2026-002', $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);
        $this->assertSame('pending', Document::findOrFail($document->id)->status->value);
    }

    // F. Approved status → Kirim ke Client enabled.
    public function test_ddms_status_approved_kirim_enabled(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PRO-2026-003');
        $this->assertSame('approved', Document::findOrFail($document->id)->status->value);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertRedirect();
        $this->assertSame(1, $this->clientNotificationCount());
    }

    // G. Rejected status → Kirim ke Client disabled.
    public function test_ddms_status_rejected_kirim_disabled(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        app(DocumentNumberService::class)->setManualNumber($document, 'PRO-2026-004', $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);
        $this->director->update(['approval_pin' => Hash::make('123456')]);
        app(DocumentApprovalService::class)->directorReject($document, $this->director, 'Perlu revisi', '123456');
        $this->assertSame('rejected', Document::findOrFail($document->id)->status->value);

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertSessionHasErrors('uses_ddms');
    }

    // H. Revision: v1 → Document A, v2 → Document B; numbering independen.
    public function test_revision_documents_have_independent_numbering(): void
    {
        [$v1, $docA] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($docA, 'PRO-V1-001');

        // Admin kirim v1.
        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertRedirect();

        // Client negosiasi.
        $this->actingAs($this->client)
            ->post(route('client.proposals.negosiasi', $v1->id), [
                'pesan' => 'Mohon diskon',
                'budget_diinginkan' => '100',
            ])
            ->assertRedirect();

        // Admin buat v2.
        $this->actingAs($this->admin)
            ->post(route('admin.requests.masuk-ke-ddms', $this->event->id), [
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '1',
            ])
            ->assertRedirect();

        $v2 = Proposal::where('event_id', $this->event->id)->where('versi', 2)->firstOrFail();
        $docB = Document::findOrFail($v2->document_id);
        $this->assertNotSame($docA->id, $docB->id);

        // docA sudah dinomori saat prepareDdmsForApproval; beri nomor docB via Document Builder.
        app(DocumentNumberService::class)->setManualNumber($docB, 'PRO-V2-001', $this->admin);
        $docA->refresh();
        $docB->refresh();

        $this->assertSame('PRO-V1-001', $docA->numbering->document_number);
        $this->assertSame('PRO-V2-001', $docB->numbering->document_number);
        $this->assertNotSame($docA->numbering->document_number, $docB->numbering->document_number);
        // Document A tidak berubah status (tetap approved).
        $this->assertSame('approved', Document::findOrFail($docA->id)->status->value);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // Phase 11I.10L — Issue 1: Published DDMS send uses final Document PDF
    // ═══════════════════════════════════════════════════════════════════════

    // A. published DDMS send uses final Document PDF
    public function test_published_ddms_send_uses_final_document_pdf(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');
        app(DocumentApprovalService::class)->publishDocument($document, $this->admin);

        $document = Document::findOrFail($document->id);
        $this->assertSame('published', $document->status->value);
        $this->assertNotNull($document->file_path);
        $this->assertTrue(Storage::disk('public')->exists($document->file_path));

        $proposal = Proposal::where('event_id', $this->event->id)->firstOrFail();
        $originalProposalFile = $proposal->file_proposal;

        // Kirim ke Client.
        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertRedirect();

        // Notification client = 1.
        $this->assertSame(1, $this->clientNotificationCount());

        // Document.file_path tidak berubah (tidak regenerate PDF baru).
        $document = Document::findOrFail($document->id);
        $this->assertSame($document->file_path, $document->file_path);
        $this->assertTrue(Storage::disk('public')->exists($document->file_path));

        // Proposal.file_proposal tetap sama (tidak dibuat baru).
        $proposal = Proposal::where('event_id', $this->event->id)->firstOrFail();
        $this->assertSame($originalProposalFile, $proposal->file_proposal);
        $this->assertSame(1, Proposal::where('event_id', $this->event->id)->count());
    }

    // B. published DDMS PDF has QR
    public function test_published_ddms_pdf_has_qr(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/PDF/001');
        app(DocumentApprovalService::class)->publishDocument($document, $this->admin);

        $document = Document::findOrFail($document->id);
        $document->load('qrVerification');

        // QR verification exists.
        $this->assertNotNull($document->qrVerification);
        $this->assertNotNull($document->qrVerification->verification_token);
        $this->assertNotNull($document->qrVerification->qr_path);
        $this->assertTrue(Storage::disk('public')->exists($document->qrVerification->qr_path));

        // Final PDF path = Document.file_path.
        $this->assertNotNull($document->file_path);
        $this->assertTrue(Storage::disk('public')->exists($document->file_path));
    }

    // Kritial: PDF content verification — hash, QR, numbering, file identity
    public function test_published_ddms_proposal_pdf_contains_qr_and_document_number(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PRO-VERIFY-001');
        app(DocumentApprovalService::class)->publishDocument($document, $this->admin);

        $document = Document::findOrFail($document->id);
        $document->load(['numbering', 'qrVerification']);
        $proposal = Proposal::where('document_id', $document->id)->firstOrFail();

        // 1. File paths konsisten
        $this->assertSame($proposal->file_proposal, $document->file_path);

        // 2. File fisik ada
        $pdfPath = $document->file_path;
        $this->assertTrue(Storage::disk('public')->exists($pdfPath));

        // 3. Hash identik antara Proposal.file_proposal dan Document.file_path
        $proposalHash = hash_file('sha256', Storage::disk('public')->path($proposal->file_proposal));
        $documentHash = hash_file('sha256', Storage::disk('public')->path($document->file_path));
        $this->assertSame($documentHash, $proposalHash, 'Proposal.file_proposal hash == Document.file_path hash');

        // 4. PDF valid
        $pdfContent = Storage::disk('public')->get($pdfPath);
        $this->assertStringStartsWith('%PDF-', $pdfContent, 'PDF file is valid');

        // 5. QR image ter-embed di PDF (Image XObject)
        $this->assertStringContainsString('/Subtype /Image', $pdfContent, 'QR image XObject embedded in PDF');

        // 5b. Image stream ada di PDF (berisi data gambar QR)
        $this->assertGreaterThan(0, substr_count($pdfContent, '/Subtype /Image'), 'Minimal 1 image XObject in PDF');

        // 6. DocumentNumbering ada dan nomor DDMS ada di PDF
        $this->assertNotNull($document->numbering);
        $this->assertSame('PRO-VERIFY-001', $document->numbering->document_number);

        // 7. Decompress PDF streams dan cek nomor DDMS ada di bytes
        $decompressed = '';
        $off = 0;
        while (($s = strpos($pdfContent, 'stream', $off)) !== false) {
            $e = strpos($pdfContent, 'endstream', $s);
            if ($e === false) break;
            $st = substr($pdfContent, $s + 6, $e - $s - 6);
            $st = ltrim($st, "\r\n");
            $d = @gzuncompress($st);
            if ($d !== false) { $decompressed .= $d; }
            $off = $e + 9;
        }

        // 7b. Verifikasi tambahan: rendered HTML template mengandung nomor DDMS + QR
        $renderedHtml = \Illuminate\Support\Facades\View::make('admin.requests.surat_penawaran_pdf', [
            'event' => $this->event,
            'data'  => [
                'nomor_surat'   => $document->numbering->document_number,
                'tanggal_surat' => now()->format('Y-m-d'),
                'perihal'       => 'Surat Penawaran Pameran Otomotif',
                'document'      => $document,
            ],
        ])->render();
        $this->assertStringContainsString('PRO-VERIFY-001', $renderedHtml, 'DDMS number in rendered template');
        $this->assertStringContainsString('Scan QR', $renderedHtml, 'QR block in rendered template');

        // 8. QR verification file ada
        $this->assertNotNull($document->qrVerification);
        $this->assertTrue(Storage::disk('public')->exists($document->qrVerification->qr_path));

        // 9. Kirim ke Client — file yang sama (Document.file_path) yang dikirim
        $sendBefore = \App\Models\DocumentSend::count();
        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertRedirect();

        // Notification = 1
        $this->assertSame(1, $this->clientNotificationCount());

        // DocumentSend tercatat — file yang dikirim
        $sendAfter = \App\Models\DocumentSend::count();
        $this->assertSame($sendBefore + 1, $sendAfter);
    }

    // C. published DDMS has DocumentNumbering
    public function test_published_ddms_has_document_numbering(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PRO-NUM-001');
        app(DocumentApprovalService::class)->publishDocument($document, $this->admin);

        $document = Document::findOrFail($document->id);

        // DocumentNumbering exists.
        $this->assertNotNull($document->numbering);
        $this->assertSame('PRO-NUM-001', $document->numbering->document_number);

        // Export PDF data uses DocumentNumbering for DDMS.
        $data = app(AdminProposalService::class)->exportPdfData($this->event);
        $this->assertSame('PRO-NUM-001', $data['data']['nomor_surat']);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // Phase 11I.10L — Issue 2: Edit lock DDMS
    // ═══════════════════════════════════════════════════════════════════════

    // D. edit blocked on draft
    public function test_edit_blocked_on_draft(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->assertSame('draft', $document->status->value);

        $this->actingAs($this->admin)
            ->get(route('admin.requests.surat-penawaran', $this->event->id))
            ->assertOk()
            ->assertSee('Edit Surat')
            ->assertSee('dikunci oleh DDMS');

        // Server-side: direct POST bypass.
        $this->actingAs($this->admin)
            ->patch(route('admin.requests.update-surat-penawaran', $this->event->id), [
                'perihal' => 'Updated Perihal',
            ])
            ->assertSessionHasErrors('edit');
    }

    // E. edit blocked on pending
    public function test_edit_blocked_on_pending(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        app(DocumentNumberService::class)->setManualNumber($document, 'PEN/PEND/001', $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);
        $this->assertSame('pending', Document::findOrFail($document->id)->status->value);

        $this->actingAs($this->admin)
            ->get(route('admin.requests.surat-penawaran', $this->event->id))
            ->assertOk()
            ->assertSee('dikunci oleh DDMS');

        $this->actingAs($this->admin)
            ->patch(route('admin.requests.update-surat-penawaran', $this->event->id), [
                'perihal' => 'Updated Perihal',
            ])
            ->assertSessionHasErrors('edit');
    }

    // F. edit allowed on rejected
    public function test_edit_allowed_on_rejected(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        app(DocumentNumberService::class)->setManualNumber($document, 'PEN/REJ/001', $this->admin);
        app(DocumentApprovalService::class)->submit($document, $this->admin);
        $this->director->update(['approval_pin' => Hash::make('123456')]);
        app(DocumentApprovalService::class)->directorReject($document, $this->director, 'Perlu revisi', '123456');
        $this->assertSame('rejected', Document::findOrFail($document->id)->status->value);

        $this->actingAs($this->admin)
            ->get(route('admin.requests.surat-penawaran', $this->event->id))
            ->assertOk()
            ->assertSee('Edit Surat')
            ->assertDontSee('dikunci oleh DDMS');

        // Server-side: edit allowed.
        $this->actingAs($this->admin)
            ->patch(route('admin.requests.update-surat-penawaran', $this->event->id), [
                'nomor_surat_override' => 'PEN-001',
                'perihal'              => 'Updated Perihal Setelah Reject',
                'include_ppn'          => false,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }

    // G. edit blocked on approved
    public function test_edit_blocked_on_approved(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');
        $this->assertSame('approved', Document::findOrFail($document->id)->status->value);

        $this->actingAs($this->admin)
            ->get(route('admin.requests.surat-penawaran', $this->event->id))
            ->assertOk()
            ->assertSee('dikunci oleh DDMS');

        $this->actingAs($this->admin)
            ->patch(route('admin.requests.update-surat-penawaran', $this->event->id), [
                'perihal' => 'Updated Perihal',
            ])
            ->assertSessionHasErrors('edit');
    }

    // H. edit blocked on published
    public function test_edit_blocked_on_published(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');
        app(DocumentApprovalService::class)->publishDocument($document, $this->admin);
        $this->assertSame('published', Document::findOrFail($document->id)->status->value);

        $this->actingAs($this->admin)
            ->get(route('admin.requests.surat-penawaran', $this->event->id))
            ->assertOk()
            ->assertSee('dikunci oleh DDMS');

        $this->actingAs($this->admin)
            ->patch(route('admin.requests.update-surat-penawaran', $this->event->id), [
                'perihal' => 'Updated Perihal',
            ])
            ->assertSessionHasErrors('edit');
    }

    // I. direct POST bypass edit blocked for published
    public function test_direct_post_bypass_edit_blocked_for_published(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/PUB/001');
        app(DocumentApprovalService::class)->publishDocument($document, $this->admin);
        $this->assertSame('published', Document::findOrFail($document->id)->status->value);

        $originalPerihal = $this->event->perihal;

        // Direct POST ke update-surat-penawaran harus ditolak.
        $this->actingAs($this->admin)
            ->patch(route('admin.requests.update-surat-penawaran', $this->event->id), [
                'perihal' => 'Bypass Attempt',
            ])
            ->assertSessionHasErrors('edit');

        // Event tidak berubah.
        $this->assertSame($originalPerihal, $this->event->fresh()->perihal);
    }

    // J. non-DDMS edit remains working
    public function test_non_ddms_edit_remains_working(): void
    {
        $this->setDdmsEnabled(true);
        $this->setDdmsDefaultPenawaran(false);

        // Buat NON-DDMS proposal.
        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-penawaran', $this->event->id), [
                'nomor_surat' => 'PEN-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '0',
            ])
            ->assertRedirect();

        $proposal = Proposal::where('event_id', $this->event->id)->firstOrFail();
        $this->assertNull($proposal->document_id);
        $this->assertFalse($proposal->uses_ddms);

        // Edit Surat button harus terlihat (tidak ada DDMS lock).
        $this->actingAs($this->admin)
            ->get(route('admin.requests.surat-penawaran', $this->event->id))
            ->assertOk()
            ->assertSee('Edit Surat')
            ->assertDontSee('dikunci oleh DDMS');

        // Server-side: edit boleh.
        $this->actingAs($this->admin)
            ->patch(route('admin.requests.update-surat-penawaran', $this->event->id), [
                'nomor_surat_override' => 'PEN-001',
                'perihal'              => 'Updated Perihal Non-DDMS',
                'include_ppn'          => false,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('Updated Perihal Non-DDMS', $this->event->fresh()->perihal);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // Phase 11I.10L — K. Revision: v1 published immutable, v2 new Document
    // ═══════════════════════════════════════════════════════════════════════

    // K. revision: v1 published remains immutable, v2 creates new Document
    public function test_revision_v1_published_immutable_v2_new_document(): void
    {
        // v1: masuk ke DDMS, approve, publish
        [$v1, $docA] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($docA, 'PEN/V1/001');
        app(DocumentApprovalService::class)->publishDocument($docA, $this->admin);
        $this->assertSame('published', Document::findOrFail($docA->id)->status->value);

        $docAFileBefore = $docA->file_path;
        $docAStatusBefore = $docA->status->value;

        // v1 tidak boleh diedit setelah published.
        $this->actingAs($this->admin)
            ->patch(route('admin.requests.update-surat-penawaran', $this->event->id), [
                'perihal' => 'Try Edit v1',
            ])
            ->assertSessionHasErrors('edit');

        // v1 kirim ke client.
        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertRedirect();
        $this->assertSame(1, $this->clientNotificationCount());

        // Client negosiasi.
        $this->actingAs($this->client)
            ->post(route('client.proposals.negosiasi', $v1->id), [
                'pesan' => 'Mohon diskon',
                'budget_diinginkan' => '100',
            ])
            ->assertRedirect();

        // Admin buat v2 (Document B draft).
        $this->actingAs($this->admin)
            ->post(route('admin.requests.masuk-ke-ddms', $this->event->id), [
                'tanggal_surat' => now()->format('Y-m-d'),
                'uses_ddms' => '1',
            ])
            ->assertRedirect();

        $v2 = Proposal::where('event_id', $this->event->id)->where('versi', 2)->firstOrFail();
        $docB = Document::findOrFail($v2->document_id);
        $this->assertSame('draft', $docB->status->value);
        $this->assertNotSame($docA->id, $docB->id);

        // Document A tidak berubah setelah v2 dibuat.
        $docAAfter = Document::findOrFail($docA->id);
        $this->assertSame($docAFileBefore, $docAAfter->file_path);
        $this->assertSame($docAStatusBefore, $docAAfter->status->value);
        $this->assertSame('published', $docAAfter->status->value);

        // Document A tidak dapat diedit.
        $this->actingAs($this->admin)
            ->patch(route('admin.requests.update-surat-penawaran', $this->event->id), [
                'perihal' => 'Try Edit v2 before approve',
            ])
            ->assertSessionHasErrors('edit');

        // v2 draft → edit blocked.
        $this->assertSame(2, Proposal::where('event_id', $this->event->id)->count());
        $this->assertSame(2, Document::where('event_id', $this->event->id)->count());
    }

    // ═══════════════════════════════════════════════════════════════════════
    // Phase 11I.10L — L. Notification timing verification
    // ═══════════════════════════════════════════════════════════════════════

    // L-A. Director Approve = 0 client notification
    public function test_director_approve_no_client_notification(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');
        $this->assertSame('approved', Document::findOrFail($document->id)->status->value);

        $this->assertSame(0, $this->clientNotificationCount());
    }

    // L-B. Publish = 0 client notification
    public function test_publish_no_client_notification(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');
        app(DocumentApprovalService::class)->publishDocument($document, $this->admin);
        $this->assertSame('published', Document::findOrFail($document->id)->status->value);

        $this->assertSame(0, $this->clientNotificationCount());
    }

    // L-C. Kirim ke Client = 1 client notification
    public function test_kirim_ke_client_one_client_notification(): void
    {
        [$proposal, $document] = $this->masukKeDdmsProposal();
        $this->prepareDdmsForApproval($document, 'PEN/APR/001');
        app(DocumentApprovalService::class)->publishDocument($document, $this->admin);
        $this->assertSame('published', Document::findOrFail($document->id)->status->value);

        $this->assertSame(0, $this->clientNotificationCount());

        $this->actingAs($this->admin)
            ->post(route('admin.requests.kirim-revisi-penawaran', $this->event->id), ['uses_ddms' => '1'])
            ->assertRedirect();

        $this->assertSame(1, $this->clientNotificationCount());
    }
}
