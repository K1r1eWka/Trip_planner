<x-layout :fullBg="true">
    <x-slot name="title">Join a Trip — Trip Planner</x-slot>
    <style>:root { --bg-image: url('{{ asset("images/image8.png") }}'); }</style>

    <div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 56px);">
        <div class="card" style="width: 400px; background: rgba(255,255,255,0.95);">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Join a Trip</h5>
                <p class="text-muted small mb-4">Enter the invite code shared by the trip owner</p>

                <form action="{{ route('trips.join') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="invite_code"
                            class="form-control @error('invite_code') is-invalid @enderror"
                            placeholder="Enter invite code..."
                            value="{{ old('invite_code') }}"
                            autofocus>
                        @error("invite_code")
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">Join Trip</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('trips.index') }}" class="text-muted small">← Back to my trips</a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
