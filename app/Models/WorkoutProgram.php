<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class WorkoutProgram extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name','plan_day','goal_id','sport_id','poster_image','video_name',
        'video_url','overview','created_by','status'
    ];
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workout_programs';

    /**
     * workoutType is used to get plan days
     *
     * @return \Illuminate\Http\Eloquent\belongsTo
     * @author Spec Developer
     */
    public function getPlanDay() {
        return $this->belongsTo(PlanDay::class, 'plan_day', 'id');
    }

    /**
     * user is used to get user data
     *
     * @return \Illuminate\Http\Eloquent\belongsTo
     * @author Spec Developer
     */
    public function user() {
        return $this->belongsTo(User::class,'created_by','id');
    }

    /**
     * getCreatedAtAttribute is used to convert UTC date time to Users local date time
     *
     * @param  date $dateWithTime date time that you want to convert
     * @return date user local date time
     * @author Spec Developer
     */
    public function getCreatedAtAttribute($dateWithTime) {
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
