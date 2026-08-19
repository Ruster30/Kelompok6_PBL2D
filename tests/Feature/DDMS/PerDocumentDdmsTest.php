<?php

declare(strict_types=1);

namespace Tests\Feature\DDMS;

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\DocumentNumbering;
use App\Models\DocumentQrVerification;
use App\Models\DocumentVerificationLog;
use App\Models\Event;
use App\Models\User;
use App\Services\DdmsSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Phase 11I.6 — Per-Document DDMS Mode.
 *
 * Dokumen non-DDMS tetap bisa dibuat, tetapi TIDAK masuk alur DDMS
 * (submit/approve/publish/token/QR/verifikasi). Dokumen DDMS mengikuti
 * alur yang sudah ada.
 */
class PerDocumentDdmsTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $directorUser;
    private User $clientUser;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'P6 Admin',
            'email' => 'p6-admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->directorUser = User::create([
            'name' => 'P6 Director',
            'email' => 'p6-director@test.com',
            'password' => bcrypt('password'),
            'role' => 'director',
        ]);

        $this->clientUser = User::create([
            'name' => 'P6 Client',
            'email' => 'p6-client@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);

        $this->event = Event::create([
            'client_id' => $this->clientUser->id,
            'nama_event' => 'P6 Event Alpha',
            'tanggal_event' => now()->toDateString(),
            'periode_awal' => now(),
            'periode_akhir' => now()->addDays(30),
        ]);
    }

    private function setDdmsEnabled(bool $enabled): void
    {
        app(DdmsSettingService::class)->updateSetting(
            'ddms_enabled',
            $enabled ? '1' : '0',
            'toggle',
        );
    }

    private function makeDoc(DocumentStatus $status, bool $usesDdms, bool $withNumbering = false): Document
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'proposal',
            'nama_file' => 'p6_' . Str::random(6) . '.pdf',
            'status' => $status,
            'document_source' => DocumentSource::Generated,
            'uses_ddms' => $usesDdms,
            'file_path' => 'documents/p6_doc.pdf',
        ]);

        if ($withNumbering) {
            DocumentNumbering::create([
                'document_id' => $document->id,
                'prefix' => 'MANUAL',
                'year' => (int) now()->format('Y'),
                'sequence_number' => 0,
                'generated_by' => $this->adminUser->id,
                'document_number' => 'P6-' . ($usesDdms ? 'DDMS' : 'NDD') . '-' . $document->id,
            ]);
        }

        return $document;
    }

    private function makePendingDdms(): Document
    {
        $document = $this->makeDoc(DocumentStatus::Pending, true);
        DocumentApproval::create([
            'document_id' => $document->id,
            'submitted_by' => $this->adminUser->id,
            'status' => DocumentApproval::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        return $document;
    }

    private function makePublishedDdms(): array
    {
        $document = $this->makeDoc(DocumentStatus::Published, true, true);
        $qr = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => (string) Str::uuid(),
            'qr_path' => 'document-qr/' . Str::uuid() . '.png',
            'generated_by' => $this->adminUser->id,
            'generated_at' => now(),
        ]);
        DocumentVerificationLog::create([
            'verification_id' => $qr->id,
            'verified_at' => now(),
            'status' => DocumentVerificationLog::STATUS_VALID,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PerDocumentDdmsTest',
        ]);

        return [$document, $qr];
    }

    // ── Creation rules ───────────────────────────────────────────

    public function test_generate_with_global_off_forces_non_ddms_even_when_requested(): void
    {
        $this->setDdmsEnabled(false);

        $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.generate'), [
                'event_id' => $this->event->id,
                'jenis_dokumen' => 'proposal',
                'uses_ddms' => 1,
            ])
            ->assertRedirect();

        $doc = Document::where('event_id', $this->event->id)->firstOrFail();
        $this->assertFalse($doc->uses_ddms);
    }

    public function test_generate_with_global_on_and_checkbox_selected_creates_ddms(): void
    {
        $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.generate'), [
                'event_id' => $this->event->id,
                'jenis_dokumen' => 'proposal',
                'uses_ddms' => 1,
            ])
            ->assertRedirect();

        $doc = Document::where('event_id', $this->event->id)->firstOrFail();
        $this->assertTrue($doc->uses_ddms);
    }

    public function test_generate_without_checkbox_creates_non_ddms(): void
    {
        $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.generate'), [
                'event_id' => $this->event->id,
                'jenis_dokumen' => 'proposal',
            ])
            ->assertRedirect();

        $doc = Document::where('event_id', $this->event->id)->firstOrFail();
        $this->assertFalse($doc->uses_ddms);
    }

    // ── Non-DDMS blocked from DDMS workflow ──────────────────────

    public function test_non_ddms_document_cannot_submit_approval(): void
    {
        $doc = $this->makeDoc(DocumentStatus::Draft, false, true);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.submit', $doc->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(DocumentStatus::Draft, $doc->fresh()->status);
    }

    public function test_non_ddms_document_cannot_be_approved(): void
    {
        $doc = $this->makePendingDdms();
        $doc->uses_ddms = false;
        $doc->save();

        $response = $this->actingAs($this->directorUser)
            ->post(route('director.approval.approve', $doc->id), ['pin' => '123456']);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(DocumentStatus::Pending, $doc->fresh()->status);
    }

    public function test_non_ddms_document_cannot_be_published_and_gets_no_token_or_qr(): void
    {
        $doc = $this->makeDoc(DocumentStatus::Approved, false, true);

        $response = $this->actingAs($this->directorUser)
            ->post(route('director.approval.publish', $doc->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(DocumentStatus::Approved, $doc->fresh()->status);
        $this->assertSame(0, DocumentQrVerification::where('document_id', $doc->id)->count());
    }

    public function test_non_ddms_pdf_does_not_render_signature_qr(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('document-qr/p6.png', 'png-bytes');

        // Dokumen Published namun non-DDMS (dengan qr_path palsu) tidak boleh menampilkan QR.
        $doc = $this->makeDoc(DocumentStatus::Published, false, true);
        DocumentQrVerification::create([
            'document_id' => $doc->id,
            'verification_token' => (string) Str::uuid(),
            'qr_path' => 'document-qr/p6.png',
            'generated_by' => $this->adminUser->id,
            'generated_at' => now(),
        ]);

        $rendered = view('admin.pdf_templates.partials.signature_qr', [
            'document' => $doc,
        ])->render();

        $this->assertSame('', trim($rendered));
    }

    // ── Existing / published behavior retained ────────────────────

    public function test_ddms_document_full_workflow_still_works(): void
    {
        Storage::fake('public');
        $this->directorUser->update(['approval_pin' => Hash::make('123456')]);

        $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.generate'), [
                'event_id' => $this->event->id,
                'jenis_dokumen' => 'proposal',
                'uses_ddms' => 1,
            ])
            ->assertRedirect();

        $doc = Document::where('event_id', $this->event->id)->firstOrFail();
        $this->assertTrue($doc->uses_ddms);
        $this->assertSame(DocumentStatus::Draft, $doc->status);

        $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.set_number', $doc->id), ['nomor_surat' => 'P6-E2E-001'])
            ->assertRedirect();

        $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.submit', $doc->id))
            ->assertRedirect();
        $this->assertSame(DocumentStatus::Pending, $doc->fresh()->status);

        $this->actingAs($this->directorUser)
            ->post(route('director.approval.approve', $doc->id), ['pin' => '123456'])
            ->assertRedirect();
        $this->assertSame(DocumentStatus::Approved, $doc->fresh()->status);

        $this->actingAs($this->directorUser)
            ->post(route('director.approval.publish', $doc->id))
            ->assertRedirect();

        $published = $doc->fresh();
        $this->assertSame(DocumentStatus::Published, $published->status);
        $this->assertNotNull($published->qrVerification?->verification_token);
        $this->assertNotNull($published->qrVerification?->qr_path);
    }

    public function test_published_ddms_document_remains_publicly_verifiable_even_when_global_off(): void
    {
        [$doc, $qr] = $this->makePublishedDdms();
        $this->setDdmsEnabled(false);

        $response = $this->get(route('verify.document', $qr->verification_token));

        $response->assertOk();
        $response->assertViewIs('public.verification.valid');
    }

    // ── UI ────────────────────────────────────────────────────────

    public function test_builder_ui_shows_ddms_choice_when_enabled(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('admin.document_builder.index'))
            ->assertOk()
            ->assertSee('Gunakan DDMS');
    }

    public function test_builder_ui_explains_ordinary_mode_when_global_off(): void
    {
        $this->setDdmsEnabled(false);

        $this->actingAs($this->adminUser)
            ->get(route('admin.document_builder.index'))
            ->assertOk()
            ->assertSee('DDMS sedang dinonaktifkan oleh administrator. Dokumen akan dibuat sebagai dokumen biasa.');
    }

    public function test_existing_ordinary_document_remains_usable(): void
    {
        $uploaded = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'proposal',
            'nama_file' => 'p6_uploaded.pdf',
            'status' => DocumentStatus::Draft,
            'document_source' => DocumentSource::Uploaded,
            'file_path' => 'documents/p6_uploaded.pdf',
        ]);

        $this->assertFalse($uploaded->fresh()->uses_ddms);

        $this->actingAs($this->adminUser)
            ->get(route('admin.document_builder.preview', $uploaded->id))
            ->assertOk()
            ->assertSee('Non-DDMS');
    }
}