<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FitnessLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by',
        'status',
        'icon_file'
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fitness_levels';
}
