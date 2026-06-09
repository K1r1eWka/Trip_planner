<x-layout :fullBg="true">
    <x-slot name="title">Create Trip — Trip Planner</x-slot>
    <style>:root { --bg-image: url('{{ asset("images/image6.png") }}'); }</style>

    <div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 56px);">
        <div class="card p-4" style="width: 100%; max-width: 480px; background: rgba(255,255,255,0.92); backdrop-filter: blur(8px);">
            <h4 class="mb-1 fw-semibold">New Trip</h4>
            <p class="text-muted mb-4" style="font-size: 0.9rem;">Plan your next adventure</p>

            <form action="{{ route('trips.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Trip name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error("name") <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-4">
                    <label for="description" class="form-label">Description <span class="text-muted">(optional)</span></label>
                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error("description") <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-dark w-100 mb-3">Create Trip</button>
                <p class="text-center text-muted mb-0" style="font-size: 0.9rem;">
                    <a href="{{ route('trips.index') }}" class="text-dark">Back to my trips</a>
                </p>
            </form>
        </div>
    </div>
</x-layout>
