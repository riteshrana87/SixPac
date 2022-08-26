<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Squad extends Model
{
    use HasFactory;

    protected $fillable = [
        'squad_name',
        'squad_profile_pic',
        'banner_pic',
        'notes',
        'status'
    ];


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'squad';

    public function members(){
        return $this->hasMany(SquadMembers::class, 'squad_id');
    }

    public function membersOnly(){
        return $this->hasMany(SquadMembers::class, 'squad_id')->where('leader_member_flag',0);
    }

    public function leaderMembers(){
        return $this->hasMany(SquadMembers::class, 'squad_id')->where('leader_member_flag',1);
    }


    public static function getLeaderMembers($id){
        $detail = SquadMembers::with('user')->where('squad_id',$id)->where('leader_member_flag',1)->first();
        
        if(!empty($detail)){
            return $detail->user->user_name;
        }else{
            return '';
        }
  
        }
    
}
