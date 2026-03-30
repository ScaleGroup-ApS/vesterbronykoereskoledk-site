<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageAttachmentController extends Controller
{
    public function show(Conversation $conversation, Message $message, Media $media): StreamedResponse
    {
        $this->authorize('view', $conversation);

        abort_unless($message->conversation_id === $conversation->id, 404);
        abort_unless($media->model_id === $message->id && $media->model_type === Message::class, 404);

        return $media->toInlineResponse($media->file_name);
    }
}
