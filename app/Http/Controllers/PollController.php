<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Trip;
use App\Models\Vote;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function create(Request $request, Trip $trip)
    {
        if ($request->user()->cannot("view", $trip)) {
            abort(403);
        }

        return view("polls.create", compact("trip"));
    }

    public function store(Request $request, Trip $trip)
    {
        if ($request->user()->cannot("view", $trip)) {
            abort(403);
        }

        $request->validate([
            "title" => "required|max:255",
            "type" => "required|in:destination,accommodation,transport,activities,other",
            "options" => "required|array|min:2",
            "options.*" => "required|string|max:255",
        ]);

        $poll = Poll::create([
            "trip_id" => $trip->id,
            "title" => $request->title,
            "type" => $request->type,
            "is_closed" => false,
        ]);

        foreach ($request->options as $optionTitle) {
            if (trim($optionTitle) !== "") {
                PollOption::create([
                    "poll_id" => $poll->id,
                    "title" => trim($optionTitle),
                ]);
            }
        }

        return redirect()->route("trips.show", $trip)->with("success", "Poll created!");
    }

    public function vote(Request $request, Trip $trip, Poll $poll)
    {
        if ($request->user()->cannot("view", $trip)) {
            abort(403);
        }

        if ($poll->is_closed) {
            return redirect()->route("trips.show", $trip)->with("error", "This poll is closed.");
        }

        $request->validate([
            "poll_option_id" => "required|exists:poll_options,id",
        ]);

        $optionId = $request->poll_option_id;

        $belongsToPoll = $poll->options()->where("id", $optionId)->exists();
        if (!$belongsToPoll) {
            abort(403);
        }

        $alreadyVoted = Vote::whereHas("pollOption", fn($q) => $q->where("poll_id", $poll->id))
            ->where("user_id", $request->user()->id)
            ->exists();

        if ($alreadyVoted) {
            return redirect()->route("trips.show", $trip)->with("error", "You already voted in this poll.");
        }

        Vote::create([
            "user_id" => $request->user()->id,
            "poll_option_id" => $optionId,
        ]);

        return redirect()->route("trips.show", $trip)->with("success", "Vote recorded!");
    }

    public function close(Request $request, Trip $trip, Poll $poll)
    {
        if ($request->user()->cannot("manage", $trip)) {
            abort(403);
        }

        $poll->update(["is_closed" => true]);

        return redirect()->route("trips.show", $trip)->with("success", "Poll closed.");
    }

    public function destroy(Request $request, Trip $trip, Poll $poll)
    {
        if ($request->user()->cannot("manage", $trip)) {
            abort(403);
        }

        $poll->delete();

        return redirect()->route("trips.show", $trip)->with("success", "Poll deleted.");
    }
}
