<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use DB;
use Log;
use Auth,Crypt;
use App\Http\Requests;
use Artisaninweb\SoapWrapper\Facades\SoapWrapper;

class VisaProcessController extends Controller
{
    //Key used for encryption purpose
    private $encryption_key;
    protected $visa_process_id;

    public function __construct(){
      $this->visa_process_id=1;
      $this->encryption_key = $this->get_secure_key();
    }
        
    /**
     * To load the request details done by Hr partner
     * @author ganesh.veilsamy
     * @addedon 12-11-2022
     * @param  mixed $request
     * @return void
     */
    public function get_request_details(Request $request){
          if(Auth::User()->has_any_role(['hr_partner']))
          {
            $request_details=$this->request_brief_details([
            ['method'=>'where','column'=>'pr.initiated_by','condition'=>'=','value'=>Auth::User()->aceid],
            ],null);
            return View::make('layouts.visa_process.home',['request_details'=>$request_details]);
          }
          elseif(Auth::User()->has_any_role('gm_reviewer'))
            return redirect('/visa_process/review');
          elseif (Auth::User()->has_any_role('us_hr_reviewer'))
            return redirect('/visa_process/hr_partner_review');
          elseif (Auth::User()->has_any_role('visa_user'))
            return redirect('/visa_process/myaction');
          elseif(Auth::User()->has_any_role('visa_process_admin'))
            return redirect('/visa_process/administration');
        }
    
        /**
         * To load the request details which is waiting for employee actions
         * @author ganesh.veilsamy
         * @addedon 12-11-2022
         * @param  mixed $request
         * @return void
         */
        public function get_action_details(Request $request){
          $request_details=$this->request_brief_details([
            ['method'=>'where','column'=>'pr.employee_aceid','condition'=>'=','value'=>Auth::User()->aceid],
            ['method'=>'where','column'=>'pr.status_id','condition'=>'>','value'=>1],
          ],null);
          return View::make('layouts.visa_process.employee_action',['request_details'=>$request_details]);
        }
            
        /**
         * To load the request details which are awaiting for reviewer action
         * @author ganesh.veilsamy
         * @addedon 18-11-2022
         * @param  mixed $request
         * @return void
         */
        public function get_review_details(Request $request){
          $request_details=$this->request_brief_details([
            ['method'=>'where','column'=>'pr.status_id','condition'=>'>','value'=>2],
          ],null);
          return View::make('layouts.visa_process.review',['request_details'=>$request_details]);
        }
    
        /**
         * To load the request details which is waiting for US HR Partner review
         * @author dinakar
         * @addedon 18-11-2022
         * @param  mixed $request
         * @return void
         */
        public function get_hr_partner_review_details(Request $request){
          $request_details=$this->request_brief_details([
            ['method'=>'where','column'=>'pr.status_id','condition'=>'>','value'=>3],
          ],null);
          return View::make('layouts.visa_process.hr_partner_review',['request_details'=>$request_details]);
        }
        /**
         * To fetch and share the request details to the request page. 
         * @author ganesh.veilsamy
         * @addedon 15-11-2022
         * @param  mixed $request
         * @return mixed $request_page_details
         */
        public function redirect_to_request($id=null){
          try{
            $saved_request_details=(object)[];$edit_id=null;$current_action_role='hr_partner';
                  
            //For URL restriction
            if(isset($id)){
              $edit_id = Crypt::decrypt($id);
              $employee_aceid = DB::table('visa_process_request_details')->where('request_code',$edit_id)->value('employee_aceid');
              $hr_partner_aceid = DB::table('visa_process_request_details')->where('request_code',$edit_id)->value('initiated_by');
              if(!Auth::User()->has_any_role(['gm_reviewer','us_hr_reviewer'])){
                if(Auth::User()->has_any_role(['hr_partner']) && !in_array(Auth::User()->aceid, [$employee_aceid, $hr_partner_aceid]))
                  return redirect('visa_process/home');
                if(Auth::User()->has_any_role(['visa_user']) && Auth::User()->aceid != $employee_aceid)
                  return redirect('visa_process/home');
              }
            }
    
            if(isset($id)){
              $edit_id=Crypt::decrypt($id);
              $saved_request_details=$this->request_full_details($edit_id);
              $current_status=$saved_request_details->status_id;
              $current_action_role=$this->role_checker($current_status,$saved_request_details->employee_aceid);
            }
            $request_page_details=[];$visa_process_id=1;
            //Added by Venkatesan.R for providing access to modify the salary range
            if(Auth::User()->has_any_role(['visa_process_salary_range_edit_access']) && $current_status == 5)
              $current_action_role = 'visa_process_salary_range_edit_access';
            //Getting configuration fields
            $editable_fields_config=DB::table('visa_process_fields_editable_config');
            $visible_fields_config=DB::table('visa_process_field_visible_config');
            if(isset($id)&&$id){
              $editable_fields_config=$editable_fields_config->where('status_id',$current_status);
              $visible_fields_config=$visible_fields_config->where('status_id',$current_status);
            }
            else{
              $editable_fields_config=$editable_fields_config->whereNull('status_id');
              $visible_fields_config=$visible_fields_config->whereNull('status_id');
            }
              
            $editable_fields_config=$editable_fields_config->where('role',$current_action_role)
              ->where('active',1)->value('fields');  
            $visible_fields_config=$visible_fields_config->where('role',$current_action_role)
            ->where('active',1)->value('fields');  
            $editable_fields=explode(',',$editable_fields_config);
             $visible_fields=explode(',',$visible_fields_config);
    
            // Dependency details fetch
            $process_request_id=DB::table('visa_process_request_details')->where('request_code',$edit_id)->value('id');
            $dependency_details=[];
            if($process_request_id){
              $dependency_details=DB::table('visa_process_dependency_details')->where('process_request_id',$process_request_id)->pluck('dependency_details')->toArray();
              $dependency_details_label_config = ["Spouse","First child","Second child","Third child","Fourth child"];
              $min = min(count($dependency_details),count($dependency_details_label_config));
              $dependency_details = array_combine(array_slice($dependency_details_label_config,0,$min),$dependency_details);
            }
            // Status tracker details 
            $status_details = [];
            if($process_request_id)
              $status_details=json_decode(json_encode(DB::table('visa_process_status_tracker as vpst')->join('users','users.aceid','=','vpst.action_by')->select('vpst.process_request_id','vpst.old_status','vpst.new_status','users.username',DB::raw('DATE_FORMAT(vpst.created_at,"%d-%b-%Y") as created_at'),'vpst.comments as remarks')->where('vpst.process_request_id','=',$process_request_id)->whereRaw('vpst.new_status != vpst.old_status')->orderBy('vpst.updated_at','ASC')->get()),true)??[];
            // Getting file details
            $visa_process_file_details = [];
            $file_details = $this->get_uploaded_file_details($edit_id);
            foreach($file_details as $key => $value){
              if(!$value)
                continue;
              $value = explode(",",$value);
              $value = array_filter($value, function ($file) { return file_exists(public_path($file));});
              $visa_process_file_details[str_replace("path","details",$key)] = array_map(function ($file){ 
                return [
                  "fileName"=>basename($file),
                  "filePath"=>$file,
                  "originalName"=>DB::table('temp_file_details')->where('actual_file_name',basename($file))->value('original_file_name'),
                  "fileSize"=>filesize(public_path($file)),
                  "tempName"=>basename($file)]; 
                }, $value);
            }
            //Master details fetch
          // if(Auth::User()->has_any_role(['visa_hr_admin']))
              $users=DB::table('users')->where('active',1)->pluck(DB::raw("concat(aceid,'-',username)"),'aceid')->toArray();
            // else
            //   $users=$this->populatingUsersList();
            $visa_type=DB::table('visa_process_visa_type')->where('visa_process_id',$this->visa_process_id)
              ->where('active',1)->pluck('visa_type','id')->toArray();
            $request_type=DB::table('visa_request_type_master')->where('visa_process_id',$this->visa_process_id)
            ->where('active',1)->pluck('request_type','id')->toArray();
            $petition_type=DB::table('visa_petition_master')->where('visa_process_id',$this->visa_process_id)
            ->where('active',1)->pluck('name','id')->toArray();
            
            $visa_type_id = null;
            if($process_request_id)
              $visa_type_id = DB::table('visa_process_request_details')->where('id',$process_request_id)->where('visa_process_id',$this->visa_process_id)->where('active',1)->value('visa_type_id');
            if($visa_type_id && $visa_type_id == 4)
              $petitioner_entity=DB::table('visa_petitioner_entity_master')->where('visa_process_id',$this->visa_process_id)->where('active',1)->where('id','<>',2)->pluck('petitioner_entity','id')->toArray();
            else
              $petitioner_entity=DB::table('visa_petitioner_entity_master')->where('visa_process_id',$this->visa_process_id)
              ->where('active',1)->pluck('petitioner_entity','id')->toArray();
            $customers = DB::table('trd_projects')->where('active',1)->pluck('customer_name','customer_code')->toArray();
            //$customers = array_merge($customers,$trd_projects);
            asort($customers,SORT_NATURAL | SORT_FLAG_CASE);
            $gender_master=DB::table('gender_master')->where('active',1)->pluck('name','id')->toArray();
            $education_category_master = DB::table('education_category_master')->where('active',1)->pluck('shortterm','id')->toArray();
            $education_master=DB::table('education_details_master')->where('active',1)->pluck('qualification','id')->toArray();
            $job_title_master=DB::table('visa_job_titile_master')->where('active',1)->pluck('name','id')->toArray();
            $visa_filing_master=DB::table('visa_filing_master')->where('active',1)->pluck('filing_type','id')->toArray();
            $us_managers=['ACE2386'=>'Tithee Paal'];// Harcoded for testing purpose.. need to fetch US Managers 
            $us_managers = DB::table('users as employee')->join('users as man','man.aceid','=','employee.ReportingToACEID')->where('employee.OfficeLocation','US')->where('man.active',1)->orderBy('man.username','ASC')->pluck('man.username','man.aceid')->toArray();
            $visa_attorneys_master=DB::table('visa_attorneys_master')->where('active',1)->pluck('name','id')->toArray();
            $visa_interview_type_master=DB::table('visa_interview_type_master')->where('visa_process_id',$this->visa_process_id)
            ->where('active',1)->pluck('type','id')->toArray();
            $visa_status_master=DB::table('visa_status_master')->where('visa_process_id',$this->visa_process_id)
            ->where('active',1)->pluck('status','id')->toArray();
            $visa_travel_type_master=DB::table('visa_travel_type_master')->where('active',1)->pluck('name','id')->toArray();
            $department = DB::table('trd_departments')->where('active',1)->pluck('name')->toArray();
            //For hr remarks in US salary negotiation section
            $hr_review_remarks = DB::table('visa_process_status_tracker as vpst')
                                  ->where('vpst.process_request_id',$process_request_id)
                                  ->where('vpst.old_status',5)
                                  ->orderBy('vpst.updated_at','desc')
                                  ->value('comments');
            $aspire_job_title_master = [];
            if($process_request_id)
              $aspire_job_title_master=DB::table('visa_process_request_details as vprd')
                                        ->join('users as emp','emp.aceid','=','vprd.employee_aceid')
                                        ->join('visa_immigration_job_title_master as vijtm','vijtm.level','=','emp.LevelName')
                                        ->where('vprd.id',$process_request_id)
                                        ->pluck('vijtm.name','vijtm.id')->toArray();
            $request_page_details=[
              'editable_fields'=>$editable_fields,
              'visible_fields'=>$visible_fields,
              'users'=>$users,
              'visa_type'=>$visa_type,
              'request_type'=>$request_type,
              'petition_type'=>$petition_type,
              'petitioner_entity'=>$petitioner_entity,
              'customers'=>$customers,
              'gender_master'=>$gender_master,
              'education_category_master' => $education_category_master,
              'education_master'=>$education_master,
              'job_title_master'=>$job_title_master,
              'visa_filing_master'=>$visa_filing_master,
              'edit_id'=>$edit_id,
              'saved_request_details'=>$saved_request_details,
              'us_managers'=>$us_managers,
              'visa_attorneys_master'=>$visa_attorneys_master,
              'visa_interview_type_master'=>$visa_interview_type_master,
              'visa_status_master'=>$visa_status_master,
              'visa_travel_type_master'=>$visa_travel_type_master,
              'dependency_details'=>$dependency_details,
              'department'=>$department,
              'status_details'=>$status_details,
              'aspire_job_title_master'=>$aspire_job_title_master,
              'hr_review_remarks'=>$hr_review_remarks,
              'visa_process_file_details'=>$visa_process_file_details,
            ];
            return View::make('layouts.visa_process.request',$request_page_details);  
          }
          catch(\Exception $e){
            // dd($e);
            Log::info($e);
            return View::make('layouts.visa_process.request',['error'=>'Error occured while fetching data. Please write to help.mis@aspiresys.com for assistence']);
          }
          
        }
        
