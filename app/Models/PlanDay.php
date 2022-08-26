<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by',
        'status'
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'plan_days';
}
