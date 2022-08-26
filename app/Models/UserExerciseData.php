<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExerciseData extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','exercise_id','calory','time_spend','notes','description','met','date_and_time'];
    protected $table = 'user_exercise_data';
}