        /**
         * To fetch the selected user info 
         * @author  ganesh.veilsamy
         * @addedon 11-11-2022
         * @param  mixed $request
         * @return void
         */
        public function get_employee_details(Request $request){
          try{
            $aceid=$request->aceid;
            $user_details=DB::table('users')->leftJoin('users as pm','users.ReportingToACEID','=','pm.aceid')
            ->leftJoin('trd_departments','trd_departments.code','=','users.DepartmentId')
            ->leftJoin('trd_practice as practice','practice.code','=','users.PracticeId')
            ->select('users.email as user_email','trd_departments.name as user_dept','pm.username as user_pm','practice.name as practice')
            ->where('users.aceid',$aceid)
            ->first();
            return json_encode(['user_details'=>$user_details]);
          }
          catch(\Exception $e){
            Log::info($e);
            return json_encode(['error'=>'Error occured while fetching user details.']);
          }
          
        }
        
        /**
         * To insert/update the details entered by the user
         * @author ganesh.veilsamy
         * @addedon 11-11-2022
         * @param  mixed $request
         * @return void
         */
        public function save_request_details(Request $request){
          try{
            if(isset($request['edit_id'])&&$request['edit_id']){
              $saved_request_data=DB::table('visa_process_request_details')->where('request_code',$request['edit_id'])->first();
              if($saved_request_data){
                $current_status=$saved_request_data->status_id;
                $request_code=$saved_request_data->request_code;
              }
              else{
                return json_encode(['error'=>'Request ID not found']);
                exit();
              }
            }
            else{
              $current_status=0;
              $request_code=$this->visa_code_generator();
            }
            $employee_id=($request['employee_id'])?$request['employee_id']:$saved_request_data->employee_aceid;
            $current_action_role=$this->role_checker($current_status,$employee_id);
            $next_status=$this->status_assigner($current_action_role,$request['action'],$current_status,$request['employee_id']);
            if(!$next_status){
              return json_encode(['error'=>"You don't have permission to perform this action"]);
              exit();
            }
            if(isset($request['visa_status_id']) && $request['visa_status_id'] == 2 && $next_status == 9){
              $next_status = 10; // when Visa Status is selected as "Visa Denied" in visa stamping page.
            }
            if(isset($request['acceptance_by_user']) && $request['acceptance_by_user'] == 0 && $next_status == 6 && $next_status != $current_status){
              $next_status = 14; // When user rejected the offer in US salary negotiation section
            }
            $save_details_array=[
              'request_code'=>$request_code, 'visa_process_id'=>1,
              'employee_aceid'=>$request['employee_id'], 'initiated_by'=>Auth::User()->aceid,
              'visa_type_id'=>$request['visa_type_id'],'request_type_id'=>$request['request_type_id'],
              'client_code'=>$request['client_code'], 'petition_id'=>$request['petition_id'],
              'hr_remarks'=>$request['remarks'], 'status_id'=>$next_status,
              'active'=>1,
    
              'address'=>$request['address'], 'first_name'=>$request['first_name'],
              'last_name'=>$request['last_name'],'gender_id'=>$request['gender_id'],
              'date_of_birth'=>date('Y-m-d', strtotime($request['dob'])),
              'date_of_joining'=>date('Y-m-d', strtotime($request['doj'])),
              'employee_remarks'=>$request['remarks'],'passport_no'=>$request['passport_no'],
              'education_details_id'=>$request['education'],
              'education_category_id'=>$request['education_category'],
    
              'minimum_wage'=>$request['minimum_wage'],'work_location'=>$request['work_location'],
              'filing_type_id'=>$request['filing_type_id'],'job_titile_id'=>$request['job_titile_id'],
              'gm_remarks'=>$request['remarks'],
              
              'india_experience'=>$request['india_experience_year'].".".$request['india_experience_month'],
              'overall_experience'=>$request['overall_experience_year'].".".$request['overall_experience_month'],
              'band_detail'=>$request['band_details'],
              'salary_range_from'=>DB::raw("AES_ENCRYPT(".$request['salary_range_from'].",UNHEX(SHA2('".$this->encryption_key."',512)))"),
              'salary_range_to'=>DB::raw("AES_ENCRYPT(".$request['salary_range_to'].",UNHEX(SHA2('".$this->encryption_key."',512)))"),
              'us_job_title_id'=>$request['us_job_title_id'],
              
              'acceptance_by_user'=>$request['acceptance_by_user'],
              'us_salary'=>DB::raw("AES_ENCRYPT(".$request['us_salary'].",UNHEX(SHA2('".$this->encryption_key."',512)))"),
              'one_time_bonus'=>DB::raw("AES_ENCRYPT(".$request['one_time_bonus'].",UNHEX(SHA2('".$this->encryption_key."',512)))"),
              'one_time_bonus_payout_date'=>date('Y-m-d', strtotime($request['one_time_bonus_payout_date'])),
              'next_salary_revision_on'=>date('Y-m-d', strtotime($request['next_salary_revision_on'])),
              'offshore_hr_remarks'=>$request['remarks'],
              
              'us_manager_id'=>$request['us_manager_id'],
              'inszoom_id'=>$request['inszoom_id'],
              'entity_id'=>$request['entity_id'],
              'attorneys_id'=>$request['attorneys_id'],
              'petition_file_date'=>date('Y-m-d', strtotime($request['petition_file_date'])),
              'receipt_no'=>$request['receipt_no'],
              'petition_start_date'=>date('Y-m-d', strtotime($request['petition_start_date'])),
              'petition_end_date'=>date('Y-m-d', strtotime($request['petition_end_date'])),
    
              'visa_interview_type_id'=>$request['visa_interview_type_id'],
              'visa_ofc_date'=>date('Y-m-d', strtotime($request['visa_ofc_date'])),
              'visa_interview_date'=>date('Y-m-d', strtotime($request['visa_interview_date'])),
              'visa_status_id'=>$request['visa_status_id'],
              'travel_date'=>date('Y-m-d', strtotime($request['travel_date'])),
              'travel_location'=>$request['travel_location'],
              'traveling_type_id'=>$request['traveling_type_id'],
    
              'record_number'=>$request['record_number'],
              'most_recent_doe'=>date('Y-m-d', strtotime($request['most_recent_doe'])),
              'admit_until'=>date('Y-m-d', strtotime($request['admit_until'])),
              'gc_initiated_on'=>date('Y-m-d', strtotime($request['gc_initiated_on'])),
              
              'created_at'=>date('Y-m-d H:i:s'),
              'updated_at'=>date('Y-m-d H:i:s'),
              'offer_letter_path'=>implode(",",[$request['offer_letter_path'],$request['word_document_path']]),
              'immigration_offer_letter_path'=>$request['immigration_offer_letter_path'],
              'green_card_title'=>$request['green_card_title'],
    
              'gender' => DB::table('gender_master')->where('id',$request['gender_id'])->value('name'),
              'educational_qualification' => DB::table('education_details_master')->where('id',$request['education'])->value('qualification'),
              'aceid' => Auth::User()->aceid,
            ];
            // To avoid storing the base UTC date on empty string inputs.
            if($request['one_time_bonus_payout_date'] == "")
              $save_details_array['one_time_bonus_payout_date'] = null;
            if($request['next_salary_revision_on'] == "")
              $save_details_array['next_salary_revision_on'] = null;
            
            if($request['petition_start_date'] == "")
              $save_details_array['petition_start_date'] = null;
    
            if($request['petition_end_date'] == "")
              $save_details_array['petition_end_date'] = null;
          
            if($request['visa_ofc_date'] == "")
              $save_details_array['visa_ofc_date'] = null;
            
            if($request['visa_interview_date'] == "")
              $save_details_array['visa_interview_date'] = null;
    
            if($current_status == 2) {
              $save_details_array['passport_file_path'] = $this->move_temp_file($request['passport-file-hidden'],$request_code,"passport");
              $save_details_array['cv_file_path'] = $this->move_temp_file($request['cv-file-hidden'],$request_code,"cv");
              $save_details_array['degree_file_path'] = $this->move_temp_file($request['degree-file-hidden'],$request_code,"degree");
            }
    
            if($current_status == 6 || $current_status == 13)
              $save_details_array['petition_file_path'] = $this->move_temp_file($request['petition-file-hidden'],$request_code,"petition");
            
            if($current_status == 7)
              $save_details_array['visa_file_path'] = $this->move_temp_file($request['visa-file-hidden'],$request_code,"visa");
    
            $table_to_insert_update=DB::table('visa_process_dbtable_insert_config')
              ->where('visa_process_id',1)->where('role',$current_action_role)
              ->where('status_id',$current_status)->where('active',1)
              ->orderBy('ordering')->pluck('fields','table_to_insert')
              ->toArray();
            
            if($current_status == 5 && $current_action_role == "visa_process_salary_range_edit_access"){
              $process_request_id = DB::table('visa_process_request_details')->where('request_code',$request['edit_id'])->value('id');
              $old_salary_range_from = DB::table('visa_process_review_details')->where('process_request_id',$process_request_id)->value('salary_range_from');
              $old_salary_range_to = DB::table('visa_process_review_details')->where('process_request_id',$process_request_id)->value('salary_range_to');
            }
              
            DB::beginTransaction();
            foreach($table_to_insert_update as $table_name=>$field){
              $colums_array=[];$array_to_insert_update=[];
              $columns_array=explode(',',$field);
              if($table_name=='visa_process_dependency_details'){
                if(isset($request['dependent_name_input']) && is_array($request['dependent_name_input']) && count($request['dependent_name_input'])){
                  foreach($request['dependent_name_input'] as $val){
                    foreach($columns_array as $column){
                      $inner_array[$column]=array_key_exists($column,$save_details_array)?$save_details_array[$column]: ($column=='dependency_details'? $val : null );
                    }
                    $array_to_insert_update[]=$inner_array;
                  }
                }
              }else{
                foreach($columns_array as $column){
                  $array_to_insert_update[$column]=array_key_exists($column,$save_details_array)?$save_details_array[$column]:null;
                }
              }
              if(isset($request['edit_id'])&&$request['edit_id']){
                if($table_name=='visa_process_request_details')
                  DB::table($table_name)->where('request_code',$request_code)->update($array_to_insert_update);
                else{
                  $is_update_request = DB::table($table_name)->where('process_request_id',$process_request_id)->pluck('id')->toArray();
                  if(is_array($is_update_request) && count($is_update_request)){
                    if($table_name=='visa_process_dependency_details'){
                      DB::table($table_name)->where('process_request_id',$process_request_id)->delete();
                      DB::table($table_name)->insert($array_to_insert_update);
                    }
                    else
                      DB::table($table_name)->where('process_request_id',$process_request_id)->update($array_to_insert_update);
                  }
                  else
                  DB::table($table_name)->insert($array_to_insert_update);
                }
                
              }
              else{
                DB::table($table_name)->insert($array_to_insert_update);
              }
              if($table_name=='visa_process_request_details'){
              $process_request_id=DB::table('visa_process_request_details')->where('request_code',$request_code)->value('id');
              $save_details_array['process_request_id']=$process_request_id;
              }
            }
            if($next_status==2){
              // $user_id=DB::table('users')->where('aceid',$employee_id)->value('id');
              // $role_id=DB::table('role')->where('name','visa_user')->value('id');
              // DB::table('user_role_mapping')->insert(['user_id'=>$user_id,'role_id'=>$role_id]);
              $data = [
                  'active' => 1,
                  'created_by' => Auth::User()->aceid,
                  'created_at' => date('Y-m-d h:i:s'),
                  'updated_at' => date('Y-m-d h:i:s')
              ];
              $condition = [
                  'aceid' => $employee_id,
                  'role_code' => 'VIS_USR'
              ];
              if(DB::table('trf_user_role_mapping')->where($condition)->exists())
                unset($data['created_at']);
              DB::table('trf_user_role_mapping')->updateOrInsert($condition, $data);

            }
            DB::table('visa_process_status_tracker')->insert([
              'process_request_id'=>$process_request_id,
              'visa_process_id'=>1,
              'old_status'=>$current_status,
              'new_status'=>$next_status,
              'action_by'=>Auth::User()->aceid,
              'comments'=>$request['remarks'],
              'created_at'=>date('Y-m-d H:i:s'),
              'updated_at'=>date('Y-m-d H:i:s')
            ]);
            if($current_status == 5 && $current_action_role == "visa_process_salary_range_edit_access")
            {
              DB::table('visa_process_salary_range_status_tracker')->insert([
                'visa_process_id'=>1,
                'process_request_id'=>DB::table('visa_process_request_details')->where('request_code',$request['edit_id'])->value('id'),
                'old_salary_range_from'=>$old_salary_range_from,
                'old_salary_range_to'=>$old_salary_range_to,
                'new_salary_range_from'=>$request['salary_range_from'],
                'new_salary_range_to'=>$request['salary_range_to'],
                'updated_by'=>Auth::User()->aceid,
                'created_at'=>date('Y-m-d h:i:s'),
                'updated_at'=>date('Y-m-d h:i:s')
              ]);
            }
            DB::commit();
            $mailController = new MailController();
            //Key-value mapping: next_status => ["config_name,subject",...]
            $config_array = [
              2=>"Initiation",
              3=>"EmployeeAction",
              4=>"GmReview",
              5=>"UsHrSalaryNegotiation",
              6=>"OffshoreHrSalaryNegotiation,PetitionUnderProcess",
              7=>"PetitionApproved",
              8=>"PetitionDenied",
              9=>"VisaApproved",
              10=>"VisaDenied",
              11=>"VisaStamping",
              12=>"VisaTracking",
              13=>"RFEProgress",
              14=>"UndertakenConditionRefusal",
            ];
            $mail_details = [];
            if($next_status != $current_status && $next_status>=2 && $next_status<15)
            {
                // foreach($config_array[$next_status] as $mail_config){
                //   $mail_details = explode(",",$mail_config);
                //   $mailController->sendMail($save_details_array['process_request_id'],$mail_details[0],$mail_details[1],$this->get_secure_key());
                // }
                $mail_details = [
                  "mail_name" => $config_array[$next_status],
                  "request_id" => $request_code,
                  "mail_flag" => "visa",
                ];
            }
            if($next_status == $current_status)
              return json_encode(['success'=>'Request has been saved successfully','redirect_url'=>'/visa_process/home']);
            if($next_status == 1)
              return json_encode(['success'=>'Request has been saved successfully','redirect_url'=>'/visa_process/home']);
            if($next_status == 2)
            {
              return json_encode(['success'=>'Request has been initiated successfully','redirect_url'=>'/visa_process/home', 'mail_details' => $mail_details]);
            }
            if($next_status == 3)
            {
              return json_encode(['success'=>'Request has been send for Immigration team to review','redirect_url'=>'../home', 'mail_details' => $mail_details]);
            }
            if($next_status == 4)
            {
              return json_encode(['success'=>'Request is under US salary discussion','redirect_url'=>'../home', 'mail_details' => $mail_details]);
            }
            if($next_status == 5)
            {
              return json_encode(['success'=>'Request has been moved for Offshore salary discussion','redirect_url'=>'../home', 'mail_details' => $mail_details]);
            }
            if($next_status == 6)
            {
              return json_encode(['success'=>'Petition process has been initiated','redirect_url'=>'../home', 'mail_details' => $mail_details]);
            }
            if($next_status == 7)
            {
              return json_encode(['success'=>'Petition has been approved','redirect_url'=>'../home', 'mail_details' => $mail_details]);
            }
            if($next_status == 8)
            {
              return json_encode(['success'=>'Petition has been denied','redirect_url'=>'../home', 'mail_details' => $mail_details]);
            }
            if($next_status == 9)
            {
              return json_encode(['success'=>'Visa process has been approved','redirect_url'=>'../home', 'mail_details' => $mail_details]);
            }
            if($next_status == 10)
            {
              return json_encode(['success'=>'Visa process has been rejected','redirect_url'=>'../home', 'mail_details' => $mail_details]);
            }
            if($next_status == 11)
            {
              return json_encode(['success' =>'Travel process has been initiated', 'redirect_url'=>'../home', 'mail_details' => $mail_details]);
            }
            if($next_status == 12)
            {
              return json_encode(['success' => 'Travel process has been completed', 'redirect_url'=>'../home', 'mail_details' => $mail_details]);
            }
            if($next_status == 13)
              return json_encode(['success' => 'Petition status has been updated as RFE', 'redirect_url'=>'../home', 'mail_details' => $mail_details]);
            if($next_status == 14)
              return json_encode(['success' => 'Changes are saved successfully', 'redirect_url'=>'../home', 'mail_details' => $mail_detailss]);
          }
          catch(\Exception $e){
            Log::info($e);
            DB::rollback();
            return $e;
          }
        }
        
