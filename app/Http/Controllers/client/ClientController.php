<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Event, Invoice, Payment, Proposal, Timeline, Notification};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};

class ClientController extends Controller
{
    // ── private helper: pastikan event milik client ini ─
    private function myEvent(int $id): Event
    {
        return Event::where('client_id', Auth::id())->findOrFail($id);
    }

    // ══════════════════════════════════════════════════
    //  DASHBOARD
    // ══════════════════════════════════════════════════
    public function dashboard()
    {
        $uid    = Auth::id();
        $events = Event::where('client_id', $uid)->get();
        $eIds   = $events->pluck('id');
        $invoiceIds = Invoice::whereIn('event_id',$eIds)->pluck('id');
        $totalDibayar = Payment::whereIn('invoice_id',$invoiceIds)
            ->where('status_pembayaran','diverifikasi')
            ->sum('nominal');

        return view('client.dashboard', [
            'eventBerjalan'  => $events->where('status_event','berjalan')->count(),
            'eventMenunggu'  => $events->whereIn('status_event',['menunggu','diproses'])->count(),  
            'totalDibayar' => $totalDibayar,
            'recentEvents'   => Event::where('client_id',$uid)
                                     ->with(['latestProposal','timelines'])
                                     ->latest()->take(3)->get(),
            'notifications'  => Notification::where('user_id',$uid)->latest()->take(5)->get(),
            'unreadCount'    => Notification::where('user_id',$uid)->where('dibaca',false)->count(),
        ]);
    }

    // ══════════════════════════════════════════════════
    //  EVENT TERDAFTAR
    // ══════════════════════════════════════════════════
    public function events()
    {
        $events = Event::where('client_id', Auth::id())
                       ->with('timelines')
                       ->latest()->get();

        return view('client.events', [
            'events'      => $events,
            'unreadCount' => Notification::where('user_id',Auth::id())->where('dibaca',false)->count(),
            'notifications' => Notification::where('user_id',Auth::id())->latest()->take(5)->get(),
        ]);
    }

    // ══════════════════════════════════════════════════
    //  TIMELINE
    // ══════════════════════════════════════════════════
    public function timeline(?int $eventId = null)
    {
        $uid      = Auth::id();
        $myEvents = Event::where('client_id',$uid)->get();

        $selected = $eventId
            ? $this->myEvent($eventId)
            : $myEvents->first();

        $timelines = $selected
            ? Timeline::where('event_id',$selected->id)->orderBy('tanggal_kegiatan')->get()
            : collect();

        $totalTask = $timelines->count();
        $doneTask  = $timelines->where('status_kegiatan','selesai')->count();
        $progress  = $totalTask > 0 ? round($doneTask/$totalTask*100) : 0;

        return view('client.timeline', [
            'myEvents'      => $myEvents,
            'selected'      => $selected,
            'timelines'     => $timelines,
            'totalTask'     => $totalTask,
            'doneTask'      => $doneTask,
            'progress'      => $progress,
            'unreadCount'   => Notification::where('user_id',$uid)->where('dibaca',false)->count(),
            'notifications' => Notification::where('user_id',$uid)->latest()->take(5)->get(),
        ]);
    }

    // ══════════════════════════════════════════════════
    //  ANGGARAN & FAKTUR
    // ══════════════════════════════════════════════════
    public function invoices()
    {
        $uid    = Auth::id();
        $eIds   = Event::where('client_id',$uid)->pluck('id');

        $invoiceIds = Invoice::whereIn('event_id',$eIds)
                     ->pluck('id');

        $invoices = Invoice::whereIn('event_id',$eIds)
                           ->with('event')->latest()->paginate(10);

        $payments = Payment::whereIn('invoice_id',$invoiceIds)
                           ->with('invoice.event')->latest()->get();

        $totalInvoice  = Invoice::whereIn('event_id',$eIds)->sum('total_invoice');
        $totalDibayar = Payment::whereIn('invoice_id',$invoiceIds)
                       ->where('status_pembayaran','diverifikasi')
                       ->sum('nominal');

        return view('client.invoices', [
            'invoices'      => $invoices,
            'payments'      => $payments,
            'totalInvoice'  => $totalInvoice,
            'totalDibayar'  => $totalDibayar,
            'sisaTagihan'   => $totalInvoice - $totalDibayar,
            'unreadCount'   => Notification::where('user_id',$uid)->where('dibaca',false)->count(),
            'notifications' => Notification::where('user_id',$uid)->latest()->take(5)->get(),
        ]);
    }

