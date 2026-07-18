<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Event;
use App\Services\FeedbackService;

class FeedbackController extends Controller
{
    public function __construct(
        private FeedbackService $feedbackService
    ) {}

    public function create(Event $event)
    {
        $clientId = auth()->id();

        if ($this->feedbackService->hasGivenFeedback($event, $clientId)) {
            return back()->with("error", "Feedback sudah diberikan");
        }

        return view("client.feedback", compact("event"));
    }

    public function store(StoreFeedbackRequest $request)
    {
        $this->feedbackService->createFeedback([
            "event_id"  => $request->event_id,
            "client_id" => auth()->id(),
            "rating"    => $request->rating,
            "ulasan"    => $request->ulasan,
        ]);

        return redirect()
            ->route("client.events")
            ->with("success", "Feedback berhasil dikirim");
    }
}