        /**
         * To generate the unique code for the visa process
         * @author ganesh.veilsamy 
         * @addedon 12-11-2022
         * @param null
         * @return char $request_code
         */
        public function visa_code_generator()
        {
                $currentdate = strtotime(date('m/d/y'));
                $inputyear = strftime('%Y',$currentdate);
                $fystart='04/01';
                $fyend='03/31';
                $fystartdate = strtotime($fystart.'/'.$inputyear);
                $fyenddate = strtotime($fyend.'/'.$inputyear);
                if($currentdate <= $fyenddate)
              {
                  $fy = (intval($inputyear)%1000);
                  $fy1 = ((intval($inputyear)-1)%1000);
                  $fy2=$fy1.$fy;
              }
              else
              {
                  $fy = (intval($inputyear)%1000);
                  $fy1 = (intval(intval($inputyear) + 1)%1000);
                  $fy2=$fy.$fy1;
              }
               $req_code=$fy2.'VISA';
               //request id increament process
               if(count(DB::table('visa_process_request_details')->where('request_code','LIKE','%'.$req_code.'%')->get()))
               {
                   $short_not=DB::table('visa_process_request_details')->where('request_code','LIKE','%'.$req_code.'%')->orderBy('request_code', 'DESC')->take(1)->first()->request_code;
                   $arr=substr($short_not, -4);
                   $num=intval($arr)+1;
                   $num = str_pad($num, 4, '0', STR_PAD_LEFT);
                   $num=(string)$num;
                   $request_code=$req_code.$num;
               }
               //if request code is new set request code from 0001
              else
              {
                  $request_code=$req_code.'0001';
              }
              return $request_code;
        }
    
