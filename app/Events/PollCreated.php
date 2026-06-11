<?php

namespace App\Events;

use App\Models\Poll;
use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PollCreated implements ShouldBroadcast
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
        return "poll.created";
    }

    public function broadcastWith(): array
    {
        $poll = $this->poll->load("options");

        return [
            "poll_id" => $poll->id,
            "title" => $poll->title,
            "type" => $poll->type,
            "options" => $poll->options->map(fn($o) => [
                "id" => $o->id,
                "title" => $o->title,
            ])->toArray(),
        ];
    }
}
