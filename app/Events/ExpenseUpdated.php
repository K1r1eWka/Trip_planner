<?php

namespace App\Events;

use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExpenseUpdated implements ShouldBroadcast
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
        return "expense.updated";
    }

    public function broadcastWith(): array
    {
        $expenses = $this->trip->expenses()->get();

        return [
            "expenses" => $expenses->map(fn($e) => [
                "id" => $e->id,
                "title" => $e->title,
                "amount" => $e->amount,
                "user_id" => $e->user_id,
            ])->toArray(),
            "total" => $expenses->sum("amount"),
        ];
    }
}
