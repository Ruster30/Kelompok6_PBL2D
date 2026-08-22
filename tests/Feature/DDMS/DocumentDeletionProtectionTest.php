<?php

declare(strict_types=1);

namespace Tests\Feature\DDMS;

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Exceptions\DDMS\DDMSException;
use App\Models\Document;
use App\Models\DocumentNumbering;
use App\Models\DocumentQrVerification;
use App\Models\DocumentVerificationLog;
use App\Models\Event;
use App\Models\User;
use App\Services\AdminProposalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Phase 11H.6 — Proteksi audit trail dari penghapusan dokumen Published.
 *
 * Aturan bisnis: dokumen Published TIDAK boleh di-hard-delete permanen
 * (melindungi QR verification + verification audit log via FK cascade).
 * Perilaku penghapusan dokumen Draft tetap dipertahankan.
 */
class DocumentDeletionProtectionTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'H6 Admin',
            'email' => 'h6-admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $clientUser = User::create([
            'name' => 'H6 Client',
            'email' => 'h6-client@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);

        $this->event = Event::create([
            'client_id' => $clientUser->id,
            'nama_event' => 'H6 Event Alpha',
            'tanggal_event' => now()->toDateString(),
            'periode_awal' => now(),
            'periode_akhir' => now()->addDays(30),
        ]);
    }

    private function makePublishedDocumentWithQrAndLog(): Document
    {
        $document = Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'proposal',
            'nama_file' => 'h6_published_' . Str::random(6) . '.pdf',
            'status' => DocumentStatus::Published,
            'document_source' => DocumentSource::Generated,
            'file_path' => 'documents/h6_published.pdf',
        ]);

        DocumentNumbering::create([
            'document_id' => $document->id,
            'prefix' => 'MANUAL',
            'year' => (int) now()->format('Y'),
            'sequence_number' => 0,
            'generated_by' => $this->adminUser->id,
            'document_number' => 'H6-PUB-' . $document->id,
        ]);

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
            'user_agent' => 'DocumentDeletionProtectionTest',
        ]);

        return $document;
    }

    private function makeDraftDocument(): Document
    {
        return Document::create([
            'event_id' => $this->event->id,
            'tipe' => 'proposal',
            'nama_file' => 'h6_draft_' . Str::random(6) . '.pdf',
            'status' => DocumentStatus::Draft,
            'document_source' => DocumentSource::Generated,
            'file_path' => 'documents/h6_draft.pdf',
        ]);
    }

    public function test_published_document_cannot_be_hard_deleted(): void
    {
        $document = $this->makePublishedDocumentWithQrAndLog();
        $service = app(AdminProposalService::class);

        try {
            $service->deleteDocument($document);
            $this->fail('Dokumen Published seharusnya tidak dapat dihapus permanen.');
        } catch (DDMSException $e) {
            $this->assertStringContainsString('dipublish', $e->getMessage());
        }

        // Dokumen, QR verification, dan audit log tetap ada.
        $this->assertNotNull(Document::find($document->id));
        $this->assertSame(1, DocumentQrVerification::where('document_id', $document->id)->count());
        $this->assertSame(1, DocumentVerificationLog::count());
    }

    public function test_http_delete_of_published_document_is_rejected_with_error(): void
    {
        $document = $this->makePublishedDocumentWithQrAndLog();

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.documents.destroy', $document->id));

        $response->assertRedirect(route('admin.documents.index'));
        $response->assertSessionHas('error');

        $this->assertNotNull(Document::find($document->id));
        $this->assertSame(1, DocumentQrVerification::where('document_id', $document->id)->count());
        $this->assertSame(1, DocumentVerificationLog::count());
    }

    public function test_draft_document_deletion_still_works(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/h6_draft.pdf', '%PDF-1.4');

        $document = $this->makeDraftDocument();

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.documents.destroy', $document->id));

        $response->assertRedirect(route('admin.documents.index'));
        $response->assertSessionHas('success');

        $this->assertNull(Document::find($document->id));
    }
}