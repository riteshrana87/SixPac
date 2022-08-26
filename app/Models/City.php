<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = ['name','country_id','country_code','state_id','state_code','latitude','longitude'];
    protected $table = 'cities';
	
	public function usersData(){
        return $this->hasOne(User::class, 'id','created_by');
    }

    public function getCityState() {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }
}
