<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $users = User::query()->where('id', '!=', auth()->id())->get();

        return inertia('Chat/Index', [
            'users' => UserResource::collection($users)->resolve(),
        ]);
    }
}
