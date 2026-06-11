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

            {{-- Col 1: Tasks --}}
            <div class="col-md-4 align-self-start">
                <div class="card" style="background: rgba(255,255,255,0.92);">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Tasks</h6>
                        <div class="mb-3">
                            <div class="d-flex gap-2 mb-2">
                                <input type="text" id="task-title-input" class="form-control form-control-sm" placeholder="Task title..." required>
                                <button class="btn btn-dark btn-sm px-3" onclick="submitTask()">Add</button>
                            </div>
                            <input type="text" id="task-desc-input" class="form-control form-control-sm" placeholder="Description (optional)">
                            <div id="task-error" class="text-danger small mt-1" style="display:none;"></div>
                        </div>
                        <div id="tasks-list">
                        @forelse($trip->tasks as $task)
                            <div class="d-flex align-items-center justify-content-between mb-2" data-task-id="{{ $task->id }}">
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
                                    <button onclick="toggleTask({{ $task->id }}, '{{ $task->status === 'done' ? 'pending' : 'done' }}', '{{ route('tasks.update', [$trip, $task]) }}')"
                                        class="btn btn-sm {{ $task->status === 'done' ? 'btn-outline-warning' : 'btn-outline-success' }} py-0">
                                        {{ $task->status === 'done' ? '↩' : '✓' }}
                                    </button>
                                    @if(Auth::id() === $task->user_id || Auth::user()->cannot('manage', $trip) === false)
                                        <button onclick="deleteTask({{ $task->id }}, '{{ route('tasks.destroy', [$trip, $task]) }}')"
                                            class="btn btn-sm btn-outline-danger py-0">×</button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small mb-0" id="no-tasks-msg">No tasks yet.</p>
                        @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Col 2: Polls --}}
            <div class="col-md-4 align-self-start">
                <div class="card" style="background: rgba(255,255,255,0.92);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-semibold mb-0">Polls</h6>
                            <a href="{{ route('polls.create', $trip) }}" class="btn btn-dark btn-sm">+ New Poll</a>
                        </div>

                        <div id="polls-container">
                        @forelse($trip->polls()->with('options.votes')->get() as $poll)
                            @php
                                $totalVotes = $poll->options->sum(fn($o) => $o->votes->count());
                                $userVotedOptionId = $poll->options
                                    ->flatMap(fn($o) => $o->votes)
                                    ->where('user_id', Auth::id())
                                    ->first()?->poll_option_id;
                            @endphp
                            <div class="mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}" id="poll-{{ $poll->id }}">
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
                                    <div id="vote-form-{{ $poll->id }}">
                                        <div class="d-flex flex-column gap-1 mb-2" id="vote-options-{{ $poll->id }}">
                                            @foreach($poll->options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="poll_option_id"
                                                        id="option_{{ $option->id }}" value="{{ $option->id }}">
                                                    <label class="form-check-label small" for="option_{{ $option->id }}">
                                                        {{ $option->title }}
                                                        <span class="text-muted">({{ $option->votes->count() }})</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button class="btn btn-sm btn-dark"
                                            onclick="submitVote({{ $poll->id }}, '{{ route('polls.vote', [$trip, $poll]) }}')">
                                            Vote
                                        </button>
                                    </div>
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
                                                    <span class="text-muted" id="count-{{ $option->id }}">{{ $count }} vote{{ $count !== 1 ? 's' : '' }}</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar {{ $isMyVote ? 'bg-success' : 'bg-dark' }}"
                                                        id="bar-{{ $option->id }}"
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
                            <p class="text-muted small mb-0 no-polls-msg">No polls yet.</p>
                        @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Col 3: Members + Expenses --}}
            <div class="col-md-4 align-self-start d-flex flex-column gap-4">

                {{-- Members --}}
                <div class="card" style="background: rgba(255,255,255,0.92);">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Members</h6>
                        <ul class="list-unstyled mb-0" id="members-list">
                            @foreach($trip->members->sortByDesc(fn($m) => $m->id === $trip->owner_id) as $member)
                                <li class="d-flex align-items-center mb-2">
                                    @if($member->id === $trip->owner_id)
                                        <span class="badge me-2" style="font-size: 0.75rem; background: #f0b429; color: #000;">Owner</span>
                                    @else
                                        <span class="badge me-2" style="font-size: 0.75rem; background: #3b82f6;">Member</span>
                                    @endif
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

                {{-- Expenses --}}
                <div class="card" style="background: rgba(255,255,255,0.92);">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Expenses</h6>
                        <div class="mb-3">
                            <div class="d-flex gap-2 mb-1">
                                <input type="text" id="expense-title-input" class="form-control form-control-sm" placeholder="Title..." required>
                                <input type="number" id="expense-amount-input" step="0.01" min="0.01" class="form-control form-control-sm" placeholder="€0.00" style="width: 90px;" required>
                                <button class="btn btn-dark btn-sm px-3" onclick="submitExpense()">Add</button>
                            </div>
                            <div id="expense-error" class="text-danger small" style="display:none;"></div>
                        </div>
                        <div id="expenses-list">
                        @forelse($trip->expenses as $expense)
                            <div class="d-flex align-items-center justify-content-between mb-2" data-expense-id="{{ $expense->id }}">
                                <span class="small">{{ $expense->title }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="small fw-semibold">€{{ number_format($expense->amount, 2) }}</span>
                                    @if(Auth::id() === $expense->user_id || Auth::user()->cannot('manage', $trip) === false)
                                        <button onclick="deleteExpense({{ $expense->id }}, '{{ route('expenses.destroy', [$trip, $expense]) }}')"
                                            class="btn btn-sm btn-outline-danger py-0">×</button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small mb-0 no-expenses-msg">No expenses yet.</p>
                        @endforelse
                        </div>
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

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        const CSRF = "{{ csrf_token() }}";
        const TRIP_ID = {{ $trip->id }};
        const CURRENT_USER_ID = {{ Auth::id() }};

        const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
            cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}"
        });

        const channel = pusher.subscribe("trip." + TRIP_ID);

        function apiFetch(url, method, body) {
            return fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": CSRF,
                },
                body: body ? JSON.stringify(body) : undefined,
            }).then(function(res) { return res.json(); });
        }

        // ── Tasks ──────────────────────────────────────────
        function renderTasks(tasks) {
            const list = document.getElementById("tasks-list");
            if (!list) return;
            if (!tasks.length) {
                list.innerHTML = '<p class="text-muted small mb-0">No tasks yet.</p>';
                return;
            }
            list.innerHTML = tasks.map(function(t) {
                const isDone = t.status === "done";
                const canDelete = t.user_id === CURRENT_USER_ID;
                const toggleUrl = "/trips/" + TRIP_ID + "/tasks/" + t.id;
                const deleteUrl = "/trips/" + TRIP_ID + "/tasks/" + t.id;
                return `
                    <div class="d-flex align-items-center justify-content-between mb-2" data-task-id="${t.id}">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge ${isDone ? 'bg-success' : 'bg-warning text-dark'}">${t.status}</span>
                            <div>
                                <span class="small">${t.title}</span>
                                ${t.description ? `<div class="text-muted" style="font-size:0.78rem;">${t.description}</div>` : ''}
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <button onclick="toggleTask(${t.id}, '${isDone ? 'pending' : 'done'}', '${toggleUrl}')"
                                class="btn btn-sm ${isDone ? 'btn-outline-warning' : 'btn-outline-success'} py-0">
                                ${isDone ? '↩' : '✓'}
                            </button>
                            ${canDelete ? `<button onclick="deleteTask(${t.id}, '${deleteUrl}')" class="btn btn-sm btn-outline-danger py-0">×</button>` : ''}
                        </div>
                    </div>`;
            }).join("");
        }

        function submitTask() {
            const title = document.getElementById("task-title-input").value.trim();
            const desc = document.getElementById("task-desc-input").value.trim();
            const err = document.getElementById("task-error");
            if (!title) { err.textContent = "Title is required."; err.style.display = "block"; return; }
            err.style.display = "none";
            apiFetch("/trips/" + TRIP_ID + "/tasks", "POST", { title: title, description: desc })
                .then(function(data) {
                    if (data.tasks) {
                        renderTasks(data.tasks);
                        document.getElementById("task-title-input").value = "";
                        document.getElementById("task-desc-input").value = "";
                    }
                });
        }

        function toggleTask(taskId, newStatus, url) {
            apiFetch(url, "PATCH", { status: newStatus })
                .then(function(data) { if (data.tasks) renderTasks(data.tasks); });
        }

        function deleteTask(taskId, url) {
            apiFetch(url, "DELETE")
                .then(function(data) { if (data.tasks) renderTasks(data.tasks); });
        }

        channel.bind("task.updated", function(data) {
            renderTasks(data.tasks);
        });

        // ── Expenses ───────────────────────────────────────
        function renderExpenses(expenses, total) {
            const list = document.getElementById("expenses-list");
            if (!list) return;
            let html = "";
            if (!expenses.length) {
                html = '<p class="text-muted small mb-0 no-expenses-msg">No expenses yet.</p>';
            } else {
                html = expenses.map(function(e) {
                    const canDelete = e.user_id === CURRENT_USER_ID;
                    const deleteUrl = "/trips/" + TRIP_ID + "/expenses/" + e.id;
                    return `
                        <div class="d-flex align-items-center justify-content-between mb-2" data-expense-id="${e.id}">
                            <span class="small">${e.title}</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small fw-semibold">€${parseFloat(e.amount).toFixed(2)}</span>
                                ${canDelete ? `<button onclick="deleteExpense(${e.id}, '${deleteUrl}')" class="btn btn-sm btn-outline-danger py-0">×</button>` : ''}
                            </div>
                        </div>`;
                }).join("") + `
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="small fw-semibold">Total</span>
                        <span class="small fw-bold">€${parseFloat(total).toFixed(2)}</span>
                    </div>`;
            }
            list.innerHTML = html;
        }

        function submitExpense() {
            const title = document.getElementById("expense-title-input").value.trim();
            const amount = document.getElementById("expense-amount-input").value;
            const err = document.getElementById("expense-error");
            if (!title || !amount) { err.textContent = "Title and amount are required."; err.style.display = "block"; return; }
            err.style.display = "none";
            apiFetch("/trips/" + TRIP_ID + "/expenses", "POST", { title: title, amount: amount })
                .then(function(data) {
                    if (data.expenses !== undefined) {
                        renderExpenses(data.expenses, data.total);
                        document.getElementById("expense-title-input").value = "";
                        document.getElementById("expense-amount-input").value = "";
                    }
                });
        }

        function deleteExpense(expenseId, url) {
            apiFetch(url, "DELETE")
                .then(function(data) { if (data.expenses !== undefined) renderExpenses(data.expenses, data.total); });
        }

        channel.bind("expense.updated", function(data) {
            renderExpenses(data.expenses, data.total);
        });

        // ── Members ────────────────────────────────────────
        channel.bind("member.joined", function(data) {
            const list = document.getElementById("members-list");
            if (!list) return;
            const li = document.createElement("li");
            li.className = "d-flex align-items-center mb-2";
            li.innerHTML = `<span class="badge me-2" style="font-size:0.75rem; background:#3b82f6;">Member</span>${data.name}`;
            list.appendChild(li);
        });

        // ── Polls ──────────────────────────────────────────
        function renderPollResults(pollId, options, votedOptionId) {
            const form = document.getElementById("vote-form-" + pollId);
            if (form) {
                let html = '<div class="d-flex flex-column gap-1">';
                options.forEach(function(option) {
                    const isMyVote = option.id == votedOptionId;
                    html += `
                        <div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="${isMyVote ? 'fw-semibold' : ''}">
                                    ${option.title}
                                    ${isMyVote ? '<span class="text-success">✓</span>' : ''}
                                </span>
                                <span class="text-muted" id="count-${option.id}">${option.votes} vote${option.votes !== 1 ? 's' : ''}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar ${isMyVote ? 'bg-success' : 'bg-dark'}"
                                    id="bar-${option.id}"
                                    style="width: ${option.percent}%"></div>
                            </div>
                        </div>`;
                });
                html += '</div><p class="text-muted small mt-2 mb-0">You voted in this poll.</p>';
                form.outerHTML = html;
            } else {
                options.forEach(function(option) {
                    const bar = document.getElementById("bar-" + option.id);
                    if (bar) bar.style.width = option.percent + "%";
                    const countEl = document.getElementById("count-" + option.id);
                    if (countEl) countEl.textContent = option.votes + " vote" + (option.votes !== 1 ? "s" : "");
                });
            }
        }

        function submitVote(pollId, url) {
            const selected = document.querySelector("#vote-options-" + pollId + " input[type=radio]:checked");
            if (!selected) {
                alert("Please select an option.");
                return;
            }

            const token = document.querySelector("meta[name='csrf-token']")?.content
                || "{{ csrf_token() }}";

            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": token,
                },
                body: JSON.stringify({ poll_option_id: selected.value }),
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.options) {
                    renderPollResults(pollId, data.options, data.voted_option_id);
                }
            });
        }

        channel.bind("vote.cast", function(data) {
            const form = document.getElementById("vote-form-" + data.poll_id);
            if (form) {
                // этот пользователь ещё не голосовал — просто обновляем счётчики в форме
                data.options.forEach(function(option) {
                    const label = document.querySelector("label[for='option_" + option.id + "'] .text-muted");
                    if (label) label.textContent = "(" + option.votes + ")";
                });
            } else {
                // этот пользователь уже голосовал — обновляем прогресс-бары
                renderPollResults(data.poll_id, data.options, null);
            }
        });

        channel.bind("poll.created", function(data) {
            const pollsContainer = document.getElementById("polls-container");
            if (!pollsContainer) return;

            const noPolls = pollsContainer.querySelector(".no-polls-msg");
            if (noPolls) noPolls.remove();

            let optionsHtml = "";
            data.options.forEach(function(option) {
                optionsHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="poll_option_id"
                            id="option_${option.id}" value="${option.id}">
                        <label class="form-check-label small" for="option_${option.id}">
                            ${option.title}
                            <span class="text-muted">(0)</span>
                        </label>
                    </div>`;
            });

            const voteUrl = "/trips/{{ $trip->id }}/polls/" + data.poll_id + "/vote";

            const div = document.createElement("div");
            div.className = "mb-4 pb-3 border-bottom";
            div.id = "poll-" + data.poll_id;
            div.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="fw-semibold">${data.title}</span>
                        <span class="badge bg-secondary ms-2" style="font-size: 0.7rem;">${data.type.charAt(0).toUpperCase() + data.type.slice(1)}</span>
                    </div>
                </div>
                <div id="vote-form-${data.poll_id}">
                    <div class="d-flex flex-column gap-1 mb-2" id="vote-options-${data.poll_id}">
                        ${optionsHtml}
                    </div>
                    <button class="btn btn-sm btn-dark" onclick="submitVote(${data.poll_id}, '${voteUrl}')">Vote</button>
                </div>`;

            pollsContainer.appendChild(div);
        });

    </script>
</x-layout>
