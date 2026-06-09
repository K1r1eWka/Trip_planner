<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $ownedTrips = Trip::where("owner_id", $user->id)->get();
        $memberTrips = $user->trips()->where("trips.owner_id", "!=", $user->id)->get();

        return view("trips.index", compact("ownedTrips", "memberTrips"));
    }

    public function create()
    {
        return view("trips.create");
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|max:255",
            "description" => "nullable|max:1000",
        ]);

        $trip = Trip::create([
            "name" => $request->name,
            "description" => $request->description,
            "owner_id" => $request->user()->id,
            "invite_code" => Str::random(8),
        ]);

        $trip->members()->attach($request->user()->id);

        return redirect()->route("trips.show", $trip)->with("success", "Trip created!");
    }

    public function show(Request $request, Trip $trip)
    {
        $user = $request->user();

        if ($trip->owner_id !== $user->id && !$trip->hasMember($user)) {
            abort(403);
        }

        $trip->load(["owner", "members", "tasks", "expenses", "checkpoints"]);

        return view("trips.show", compact("trip"));
    }

    public function destroy(Request $request, Trip $trip)
    {
        if ($request->user()->cannot("delete", $trip)) {
            abort(403);
        }

        $trip->delete();

        return redirect()->route("trips.index")->with("success", "Trip deleted.");
    }
}