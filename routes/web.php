<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;

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
});