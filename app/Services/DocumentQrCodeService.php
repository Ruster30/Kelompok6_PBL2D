<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentQrVerification;
use App\Models\User;
use App\Repositories\Contracts\DocumentQrVerificationRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * DocumentQrCodeService
 *
 * Generate QR Code image dan menyimpannya ke storage.
 * QR berisi URL verifikasi: APP_URL/verify/{token}
 */
class DocumentQrCodeService
{
    private const QR_DIR = "qr";

    public function __construct(
        private readonly DocumentQrVerificationRepositoryInterface $qrRepository,
    ) {}

    /**
     * Generate QR Code untuk dokumen Approved.
     * Jika sudah ada, return existing.
     */
    public function generate(Document $document, User $generatedBy): DocumentQrVerification
    {
        // Cek apakah sudah ada QR
        $existing = $this->qrRepository->findByDocument($document->id);
        if ($existing) {
            return $existing;
        }

        $token    = bin2hex(random_bytes(16));
        $verifyUrl = url("/verify/{$token}");

        // Generate QR image via API
        $qrFileName = "qr-{$document->id}.png";
        $qrPath = self::QR_DIR . "/" . $qrFileName;

        try {
            $response = Http::timeout(10)
                ->get("https://api.qrserver.com/v1/create-qr-code/", [
                    "size" => "300x300",
                    "data" => $verifyUrl,
                ]);

            if ($response->successful()) {
                Storage::disk("public")->put($qrPath, $response->body());
            } else {
                Log::warning("QR API gagal, menyimpan tanpa gambar", [
                    "document_id" => $document->id,
                    "status" => $response->status(),
                ]);
                $qrPath = "";
            }
        } catch (\Exception $e) {
            Log::warning("QR API timeout, menyimpan tanpa gambar", [
                "document_id" => $document->id,
                "error" => $e->getMessage(),
            ]);
            $qrPath = "";
        }

        // Simpan metadata ke database
        $qr = $this->qrRepository->create([
            "document_id"         => $document->id,
            "verification_token"  => $token,
            "qr_path"             => $qrPath,
            "generated_by"        => $generatedBy->id,
            "generated_at"        => now(),
            "expires_at"          => now()->addYear(),
        ]);

        Log::info("QR Code berhasil dibuat", [
            "document_id" => $document->id,
            "qr_id" => $qr->id,
            "qr_path" => $qrPath,
        ]);

        return $qr;
    }
}