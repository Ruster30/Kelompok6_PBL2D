<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\DocumentSource;
use App\Models\Document;
use App\Models\Event;
use App\Services\DocumentBuilderService;
use App\Services\PaymentSchemeService;
use Illuminate\Http\Request;
use App\Enums\DocumentStatus;
use App\Models\User;
use App\Services\DocumentApprovalService;
use Illuminate\Support\Facades\Storage;

class DocumentBuilderController extends Controller
{
    public function __construct(
        private readonly DocumentBuilderService $service,
        private readonly PaymentSchemeService $paymentSchemeService,
        private readonly DocumentApprovalService $approvalService,
    ) {}

    /**
     * Tampilkan halaman Document Builder.
     */
    public function index(Request $request)
    {
        return view("admin.document_builder.index", [
            "events"           => Event::orderBy("nama_event")->get(),
            "selectedEventId"  => $request->integer("event_id"),
            "selectedJenis"    => $request->get("jenis_dokumen", ""),
            "latestDocuments"  => Document::query()
                ->where("document_source", DocumentSource::Generated)
                ->when($request->integer("event_id"), fn($q, $id) => $q->where("event_id", $id))
                ->orderBy("created_at", "desc")
                ->paginate(10),
        ]);
    }



    /**
     * Tampilkan detail dokumen (READ-ONLY).
     */

    /**
     * Generate dokumen, simpan, lalu redirect ke preview.
     */

    /**
     * Tampilkan halaman preview dokumen yang sudah disimpan.
     */
    public function previewDocument(Document $document)
    {
        $document->loadMissing(["event.client", "numbering", "qrVerification"]);

        return view("admin.document_builder.preview", compact("document"));
    }

    /**
     * Generate dokumen, simpan, lalu redirect ke preview.
     */
    public function generate(Request $request)
    {
        $data = $request->validate([
            "event_id"      => "required|exists:events,id",
            "jenis_dokumen" => "required|in:proposal,surat_kontrak,invoice,rab",
        ]);

        $event    = Event::with("client")->findOrFail($data["event_id"]);
        $document = $this->service->generateAndSave($event, $data["jenis_dokumen"]);

        return redirect()
            ->route("admin.document_builder.preview", $document->id)
            ->with("success", "Dokumen berhasil dibuat.");
    }

    /**
     * Tampilkan detail dokumen (READ-ONLY).
     */


    /**
     * Download PDF dari storage.
     */
    public function downloadDocument(Document $document)
    {
        if (!$document->file_path || !Storage::disk("public")->exists($document->file_path)) {
            abort(404, "File PDF tidak ditemukan.");
        }

        return Storage::disk("public")->download($document->file_path, $document->nama_file . ".pdf");
    }

    /**
     * Tampilkan PDF untuk print (inline).
     */
    public function printDocument(Document $document)
    {
        if (!$document->file_path || !Storage::disk("public")->exists($document->file_path)) {
            abort(404, "File PDF tidak ditemukan.");
        }

        $absolutePath = Storage::disk("public")->path($document->file_path);
        $pdfName = $document->nama_file . ".pdf";

        return response()->file($absolutePath, [
            "Content-Type"        => "application/pdf",
            "Content-Disposition" => "inline; filename=\"" . $pdfName . "\"",
        ]);
    }

    /**
     * Tampilkan detail dokumen (READ-ONLY).
     */


    /**
     * Submit dokumen untuk approval (ubah status Draft ? Pending).
     */
    public function submitApproval(Document $document)
    {
        try {
            $this->approvalService->submit($document, auth()->user());
        } catch (\App\Exceptions\DDMS\DDMSException $e) {
            return redirect()
                ->route("admin.document_builder.preview", $document->id)
                ->with("error", $e->getMessage());
        }

        return redirect()
            ->route("admin.document_builder.preview", $document->id)
            ->with("success", "Dokumen berhasil disubmit untuk approval.");
    }

