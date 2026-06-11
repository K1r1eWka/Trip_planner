<?php

namespace App\Http\Controllers;

use App\Mail\TripInvitationMail;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InviteController extends Controller
{
    public function send(Request $request, Trip $trip)
    {
        if ($request->user()->cannot("manage", $trip)) {
            abort(403);
        }

        $request->validate([
            "email" => "required|email",
        ]);

        Mail::to($request->email)->send(new TripInvitationMail($trip, $request->email));

        return redirect()->route("trips.show", $trip)->with("success", "Invitation sent to " . $request->email . "!");
    }
}