<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Event;
use App\Models\Rab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $ext = strtolower($file->getClientOriginalExtension());
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

    // ─────────────── PROPOSAL BUILDER ───────────────
    public function builder()
    {
        return view('admin.proposals.builder', [
            'events' => Event::orderBy('nama_event')->get(),
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'event_id'   => 'required|exists:events,id',
            'sections'   => 'required|array|min:1',
        ]);

        $event = Event::with(['client', 'rabs'])->findOrFail($request->event_id);
        $sections = $request->sections;
        $rabItems = in_array('rab', $sections) ? Rab::where('event_id', $event->id)->get() : collect();

        $pdf = Pdf::loadView('admin.proposals.proposal_pdf', compact('event', 'sections', 'rabItems'));
        return $pdf->stream('proposal-' . str_replace(' ', '-', $event->nama_event) . '.pdf');
    }
}