        public function status_assigner($role,$action,$current_status,$employee){
          $next_action_status=DB::table('visa_process_flow_config')
            ->where('role',$role)
            ->where('action',$action)
            ->where('old_status',$current_status)
            ->value('new_status');
    
          return $next_action_status;
        }
        
        /**
         * To get the basic details of the request based on the user roles.
         * @author  ganesh.veilsamy
         * @addedon 12-11-2022
         * @param  mixed $filter_creterias
         * @param  mixed $orderby_values
         * @return object $request_brief_details
         */
        public function request_brief_details($filter_creterias,$orderby_values){
            $request_brief_details=DB::table('visa_process_request_details as pr')
            ->leftJoin('users as employee','employee.aceid','=','pr.employee_aceid')
            ->leftJoin('visa_process_status as st','st.id','=','pr.status_id')
            ->join('visa_process_visa_type as vt','vt.id','=','pr.visa_type_id')
            ->join('visa_request_type_master as rt','rt.id','=','pr.request_type_id')
            ->join('trd_projects as cm','cm.customer_code','=','pr.client_code')
            ->join('visa_petition_master as pm','pm.id','=','pr.petition_id')
            ->leftJoin('trd_departments as dept','dept.code','=','employee.DepartmentId')
            ->leftJoin('users as man','man.aceid','=','employee.ReportingToACEID')
            ->select('pr.id as process_request_id','pr.request_code as request_code','pr.visa_process_id as visa_process_id',
            'pr.employee_aceid as employee_aceid','pr.initiated_by as initiated_by','pr.visa_type_id as visa_type_id',
            'pr.request_type_id as request_type_id','pr.client_code as client_code','pr.petition_id as petition_id',
            'pr.hr_remarks as hr_remarks','pr.status_id as status_id',DB::raw('DATE_FORMAT(pr.created_at, "%d-%b-%Y") as created_at'),'employee.username as employee_name',
            'employee.email as employee_mail','employee.ReportingToACEID as employee_manager_id','employee.DepartmentId as employee_dept_id',
            'st.name as status','vt.visa_type as visa_type','rt.request_type','cm.customer_name as client_name',
            'pm.name as petition_name','dept.name as employee_dept','man.username as manager_name');
            if(count($filter_creterias)){
              foreach($filter_creterias as $filters){
                if($filters['condition'])
                $request_brief_details=$request_brief_details->{$filters['method']}($filters['column'],$filters['condition'],$filters['value']);
                else
                $request_brief_details=$request_brief_details->{$filters['method']}($filters['column'],$filters['value']);
              }
            }
            $request_brief_details=$request_brief_details->orderBy('pr.id',"DESC")->distinct()->get();
          return $request_brief_details;
        }
    
