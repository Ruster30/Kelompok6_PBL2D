<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function create(Event $event)
    {
        $cek = Feedback::where('event_id', $event->id)
            ->where('client_id', auth()->id())
            ->exists();

        if ($cek) {
            return back()->with('error', 'Feedback sudah diberikan');
        }

        return view('client.feedback', compact('event'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'required|min:10',
        ]);

        Feedback::create([
            'event_id'  => $request->event_id,
            'client_id' => auth()->id(),
            'rating'    => $request->rating,
            'ulasan'    => $request->ulasan,
        ]);

        return redirect()
            ->route('client.events')
            ->with('success', 'Feedback berhasil dikirim');
    }
}
