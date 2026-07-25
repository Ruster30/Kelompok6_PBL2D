<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\DocumentQrVerification;
use App\Models\User;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * DocumentService
 *
 * Workflow Orchestrator DDMS.
 * Tidak berisi business logic — hanya mengorkestrasi Domain Services.
 *
 * Arsitektur:
 *   Controller
 *       ↓
 *   DocumentService (Orchestrator)   ←  class ini
 *       ↓
 *   Domain Services (Approval, Numbering, QR, Log)
 *       ↓
 *   Repositories
 *       ↓
 *   Models → Database
 *
 * @todo Migrasikan exception ke custom exception class.
 */
class DocumentService
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documentRepository,
        private readonly DocumentApprovalService $approvalService,
        private readonly DocumentNumberingService $numberingService,
        private readonly DocumentQrVerificationService $qrService,
        private readonly DocumentVerificationLogService $logService,
    ) {}

    // ── Submit for Approval ──────────────────────────────────

    /**
     * Workflow: Dokumen diajukan untuk approval.
     *
     * Flow:
     *   DocumentApprovalService::submit($document, $submittedBy)
     *
     * Business rule:
     *   - Hanya dokumen Draft yang boleh disubmit (divalidasi oleh ApprovalService)
     */
    public function submitForApproval(Document $document, User $submittedBy): DocumentApproval
    {
        return $this->approvalService->submit($document, $submittedBy);
    }

    // ── Approve Document ─────────────────────────────────────

    /**
     * Workflow: Dokumen disetujui → diberi nomor → diberi QR.
     *
     * Flow:
     *   1. Approve:    DocumentApprovalService::approve()
     *   2. Numbering:  DocumentNumberingService::generate()
     *   3. QR:         DocumentQrVerificationService::generate()
     *
     * Business rule:
     *   - Approval harus Pending (divalidasi oleh ApprovalService)
     *   - Dokumen harus Approved sebelum numbering (divalidasi oleh NumberingService)
     *   - Dokumen harus Published sebelum QR (divalidasi oleh QrService)
     *
     * @return array{DokumenApproval, DocumentNumbering, DocumentQrVerification}
     *
     * @throws \RuntimeException Jika salah satu langkah gagal (semua di-rollback)
     */
    public function approveDocument(DocumentApproval $approval, User $approver): array
    {
        return DB::transaction(function () use ($approval, $approver): array {
            // Step 1: Approve → ubah status jadi approved
            $approved = $this->approvalService->approve($approval, $approver);

            // Step 2: Generate nomor → ubah status jadi published
            $document = $approved->document;
            $numbering = $this->numberingService->generate($document, $approver);

            // Step 3: Generate QR metadata
            $qr = $this->qrService->generate($document, $approver);

            return [$approved, $numbering, $qr];
        });
    }

    // ── Reject Document ──────────────────────────────────────

    /**
     * Workflow: Dokumen ditolak.
     *
     * Flow:
     *   DocumentApprovalService::reject($approval, $approver, $reason)
     *
     * Business rule:
     *   - Approval harus Pending (divalidasi oleh ApprovalService)
     *   - Alasan wajib diisi (divalidasi oleh ApprovalService)
     */
    public function rejectDocument(DocumentApproval $approval, User $approver, string $reason): DocumentApproval
    {
        return $this->approvalService->reject($approval, $approver, $reason);
    }

    // ── Verify Document (Public Scan) ────────────────────────

    /**
     * Workflow: Verifikasi dokumen via QR token (publik/admin).
     *
     * Flow:
     *   1. Validasi token:   DocumentQrVerificationService::validateToken()
     *   2. Jika valid:       LogService::logValidVerification() → return QR
     *   3. Jika expired:     LogService::logExpiredVerification() → throw
     *
     * Business rule:
     *   - Token harus ditemukan (divalidasi oleh QrService)
     *   - Token belum expired (divalidasi oleh QrService)
     *   - Setiap verifikasi dictat di log (audit trail)
     *
     * @throws \RuntimeException Jika token expired (setelah dicatat di log)
     */
    public function verifyDocument(
        string $token,
        ?User $verifiedBy,
        string $ipAddress,
        string $userAgent,
        string $source = \App\Models\DocumentVerificationLog::SOURCE_PUBLIC,
    ): DocumentQrVerification {
        try {
            $qr = $this->qrService->validateToken($token);

            $this->logService->logValidVerification(
                $qr,
                $verifiedBy,
                $ipAddress,
                $userAgent,
                $source,
            );

            return $qr;

        } catch (\RuntimeException $e) {
            // Coba cari QR untuk dicatat di log expired
            $qr = $this->qrService->findByToken($token);

            if ($qr && str_contains($e->getMessage(), 'kedaluwarsa')) {
                $this->logService->logExpiredVerification(
                    $qr,
                    $verifiedBy,
                    $ipAddress,
                    $userAgent,
                    $source,
                );
            }

            // Jika token tidak ditemukan, logInvalidVerification akan throw
            // sesuai keterbatasan skema (lihat DocumentVerificationLogService)

            throw $e;
        }
    }
}
