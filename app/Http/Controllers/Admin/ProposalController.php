<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentSend;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Negotiation;
use App\Services\TimelineAutoFill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ProposalController extends Controller
{
    // ─────────────── DOKUMEN UMUM ───────────────

    public function index(Request $request)
    {
        $query = Document::with(['user', 'event'])->latest();

        if ($request->search) {
            $query->where('nama_file', 'like', '%' . $request->search . '%');
        }
        if ($request->type) {
            $query->where('tipe', $request->type);
        }

        $clients = User::where('role', 'client')->orderBy('name')->get();

        return view('admin.proposals.documents', [
            'documents' => $query->paginate(10)->withQueryString(),
            'events'    => Event::orderBy('nama_event')->get(),
            'clients'   => $clients,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file'     => 'required|file|max:102400|mimes:svg,png,jpg,jpeg,pdf,docx,xlsx',
            'event_id' => 'nullable|exists:events,id',
            'tipe'     => 'required|in:proposal,kontrak,invoice,rab,laporan,lainnya',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        Document::create([
            'event_id'  => $request->event_id,
            'user_id'   => auth()->id(),
            'nama_file' => $file->getClientOriginalName(),
            'file_path' => $path,
            'tipe'      => $request->tipe,
        ]);

        return redirect()->route('admin.proposals.index')
            ->with('success', 'File berhasil diunggah.');
    }

    /**
     * Preview dokumen di tab baru (stream, bukan download).
     */
    public function preview(Document $document)
    {
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->response($document->file_path, $document->nama_file, [
            'Content-Disposition' => 'inline; filename="' . $document->nama_file . '"',
        ]);
    }

    /**
     * Download dokumen langsung.
     */
    public function downloadDocument(Document $document)
    {
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->download($document->file_path, $document->nama_file);
    }

    /**
     * Kirim dokumen ke client yang dipilih.
     * - Simpan riwayat ke document_sends
     * - Kirim notifikasi dalam sistem
     * - Kirim email jika SMTP terkonfigurasi
     */
    public function sendToClient(Request $request, Document $document)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'pesan'     => 'nullable|string|max:1000',
        ]);

        $client    = User::findOrFail($request->client_id);
        $emailSent = false;

        // Simpan riwayat pengiriman
        DocumentSend::create([
            'document_id'  => $document->id,
            'sender_id'    => auth()->id(),
            'recipient_id' => $client->id,
            'pesan'        => $request->pesan,
            'email_sent'   => false,
            'sent_at'      => now(),
        ]);

        // Notifikasi dalam sistem
        Notification::create([
            'user_id' => $client->id,
            'judul'   => 'Dokumen Baru Dikirim',
            'pesan'   => 'Admin telah mengirimkan dokumen "' . $document->nama_file . '" kepada Anda.'
                . ($request->pesan ? ' Pesan: ' . $request->pesan : ''),
            'tipe'    => 'info',
        ]);

        // Kirim email jika SMTP terkonfigurasi
        if ($this->smtpConfigured()) {
            try {
                $filePath = Storage::disk('public')->path($document->file_path);
                Mail::raw(
                    "Yth. {$client->name},\n\n"
                    . "Admin telah mengirimkan dokumen \"{$document->nama_file}\" kepada Anda.\n"
                    . ($request->pesan ? "\nPesan: {$request->pesan}\n" : '')
                    . "\nSilakan login ke sistem untuk melihat detail.\n\n"
                    . "Salam,\nTim Alpha.corp",
                    function ($mail) use ($client, $document, $filePath) {
                        $mail->to($client->email)
                             ->subject('Dokumen Baru: ' . $document->nama_file)
                             ->attach($filePath, ['as' => $document->nama_file]);
                    }
                );
                $emailSent = true;

                // Update flag email_sent
                DocumentSend::where('document_id', $document->id)
                    ->where('recipient_id', $client->id)
                    ->latest()
                    ->first()
                    ?->update(['email_sent' => true]);

            } catch (\Exception $e) {
                // SMTP gagal — notifikasi sistem tetap terkirim
                \Log::warning('Kirim email dokumen gagal: ' . $e->getMessage());
            }
        }

        $msg = $emailSent
            ? "Dokumen berhasil dikirim ke {$client->name} via notifikasi dan email."
            : "Dokumen berhasil dikirim ke {$client->name} via notifikasi sistem.";

        return redirect()->route('admin.proposals.index')->with('success', $msg);
    }

    public function destroy(Document $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        return redirect()->route('admin.proposals.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    // ─────────────── SURAT PENAWARAN ───────────────

    public function suratPenawaran(Event $event)
    {
        $event->load([
            'client',
            'rabs',
            'activeProposal'
        ]);

        // Gunakan override nomor surat jika sudah diset admin, atau generate otomatis
        $nomorSurat = $event->nomor_surat_override
            ?? sprintf(
                'PEN-%s-%03d',
                now()->format('Ymd'),
                Proposal::whereDate('created_at', today())->count() + 1
            );

        // [TAMBAH] Surat terkunci jika proposal aktif sudah berstatus 'diterima'.
        // Begitu klien menerima, admin tidak bisa lagi Edit / Revisi.
        $isLocked = $event->activeProposal && $event->activeProposal->status === 'diterima';

        return view('admin.requests.surat_penawaran', compact('event', 'nomorSurat', 'isLocked'));
    }

    /**
     * Simpan perubahan field surat penawaran yang diedit admin.
     * Route: PATCH /admin/requests/{event}/update-surat-penawaran
     */
    public function updateSuratPenawaran(Request $request, Event $event)
    {

        // [TAMBAH] Guard: tolak perubahan apabila proposal aktif sudah diterima client.
        $event->load('activeProposal');
        if ($event->activeProposal && $event->activeProposal->status === 'diterima') {
            return redirect()
                ->route('admin.requests.surat-penawaran', $event->id)
                ->with('error', 'Surat penawaran telah diterima oleh client sehingga tidak dapat direvisi.');
        }

        $data = $request->validate([
            'nomor_surat_override' => 'required|string|max:100',
            'perihal'              => 'nullable|string|max:255', 
            'lokasi_event'         => 'nullable|string|max:255',
            'jenis_event'          => 'nullable|string|max:100',
            'tanggal_event'        => 'nullable|date',
            'tanggal_selesai'      => 'nullable|date|after_or_equal:tanggal_event',
            'luas_area'            => 'nullable|string|max:100',
            'rentang_anggaran'     => 'nullable|string|max:100',
            'terbilang'            => 'nullable|string|max:255',
            'detail_kebutuhan'     => 'nullable|string',
        ]);

        $event->update($data);

        $isLocked = $event->activeProposal && $event->activeProposal->status === 'diterima';

        return redirect()
            ->route('admin.requests.surat-penawaran', $event->id)
            ->with('success', 'Data surat penawaran berhasil diperbarui.');
    }

    /**
     * Generate PDF Surat Penawaran & simpan ke proposals, kirim notifikasi ke client.
     * Route: POST /admin/requests/{event}/kirim-penawaran
     */
    public function kirimPenawaran(Request $request, Event $event)
    {
        $data = $request->validate([
            'nomor_surat'   => 'required|string|max:100',
            'tanggal_surat' => 'required|date',
        ]);

        $data['perihal'] = $event->perihal ?? 'Surat Penawaran Event';

        $event->load(['client', 'rabs']);

        $pdf      = Pdf::loadView('admin.requests.surat_penawaran_pdf', compact('event', 'data'));
        $version  = ((int) Proposal::where('event_id', $event->id)->max('versi')) + 1;
        $filename = 'surat-penawaran-' . Str::slug($event->nama_event) . '-v' . $version . '.pdf';
        $path     = 'proposals/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        // Nonaktifkan proposal sebelumnya
        Proposal::where('event_id', $event->id)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
            ]);

        // Simpan sebagai Proposal
        Proposal::create([
            'event_id'         => $event->id,
            'nomor_proposal'   => $data['nomor_surat'],
            'file_proposal'    => $path,
            'versi'            => $version,
            'status'           => 'menunggu_konfirmasi',
            'is_active'        => true,
            'tanggal_proposal' => $data['tanggal_surat'],
        ]);

        $event->update(['status_event' => 'diproses']);

        Notification::create([
            'user_id' => $event->client_id,
            'judul'   => 'Surat Penawaran Dikirim',
            'pesan'   => 'Surat penawaran untuk event "' . $event->nama_event . '" telah dikirim. Silakan tinjau dan berikan respon Anda.',
            'tipe'    => 'info',
        ]);

        $isLocked = $event->activeProposal && $event->activeProposal->status === 'diterima';

        return redirect()->route('admin.requests.index')
            ->with('success', 'Surat penawaran berhasil dikirim ke client.');
    }

    public function exportPdf(Event $event)
    {
        $event->load(['client', 'rabs', 'activeProposal']);

        $data = [
            'nomor_surat'   => $event->nomor_surat_override
                ?? $event->activeProposal?->nomor_proposal
                ?? sprintf('PEN-%s-%03d', now()->format('Ymd'), Proposal::where('event_id', $event->id)->count()),
            'tanggal_surat' => $event->activeProposal?->tanggal_proposal?->format('Y-m-d')
                ?? now()->format('Y-m-d'),
        ];

        $pdf      = Pdf::loadView('admin.requests.surat_penawaran_pdf', compact('event', 'data'))
                       ->setPaper('a4', 'portrait');
        $filename = 'Surat-Penawaran-' . Str::slug($event->nama_event) . '.pdf';

        return $pdf->download($filename);
    }

    public function revisiPenawaran(Event $event)
    {
        return redirect()
        ->route('admin.requests.surat-penawaran', $event->id)
        ->with('info', 'Silakan lakukan revisi pada surat penawaran di bawah ini, lalu klik "Kirim Revisi" untuk mengirimkannya ke client.');
    }

    public function kirimRevisiPenawaran(Event $event)
{
    // Guard: tidak bisa revisi jika proposal aktif sudah diterima
    $event->load(['client', 'rabs', 'activeProposal']);
    if ($event->activeProposal && $event->activeProposal->status === 'diterima') {
        return redirect()
            ->route('admin.requests.surat-penawaran', $event->id)
            ->with('error', 'Surat penawaran telah diterima oleh client sehingga tidak dapat direvisi.');
    }
 
    $version  = ((int) Proposal::where('event_id', $event->id)->max('versi')) + 1;
    $nomorRev = $event->nomor_surat_override
        ?? sprintf('REV-%s-%03d', now()->format('Ymd'), $version);
 
    $data = [
        'nomor_surat'   => $nomorRev,
        'tanggal_surat' => now()->format('Y-m-d'),
        'perihal'       => $event->perihal ?? 'Surat Penawaran Event',
    ];
 
    $pdf      = Pdf::loadView('admin.requests.surat_penawaran_pdf', compact('event', 'data'));
    $filename = 'revisi-penawaran-' . Str::slug($event->nama_event) . '-v' . $version . '.pdf';
    $path     = 'proposals/' . $filename;
    Storage::disk('public')->put($path, $pdf->output());
 
    Proposal::where('event_id', $event->id)
        ->where('is_active', true)
        ->update([
            'is_active' => false,
        ]);
 
    Proposal::create([
        'event_id'         => $event->id,
        'nomor_proposal'   => $nomorRev,
        'file_proposal'    => $path,
        'versi'            => $version,
        'status'           => 'direvisi',
        'is_active'        => true,
        'tanggal_proposal' => now()->toDateString(),
    ]);
 
    Notification::create([
        'user_id' => $event->client_id,
        'judul'   => 'Revisi Penawaran Dikirim',
        'pesan'   => 'Revisi surat penawaran untuk event "' . $event->nama_event . '" telah dikirim.',
        'tipe'    => 'info',
    ]);

    $isLocked = $event->activeProposal && $event->activeProposal->status === 'diterima';
 
    return redirect()
        ->route('admin.requests.surat-penawaran', $event->id)
        ->with('success', 'Revisi penawaran berhasil dikirim ke client.');
}
 




    // ─────────────── HELPER ───────────────

    public function setujuiNegosiasi(Event $event)
    {
        $proposal = $event->activeProposal ?? $event->latestProposal;

        if ($proposal) {
            $proposal->update(['status' => 'diterima']);
        }

        $event->update(['status_event' => 'diproses']);

        TimelineAutoFill::negosiasiSelesai(
            $event,
            Negotiation::where('event_id', $event->id)->latest()->first()
        );

        Notification::create([
            'user_id' => $event->client_id,
            'judul'   => 'Negosiasi Disetujui',
            'pesan'   => 'Negosiasi untuk event "' . $event->nama_event . '" telah disetujui. Timeline event sudah disiapkan.',
            'tipe'    => 'sukses',
        ]);

        return redirect()->route('admin.requests.index')
            ->with('success', 'Negosiasi disetujui dan timeline event telah disiapkan.');
    }

    public function tolakNegosiasi(Event $event)
    {
        $proposal = $event->activeProposal ?? $event->latestProposal;

        if ($proposal) {
            $proposal->update(['status' => 'ditolak']);
        }

        $event->update(['status_event' => 'menunggu']);

        Notification::create([
            'user_id' => $event->client_id,
            'judul'   => 'Negosiasi Ditolak',
            'pesan'   => 'Negosiasi untuk event "' . $event->nama_event . '" belum dapat disetujui.',
            'tipe'    => 'peringatan',
        ]);

        return redirect()->route('admin.requests.index')
            ->with('success', 'Negosiasi berhasil ditolak.');
    }

    /**
     * Cek apakah SMTP sudah dikonfigurasi (bukan driver log/array).
     */
    private function smtpConfigured(): bool
    {
        $mailer = config('mail.default');
        return !in_array($mailer, ['log', 'array', null]);
    }
}
