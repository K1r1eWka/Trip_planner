<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\JoinController;

Route::get("/", function () {
    return view("welcome");
});

Route::middleware("guest")->group(function () {
    Route::get("/register", [AuthController::class, "showRegister"])->name("register");
    Route::post("/register", [AuthController::class, "register"]);
    Route::get("/login", [AuthController::class, "showLogin"])->name("login");
    Route::post("/login", [AuthController::class, "login"]);
});

Route::post("/logout", [AuthController::class, "logout"])->middleware("auth")->name("logout");

Route::middleware("auth")->group(function () {
    Route::get("/trips", [TripController::class, "index"])->name("trips.index");
    Route::get("/trips/create", [TripController::class, "create"])->name("trips.create");
    Route::post("/trips", [TripController::class, "store"])->name("trips.store");
    Route::get("/trips/{trip}", [TripController::class, "show"])->name("trips.show");
    Route::delete("/trips/{trip}", [TripController::class, "destroy"])->name("trips.destroy");

    Route::post("/trips/{trip}/tasks", [TaskController::class, "store"])->name("tasks.store");
    Route::patch("/trips/{trip}/tasks/{task}", [TaskController::class, "update"])->name("tasks.update");
    Route::delete("/trips/{trip}/tasks/{task}", [TaskController::class, "destroy"])->name("tasks.destroy");

    Route::get("/trips/{trip}/polls/create", [PollController::class, "create"])->name("polls.create");
    Route::post("/trips/{trip}/polls", [PollController::class, "store"])->name("polls.store");
    Route::post("/trips/{trip}/polls/{poll}/vote", [PollController::class, "vote"])->name("polls.vote");
    Route::patch("/trips/{trip}/polls/{poll}/close", [PollController::class, "close"])->name("polls.close");
    Route::delete("/trips/{trip}/polls/{poll}", [PollController::class, "destroy"])->name("polls.destroy");

    Route::get("/join", [JoinController::class, "show"])->name("trips.join");
    Route::post("/join", [JoinController::class, "join"]);
});