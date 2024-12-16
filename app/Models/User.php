<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Auth;
use DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'aceid','username','email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Method used to check whether user has the respective role or not
     * Added by dinakar on 08 Jan 2024
     * 
     * @param Array
     * @return Boolean
     */
    public function has_any_role($roles){
        if(!is_array($roles)){
            $roles = explode(",",$roles);
        }
        $user_roles = DB::table('trf_user_role_mapping as urm')
        ->join('trd_roles as r','r.unique_key','urm.role_code')
        ->select('r.name')
        ->where([
            ['urm.aceid', Auth::user()->aceid],
            ['urm.active', 1]
        ])
        ->whereIn('r.name',$roles)->get()->toArray();

        if($user_roles)
            return true;
        else
            return false;
    }
    /**
     * Method used to fetch all rolecodes for the respective user
     * Added by ganesh on 08 Jan 2024
     * 
     * @return Array
     */
    public function has_any_role_code($roles){
        if(!is_array($roles)){
            $roles = explode(",",$roles);
        }
        $user_roles = DB::table('trf_user_role_mapping')
        ->where([['aceid',Auth::user()->aceid],['active',1]])
        ->whereIn('role_code',$roles)->pluck('role_code')->toArray();
        if($user_roles&&count($user_roles))
            return true;
        else if(in_array('requestor',$roles)&&count($roles)==1)
            return true;
        else
            return false;
    }

    /**
     * Method used to fetch all roles for the respective user
     * Added by dinakar on 08 Jan 2024
     * 
     * @return Array
     */
    public function respective_roles(){
        $user_roles = DB::table('trf_user_role_mapping as urm')
        ->join('trd_roles as r','r.unique_key','urm.role_code')
        ->select('r.name')
        ->where([
            ['urm.aceid', Auth::user()->aceid],
            ['urm.active', 1]
        ])
        ->distinct()->pluck('r.name')->toArray();

        return $user_roles;
    }
    public function respective_roles_code(){
        $user_roles = DB::table('trf_user_role_mapping')
        ->select('role_code')
        ->where([['aceid', Auth::user()->aceid],['active', 1]])
        ->distinct()->pluck('role_code')->toArray();

        return $user_roles;
    }

    // Check whether user has report access or not
    public function hasReportAccess($module = "ANY")
    {
        $roles_having_both_access = ['PRO_OW', 'PRO_OW_HIE', 'DU_H', 'DU_H_HIE', 'DEP_H', 'CLI_PTR', 'GEO_H', 'BF_REV', 'FIN_APP'];
        $roles_having_travel_access = ['AN_COST_FAC', 'AN_COST_FIN', 'TRV_PROC_TICKET', 'TRV_PROC_FOREX'];
        $roles_having_visa_access = ['AN_COST_VISA', 'HR_REV', 'HR_PRT', 'GM_REV', 'TRV_PROC_VISA'];

        $roles = match ($module) {
            "ANY"  => array_merge($roles_having_travel_access, $roles_having_visa_access),
            "TRAVEL" => $roles_having_travel_access,
            "VISA" => $roles_having_visa_access,
            default => [],
        };

        $roles = array_unique( array_merge($roles, $roles_having_both_access) );

        return Auth::User()->has_any_role_code($roles);
    }

}
