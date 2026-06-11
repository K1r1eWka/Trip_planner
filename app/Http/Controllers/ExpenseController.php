<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Trip;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
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

        return redirect()->route("trips.show", $trip)->with("success", "Expense added!");
    }

    public function destroy(Request $request, Trip $trip, Expense $expense)
    {
        if ($expense->user_id !== $request->user()->id && $request->user()->cannot("manage", $trip)) {
            abort(403);
        }

        $expense->delete();

        return redirect()->route("trips.show", $trip)->with("success", "Expense removed.");
    }
}