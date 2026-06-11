<?php

namespace App\Http\Controllers;

use App\Events\MemberJoined;
use App\Mail\MemberJoinedMail;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class JoinController extends Controller
{
    public function show()
    {
        return view("trips.join");
    }

    public function join(Request $request)
    {
        $request->validate([
            "invite_code" => "required|string",
        ]);

        $trip = Trip::where("invite_code", $request->invite_code)->first();

        if (!$trip) {
            return back()->withErrors(["invite_code" => "Invalid invite code."])->withInput();
        }

        if ($trip->owner_id === $request->user()->id || $trip->hasMember($request->user())) {
            return redirect()->route("trips.show", $trip)->with("success", "You are already in this trip.");
        }

        $trip->members()->attach($request->user()->id);

        broadcast(new MemberJoined($trip, $request->user()))->toOthers();

        Mail::to($trip->owner->email)->send(new MemberJoinedMail($trip, $request->user()));

        return redirect()->route("trips.show", $trip)->with("success", "You joined the trip!");
    }
}