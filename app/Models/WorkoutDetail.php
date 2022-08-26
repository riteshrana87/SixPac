<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutDetail extends Model
{
    use HasFactory;
    protected $fillable = ['getfit_id','categories_id','designated_id','body_parts_id','workouts_type_id','name','description','duration','location','fitness_level','age_group','gender','program_duration','status','is_public','Workout_flag','created_by','updated_by'];
    protected $table = 'workout_details';

    public function comments(){
        return $this->morphMany(WorkoutMedia::class, 'workout_mediable');
    }

}
