<div class="flex h-[calc(100vh-12rem)] overflow-hidden rounded-xl border bg-white dark:bg-gray-900 dark:border-gray-700">

    {{-- Left: conversation list --}}
    <aside class="flex w-1/4 shrink-0 flex-col overflow-hidden border-r dark:border-gray-700">
        <div class="border-b px-4 py-3 dark:border-gray-700">
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Samtaler</span>
        </div>

        <div class="flex-1 overflow-y-auto">
            @forelse ($conversations as $conversation)
                @php
                    $isActive = $conversation->id === $activeConversationId;
                    $lastMessage = $conversation->lastMessage;
                    $otherUsers = $conversation->users->where('id', '!=', auth()->id());
                    $displayName = $conversation->name
                        ?? $otherUsers->pluck('name')->implode(', ')
                        ?: 'Ukendt';
                @endphp
                <button
                    wire:click="selectConversation({{ $conversation->id }})"
                    class="w-full px-4 py-3 text-left transition hover:bg-gray-50 dark:hover:bg-gray-800 {{ $isActive ? 'bg-primary-50 border-l-2 border-primary-600 dark:bg-primary-900/20' : '' }}"
                >
                    <div class="flex items-center justify-between gap-2">
                        <span class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $displayName }}
                        </span>
                        @if ($lastMessage)
                            <span class="shrink-0 text-xs text-gray-400">
                                {{ $lastMessage->created_at->format('H:i') }}
                            </span>
                        @endif
                    </div>
                    @if ($lastMessage)
                        <p class="mt-0.5 truncate text-xs text-gray-500 dark:text-gray-400">
                            {{ $lastMessage->user?->name }}: {{ $lastMessage->body }}
                        </p>
                    @endif
                </button>
            @empty
                <p class="px-4 py-6 text-center text-xs text-gray-400">Ingen samtaler endnu.</p>
            @endforelse
        </div>
    </aside>

    {{-- Right: message thread --}}
    <div class="flex flex-1 flex-col overflow-hidden">
        @if ($activeConversation)
            {{-- Header --}}
            <div class="border-b px-4 py-3 dark:border-gray-700">
                @php
                    $otherUsers = $activeConversation->users->where('id', '!=', auth()->id());
                    $headerName = $activeConversation->name
                        ?? $otherUsers->pluck('name')->implode(', ')
                        ?: 'Ukendt';
                @endphp
                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $headerName }}</span>
                @if ($activeConversation->type?->value === 'group')
                    <span class="ml-2 text-xs text-gray-400">{{ $activeConversation->users->count() }} medlemmer</span>
                @endif
            </div>

            {{-- Messages --}}
            <div
                class="flex flex-1 flex-col gap-2 overflow-y-auto p-4"
                wire:poll.5s
            >
                @forelse ($messages as $message)
                    @php $isMine = $message->user_id === auth()->id(); @endphp
                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%]">
                            @if (! $isMine)
                                <span class="mb-1 block text-xs text-gray-400">{{ $message->user?->name }}</span>
                            @endif
                            <div class="rounded-2xl px-4 py-2 text-sm {{ $isMine ? 'rounded-tr-sm bg-primary-600 text-white' : 'rounded-tl-sm bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100' }}">
                                {{ $message->body }}
                            </div>
                            <span class="mt-0.5 block text-right text-xs text-gray-400">
                                {{ $message->created_at->format('H:i') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-1 items-center justify-center text-sm text-gray-400">
                        Ingen beskeder endnu. Skriv den første!
                    </div>
                @endforelse
            </div>

            {{-- Input --}}
            <div class="border-t px-4 py-3 dark:border-gray-700">
                <form wire:submit="sendMessage" class="flex items-center gap-2">
                    <input
                        wire:model="newMessage"
                        type="text"
                        placeholder="Skriv en besked…"
                        autocomplete="off"
                        class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                    />
                    <button
                        type="submit"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-primary-700 disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        Send
                    </button>
                </form>
            </div>
        @else
            <div class="flex flex-1 items-center justify-center text-sm text-gray-400">
                Vælg en samtale for at starte.
            </div>
        @endif
    </div>
</div>
