<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercisables extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id','exercisable_type','exercisable_id'
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exercisables';
}