        public function request_full_details($request_id){
          try{
            $request_full_details=DB::table('visa_process_request_details as pr')
            ->leftJoin('users as employee','employee.aceid','=','pr.employee_aceid')
            ->leftJoin('visa_process_status as st','st.id','=','pr.status_id')
            ->join('visa_process_visa_type as vt','vt.id','=','pr.visa_type_id')
            ->join('visa_request_type_master as rt','rt.id','=','pr.request_type_id')
            ->join('trd_projects as cm','cm.customer_code','=','pr.client_code')
            ->join('visa_petition_master as pm','pm.id','=','pr.petition_id')
            ->join('visa_process_currency_config as vpcc','vpcc.visa_process_id','=','pr.visa_process_id')
            ->join('trd_currency as cur','cur.id','=','vpcc.currency_id')
            ->leftJoin('trd_departments as dept','dept.code','=','employee.DepartmentId')
            ->leftJoin('trd_practice as prac','prac.code','=','employee.PracticeId')
            ->leftJoin('users as man','man.aceid','=','employee.ReportingToACEID')
            ->leftJoin('visa_process_employee_details as ed','ed.process_request_id','=','pr.id')
            ->leftJoin('gender_master as gm','gm.id','=','ed.gender_id')
            ->leftJoin('education_details_master as em','em.id','=','ed.education_details_id')
            ->leftJoin('education_category_master as ecm','ecm.id','ed.education_category_id')
            ->leftJoin('visa_process_job_details as jd','jd.process_request_id','=','pr.id')
            ->leftJoin('visa_job_titile_master as jtm','jtm.id','=','jd.job_titile_id')
            ->leftJoin('visa_filing_master as fm','fm.id','=','jd.filing_type_id')
            ->leftJoin('visa_process_review_details as rd','rd.process_request_id','=','pr.id')
            ->leftJoin('users as us_manager','us_manager.aceid','rd.us_manager_id')
            ->leftJoin('visa_petitioner_entity_master as petition_entity','petition_entity.id','rd.entity_id')
            ->leftJoin('visa_attorneys_master as vam','vam.id','rd.attorneys_id')
            //->leftJoin('visa_job_titile_master as ujtm','ujtm.id','=','rd.us_job_title_id')
            ->leftJoin('visa_immigration_job_title_master as vijtm','vijtm.id','=','rd.us_job_title_id')
            ->leftJoin('visa_interview_type_master as vitm','vitm.id','rd.visa_interview_type_id')
            ->leftJoin('visa_status_master as vsm','vsm.id','rd.visa_status_id')
            ->leftJoin('visa_process_travel_details as vptd','vptd.process_request_id','pr.id')
            ->leftJoin('visa_travel_type_master as vttm','vttm.id','vptd.traveling_type_id')
            ->leftJoin('visa_process_tracking_details as vptr','vptr.process_request_id','pr.id')
            ->leftJoin('visa_process_status_tracker as vpst',function($join){
              $join->on('vpst.process_request_id','=','pr.id')
              ->where('vpst.new_status','=',5)
              ->orWhere('vpst.new_status','=',6);
            })
             ->select('pr.id as process_request_id','pr.request_code as request_code','pr.visa_process_id as visa_process_id',
               'pr.employee_aceid as employee_aceid','pr.initiated_by as initiated_by','pr.visa_type_id as visa_type_id',
               'pr.request_type_id as request_type_id','pr.client_code as client_code','pr.petition_id as petition_id',
               'pr.hr_remarks as hr_remarks','pr.status_id as status_id','pr.created_at as created_at','employee.username as employee_name',
               'employee.email as employee_mail','employee.ReportingToACEID as employee_manager_id','employee.DepartmentId as employee_dept_id',
               'st.name as status','vt.visa_type as visa_type','rt.request_type','cm.customer_name as client_name',
               'pm.name as petition_name','dept.name as employee_dept','man.username as manager_name','ed.first_name',
               'ed.last_name','ed.gender_id','gm.name as gender',DB::raw('DATE_FORMAT(ed.date_of_birth, "%d-%b-%Y") as "dob"'),
               DB::raw('DATE_FORMAT(ed.date_of_joining, "%d-%b-%Y") as "doj"'),'ed.address as address','ed.passport_no','ed.passport_file_path','ed.cv_file_path',
               'em.qualification as education','ed.education_details_id','ecm.shortterm as education_category','ed.education_category_id','ed.employee_remarks','ed.india_experience','ed.overall_experience' 
               ,'jd.minimum_wage','jd.work_location','jd.job_titile_id','jd.filing_type_id','jtm.name as job_title',
               'fm.filing_type as filing_type','jd.gm_remarks','ed.india_experience','ed.overall_experience',
               'ed.band_detail',DB::raw("CAST(AES_DECRYPT(rd.salary_range_from,UNHEX(SHA2('".$this->encryption_key."',512))) as DECIMAL) as salary_range_from"),DB::raw("CAST(AES_DECRYPT(rd.salary_range_to,UNHEX(SHA2('".$this->encryption_key."',512))) as DECIMAL) as salary_range_to"),'rd.us_job_title_id','vijtm.name as us_job_title',
               'rd.acceptance_by_user',DB::raw("CAST(AES_DECRYPT(rd.us_salary,UNHEX(SHA2('".$this->encryption_key."',512))) as DECIMAL) as us_salary"),DB::raw("CAST(AES_DECRYPT(rd.one_time_bonus,UNHEX(SHA2('".$this->encryption_key."',512))) as DECIMAL) as one_time_bonus"),DB::raw('DATE_FORMAT(rd.one_time_bonus_payout_date,"%d-%b-%Y") as "one_time_bonus_payout_date"'),DB::raw('DATE_FORMAT(rd.next_salary_revision_on, "%d-%b-%Y") as "next_salary_revision_on"'),
               'us_manager.aceid as us_ReportingToACEID','petition_entity.id as petitioner_entity_id','vam.id as visa_attorneys_id','us_manager.username as us_manager_id','rd.inszoom_id','petition_entity.petitioner_entity as entity_id','vam.name as attorneys_id',
               DB::raw('DATE_FORMAT(rd.petition_file_date, "%d-%b-%Y") as "petition_file_date"'),'rd.receipt_no',DB::raw('DATE_FORMAT(rd.petition_start_date, "%d-%b-%Y") as "petition_start_date"'),
               DB::raw('DATE_FORMAT(rd.petition_end_date, "%d-%b-%Y") as "petition_end_date"'),'vitm.type as visa_interview_type','rd.visa_interview_type_id',DB::raw('DATE_FORMAT(rd.visa_ofc_date, "%d-%b-%Y") as "visa_ofc_date"'),
               DB::raw('DATE_FORMAT(rd.visa_interview_date, "%d-%b-%Y") as "visa_interview_date"'),'vsm.status as visa_status','rd.visa_status_id',
               DB::raw('DATE_FORMAT(vptd.travel_date, "%d-%b-%Y") as "travel_date"'),'vptd.travel_location','vttm.name as traveling_type','vptd.traveling_type_id','vptr.record_number',DB::raw('DATE_FORMAT(vptr.most_recent_doe, "%d-%b-%Y") as "most_recent_doe"'),
               DB::raw('DATE_FORMAT(vptr.admit_until, "%d-%b-%Y") as "admit_until"'),DB::raw('DATE_FORMAT(vptr.gc_initiated_on, "%d-%b-%Y") as "gc_initiated_on"'), 'vptr.offer_letter_path','vpst.comments',
               'vptr.green_card_title',DB::raw('DATE_FORMAT(vpst.created_at,"%d-%b-%Y") as created_at'),'cur.currency as currency_notation','prac.name as practice')
            ->where('pr.request_code',$request_id)->first();
            
            return $request_full_details;
          }
          catch(\Exception $e){
            Log::Info($e);
          }
          
        }
    
        public function role_checker($current_status,$employee){
          if( $current_status == 5 && Auth::User()->has_any_role('visa_process_salary_range_edit_access') )
            $role = 'visa_process_salary_range_edit_access';
          else if(in_array($current_status,[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14])&&Auth::User()->has_any_role(['hr_partner']))
            $role='hr_partner';
          // added us_hr_reviewer role -> dinakar on 18th Nov 2022
          // else if(in_array($current_status,[4,5])&&Auth::User()->has_any_role(['us_hr_reviewer']))
          //   $role='us_hr_reviewer';
          else if(in_array($current_status,[3,4,5,6,7,8,9,10,11,12,13,14])&&Auth::User()->has_any_role(['gm_reviewer']))
            $role='gm_reviewer';
          else if(in_array($current_status,[2,3,4,5,6,7,8,9,10,11,12,13,14])&&Auth::User()->has_any_role(['visa_user'])&&$employee==Auth::User()->aceid)
            $role='visa_user';
          else if(Auth::User()->has_any_role(['us_hr_reviewer']))
            $role='us_hr_reviewer';
          else
            $role='';
          return $role;  
        }
    
        public function upload_offer_letter(Request $request)
        {
          try {
            //To store green card eligibility field
            $process_request_id = DB::table('visa_process_request_details')->where('request_code',$request["request_code"])->value('id');
            $need_to_insert = [
              'process_request_id'=> $process_request_id,
              'green_card_title' => $request['green_card_title'],
              'created_at' => date("Y/m/d H:i:s",time()),
              'updated_at' => date("Y/m/d H:i:s",time())  
            ];
            
            $id = DB::table('visa_process_tracking_details')->where('process_request_id',$process_request_id)->value('id');
            if(isset($id) && $id != null)
              DB::table('visa_process_tracking_details')->where('id',$id)->update($need_to_insert);
            else
            DB::table('visa_process_tracking_details')->insert($need_to_insert);
    
            //Offer letter in PDF format
            $filename = $request['filename'];
            $filepath = public_path('offer_letter/');
            $fullpath = $filepath.$filename;
            move_uploaded_file($_FILES['pdf']['tmp_name'],$fullpath);
            $offer_letter = ["filename" => $filename, 'path' => "../../offer_letter/".$filename];
        
            //Offer letter in PDF format for immigration purpose
            $immigration_filename = $request['immigration_filename'];
            $immigration_fullpath = $filepath.$immigration_filename;
            move_uploaded_file($_FILES['immigration_offer_letter']['tmp_name'],$immigration_fullpath);
            $immigration_offer_letter = ['filename' => $immigration_filename,'path' => "../../offer_letter/".$immigration_filename];
    
            //Offer letter in docx format
            $word_filename = $request['word_filename'];
            $word_fullpath = $filepath.$word_filename;
            move_uploaded_file($_FILES['word']['tmp_name'],$word_fullpath);
            $docx_offer_letter = ['filename' => $word_filename, 'path' => "../../offer_letter/".$word_filename];
    
            $response = compact('offer_letter','immigration_offer_letter','docx_offer_letter');
    
            return json_encode($response);
          }
          catch (Exception $e) {
            Log::info($e);
            return json_encode(['error'=>'Error occured while generating the offer letter']);
          }
        }
    
        //For fetching the data for history page based on role of the Employee
        public function get_history_details(Request $request)
        {
            //For financial filter
            if($request['visa_process_financial_year'])
              $financial_year = array_map(function ($y) { return date("Y-m-d h:i:s",strtotime(trim($y))); },explode('to',$request['visa_process_financial_year']));
            else
              $financial_year = [
                (intval(date("m")) > 3 ? intval(date("Y")) : intval(date("Y"))-1)."-04-01 00:00:00",
                (intval(date("m")) > 3 ? intval(date("Y"))+1 : intval(date("Y")))."-03-31 00:00:00",
              ];
    
            if(Auth::User()->has_any_role(['hr_partner']))
              return $this->get_hr_partner_history_details($financial_year);	
            if(Auth::User()->has_any_role(['gm_reviewer']))
              return $this->get_gm_reviewer_history_details($financial_year);
            if(Auth::User()->has_any_role(['us_hr_reviewer']))
              return $this->get_us_hr_reviewer_history_details($financial_year);
        }
    
        public function get_hr_partner_history_details($financial_year)
        {
          $filter_creterias = [['method'=>'where','column'=>'pr.initiated_by','condition'=>'=','value'=>Auth::User()->aceid]];
          if(isset($financial_year)){
            array_push($filter_creterias,['method'=>'whereDate','column'=>'pr.created_at','condition'=>'>=','value'=>$financial_year[0]]);
            array_push($filter_creterias,['method'=>'whereDate','column'=>'pr.created_at','condition'=>'<=','value'=>$financial_year[1]]);
          }
          $request_details=$this->request_filter_details($filter_creterias,null,['request_code','employee_aceid','username','designation','department','manager','visa_type','request_type','client_name','petition','status','created_at','us_salary','one_time_bonus','next_salary_revision_on','hr_remarks'],$financial_year);
    
          $columns = [
            ['data' => 'request_code', 'title'=>'Request id'],
            ['data' => 'employee_aceid', 'title'=>'Employee aceid','class'=>'hide'],
            ['data' => 'username', 'title'=>'Username'],
            ['data' => 'designation', 'title'=>'Designation','class'=>'hide'],
            ['data' => 'department', 'title'=>'Department','class'=>'hide'],
            ['data' => 'manager', 'title'=>'Manager'],
            ['data' => 'visa_type', 'title'=>'Visa type'],
            ['data' => 'request_type', 'title'=>'Request type'],
            ['data' => 'client_name', 'title'=>'Client name'],
            ['data' => 'petition', 'title'=>'Petition'],
            ['data' => 'status', 'title'=>'Status'],
            ['data' => 'created_at', 'title'=>'Created at'],
            ['data' => 'us_salary', 'title'=>'Us salary','class'=>'hide'],
            ['data' => 'one_time_bonus', 'title'=>'One time bonus','class'=>'hide'],
            ['data' => 'next_salary_revision_on', 'title'=>'Next salary revision on','class'=>'hide'],
          ];
          
          return json_encode(['data'=>$request_details,'columns'=>$columns]);      
        }
        
