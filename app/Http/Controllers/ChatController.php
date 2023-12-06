<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreRequest;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\User\UserResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    private function getUserIdsAsString(array $user_ids): string
    {
        sort($user_ids);

        return implode('-', $user_ids);
    }

    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $users = User::query()->where('id', '!=', auth()->id())->get();

        return inertia('Chat/Index', [
            'users' => UserResource::collection($users)->resolve(),
        ]);
    }

    public function store(StoreRequest $request): \Inertia\Response|\Inertia\ResponseFactory
    {
        $user_ids = array_merge($request->validated()['users'], (array)auth()->id());

        try {
            DB::beginTransaction();

            $chat = Chat::query()->firstOrCreate([
                'users' => $this->getUserIdsAsString($user_ids),
            ], [
                'title' => $request->validated()['title'] ?? null,
            ]);

            $chat->users()->sync($user_ids);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
        }

        return inertia('Chat/Show', [
            'chat' => ChatResource::make($chat)->resolve(),
        ]);
    }
}
