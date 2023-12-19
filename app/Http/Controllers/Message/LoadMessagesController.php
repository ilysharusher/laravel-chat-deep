<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\LoadMessageRequest;
use App\Http\Resources\Message\MessageResource;
use App\Models\Chat;

class LoadMessagesController extends Controller
{
    public function __invoke(LoadMessageRequest $request, Chat $chat = null): array
    {
        $requestValidated = $request->validated();
        $chat = $chat ?? Chat::query()->findOrFail($requestValidated['chat_id']);

        return MessageResource::collection(
            $chat->messages()
                ->with('user')
                ->latest()
                ->paginate(5, '*', 'page', $requestValidated['page'] ?? 1)
        )->resolve();
    }
}
