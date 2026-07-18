<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\BayarRequest;
use App\Http\Requests\Client\StoreEventRequest;
use App\Http\Requests\Client\SubmitNegosiasiRequest;
use App\Models\Document;
use App\Models\Proposal;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function __construct(
        private ClientService $clientService,
    ) {}

    // ========== DASHBOARD ==========

    public function dashboard()
    {
        return view('client.dashboard', $this->clientService->getDashboardData());
    }

    // ========== EVENTS ==========

    public function events()
    {
        return view('client.events', $this->clientService->getEventsData());
    }

    public function eventCreate()
    {
        return view('client.event-create', $this->clientService->getEventCreateData());
    }

    public function eventStore(StoreEventRequest $request)
    {
        $this->clientService->createEvent($request->validated());

        return redirect()->route('client.dashboard')
            ->with('success', 'Request event berhasil dikirim! Kami akan segera menghubungi Anda.');
    }

    // ========== TIMELINE ==========

    public function timeline(?int $eventId = null)
    {
        return view('client.timeline', $this->clientService->getTimelineData($eventId));
    }

    // ========== INVOICES & PAYMENTS ==========

    public function invoices()
    {
        return view('client.invoices', $this->clientService->getInvoicesData());
    }

    public function bayar(BayarRequest $request, int $id)
    {
        $invoice = \App\Models\Invoice::whereIn('event_id',
            \App\Models\Event::where('client_id', Auth::id())->pluck('id')
        )->findOrFail($id);

        if ($invoice->status_invoice === 'lunas') {
            return back()->with('error', 'Invoice ini sudah lunas.');
        }

        if ($invoice->status_invoice === 'menunggu_verifikasi') {
            return back()->with('error', 'Bukti pembayaran sebelumnya masih menunggu verifikasi admin.');
        }

        $this->clientService->processPayment($id, $request->validated());

        return back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }

    // ========== PROPOSALS ==========

    public function proposals(Request $request, string $tab = 'penawaran')
    {
        return view('client.proposals', $this->clientService->getProposalsData(
            $tab,
            $request->search,
            $request->event_id
        ));
    }

    public function proposalShow(int $id)
    {
        $data = $this->clientService->getProposalShowData($id);

        if (isset($data['_redirect'])) {
            return redirect()->to($data['_redirect'])->with('success', $data['_message']);
        }

        return view('client.proposal-show', $data);
    }

    public function negosiasiForm(int $id)
    {
        $data = $this->clientService->getNegosiasiFormData($id);

        if (!in_array($data['proposal']->status, ['menunggu_konfirmasi', 'direvisi'])) {
            return redirect()
                ->route('client.proposals.show', $data['proposal']->id)
                ->with('error', 'Penawaran ini tidak lagi dapat dinegosiasikan.');
        }

        return view('client.negosiasi-form', $data);
    }

    public function terimaProposal(Request $request, int $id)
    {
        $this->clientService->terimaProposal($id);

        return redirect()
            ->route('client.proposals.show', $id)
            ->with('success', 'Penawaran berhasil diterima! Timeline event telah disiapkan secara otomatis.');
    }

    public function submitNegosiasi(SubmitNegosiasiRequest $request, int $id)
    {
        $this->clientService->submitNegosiasi($id, $request->validated());

        return redirect()
            ->route('client.proposals.show', $id)
            ->with('success', 'Negosiasi berhasil diajukan. Admin akan mereview dan merespon segera.');
    }

    public function terimaSetelahNegosiasi(Request $request, int $id)
    {
        $this->clientService->terimaSetelahNegosiasi($id);

        return redirect()
            ->route('client.proposals.show', $id)
            ->with('success', 'Penawaran diterima! Timeline event telah disiapkan secara otomatis.');
    }

    // ========== DOCUMENTS ==========

    public function documentPreview(Document $document)
    {
        $this->clientService->verifyDocumentAccess($document);

        return Storage::disk('public')->response($document->file_path, $document->nama_file, [
            'Content-Disposition' => 'inline; filename=' . $document->nama_file,
        ]);
    }

    public function documentDownload(Document $document)
    {
        $this->clientService->verifyDocumentAccess($document);

        return Storage::disk('public')->download($document->file_path, $document->nama_file);
    }

    // ========== SETTINGS ==========

    public function settings()
    {
        return view('client.settings', $this->clientService->getSettingsData());
    }

    public function settingsProfile(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);

        $this->clientService->updateProfile($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function settingsPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $this->clientService->updatePassword($request->password);

        return back()->with('success', 'Password berhasil diubah.');
    }

    // ========== NOTIFICATIONS ==========

    public function notifications()
    {
        return view('client.notification', $this->clientService->getNotificationsData());
    }

    public function notifRead()
    {
        $this->clientService->markAllNotificationsRead();

        return back();
    }
}
