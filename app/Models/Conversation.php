<?php

namespace App\Models;

use App\Models\User;
use App\Models\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable =[
        'sender_id',
        'receiver_id',
        'last_time_message',
    ];

    // relationships
    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
