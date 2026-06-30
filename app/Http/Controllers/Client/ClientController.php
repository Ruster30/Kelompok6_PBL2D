<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Negotiation;
use App\Services\TimelineAutoFill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class ClientController extends Controller
{
    // ══════════════════════════════════════════════════
    //  HELPER PRIVATE
    // ══════════════════════════════════════════════════

    private function myEvent(int $id): Event
    {
        return Event::where('client_id', Auth::id())->findOrFail($id);
    }

    private function notifData(): array
    {
        $uid = Auth::id();
        return [
            'unreadCount'   => Notification::where('user_id', $uid)->where('dibaca', false)->count(),
            'notifications' => Notification::where('user_id', $uid)->latest()->take(5)->get(),
        ];
    }

    // ══════════════════════════════════════════════════
    //  DASHBOARD
    // ══════════════════════════════════════════════════

    public function dashboard()
    {
        $uid = Auth::id();

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

        $totalDibayar = Invoice::whereIn('event_id', $eventIds)
            ->where('status_invoice', 'lunas')
            ->sum('total_invoice');

        return view('client.dashboard', array_merge([
            'recentEvents'  => $recentEvents,
            'eventBerjalan' => $eventBerjalan,
            'eventMenunggu' => $eventMenunggu,
            'totalDibayar'  => $totalDibayar,
        ], $this->notifData()));
    }

    // ══════════════════════════════════════════════════
    //  EVENTS
    // ══════════════════════════════════════════════════

    public function events()
    {
        $uid    = Auth::id();
        $events = Event::where('client_id', $uid)->with('latestProposal')->latest()->paginate(10);

        return view('client.events', array_merge(['events' => $events], $this->notifData()));
    }

    // ══════════════════════════════════════════════════
    //  TIMELINE
    // ══════════════════════════════════════════════════

    public function timeline(?int $eventId = null)
    {
        $uid    = Auth::id();
        $events = Event::where('client_id', $uid)->orderBy('nama_event')->get();
        $selectedEvent = null;
        $timelines     = collect();
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
            $doneTask  = $timelines->where('status_kegiatan', 'selesai')->count();
            if ($totalTask > 0) {
                $progress = (int) round(($doneTask / $totalTask) * 100);
            }
        }

        return view('client.timeline', array_merge(
            compact('events', 'selectedEvent', 'timelines', 'progress', 'doneTask', 'totalTask'),
            $this->notifData()
        ));
    }

    // ══════════════════════════════════════════════════
    //  INVOICES & PEMBAYARAN
    // ══════════════════════════════════════════════════

    public function invoices()
    {
        $uid = Auth::id();

        $eIds = Event::where('client_id', $uid)
            ->pluck('id');

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

        $totalDibayar = $payments
            ->where('status_pembayaran', 'diterima')
            ->sum('nominal');

        $sisaTagihan = $totalInvoice - $totalDibayar;

        return view('client.invoices', array_merge([
            'invoices'      => $invoices,
            'payments'      => $payments,
            'totalInvoice'  => $totalInvoice,
            'totalDibayar'  => $totalDibayar,
            'sisaTagihan'   => $sisaTagihan,
        ], $this->notifData()));
    }

    // ══════════════════════════════════════════════════
    //  PROPOSALS
    // ══════════════════════════════════════════════════

    /**
     * Daftar surat penawaran — tampilkan HANYA proposal terbaru per event.
     * Jika admin sudah merevisi, versi lama tidak ditampilkan.
     */
    public function proposals()
    {
        $uid  = Auth::id();
        $eIds = Event::where('client_id', $uid)->pluck('id');

        // Ambil proposal dengan versi tertinggi per event
        // Menggunakan latestProposal relation via subquery agar efisien
        $latestProposals = Proposal::whereIn('event_id', $eIds)
            ->with('event')
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->get();

        return view('client.proposals', array_merge(
            ['latestProposals' => $latestProposals],
            $this->notifData()
        ));
    }

    /**
     * Detail proposal — selalu ambil proposal dengan versi tertinggi untuk event tersebut.
     * Jika client membuka link proposal lama, redirect ke proposal terbaru.
     */
    /**
     * Detail proposal — selalu ambil proposal dengan versi tertinggi untuk event tersebut.
     * Jika client membuka link proposal lama, redirect ke proposal terbaru.
     */
    public function proposalShow(int $id)
    {
        $uid  = Auth::id();
        $eIds = Event::where('client_id', $uid)->pluck('id');
 
        // Cari proposal yang diminta
        $proposal = Proposal::whereIn('event_id', $eIds)
            ->findOrFail($id);
 
        // Cek apakah ini proposal terbaru untuk event ini
        $latestProposal = Proposal::where('event_id', $proposal->event_id)
            ->where('is_active', true)
            ->first();
 
        // Jika bukan proposal terbaru, redirect ke yang terbaru
        if ($latestProposal && $latestProposal->id !== $proposal->id) {
            return redirect()
                ->route('client.proposals.show', $latestProposal->id)
                ->with('success', 'Menampilkan versi penawaran terbaru (v' . $latestProposal->versi . ').');
        }
 
        // Load event dengan semua relasi yang dibutuhkan untuk render surat
        $event = Event::with(['client', 'rabs', 'contract', 'proposals',])->findOrFail($proposal->event_id);
 
        // Negosiasi yang sudah dikirim client untuk event ini
        $negotiations = Negotiation::where('event_id', $proposal->event_id)
            ->where('user_id', $uid)
            ->with('user')
            ->latest()
            ->get();
 
        return view('client.proposal-show', array_merge(
            compact('proposal', 'event', 'negotiations'),
            $this->notifData()
        ));
    }
 
    // ──────────────────────────────────────────────────
    //  Form Negosiasi (halaman terpisah)
    // ──────────────────────────────────────────────────

    /**
     * Tampilkan halaman form ajukan negosiasi.
     * Route: GET /client/proposals/{id}/negosiasi
     */
    public function negosiasiForm(int $id)
    {
        $uid  = Auth::id();
        $eIds = Event::where('client_id', $uid)->pluck('id');

        $proposal = Proposal::whereIn('event_id', $eIds)
            ->with(['event'])
            ->findOrFail($id);

        // Proposal hanya bisa diterima jika masih menunggu konfirmasi atau sudah direvisi
        if (!in_array($proposal->status, ['menunggu_konfirmasi', 'direvisi'])) {
            return redirect()
                ->route('client.proposals.show', $proposal->id)
                ->with('error', 'Penawaran ini tidak lagi dapat dinegosiasikan.');
        }

        return view('client.negosiasi-form', array_merge(
            compact('proposal'),
            $this->notifData()
        ));
    }

    // ──────────────────────────────────────────────────
    //  A) Terima Proposal LANGSUNG (tanpa negosiasi)
    // ──────────────────────────────────────────────────

    /**
     * Client menekan tombol "Terima Penawaran".
     * → proposal.status = disetujui
     * → event.status_event = diproses
     * → Timeline otomatis terisi via TimelineAutoFill::proposalDiterima()
     * → Notifikasi ke admin
     *
     * Route: POST /proposals/{proposal}/terima
     */
    public function terimaProposal(Request $request, int $id)
    {
        $uid      = Auth::id();
        $eIds     = Event::where('client_id', $uid)->pluck('id');
        $proposal = Proposal::whereIn('event_id', $eIds)->findOrFail($id);

        $proposal->update([
            'status' => 'diterima'
        ]);

        $event = $proposal->event;

        TimelineAutoFill::proposalDiterima($event);

        User::where('role', 'admin')->each(function (User $admin) use ($event) {
            Notification::create([
                'user_id' => $admin->id,
                'judul'   => 'Penawaran Diterima',
                'pesan'   => 'Client ' . Auth::user()->name . ' menerima penawaran untuk event "' . $event->nama_event . '". Timeline otomatis telah diisi.',
                'tipe'    => 'sukses',
                'dibaca'  => false,
            ]);
        });

        return redirect()
            ->route('client.proposals.show', $proposal->id)
            ->with('success', 'Penawaran berhasil diterima! Timeline event telah disiapkan secara otomatis.');
    }

    // ──────────────────────────────────────────────────
    //  B1) Submit Negosiasi — client kirim nego
    // ──────────────────────────────────────────────────

    /**
     * Client mengajukan negosiasi/keberatan terhadap penawaran.
     * → simpan ke tabel negotiations
     * → proposal.status = 'draft' (menunggu revisi admin)
     * → event.status_event = 'menunggu'
     * → Notifikasi ke admin
     *
     * Route: POST /proposals/{proposal}/negosiasi
     */
    public function submitNegosiasi(Request $request, int $id)
    {
        $uid      = Auth::id();
        $eIds     = Event::where('client_id', $uid)->pluck('id');
        $proposal = Proposal::whereIn('event_id', $eIds)->findOrFail($id);

        $data = $request->validate([
            'pesan'             => 'required|string|max:2000',
            'budget_diinginkan' => 'nullable|string|max:100',
            'catatan_tambahan'  => 'nullable|string|max:1000',
        ]);

        // Konversi budget ke numeric jika diisi (hapus karakter non-angka)
        $budgetNumeric = null;
        if (!empty($data['budget_diinginkan'])) {
            $budgetNumeric = (float) preg_replace('/[^0-9]/', '', $data['budget_diinginkan']);
            if ($budgetNumeric <= 0) $budgetNumeric = null;
        }

        Negotiation::create([
            'event_id'          => $proposal->event_id,
            'user_id'           => $uid,
            'pesan'             => $data['pesan'],
            'budget_diinginkan' => $budgetNumeric,
            'catatan_tambahan'  => $data['catatan_tambahan'] ?? null,
        ]);

        $proposal->update([
            'status' => 'negosiasi'
        ]);

        $proposal->event->update(['status_event' => 'menunggu']);

        User::where('role', 'admin')->each(function (User $admin) use ($proposal) {
            Notification::create([
                'user_id' => $admin->id,
                'judul'   => 'Negosiasi Baru dari Client',
                'pesan'   => 'Client ' . Auth::user()->name . ' mengajukan negosiasi untuk event "' . $proposal->event->nama_event . '".',
                'tipe'    => 'peringatan',
                'dibaca'  => false,
            ]);
        });

        return redirect()
            ->route('client.proposals.show', $proposal->id)
            ->with('success', 'Negosiasi berhasil diajukan. Menunggu respon dari admin.');
    }

    // ──────────────────────────────────────────────────
    //  B2) Terima Penawaran SETELAH Negosiasi
    // ──────────────────────────────────────────────────

    /**
     * Client menerima revisi penawaran setelah negosiasi.
     * Route: POST /proposals/{proposal}/terima-setelah-negosiasi
     */
    public function terimaSetelahNegosiasi(Request $request, int $id)
    {
        $uid      = Auth::id();
        $eIds     = Event::where('client_id', $uid)->pluck('id');
        $proposal = Proposal::whereIn('event_id', $eIds)->findOrFail($id);

        $proposal->update([
            'status' => 'diterima'
        ]);

        $event = $proposal->event;

        $negotiation = Negotiation::where('event_id', $event->id)->latest()->first();

        TimelineAutoFill::negosiasiSelesai($event, $negotiation);

        User::where('role', 'admin')->each(function (User $admin) use ($event) {
            Notification::create([
                'user_id' => $admin->id,
                'judul'   => 'Negosiasi Selesai — Penawaran Diterima',
                'pesan'   => 'Client ' . Auth::user()->name . ' menerima penawaran revisi untuk event "' . $event->nama_event . '". Timeline otomatis telah diisi.',
                'tipe'    => 'sukses',
                'dibaca'  => false,
            ]);
        });

        return redirect()
            ->route('client.proposals.show', $proposal->id)
            ->with('success', 'Penawaran diterima! Timeline event telah disiapkan secara otomatis.');
    }

    // ══════════════════════════════════════════════════
    //  EVENT BARU
    // ══════════════════════════════════════════════════

    public function eventCreate()
    {
        return view('client.event-create', $this->notifData());
    }

    public function eventStore(Request $request)
    {
        $validated = $request->validate([
            'nama_event'       => 'required|string|max:255',
            'jenis_event'      => 'required|string',
            'tanggal_event'    => 'required|date|after:today',
            'lokasi_event'     => 'required|string|max:500',
            'jumlah_tamu'      => 'required|integer|min:1',
            'rentang_anggaran' => 'nullable|string|max:100',
            'detail_kebutuhan' => 'nullable|string|max:2000',
        ]);

        $event = Event::create(array_merge($validated, [
            'client_id'    => Auth::id(),
            'status_event' => 'menunggu',
        ]));

        User::where('role', 'admin')->each(function (User $admin) use ($event) {
            Notification::create([
                'user_id' => $admin->id,
                'judul'   => 'Request Event Baru',
                'pesan'   => 'Client ' . Auth::user()->name . ' mengajukan event baru: "' . $event->nama_event . '".',
                'tipe'    => 'info',
                'dibaca'  => false,
            ]);
        });

        return redirect()->route('client.dashboard')
            ->with('success', 'Request event berhasil dikirim! Kami akan segera menghubungi Anda.');
    }

    // ══════════════════════════════════════════════════
    //  PENGATURAN
    // ══════════════════════════════════════════════════

    public function settings()
    {
        return view('client.settings', array_merge(['user' => Auth::user()], $this->notifData()));
    }

    public function settingsProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        $user->update($data);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function settingsPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|string|min:8|confirmed',
        ]);
        Auth::user()->update(['password' => bcrypt($request->password)]);
        return back()->with('success', 'Password berhasil diubah.');
    }

    // ══════════════════════════════════════════════════
    //  NOTIFIKASI
    // ══════════════════════════════════════════════════

    public function notifications()
    {
        $uid           = Auth::id();
        $notifications = Notification::where('user_id', $uid)->latest()->paginate(15);
        Notification::where('user_id', $uid)->where('dibaca', false)->update(['dibaca' => true]);

        return view('client.notification', array_merge(
            ['notifications' => $notifications],
            $this->notifData()
        ));
    }

    public function notifRead()
    {
        Notification::where('user_id', Auth::id())->where('dibaca', false)->update(['dibaca' => true]);
        return back();
    }
}