        public function get_gm_reviewer_history_details($financial_year=null)
        {
          $filter_creterias =[['method'=>'where','column'=>'pr.status_id','condition'=>'>=','value'=>3]];
          if($financial_year){
            array_push($filter_creterias,['method'=>'whereDate','column'=>'pr.created_at','condition'=>'>=','value'=>$financial_year[0]]);
            array_push($filter_creterias,['method'=>'whereDate','column'=>'pr.created_at','condition'=>'<=','value'=>$financial_year[1]]);
          }
          $request_details=$this->request_filter_details($filter_creterias,null,['request_code','employee_aceid','username','designation','department','manager','visa_type','request_type','client_name','petition','status','created_at','minimum_wage','work_location','aspire_job_title','filing_type','gm_remarks','us_manager','inszoom_id','petitioner_entity','attorneys','','petition_file_date','receipt_no','petition_start_date','petition_end_date','interview_type','visa_ofc_date','visa_interview_date','travel_date','travel_location','traveling_type','record_number','most_recent_doe','admit_until','gc_initiated_on','initiated_by','first_name','last_name','gender','date_of_birth','date_of_joining','address','passport_no','education_category','education_qualification','india_experience','overall_experience','band_level']);
    
          $columns = [
            ['data' => 'request_code', 'title'=>'Request id'],
            ['data' => 'employee_aceid', 'title'=>'Employee aceid', 'class'=>'hide'],
            ['data' => 'username', 'title'=>'Username'],
            ['data' => 'designation', 'title'=>'Designation','class'=>'hide'],
            ['data' => 'department', 'title'=>'Department', 'class'=>'hide'],
            ['data' => 'manager', 'title'=>'Manager'],
            ['data' => 'visa_type', 'title'=>'Visa type'],
            ['data' => 'request_type', 'title'=>'Request type'],
            ['data' => 'client_name', 'title'=>'Client name'],['data' => 'petition', 'title'=>'Petition'],
            ['data' => 'status', 'title'=>'Status'],
            ['data' => 'created_at', 'title'=>'Created at'],
            ['data' => 'initiated_by', 'title'=>'Initiated by', 'class'=>'hide'],
            ['data' => 'first_name', 'title' => 'First name', 'class' => 'hide'],
            ['data' => 'last_name', 'title' => 'Last name', 'class' => 'hide'],
            ['data' => 'gender', 'title' => 'Gender', 'class' => 'hide'],
            ['data' => 'date_of_birth', 'title' => 'Date of birth', 'class' => 'hide'],
            ['data' => 'date_of_joining', 'title' => 'Date of joining', 'class' => 'hide'],
            ['data' => 'address', 'title'=>'Address', 'class' => 'hide'],
            ['data' => 'passport_no', 'title'=>'Passport number', 'class' => 'hide'],
            ['data' => 'education_category', 'title'=>'Graduation', 'class' => 'hide'],
            ['data' => 'education_qualification', 'title'=>'Course', 'class' => 'hide'],
            ['data' => 'india_experience', 'title'=>'Aspire experience', 'class' => 'hide'],
            ['data' => 'overall_experience', 'title'=>'Overall experience', 'class' => 'hide'],
            ['data' => 'band_level', 'title'=>'Band level', 'class' => 'hide'],
            ['data' => 'minimum_wage', 'title'=>'Minimum wage', 'class'=>'hide'],
            ['data' => 'work_location', 'title'=>'Work location','class'=>'hide'],
            ['data' => 'aspire_job_title', 'title'=>'Aspire job title','class'=>'hide'],
            ['data' => 'filing_type', 'title'=>'Filing type','class'=>'hide'],
            ['data' => 'gm_remarks', 'title'=>'Gm remarks','class'=>'hide'],
            ['data' => 'us_manager', 'title'=>'Us manager','class'=>'hide'],
            ['data' => 'inszoom_id', 'title'=>'Inszoom id','class'=>'hide'],
            ['data' => 'petitioner_entity', 'title'=>'Petitioner entity','class'=>'hide'],
            ['data' => 'attorneys', 'title'=>'Attorneys','class'=>'hide'],
            ['data' => 'petition_file_date', 'title'=>'Petition file date','class'=>'hide'],
            ['data' => 'receipt_no', 'title'=>'Receipt no','class'=>'hide'],
            ['data' => 'petition_start_date', 'title'=>'Petition start date','class'=>'hide'],
            ['data' => 'petition_end_date', 'title'=>'Petition end date','class'=>'hide'],
            ['data' => 'interview_type', 'title'=>'Interview type','class'=>'hide'],
            ['data' => 'visa_ofc_date', 'title'=>'Visa ofc date','class'=>'hide'],
            ['data' => 'visa_interview_date', 'title'=>'Visa interview date','class'=>'hide'],
            ['data' => 'travel_date', 'title'=>'Travel date','class'=>'hide'],
            ['data' => 'travel_location', 'title'=>'Travel location','class'=>'hide'],
            ['data' => 'traveling_type', 'title'=>'Traveling type','class'=>'hide'],
            ['data' => 'record_number', 'title'=>'Record number','class'=>'hide'],
            ['data' => 'most_recent_doe', 'title'=>'Most recent doe','class'=>'hide'],
            ['data' => 'admit_until', 'title'=>'Admit until','class'=>'hide'],
            ['data' => 'gc_initiated_on', 'title'=>'Gc initiated on','class'=>'hide']
          ];
          
          return json_encode(['data'=>$request_details,'columns'=>$columns]);          
        }
        
        public function get_us_hr_reviewer_history_details($financial_year = null)
        {
          $filter_creterias =[['method'=>'where','column'=>'pr.status_id','condition'=>'>','value'=>3]];
          if($financial_year){
            array_push($filter_creterias,['method'=>'whereDate','column'=>'pr.created_at','condition'=>'>=','value'=>$financial_year[0]]);
            array_push($filter_creterias,['method'=>'whereDate','column'=>'pr.created_at','condition'=>'<=','value'=>$financial_year[1]]);
          }
          $request_details = $this->request_filter_details($filter_creterias,null,['request_code','employee_aceid','username','designation','department','manager','visa_type','request_type','client_name','petition','status','created_at','initiated_by','salary_range_from','salary_range_to','us_job_title','green_card_title']);
    
          $columns = [
            ['data' => 'request_code', 'title'=>'Request id'],
            ['data' => 'employee_aceid', 'title'=>'Employee aceid','class'=>'hide'],
            ['data' => 'username', 'title'=>'Username'],
            ['data' => 'designation', 'title'=>'Designation','class'=>'hide'],
            ['data' => 'department', 'title'=>'Department','class'=>'hide'],
            ['data' => 'manager', 'title'=>'Manager'],
            ['data' => 'visa_type', 'title'=>'Visa type'],
            ['data' => 'request_type', 'title'=>'Request type'],
            ['data' => 'client_name', 'title'=>'Client name'],
            ['data' => 'petition', 'title'=>'Petition'],
            ['data' => 'status', 'title'=>'Status'],
            ['data' => 'created_at', 'title'=>'Created at'],
            ['data' => 'initiated_by', 'title'=>'Initiated by','class'=>'hide'],
            ['data' => 'salary_range_from', 'title'=>'Salary range from','class'=>'hide'],
            ['data' => 'salary_range_to', 'title'=>'Salary range to','class'=>'hide'],
            ['data' => 'us_job_title', 'title'=>'Us job title','class'=>'hide'],
            ['data' => 'green_card_title', 'title'=>'Green card title','class'=>'hide'],
          ];
    
          return json_encode(['data'=>$request_details,'columns'=>$columns]);
        }
    