    /**
     * Hapus draft dokumen (hanya jika status Draft).
     */
    public function destroyDraft(Document $document)
    {
        if ($document->status !== DocumentStatus::Draft) {
            return redirect()
                ->route("admin.document_builder.preview", $document->id)
                ->with("error", "Hanya dokumen dengan status Draft yang dapat dihapus.");
        }

        // Hapus file dari storage
        if ($document->file_path && Storage::disk("public")->exists($document->file_path)) {
            Storage::disk("public")->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route("admin.document_builder.index")
            ->with("success", "Draft dokumen berhasil dihapus.");
    }

    /**
     * Ubah nama dokumen (hanya jika status Draft).
     */
    public function renameDraft(Document $document, Request $request)
    {
        if ($document->status !== DocumentStatus::Draft) {
            return redirect()
                ->route("admin.document_builder.preview", $document->id)
                ->with("error", "Hanya dokumen dengan status Draft yang dapat diubah.");
        }

        $data = $request->validate([
            "nama_file" => "required|string|max:255",
        ]);

        $document->update(["nama_file" => $data["nama_file"]]);

        return redirect()
            ->route("admin.document_builder.preview", $document->id)
            ->with("success", "Nama dokumen berhasil diubah.");
    }

    /**
     * Tampilkan detail dokumen (READ-ONLY).
     */
    public function show(Document $document)
    {
        $document->loadMissing(["event.client", "template", "user", "updatedBy"]);

        return view("admin.document_builder.show", compact("document"));
    }

    /**
     * Preview PDF.
     */
    public function preview(Request $request)
    {
        $data = $this->validateWithScheme($request);

        $event     = Event::findOrFail($data["event_id"]);
        $generated = $this->service->generate($event, $data["jenis_dokumen"]);

        $headers = [
            "Content-Type"        => "application/pdf",
            "Content-Disposition" => "inline; filename=\"" . $generated["filename"] . "\"",
        ];

        return response($generated["pdf"]->output(), 200, $headers);
    }

    /**
     * Download PDF.
     */
    public function download(Request $request)
    {
        $data = $this->validateWithScheme($request);

        $event     = Event::findOrFail($data["event_id"]);
        $generated = $this->service->generate($event, $data["jenis_dokumen"]);

        return $generated["pdf"]->download($generated["filename"]);
    }

    /**
     * Kirim dokumen ke client.
     */
    public function sendToClient(Request $request)
    {
        $data = $this->validateWithScheme($request);

        $event    = Event::findOrFail($data["event_id"]);
        $document = $this->service->sendToClient($event, $data["jenis_dokumen"]);

        return redirect()
            ->route("admin.document_builder.index")
            ->with("success", "Dokumen berhasil dikirim ke client dan disimpan.");
    }

    /**
     * Upload denah/layout untuk event.
     */
    public function uploadDenah(UploadDenahRequest $request)
    {
        $event = Event::findOrFail($request->event_id);

        // Hapus file lama jika ada
        if ($event->layout_denah && Storage::disk('public')->exists($event->layout_denah)) {
            Storage::disk('public')->delete($event->layout_denah);
        }

        $file = $request->file('layout_denah');
        $path = $file->storeAs(
            'layouts',
            'denah-' . $event->id . '-' . now()->format('YmdHis') . '.' . $file->extension(),
            'public'
        );

        $event->update(['layout_denah' => $path]);

        return response()->json([
            'success'   => true,
            'message'   => 'Denah/layout berhasil diupload.',
            'url'       => Storage::url($path),
            'file_path' => $path,
        ]);
    }

    /**
     * Cek status denah/layout untuk event.
     */
    public function denahStatus(int $eventId)
    {
        $event = Event::find($eventId);

        if (!$event || !$event->layout_denah) {
            return response()->json(['has_denah' => false]);
        }

        return response()->json([
            'has_denah' => true,
            'url'       => Storage::url($event->layout_denah),
            'file_path' => $event->layout_denah,
        ]);
    }

    /**
     * Hapus denah/layout dari event.
     */
    public function hapusDenah(int $eventId)
    {
        $event = Event::findOrFail($eventId);

        if ($event->layout_denah && Storage::disk('public')->exists($event->layout_denah)) {
            Storage::disk('public')->delete($event->layout_denah);
        }

        $event->update(['layout_denah' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Denah/layout berhasil dihapus.',
        ]);
    }

    private function validateWithScheme(Request $request): array
    {
        $base = $request->validate([
            "event_id"      => "required|exists:events,id",
            "jenis_dokumen" => "required|in:proposal,surat_kontrak,invoice,rab",
        ]);

        if ($base["jenis_dokumen"] === "invoice" && $request->has("jenis_pembayaran")) {
            $schemeData = $request->validate([
                "jenis_pembayaran" => "required|in:full_payment,dp_dan_pelunasan",
                "mode_dp"          => "nullable|required_if:jenis_pembayaran,dp_dan_pelunasan|in:persentase,nominal",
                "persentase_dp"    => "nullable|required_if:mode_dp,persentase|numeric|min:1|max:100",
                "nilai_dp"         => "nullable|required_if:mode_dp,nominal|numeric|min:1",
            ]);

            // Simpan skema pembayaran dan generate invoice
            $this->paymentSchemeService->saveScheme((int) $base["event_id"], $schemeData);
        }

        return $base;
    }
}
