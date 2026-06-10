<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Trip;
use App\Models\Task;
use App\Models\Expense;
use App\Models\Checkpoint;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Vote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $kirill = User::create([
            "name" => "Kirill",
            "email" => "kirill@example.com",
            "password" => Hash::make("password"),
            "role" => "user",
        ]);

        $maks = User::create([
            "name" => "Maks",
            "email" => "maks@example.com",
            "password" => Hash::make("password"),
            "role" => "user",
        ]);

        $admin = User::create([
            "name" => "Admin",
            "email" => "admin@example.com",
            "password" => Hash::make("password"),
            "role" => "admin",
        ]);

        $trip = Trip::create([
            "name" => "Paris Trip",
            "description" => "Weekend in Paris",
            "owner_id" => $kirill->id,
            "invite_code" => Str::random(8),
        ]);

        $trip->members()->attach([$kirill->id, $maks->id]);

        Task::create(["trip_id" => $trip->id, "user_id" => $kirill->id, "title" => "Book flights", "status" => "done"]);
        Task::create(["trip_id" => $trip->id, "user_id" => $maks->id, "title" => "Find hotel", "status" => "pending"]);

        Expense::create(["trip_id" => $trip->id, "user_id" => $kirill->id, "title" => "Flights", "amount" => 240.00]);
        Expense::create(["trip_id" => $trip->id, "user_id" => $maks->id, "title" => "Hotel", "amount" => 180.50]);

        Checkpoint::create(["trip_id" => $trip->id, "title" => "Paris", "date" => "2026-07-10"]);
        Checkpoint::create(["trip_id" => $trip->id, "title" => "Versailles", "date" => "2026-07-12"]);

        $poll1 = Poll::create(["trip_id" => $trip->id, "title" => "Where should we stay?", "type" => "accommodation", "is_closed" => false]);
        $opt1 = PollOption::create(["poll_id" => $poll1->id, "title" => "Hotel near Eiffel Tower"]);
        $opt2 = PollOption::create(["poll_id" => $poll1->id, "title" => "Airbnb in Marais"]);
        $opt3 = PollOption::create(["poll_id" => $poll1->id, "title" => "Hostel near Louvre"]);
        Vote::create(["user_id" => $kirill->id, "poll_option_id" => $opt1->id]);
        Vote::create(["user_id" => $maks->id, "poll_option_id" => $opt2->id]);

        $poll2 = Poll::create(["trip_id" => $trip->id, "title" => "How do we get there?", "type" => "transport", "is_closed" => false]);
        PollOption::create(["poll_id" => $poll2->id, "title" => "Flight"]);
        PollOption::create(["poll_id" => $poll2->id, "title" => "Train"]);
        PollOption::create(["poll_id" => $poll2->id, "title" => "Bus"]);
    }
}
