<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Negotiation;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Proposal;
use App\Models\User;
use App\Services\TimelineAutoFill;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientService
{
    private function uid(): int
    {
        return (int) Auth::id();
    }

    private function myEvent(int $id): Event
    {
        return Event::where('client_id', $this->uid())->findOrFail($id);
    }

    private function notifData(): array
    {
        $uid = $this->uid();
        return [
            'unreadCount'   => Notification::where('user_id', $uid)->where('dibaca', false)->count(),
            'notifications' => Notification::where('user_id', $uid)->latest()->take(5)->get(),
        ];
    }

    private function mergeNotif(array $data): array
    {
        return array_merge($data, $this->notifData());
    }

    private function clientEventIds(): Collection
    {
        return Event::where('client_id', $this->uid())->pluck('id');
    }

    // ========== DASHBOARD ==========

    public function getDashboardData(): array
    {
        $uid = $this->uid();

        $recentEvents = Event::where('client_id', $uid)
            ->with(['timelines', 'latestProposal'])
            ->latest()
            ->take(5)
            ->get();

        $eventBerjalan = Event::where('client_id', $uid)
            ->where('status_event', 'berjalan')
            ->count();

        $eventMenunggu = Event::where('client_id', $uid)
            ->where('status_event', 'menunggu')
            ->count();

        $eventIds = Event::where('client_id', $uid)->pluck('id');

        $totalDibayar = Payment::whereHas('invoice', function ($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds);
            })
            ->where('status_pembayaran', 'diverifikasi')
            ->sum('nominal');

        return $this->mergeNotif([
            'recentEvents'  => $recentEvents,
            'eventBerjalan' => $eventBerjalan,
            'eventMenunggu' => $eventMenunggu,
            'totalDibayar'  => $totalDibayar,
        ]);
    }

    // ========== EVENTS ==========

    public function getEventsData(): array
    {
        $events = Event::where('client_id', $this->uid())
            ->with('latestProposal')
            ->latest()
            ->paginate(10);

        return $this->mergeNotif(['events' => $events]);
    }

    public function getEventCreateData(): array
    {
        return $this->notifData();
    }

    public function createEvent(array $data): Event
    {
        $event = Event::create(array_merge($data, [
            'client_id'    => $this->uid(),
            'status_event' => 'menunggu',
        ]));

        User::where('role', 'admin')->each(function (User $admin) use ($event) {
            Notification::create([
                'user_id' => $admin->id,
                'judul'   => 'Request Event Baru',
                'pesan'   => 'Client ' . Auth::user()->name . ' mengajukan event baru: ' . $event->nama_event . '.',
                'tipe'    => 'info',
                'dibaca'  => false,
            ]);
        });

        return $event;
    }

    // ========== TIMELINE ==========

    public function getTimelineData(?int $eventId): array
    {
        $events = Event::where('client_id', $this->uid())->orderBy('nama_event')->get();
        $selectedEvent = null;
        $timelines = collect();
        $progress = 0;
        $doneTask = 0;
        $totalTask = 0;

        if ($eventId) {
            $selectedEvent = $this->myEvent($eventId);
        } elseif ($events->isNotEmpty()) {
            $selectedEvent = $events->first();
        }

        if ($selectedEvent) {
            $timelines = $selectedEvent->timelines()->orderBy('tanggal_kegiatan')->get();
            $totalTask = $timelines->count();
            $doneTask = $timelines->where('status_kegiatan', 'selesai')->count();
            if ($totalTask > 0) {
                $progress = (int) round(($doneTask / $totalTask) * 100);
            }
        }

        return $this->mergeNotif(compact(
            'events', 'selectedEvent', 'timelines', 'progress', 'doneTask', 'totalTask'
        ));
    }

    // ========== INVOICES & PAYMENTS ==========

    public function getInvoicesData(): array
    {
        $eIds = $this->clientEventIds();

        $invoices = Invoice::whereIn('event_id', $eIds)
            ->with(['event', 'payments'])
            ->latest()
            ->get();

        $payments = Payment::whereHas('invoice', function ($q) use ($eIds) {
                $q->whereIn('event_id', $eIds);
            })
            ->with(['invoice.event'])
            ->latest()
            ->get();

        $totalInvoice = $invoices->sum('total_invoice');
        $totalDibayar = $payments->where('status_pembayaran', 'diverifikasi')->sum('nominal');
        $sisaTagihan = max(0, $totalInvoice - $totalDibayar);

        return $this->mergeNotif([
            'invoices'     => $invoices,
            'payments'     => $payments,
            'totalInvoice' => $totalInvoice,
            'totalDibayar' => $totalDibayar,
            'sisaTagihan'  => $sisaTagihan,
        ]);
    }

    public function processPayment(int $invoiceId, array $data): void
    {
        $eventIds = $this->clientEventIds();
        $invoice = Invoice::whereIn('event_id', $eventIds)->findOrFail($invoiceId);

        $path = request()->file('bukti_pembayaran')->store('payments', 'public');

        Payment::create([
            'invoice_id'       => $invoice->id,
            'nominal'          => $data['nominal'],
            'tanggal_pembayaran' => now()->toDateString(),
            'status_pembayaran' => 'menunggu',
            'bukti_pembayaran'  => $path,
            'jenis_pembayaran'  => $data['jenis_pembayaran'],
        ]);

        $invoice->update(['status_invoice' => 'menunggu_verifikasi']);

        User::where('role', 'admin')->each(function (User $admin) use ($invoice) {
            Notification::create([
                'user_id' => $admin->id,
                'judul'   => 'Pembayaran Menunggu Verifikasi',
                'pesan'   => 'Client ' . Auth::user()->name . ' mengunggah bukti pembayaran untuk invoice ' . $invoice->nomor_invoice . '.',
                'tipe'    => 'pembayaran',
                'dibaca'  => false,
            ]);
        });
    }

    // ========== PROPOSALS ==========

    public function getProposalsData(?string $tab, ?string $search, ?int $filterEventId): array
    {
        $uid = $this->uid();
        $eIds = Event::where('client_id', $uid)->pluck('id');

        $tabMap = [
            'penawaran' => 'penawaran',
            'proposal'  => 'proposal',
            'rab'       => 'rab',
            'kontrak'   => 'kontrak',
            'laporan'   => 'laporan',
        ];

        if (!array_key_exists($tab, $tabMap)) {
            $tab = 'penawaran';
        }

        $latestProposals = collect();
        if ($tab === 'penawaran') {
            $latestProposals = Proposal::whereIn('event_id', $eIds)
                ->with('event')
                ->whereIn('id', function ($sub) use ($eIds) {
                    $sub->selectRaw('MAX(id)')
                        ->from('proposals')
                        ->whereIn('event_id', $eIds)
                        ->groupBy('event_id');
                })
                ->orderByDesc('created_at')
                ->get();
        }

        $documents = collect();
        if ($tab !== 'penawaran') {
            $query = Document::with(['event', 'user'])
                ->whereIn('event_id', $eIds)
                ->where('tipe', $tabMap[$tab])
                ->latest();

            if ($search) {
                $query->where('nama_file', 'like', '%' . $search . '%');
            }
            if ($filterEventId) {
                $query->where('event_id', $filterEventId);
            }

            $documents = $query->paginate(9)->withQueryString();
        }

        $events = Event::where('client_id', $uid)->orderBy('nama_event')->get();

        return $this->mergeNotif([
            'activeTab'       => $tab,
            'latestProposals' => $latestProposals,
            'documents'       => $documents,
            'events'          => $events,
        ]);
    }

    public function getProposalShowData(int $id): array
    {
        $eIds = $this->clientEventIds();

        $proposal = Proposal::whereIn('event_id', $eIds)->findOrFail($id);

        $latestProposal = Proposal::where('event_id', $proposal->event_id)
            ->where('is_active', true)
            ->first();

        if ($latestProposal && $latestProposal->id !== $proposal->id) {
            return $this->mergeNotif([
                '_redirect' => route('client.proposals.show', $latestProposal->id),
                '_message'  => 'Menampilkan versi penawaran terbaru (v' . $latestProposal->versi . ').',
            ]);
        }

        $event = Event::with(['client', 'rabs', 'contract', 'proposals'])
            ->findOrFail($proposal->event_id);

        $negotiations = Negotiation::where('event_id', $proposal->event_id)
            ->where('user_id', $this->uid())
            ->with('user')
            ->latest()
            ->get();

        return $this->mergeNotif(compact('proposal', 'event', 'negotiations'));
    }

    public function getNegosiasiFormData(int $id): array
    {
        $eIds = $this->clientEventIds();

        $proposal = Proposal::whereIn('event_id', $eIds)
            ->with(['event'])
            ->findOrFail($id);

        return $this->mergeNotif(compact('proposal'));
    }

    public function terimaProposal(int $id): void
    {
        $eIds = $this->clientEventIds();
        $proposal = Proposal::whereIn('event_id', $eIds)->findOrFail($id);

        $proposal->update(['status' => 'diterima']);

        $event = $proposal->event;
        TimelineAutoFill::proposalDiterima($event);

        User::where('role', 'admin')->each(function (User $admin) use ($event) {
            Notification::create([
                'user_id' => $admin->id,
                'judul'   => 'Penawaran Diterima',
                'pesan'   => 'Client ' . Auth::user()->name . ' menerima penawaran untuk event ' . $event->nama_event . '. Timeline otomatis telah diisi.',
                'tipe'    => 'sukses',
                'dibaca'  => false,
            ]);
        });
    }

    public function submitNegosiasi(int $id, array $data): void
    {
        $eIds = $this->clientEventIds();
        $proposal = Proposal::whereIn('event_id', $eIds)->findOrFail($id);

        $budgetNumeric = null;
        if (!empty($data['budget_diinginkan'])) {
            $budgetNumeric = (float) preg_replace('/[^0-9]/', '', $data['budget_diinginkan']);
        }

        Negotiation::create([
            'event_id'          => $proposal->event_id,
            'user_id'           => $this->uid(),
            'pesan'             => $data['pesan'],
            'budget_diinginkan' => $budgetNumeric,
            'catatan_tambahan'  => $data['catatan_tambahan'] ?? null,
        ]);

        $proposal->update(['status' => 'negosiasi']);
        $proposal->event->update(['status_event' => 'menunggu']);

        User::where('role', 'admin')->each(function (User $admin) use ($proposal) {
            Notification::create([
                'user_id' => $admin->id,
                'judul'   => 'Negosiasi Diajukan',
                'pesan'   => 'Client ' . Auth::user()->name . ' mengajukan negosiasi untuk event ' . $proposal->event->nama_event . '.',
                'tipe'    => 'info',
                'dibaca'  => false,
            ]);
        });
    }

    public function terimaSetelahNegosiasi(int $id): void
    {
        $eIds = $this->clientEventIds();
        $proposal = Proposal::whereIn('event_id', $eIds)->findOrFail($id);

        $proposal->update(['status' => 'diterima']);

        $event = $proposal->event;
        $negotiation = Negotiation::where('event_id', $event->id)->latest()->first();
        TimelineAutoFill::negosiasiSelesai($event, $negotiation);

        User::where('role', 'admin')->each(function (User $admin) use ($event) {
            Notification::create([
                'user_id' => $admin->id,
                'judul'   => 'Negosiasi Selesai Penawaran Diterima',
                'pesan'   => 'Client ' . Auth::user()->name . ' menerima penawaran revisi untuk event ' . $event->nama_event . '. Timeline otomatis telah diisi.',
                'tipe'    => 'sukses',
                'dibaca'  => false,
            ]);
        });
    }

    // ========== DOCUMENTS ==========

    public function verifyDocumentAccess(Document $document): void
    {
        $eIds = $this->clientEventIds();
        abort_unless($eIds->contains($document->event_id), 403);
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);
    }

    // ========== SETTINGS ==========

    public function getSettingsData(): array
    {
        return $this->mergeNotif(['user' => Auth::user()]);
    }

    public function updateProfile(array $data): void
    {
        $user = Auth::user();
        $user->update($data);
    }

    public function updatePassword(string $password): void
    {
        Auth::user()->update(['password' => bcrypt($password)]);
    }

    // ========== NOTIFICATIONS ==========

    public function getNotificationsData(): array
    {
        $uid = $this->uid();
        $notifications = Notification::where('user_id', $uid)->latest()->paginate(15);
        Notification::where('user_id', $uid)->where('dibaca', false)->update(['dibaca' => true]);

        return $this->mergeNotif(['notifications' => $notifications]);
    }

    public function markAllNotificationsRead(): void
    {
        Notification::where('user_id', $this->uid())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);
    }
}
