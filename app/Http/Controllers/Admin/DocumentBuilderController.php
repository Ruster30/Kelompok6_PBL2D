<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\DocumentBuilderService;
use Illuminate\Http\Request;

class DocumentBuilderController extends Controller
{
    public function __construct(
        private readonly DocumentBuilderService $service
    ) {}

    /**
     * Tampilkan halaman Document Builder.
     * GET /admin/document-builder
     */
    public function index(Request $request)
    {
        return view('admin.document_builder.index', [
            'events'           => Event::orderBy('nama_event')->get(),
            'selectedEventId'  => $request->integer('event_id'),
            'selectedJenis'    => $request->get('jenis_dokumen', ''),
        ]);
    }

    /**
     * Preview PDF (stream inline ke browser).
     * POST /admin/document-builder/preview
     */
    public function preview(Request $request)
    {
        $validated = $this->validateRequest($request);

        $event     = Event::findOrFail($validated['event_id']);
        $generated = $this->service->generate($event, $validated['jenis_dokumen']);

        return response($generated['pdf']->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $generated['filename'] . '"',
        ]);
    }

    /**
     * Download PDF.
     * POST /admin/document-builder/download
     */
    public function download(Request $request)
    {
        $validated = $this->validateRequest($request);

        $event     = Event::findOrFail($validated['event_id']);
        $generated = $this->service->generate($event, $validated['jenis_dokumen']);

        return $generated['pdf']->download($generated['filename']);
    }

    /**
     * Print PDF (stream dengan header print).
     * POST /admin/document-builder/print
     */
    public function print(Request $request)
    {
        // Sama seperti preview — browser akan handle print dialog via JS
        return $this->preview($request);
    }

    /**
     * Kirim dokumen ke client (simpan file, catat DB, notifikasi, email).
     * POST /admin/document-builder/send
     */
    public function sendToClient(Request $request)
    {
        $validated = $this->validateRequest($request);

        $event    = Event::findOrFail($validated['event_id']);
        $document = $this->service->sendToClient($event, $validated['jenis_dokumen']);

        return redirect()
            ->route('admin.document_builder.index')
            ->with('success', 'Dokumen berhasil dikirim ke client dan disimpan.');
    }

    // ─── Private Helpers ────────────────────────────────────────────────────

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'event_id'      => 'required|exists:events,id',
            'jenis_dokumen' => 'required|in:proposal,surat_kontrak,invoice,rab',
        ]);
    }
}
