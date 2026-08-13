<?php
declare(strict_types=1);

namespace Tests\Feature\DDMS;

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentQrVerification;
use App\Models\DocumentNumbering;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $clientUser;
    private User $adminUser;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientUser = User::create([
            'name' => 'Test Client User',
            'email' => 'client@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);
        $this->adminUser = User::create([
            'name' => 'Test Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->event = Event::create([
            'client_id' => $this->clientUser->id,
            'nama_event' => 'Test Event',
            'tanggal_event' => now()->toDateString(),
            'periode_awal' => now(),
            'periode_akhir' => now()->addDays(30),
        ]);
    }

    public function test_valid_token_published_generated_with_document_number()
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => 'document_2026_001.pdf',
            'status' => DocumentStatus::Published,
            'document_source' => DocumentSource::Generated,
            'file_path' => '/path/to/file.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'DOC',
            'year' => 2026,
            'sequence_number' => 1,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'DOC-2026-001',
            'formatted_number' => 'DOC/2026/001',
        ]);

        $verification = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => 'valid-token-12345',
            'qr_code_path' => '/path/to/qr.png',
            'generated_at' => now(),
        ]);

        $response = $this->get("/verify/{$verification->verification_token}");

        $response->assertStatus(200);
        $response->assertViewIs('public.verification.valid');
        $response->assertViewHas('document', $document);
        $response->assertSee('Dokumen Terverifikasi');
        $response->assertSee('DOC-2026-001');
        $response->assertSee('Published');
    }

    public function test_token_not_found()
    {
        $response = $this->get('/verify/invalid-token-xyz');
        $response->assertStatus(200);
        $response->assertViewIs('public.verification.not-found');
        $response->assertSee('Dokumen Tidak Ditemukan');
    }

    public function test_token_valid_but_document_is_draft()
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => 'document_draft.pdf',
            'status' => DocumentStatus::Draft,
            'document_source' => DocumentSource::Generated,
            'file_path' => '/path/to/file.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'DOC',
            'year' => 2026,
            'sequence_number' => 2,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'DOC-2026-001',
            'formatted_number' => 'DOC/2026/001',
        ]);

        $verification = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => 'draft-token-12345',
            'qr_code_path' => '/path/to/qr.png',
            'generated_at' => now(),
        ]);

        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $response->assertViewIs('public.verification.invalid');
        $response->assertSee('Dokumen Belum Dapat Diverifikasi');
    }

    public function test_token_valid_but_document_is_pending()
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => 'document_pending.pdf',
            'status' => DocumentStatus::Pending,
            'document_source' => DocumentSource::Generated,
            'file_path' => '/path/to/file.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'DOC',
            'year' => 2026,
            'sequence_number' => 3,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'DOC-2026-002',
            'formatted_number' => 'DOC/2026/002',
        ]);

        $verification = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => 'pending-token-12345',
            'qr_code_path' => '/path/to/qr.png',
            'generated_at' => now(),
        ]);

        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $response->assertViewIs('public.verification.invalid');
    }

    public function test_token_valid_but_document_is_approved()
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => 'document_approved.pdf',
            'status' => DocumentStatus::Approved,
            'document_source' => DocumentSource::Generated,
            'file_path' => '/path/to/file.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'DOC',
            'year' => 2026,
            'sequence_number' => 4,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'DOC-2026-003',
            'formatted_number' => 'DOC/2026/003',
        ]);

        $verification = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => 'approved-token-12345',
            'qr_code_path' => '/path/to/qr.png',
            'generated_at' => now(),
        ]);

        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $response->assertViewIs('public.verification.invalid');
    }

    public function test_token_valid_but_document_is_rejected()
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => 'document_rejected.pdf',
            'status' => DocumentStatus::Rejected,
            'document_source' => DocumentSource::Generated,
            'file_path' => '/path/to/file.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'DOC',
            'year' => 2026,
            'sequence_number' => 5,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'DOC-2026-004',
            'formatted_number' => 'DOC/2026/004',
        ]);

        $verification = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => 'rejected-token-12345',
            'qr_code_path' => '/path/to/qr.png',
            'generated_at' => now(),
        ]);

        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $response->assertViewIs('public.verification.invalid');
    }

    public function test_token_valid_but_document_is_uploaded_not_generated()
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => 'document_uploaded.pdf',
            'status' => DocumentStatus::Published,
            'document_source' => DocumentSource::Uploaded,
            'file_path' => '/path/to/file.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'DOC',
            'year' => 2026,
            'sequence_number' => 6,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'DOC-2026-005',
            'formatted_number' => 'DOC/2026/005',
        ]);

        $verification = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => 'uploaded-token-12345',
            'qr_code_path' => '/path/to/qr.png',
            'generated_at' => now(),
        ]);

        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $response->assertViewIs('public.verification.invalid');
    }

    public function test_token_valid_published_generated_but_no_document_number()
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => 'document_nonumber.pdf',
            'status' => DocumentStatus::Published,
            'document_source' => DocumentSource::Generated,
            'file_path' => '/path/to/file.pdf',
        ]);

        $verification = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => 'no-number-token-12345',
            'qr_code_path' => '/path/to/qr.png',
            'generated_at' => now(),
        ]);

        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $response->assertViewIs('public.verification.invalid');
    }

    public function test_public_verification_requires_no_authentication()
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => 'document_public.pdf',
            'status' => DocumentStatus::Published,
            'document_source' => DocumentSource::Generated,
            'file_path' => '/path/to/file.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'DOC',
            'year' => 2026,
            'sequence_number' => 7,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'DOC-2026-006',
            'formatted_number' => 'DOC/2026/006',
        ]);

        $verification = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => 'public-token-12345',
            'qr_code_path' => '/path/to/qr.png',
            'generated_at' => now(),
        ]);

        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $response->assertViewIs('public.verification.valid');
    }
}