<?php

namespace App\Services;

use App\Enums\DocumentSource;
use App\Interfaces\DocumentRepositoryInterface;
use App\Interfaces\ProposalRepositoryInterface;
use App\Models\Document;
use App\Models\DocumentSend;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\User;
use App\Services\DdmsSettingService;
use App\Services\TimelineAutoFill;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProposalService
{
    public function __construct(
        private DocumentRepositoryInterface $documentRepository,
        private ProposalRepositoryInterface $proposalRepository,
        private DdmsSettingService $ddmsSettingService,
    ) {}

    public function getDocumentIndexData(?string $search, ?string $type): array
    {
        $documents = $this->documentRepository->paginateWithFilters($search, $type);
        $events = Event::orderBy('nama_event')->get();
        $clients = User::where('role', 'client')->orderBy('name')->get();
        return compact('documents', 'events', 'clients');
    }

    public function uploadDocument(array $data): Document
    {
        return $this->documentRepository->create($data);
    }

    public function sendDocumentToClient(Document $document, int $clientId, ?string $pesan): string
    {
        $client = User::findOrFail($clientId);
        $emailSent = false;

        DocumentSend::create(['document_id' => $document->id, 'sender_id' => auth()->id(), 'recipient_id' => $client->id, 'pesan' => $pesan, 'email_sent' => false, 'sent_at' => now()]);
        Notification::create(['user_id' => $client->id, 'judul' => 'Dokumen Baru Dikirim', 'pesan' => 'Admin telah mengirimkan dokumen ' . $document->nama_file . ' kepada Anda.' . ($pesan ? ' Pesan: ' . $pesan : ''), 'tipe' => 'info']);

        if ($this->smtpConfigured()) {
            try {
                $fp = Storage::disk('public')->path($document->file_path);
                $eol = PHP_EOL;
                Mail::raw('Yth. ' . $client->name . ',' . $eol . $eol . 'Admin telah mengirimkan dokumen ' . $document->nama_file . ' kepada Anda.' . $eol . ($pesan ? $eol . 'Pesan: ' . $pesan . $eol : '') . $eol . 'Silakan login ke sistem untuk melihat detail.' . $eol . $eol . 'Salam,' . $eol . 'Tim Alpha.corp', function ($m) use ($client, $document, $fp) { $m->to($client->email)->subject('Dokumen Baru: ' . $document->nama_file)->attach($fp, ['as' => $document->nama_file]); });
                $emailSent = true;
                DocumentSend::where('document_id', $document->id)->where('recipient_id', $client->id)->latest()->first()?->update(['email_sent' => true]);
            } catch (\Exception $e) {
                \Log::warning('Kirim email dokumen gagal: ' . $e->getMessage());
            }
        }

        return $emailSent ? 'Dokumen berhasil dikirim ke ' . $client->name . ' via notifikasi dan email.' : 'Dokumen berhasil dikirim ke ' . $client->name . ' via notifikasi sistem.';
    }

    public function deleteDocument(Document $document): void
    {
        // Dokumen Published tidak boleh dihapus permanen.
        // Melindungi QR verification dan audit trail verifikasi
        // (FK cascade: documents -> document_qr_verifications -> document_verification_logs).
        if ($document->isPublished()) {
            throw new \App\Exceptions\DDMS\DDMSException(
                'Dokumen yang sudah dipublish tidak dapat dihapus permanen karena menyimpan riwayat verifikasi.'
            );
        }

        if ($document->file_path) { Storage::disk('public')->delete($document->file_path); }
        $this->documentRepository->delete($document);
    }

    public function getSuratPenawaranData(Event $event): array
    {
        $event->load(['client', 'rabs', 'activeProposal']);
        $nomorSurat = $event->nomor_surat_override ?? sprintf('PEN-%s-%03d', now()->format('Ymd'), $this->proposalRepository->getTodayCount() + 1);
        $isLocked = $event->activeProposal && $event->activeProposal->status === 'diterima';
        return compact('event', 'nomorSurat', 'isLocked');
    }

    public function checkProposalLocked(Event $event): bool
    {
        $event->load('activeProposal');
        return $event->activeProposal && $event->activeProposal->status === 'diterima';
    }

    public function updateSuratPenawaran(Event $event, array $data): void { $event->update($data); }

    /**
     * Kirim Penawaran (tombol "Kirim ke Client") — Phase 11I.10F.
     *
     * Workflow DDMS (effective uses_ddms=true):
     *   Proposal + Document DRAFT sudah dibuat lebih dulu oleh masukKeDdms().
     *   Aksi ini HANYA mengirim notifikasi ke Client untuk Proposal yang SUDAH
     *   ada (dan Document-nya sudah Director-approved/published). Tidak membuat
     *   versi Proposal baru dan tidak membuat Document baru.
     *
     * Workflow NON-DDMS (uses_ddms=false):
     *   Membuat Proposal (v1 / revisi) dan langsung mengirim notifikasi Client.
     */
    public function kirimPenawaran(Event $event, array $data): void
    {
        $this->sendProposalToClient($event, $data);
    }

     public function exportPdfData(Event $event): array
    {
        $event->load(['client', 'rabs', 'activeProposal']);
 
        // Nomor resmi:
        //   NON-DDMS -> Proposal.nomor_proposal (input Admin)
        //   DDMS      -> DocumentNumbering.document_number (lewat Proposal.document_id
        //               -> Document.id -> DocumentNumbering.document_id)
        // Jangan tampilkan placeholder Proposal.nomor_proposal jika DocumentNumbering
        // sudah tersedia untuk proposal DDMS.
        $ddmsNumber = $event->activeProposal?->document?->numbering?->document_number;

        // Sumber nomor surat final (aturan Phase 11I.10K):
        //   NON-DDMS → Proposal.nomor_proposal (input Admin)
        //   DDMS      → DocumentNumbering.document_number (via Proposal.document_id → Document.id → Document.numbering → DocumentNumbering)
        $ddmsNumber = $event->activeProposal?->document?->numbering?->document_number;

        $data = [
            'nomor_surat'  => $event->nomor_surat_override
                ?? $ddmsNumber
                ?? $event->activeProposal?->nomor_proposal
                ?? sprintf('PEN-%s-%03d', now()->format('Ymd'), $this->proposalRepository->getEventCount($event->id)),
            'tanggal_surat' => $event->activeProposal?->tanggal_proposal?->format('Y-m-d')
                ?? now()->format('Y-m-d'),
            'perihal'      => $event->perihal ?? 'Surat Penawaran Pameran Otomotif',
            'document'     => $event->activeProposal?->document, // untuk template QR/numbering
        ];
 
        return compact('event', 'data');
    }

    /**
     * Kirim Revisi Penawaran (tombol "Kirim ke Client" pada konteks revisi)
     * — Phase 11I.10F.
     *
     * Sama dengan kirimPenawaran(): untuk DDMS hanya mengirim notifikasi ke
     * Client pada Proposal yang SUDAH ada (Document approved/published); untuk
     * NON-DDMS membuat revisi (v2, ...) dan langsung mengirim notifikasi.
     */
    public function kirimRevisiPenawaran(Event $event, array $data = []): void
    {
        $this->sendProposalToClient($event, $data);
    }

    /**
     * Aksi "Kirim ke Client" yang dipakai bersama oleh kirimPenawaran dan
     * kirimRevisiPenawaran.
     *
     * Gate server-side (assertDdmsCanSend) menolak pengiriman bila Proposal
     * menggunakan DDMS namun Document-nya BELUM Director-approved/published
     * (cegah bypass tombol disabled via DevTools / direct POST).
     */
    private function sendProposalToClient(Event $event, array $data): void
    {
        $this->assertDdmsCanSend($event);

        $event->load(['client', 'rabs', 'latestProposal']);
        $latest = $event->latestProposal;

        // DDMS: Proposal + Document sudah dibuat oleh masukKeDdms().
        // Kirim hanya memberi notifikasi ke Client pada Proposal yang ada;
        // TIDAK membuat versi Proposal baru maupun Document baru.
        if ($latest && $latest->document && $latest->document->uses_ddms) {
            $this->notifyClientProposalSent($event);
            return;
        }

        // NON-DDMS: buat Proposal (v1 bila belum ada, revisi bila sudah ada)
        // dan langsung kirim notifikasi Client.
        $this->createNonDdmsProposal($event, $data, (bool) $latest);
    }

    /**
     * Buat Proposal NON-DDMS (tanpa Document DDMS) dan kirim notifikasi Client.
     */
    private function createNonDdmsProposal(Event $event, array $data, bool $isRevision): void
    {
        $data['perihal'] = $event->perihal ?? 'Surat Penawaran Event';
        $event->load(['client', 'rabs']);

        $pdf = Pdf::loadView('admin.requests.surat_penawaran_pdf', compact('event', 'data'));
        $version = $this->proposalRepository->getNextVersion($event->id);
        $filename = ($isRevision ? 'revisi-penawaran-' : 'surat-penawaran-')
            . Str::slug($event->nama_event) . '-v' . $version . '.pdf';
        $path = 'proposals/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        try {
            DB::transaction(function () use ($event, $data, $path, $version, $isRevision) {
                $this->proposalRepository->deactivateActive($event->id);

                if ($isRevision) {
                    $nomor = $data['nomor_surat']
                        ?? $event->nomor_surat_override
                        ?? sprintf('REV-%s-%03d', now()->format('Ymd'), $version);
                    $status = 'direvisi';
                } else {
                    $nomor = $data['nomor_surat']
                        ?? sprintf('PEN-%s-%03d', now()->format('Ymd'), $this->proposalRepository->getTodayCount() + 1);
                    $status = 'menunggu_konfirmasi';
                }

                $this->proposalRepository->create([
                    'event_id' => $event->id,
                    'nomor_proposal' => $nomor,
                    'file_proposal' => $path,
                    'versi' => $version,
                    'status' => $status,
                    'is_active' => true,
                    'tanggal_proposal' => $data['tanggal_surat'] ?? now()->format('Y-m-d'),
                ]);
            });
        } catch (\Throwable $e) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }

        $this->notifyClientProposalSent($event);
    }

    /**
     * Masuk ke DDMS (tombol "Masuk ke DDMS") — Phase 11I.10F.
     *
     * Membuat Proposal + Document DRAFT (tanpa notifikasi Client), lalu
     * mengembalikan Document agar Controller dapat me-redirect ke halaman DDMS.
     *
     * Idempotensi / duplicate protection:
     *   Jika sudah ada Proposal/Document DDMS yang masih dalam siklus belum
     *   selesai (draft/pending/rejected), jangan buat Document/Proposal kedua;
     *   kembalikan Document yang sudah ada.
     *   Hanya bila Document terakhir SUDAH approved/published (siklus selesai)
     *   maka panggilan berikutnya membuat revisi (v2, Document B, ...).
     *
     * Bila effective DDMS false (global OFF / checkbox off), fallback ke
     * workflow NON-DDMS (langsung Kirim) dan kembalikan null (tidak ada
     * Document untuk di-redirect).
     */
    public function masukKeDdms(Event $event, array $data): ?Document
    {
        $event->load(['client', 'rabs', 'latestProposal']);

        $effectiveDdms = $this->resolveEffectiveDdms($data['uses_ddms'] ?? false);

        if (! $effectiveDdms) {
            // Defensive fallback: behave like a direct NON-DDMS send.
            $this->createNonDdmsProposal($event, $data, (bool) $event->latestProposal);
            return null;
        }

        $latest = $event->latestProposal;

        // Idempotensi / penentuan revisi.
        if ($latest && $latest->document && $latest->document->uses_ddms) {
            $status = $latest->document->status->value;
            if (! in_array($status, [
                \App\Enums\DocumentStatus::Approved->value,
                \App\Enums\DocumentStatus::Published->value,
            ], true)) {
                // Masih dalam siklus DDMS yang belum selesai → jangan buat duplikat.
                return $latest->document;
            }
            // approved/published → lanjut buat revisi baru (v2, Document B, ...).
        }

        $isRevision = (bool) $latest;

        $data['perihal'] = $event->perihal ?? 'Surat Penawaran Event';
        $pdf = Pdf::loadView('admin.requests.surat_penawaran_pdf', compact('event', 'data'));
        $version = $this->proposalRepository->getNextVersion($event->id);
        $filename = 'surat-penawaran-' . Str::slug($event->nama_event) . '-v' . $version . '.pdf';
        $path = 'proposals/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        try {
            $document = DB::transaction(function () use ($event, $data, $path, $version, $isRevision) {
                $this->proposalRepository->deactivateActive($event->id);

                $nomor = $isRevision
                    ? ($data['nomor_surat'] ?? sprintf('REV-%s-%03d', now()->format('Ymd'), $version))
                    : ($data['nomor_surat'] ?? sprintf('PEN-%s-%03d', now()->format('Ymd'), $this->proposalRepository->getTodayCount() + 1));

                $proposal = $this->proposalRepository->create([
                    'event_id' => $event->id,
                    'nomor_proposal' => $nomor,
                    'file_proposal' => $path,
                    'versi' => $version,
                    'status' => 'menunggu_konfirmasi',
                    'is_active' => true,
                    'tanggal_proposal' => $data['tanggal_surat'] ?? now()->format('Y-m-d'),
                ]);

                // DDMS layer: link Document ke PDF kanonik yang SAMA.
                // Tidak membuat PDF kedua.
                $document = $this->createProposalDocument($event, $path, $version);
                $proposal->update(['document_id' => $document->id]);

                return $document;
            });
        } catch (\Throwable $e) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }

        return $document;
    }

    /**
     * Kirim notifikasi ke Client bahwa Surat Penawaran telah dikirim.
     * Dipanggil HANYA dari aksi "Kirim ke Client" (Admin), bukan dari
     * pembuatan/approval Document DDMS.
     */
    private function notifyClientProposalSent(Event $event): void
    {
        $event->update(['status_event' => 'diproses']);
        Notification::create([
            'user_id' => $event->client_id,
            'judul' => 'Surat Penawaran Dikirim',
            'pesan' => 'Surat penawaran untuk event ' . $event->nama_event . ' telah dikirim.',
            'tipe' => 'info',
        ]);
    }

    /**
     * Create a DDMS Document for a Proposal from the ALREADY-generated
     * canonical Surat Penawaran PDF. This reuses the existing Document model
     * and does NOT generate a second PDF.
     *
     * Document fields follow the actual schema:
     *   tipe            = proposal
     *   document_source = generated
     *   uses_ddms       = true
     *   status          = draft
     *   file_path       = Proposal.file_proposal (same physical PDF)
     */
    private function createProposalDocument(Event $event, string $filePath, int $version): Document
    {
        return Document::create([
            'event_id'        => $event->id,
            'user_id'         => auth()->id(),
            'nama_file'       => 'Surat Penawaran - ' . $event->nama_event . ' (v' . $version . ')',
            'file_path'       => $filePath,
            'tipe'            => Document::TIPE_PROPOSAL,
            'status'          => Document::STATUS_DRAFT,
            'document_source' => DocumentSource::Generated,
            'uses_ddms'       => true,
        ]);
    }

    /**
     * Effective DDMS decision for Proposal:
     * global master switch ddms_enabled must be ON, otherwise always false
     * even if the request asked for it.
     */
    private function resolveEffectiveDdms(mixed $requested): bool
    {
        $globalEnabled = $this->ddmsSettingService->getSettingValue('ddms_enabled', '1') === '1';

        if (! $globalEnabled) {
            return false;
        }

        return filter_var($requested, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Apakah tombol "Kirim" Surat Penawaran boleh ditampilkan/dijalankan?
     *
     * Aturan (Phase 11I.10E):
     * - Global DDMS OFF  -> selalu boleh (Proposal biasa).
     * - Belum ada Proposal -> boleh (pembuatan v1).
     * - Proposal terakhir NON-DDMS -> boleh.
     * - Proposal terakhir DDMS DAN Document belum approved/published -> TIDAK boleh.
     * - Proposal terakhir DDMS DAN Document sudah approved/published -> boleh.
     */
    public function canSendProposal(Event $event): bool
    {
        $globalEnabled = $this->ddmsSettingService->getSettingValue('ddms_enabled', '1') === '1';
        if (! $globalEnabled) {
            return true;
        }

        $proposal = $event->latestProposal;
        if (! $proposal) {
            return true;
        }

        $document = $proposal->document;
        if (! $document || ! $document->uses_ddms) {
            return true;
        }

        return in_array(
            $document->status->value,
            [
                \App\Enums\DocumentStatus::Approved->value,
                \App\Enums\DocumentStatus::Published->value,
            ],
            true
        );
    }

    /**
     * Server-side guard untuk kirimPenawaran / kirimRevisiPenawaran.
     *
     * Mencegah bypass tombol disabled via direct POST / DevTools.
     * Jika Proposal menggunakan DDMS dan Document belum disetujui Director,
     * request ditolak dengan validation error.
     */
    private function assertDdmsCanSend(Event $event): void
    {
        if (! $this->canSendProposal($event)) {
            throw ValidationException::withMessages([
                'uses_ddms' => 'Surat Penawaran dengan DDMS harus disetujui Director terlebih dahulu.',
            ]);
        }
    }

    public function setujuiNegosiasi(Event $event): void
    {
        $proposal = $event->activeProposal ?? $event->latestProposal;
        if ($proposal) { $this->proposalRepository->update($proposal, ['status' => 'diterima']); }
        $event->update(['status_event' => 'diproses']);
        TimelineAutoFill::negosiasiSelesai($event, $this->proposalRepository->getLatestNegotiation($event->id));
        Notification::create(['user_id' => $event->client_id, 'judul' => 'Negosiasi Disetujui', 'pesan' => 'Negosiasi untuk event ' . $event->nama_event . ' telah disetujui.', 'tipe' => 'sukses']);
    }

    public function tolakNegosiasi(Event $event): void
    {
        $proposal = $event->activeProposal ?? $event->latestProposal;
        if ($proposal) { $this->proposalRepository->update($proposal, ['status' => 'ditolak']); }
        $event->update(['status_event' => 'menunggu']);
        Notification::create(['user_id' => $event->client_id, 'judul' => 'Negosiasi Ditolak', 'pesan' => 'Negosiasi untuk event ' . $event->nama_event . ' belum dapat disetujui.', 'tipe' => 'peringatan']);
    }

    private function smtpConfigured(): bool
    {
        $mailer = config('mail.default');
        return !in_array($mailer, ['log', 'array', null]);
    }
}
