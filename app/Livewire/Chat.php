<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class Chat extends Component
{
    public ?int $activeConversationId = null;

    public string $newMessage = '';

    public function mount(): void
    {
        $first = $this->getConversationsProperty()->first();
        if ($first) {
            $this->activeConversationId = $first->id;
        }
    }

    public function getConversationsProperty(): Collection
    {
        return auth()->user()->conversations()
            ->with(['lastMessage.user', 'users'])
            ->latest()
            ->get();
    }

    public function getActiveConversationProperty(): ?Conversation
    {
        if (! $this->activeConversationId) {
            return null;
        }

        return $this->conversations->firstWhere('id', $this->activeConversationId);
    }

    public function getMessagesProperty(): Collection
    {
        if (! $this->activeConversationId) {
            return collect();
        }

        return Message::where('conversation_id', $this->activeConversationId)
            ->with('user')
            ->oldest()
            ->get();
    }

    public function selectConversation(int $id): void
    {
        $this->activeConversationId = $id;
    }

    public function sendMessage(): void
    {
        $this->validate(['newMessage' => 'required|string|max:5000']);

        Message::create([
            'conversation_id' => $this->activeConversationId,
            'user_id' => auth()->id(),
            'body' => $this->newMessage,
        ]);

        $this->newMessage = '';
    }

    public function render(): View
    {
        return view('livewire.chat', [
            'conversations' => $this->conversations,
            'messages' => $this->messages,
            'activeConversation' => $this->activeConversation,
        ]);
    }
}
