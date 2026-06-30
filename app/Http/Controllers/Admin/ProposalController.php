<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Event;
use App\Models\Rab;
use App\Models\Proposal;
use App\Models\Notification;
use App\Models\Negotiation;
use Illuminate\Http\Request;
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

        return view('admin.proposals.documents', [
            'documents' => $query->paginate(10)->withQueryString(),
            'events'    => Event::orderBy('nama_event')->get(),
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file'     => 'required|file|max:20480|mimes:svg,png,jpg,jpeg,pdf,docx,xlsx',
            'event_id' => 'nullable|exists:events,id',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $type = match (true) {
            str_contains(strtolower($file->getClientOriginalName()), 'proposal') => 'proposal',
            str_contains(strtolower($file->getClientOriginalName()), 'kontrak')  => 'kontrak',
            default => 'lainnya',
        };

        Document::create([
            'event_id'  => $request->event_id,
            'user_id'   => auth()->id(),
            'nama_file' => $file->getClientOriginalName(),
            'file_path' => $path,
            'tipe'      => $type,
        ]);

        return redirect()->route('admin.proposals.index')->with('success', 'File berhasil diunggah.');
    }

    public function destroy(Document $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        return redirect()->route('admin.proposals.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    // ─────────────── INVOICE & KWITANSI ───────────────
    public function invoices()
    {
        return view('admin.proposals.invoices', [
            'invoices' => Invoice::with('event.client')->latest()->get(),
            'events'   => Event::orderBy('nama_event')->get(),
        ]);
    }

    public function storeInvoice(Request $request)
    {
        $data = $request->validate([
            'nomor_invoice'   => 'required|string|max:100|unique:invoices,nomor_invoice',
            'event_id'        => 'required|exists:events,id',
            'total_invoice'   => 'required|numeric|min:0',
            'tanggal_invoice' => 'required|date',
            'status_invoice'  => 'required|in:draft,terkirim,lunas',
        ]);

        Invoice::create($data);

        return redirect()->route('admin.proposals.invoices')->with('success', 'Kwitansi berhasil dibuat.');
    }

    public function updateInvoice(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'nomor_invoice'   => 'required|string|max:100|unique:invoices,nomor_invoice,' . $invoice->id,
            'event_id'        => 'required|exists:events,id',
            'total_invoice'   => 'required|numeric|min:0',
            'tanggal_invoice' => 'required|date',
            'status_invoice'  => 'required|in:draft,terkirim,lunas',
        ]);

        $invoice->update($data);

        return redirect()->route('admin.proposals.invoices')->with('success', 'Kwitansi berhasil diperbarui.');
    }

    public function destroyInvoice(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.proposals.invoices')->with('success', 'Kwitansi berhasil dihapus.');
    }

    public function printInvoice(Invoice $invoice)
    {
        $invoice->load('event.client');
        $pdf = Pdf::loadView('admin.proposals.invoice_pdf', compact('invoice'));
        return $pdf->stream('kwitansi-' . $invoice->nomor_invoice . '.pdf');
    }

    public function download(Proposal $proposal)
    {
        abort_unless(Storage::disk('public')->exists($proposal->file_proposal), 404);
        return Storage::disk('public')->response($proposal->file_proposal);
    }

    // ─────────────── SURAT PENAWARAN ───────────────

    /**
     * Tampilkan preview Surat Penawaran dari suatu event/request.
     * Route: GET /admin/requests/{event}/surat-penawaran
     */
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

        return view('admin.requests.surat_penawaran', compact('event', 'nomorSurat'));
    }

    /**
     * Simpan perubahan field surat penawaran yang diedit admin.
     * Route: PATCH /admin/requests/{event}/update-surat-penawaran
     */
    public function updateSuratPenawaran(Request $request, Event $event)
    {
        $data = $request->validate([
            'nomor_surat_override' => 'required|string|max:100',
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

        $event->load(['client', 'rabs']);

        // Generate PDF Surat Penawaran
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

        // Update status event → diproses
        $event->update(['status_event' => 'diproses']);

        // Notifikasi ke client
        Notification::create([
            'user_id' => $event->client_id,
            'judul'   => 'Surat Penawaran Dikirim',
            'pesan'   => 'Surat penawaran untuk event "' . $event->nama_event . '" telah dikirim. Silakan tinjau dan berikan respon Anda.',
            'tipe'    => 'info',
        ]);

        return redirect()
            ->route('admin.requests.index')
            ->with('success', 'Surat penawaran berhasil dikirim ke client.');
    }

    /**
     * Export PDF Surat Penawaran langsung (tanpa menyimpan).
     * Route: GET /admin/requests/{event}/export-pdf
     */
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

    /**
     * Revisi penawaran — buat proposal baru dengan status diajukan.
     * Route: POST /admin/requests/{event}/revisi-penawaran
     */
    public function revisiPenawaran(Event $event)
    {
        $event->load(['client', 'rabs']);

        $pdf = Pdf::loadView('admin.requests.surat_penawaran_pdf', [
            'event' => $event,
            'data'  => [
                'nomor_surat'   => $event->nomor_surat_override
                    ?? sprintf('REV-%s-%03d', now()->format('Ymd'), Proposal::where('event_id', $event->id)->count() + 1),
                'tanggal_surat' => now()->format('Y-m-d'),
            ],
        ]);

        $version  = ((int) Proposal::where('event_id', $event->id)->max('versi')) + 1;
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
            'nomor_proposal'   => sprintf('REV-%s-%03d', now()->format('Ymd'), $version),
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

        return redirect()
            ->route('admin.requests.index')
            ->with('success', 'Revisi penawaran berhasil dikirim.');
    }
}