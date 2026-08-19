<?php

declare(strict_types=1);

namespace Tests\Feature\DDMS;

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentNumbering;
use App\Models\DocumentQrVerification;
use App\Models\Event;
use App\Models\User;
use App\Services\DocumentApprovalService;
use App\Services\DocumentBuilderService;
use App\Services\DocumentNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Phase 11H.4 — Hardening & Critical Regression Tests.
 *
 * Menutup celah coverage pada alur final:
 * - publish idempotency
 * - admin tidak bisa publish
 * - nomor dokumen tidak boleh duplikat antar dokumen
 * - token harus UUID v4 valid
 * - QR benar-benar terintegrasi ke PDF Published
 * - E2E journey lengkap (generate → number → submit → PIN approve → publish → verify)
 * - audit read-only (tidak ada endpoint mutasi) + role denial
 */
class WorkflowHardeningTest extends TestCase
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
            'name' => 'H4 Admin',
            'email' => 'h4-admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->directorUser = User::create([
            'name' => 'H4 Director',
            'email' => 'h4-director@test.com',
            'password' => bcrypt('password'),
            'role' => 'director',
        ]);

        $this->clientUser = User::create([
            'name' => 'H4 Client',
            'email' => 'h4-client@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);

        $this->vendorUser = User::create([
            'name' => 'H4 Vendor',
            'email' => 'h4-vendor@test.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
        ]);

        $this->event = Event::create([
            'client_id' => $this->clientUser->id,
            'nama_event' => 'H4 Event Alpha',
            'tanggal_event' => now()->toDateString(),
            'periode_awal' => now(),
            'periode_akhir' => now()->addDays(30),
        ]);
    }

    private function makeDraftDocument(): Document
    {
        return Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'proposal',
            'nama_file' => 'h4_draft_' . \Illuminate\Support\Str::random(6) . '.pdf',
            'status' => DocumentStatus::Draft,
            'document_source' => DocumentSource::Generated,
            'uses_ddms' => true,
            'file_path' => 'documents/h4_draft.pdf',
        ]);
    }

    private function makeApprovedDocument(): Document
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'proposal',
            'nama_file' => 'h4_approved_' . \Illuminate\Support\Str::random(6) . '.pdf',
            'status' => DocumentStatus::Approved,
            'document_source' => DocumentSource::Generated,
            'uses_ddms' => true,
            'file_path' => 'documents/h4_approved.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'MANUAL',
            'year' => (int) now()->format('Y'),
            'sequence_number' => 0,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'H4-APR-' . $document->id,
        ]);

        return $document;
    }

    public function test_publish_is_idempotent_and_never_duplicates_token_or_qr(): void
    {
        Storage::fake('public');
        $this->mock(DocumentBuilderService::class, function ($mock) {
            $mock->shouldReceive('regeneratePublishedPdf');
        });

        $document = $this->makeApprovedDocument();
        $service = app(DocumentApprovalService::class);

        $service->publishDocument($document, $this->directorUser);

        $published = $document->fresh();
        $this->assertSame(DocumentStatus::Published, $published->status);
        $this->assertNotNull($published->qrVerification?->verification_token);
        $this->assertNotNull($published->qrVerification?->qr_path);

        $this->assertSame(1, DocumentQrVerification::where('document_id', $document->id)->count());

        // Publish ulang dokumen yang sudah Published harus ditolak.
        try {
            $service->publishDocument($published, $this->directorUser);
            $this->fail('Publish ulang dokumen Published seharusnya ditolak.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('Approved', $e->getMessage());
        }

        $after = $document->fresh();
        $this->assertSame(DocumentStatus::Published, $after->status);
        $this->assertSame(1, DocumentQrVerification::where('document_id', $document->id)->count());
    }

    public function test_admin_cannot_publish_document(): void
    {
        $document = $this->makeApprovedDocument();

        $response = $this->actingAs($this->adminUser)->post(route('director.approval.publish', $document->id));

        $response->assertForbidden();
        $this->assertSame(DocumentStatus::Approved, $document->fresh()->status);
        $this->assertNull(DocumentQrVerification::where('document_id', $document->id)->first());
    }

    public function test_duplicate_document_number_across_documents_is_rejected(): void
    {
        $first = $this->makeDraftDocument();
        $second = $this->makeDraftDocument();
        $service = app(DocumentNumberService::class);

        $service->setManualNumber($first, 'H4-DUP-001', $this->adminUser);

        try {
            $service->setManualNumber($second, 'H4-DUP-001', $this->adminUser);
            $this->fail('Nomor duplikat antar dokumen seharusnya ditolak.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nomor_surat', $e->errors());
        }

        $this->assertNull($second->fresh()->numbering);
    }

    public function test_document_number_exactly_100_characters_is_accepted(): void
    {
        $document = $this->makeDraftDocument();
        $number = str_repeat('N', 100);

        app(DocumentNumberService::class)->setManualNumber($document, $number, $this->adminUser);

        $this->assertSame($number, $document->fresh()->numbering?->document_number);
    }

    public function test_document_number_over_100_characters_is_rejected_safely(): void
    {
        $document = $this->makeDraftDocument();
        $number = str_repeat('N', 101);

        $service = app(DocumentNumberService::class);

        try {
            $service->setManualNumber($document, $number, $this->adminUser);
            $this->fail('Nomor lebih dari 100 karakter seharusnya ditolak.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nomor_surat', $e->errors());
        }

        $this->assertNull($document->fresh()->numbering);

        // Jalur HTTP juga harus menolak via validasi normal (tanpa 500 / DB exception).
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.set_number', $document->id), [
                'nomor_surat' => str_repeat('N', 101),
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('nomor_surat');
        $this->assertNull($document->fresh()->numbering);
    }

    public function test_verification_token_is_a_valid_uuid_v4(): void
    {
        Storage::fake('public');
        $this->mock(DocumentBuilderService::class, function ($mock) {
            $mock->shouldReceive('regeneratePublishedPdf');
        });

        $document = $this->makeApprovedDocument();
        app(DocumentApprovalService::class)->publishDocument($document, $this->directorUser);

        $token = $document->fresh()->qrVerification->verification_token;

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $token,
            "Token '{$token}' bukan UUID v4 yang valid.",
        );
    }

    public function test_qr_image_is_embedded_in_pdf_only_after_publish(): void
    {
        // Menggunakan storage NYATA (bukan fake) karena DomPDF membaca file QR
        // melalui storage_path('app/public/{qr_path}') — path filesystem nyata.
        $disk = Storage::disk('public');
        $createdFiles = [];

        try {
            $document = $this->makeApprovedDocument();
            $document->file_path = 'documents/h4_' . uniqid() . '.pdf';
            $document->save();
            $createdFiles[] = $document->file_path;

            $builder = app(DocumentBuilderService::class);

            // PDF pada status Approved: tanpa QR (template proposal tanpa image lain).
            $builder->regenerateFinalPdf($document, $this->event, 'proposal');
            $pdfBeforePublish = $disk->get($document->file_path);
            $this->assertIsString($pdfBeforePublish);
            $this->assertStringStartsWith('%PDF-', $pdfBeforePublish);
            $this->assertStringNotContainsString('/Subtype /Image', $pdfBeforePublish);

            // Setelah Publish: QR sudah tersedia, PDF final harus memuat image object QR.
            app(DocumentApprovalService::class)->publishDocument($document, $this->directorUser);

            $published = $document->fresh();
            if ($published->qrVerification?->qr_path) {
                $createdFiles[] = $published->qrVerification->qr_path;
            }

            $pdfAfterPublish = $disk->get($document->file_path);
            $this->assertIsString($pdfAfterPublish);
            $this->assertStringStartsWith('%PDF-', $pdfAfterPublish);
            $this->assertStringContainsString('/Subtype /Image', $pdfAfterPublish);
        } finally {
            // Hanya hapus file yang dibuat oleh test ini — jangan menyentuh
            // direktori storage publik yang mungkin berisi artifact lain.
            foreach ($createdFiles as $path) {
                if ($path && $disk->exists($path)) {
                    $disk->delete($path);
                }
            }
        }
    }

    public function test_end_to_end_generated_verification_journey(): void
    {
        Storage::fake('public');
        $this->directorUser->update(['approval_pin' => Hash::make('123456')]);

        $number = 'H4-E2E-001';

        // 1. Generate dokumen (Draft, Generated).
        $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.generate'), [
                'event_id' => $this->event->id,
                'jenis_dokumen' => 'proposal',
                'uses_ddms' => 1,
            ])
            ->assertRedirect();

        $document = Document::where('event_id', $this->event->id)->latest('id')->firstOrFail();
        $this->assertSame(DocumentStatus::Draft, $document->status);
        $this->assertSame(DocumentSource::Generated, $document->document_source);

        // 2. Admin set nomor surat manual.
        $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.set_number', $document->id), [
                'nomor_surat' => $number,
            ])
            ->assertRedirect();
        $this->assertSame($number, $document->fresh()->numbering?->document_number);

        // 3. Submit ke approval (Draft → Pending).
        $this->actingAs($this->adminUser)
            ->post(route('admin.document_builder.submit', $document->id))
            ->assertRedirect();
        $this->assertSame(DocumentStatus::Pending, $document->fresh()->status);

        // 4. Director approve dengan PIN (Pending → Approved).
        $this->actingAs($this->directorUser)
            ->post(route('director.approval.approve', $document->id), ['pin' => '123456'])
            ->assertRedirect();
        $this->assertSame(DocumentStatus::Approved, $document->fresh()->status);

        // 5. Director publish (Approved → Published).
        $this->actingAs($this->directorUser)
            ->post(route('director.approval.publish', $document->id))
            ->assertRedirect();

        $published = $document->fresh();
        $this->assertSame(DocumentStatus::Published, $published->status);

        // 6. Token UUID v4 + QR tersedia.
        $token = $published->qrVerification?->verification_token;
        $this->assertIsString($token);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $token,
        );
        $this->assertNotNull($published->qrVerification?->qr_path);

        // 7. Public verify: valid.
        $response = $this->get(route('verify.document', $token));
        $response->assertOk();
        $response->assertViewIs('public.verification.valid');
        $response->assertSee($number);
    }

    public function test_audit_monitoring_has_no_mutation_routes(): void
    {
        $methods = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn ($route) => str_contains($route->uri(), 'verification-audit'))
            ->flatMap(fn ($route) => $route->methods())
            ->unique()
            ->sort()
            ->values()
            ->all();

        $this->assertEquals(['GET', 'HEAD'], $methods);
    }

    public function test_audit_monitoring_denies_client_and_vendor_even_on_detail(): void
    {
        $document = $this->makeApprovedDocument();
        $qr = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => (string) \Illuminate\Support\Str::uuid(),
            'qr_path' => null,
            'generated_by' => $this->adminUser->id,
            'generated_at' => now(),
        ]);

        $log = \App\Models\DocumentVerificationLog::create([
            'verification_id' => $qr->id,
            'verified_at' => now(),
            'status' => \App\Models\DocumentVerificationLog::STATUS_VALID,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'WorkflowHardeningTest',
        ]);

        $this->actingAs($this->clientUser)->get(route('admin.verification-audit.index'))->assertForbidden();
        $this->actingAs($this->clientUser)->get(route('admin.verification-audit.show', $log))->assertForbidden();
        $this->actingAs($this->vendorUser)->get(route('admin.verification-audit.index'))->assertForbidden();
        $this->actingAs($this->vendorUser)->get(route('admin.verification-audit.show', $log))->assertForbidden();
    }

    public function test_director_can_download_history_pdf_but_not_other_documents(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/h4-dl.pdf', '%PDF-1.4 h4 download test');

        $published = $this->makeApprovedDocument();
        $published->status = DocumentStatus::Published;
        $published->file_path = 'documents/h4-dl.pdf';
        $published->save();

        $response = $this->actingAs($this->directorUser)
            ->get(route('director.approval.history-download', $published->id));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');

        // Dokumen Draft (di luar scope riwayat Director) → 404, bukan melayani file.
        $draft = $this->makeDraftDocument();
        $draft->file_path = 'documents/h4-dl.pdf';
        $draft->save();

        $this->actingAs($this->directorUser)
            ->get(route('director.approval.history-download', $draft->id))
            ->assertNotFound();
    }

    public function test_director_history_download_denies_other_roles(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/h4-dl2.pdf', '%PDF-1.4 h4 download test');

        $published = $this->makeApprovedDocument();
        $published->status = DocumentStatus::Published;
        $published->file_path = 'documents/h4-dl2.pdf';
        $published->save();

        $uri = route('director.approval.history-download', $published->id);

        $this->actingAs($this->adminUser)->get($uri)->assertForbidden();
        $this->actingAs($this->clientUser)->get($uri)->assertForbidden();
        $this->actingAs($this->vendorUser)->get($uri)->assertForbidden();
    }

    public function test_director_history_download_redirects_guest_to_login(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/h4-dl3.pdf', '%PDF-1.4 h4 download test');

        $published = $this->makeApprovedDocument();
        $published->status = DocumentStatus::Published;
        $published->file_path = 'documents/h4-dl3.pdf';
        $published->save();

        $uri = route('director.approval.history-download', $published->id);

        $this->get($uri)->assertRedirect(route('login'));
    }
}