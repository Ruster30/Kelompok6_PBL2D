<?php

namespace Tests\Feature\DDMS;

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\User;
use App\Services\DdmsSettingService;
use App\Services\DocumentApprovalService;
use App\Services\DocumentNumberService;
use App\Services\DocumentBuilderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TempPdfVerifyTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_contains_ddms_number_and_qr(): void
    {
        Storage::fake('public');

        $admin  = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);
        $director = User::factory()->create(['role' => 'director']);
        $event  = Event::factory()->withClient($client)->create();

        app(DdmsSettingService::class)->updateSetting('ddms_enabled', '1', 'toggle');
        app(DdmsSettingService::class)->updateSetting('ddms_default_penawaran', '1', 'default');

        // 1-2. Masuk DDMS → draft
        $this->actingAs($admin)->post(route('admin.requests.masuk-ke-ddms', $event->id), [
            'nomor_surat' => 'PEN-001',
            'tanggal_surat' => now()->format('Y-m-d'),
            'uses_ddms' => '1',
        ]);

        $proposal = Proposal::where('event_id', $event->id)->latest('id')->firstOrFail();
        $document = Document::findOrFail($proposal->document_id);
        $this->assertSame('draft', $document->status->value);

        // 3. Set DocumentNumbering
        app(DocumentNumberService::class)->setManualNumber($document, 'PRO-TEST-001', $admin);

        // 4-5. Submit + Approve
        app(DocumentApprovalService::class)->submit($document, $admin);
        $this->assertSame('pending', Document::findOrFail($document->id)->status->value);
        $director->update(['approval_pin' => Hash::make('123456')]);
        app(DocumentApprovalService::class)->directorApprove($document, $director, '123456');

        $document->refresh();
        $this->assertSame('approved', $document->status->value);

        // 6-8. Verify PDF rewritten with canonical template + DDMS number
        $pdfPath = $proposal->file_proposal;
        $this->assertNotNull($pdfPath);
        $this->assertTrue(Storage::disk('public')->exists($pdfPath), 'PDF file exists after approve');

        $pdfContent = Storage::disk('public')->get($pdfPath);
        $this->assertStringStartsWith('%PDF-', $pdfContent);

        // Decompress PDF streams (dompdf uses FlateDecode) and confirm the
        // DDMS number is actually embedded in the PDF bytes (not just DB).
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
        $this->assertStringContainsString('PRO-TEST-001', $decompressed, 'DDMS number embedded in actual PDF bytes after approve');

        // dompdf compresses text streams (FlateDecode), so verify the rendered
        // HTML (which dompdf converts to PDF) contains the number + the QR img.
        $renderedHtml = \Illuminate\Support\Facades\View::make('admin.requests.surat_penawaran_pdf', [
            'event' => $event,
            'data'  => [
                'nomor_surat' => 'PRO-TEST-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'perihal' => 'x',
                'document' => $document->load(['numbering', 'qrVerification']),
            ],
        ])->render();
        $this->assertStringContainsString('PRO-TEST-001', $renderedHtml, 'DDMS number in rendered template');
        $this->assertStringContainsString('No. Surat', $renderedHtml);

        $clientNotifBeforePublish = Notification::where('user_id', $client->id)->count();
        $this->assertSame(0, $clientNotifBeforePublish, 'No client notification after approve');

        // 9-12. Publish → token + QR created
        app(DocumentApprovalService::class)->publishDocument($document, $director);
        $document->refresh();
        $this->assertSame('published', $document->status->value);
        $this->assertNotNull($document->qrVerification?->verification_token);
        $this->assertNotNull($document->qrVerification?->qr_path);

        // 13-14. PDF rewritten with number + QR
        $pdfContent2 = Storage::disk('public')->get($pdfPath);
        $this->assertStringContainsString('/Subtype /Image', $pdfContent2, 'QR image embedded in PDF after publish');

        $decompressed2 = '';
        $off2 = 0;
        while (($s = strpos($pdfContent2, 'stream', $off2)) !== false) {
            $e = strpos($pdfContent2, 'endstream', $s);
            if ($e === false) break;
            $st = substr($pdfContent2, $s + 6, $e - $s - 6);
            $st = ltrim($st, "\r\n");
            $d = @gzuncompress($st);
            if ($d !== false) { $decompressed2 .= $d; }
            $off2 = $e + 9;
        }
        $this->assertStringContainsString('PRO-TEST-001', $decompressed2, 'DDMS number embedded in actual PDF bytes after publish');

        $renderedHtml2 = \Illuminate\Support\Facades\View::make('admin.requests.surat_penawaran_pdf', [
            'event' => $event,
            'data'  => [
                'nomor_surat' => 'PRO-TEST-001',
                'tanggal_surat' => now()->format('Y-m-d'),
                'perihal' => 'x',
                'document' => $document->load(['numbering', 'qrVerification']),
            ],
        ])->render();
        $this->assertStringContainsString('PRO-TEST-001', $renderedHtml2, 'DDMS number in rendered template after publish');
        $this->assertStringContainsString('Scan QR', $renderedHtml2, 'QR block in rendered template after publish');

        // 15. file path consistency
        $this->assertSame($proposal->file_proposal, $document->file_path, 'file_proposal === file_path');

        // 16. No second PDF in documents/
        // (no assertions needed; just ensure path under proposals/)
        $this->assertStringStartsWith('proposals/', $pdfPath);

        // Notification timing: only after Kirim ke Client
        $clientNotifAfterPublish = Notification::where('user_id', $client->id)->count();
        $this->assertSame(0, $clientNotifAfterPublish, 'No client notification after publish');

        // Admin clicks Kirim ke Client
        $this->actingAs($admin)->post(route('admin.requests.kirim-revisi-penawaran', $event->id), ['uses_ddms' => '1']);
        $clientNotifFinal = Notification::where('user_id', $client->id)->count();
        $this->assertSame(1, $clientNotifFinal, 'Client notified only after Kirim ke Client');

        fwrite(STDERR, "\n[VERIFY] PDF after approve has number: " . (str_contains($pdfContent, 'PRO-TEST-001') ? 'YES' : 'NO'));
        fwrite(STDERR, "\n[VERIFY] PDF after publish has number: " . (str_contains($pdfContent2, 'PRO-TEST-001') ? 'YES' : 'NO'));
        fwrite(STDERR, "\n[VERIFY] PDF after publish has QR image: " . (str_contains($pdfContent2, '/Subtype /Image') ? 'YES' : 'NO'));
        fwrite(STDERR, "\n[VERIFY] file_path: " . $pdfPath);
        fwrite(STDERR, "\n[VERIFY] document status: " . $document->status->value);
        fwrite(STDERR, "\n[VERIFY] numbering: " . ($document->numbering?->document_number ?? 'NULL'));
        fwrite(STDERR, "\n[VERIFY] qrVerification token: " . ($document->qrVerification?->verification_token ?? 'NULL'));
    }
}
