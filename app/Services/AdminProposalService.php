<?php

namespace App\Services;

use App\Interfaces\DocumentRepositoryInterface;
use App\Interfaces\ProposalRepositoryInterface;
use App\Models\Document;
use App\Models\DocumentSend;
use App\Models\Event;
use App\Models\Notification;
use App\Models\User;
use App\Services\TimelineAutoFill;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProposalService
{
    public function __construct(
        private DocumentRepositoryInterface $documentRepository,
        private ProposalRepositoryInterface $proposalRepository,
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

    public function kirimPenawaran(Event $event, array $data): void
    {
        $data['perihal'] = $event->perihal ?? 'Surat Penawaran Event';
        $event->load(['client', 'rabs']);
        $pdf = Pdf::loadView('admin.requests.surat_penawaran_pdf', compact('event', 'data'));
        $version = $this->proposalRepository->getNextVersion($event->id);
        $filename = 'surat-penawaran-' . Str::slug($event->nama_event) . '-v' . $version . '.pdf';
        $path = 'proposals/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());
        $this->proposalRepository->deactivateActive($event->id);
        $this->proposalRepository->create(['event_id' => $event->id, 'nomor_proposal' => $data['nomor_surat'], 'file_proposal' => $path, 'versi' => $version, 'status' => 'menunggu_konfirmasi', 'is_active' => true, 'tanggal_proposal' => $data['tanggal_surat']]);
        $event->update(['status_event' => 'diproses']);
        Notification::create(['user_id' => $event->client_id, 'judul' => 'Surat Penawaran Dikirim', 'pesan' => 'Surat penawaran untuk event ' . $event->nama_event . ' telah dikirim.', 'tipe' => 'info']);
    }

     public function exportPdfData(Event $event): array
    {
        $event->load(['client', 'rabs', 'activeProposal']);
 
        $data = [
            'nomor_surat'  => $event->nomor_surat_override
                ?? $event->activeProposal?->nomor_proposal
                ?? sprintf('PEN-%s-%03d', now()->format('Ymd'), $this->proposalRepository->getEventCount($event->id)),
            'tanggal_surat' => $event->activeProposal?->tanggal_proposal?->format('Y-m-d')
                ?? now()->format('Y-m-d'),
            'perihal'      => $event->perihal ?? 'Surat Penawaran Pameran Otomotif',
        ];
 
        return compact('event', 'data');
    }

    public function kirimRevisiPenawaran(Event $event): void
    {
        $event->load(['client', 'rabs', 'activeProposal']);
        $version = $this->proposalRepository->getNextVersion($event->id);
        $nomorRev = $event->nomor_surat_override ?? sprintf('REV-%s-%03d', now()->format('Ymd'), $version);
        $data = ['nomor_surat' => $nomorRev, 'tanggal_surat' => now()->format('Y-m-d'), 'perihal' => $event->perihal ?? 'Surat Penawaran Event'];
        $pdf = Pdf::loadView('admin.requests.surat_penawaran_pdf', compact('event', 'data'));
        $filename = 'revisi-penawaran-' . Str::slug($event->nama_event) . '-v' . $version . '.pdf';
        $path = 'proposals/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());
        $this->proposalRepository->deactivateActive($event->id);
        $this->proposalRepository->create(['event_id' => $event->id, 'nomor_proposal' => $nomorRev, 'file_proposal' => $path, 'versi' => $version, 'status' => 'direvisi', 'is_active' => true, 'tanggal_proposal' => now()->toDateString()]);
        Notification::create(['user_id' => $event->client_id, 'judul' => 'Revisi Penawaran Dikirim', 'pesan' => 'Revisi surat penawaran untuk event ' . $event->nama_event . ' telah dikirim.', 'tipe' => 'info']);
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
