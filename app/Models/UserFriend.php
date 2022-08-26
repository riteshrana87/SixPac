<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFriend extends Model
{
    use HasFactory;
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status'
    ];


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_friends';
}
