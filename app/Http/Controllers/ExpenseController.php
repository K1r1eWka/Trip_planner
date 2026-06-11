<?php

namespace App\Http\Controllers;

use App\Events\ExpenseUpdated;
use App\Models\Expense;
use App\Models\Trip;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    private function expensesPayload(Trip $trip): array
    {
        $expenses = $trip->expenses()->get();
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

    public function store(Request $request, Trip $trip)
    {
        if ($request->user()->cannot("view", $trip)) {
            abort(403);
        }

        $request->validate([
            "title" => "required|max:255",
            "amount" => "required|numeric|min:0.01|max:999999.99",
        ]);

        Expense::create([
            "trip_id" => $trip->id,
            "user_id" => $request->user()->id,
            "title" => $request->title,
            "amount" => $request->amount,
        ]);

        broadcast(new ExpenseUpdated($trip))->toOthers();

        if ($request->expectsJson()) {
            $payload = $this->expensesPayload($trip);
            $payload["current_user_id"] = $request->user()->id;
            return response()->json($payload);
        }

        return redirect()->route("trips.show", $trip)->with("success", "Expense added!");
    }

    public function destroy(Request $request, Trip $trip, Expense $expense)
    {
        if ($expense->user_id !== $request->user()->id && $request->user()->cannot("manage", $trip)) {
            abort(403);
        }

        $expense->delete();

        broadcast(new ExpenseUpdated($trip))->toOthers();

        if ($request->expectsJson()) {
            $payload = $this->expensesPayload($trip);
            $payload["current_user_id"] = $request->user()->id;
            return response()->json($payload);
        }

        return redirect()->route("trips.show", $trip)->with("success", "Expense removed.");
    }
}
