<?php

namespace App\Events;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberJoined implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Trip $trip, public User $user)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel("trip." . $this->trip->id);
    }

    public function broadcastAs(): string
    {
        return "member.joined";
    }

    public function broadcastWith(): array
    {
        return [
            "user_id" => $this->user->id,
            "name" => $this->user->name,
            "is_owner" => $this->user->id === $this->trip->owner_id,
        ];
    }
}
