<div class="space-y-4">

    {{-- Header + mark all --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            @if($unreadCount > 0)
                {{ $unreadCount }} ulæste
            @else
                Alle notifikationer er læst
            @endif
        </p>
        @if($unreadCount > 0)
            <button wire:click="markAllRead"
                    class="text-sm font-medium text-primary transition hover:underline">
                Markér alle som læst
            </button>
        @endif
    </div>

    @if(empty($notifications))
        <p class="rounded-xl border border-dashed p-8 text-center text-sm text-gray-400">
            Du har ingen notifikationer endnu.
        </p>
    @else
        <div class="divide-y rounded-xl border">
            @foreach($notifications as $notification)
                <div class="flex items-start gap-3 px-4 py-3 {{ $notification['is_read'] ? '' : 'bg-primary/5' }}">
                    {{-- Unread dot --}}
                    <div class="mt-1.5 size-2 shrink-0 rounded-full {{ $notification['is_read'] ? 'bg-transparent' : 'bg-primary' }}"></div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium">{{ $notification['type_label'] }}</p>
                        @if(! empty($notification['data']['message']))
                            <p class="mt-0.5 text-sm text-gray-500">{{ $notification['data']['message'] }}</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-400">{{ $notification['created_at'] }}</p>
                    </div>

                    @if(! $notification['is_read'])
                        <button wire:click="markRead('{{ $notification['id'] }}')"
                                class="shrink-0 text-xs text-gray-400 transition hover:text-primary">
                            Markér læst
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>
