<div class="flex flex-col gap-4">
    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- List view --}}
    @if ($view === 'list')
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Support-tickets</h3>
            <button
                wire:click="showCreateForm"
                class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-primary-500"
            >
                <x-heroicon-m-plus class="h-4 w-4" />
                Opret ticket
            </button>
        </div>

        @forelse ($tickets as $ticket)
            <button
                wire:click="viewTicket({{ $ticket['id'] }})"
                class="flex w-full items-start gap-4 rounded-xl border bg-white p-4 text-left shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800"
            >
                @php
                    $priorityColors = [
                        'low' => 'bg-gray-100 text-gray-600',
                        'normal' => 'bg-blue-100 text-blue-700',
                        'high' => 'bg-orange-100 text-orange-700',
                        'urgent' => 'bg-red-100 text-red-700',
                    ];
                    $statusColors = [
                        'open' => 'bg-green-100 text-green-700',
                        'waiting' => 'bg-amber-100 text-amber-700',
                        'solved' => 'bg-gray-100 text-gray-600',
                        'closed' => 'bg-gray-200 text-gray-600',
                        'pending' => 'bg-yellow-100 text-yellow-700',
                    ];
                    $priorityLabels = [
                        'low' => 'Lav',
                        'normal' => 'Normal',
                        'high' => 'Høj',
                        'urgent' => 'Haster',
                    ];
                    $statusLabels = [
                        'open' => 'Åben',
                        'waiting' => 'Venter',
                        'solved' => 'Løst',
                        'closed' => 'Lukket',
                        'pending' => 'Afventer',
                    ];
                @endphp

                <div class="mt-0.5 flex shrink-0 gap-2">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $priorityColors[$ticket['priority'] ?? 'normal'] ?? $priorityColors['normal'] }}">
                        {{ $priorityLabels[$ticket['priority'] ?? 'normal'] ?? $ticket['priority'] ?? 'Normal' }}
                    </span>
                    @if (!empty($ticket['status']))
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColors[$ticket['status']] ?? $statusColors['open'] }}">
                            {{ $statusLabels[$ticket['status']] ?? $ticket['status'] }}
                        </span>
                    @endif
                </div>

                <div class="flex min-w-0 flex-1 flex-col gap-0.5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $ticket['subject'] ?? 'Ingen emne' }}
                        </span>
                        @if (!empty($ticket['createdAt'] ?? $ticket['created_at'] ?? null))
                            <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">
                                {{ \Carbon\Carbon::parse($ticket['createdAt'] ?? $ticket['created_at'])->format('d.m.Y H:i') }}
                            </span>
                        @endif
                    </div>
                </div>
            </button>
        @empty
            <div class="py-12 text-center text-sm text-gray-500">
                Ingen support-tickets fundet.
            </div>
        @endforelse

    {{-- Create view --}}
    @elseif ($view === 'create')
        <div class="flex items-center gap-2">
            <button
                wire:click="showList"
                class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                <x-heroicon-m-arrow-left class="h-4 w-4" />
                Tilbage
            </button>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Opret ticket</h3>
        </div>

        <form wire:submit="createTicket" class="flex flex-col gap-4 rounded-xl border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div>
                <label for="subject" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Emne</label>
                <input
                    wire:model="subject"
                    type="text"
                    id="subject"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                    placeholder="Beskriv dit problem kort..."
                />
                @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="priority" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Prioritet</label>
                <select
                    wire:model="priority"
                    id="priority"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                >
                    <option value="low">Lav</option>
                    <option value="normal">Normal</option>
                    <option value="high">Høj</option>
                    <option value="urgent">Urgent</option>
                </select>
                @error('priority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="message" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Besked</label>
                <textarea
                    wire:model="message"
                    id="message"
                    rows="5"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                    placeholder="Beskriv dit problem i detaljer..."
                ></textarea>
                @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-primary-500 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="createTicket">Opret ticket</span>
                    <span wire:loading wire:target="createTicket">Opretter...</span>
                </button>
            </div>
        </form>

    {{-- Show view --}}
    @elseif ($view === 'show')
        <div class="flex items-center gap-2">
            <button
                wire:click="showList"
                class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                <x-heroicon-m-arrow-left class="h-4 w-4" />
                Tilbage
            </button>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ $activeTicket['subject'] ?? 'Ticket' }}
            </h3>
        </div>

        <div class="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            @php
                $allComments = collect($activeTicket['threads'] ?? [])
                    ->flatMap(fn ($thread) => $thread['comments'] ?? [])
                    ->values();
            @endphp

            @if ($allComments->isNotEmpty())
                <div class="flex flex-col gap-3">
                    @foreach ($allComments as $comment)
                        @php
                            $isStaff = ($comment['authorType'] ?? '') === 'staff';
                            $isSystem = ($comment['authorType'] ?? '') === 'system';
                        @endphp

                        @if ($isSystem)
                            <div class="flex justify-center">
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                    {{ $comment['content'] ?? $comment['message'] ?? '' }}
                                </span>
                            </div>
                        @else
                            <div class="flex {{ $isStaff ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[80%] rounded-lg px-4 py-3 text-sm {{ $isStaff ? 'bg-blue-600 text-white' : 'border bg-white text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200' }}">
                                    <p class="whitespace-pre-wrap">{{ $comment['content'] ?? $comment['message'] ?? '' }}</p>
                                    @if (!empty($comment['createdAt'] ?? $comment['created_at'] ?? null))
                                        <p class="mt-1 text-xs {{ $isStaff ? 'text-blue-200' : 'text-gray-400 dark:text-gray-500' }}">
                                            {{ \Carbon\Carbon::parse($comment['createdAt'] ?? $comment['created_at'])->format('d. M H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Ingen kommentarer endnu.</p>
            @endif
        </div>

        <form wire:submit="addComment" class="flex flex-col gap-3 rounded-xl border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <label for="commentMessage" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tilføj svar</label>
            <textarea
                wire:model="commentMessage"
                id="commentMessage"
                rows="3"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                placeholder="Skriv dit svar..."
            ></textarea>
            @error('commentMessage') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

            <div class="flex justify-end">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-primary-500 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="addComment">Send svar</span>
                    <span wire:loading wire:target="addComment">Sender...</span>
                </button>
            </div>
        </form>
    @endif
</div>
