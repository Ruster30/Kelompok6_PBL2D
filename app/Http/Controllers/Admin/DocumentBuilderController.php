<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadDenahRequest;
use App\Models\Event;
use App\Services\DocumentBuilderService;
use App\Services\PaymentSchemeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentBuilderController extends Controller
{
    public function __construct(
        private readonly DocumentBuilderService $service,
        private readonly PaymentSchemeService $paymentSchemeService,
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
        ]);
    }

    /**
     * Preview PDF.
     */
    public function preview(Request $request)
    {
        $data = $this->validateWithScheme($request);

        $event     = Event::findOrFail($data["event_id"]);
        $generated = $this->service->generate($event, $data["jenis_dokumen"]);

        return response($generated["pdf"]->output(), 200, [
            "Content-Type"        => "application/pdf",
            "Content-Disposition" => 'inline; filename="' . $generated["filename"] . '"',
        ]);
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
