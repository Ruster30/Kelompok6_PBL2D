<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Models\DocumentQrVerification;
use App\Services\DocumentVerificationLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * PublicVerificationController
 *
 * Endpoint publik untuk verifikasi dokumen melalui QR Code.
 * Tidak memerlukan authentication.
 * Hanya menampilkan informasi publik dari dokumen Published+Generated+valid token.
 */
class PublicVerificationController extends Controller
{
    public function __construct(
        private readonly DocumentVerificationLogService $logService,
    ) {}

    /**
     * Tampilkan halaman verifikasi dokumen berdasarkan token.
     *
     * Business Rule:
     * - Token harus ditemukan di document_qr_verifications.
     * - Dokumen harus Published + Generated + memiliki nomor surat.
     * - Jika valid: tampilkan informasi publik dokumen.
     * - Jika tidak valid: tampilkan halaman "Dokumen Tidak Ditemukan".
     * - Jangan membuat token baru, jangan regenerate QR, jangan edit dokumen.
     * - Jangan tampilkan data sensitif.
     * - Log setiap akses (valid/invalid/tampered).
     */
    public function verify(Request $request, string $token)
    {
        $ipAddress = $request->ip() ?? '';
        $userAgent = $request->userAgent() ?? '';

        // Cari token di database
        $qrVerification = DocumentQrVerification::where('verification_token', $token)
            ->with(['document.numbering', 'document.event.client', 'document.approvals'])
            ->first();

        // Token tidak ditemukan
        if (!$qrVerification) {
            Log::info('Verifikasi dokumen: token tidak ditemukan', [
                'token' => $token,
                'ip_address' => $ipAddress,
            ]);

            return view('public.verification.not-found', [
                'message' => 'Dokumen Tidak Ditemukan',
                'detail' => 'Token verifikasi tidak valid atau tidak terdaftar.',
            ]);
        }

        $document = $qrVerification->document;

        // Validasi state dokumen
        if (!$document || $document->status !== DocumentStatus::Published) {
            Log::warning('Verifikasi dokumen: dokumen bukan Published', [
                'token' => $token,
                'document_id' => $document?->id,
                'status' => $document?->status->value,
                'ip_address' => $ipAddress,
            ]);

            return view('public.verification.invalid', [
                'message' => 'Dokumen Belum Dapat Diverifikasi',
                'detail' => 'Status dokumen tidak sesuai untuk verifikasi.',
            ]);
        }

        if ($document->document_source !== DocumentSource::Generated) {
            Log::warning('Verifikasi dokumen: dokumen bukan Generated', [
                'token' => $token,
                'document_id' => $document->id,
                'source' => $document->document_source->value,
                'ip_address' => $ipAddress,
            ]);

            return view('public.verification.invalid', [
                'message' => 'Dokumen Belum Dapat Diverifikasi',
                'detail' => 'Dokumen ini bukan hasil generate sistem.',
            ]);
        }

        // Validasi nomor surat
        if (!$document->numbering || !$document->numbering->document_number) {
            Log::warning('Verifikasi dokumen: nomor surat tidak ada', [
                'token' => $token,
                'document_id' => $document->id,
                'ip_address' => $ipAddress,
            ]);

            return view('public.verification.invalid', [
                'message' => 'Dokumen Belum Dapat Diverifikasi',
                'detail' => 'Nomor surat tidak tersedia.',
            ]);
        }

        // Verifikasi valid — log akses
        try {
            $this->logService->logValidVerification(
                $qrVerification,
                null,
                $ipAddress,
                $userAgent,
            );
        } catch (\Exception $e) {
            Log::error('Verifikasi dokumen: gagal mencatat log', [
                'token' => $token,
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
        }

        Log::info('Verifikasi dokumen: dokumen terverifikasi', [
            'token' => $token,
            'document_id' => $document->id,
            'document_number' => $document->numbering->document_number,
            'ip_address' => $ipAddress,
        ]);

        $latestApproval = $document->approvals()
            ->where('status', 'approved')
            ->latest('reviewed_at')
            ->first();

        return view('public.verification.valid', [
            'document' => $document,
            'qrVerification' => $qrVerification,
            'approval' => $latestApproval,
        ]);
    }
}