<?php

declare(strict_types=1);

namespace Tests\Feature\DDMS;

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentQrVerification;
use App\Models\DocumentVerificationLog;
use App\Models\DocumentNumbering;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VerificationAuditTest extends TestCase
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
            'name' => 'Admin User',
            'email' => 'admin-audit@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->directorUser = User::create([
            'name' => 'Director User',
            'email' => 'director-audit@test.com',
            'password' => bcrypt('password'),
            'role' => 'director',
        ]);

        $this->clientUser = User::create([
            'name' => 'Client User',
            'email' => 'client-audit@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);

        $this->vendorUser = User::create([
            'name' => 'Vendor User',
            'email' => 'vendor-audit@test.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
        ]);

        $this->event = Event::create([
            'client_id' => $this->clientUser->id,
            'nama_event' => 'Audit Event Alpha',
            'tanggal_event' => now()->toDateString(),
            'periode_awal' => now(),
            'periode_akhir' => now()->addDays(30),
        ]);
    }

    private function createLog(
        string $token,
        string $fileName,
        string $documentNumber,
        string $status,
        string $verifiedAt,
        string $source = DocumentVerificationLog::SOURCE_PUBLIC,
        ?User $verifiedBy = null,
    ): DocumentVerificationLog {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'sertifikat',
            'nama_file' => $fileName,
            'status' => DocumentStatus::Published,
            'document_source' => DocumentSource::Generated,
            'file_path' => '/path/' . $fileName,
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'DOC',
            'year' => 2026,
            'sequence_number' => random_int(1, 9999),
            'generated_by' => $this->adminUser->id,
            'document_number' => $documentNumber,
        ]);

        $verification = DocumentQrVerification::create([
            'document_id' => $document->id,
            'verification_token' => $token,
            'qr_path' => '/qrcodes/' . $token . '.png',
            'generated_by' => $this->adminUser->id,
            'generated_at' => now(),
        ]);

        return DocumentVerificationLog::create([
            'verification_id' => $verification->id,
            'verified_at' => $verifiedAt,
            'status' => $status,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'VerificationAuditTest/11H.3',
            'verified_by' => $verifiedBy?->id,
            'verification_source' => $source,
        ]);
    }

    public function test_admin_can_access_audit_index(): void
    {
        $this->createLog('token-admin-index', 'contract_admin.pdf', 'DOC-2026-ADM', DocumentVerificationLog::STATUS_VALID, '2026-08-10 10:00:00');

        $response = $this->actingAs($this->adminUser)->get(route('admin.verification-audit.index'));

        $response->assertOk();
        $response->assertSee('Verification Audit');
        $response->assertSee('contract_admin.pdf');
        $response->assertSee('DOC-2026-ADM');
    }

    public function test_director_can_access_audit_index(): void
    {
        $this->createLog('token-director-index', 'contract_director.pdf', 'DOC-2026-DIR', DocumentVerificationLog::STATUS_VALID, '2026-08-10 10:00:00');

        $response = $this->actingAs($this->directorUser)->get(route('director.verification-audit.index'));

        $response->assertOk();
        $response->assertSee('Verification Audit');
        $response->assertSee('contract_director.pdf');
    }

    public function test_client_cannot_access_audit_index(): void
    {
        $response = $this->actingAs($this->clientUser)->get(route('admin.verification-audit.index'));
        $response->assertForbidden();
    }

    public function test_vendor_cannot_access_audit_index(): void
    {
        $response = $this->actingAs($this->vendorUser)->get(route('admin.verification-audit.index'));
        $response->assertForbidden();
    }

    public function test_guest_cannot_access_audit_index(): void
    {
        $response = $this->get(route('admin.verification-audit.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_pagination_works(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            $this->createLog("token-page-{$i}", "file_{$i}.pdf", "DOC-2026-{$i}", DocumentVerificationLog::STATUS_VALID, '2026-08-10 10:00:00');
        }

        $response = $this->actingAs($this->adminUser)->get(route('admin.verification-audit.index'));

        $response->assertOk();
        $response->assertSee('file_1.pdf');
        $response->assertSee('?page=2', false);
    }

    public function test_status_filter_works(): void
    {
        $this->createLog('token-status-valid', 'valid_file.pdf', 'DOC-VALID', DocumentVerificationLog::STATUS_VALID, '2026-08-10 10:00:00');
        $this->createLog('token-status-expired', 'expired_file.pdf', 'DOC-EXP', DocumentVerificationLog::STATUS_EXPIRED, '2026-08-10 10:00:00');

        $response = $this->actingAs($this->adminUser)->get(route('admin.verification-audit.index', [
            'status' => DocumentVerificationLog::STATUS_EXPIRED,
        ]));

        $response->assertOk();
        $response->assertSee('expired_file.pdf');
        $response->assertDontSee('valid_file.pdf');
    }

    public function test_date_range_filter_works(): void
    {
        $this->createLog('token-date-old', 'old_file.pdf', 'DOC-OLD', DocumentVerificationLog::STATUS_VALID, '2026-08-01 10:00:00');
        $this->createLog('token-date-new', 'new_file.pdf', 'DOC-NEW', DocumentVerificationLog::STATUS_VALID, '2026-08-15 10:00:00');

        $response = $this->actingAs($this->adminUser)->get(route('admin.verification-audit.index', [
            'date_from' => '2026-08-10',
            'date_to' => '2026-08-16',
        ]));

        $response->assertOk();
        $response->assertSee('new_file.pdf');
        $response->assertDontSee('old_file.pdf');
    }

    public function test_search_works_for_document_number_and_name(): void
    {
        $this->createLog('token-search-1', 'special_contract_alpha.pdf', 'DOC-SEARCH-001', DocumentVerificationLog::STATUS_VALID, '2026-08-10 10:00:00');
        $this->createLog('token-search-2', 'general_file.pdf', 'DOC-GENERAL-002', DocumentVerificationLog::STATUS_VALID, '2026-08-10 10:00:00');

        $responseByName = $this->actingAs($this->adminUser)->get(route('admin.verification-audit.index', [
            'search' => 'special_contract_alpha',
        ]));
        $responseByName->assertOk();
        $responseByName->assertSee('special_contract_alpha.pdf');
        $responseByName->assertDontSee('general_file.pdf');

        $responseByNumber = $this->actingAs($this->adminUser)->get(route('admin.verification-audit.index', [
            'search' => 'DOC-SEARCH-001',
        ]));
        $responseByNumber->assertOk();
        $responseByNumber->assertSee('special_contract_alpha.pdf');
        $responseByNumber->assertDontSee('general_file.pdf');
    }

    public function test_detail_page_works(): void
    {
        $log = $this->createLog(
            'token-detail-view',
            'detail_document.pdf',
            'DOC-DETAIL-001',
            DocumentVerificationLog::STATUS_VALID,
            '2026-08-12 14:30:00',
            DocumentVerificationLog::SOURCE_PUBLIC,
            $this->adminUser,
        );

        $response = $this->actingAs($this->adminUser)->get(route('admin.verification-audit.show', $log));

        $response->assertOk();
        $response->assertSee('Verification Audit Detail');
        $response->assertSee('detail_document.pdf');
        $response->assertSee('DOC-DETAIL-001');
        $response->assertSee('token-detail-view');
    }

    public function test_unauthorized_user_cannot_access_detail(): void
    {
        $log = $this->createLog('token-unauthorized-detail', 'private_document.pdf', 'DOC-PRIVATE-001', DocumentVerificationLog::STATUS_VALID, '2026-08-12 14:30:00');

        $response = $this->actingAs($this->clientUser)->get(route('admin.verification-audit.show', $log));

        $response->assertForbidden();
    }

    public function test_index_avoids_n_plus_one_queries(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->createLog("token-n1-{$i}", "n1_file_{$i}.pdf", "DOC-N1-{$i}", DocumentVerificationLog::STATUS_VALID, '2026-08-10 10:00:00');
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($this->adminUser)->get(route('admin.verification-audit.index'));

        $response->assertOk();

        $queryCount = count(DB::getQueryLog());
        $this->assertLessThanOrEqual(16, $queryCount);
    }
}