        //To rendering the history page
        public function get_filter_details()
        {  
          $current_year = date("Y");
          $visa_process_financial_year_list=[];
          for($i=2021;$i<=$current_year;$i++)
          {
              $fin_year_start_date =date("d-M-Y", strtotime("04/01/".$i));
              $fin_year_end_date = date('d-M-Y', strtotime("03/31/".($i+1)));
              if($i+1 <= $current_year || intval(date("m"))>=4)
                $visa_process_financial_year_list["$fin_year_start_date to $fin_year_end_date"]  =  implode('-',[$i,$i+1]);
          }
          return View::make('layouts.visa_process.visa_process_history',['visa_process_financial_year_list'=>array_reverse($visa_process_financial_year_list,true)]);     
        }
        public function request_filter_details($filter_creterias, $orderby_values, $req_fields=[],$financial_year=[])
        {
          try {
            //Fetching the overall request details
            $request_filter_details = DB::table('visa_process_request_details as pr')
                    ->join('users as employee','employee.aceid','=','pr.employee_aceid')
                    ->join('users as man','man.aceid','=','employee.ReportingToACEID')
                    ->join('users as hr_partner','hr_partner.aceid','=','pr.initiated_by')
                    ->join('trd_departments as dept','dept.code','=','employee.DepartmentId')
                    ->join('visa_process_visa_type as vpvt','vpvt.id','=','pr.visa_type_id')
                    ->join('visa_request_type_master as vrtm','vrtm.id','=','pr.request_type_id')
                    ->join('trd_projects as vcm','vcm.customer_code','=','pr.client_code')
                    ->join('visa_petition_master as vpm','vpm.id','=','pr.petition_id')
                    ->join('visa_process_status as vps','vps.id','=','pr.status_id')
                    ->leftJoin('visa_process_employee_details as vped', 'vped.process_request_id','=','pr.id')
                    ->leftJoin('gender_master as gm', 'gm.id', '=', 'vped.gender_id')
                    ->leftJoin('education_category_master as ecm', 'ecm.id', '=', 'vped.education_category_id')
                    ->leftJoin('education_details_master as edm', 'edm.id', '=', 'vped.education_details_id')
                    ->leftJoin('visa_process_job_details as vpjd','vpjd.process_request_id','=','pr.id')
                    ->leftJoin('visa_job_titile_master as vjt','vjt.id','=','vpjd.job_titile_id')
                    ->leftJoin('visa_filing_master as vfm','vfm.id','=','vpjd.filing_type_id')
                    ->leftJoin('visa_process_review_details as vprd','vprd.process_request_id','=','pr.id')
                    ->leftJoin('users as us_manager','us_manager.aceid','=','vprd.us_manager_id')
                    ->leftJoin('visa_petitioner_entity_master as vpem','vpem.id','=','vprd.entity_id')
                    ->leftJoin('visa_attorneys_master as vam','vam.id','=','vprd.attorneys_id')
                    ->leftJoin('visa_job_titile_master as vjtm','vjtm.id','=','vprd.us_job_title_id')
                    ->leftJoin('visa_interview_type_master as vitm','vitm.id','=','vprd.visa_interview_type_id')
                    ->leftJoin('visa_process_travel_details as vptd','vptd.process_request_id','=','pr.id')
                    ->leftJoin('visa_travel_type_master as vttm','vttm.id','=','vptd.traveling_type_id')
                    ->leftJoin('visa_process_tracking_details as vptr','vptr.process_request_id','=','pr.id')
                    ->select('pr.request_code','pr.employee_aceid','employee.username','employee.DesignationName as designation','dept.name as department','man.username as manager','hr_partner.username as initiated_by','vpvt.visa_type','vrtm.request_type','vcm.customer_name as client_name','vpm.name as petition','vps.name as status',DB::raw('DATE_FORMAT(pr.created_at,"%d-%b-%Y") as created_at'),'vpjd.minimum_wage','vpjd.work_location','vjt.name as aspire_job_title','vfm.filing_type','vpjd.gm_remarks','vprd.acceptance_by_user','us_manager.username as us_manager','vprd.inszoom_id','vpem.petitioner_entity','vam.name as attorneys',DB::raw('DATE_FORMAT(vprd.petition_file_date,"%d-%b-%Y") as petition_file_date'),'vprd.receipt_no',DB::raw('DATE_FORMAT(vprd.petition_start_date,"%d-%b-%Y") as petition_start_date'),DB::raw('DATE_FORMAT(vprd.petition_end_date,"%d-%b-%Y") as petition_end_date'),DB::raw("CAST(AES_DECRYPT(vprd.salary_range_from,UNHEX(SHA2('".$this->encryption_key."',512))) as DECIMAL) as salary_range_from"),DB::raw("CAST(AES_DECRYPT(vprd.salary_range_to,UNHEX(SHA2('".$this->encryption_key."',512))) as DECIMAL) as salary_range_to"),'vjtm.name as us_job_title',DB::raw("CAST(AES_DECRYPT(vprd.us_salary,UNHEX(SHA2('".$this->encryption_key."',512))) as DECIMAL) as us_salary"),DB::raw("CAST(AES_DECRYPT(vprd.one_time_bonus,UNHEX(SHA2('".$this->encryption_key."',512))) as DECIMAL) as one_time_bonus"),DB::raw('DATE_FORMAT(vprd.next_salary_revision_on,"%d-%b-%Y") as next_salary_revision_on'),'vitm.type as interview_type',DB::raw('DATE_FORMAT(vprd.visa_ofc_date,"%d-%b-%Y") as visa_ofc_date'),DB::raw('DATE_FORMAT(vprd.visa_interview_date,"%d-%b-%Y") as visa_interview_date'),DB::raw('DATE_FORMAT(vptd.travel_date,"%d-%b-%Y") as travel_date'),'vptd.travel_location','vttm.name as traveling_type','vptr.record_number',DB::raw('DATE_FORMAT(vptr.most_recent_doe,"%d-%b-%Y") as most_recent_doe'),DB::raw('DATE_FORMAT(vptr.admit_until,"%d-%b-%Y") as admit_until'),DB::raw('DATE_FORMAT(vptr.gc_initiated_on,"%d-%b-%Y") as gc_initiated_on'),DB::raw("IF(vptr.green_card_title=1,'Yes','-') as green_card_title"),"vped.first_name","vped.last_name","gm.name as gender", DB::raw('DATE_FORMAT(vped.date_of_birth,"%d-%b-%Y") as date_of_birth'), DB::raw('DATE_FORMAT(vped.date_of_joining,"%d-%b-%Y") as date_of_joining'), 'vped.address', 'vped.passport_no', 'ecm.shortterm as education_category', 'edm.qualification as education_qualification', 'vped.india_experience', 'vped.overall_experience', 'vped.band_detail as band_level')
                    ->where('pr.active','=',1)
                    ->distinct();
    
              //Filter the details based on condition
              if(count($filter_creterias)){
                foreach($filter_creterias as $filters){
                  if($filters['condition'])
                  $request_filter_details=$request_filter_details->{$filters['method']}($filters['column'],$filters['condition'],$filters['value']);
                  else
                  $request_filter_details=$request_filter_details->{$filters['method']}($filters['column'],$filters['value']);
                }
              }
    
              $request_filter_details = json_decode(json_encode($request_filter_details->orderBy('pr.id','desc')->get()),true);
    
              //Filter only the required fields for the current role
              foreach ($request_filter_details as $key => $value)
                $request_filter_details[$key] = array_filter($value, function ($k) use ($req_fields) { return !count($req_fields) || in_array($k,$req_fields); }, ARRAY_FILTER_USE_KEY);      
              
              
              foreach ($request_filter_details as $key => $value){
                array_walk($value, function (&$element) { $element=$element==""?"-":$element; } );
                if(array_key_exists('india_experience', $value)){
                  $exp = explode('.', $value['india_experience']);
                  $year = array_key_exists(0, $exp) ? $exp[0] : 0;
                  $month = array_key_exists(1, $exp) ? $exp[1] : 0;
                  $value['india_experience'] = implode(" ",[$year, 'year(s)', 'and', $month, 'month(s)']);
                }
                if(array_key_exists('overall_experience', $value)){
                  $exp = explode('.', $value['overall_experience']);
                  $year = array_key_exists(0, $exp) ? $exp[0] : 0;
                  $month = array_key_exists(1, $exp) ? $exp[1] : 0;
                  $value['overall_experience'] = implode(" ",[$year, 'year(s)', 'and', $month, 'month(s)']);
                }
                $request_filter_details[$key] = $value;
              }
              
              return $request_filter_details;
            }
            catch (Exception $e) {
              Log::info($e);
              return View::make('layouts.visa_process.request',['error'=>'Error occured while fetching data. Please write to help.mis@aspiresys.com for assistence']);
            }
        }
    
        // For fetching user details
        public function get_employee_other_details(Request $request){
          try {
            $aceid = Auth::User()->aceid;
            // $aceid = "ACE10208";
            $request_code = $request->input('request_code');
            // $request_code = "2425VISA0002";
            $status_id = DB::table('visa_process_request_details')->where('request_code',$request['request_code'])->value('status_id');
            // $status_id = DB::table('visa_process_request_details')->where('request_code',$request_code)->value('status_id');
            $details = [];
            if($status_id==2 && Auth::User()->has_any_role('visa_user'))
            {
              $attributes_required = ['USRATTR_29', 'USRATTR_30', 'USRATTR_37'] ;
              $data = json_decode(DB::table('trf_user_detail_mapping as udm')
                          ->join('users as u', 'u.aceid', 'udm.aceid')
                          ->select(DB::raw('JSON_MERGE_PRESERVE( JSON_OBJECTAGG(udm.attribute_name, udm.mapping_value), JSON_OBJECT("DateOfBirth", u.DateOfBirth, "DateOfJoining", u.JoiningDate, "LevelName", u.LevelName)) as proof_details'))
                          ->where([['udm.aceid', $aceid], ['udm.active', 1]])
                          ->whereIn('udm.attribute', $attributes_required)
                          ->value('proof_details')
              , true);

              $data["DateOfJoining"] = array_key_exists("DateOfJoining", $data) ? date('d-M-Y', strtotime($data["DateOfJoining"])) : null;
              $data["DateOfBirth"] = array_key_exists("DateOfBirth", $data) ? date('d-M-Y', strtotime($data["DateOfBirth"])) : null;

              $key_names = ["address" => "Address", "band_details" => "LevelName", "dob" => "DateOfBirth", "doj" => "DateOfJoining", "first_name" => "NameInPassport", "passport_no" => "PassportNumber"];
    
              $details = array_map( function ($e) use($data) {
                if(array_key_exists($e, $data))
                  return $data[$e];
                else
                  return null;
              }, $key_names );
            }
            return json_encode($details);
          }
          catch (Exception $e) {
            Log::info("Error occured in loading the user details for request code - ".$request['request_code']);
          }
        }
    
