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

    private function createValidPublishedDocument(string $token = 'valid-token-12345'): array
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => 'document_security_test.pdf',
            'status' => DocumentStatus::Published,
            'document_source' => DocumentSource::Generated,
            'file_path' => '/path/to/file.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'DOC',
            'year' => 2026,
            'sequence_number' => 77,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'DOC-2026-SECURITY',
            'formatted_number' => 'DOC/2026/SECURITY',
        ]);

        $verification = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => $token,
            'qr_code_path' => '/path/to/qr.png',
            'generated_at' => now(),
        ]);

        return [$document, $verification];
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

    public function test_rate_limit_30_requests_per_minute()
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => 'document_ratelimit.pdf',
            'status' => DocumentStatus::Published,
            'document_source' => DocumentSource::Generated,
            'file_path' => '/path/to/file.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'DOC',
            'year' => 2026,
            'sequence_number' => 99,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'DOC-2026-RATELIMIT',
            'formatted_number' => 'DOC/2026/RATELIMIT',
        ]);

        $verification = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => 'rate-limit-token-test',
            'qr_code_path' => '/path/to/qr.png',
            'generated_at' => now(),
        ]);

        $token = $verification->verification_token;

        for ($i = 0; $i < 30; $i++) {
            $response = $this->get("/verify/{$token}");
            $response->assertStatus(200)
                ->assertViewIs('public.verification.valid');
        }

        $response = $this->get("/verify/{$token}");
        $response->assertStatus(429);
    }

    public function test_token_too_short_returns_safe_error()
    {
        [$document, $verification] = $this->createValidPublishedDocument();

        $response = $this->get('/verify/abc123');

        $response->assertStatus(200);
        $response->assertViewIs('public.verification.not-found');
        $response->assertSee('Dokumen Tidak Ditemukan');
        $response->assertDontSee('Dokumen Terverifikasi');
        $response->assertDontSee('SQLSTATE');
        $response->assertDontSee('QueryException');
        $response->assertDontSee('SQL');
        $response->assertDontSee('Exception');
        $response->assertDontSee('Stack trace');
        
        
    }

    public function test_token_with_invalid_characters_returns_safe_error()
    {
        $this->createValidPublishedDocument();

        $invalidTokens = [
            'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ',
            'abcd1234!@#$%^&*()_+[]{}|;:,.?',
        ];

        foreach ($invalidTokens as $token) {
            $response = $this->get('/verify/' . rawurlencode($token));

            $response->assertStatus(200);
            $response->assertViewIs('public.verification.not-found');
            $response->assertSee('Dokumen Tidak Ditemukan');
            $response->assertDontSee('Dokumen Terverifikasi');
            $response->assertDontSee('SQLSTATE');
            $response->assertDontSee('QueryException');
            $response->assertDontSee('Exception');
            $response->assertDontSee('Stack trace');
        }
    }

    public function test_sql_injection_payload_cannot_bypass_verification()
    {
        [$document] = $this->createValidPublishedDocument();

        $payloads = [
            "' OR '1'='1",
            "' OR 1=1 --",
            "' UNION SELECT * FROM users --",
            "'; DROP TABLE documents; --",
            "' OR ''='",
            "1' OR '1'='1' --",
        ];

        foreach ($payloads as $payload) {
            $response = $this->get('/verify/' . rawurlencode($payload));

            $response->assertStatus(200);
            $response->assertViewIs('public.verification.not-found');
            $response->assertSee('Dokumen Tidak Ditemukan');
            $response->assertDontSee('Dokumen Terverifikasi');
            $response->assertDontSee('SQLSTATE');
            $response->assertDontSee('QueryException');
            $response->assertDontSee('Syntax error');
            $response->assertDontSee('PDO');
            $response->assertDontSee('Exception');
            $response->assertDontSee('Stack trace');
            
            $response->assertDontSee($document->numbering->document_number);
        }

        $this->assertSame(DocumentStatus::Published, $document->fresh()->status);
        $this->assertSame(1, Document::count());
        $this->assertSame(1, DocumentNumbering::count());
        $this->assertSame(1, DocumentQrVerification::count());
    }

    public function test_empty_token_route_handled_by_framework()
    {
        $response = $this->get('/verify/');

        $response->assertStatus(404);
        $response->assertDontSee('Dokumen Terverifikasi');
        $response->assertDontSee('SQLSTATE');
        $response->assertDontSee('QueryException');
    }

    public function test_malformed_token_does_not_create_new_token_or_qr()
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => 'document_no_qr.pdf',
            'status' => DocumentStatus::Published,
            'document_source' => DocumentSource::Generated,
            'file_path' => '/path/to/file.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'DOC',
            'year' => 2026,
            'sequence_number' => 78,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'DOC-2026-NOQR',
            'formatted_number' => 'DOC/2026/NOQR',
        ]);

        $tokensBefore = DocumentQrVerification::count();

        foreach (['abc123', 'xxxx', "'; DROP TABLE qr; --"] as $token) {
            $this->get('/verify/' . rawurlencode($token));
        }

        $this->assertSame($tokensBefore, DocumentQrVerification::count());

        $document->refresh();
        $this->assertSame(DocumentStatus::Published, $document->status);
        $this->assertNull($document->qrVerification);
    }

    
    public function test_valid_verification_creates_audit_log_in_database()
    {
        [$document, $verification] = $this->createValidPublishedDocument("audit-token-test");
        $logCountBefore = \App\Models\DocumentVerificationLog::count();
        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200)->assertViewIs("public.verification.valid");
        $logCountAfter = \App\Models\DocumentVerificationLog::count();
        $this->assertEquals($logCountBefore + 1, $logCountAfter);
        $log = \App\Models\DocumentVerificationLog::where("verification_id", $verification->id)->first();
        $this->assertNotNull($log);
        $this->assertSame($verification->id, $log->verification_id);
        $this->assertSame(\App\Models\DocumentVerificationLog::STATUS_VALID, $log->status);
    }

    public function test_valid_verification_log_contains_ip_address()
    {
        [$document, $verification] = $this->createValidPublishedDocument("ip-token-test");
        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $log = \App\Models\DocumentVerificationLog::where("verification_id", $verification->id)->first();
        $this->assertNotNull($log);
        $this->assertNotNull($log->ip_address);
        $this->assertNotEmpty($log->ip_address);
    }

    public function test_valid_verification_log_contains_user_agent()
    {
        [$document, $verification] = $this->createValidPublishedDocument("ua-token-test");
        $response = $this->withHeader("User-Agent", "CustomTestClient/11H.2C")->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $log = \App\Models\DocumentVerificationLog::where("verification_id", $verification->id)->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString("CustomTestClient", $log->user_agent);
    }

    public function test_valid_verification_log_uses_correct_status_and_source()
    {
        [$document, $verification] = $this->createValidPublishedDocument("status-token-test");
        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $log = \App\Models\DocumentVerificationLog::where("verification_id", $verification->id)->first();
        $this->assertNotNull($log);
        $this->assertSame(\App\Models\DocumentVerificationLog::STATUS_VALID, $log->status);
        $this->assertSame(\App\Models\DocumentVerificationLog::SOURCE_PUBLIC, $log->verification_source);
    }

    public function test_invalid_token_does_not_create_database_audit_log()
    {
        $logCountBefore = \App\Models\DocumentVerificationLog::count();
        $response = $this->get("/verify/invalid-token-xyz");
        $response->assertStatus(200)->assertViewIs("public.verification.not-found");
        $logCountAfter = \App\Models\DocumentVerificationLog::count();
        $this->assertSame($logCountBefore, $logCountAfter);
    }

    public function test_valid_response_includes_security_headers()
    {
        [$document, $verification] = $this->createValidPublishedDocument("security-headers-valid-token");
        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $response->assertHeader("X-Frame-Options", "DENY");
        $response->assertHeader("X-Content-Type-Options", "nosniff");
        $response->assertHeader("Strict-Transport-Security", "max-age=31536000; includeSubDomains");
        $response->assertHeader("Content-Security-Policy", "default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; font-src 'self' https://cdn.jsdelivr.net");
    }

    public function test_invalid_token_response_includes_security_headers()
    {
        $response = $this->get("/verify/invalid-security-headers-token");
        $response->assertStatus(200);
        $response->assertHeader("X-Frame-Options", "DENY");
        $response->assertHeader("X-Content-Type-Options", "nosniff");
        $response->assertHeader("Strict-Transport-Security", "max-age=31536000; includeSubDomains");
        $response->assertHeader("Content-Security-Policy", "default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; font-src 'self' https://cdn.jsdelivr.net");
    }

    public function test_draft_document_response_includes_security_headers()
    {
        $document = Document::create([
            "event_id" => $this->event->id,
            "tipe" => "sertifikat",
            "nama_file" => "document_draft_headers.pdf",
            "status" => DocumentStatus::Draft,
            "document_source" => DocumentSource::Generated,
            "file_path" => "/path/to/file.pdf",
        ]);

        DocumentNumbering::create([
            "document_id" => $document->id,
            "prefix" => "DOC",
            "year" => 2026,
            "sequence_number" => 99,
            "generated_by" => $this->adminUser->id,
            "document_number" => "DOC-2026-HDR",
            "formatted_number" => "DOC/2026/HDR",
        ]);

        $verification = DocumentQrVerification::create([
            "document_id" => $document->id,
            "verification_token" => "draft-security-headers-token",
            "qr_code_path" => "/path/to/qr.png",
            "generated_at" => now(),
        ]);

        $response = $this->get("/verify/{$verification->verification_token}");
        $response->assertStatus(200);
        $response->assertHeader("X-Frame-Options", "DENY");
        $response->assertHeader("X-Content-Type-Options", "nosniff");
        $response->assertHeader("Strict-Transport-Security", "max-age=31536000; includeSubDomains");
        $response->assertHeader("Content-Security-Policy", "default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; font-src 'self' https://cdn.jsdelivr.net");
    }
}
