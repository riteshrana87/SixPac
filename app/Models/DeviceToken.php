<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;
    
    protected $fillable = ['device_token','user_id','device_type'];
    protected $table = 'device_token';

    public static function getAllUsersDeviceToken($userId){
        //$deviceToken = DeviceToken::where(['user_id' => $userpost->user_id])->orderBy('created_at', 'DESC')->first();
        $deviceTokens = DeviceToken::where('user_id', $userId)->pluck('device_token')->toArray();
        return $deviceTokens;
    }
}
