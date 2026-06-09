<x-layout :fullBg="true">
    <x-slot name="title">{{ $trip->name }} — Trip Planner</x-slot>
    <style>:root { --bg-image: url('{{ asset("images/image9.jpg") }}'); }</style>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-white fw-bold mb-1">{{ $trip->name }}</h2>
                @if($trip->description)
                    <p class="text-white-50 mb-0">{{ $trip->description }}</p>
                @endif
            </div>
            <a href="{{ route('trips.index') }}" class="btn btn-light btn-sm">← Back</a>
        </div>

        <div class="row g-4">
            {{-- Members --}}
            <div class="col-md-4">
                <div class="card h-100" style="background: rgba(255,255,255,0.92);">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Members</h6>
                        <ul class="list-unstyled mb-0">
                            @foreach($trip->members as $member)
                                <li class="d-flex align-items-center mb-2">
                                    <span class="badge bg-secondary me-2" style="font-size: 0.75rem;">
                                        {{ $member->id === $trip->owner_id ? 'Owner' : 'Member' }}
                                    </span>
                                    {{ $member->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @can("delete", $trip)
                        <div class="card-footer bg-transparent border-0 pb-3">
                            <p class="text-muted small mb-1">Invite code:</p>
                            <code class="fs-6">{{ $trip->invite_code }}</code>
                        </div>
                    @endcan
                </div>
            </div>

            {{-- Tasks --}}
            <div class="col-md-4">
                <div class="card h-100" style="background: rgba(255,255,255,0.92);">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Tasks</h6>
                        @forelse($trip->tasks as $task)
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge {{ $task->status === 'done' ? 'bg-success' : 'bg-warning text-dark' }} me-2">{{ $task->status }}</span>
                                <span class="small">{{ $task->title }}</span>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No tasks yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Expenses --}}
            <div class="col-md-4">
                <div class="card h-100" style="background: rgba(255,255,255,0.92);">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Expenses</h6>
                        @forelse($trip->expenses as $expense)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small">{{ $expense->title }}</span>
                                <span class="small fw-semibold">€{{ number_format($expense->amount, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No expenses yet.</p>
                        @endforelse
                        @if($trip->expenses->isNotEmpty())
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="small fw-semibold">Total</span>
                                <span class="small fw-bold">€{{ number_format($trip->expenses->sum('amount'), 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Checkpoints --}}
            @if($trip->checkpoints->isNotEmpty())
                <div class="col-12">
                    <div class="card" style="background: rgba(255,255,255,0.92);">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Route Checkpoints</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($trip->checkpoints as $checkpoint)
                                    <span class="badge bg-dark py-2 px-3">
                                        {{ $checkpoint->title }}
                                        @if($checkpoint->date)
                                            <span class="text-white-50 ms-1">{{ \Carbon\Carbon::parse($checkpoint->date)->format('d M') }}</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