        public function update_data($base,...$replacements)
        {
            $result = $base ?? [];
            foreach ($replacements as $replacement){
                if(!is_array($replacement))
                  continue;
              
                $changed_data = is_array($replacement) ? array_diff_assoc($replacement,$result) : $result;
                if(array_key_exists('updated_at',$result)){
                    if(array_key_exists('updated_at',$changed_data) && date($result['updated_at']) <= date($replacement['updated_at']))
                        $result = array_replace($result,$changed_data);
                    else
                        $result = array_replace($changed_data,$result);
                }
                else{
                    $result = array_replace($result,$changed_data);
                }
            }
    
            return $result;
        }
    
        public function populatingUsersList(){
          //dd("Test");
          ini_set('max_execution_time',7200);
          $soap = new SoapWrapper();
          SoapWrapper::add ( function ($service) {
            $service->name ( 'idmservice' )->wsdl ('http://idmsynergitaws.aspiresys.com/IDMWebService.asmx?WSDL')->trace ( true );
          } );
          $users_list = [];
          if(Auth::User()->has_any_role(['visa_hr_admin'])){
            $data = array (
              'date' => " ",
              'userAceNumber' => 'ACE0089',
              'relationid' => +1
            );
          }else{
            $data = array (
              'date' => " ",
              'userAceNumber' => Auth::User()->aceid,
              'relationid' => +1
            );
          }
          // $response = $soap->call('GetEmployeeList', $data);

          // dd($response);
          SoapWrapper::service ( 'idmservice', function ($service) use($data) {
            $usersDetailsList = simplexml_load_string ( $service->call ( 'GetEmployeeList', $data ) );
            if(!is_bool($usersDetailsList)){
              foreach ( $usersDetailsList as $userDetails ) {
                $aceid = ( string ) $userDetails->attributes ()['ACEID'];
                $name = (string)$userDetails->Name->attributes()['UserName'];
                $this->users_list[$aceid] = $aceid ." - ". $name;
              }
            }else{
              $this->users_list[]=array();
            }
          });
        return $this->users_list;
      }
    
      //Get the master details based on the input
      public function fetch_master_details(Request $request){
          $visa_type_id = $request["visa_type_id"];
          $request_type = DB::table('visa_process_visa_type')->where('id',$visa_type_id)->value('visa_type');
    
          if($request_type == "New L1")
            return json_encode(DB::table('visa_petition_master')->where('active',1)->where('name','<>','Client Location')->pluck('name','id'));
          else 
            return json_encode(DB::table('visa_petition_master')->where('active',1)->pluck('name','id'));
      }
    
      public function get_education_details(Request $request){
        $education_category_id = $request['education_category_id'];
    
        return json_encode(DB::table('education_details_master')->where('active',1)->where('category_id',$education_category_id)->pluck('qualification','id'));
      }
    
      //upload the files to server
      public function upload_files(Request $request){
        try {
          $session_id = session()->getId();
          $file = $request->file("file");
          $extension = substr($file->getClientOriginalName(), strpos($file->getClientOriginalName(),'.')+1);
          $tmp_name = $session_id."_".time().'.'.$extension;
          $file_size = $file->getSize();
          $file->move(public_path("temp"),$tmp_name);
    
          $data_need_to_insert = [
            'id' => null,
            'session_id' => $session_id,
            'request_code' => $request['edit_id'],
            'temporary_file_name' => $tmp_name,
            'original_file_name' =>  $file->getClientOriginalName(),
            'actual_file_name' => null,
            'uploaded_by' => Auth::User()->aceid,
            'active' => 1,
            'created_at' => date('Y/m/d h:i:s'),
            'updated_at' => date('Y/m/d h:i:s'),
          ];
    
          DB::table('temp_file_details')->insert($data_need_to_insert);
    
          return json_encode(["fileName"=>$file->getClientOriginalName(), "filePath" => "/temp/$tmp_name", "fileSize" =>$file_size , "tempName" => $tmp_name]);
        }
        catch (Exception $e) {
          Log::info("Error in uploading the file for request code - ".$request['edit_id']);
          return json_encode(['error'=>'Error in uploading the file']);
        }
      }
    
      //Delete the files from server
      public function delete_file(Request $request)
      {
        try {
          $file_path = $request["filePath"];
          $file_name = $file_path ? basename($file_path) : null;
          $type = $request['type'];
    
          if(!file_exists(public_path($file_path)))
            return json_encode(["message"=>"File doesn't exists"]);
    
          unlink(public_path($file_path));
          
          $process_request_id = DB::table('visa_process_request_details')->where('request_code',$request['edit_id'])->value('id');
          extract($this->get_uploaded_file_details($request['edit_id']));
          if($request['type'] == "passport-file")  $passport_file_path = implode(',',array_filter(explode(',',$passport_file_path),function($e) use($file_name){ return basename($e)!=$file_name;} ));
          if($request['type'] == "cv-file") $cv_file_path = implode(',',array_filter(explode(',',$cv_file_path), function ($e) use($file_name) { return basename($e)!=$file_name; } ));
          if($request['type'] == "degree-file") $degree_file_path = implode(',',array_filter(explode(',',$degree_file_path), function ($e) use($file_name) { return basename($e)!=$file_name; } ));
          if($request['type'] == "petition-file") $petition_file_path = implode(',',array_filter(explode(',',$petition_file_path), function ($e) use($file_name) { return basename($e)!=$file_name; } ));
          if($request['type'] == "visa-file") $visa_file_path = implode(',',array_filter(explode(',',$visa_file_path), function ($e) use($file_name) { return basename($e) != $file_name; }));
          $updated_at = date("Y-m-d h:i:s", time());
          if(in_array($type, ['passport-file','cv-file','degree-file'])){
            $values_to_update = compact("passport_file_path","cv_file_path","degree_file_path","updated_at");
            DB::table('visa_process_employee_details')->where('process_request_id',$process_request_id)->update($values_to_update);
          }    
          if(in_array($type, ['petition-file','visa-file'])){
            $values_to_update = compact("petition_file_path","petition_file_path","updated_at");
            DB::table('visa_process_review_details')->where('process_request_id',$process_request_id)->update($values_to_update);
          }
          return json_encode(["message" => "File removed successfully!"]);
        }
        catch (Exception $e) {
          Log::info($e);
          return json_encode(["error" => "Error occured in deleting the file"]);
        }
      }
    
      //Move the file from temparory location to actual location
      public function move_temp_file($file_names,$edit_id,$type)
      {
        $new_file_names = []; $file_names = array_merge($this->get_uploaded_file_details($edit_id,$type),explode(",",$file_names));
        foreach($file_names as $index => $file_name)
        {
          if($file_name && file_exists(public_path()."/temp/$file_name"))
            $file_path = public_path()."/temp/$file_name";
          else if($file_name && file_exists(public_path($file_name)))
            $file_path = public_path($file_name);
          else 
            continue;
          $ext = pathinfo($file_path, PATHINFO_EXTENSION);
          $count = intval($index)+1;
          $aceid = DB::table('visa_process_request_details')->where('request_code',$edit_id)->value('employee_aceid');
          $new_file_name = "/visa_uploaded_documents/".implode("_",[$edit_id,$aceid,$type,$count.".".$ext]);
          rename($file_path,public_path($new_file_name));
          array_push($new_file_names,$new_file_name);
          if(DB::table('temp_file_details')->where('temporary_file_name',$file_name)->exists())
            DB::table('temp_file_details')->where('temporary_file_name',$file_name)->update(['actual_file_name'=>basename($new_file_name)]);
        }
        return implode(",",$new_file_names);
      }
    
      //Fetch the uploaded files details
      public function get_uploaded_file_details($edit_id,$type=null)
      {
        $file_details = json_decode(json_encode(DB::table('visa_process_request_details as vprd')
                ->leftJoin('visa_process_employee_details as vped','vped.process_request_id','vprd.id')
                ->leftJoin('visa_process_review_details as vprw','vprw.process_request_id','vprd.id')
                ->select('vped.passport_file_path','vped.cv_file_path','vped.degree_file_path','vprw.petition_file_path','vprw.visa_file_path')
                ->where('vprd.request_code',$edit_id)->first()),true) ?? [];    
        if($type) 
          return $file_details[$type."_file_path"] ? explode(",",$file_details[$type."_file_path"]) : [];
        return $file_details;
      }
    
      //To get secure key for encrypting the sensitive data
      function get_secure_key()
      {
        $connection = mysqli_connect('172.24.113.79', 't_keyadmin', 'Encrypt@sp1rekey', 'encrypted_key_details');
        $sql = "SELECT * FROM travel_key_details WHERE config_name = 'visa_secure_key' AND active = 1";
        $result = $connection->query($sql);
        $temp = $result->fetch_assoc();
        return is_array($temp) && array_key_exists('config_value', $temp) ? $temp['config_value'] : false;
      }
}
