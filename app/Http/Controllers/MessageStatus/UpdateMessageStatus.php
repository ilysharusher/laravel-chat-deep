<?php

namespace App\Http\Controllers\MessageStatus;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageStatus\UpdateMessageStatusRequest;
use App\Models\MessageStatus;

class UpdateMessageStatus extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateMessageStatusRequest $request): void
    {
        $validatedData = $request->validated();

        MessageStatus::query()
            ->where('user_id', $validatedData['user_id'])
            ->where('message_id', $validatedData['message_id'])
            ->update([
                'is_read' => true,
            ]);
    }
}
