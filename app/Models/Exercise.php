<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','workout_type_id','duration_id',
        'poster_image','video_name','video_thumb',
        'overview','gender','location',
        'created_by','status'
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exercises';

    /**
     * exerciseParams is used to get exercise data
     *
     * @return \Illuminate\Http\Eloquent\belongsTo
     * @author Spec Developer
     */
    public function interests()
    {
        return $this->morphedByMany(Interests::class, 'exercisable');
    }

    public function equipments()
    {
        return $this->morphedByMany(Equipment::class, 'exercisable');
    }

    public function bodyParts()
    {
        return $this->morphedByMany(BodyParts::class, 'exercisable');
    }

    public function ageGroups()
    {
        return $this->morphedByMany(AgeGroup::class, 'exercisable');
    }

    public function fitnessLevels()
    {
        return $this->morphedByMany(FitnessLevel::class, 'exercisable');
    }

    /**
     * workoutType is used to get workout type
     *
     * @return \Illuminate\Http\Eloquent\belongsTo
     * @author Spec Developer
     */
    public function workoutType() {
        return $this->belongsTo(WorkoutType::class);
    }

    /**
     * duration is used to get exercise duration
     *
     * @return \Illuminate\Http\Eloquent\belongsTo
     * @author Spec Developer
     */
    public function duration() {
        return $this->belongsTo(ExerciseDuration::class,'duration_id','id');
    }

    /**
     * workoutType is used to get workout type
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
