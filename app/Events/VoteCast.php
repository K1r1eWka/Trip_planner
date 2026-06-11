<?php

namespace App\Events;

use App\Models\Poll;
use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoteCast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Poll $poll, public Trip $trip)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel("trip." . $this->trip->id);
    }

    public function broadcastAs(): string
    {
        return "vote.cast";
    }

    public function broadcastWith(): array
    {
        $poll = $this->poll->load("options.votes");
        $totalVotes = $poll->options->sum(fn($o) => $o->votes->count());

        return [
            "poll_id" => $poll->id,
            "options" => $poll->options->map(fn($o) => [
                "id" => $o->id,
                "title" => $o->title,
                "votes" => $o->votes->count(),
                "percent" => $totalVotes > 0 ? round($o->votes->count() / $totalVotes * 100) : 0,
            ])->toArray(),
        ];
    }
}
