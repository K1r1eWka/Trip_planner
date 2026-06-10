<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Trip;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Trip $trip)
    {
        if ($request->user()->cannot("view", $trip)) {
            abort(403);
        }

        $request->validate([
            "title" => "required|max:255",
            "description" => "nullable|max:1000",
        ]);

        Task::create([
            "trip_id" => $trip->id,
            "user_id" => $request->user()->id,
            "title" => $request->title,
            "description" => $request->description,
            "status" => "pending",
        ]);

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

        return redirect()->route("trips.show", $trip)->with("success", "Task updated!");
    }

    public function destroy(Request $request, Trip $trip, Task $task)
    {
        if ($request->user()->id !== $task->user_id && $request->user()->cannot("manage", $trip)) {
            abort(403);
        }

        $task->delete();

        return redirect()->route("trips.show", $trip)->with("success", "Task deleted.");
    }
}