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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Phase 11I.5 — Global DDMS Feature Toggle.
 *
 * ddms_enabled = 0 memblokir pembuatan/alur baru, tanpa memengaruhi
 * dokumen Published lama, QR, token, dan public verification.
 */
class DdmsFeatureToggleTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $directorUser;
    private User $clientUser;
    private User $vendorUser;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'I5 Admin',
            'email' => 'i5-admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->directorUser = User::create([
            'name' => 'I5 Director',
            'email' => 'i5-director@test.com',
            'password' => bcrypt('password'),
            'role' => 'director',
        ]);

        $this->clientUser = User::create([
            'name' => 'I5 Client',
            'email' => 'i5-client@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);

        $this->vendorUser = User::create([
            'name' => 'I5 Vendor',
            'email' => 'i5-vendor@test.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
        ]);

        $this->event = Event::create([
            'client_id' => $this->clientUser->id,
            'nama_event' => 'I5 Event Alpha',
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

    private function makeDraftDocument(): Document
    {
        return Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'proposal',
            'nama_file' => 'i5_draft_' . Str::random(6) . '.pdf',
            'status' => DocumentStatus::Draft,
            'document_source' => DocumentSource::Generated,
            'file_path' => 'documents/i5_draft.pdf',
        ]);
    }

    private function makePendingDocument(): Document
    {
        $document = $this->makeDraftDocument();
        $document->status = DocumentStatus::Pending;
        $document->uses_ddms = true;
        $document->save();

        DocumentApproval::create([
            'document_id' => $document->id,
            'submitted_by' => $this->adminUser->id,
            'status' => DocumentApproval::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        return $document;
    }

    private function makeApprovedDocument(): Document
    {
        $document = $this->makeDraftDocument();
        $document->status = DocumentStatus::Approved;
        $document->uses_ddms = true;
        $document->save();

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'MANUAL',
            'year' => (int) now()->format('Y'),
            'sequence_number' => 0,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'I5-APR-' . $document->id,
        ]);

        return $document;
    }

    private function makePublishedDocumentWithQrAndLog(): array
    {
        $document = $this->makeApprovedDocument();
        $document->status = DocumentStatus::Published;
        $document->save();

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
            'user_agent' => 'DdmsFeatureToggleTest',
        ]);

        return [$document, $qr];
    }

    public function test_ddms_is_enabled_by_default(): void
    {
        $this->assertSame('1', app(DdmsSettingService::class)->getSettingValue('ddms_enabled', '1'));
    }

    public function test_admin_can_toggle_ddms_off_and_on(): void
    {
        // Halaman pengaturan admin menampilkan kartu DDMS.
        $this->actingAs($this->adminUser)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('DDMS');

        $this->actingAs($this->adminUser)
            ->post(route('admin.settings.ddms-toggle'), ['enabled' => '0'])
            ->assertRedirect();

        $this->assertSame('0', app(DdmsSettingService::class)->getSettingValue('ddms_enabled', '1'));

        $this->actingAs($this->adminUser)
            ->post(route('admin.settings.ddms-toggle'), ['enabled' => '1'])
            ->assertRedirect();

        $this->assertSame('1', app(DdmsSettingService::class)->getSettingValue('ddms_enabled', '1'));
    }

    public function test_non_admin_cannot_change_ddms_setting(): void
    {
        $uri = route('admin.settings.ddms-toggle');

        $this->actingAs($this->clientUser)->post($uri, ['enabled' => '0'])->assertForbidden();
        $this->actingAs($this->vendorUser)->post($uri, ['enabled' => '0'])->assertForbidden();
        $this->actingAs($this->directorUser)->post($uri, ['enabled' => '0'])->assertForbidden();
    }

    public function test_guest_cannot_change_ddms_setting(): void
    {
        // Route POST-only: GET ditolak 405; POST sebagai guest diarahkan ke login (auth middleware).
        $this->get(route('admin.settings.ddms-toggle'))->assertMethodNotAllowed();
        $this->post(route('admin.settings.ddms-toggle'), ['enabled' => '0'])->assertRedirect(route('login'));
    }

    public function test_ddms_off_does_not_block_generation_and_forces_non_ddms(): void
    {
        $this->setDdmsEnabled(false);
        $before = Document::count();

        // Global OFF: generate TETAP diizinkan, tetapi dipaksa non-DDMS (termasuk saat request memaksa DDMS).
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.generate'), [
                'event_id' => $this->event->id,
                'jenis_dokumen' => 'proposal',
                'uses_ddms' => 1,
            ]);

        $response->assertRedirect();
        $response->assertSessionMissing('error');
        $this->assertSame($before + 1, Document::count());
        $latest = Document::latest('id')->first();
        $this->assertFalse($latest->uses_ddms);
    }

    public function test_ddms_off_blocks_submit_endpoint(): void
    {
        $document = $this->makeDraftDocument();
        $this->setDdmsEnabled(false);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.submit', $document->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(DocumentStatus::Draft, $document->fresh()->status);
    }

    public function test_ddms_off_blocks_director_approve_endpoint(): void
    {
        $document = $this->makePendingDocument();
        $this->setDdmsEnabled(false);

        $response = $this->actingAs($this->directorUser)
            ->post(route('director.approval.approve', $document->id), ['pin' => '123456']);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(DocumentStatus::Pending, $document->fresh()->status);
    }

    public function test_ddms_off_blocks_director_publish_endpoint(): void
    {
        $document = $this->makeApprovedDocument();
        $this->setDdmsEnabled(false);

        $response = $this->actingAs($this->directorUser)
            ->post(route('director.approval.publish', $document->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(DocumentStatus::Approved, $document->fresh()->status);
        $this->assertSame(0, DocumentQrVerification::where('document_id', $document->id)->count());
    }

    public function test_ddms_off_keeps_published_document_verifiable(): void
    {
        [$document, $qr] = $this->makePublishedDocumentWithQrAndLog();
        $this->setDdmsEnabled(false);

        $response = $this->get(route('verify.document', $qr->verification_token));

        $response->assertOk();
        $response->assertViewIs('public.verification.valid');

        // Log lama tetap ada + verifikasi baru tercatat valid → publikasi tetap terverifikasi.
        $this->assertSame(2, DocumentVerificationLog::count());
        $latest = DocumentVerificationLog::latest('id')->first();
        $this->assertSame($qr->id, $latest->verification_id);
        $this->assertSame(DocumentVerificationLog::STATUS_VALID, $latest->status);
    }

    public function test_ddms_on_restores_normal_workflow(): void
    {
        $this->setDdmsEnabled(false);
        $this->setDdmsEnabled(true);

        $before = Document::count();

        $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.generate'), [
                'event_id' => $this->event->id,
                'jenis_dokumen' => 'proposal',
            ])
            ->assertRedirect();

        $this->assertSame($before + 1, Document::count());
    }

    public function test_document_builder_ui_shows_generate_when_enabled(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('admin.document_builder.index'))
            ->assertOk()
            ->assertSee('id="btnGenerate"', false)
            ->assertDontSee('DDMS sedang dinonaktifkan');
    }

    public function test_document_builder_ui_shows_generate_but_disables_ddms_when_off(): void
    {
        $this->setDdmsEnabled(false);

        $this->actingAs($this->adminUser)
            ->get(route('admin.document_builder.index'))
            ->assertOk()
            ->assertSee('id="btnGenerate"', false)
            ->assertSee('Dokumen akan dibuat sebagai dokumen biasa');
    }

    public function test_document_builder_preview_hides_submit_when_disabled(): void
    {
        $document = $this->makeDraftDocument();
        $document->uses_ddms = true;
        $document->save();
        $this->setDdmsEnabled(false);

        $this->actingAs($this->adminUser)
            ->get(route('admin.document_builder.preview', $document->id))
            ->assertOk()
            ->assertSee('DDMS sedang dinonaktifkan')
            ->assertDontSee('Submit Approval');

        $this->setDdmsEnabled(true);

        $this->actingAs($this->adminUser)
            ->get(route('admin.document_builder.preview', $document->id))
            ->assertOk()
            ->assertSee('Submit Approval')
            ->assertDontSee('DDMS sedang dinonaktifkan');
    }

    public function test_qr_signature_partial_uses_readable_size(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('document-qr/i5.png', 'png-bytes');

        $published = $this->makePublishedDocumentWithQrAndLog()[0];
        $published->qrVerification()->update(['qr_path' => 'document-qr/i5.png']);

        $renderedPublished = view('admin.pdf_templates.partials.signature_qr', [
            'document' => $published,
        ])->render();

        $this->assertStringContainsString('width:96px', $renderedPublished);
        $this->assertStringContainsString('height:96px', $renderedPublished);

        $draft = $this->makeDraftDocument();
        $renderedDraft = view('admin.pdf_templates.partials.signature_qr', [
            'document' => $draft,
        ])->render();

        $this->assertSame('', trim($renderedDraft));
    }
}
