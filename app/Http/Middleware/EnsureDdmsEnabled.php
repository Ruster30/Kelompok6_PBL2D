<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Document;
use App\Services\DdmsSettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureDdmsEnabled
 *
 * Menegakkan dua kondisi untuk operasi DDMS lanjutan (submit/approve/publish):
 *   1. Global master switch ddms_enabled = 1 (DDMS aktif).
 *   2. Dokumen target uses_ddms = true (dokumen ini memang DDMS).
 *
 * Jika salah satu gagal -> redirect back dengan pesan yang sesuai.
 * Tidak memengaruhi dokumen Published lama, QR/token, atau public verification.
 */
class EnsureDdmsEnabled
{
    public function __construct(
        private readonly DdmsSettingService $settingService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $enabled = (string) $this->settingService->getSettingValue('ddms_enabled', '1');

        if ($enabled !== '1') {
            return redirect()->back()
                ->with('error', 'DDMS sedang dinonaktifkan oleh administrator.');
        }

        $document = $request->route('document');
        if ($document instanceof Document && ! $document->uses_ddms) {
            return redirect()->back()
                ->with('error', 'Dokumen ini tidak menggunakan DDMS.');
        }

        return $next($request);
    }
}