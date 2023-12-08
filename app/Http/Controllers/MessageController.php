<?php

namespace App\Http\Controllers;

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

        try {
            DB::beginTransaction();

            $message = Message::query()->create([
                'chat_id' => $validatedData['chat_id'],
                'user_id' => $request->user()->id,
                'text' => $validatedData['text'],
            ]);

            foreach ($validatedData['interlocutors'] as $interlocutorId) {
                MessageStatus::query()->create([
                    'chat_id' => $validatedData['chat_id'],
                    'message_id' => $message->id,
                    'user_id' => $interlocutorId
                ]);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }

        return MessageResource::make($message)->resolve();
    }
}
