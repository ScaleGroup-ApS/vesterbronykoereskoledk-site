<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConversationMemberController extends Controller
{
    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorize('update', $conversation);

        abort_if($conversation->type->value === 'direct', 403, 'Cannot add members to a direct conversation.');

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $userId = (int) $request->input('user_id');

        if (! $conversation->users()->where('user_id', $userId)->exists()) {
            $conversation->users()->attach($userId);
        }

        return back()->with('success', 'Bruger tilføjet til samtalen.');
    }

    public function destroy(Conversation $conversation, User $user): RedirectResponse
    {
        $this->authorize('update', $conversation);

        abort_if($conversation->type->value === 'direct', 403, 'Cannot remove members from a direct conversation.');

        $conversation->users()->detach($user->id);

        return back()->with('success', 'Bruger fjernet fra samtalen.');
    }
}