    // Upload bukti pembayaran
    public function bayar(Request $request, int $invoiceId)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'jenis_pembayaran' => 'required|in:dp,pelunasan',
            'nominal'          => 'required|numeric|min:1000',
        ]);

        $invoice = Invoice::with('event')
            ->where('id', $invoiceId)
            ->whereHas('event', fn ($query) => $query->where('client_id', Auth::id()))
            ->firstOrFail();

        $event = $invoice->event;
        $path  = $request->file('bukti_pembayaran')
                         ->store('payments/'.$invoiceId,'public');

        Payment::create([
            'invoice_id'         => $invoice->id,
            'nominal'            => $request->nominal,
            'tanggal_pembayaran' => now()->toDateString(),
            'status_pembayaran'  => 'menunggu',
            'bukti_pembayaran'   => $path,
            'jenis_pembayaran'   => $request->jenis_pembayaran,
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'judul'   => 'Bukti Pembayaran Diunggah',
            'pesan'   => 'Bukti '.$request->jenis_pembayaran.' untuk "'.$event->nama_event.'" sedang diverifikasi.',
            'tipe'    => 'info',
        ]);

        return back()->with('success','Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }

    // ══════════════════════════════════════════════════
    //  SURAT PENAWARAN
    // ══════════════════════════════════════════════════
    public function proposals()
    {
        $uid    = Auth::id();
        $eIds   = Event::where('client_id',$uid)->pluck('id');

        $proposals = Proposal::whereIn('event_id',$eIds)
                             ->with('event')->latest()->get();

        return view('client.proposals', [
            'proposals'     => $proposals,
            'unreadCount'   => Notification::where('user_id',$uid)->where('dibaca',false)->count(),
            'notifications' => Notification::where('user_id',$uid)->latest()->take(5)->get(),
        ]);
    }

    public function proposalShow(int $id)
    {
        $uid    = Auth::id();
        $eIds   = Event::where('client_id',$uid)->pluck('id');

        $proposal = Proposal::whereIn('event_id',$eIds)
                            ->with(['event.rabs','event.contract'])
                            ->findOrFail($id);

        return view('client.proposal-show', [
            'proposal'      => $proposal,
            'unreadCount'   => Notification::where('user_id',$uid)->where('dibaca',false)->count(),
            'notifications' => Notification::where('user_id',$uid)->latest()->take(5)->get(),
        ]);
    }

    // ══════════════════════════════════════════════════
    //  AJUKAN EVENT BARU
    // ══════════════════════════════════════════════════
    public function eventCreate()
    {
        $uid = Auth::id();
        return view('client.event-create', [
            'unreadCount'   => Notification::where('user_id',$uid)->where('dibaca',false)->count(),
            'notifications' => Notification::where('user_id',$uid)->latest()->take(5)->get(),
        ]);
    }

    public function eventStore(Request $request)
    {
        $validated = $request->validate([
            'nama_event'       => 'required|string|max:255',
            'jenis_event'      => 'required|string',
            'tanggal_event'    => 'required|date|after:today',
            'lokasi_event'     => 'required|string|max:500',
            'jumlah_tamu'      => 'required|integer|min:1',
            'detail_kebutuhan' => 'nullable|string|max:2000',
        ]);

        $event = Event::create([
            ...$validated,
            'client_id'    => Auth::id(),
            'status_event' => 'menunggu',
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'judul'   => 'Pengajuan Event Baru',
            'pesan'   => 'Event "'.$event->nama_event.'" berhasil diajukan. Tim kami akan segera menghubungi Anda.',
            'tipe'    => 'info',
        ]);

        return redirect()->route('client.dashboard')
               ->with('success','Event berhasil diajukan! Tim kami akan menghubungi Anda dalam 24 jam.');
    }

    // ══════════════════════════════════════════════════
    //  PENGATURAN AKUN
    // ══════════════════════════════════════════════════
    public function settings()
    {
        $uid = Auth::id();
        return view('client.settings', [
            'user'          => Auth::user(),
            'unreadCount'   => Notification::where('user_id',$uid)->where('dibaca',false)->count(),
            'notifications' => Notification::where('user_id',$uid)->latest()->take(5)->get(),
        ]);
    }

    public function settingsProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        $user->fill(
            $request->only('name', 'email', 'phone')
        );

        $user->save();
        return back()->with('success','Profil berhasil diperbarui.');
    }

    public function settingsPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password'=>'Password saat ini tidak sesuai.']);
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();
        return back()->with('success','Password berhasil diubah.');
    }

    // Notikasi
    public function notifications()
    {
        $notifications = Notification::where(
            'user_id',
            auth()->id()
        )
        ->latest()
        ->paginate(10);

        $unreadCount = Notification::where(
            'user_id',
            auth()->id()
        )
        ->where('dibaca', false)
        ->count();

        return view(
            'client.notification',
            compact(
                'notifications',
                'unreadCount'
            )
        );
    }
    public function notifRead()
    {
        Notification::where(
            'user_id',
            auth()->id()
        )
        ->update([
            'dibaca' => true
        ]);

        return back()->with(
            'success',
            'Semua notifikasi telah dibaca.'
        );
    }
}
