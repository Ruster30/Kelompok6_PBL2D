<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DocumentSource;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Repositories\Contracts\DocumentQrVerificationRepositoryInterface;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * DocumentQrCodeService
 *
 * Generate QR Code image dan menyimpannya ke storage.
 *
 * Phase 11G.1:
 * - QR hanya dibuat untuk dokumen Published (source Generated).
 * - QR berisi URL verifikasi: APP_URL/verify/{verification_token}.
 * - QR bersifat immutable: tidak pernah digenerate ulang.
 */
class DocumentQrCodeService
{
    private const QR_PUBLIC_DIR = "document-qr";

    public function __construct(
        private readonly DocumentQrVerificationRepositoryInterface $qrRepository,
    ) {}

    /**
     * Dapatkan QR Code untuk dokumen Published.
     * Jika QR sudah ada dan file masih ada di storage, return path yang sama.
     *
     * Business Rules:
     * - Status harus Published.
     * - Document Source harus Generated.
     * - Verification Token wajib sudah ada.
     * - QR tidak pernah digenerate ulang.
     */
    public function getOrCreateQrCode(Document $document): string
    {
        // 1. Validasi status Published
        if ($document->status !== DocumentStatus::Published) {
            throw ValidationException::withMessages([
                "qr" => "QR hanya dapat dibuat untuk dokumen Published.",
            ]);
        }

        // 2. Validasi document source Generated
        if ($document->document_source !== DocumentSource::Generated) {
            throw ValidationException::withMessages([
                "qr" => "QR hanya dapat dibuat untuk dokumen Generated.",
            ]);
        }

        // 3. Verification Token wajib sudah ada
        $qrVerification = $this->qrRepository->findByDocument($document->id);
        if (! $qrVerification || ! $qrVerification->verification_token) {
            throw ValidationException::withMessages([
                "qr" => "Verification token wajib ada sebelum QR dibuat.",
            ]);
        }

        // 4. Jika QR sudah ada dan file masih ada di storage -> return existing
        if ($qrVerification->qr_path && Storage::disk("public")->exists($qrVerification->qr_path)) {
            return $qrVerification->qr_path;
        }

        // 5-6. Generate QR PNG berisi URL verifikasi
        $verifyUrl = url("/verify/" . $qrVerification->verification_token);
        $qrPath    = self::QR_PUBLIC_DIR . "/" . $qrVerification->verification_token . ".png";

        Storage::disk("public")->put($qrPath, $this->renderPng($verifyUrl));

        // 7. Update qr_path
        $this->qrRepository->update($qrVerification, [
            "qr_path" => $qrPath,
        ]);

        Log::info("QR Code dibuat untuk dokumen Published", [
            "document_id" => $document->id,
            "qr_path"     => $qrPath,
        ]);

        // 8. Return path
        return $qrPath;
    }

    /**
     * Render QR Code menjadi PNG menggunakan BaconQrCode + GD.
     * Tanpa dependency Imagick maupun API eksternal.
     */
    private function renderPng(string $text): string
    {
        $qrcode = Encoder::encode($text, ErrorCorrectionLevel::M());
        $matrix = $qrcode->getMatrix();

        $scale  = 8;
        $margin = 4;
        $size   = ($matrix->getWidth() + ($margin * 2)) * $scale;

        $image = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        imagefilledrectangle($image, 0, 0, $size, $size, $white);

        for ($y = 0; $y < $matrix->getHeight(); $y++) {
            for ($x = 0; $x < $matrix->getWidth(); $x++) {
                if ($matrix->get($x, $y)) {
                    $px = ($x + $margin) * $scale;
                    $py = ($y + $margin) * $scale;
                    imagefilledrectangle($image, $px, $py, $px + $scale - 1, $py + $scale - 1, $black);
                }
            }
        }

        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();
        imagedestroy($image);

        return $png;
    }
}
