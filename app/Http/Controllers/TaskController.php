<?php

namespace App\Http\Controllers;

use App\Events\TaskUpdated;
use App\Models\Task;
use App\Models\Trip;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private function tasksPayload(Trip $trip): array
    {
        return $trip->tasks()->get()->map(fn($t) => [
            "id" => $t->id,
            "title" => $t->title,
            "description" => $t->description,
            "status" => $t->status,
            "user_id" => $t->user_id,
        ])->toArray();
    }

    public function store(Request $request, Trip $trip)
    {
        if ($request->user()->cannot("view", $trip)) {
            abort(403);
        }

        $request->validate([
            "title" => "required|max:255",
            "description" => "nullable|max:1000",
        ]);

        $task = Task::create([
            "trip_id" => $trip->id,
            "user_id" => $request->user()->id,
            "title" => $request->title,
            "description" => $request->description,
            "status" => "pending",
        ]);

        broadcast(new TaskUpdated($trip))->toOthers();

        if ($request->expectsJson()) {
            return response()->json(["tasks" => $this->tasksPayload($trip), "current_user_id" => $request->user()->id]);
        }

        return redirect()->route("trips.show", $trip)->with("success", "Task added!");
    }

    public function update(Request $request, Trip $trip, Task $task)
    {
        if ($request->user()->cannot("view", $trip)) {
            abort(403);
        }

        $request->validate([
            "status" => "required|in:pending,done",
        ]);

        $task->update(["status" => $request->status]);

        broadcast(new TaskUpdated($trip))->toOthers();

        if ($request->expectsJson()) {
            return response()->json(["tasks" => $this->tasksPayload($trip), "current_user_id" => $request->user()->id]);
        }

        return redirect()->route("trips.show", $trip)->with("success", "Task updated!");
    }

    public function destroy(Request $request, Trip $trip, Task $task)
    {
        if ($request->user()->id !== $task->user_id && $request->user()->cannot("manage", $trip)) {
            abort(403);
        }

        $task->delete();

        broadcast(new TaskUpdated($trip))->toOthers();

        if ($request->expectsJson()) {
            return response()->json(["tasks" => $this->tasksPayload($trip), "current_user_id" => $request->user()->id]);
        }

        return redirect()->route("trips.show", $trip)->with("success", "Task deleted.");
    }
}
