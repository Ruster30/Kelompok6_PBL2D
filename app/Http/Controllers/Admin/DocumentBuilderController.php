<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\DocumentBuilderService;
use App\Services\PaymentSchemeService;
use Illuminate\Http\Request;

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
