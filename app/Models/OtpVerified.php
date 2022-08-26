<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerified extends Model
{
    use HasFactory;
    //protected $fillable = ['otp','user_id','email','mobile','isEmail','status'];
    protected $fillable = ['otp','mobile','isEmail','status'];
    protected $table = 'otp_verified';


    public static function otpData($id){

        $otp = OtpVerified::where('user_id',$id)->orderBy('created_at', 'DESC')->first();
        $otpData = "";
        if(!empty($otp)){
            $otpData = $otp->otp;
        }
        return $otpData;
    }
}