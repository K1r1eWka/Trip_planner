<x-layout :fullBg="true">
    <x-slot name="title">My Trips — Trip Planner</x-slot>
    <style>:root { --bg-image: url('{{ asset("images/image8.png") }}'); }</style>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white fw-bold mb-0">My Trips</h2>
            <a href="{{ route('trips.create') }}" class="btn btn-light fw-semibold">+ New Trip</a>
        </div>

        @if($ownedTrips->isEmpty() && $memberTrips->isEmpty())
            <div class="card p-5 text-center" style="background: rgba(255,255,255,0.88);">
                <p class="text-muted mb-3">You have no trips yet.</p>
                <a href="{{ route('trips.create') }}" class="btn btn-dark">Create your first trip</a>
            </div>
        @endif

        @if($ownedTrips->isNotEmpty())
            <h5 class="text-white-50 mb-3">Trips you own</h5>
            <div class="row g-3 mb-4">
                @foreach($ownedTrips as $trip)
                    <div class="col-md-4">
                        <div class="card h-100" style="background: rgba(255,255,255,0.92);">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold">{{ $trip->name }}</h5>
                                @if($trip->description)
                                    <p class="card-text text-muted small">{{ \Illuminate\Support\Str::limit($trip->description, 80) }}</p>
                                @endif
                                <span class="badge bg-secondary mb-2">Owner</span>
                            </div>
                            <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center pb-3">
                                <a href="{{ route('trips.show', $trip) }}" class="btn btn-dark btn-sm">View</a>
                                <form action="{{ route('trips.destroy', $trip) }}" method="POST" onsubmit="return confirm('Delete this trip?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($memberTrips->isNotEmpty())
            <h5 class="text-white-50 mb-3">Trips you joined</h5>
            <div class="row g-3">
                @foreach($memberTrips as $trip)
                    <div class="col-md-4">
                        <div class="card h-100" style="background: rgba(255,255,255,0.92);">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold">{{ $trip->name }}</h5>
                                @if($trip->description)
                                    <p class="card-text text-muted small">{{ \Illuminate\Support\Str::limit($trip->description, 80) }}</p>
                                @endif
                                <span class="badge bg-primary mb-2">Member</span>
                            </div>
                            <div class="card-footer bg-transparent border-0 pb-3">
                                <a href="{{ route('trips.show', $trip) }}" class="btn btn-dark btn-sm">View</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layout>
