<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnDemandService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service',
        'getfit_id',
        'created_by',
        'status'
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'on_demand_services';

    public function usersData() {
        return $this->hasOne(User::class, 'id','created_by');
    }

    public function getFitData() {
        return $this->hasOne(GetFit::class, 'id','getfit_id');
    }
}
