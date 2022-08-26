<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseData extends Model
{
    use HasFactory;
    protected $fillable = ['id','activity','specific_motion','mets'];
    protected $table = 'exercise_data';
}
