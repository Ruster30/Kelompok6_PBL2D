<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KirimPenawaranRequest;
use App\Http\Requests\Admin\SendClientDocumentRequest;
use App\Http\Requests\Admin\UpdateSuratPenawaranRequest;
use App\Http\Requests\Admin\UploadDocumentRequest;
use App\Models\Document;
use App\Models\Event;
use App\Services\AdminProposalService;
use App\Services\DdmsSettingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProposalController extends Controller
{
    public function __construct(
        private AdminProposalService $proposalService,
        private DdmsSettingService $ddmsSettingService,
    ) {}

    public function index(Request $request)
    {
        $data = $this->proposalService->getDocumentIndexData($request->search, $request->type);

        return view('admin.proposals.documents', $data);
    }

    public function upload(UploadDocumentRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $this->proposalService->uploadDocument([
            'event_id'  => $request->event_id,
            'user_id'   => auth()->id(),
            'nama_file' => $file->getClientOriginalName(),
            'file_path' => $path,
            'tipe'      => $request->tipe,
        ]);

        return redirect()->route('admin.documents.index')
            ->with('success', 'File berhasil diunggah.');
    }

    public function preview(Document $document)
    {
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->response($document->file_path, $document->nama_file);
    }

    public function downloadDocument(Document $document)
    {
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->download($document->file_path, $document->nama_file);
    }

    public function sendToClient(SendClientDocumentRequest $request, Document $document)
    {
        $msg = $this->proposalService->sendDocumentToClient(
            $document,
            $request->client_id,
            $request->pesan
        );

        return redirect()->route('admin.documents.index')->with('success', $msg);
    }

    public function destroy(Document $document)
    {
        try {
            $this->proposalService->deleteDocument($document);
        } catch (\App\Exceptions\DDMS\DDMSException $e) {
            return redirect()->route('admin.documents.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    public function suratPenawaran(Event $event)
    {
        $data = $this->proposalService->getSuratPenawaranData($event);

        $ddmsEnabled = $this->ddmsSettingService->getSettingValue('ddms_enabled', '1') === '1';
        $data['ddmsEnabled'] = $ddmsEnabled;
        $data['ddmsDefaultPenawaran'] = $this->ddmsSettingService->getSettingValue('ddms_default_penawaran', '0') === '1';

        $latest = $event->latestProposal;
        $document = ($latest && $latest->document && $latest->document->uses_ddms) ? $latest->document : null;
        $usesDdmsActive = (bool) $document;
        $docStatus = $usesDdmsActive ? $document->status->value : null;
        $ddmsApproved = in_array($docStatus, ['approved', 'published'], true);

        $data['latestProposal'] = $latest;
        $data['usesDdmsActive'] = $usesDdmsActive;
        $data['ddmsDocument'] = $document;
        $data['ddmsApproved'] = $ddmsApproved;
        $data['ddmsStatusLabel'] = $document ? $document->status->label() : null;
        $data['ddmsDocNumber'] = ($document && $document->numbering) ? $document->numbering->document_number : null;
        $data['canSendProposal'] = $this->proposalService->canSendProposal($event);

        return view('admin.requests.surat_penawaran', $data);
    }

    public function updateSuratPenawaran(UpdateSuratPenawaranRequest $request, Event $event)
    {
        if ($this->proposalService->checkProposalLocked($event)) {
            return redirect()
                ->route('admin.requests.surat-penawaran', $event->id)
                ->with('error', 'Surat penawaran telah diterima oleh client sehingga tidak dapat direvisi.');
        }

        $this->proposalService->updateSuratPenawaran($event, $request->validated());

        return redirect()
            ->route('admin.requests.surat-penawaran', $event->id)
            ->with('success', 'Data surat penawaran berhasil diperbarui.');
    }

    public function kirimPenawaran(KirimPenawaranRequest $request, Event $event)
    {
        if ($this->proposalService->checkProposalLocked($event)) {
            return redirect()
                ->route('admin.requests.surat-penawaran', $event->id)
                ->with('error', 'Surat penawaran telah diterima oleh client sehingga tidak dapat dikirim ulang.');
        }

        $this->proposalService->kirimPenawaran($event, $request->validated());

        return redirect()->route('admin.requests.index')
            ->with('success', 'Surat penawaran berhasil dikirim ke client.');
    }

    public function masukKeDdms(Request $request, Event $event)
    {
        if ($this->proposalService->checkProposalLocked($event)) {
            return redirect()
                ->route('admin.requests.surat-penawaran', $event->id)
                ->with('error', 'Surat penawaran telah diterima oleh client sehingga tidak dapat dikirim ulang.');
        }

        $validated = $request->validate([
            'uses_ddms'     => 'nullable|boolean',
            'nomor_surat'   => 'nullable|string|max:100',
            'tanggal_surat' => 'nullable|date',
        ]);

        $document = $this->proposalService->masukKeDdms($event, $validated);

        if (! $document) {
            // Fallback NON-DDMS (global DDMS OFF): proposal langsung dikirim.
            return redirect()->route('admin.requests.index')
                ->with('success', 'Surat penawaran berhasil dikirim ke client.');
        }

        return redirect()
            ->route('admin.document_builder.preview', $document->id)
            ->with('success', 'Proposal berhasil dibuat sebagai draft DDMS. Silakan lanjutkan proses approval.');
    }

    public function exportPdf(Event $event)
    {
        $data = $this->proposalService->exportPdfData($event);

        $pdf = Pdf::loadView('admin.requests.surat_penawaran_pdf', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'Surat-Penawaran-' . Str::slug($event->nama_event) . '.pdf';

        return $pdf->download($filename);
    }

    public function revisiPenawaran(Event $event)
    {
        if ($this->proposalService->checkProposalLocked($event)) {
            return redirect()
                ->route('admin.requests.surat-penawaran', $event->id)
                ->with('error', 'Surat penawaran telah diterima oleh client sehingga tidak dapat direvisi.');
        }

        return redirect()
            ->route('admin.requests.surat-penawaran', $event->id)
            ->with('info', 'Silakan lakukan revisi pada surat penawaran di bawah ini, lalu klik "Kirim Revisi" untuk mengirimkannya ke client.');
    }

    public function kirimRevisiPenawaran(Request $request, Event $event)
    {
        if ($this->proposalService->checkProposalLocked($event)) {
            return redirect()
                ->route('admin.requests.surat-penawaran', $event->id)
                ->with('error', 'Surat penawaran telah diterima oleh client sehingga tidak dapat direvisi.');
        }

        $this->proposalService->kirimRevisiPenawaran($event, [
            'uses_ddms' => $request->input('uses_ddms'),
        ]);

        return redirect()
            ->route('admin.requests.surat-penawaran', $event->id)
            ->with('success', 'Revisi penawaran berhasil dikirim ke client.');
    }

    public function setujuiNegosiasi(Event $event)
    {
        $this->proposalService->setujuiNegosiasi($event);

        return redirect()->route('admin.requests.index')
            ->with('success', 'Negosiasi disetujui dan timeline event telah disiapkan.');
    }

    public function tolakNegosiasi(Event $event)
    {
        $this->proposalService->tolakNegosiasi($event);

        return redirect()->route('admin.requests.index')
            ->with('success', 'Negosiasi berhasil ditolak.');
    }
}
