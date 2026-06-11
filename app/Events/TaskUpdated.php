<?php

namespace App\Events;

use App\Models\Task;
use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Trip $trip)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel("trip." . $this->trip->id);
    }

    public function broadcastAs(): string
    {
        return "task.updated";
    }

    public function broadcastWith(): array
    {
        $tasks = $this->trip->tasks()->with("user")->get();

        return [
            "tasks" => $tasks->map(fn($t) => [
                "id" => $t->id,
                "title" => $t->title,
                "description" => $t->description,
                "status" => $t->status,
                "user_id" => $t->user_id,
            ])->toArray(),
        ];
    }
}
