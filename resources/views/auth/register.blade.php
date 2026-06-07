<x-layout :fullBg="true">
    <x-slot name="title">Register — Trip Planner</x-slot>

    <div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 56px);">
        <div class="card p-4" style="width: 100%; max-width: 420px; background: rgba(255,255,255,0.92); backdrop-filter: blur(8px);">
            <h4 class="mb-1 fw-semibold">Create account</h4>
            <p class="text-muted mb-4" style="font-size: 0.9rem;">Start planning your trips</p>

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error("name") <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error("email") <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error("password") <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-dark w-100 mb-3">Create account</button>
                <p class="text-center text-muted mb-0" style="font-size: 0.9rem;">
                    Already have an account? <a href="{{ route('login') }}" class="text-dark fw-semibold">Sign in</a>
                </p>
            </form>
        </div>
    </div>
</x-layout>