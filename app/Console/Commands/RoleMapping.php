<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class RoleMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role_mapping:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $existing_user_roles=[];
        $current_date=date("Y-m-d H:i:s");
        $users=DB::table('users')->where('active',1)->pluck('aceid')->toArray();
        $roles=DB::table('trd_roles')->where('active',1)->pluck('unique_key')->toArray();
        foreach($roles as $role){
            $existing_user_roles[$role]=[];
        }
        $user_role_mappings=DB::table('trf_user_role_mapping')->where('active',1)
            ->select('role_code','aceid')->get();
        foreach($user_role_mappings as $key=>$mapping){
            if(array_key_exists($mapping->role_code,$existing_user_roles)){
                $existing_user_roles[$mapping->role_code][]=$mapping->aceid;
            }
        }
//Department heads details fetch
        $dep_head_role_add=[];
        $role='DEP_H';
        $department_heads=DB::table('trd_departments')->where('active',1)->pluck('head')->toArray();
        $department_heads=array_unique($department_heads);  
        foreach($department_heads as $head){
            if($head&&!in_array($head,$existing_user_roles[$role])&&in_array($head,$users))
            $dep_head_role_add[]=[
                'aceid'=>$head, 'role_code'=>$role,'active'=>1,'created_at'=>$current_date,'updated_at'=>$current_date
            ];
        }
        if(count($dep_head_role_add))
        DB::table('trf_user_role_mapping')->insert($dep_head_role_add);

//du heads details fetch
        $role='DU_H';$du_heads_role_add=[];
        $du_heads=DB::table('trd_practice')->whereIn('type',['program','delivery'])->where('active',1)->pluck('head')->toArray();
        $du_heads=array_unique($du_heads);  
        foreach($du_heads as $head){
            if($head&&!in_array($head,$existing_user_roles[$role])&&in_array($head,$users))
            $du_heads_role_add[]=[
                'aceid'=>$head, 'role_code'=>$role,'active'=>1,'created_at'=>$current_date,'updated_at'=>$current_date
            ];
        }
        if(count($du_heads_role_add))
        DB::table('trf_user_role_mapping')->insert($du_heads_role_add);

//DU hierary role addition
        $role='DU_H_HIE';$du_heads_hie_role_add=[];
        $du_heads_hie=DB::table('users')->whereIn('aceid',$du_heads)->where('active',1)->pluck('ReportingToACEID')->toArray();
        $du_heads_hie=array_unique(array_filter($du_heads_hie));
        $du_heads_hie1=DB::table('users')->whereIn('aceid',$du_heads_hie)->where('active',1)->pluck('ReportingToACEID')->toArray();
        $du_heads_hie1=array_unique(array_filter($du_heads_hie1));
        $du_heads_hie2=DB::table('users')->whereIn('aceid',$du_heads_hie1)->where('active',1)->pluck('ReportingToACEID')->toArray();
        $du_heads_hie2=array_unique(array_filter($du_heads_hie2));
        $final_du_heads=array_merge($du_heads_hie,$du_heads_hie1);
        $final_du_heads=array_merge($final_du_heads,$du_heads_hie2);
        $final_du_heads=array_unique($final_du_heads);
        foreach($final_du_heads as $head){
            if($head&&!in_array($head,$existing_user_roles[$role])&&!in_array($head,$department_heads)&&in_array($head,$users))
            $du_heads_hie_role_add[]=[
                'aceid'=>$head, 'role_code'=>$role,'active'=>1,'created_at'=>$current_date,'updated_at'=>$current_date
            ];
        }
        if(count($du_heads_hie_role_add))
        DB::table('trf_user_role_mapping')->insert($du_heads_hie_role_add);

//Geo head and client partner mapping
      $client_partner_permissions=[];$geo_head_permissions=[];
      $cp_and_go_details=DB::table('client_partner_geo_head_mapping')->where('active',1)
        ->select('head_type','configured_user')->get();
      foreach($cp_and_go_details as $owner){
        if($owner->head_type=='client_partner'&&!in_array($owner->configured_user,$existing_user_roles[$role])){
            $role='CLI_PTR';
            $client_partner_permissions[]=[
                'aceid'=>$owner->configured_user, 'role_code'=>$role,'active'=>1,'created_at'=>$current_date,'updated_at'=>$current_date
            ];
        }
        if($owner->head_type=='geo_head'&&!in_array($owner->configured_user,$existing_user_roles[$role])){
            $role='GEO_H';
            $geo_head_permissions[]=[
                'aceid'=>$owner->configured_user, 'role_code'=>$role,'active'=>1,'created_at'=>$current_date,'updated_at'=>$current_date
            ];
        }
          
      }
      if(count($client_partner_permissions))
      DB::table('trf_user_role_mapping')->insert($client_partner_permissions);
    if(count($geo_head_permissions))
      DB::table('trf_user_role_mapping')->insert($geo_head_permissions);

//Project owner role addition
    $role='PRO_OW';$project_owner_role_add=[];
    $project_owners=DB::table('trd_projects')->where('active',1)->pluck('project_owner')->toArray();
    $project_owners=array_unique($project_owners);  
        foreach($project_owners as $head){
            if($head&&!in_array($head,$existing_user_roles[$role])&&in_array($head,$users))
            $project_owner_role_add[]=[
                'aceid'=>$head, 'role_code'=>$role,'active'=>1,'created_at'=>$current_date,'updated_at'=>$current_date
            ];
        }
        if(count($project_owner_role_add))
        DB::table('trf_user_role_mapping')->insert($project_owner_role_add); 
//Project owner hie addition
        $role='PRO_OW_HIE';$pro_own_hie_role_add=[];
        $pro_own_hie=DB::table('users')->whereIn('aceid',$project_owners)->where('active',1)->pluck('ReportingToACEID')->toArray();
        $pro_own_hie=array_unique(array_filter($pro_own_hie));
        $pro_own_hie1=DB::table('users')->whereIn('aceid',$pro_own_hie)->where('active',1)->pluck('ReportingToACEID')->toArray();
        $pro_own_hie1=array_unique(array_filter($pro_own_hie1));
        $pro_own_hie2=DB::table('users')->whereIn('aceid',$pro_own_hie1)->where('active',1)->pluck('ReportingToACEID')->toArray();
        $pro_own_hie2=array_unique(array_filter($pro_own_hie2));
        $final_project_owners=array_merge($pro_own_hie,$pro_own_hie1);
        $final_project_owners=array_merge($final_project_owners,$pro_own_hie2);
        $final_project_owners=array_unique($final_project_owners);
        foreach($final_project_owners as $head){
            if($head&&!in_array($head,$existing_user_roles[$role])
            &&!in_array($head,$department_heads)
            &&!in_array($head,$final_du_heads)
            &&!in_array($head,$du_heads)
            &&in_array($head,$users))
            $pro_own_hie_role_add[]=[
                'aceid'=>$head, 'role_code'=>$role,'active'=>1,'created_at'=>$current_date,'updated_at'=>$current_date
            ];
        }
        if(count($pro_own_hie_role_add))
        DB::table('trf_user_role_mapping')->insert($pro_own_hie_role_add);
    }
}
