<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class PostGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'media_order',
        'file_name',
        'file_type',
        'thumb_name',
        'file_length',
        'file_size',
        'is_transacoded',
        'status',
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'post_galleries';

    /*
     * Create function for convert UTC date time to Users local date time     *
     *
    */

    public function getCreatedAtAttribute($dateWithTime){
        $dateTime = strtotime($dateWithTime);
        $utc = date("Y-m-d\TH:i:s.000\Z", $dateTime);
        $time = strtotime($utc);

        $getTimeZone = Session::get('user_timezone');
        if($getTimeZone != "UTC" && !empty($getTimeZone)){
            date_default_timezone_set($getTimeZone);
            $strtotime =  date('Y-m-d H:i:s', $time);
        }
        else
        {
            $strtotime = date("Y-m-d H:i:s", $time);
        }
        return date('Y-m-d H:i:s',strtotime($strtotime));
    }
}