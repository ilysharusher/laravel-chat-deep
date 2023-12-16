<?php

namespace App\Http\Controllers;

use App\Events\StoreMessageEvent;
use App\Events\StoreMessageStatusEvent;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\Message\MessageResource;
use App\Models\Message;
use App\Models\MessageStatus;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function store(StoreRequest $request): \Illuminate\Http\JsonResponse|array
    {
        $validatedData = $request->validated();
        $chatId = $validatedData['chat_id'];

        try {
            DB::beginTransaction();

            $message = Message::query()->create([
                'chat_id' => $chatId,
                'user_id' => $request->user()->id,
                'text' => $validatedData['text'],
            ]);

            foreach ($validatedData['interlocutors'] as $interlocutorId) {
                MessageStatus::query()->create([
                    'chat_id' => $chatId,
                    'message_id' => $message->id,
                    'user_id' => $interlocutorId,
                ]);

                $count = MessageStatus::query()
                    ->where('chat_id', $chatId)
                    ->where('user_id', $interlocutorId)
                    ->where('is_read', false)
                    ->count();

                broadcast(new StoreMessageStatusEvent($chatId, $count, $interlocutorId, $message));
            }

            broadcast(new StoreMessageEvent($message))->toOthers();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return MessageResource::make($message)->resolve();
    }
}
