<x-layout :fullBg="true">
    <x-slot name="title">New Poll — {{ $trip->name }}</x-slot>
    <style>:root { --bg-image: url('{{ asset("images/image8.png") }}'); }</style>

    <div class="container py-5" style="max-width: 560px;">
        <div class="card" style="background: rgba(255,255,255,0.95);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <a href="{{ route('trips.show', $trip) }}" class="btn btn-sm btn-outline-secondary">← Back</a>
                    <h5 class="mb-0 fw-bold">New Poll — {{ $trip->name }}</h5>
                </div>

                <form action="{{ route('polls.store', $trip) }}" method="POST" id="pollForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            placeholder="e.g. Where should we stay?" value="{{ old('title') }}" required>
                        @error("title") <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror">
                            @foreach(["destination" => "Destination", "accommodation" => "Accommodation", "transport" => "Transport", "activities" => "Activities", "other" => "Other"] as $value => $label)
                                <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error("type") <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Options</label>
                        @error("options") <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                        @error("options.*") <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                        <div id="optionsList">
                            <div class="input-group mb-2">
                                <input type="text" name="options[]" class="form-control" placeholder="Option 1" required>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" name="options[]" class="form-control" placeholder="Option 2" required>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addOption()">+ Add option</button>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-dark">Create Poll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let optionCount = 2;
        function addOption() {
            optionCount++;
            const list = document.getElementById("optionsList");
            const div = document.createElement("div");
            div.className = "input-group mb-2";
            div.innerHTML = `<input type="text" name="options[]" class="form-control" placeholder="Option ${optionCount}">
                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">×</button>`;
            list.appendChild(div);
        }
    </script>
</x-layout>
