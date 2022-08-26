<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseDuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration',
        'created_by',
        'status'
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exercise_durations';
}
