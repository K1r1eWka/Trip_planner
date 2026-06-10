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
            <div class="col-md-4 align-self-start">
                <div class="card" style="background: rgba(255,255,255,0.92);">
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
            <div class="col-md-4 align-self-start">
                <div class="card" style="background: rgba(255,255,255,0.92);">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Tasks</h6>
                        <form action="{{ route('tasks.store', $trip) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="d-flex gap-2 mb-2">
                                <input type="text" name="title" class="form-control form-control-sm @error('title') is-invalid @enderror" placeholder="Task title..." required>
                                <button type="submit" class="btn btn-dark btn-sm px-3">Add</button>
                            </div>
                            <input type="text" name="description" class="form-control form-control-sm" placeholder="Description (optional)">
                            @error("title") <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </form>
                        @forelse($trip->tasks as $task)
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $task->status === 'done' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $task->status }}</span>
                                    <div>
                                        <span class="small">{{ $task->title }}</span>
                                        @if($task->description)
                                            <div class="text-muted" style="font-size: 0.78rem;">{{ $task->description }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <form action="{{ route('tasks.update', [$trip, $task]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $task->status === 'done' ? 'pending' : 'done' }}">
                                        <button type="submit" class="btn btn-sm {{ $task->status === 'done' ? 'btn-outline-warning' : 'btn-outline-success' }} py-0">
                                            {{ $task->status === 'done' ? '↩' : '✓' }}
                                        </button>
                                    </form>
                                    @if(Auth::id() === $task->user_id || Auth::user()->cannot('manage', $trip) === false)
                                        <form action="{{ route('tasks.destroy', [$trip, $task]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0">×</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No tasks yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Expenses --}}
            <div class="col-md-4 align-self-start">
                <div class="card" style="background: rgba(255,255,255,0.92);">
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

            {{-- Polls --}}
            <div class="col-12">
                <div class="card" style="background: rgba(255,255,255,0.92);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-semibold mb-0">Polls</h6>
                            <a href="{{ route('polls.create', $trip) }}" class="btn btn-dark btn-sm">+ New Poll</a>
                        </div>

                        @forelse($trip->polls()->with('options.votes')->get() as $poll)
                            @php
                                $totalVotes = $poll->options->sum(fn($o) => $o->votes->count());
                                $userVotedOptionId = $poll->options
                                    ->flatMap(fn($o) => $o->votes)
                                    ->where('user_id', Auth::id())
                                    ->first()?->poll_option_id;
                            @endphp
                            <div class="mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="fw-semibold">{{ $poll->title }}</span>
                                        <span class="badge bg-secondary ms-2" style="font-size: 0.7rem;">{{ ucfirst($poll->type) }}</span>
                                        @if($poll->is_closed)
                                            <span class="badge bg-danger ms-1" style="font-size: 0.7rem;">Closed</span>
                                        @endif
                                    </div>
                                    @can("manage", $trip)
                                        <div class="d-flex gap-1">
                                            @if(!$poll->is_closed)
                                                <form action="{{ route('polls.close', [$trip, $poll]) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary py-0" style="font-size: 0.75rem;">Close</button>
                                                </form>
                                            @endif
                                            <form action="{{ route('polls.destroy', [$trip, $poll]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0">×</button>
                                            </form>
                                        </div>
                                    @endcan
                                </div>

                                @if(!$poll->is_closed && !$userVotedOptionId)
                                    <form action="{{ route('polls.vote', [$trip, $poll]) }}" method="POST">
                                        @csrf
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            @foreach($poll->options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="poll_option_id"
                                                        id="option_{{ $option->id }}" value="{{ $option->id }}" required>
                                                    <label class="form-check-label small" for="option_{{ $option->id }}">
                                                        {{ $option->title }}
                                                        <span class="text-muted">({{ $option->votes->count() }})</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-dark">Vote</button>
                                    </form>
                                @else
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($poll->options as $option)
                                            @php
                                                $count = $option->votes->count();
                                                $percent = $totalVotes > 0 ? round($count / $totalVotes * 100) : 0;
                                                $isMyVote = $userVotedOptionId === $option->id;
                                            @endphp
                                            <div>
                                                <div class="d-flex justify-content-between small mb-1">
                                                    <span class="{{ $isMyVote ? 'fw-semibold' : '' }}">
                                                        {{ $option->title }}
                                                        @if($isMyVote) <span class="text-success">✓</span> @endif
                                                    </span>
                                                    <span class="text-muted">{{ $count }} vote{{ $count !== 1 ? 's' : '' }}</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar {{ $isMyVote ? 'bg-success' : 'bg-dark' }}"
                                                        style="width: {{ $percent }}%"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($userVotedOptionId)
                                        <p class="text-muted small mt-2 mb-0">You voted in this poll.</p>
                                    @endif
                                @endif
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No polls yet.</p>
                        @endforelse
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