<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'users',
    ];

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function unreadMessageStatuses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MessageStatus::class)
            ->where('user_id', auth()->id())
            ->where('is_read', false);
    }

    public function lastMessage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Message::class)
            ->latestOfMany()
            ->with('user');
    }

    public function chatWith(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            ChatUser::class,
            'chat_id',
            'id',
            'id',
            'user_id'
        )->where('user_id', '!=', auth()->id());
    }

    public function paginateMessages($page): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->messages()
            ->with('user')
            ->latest()
            ->paginate(5, '*', 'page', $page ?? 1);
    }

    public function readMessages(): void
    {
        $this->unreadMessageStatuses()
            ->update([
                'is_read' => true,
            ]);
    }
}
