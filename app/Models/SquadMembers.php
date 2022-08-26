<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SquadMembers extends Model
{
    use HasFactory;

    protected $fillable = [
        'squad_id',
        'member_id',
        'leader_member_flag',
        'status'
    ];


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'squad_members';

    public function user()
    {
        return $this->belongsTo(User::class,'member_id');
    }
    
    public static function isMember($id){

        $isMember = SquadMembers::where('squad_id',$id)->where('status',2)->where('member_id',Auth::User()->id)->first();
  
        if(!empty($isMember)){
            return true;
        }else{
            return false;
        }
  
        }


        public static function requestStatus($id){
            $status = SquadMembers::where('squad_id',$id)->where('member_id',Auth::User()->id)->first('status');
            
            if(!empty($status)){
                return $status->status;
            }else{
                return 0;
            }
      
            }
}
