<x-layout :fullBg="true">
    <x-slot name="title">Trip Planner</x-slot>
    <style>:root { --bg-image: url('{{ asset("images/image8.png") }}'); }</style>

    <div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 56px);">
        <div class="text-center text-white">
            <h1 class="fw-bold mb-2" style="font-size: 3rem;">Plan Your Trip</h1>
            <p class="text-white-50 mb-5" style="font-size: 1.1rem;">Create a trip or join one with an invite code</p>

            <div class="d-flex gap-3 justify-content-center">
                @auth
                    <a href="{{ route('trips.create') }}" class="btn btn-light btn-lg px-4 fw-semibold">Create a Trip</a>
                    <a href="{{ route('trips.index') }}" class="btn btn-outline-light btn-lg px-4">Join with Code</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4 fw-semibold">Create a Trip</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">Join with Code</a>
                @endauth
            </div>
        </div>
    </div>
</x-layout>
