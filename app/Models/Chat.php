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
}
