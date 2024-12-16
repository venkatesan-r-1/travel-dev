<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use View;
use \Crypt;
use App\Models\trf_travel_request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Artisan;
use Session;

class TravelRequest extends Controller
{
    public function request_redirect(Request $request){
        $visa_request_id = $request->input('visa_request_id');
        $username = Auth::user()->username;
        $aceid = Auth::User()->aceid;
        $module=$request->module;
        $status=0;
        $detailsProvider = new DetailsProvider(); 
        $user_details = json_decode(json_encode(DB::table('users')->select('username','email')->where([['active',1],['aceid',$aceid]])->first()),true);
        $user_other_details = DB::table('trf_user_detail_mapping')
            ->where([['active',1],['aceid',$aceid]])->orderBy('id')
            ->pluck('mapping_value','attribute_name')->toArray();

        // To load the default project for functional department
        $default_project = null;
        $user_department = Auth::User()->DepartmentId;
        if(in_array($user_department, $this->FUNC_SALES_DEPT_CODES)){
            $default_project = $this->CONFIG['DEFAULT_PROJECT']['FUNCTIONAL'];
        }

        $field_details = $detailsProvider->fields_visibility_editablity_details($module, $status, Auth::user()->respective_roles_code());
        $field_attr=json_decode(json_encode($field_details['field_attr']),true);
        $field_attrbutes=array_combine(array_column($field_attr,'unique_key'), $field_attr);
        $field_details['field_attr']=$field_attrbutes;
        //dd(DB::table('trd_entity')->where('unique_key',Auth::user()->SourceCompanyID)->value('entity_name'));

        $visa_link_details=null;
        if(isset($visa_request_id)) {
            $visaRequest = new VisaRequest();
            $visa_link_details = $visaRequest->get_travel_link_details($visa_request_id);
        }
        return  View::make('layouts.travel_common')->with([
            'field_details'=>$field_details,
            'module'=>$module,
            'orgin'=>$detailsProvider->list_user_orgin(Auth::user()->aceid),
            'user_details' => $user_details,
            'user_entity' => DB::table('trd_entity')->where('unique_key',Auth::user()->SourceCompanyID)->value('entity_name'),
            'user_other_details' => $user_other_details,
            'user_department'=>Auth::User()->DepartmentId,
            'status'=>$status,
            'proof_type_list' => $detailsProvider->list_proof_type($module,'India'),
            'default_project' => $default_project,
            'visa_link_details' => $visa_link_details,
        ]);
    } 
    public function request_page($id = null){
        $visa_request_id = session()->pull('visa_request_id');
        $visa_link_details=null;
        if(isset($visa_request_id)){
            $visa_request_id = Crypt::decrypt($visa_request_id);
            $visaRequest = new VisaRequest();
            $visa_link_details = $visaRequest->get_travel_link_details($visa_request_id);
            $visa_link_details['visa_request_id'] = $visa_request_id;
        }
        $traveller_id = Auth::user()->aceid;
        $detailsProvider = new DetailsProvider(); 
        $full_details = [];
        $edit_id = null;
        $travel_common_blade='';
        $module='MOD_02';
        if(basename(url()->current()) == "visa_request")
            $module='MOD_03';
        $status=0;$user_entity='';
        $from_country=$detailsProvider->list_user_orgin($traveller_id);
        $origin_city_list=$detailsProvider->list_city($from_country);
       //dd($origin_city_list);
        
       // $origin_city=DB::table('users')->where('aceid',$traveller_id)->value('OfficeLocation');
        $behalf_of_status = $detailsProvider->check_behalf_of_status(Auth::User()->aceid);
        // To load the default project for functional department
        $default_project = null;
        if(is_null($id)){
            $user_department = Auth::User()->DepartmentId;
            if(in_array($user_department, $this->FUNC_SALES_DEPT_CODES)){
                $default_project = $this->CONFIG['DEFAULT_PROJECT']['FUNCTIONAL'];
            }
        }
        
        if($id){
            $edit_id=Crypt::decrypt($id);
            $request_details=DB::table('trf_travel_request')->where('id',$edit_id)->first();
            $has_view_access=$this->check_permissions_on_request($request_details);
            // Provide access to related request reviewers...
            if(!$has_view_access) {
                $provider = new DetailsProvider();
                $related_request_ids = $provider->get_linked_request_by_module($edit_id, $module, true);
                foreach($related_request_ids as $rel_id) {
                    $related_request_details = DB::table('trf_travel_request')->where('id', $rel_id)->first();
                    $has_view_access = $this->check_permissions_on_request($related_request_details);
                    if($has_view_access) {
                        $has_view_access = true; break;
                    }
                }
            }
            if(!$has_view_access)
             return view('layouts.unauthorised');
            $traveling_details=DB::table('trf_traveling_details')->where('request_id',$edit_id)->where('active',1)->first();
            //dd($request_details,$traveling_details);
            $module=$request_details->module;//DB::table('trf_travel_request')->where('id',$edit_id)->value('module');
            $status=$request_details->status_id;//DB::table('trf_travel_request')->where('id',$edit_id)->value('status_id');
            $full_details=$detailsProvider->request_full_details($edit_id);
            $user_entity=$request_details->requestor_entity;//DB::table('trf_travel_request')->where('id',$edit_id)->value('requestor_entity');
            $from_country=$traveling_details->from_country;
            $origin_city_list=$detailsProvider->list_city($from_country);
            $origin_city=$traveling_details->origin_city;
            $traveller_id=$request_details->travaler_id;
            $default_origin=$detailsProvider->list_user_orgin($traveller_id);
            $request_for=$request_details->request_for_code;
            $behalf_of_status = $detailsProvider->check_behalf_of_status(Auth::User()->aceid, $edit_id);
        }
        //dd($traveller_id);
        $field_details = $detailsProvider->fields_visibility_editablity_details($module, $status, Auth::user()->respective_roles_code(),$edit_id);
        //dd($traveller_id);;
        $field_attr=json_decode(json_encode($field_details['field_attr']),true);
        $field_attrbutes=array_combine(array_column($field_attr,'unique_key'), $field_attr);
        $field_details['field_attr']=$field_attrbutes;
        // related visa request ids
        $related_visa_request = null;
        if( isset($edit_id) && $module == "MOD_02" &&  isset($status) && !in_array($status, [0,'STAT_01']))
            $related_visa_request = $detailsProvider->get_linked_request_by_module($edit_id, $module);

        // Whether the user have access to update the travel date
        $can_extend_date = false;
        $extend_date_not_needed_status = ['0','STAT_01', 'STAT_23', 'STAT_15', 'STAT_16', 'STAT_17', 'STAT_18', 'STAT_19', 'STAT_20', 'STAT_21', 'STAT_22', 'STAT_26', 'STAT_27', 'STAT_34', 'STAT_36'];
        if(isset($edit_id) && !in_array($status, $extend_date_not_needed_status)) {
            $from_country = DB::table('trf_traveling_details')->where([['request_id', $edit_id],['active', 1]])->value('from_country');
            $travel_extend_users = $this->get_users_can_extend_dates([
                'request_id' => $edit_id,
                'module' => $module,
                'from_country' => $from_country,
            ]);
            if(count($travel_extend_users) && in_array(Auth::User()->aceid, $travel_extend_users))
                $can_extend_date = true;
        }
        // Whether the user have access to cancel the travel request
        $can_cancel_travel = $detailsProvider->can_cancel_travel($edit_id);
        $is_onsite_travel = $this->is_onsite_travel($edit_id);
        return view('layouts.request',[
            'edit_id'=>$edit_id,
            'field_details'=>$field_details,
            'module'=>$module,
            'origin_city_list'=>$origin_city_list,
            'origin_city'=>isset($origin_city)?$origin_city:'',
            'orgin'=>$from_country,//$detailsProvider->list_user_orgin($traveller_id),
            'default_origin'=>isset($default_origin)?$default_origin:$from_country,
            'user_entity' => $user_entity,
            'is_behalf_user' => $behalf_of_status['behalf_of_user'],
            'is_behalf_of_request' => $behalf_of_status['behalf_of_request'],
            'default_project' => $default_project,
            'request_details'=>array_key_exists('request_details',$full_details)? $full_details['request_details'] : [],
            'travelling_details'=>array_key_exists('travelling_details',$full_details)? $full_details['travelling_details'] : [],
            'proof_details'=>array_key_exists('proof_details',$full_details)? $full_details['proof_details'] : [],
            'anticipated_details'=> array_key_exists('anticipated_details',$full_details)? $full_details['anticipated_details'] : [],
            'billable_details' => array_key_exists('billable_details',$full_details)? $full_details['billable_details'] : [],
            'forex_details' => array_key_exists('forex_details',$full_details)? $full_details['forex_details'] : [],
            'approval_flow' => array_key_exists('approval_flow',$full_details)? $full_details['approval_flow'] : [],
            'approval_tracker_details'=> array_key_exists('approval_tracker_details',$full_details)? $full_details['approval_tracker_details'] : [],
            'status_details' => array_key_exists('status_details', $full_details) ? $full_details['status_details'] : [],
            'is_ticket_processed' => array_key_exists('is_ticket_processed', $full_details) ? $full_details['is_ticket_processed'] : false,
            'is_forex_processed' => array_key_exists('is_forex_processed', $full_details) ? $full_details['is_forex_processed'] : false,
            'visa_link_details'=>$visa_link_details,
            'related_visa_request'=>$related_visa_request,
            'can_extend_date' => $can_extend_date,
            'can_cancel_travel' => $can_cancel_travel,
            'is_onsite_travel' => $is_onsite_travel,
        ]);
    }

    public function anticipated_cost(){
        $detailsProvider = new DetailsProvider(); 
        $departments = $detailsProvider->list_departments();
        $currency = $detailsProvider->list_currency();
        $selected_dept = DB::table('trf_travel_request')->where('id',30)->value('department_code');
        $master_category = $detailsProvider->list_master_category($selected_dept);
        return view('layouts.anticipated_cost',['master_category'=>$master_category,'currency'=>$currency,'selected_dept'=>$selected_dept]);
    }

     /**
     * function used to save or update the travel request
     * @param $request consist of form objects which is used to save/update the request
     */
    public function save_or_update(Request $request){
        $request=$request->toArray();
        //configuration to update tables
        $configuration_column=['trf_traveling_details'=>'travelling_details_row_id','trf_travel_request_proof_details'=>'request_for_code',
        'trf_travel_anticipated_details'=>'anticipated_row_id','trf_forex_load_details'=>'forex_details_row_id'];
        $configuration_tables=['trf_travel_anticipated_details'];
        //$excludable keys for already existing table
        $excludable_columns=['created_at','created_by','request_id'];
        
        try{
            DB::beginTransaction();
            if(array_key_exists('on_behalf',$request) && $request['on_behalf']){//property_exists('on_behalf',$request)
                $traveler_id=$request['on_behalf'];
            }else{
                if(!(isset($request['from_mail_approval'])))
                $traveler_id=Auth::User()->aceid;
                else
                $traveler_id='-';
            }

            //$approval_flow_config=$this->getApprovalFlow();
            
            if(isset($request['edit_id'])){
                $request_id=(int)$request['edit_id'];
                $request_related_details = DB::table('trf_travel_request')->where('id',$request['edit_id'])->first();
                $module=isset($request['module'])?$request['module']:$request_related_details->module;
    		    $current_status=$request_related_details->status_id;
                $department_code = isset($request['department_code'])?$request['department_code']:$request_related_details->department_code;
                $request_id_to_fetch_next_flow=$request['edit_id'];
            }else{
                $request_id=$this->get_request_unique_code($request['module']);
                $module=isset($request['module'])?$request['module']:null;
                $request_id_to_fetch_next_flow=null;
                $department_code=isset($request['department_code'])?$request['department_code']:null;
                $current_status='0';
            }
            $is_onsite_travel = false;
            if( in_array($current_status, ['0', 'STAT_01']) && $request['action'] == 'submit' && $request['origin'] != 'COU_014' ) {
                $is_onsite_travel = $this->is_onsite_travel(null, $request['origin']);
            }
            if(isset($request['from_date'])){
                for($i=0;$i<count($request['from_date']);$i++){
                    $request['from_date'][$i]=date('Y-m-d',strtotime($request['from_date'][$i]));
                }
                if(isset($request['to_date'])){
                    for($i=0;$i<count($request['to_date']);$i++){
                        $request['to_date'][$i]=date('Y-m-d',strtotime($request['to_date'][$i]));
                    }
                }
            }
            if(isset($request['transaction_date'])){
                for($i=0;$i<count($request['transaction_date']);$i++){
                    $request['transaction_date'][$i]=$request['transaction_date'][$i] ? date('Y-m-d h:i:s',strtotime($request['transaction_date'][$i])) : null;
                }
            }
            
		    $adult='';$child='';
            if(isset($request['adult'])){
                $adult=$request['adult'];
            }
            if(isset($request['child'])){
                $child=$request['child'];
            }
            $comments='';
            $forex_comments=[];
            if(isset($request['remarks'])){
                $comments=$request['remarks'];
            }
	    // For saving the forex comments in the table
            if(isset($request['comments'])){
                $forex_comments = $request['comments'];
            }
            if(isset($request['common_action_comments'])){
                $comments=$request['common_action_comments'];
            }
            $from_mail_approval=null;$action_user=null;
            if(isset($request['from_mail_approval'])){
                $from_mail_approval=1;
                $action_user=$request['action_user'];
            }
            $department_mapping_code=$this->get_respective_dept_group($module,$department_code);
            $next_action_details=$this->fetch_next_process_flow($request_id_to_fetch_next_flow,$module,$request['action'],$department_mapping_code,$current_status,$comments,$from_mail_approval,$action_user, is_onsite_travel: $is_onsite_travel);

            // dd($request_id_to_fetch_next_flow,$module,$request['action'],$department_mapping_code,$current_status,$comments);
            if(!array_key_exists('status',$next_action_details)){
                $message_text=$this->messages['en']['INVALID_ACTION'];
                return json_encode(['error'=>'INV_ACTION','message_text'=>$message_text]);
            }
            // dd($request['billed_to_client']?$request['billed_to_client']:NUll);
            $get_all_insert_update_details=[ 
                'module' => $module,
                'request_id' => $request_id,
                'request_for_code' => isset($request['request_for_code'])?$request['request_for_code']:NULL,
                'travel_purpose_id' => isset($request['travel_purpose_id'])?$request['travel_purpose_id']:NULL,
                'project_code' => isset($request['project_code'])?$request['project_code']:NULL,
                'department_code' => isset($request['department_code'])?$request['department_code']:NULL,
                'practice_unit_code' => (isset($request['practice_unit_code'])&&$request['practice_unit_code'])?$request['practice_unit_code']:NULL,
                'status_id' => $next_action_details['status'],//$next_action_details['status'],//$status_id
                'requestor_entity' =>isset($request['requestor_entity'])?$request['requestor_entity']:'',//'Aspire system STPI'
                'active' => '1',
                'billed_to_client'=> isset($request['billed_to_client'])?$request['billed_to_client']:NUll,
                'created_by' => isset($request['from_mail_approval'])?'-':Auth::User()->aceid,//
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),                
                'travaler_id'=>$traveler_id,
                'travel_type_id'=>isset($request['travel_type_id'])?$request['travel_type_id']:NULL,
                'travelling_details_row_id'=>isset($request['travelling_details_row_id'])?$request['travelling_details_row_id']:[],
                'visa_number' => isset($request['visa_number'])?$request['visa_number']:NULL,
                'visa_type_code' => isset($request['visa_type_code'])?$request['visa_type_code']:NULL,
                'visa_expiry_date' => NULL,
                'from_country'=>isset($request['origin'])?$request['origin']:NULL, 
                'origin_city'=>isset($request['origin_city'])?$request['origin_city']:NULL,                
                'from_city'=>isset($request['from_city'])?$request['from_city']:NULL,
                'to_city'=>isset($request['to_city'])?$request['to_city']:NULL,
                'to_country'=>isset($request['to_country'])?$request['to_country']:NULL,
                'from_date'=>isset($request['from_date'])?$request['from_date']:NULL,
                'to_date'=>isset($request['to_date'])?$request['to_date']:NULL,
                'ticket_required' => isset($request['ticket_required'])?$request['ticket_required']:NULL,
                'forex_required' => isset($request['forex_required'])?$request['forex_required']:NULL,
                'currency_code' => isset($request['currency_code'])?$request['currency_code']:NULL,
                'accommodation_required' => isset($request['accommodation_required'])?$request['accommodation_required']:NULL,
                'prefered_accommodation' => isset($request['prefered_accommodation'])?$request['prefered_accommodation']:NULL,
                'working_from' => isset($request['working_from'])?$request['working_from']:NULL,
                'laptop_required' => isset($request['laptop_required'])?$request['laptop_required']:NULL,
                'insurance_required' => isset($request['insurance_required'])?$request['insurance_required']:NULL,
                'family_traveling' => isset($request['family_traveling'])?$request['family_traveling']:NULL,
                'no_of_members' => isset($request['family_traveling'])?$adult.'&'.$child:NULL,
                'traveller_address' => isset($request['traveller_address'])?$request['traveller_address']:NULL,
                'phone_no' => isset($request['phone_no'])?$request['phone_no']:NULL,
                'email' => isset($request['email'])?$request['email']:NULL,
                'dob' => isset($request['dob']) ? date('Y-m-d',strtotime($request['dob'])) : NULL,
                'master_category'=>isset($request['master_category'])?$request['master_category']:NULL,
                'category'=>isset($request['category'])?$request['category']:NULL,
                'sub_category'=>isset($request['sub_category'])?$request['sub_category']:NULL,
                'anticipated_currency'=>isset($request['anticipated_currency'])?$request['anticipated_currency']:NULL,
                'amount'=>isset($request['amount'])?$request['amount']:NULL,
                'anticipated_comments'=>isset($request['anticipated_comments'])?$request['anticipated_comments']:NULL,
                'anticipated_row_id'=>isset($request['anticipated_row_id'])?$request['anticipated_row_id']:NULL,
                'excluded_row'=>isset($request['excluded_row'])?$request['excluded_row']:NULL,
                'budget_success'=>isset($request['budget_success'])?$request['budget_success']:NULL,
                'message'=>isset($request['message'])?$request['message']:NULL,
                'forex_details_row_id'=>isset($request['forex_details_row_id'])?$request['forex_details_row_id']:NULL,
                'transaction_date'=>isset($request['transaction_date'])?$request['transaction_date']:NULL,
                'mode_code'=>isset($request['mode_code'])?$request['mode_code']:NULL,
                'transaction_type'=>isset($request['transaction_type'])?$request['transaction_type']:NULL,
                'visa_process'=>isset($request['visa_process'])?$request['visa_process']:null,
                'visa_type'=>isset($request['visa_type'])?$request['visa_type']:null,
                'visa_renewal_options'=>isset($request['visa_renewal_options'])?$request['visa_renewal_options']:null,
                'exiting_date'=>isset($request['exiting_date'])?date('Y-m-d h:i:s',strtotime($request['exiting_date'])):null,
                'nationality'=>isset($request['nationality'])?$request['nationality']:null,
                'approver_currency_code'=>isset($request['approver_currency_code'])?$request['approver_currency_code']:null,
                'approver_anticipated_amount'=>isset($request['approver_anticipated_amount'])?$request['approver_anticipated_amount']:null,
            ];
            
            // $input_fields_keys=array_keys($request);
            $detailsProvider = new DetailsProvider(); 
            if(isset($request['from_mail_approval'])){
                //$get_editable_fields=['INP_064','INP_068','INP_077'];
                $get_visible_editable_fields=[];
                $get_visible_editable_fields['editable_fields']=['INP_064','INP_068','INP_077'];
            }
            else{
                $get_visible_editable_fields=$detailsProvider->fields_visibility_editablity_details($module,$current_status,Auth::user()->respective_roles_code(), (array_key_exists('edit_id', $request) ? $request['edit_id'] : null));
            // To add/remove the billable detail based on access 
            }
            $get_editable_fields=DB::table('trd_input_fields')->whereIn('unique_key',$get_visible_editable_fields['editable_fields'])->pluck('input_name')->toArray();            # to check budget validations
            $onsite_travel = $this->is_onsite_travel($request_id, isset($request['origin']) ? $request['origin'] : null);
            $budget_result = [];
            if( !$onsite_travel && ( in_array($next_action_details['status'],['STAT_12']) || in_array($request['action'],['desk_review_fin','desk_review_fac']) )){
                $budget_result = $detailsProvider->budget_verification($get_all_insert_update_details,$request_id,$next_action_details['status']);  
                if(count($budget_result)){
                    $get_all_insert_update_details['budget_success'] = isset($budget_result['budget_success'])?$budget_result['budget_success']:[];
                    $get_all_insert_update_details['message'] = isset($budget_result['message'])?$budget_result['message']:[];
                }
            }
            # budget validations ends
            //if its mail approval billable will be handled automatically
            if(isset($request['from_mail_approval'])){
                if(!is_null($request['billed_to_client']))
                    $get_editable_fields[]='billed_to_client';
            }
            else{
                $billable_key=array_search('billed_to_client',$get_editable_fields); 
                if(isset($request['edit_id'])&&Auth()->User()->has_any_role_code($this->CONFIG['BILLABLE_CHOOSE_ACCESS'])&&in_array($request_related_details->status_id,$this->CONFIG['BILLABLE_ENABLED_STATUS'])){
                    if(property_exists($request_related_details,'billed_to_client')&&is_null($request_related_details->billed_to_client)){
                        $get_all_insert_update_details['billed_to_client']=isset($request['billed_to_client'])?$request['billed_to_client']:null;
                        $get_editable_fields[]='billed_to_client';
                    }
                    else if(Auth::User()->has_any_role_code(['BF_REV'])&&$request_related_details->status_id=="STAT_05"){
                        $get_all_insert_update_details['billed_to_client']=isset($request['billed_to_client'])?$request['billed_to_client']:null;
                        $get_editable_fields[]='billed_to_client';
                    }
                    else if($request_related_details->created_by==Auth::User()->aceid&&$request_related_details->status_id=='STAT_01'){
                        $get_all_insert_update_details['billed_to_client']=isset($request['billed_to_client'])?$request['billed_to_client']:null;
                        $get_editable_fields[]='billed_to_client'; 
                    }
                    else{
                        if($billable_key !== false)
                        unset($get_editable_fields[$billable_key]);
                    }
                    
                }
                else if(!isset($request['edit_id'])&&Auth()->User()->has_any_role_code($this->CONFIG['BILLABLE_CHOOSE_ACCESS'])){
                    $get_all_insert_update_details['billed_to_client']=isset($request['billed_to_client'])?$request['billed_to_client']:null;
                    $get_editable_fields[]='billed_to_client';
                }
                else
                {
                    if($billable_key !== false)
                        unset($get_editable_fields[$billable_key]);
                }
            }

            // Save the visa request id
            // if(in_array($current_status, [0, 'STAT_01']) && isset($request['visa_request_id'])) {
            //     $get_all_insert_update_details['visa_request_id'] = $request['visa_request_id'];
            //     $get_editable_fields[]='visa_request_id';
            // }

            $input_with_tables=DB::table('trd_input_fields')->where('active',1)->select('table_to_insert')
            ->orderByRaw("FIELD(table_to_insert, 'trf_forex_load_details','trf_travel_anticipated_details','trf_travel_request','trf_traveling_details','trf_travel_other_details','trf_visa_request_details') asc")->whereIn('unique_key',$get_visible_editable_fields['editable_fields'])->groupBy('table_to_insert')->pluck('table_to_insert')->toArray();
            /**
             * action based tables to remove
             * $tables_to_remove=['ticket_process'=>'trf_forex_load_details']
             * if(array_key_exists($request['action'],$table_to_remove)){
             *      unset($input_with_tables[$request['action]]);
             * }
             */

             //action based tables to remove
             $tables_to_remove=['ticket_process'=>'trf_forex_load_details', 'save_ticket_process'=>'trf_forex_load_details'];
             if(array_key_exists($request['action'],$tables_to_remove)){
                   //unset method deletes the array value by key to get the key we use array search 
                  unset($input_with_tables[array_search($tables_to_remove[$request['action']],$input_with_tables)]);     
            }
            // dd($get_editable_fields);
           foreach($input_with_tables as $table_name){
            //to be removed soon after given table name to all the fields
                if($table_name){
                // the below array intersect key fetch all the details in the fields
                $values_to_insert=[];
                //below line is used to get all the column in the tables for insertOrupdate purpose
                $fields_to_add=Schema::getColumnListing($table_name);
                //to check which are all the editable fields to be saved/updated in the tables
                $values_to_insert = array_intersect_key($get_all_insert_update_details, array_flip(array_intersect($get_editable_fields,$fields_to_add)));
                //dd($values_to_insert,$get_editable_fields,$fields_to_add);
                //hard coded for some cases like adult and child
                if(array_intersect(['adult','child'],$get_editable_fields) && $table_name=='trf_travel_other_details'){
                    $values_to_insert['no_of_members']=$get_all_insert_update_details['no_of_members'];
                }
                if(array_intersect(['origin'],$get_editable_fields) && $table_name=='trf_traveling_details'){
                    $values_to_insert['from_country']=$get_all_insert_update_details['from_country'];
                    //dd($values_to_insert);
                }
                if(array_intersect(['origin_city'],$get_editable_fields) && $table_name=='trf_traveling_details'){
                    $values_to_insert['origin_city']=$get_all_insert_update_details['origin_city'];
                    //dd($values_to_insert);
                }
                $values_to_insert['active']=1;
              
                if(in_array($table_name,['trf_traveling_details','trf_travel_anticipated_details','trf_forex_load_details'])){
                    $ids_to_be_deleted = [];
                    if(in_array($table_name, ['trf_forex_load_details']))
                    {
                        $values_to_insert['comments'] = $forex_comments;
                    }

                    $max_count=count(max($values_to_insert));
                    if(isset($request['edit_id'])){
                        //to add the key value of multiple traveling route id for saved request
                        $values_to_insert['id']=$get_all_insert_update_details[$configuration_column[$table_name]];  
                        //could not pass empty [] values in whereIn condition, will return empty result
                        $ids_to_be_deleted=DB::table($table_name)->where('request_id',$request_id)->pluck('id')->toArray();
                        // for anticipated details no need to inactive rows it can be handled separately.
                        // action for travel desk returns only one row
                        if($table_name!='trf_travel_anticipated_details'){ 
                            $ids_to_be_deleted=array_diff($ids_to_be_deleted,$values_to_insert['id']);
                            unset($values_to_insert['created_at'],$values_to_insert['created_by']);
                            if(count($ids_to_be_deleted))
                            DB::table($table_name)->whereIn('id',$ids_to_be_deleted)->update(['active'=>0]);
                        }
                    }
                    $origin_city=isset($values_to_insert['origin_city'])&&$values_to_insert['origin_city']?$values_to_insert['origin_city']:NULL;
                    $temp_country=$origin_city;
                     for($i=0;$i<$max_count;$i++){
                         $insertOrUpdateArray = [];
                         $origin_city=$temp_country;
                     foreach($values_to_insert as $key=>$value){
                         if(is_array($value)){
                             if($key=='to_city'){
                                 $temp_country=$value[$i];
                             }
                             $insertOrUpdateArray[$key]=$value[$i];
                         } 
                         else{
                             //the condition has been added for: the "origin" is the from country and if the route is
                             //multiple then the from country to the next route is the previous country visited by the traveler
                             if($key=='origin_city'){
                                 $insertOrUpdateArray[$key]=$origin_city;
                                 Log::info('from country entry');
                                 //Log::info($from_country);
                             }else{
                                 $insertOrUpdateArray[$key]=$value;
                             }
                             
                         }   
                     }
                    }

                   $from_country=isset($values_to_insert['from_country'])&&$values_to_insert['from_country']?$values_to_insert['from_country']:NULL;
                   $temp_country=$from_country;
                    for($i=0;$i<$max_count;$i++){
                        $insertOrUpdateArray = [];
                        $from_country=$temp_country;
                    foreach($values_to_insert as $key=>$value){
                        if(is_array($value)){
                            if($key=='to_country'){
                                $temp_country=$value[$i];
                            }
                            $insertOrUpdateArray[$key]=$value[$i];
                        } 
                        else{
                            //the condition has been added for: the "origin" is the from country and if the route is
                            //multiple then the from country to the next route is the previous country visited by the traveler
                            if($key=='from_country'){
                                $insertOrUpdateArray[$key]=$from_country;
                            }else{
                                $insertOrUpdateArray[$key]=$value;
                            }
                            
                        }   
                    }
                        //$insertOrUpdateArray['id'] condition has been added if a new row has been inserted
                        if(isset($request['edit_id']) && $insertOrUpdateArray['id']){
                            DB::table($table_name)->where('id',$insertOrUpdateArray['id'])->update($insertOrUpdateArray);
                        }else{
                            DB::table($table_name)->insert($insertOrUpdateArray);
                        }     
                    }
                    }else{
                        if($request['edit_id']){
                            if(in_array($table_name,['trf_travel_request'])){
                                unset($values_to_insert['request_id']);
                                // dd($values_to_insert);
                                DB::table($table_name)->where('id',$request_id)->update($values_to_insert);
                            }else{
                                DB::table($table_name)->where('request_id',$request_id)->update($values_to_insert);
                            }
                        
                        }else{
                            DB::table($table_name)->insert($values_to_insert);
                        }
                    
                        if( in_array($table_name,['trf_travel_request'])){
                            if(isset($request['edit_id']))
                                $get_all_insert_update_details['request_id']=DB::table($table_name)->where('id',$request_id)->value('id');
                            else
                                $get_all_insert_update_details['request_id']=DB::table($table_name)->where('request_id',$request_id)->value('id');
                            $request_id=$get_all_insert_update_details['request_id'];
                        
                        //$get_all_insert_update_details['request_id']=$request_id;
                    }
                    }
                }
           }
           if(isset($request['proof_type']) && in_array('proof_type',$get_editable_fields)){
            $proof_related_details=[
            'proof_type' => $request['proof_type'],
            'proof_number' => $request['proof_number'],
            'proof_request_for' => $request['proof_request_for'],
            'proof_issued_place' =>$request['proof_issued_place'],
            'proof_issue_date' => $request['proof_issue_date'],
            'proof_expiry_date' => $request['proof_expiry_date'],
            'proof_file_path'=>$request['proof_file_path'],
            'proof_name'=>$request['proof_name'],
            'file_reference_id'=>$request['file_reference_id'],
            ];
           
        
            $max_count=count(max($proof_related_details));
            for($i=0;$i<$max_count;$i++){
                foreach($proof_related_details as $key=>$value){
                    if($value[$i]){
                        if(in_array($key,['proof_issue_date','proof_expiry_date']))
                            $proof_related_details[$key][$i] = date('Y-m-d',strtotime($value[$i]));
                    
                    }
                } 
            }  
            $return_value=$this->save_proof_details($request_id,$proof_related_details);

        }
        // Save the ticket file and its cost
        if(isset($request['ticket_file_path']) || isset($request['ticket_cost'])){
            // Get the data from request
            $file_path_list = $request['ticket_file_path'];
            //$cost_list = $request['ticket_cost'];
            
            $ticket_file_reference_id = isset($request['ticket_file_reference_id']) ? $request['ticket_file_reference_id'] : [];
            $travelling_details_row_ids = isset($request['travelling_details_row_id']) && is_array($request['travelling_details_row_id']) ? $request['travelling_details_row_id'] : [];
            if(isset($ticket_file_reference_id) && is_array($ticket_file_reference_id) && count($ticket_file_reference_id)){
                $ticket_file_reference_id = array_map(fn($e) => $e ? explode(',', $e) : [], $ticket_file_reference_id);
                $ticket_file_reference_id = array_merge(...$ticket_file_reference_id);
            }
            if(isset($file_path_list) && is_array($file_path_list) && count($file_path_list)){
                $file_path_list = array_map(fn($e) => $e ? explode(',', $e) : [], $file_path_list);
                $travelling_details_row_ids = array_map( fn($a, $b) => array_fill(0, count($b), $a), $travelling_details_row_ids, $file_path_list );
                $travelling_details_row_ids = array_merge(...$travelling_details_row_ids);
                $file_path_list = array_merge(...$file_path_list);
            }
            // dd($ticket_file_reference_id, $travelling_details_row_ids, $file_path_list);
            // Delete all the existing records
            DB::table('trf_traveling_ticket_file_details')->where('request_id', $request_id)->update(['active' => 0]);
            for($index=0;$index<count($file_path_list);$index++)
            {
                // if(!array_key_exists($index, $cost_list))
                //     break;
                
                $file_paths = $file_path_list[$index];
                // $in=0;
                // foreach($file_paths as $file_path){
                    
                   // $cost = $cost_list[$index];
                    $file_reference_id = array_key_exists($index, $ticket_file_reference_id) ? $ticket_file_reference_id[$index] : null;
                    // Get the file details from DB
                    $ticket_file_details = (array)DB::table('temporary_file_details')->where([['system_name', $file_paths],['active',1]])->first();
                    if(count($ticket_file_details) == 0) {
                        $ticket_file_details = (array)DB::table('trf_traveling_ticket_file_details')->where('system_name', basename($file_paths))->first();
                    }
                    $original_name = array_key_exists('original_name', $ticket_file_details) ? $ticket_file_details['original_name'] : null;
                    // In case already saved file
                    if(is_null($original_name)) {
                        $original_name = array_key_exists('original_file_name', $ticket_file_details) ? $ticket_file_details['original_file_name'] : null;
                    }
                    $system_name = array_key_exists('system_name', $ticket_file_details) ? $ticket_file_details['system_name'] : null;

                    $old_file_location = public_path("temp/$system_name");
                    // Get the request details
                    $requestDetails = DB::table('trf_travel_request')->where([['id', $request_id],['active',1]]);
                    $request_code_ticket = $requestDetails->value('request_id');
                    $traveler_id_ticket = $requestDetails->value('travaler_id');
                    // Config
                    $file_type = "Ticket";
                    $upload_path = "ticket_uploads";
                    // Create a new file name for uploaded file
                    $extension = array_key_exists('extension', pathinfo($old_file_location)) ? pathinfo($old_file_location)['extension'] : null;
                    $new_file_name_segments = [$request_code_ticket, $traveler_id_ticket, $file_type, ($index+1).'.'.$extension];
                    $new_file_name = implode('_', $new_file_name_segments);
                    $new_file_location = public_path("$upload_path/$new_file_name");
                    // Move the uploaded file to actual directory
                    if(isset($old_file_location) && is_file($old_file_location)){
                        rename($old_file_location, $new_file_location);
                    }
                    // Get data need to insert / update in DB
                    $data_to_insert = [
                        'request_id' => $request_id,
                        'traveling_id' => array_key_exists($index, $travelling_details_row_ids) ? $travelling_details_row_ids[$index] : null,
                        'original_file_name' => $original_name,
                        'system_name' => $new_file_name,
                        //'cost' => $cost,
                        'active' => 1,
                        'created_by' => Auth::User()->aceid,
                        'created_at' => date('Y-m-d h:i:s'),
                        'updated_at' => date('Y-m-d h:i:s'),
                    ];
                    // Get data need to check
                    $data_to_check = [
                        'id' => $file_reference_id,
                    ];
                    // Remove timestamps incase record exists
                    if(DB::table('trf_traveling_ticket_file_details')->where($data_to_check)->exists())
                        unset($data_to_insert['created_at']);
                    // Insert / update the data
                    DB::table('trf_traveling_ticket_file_details')->updateOrInsert($data_to_check, $data_to_insert);
                    
                // }
                //dd("test");
                    
            }
        }
                    
            // Provide domestic access to the user
            if( in_array($current_status, ['0', 'STAT_01']) && $module == "MOD_01" ) {
                $provider = new DetailsProvider();
                $provider->assign_role('DOM_REQ', $traveler_id);
            }

            DB::commit();
            if(isset($request['edit_id']))
            {
                //to fetch all the details from the request detail and pass it to the function
                // $get_request_details=DB::table('trf_travel_request')->where('id',$request_id)->select('department_code','module','request_id','project_code','practice_unit_code')->first();
                $get_request_details=DB::table('trf_travel_request as ttr')
                ->join('trf_travel_other_details as tod','tod.request_id','ttr.id')
                ->where('ttr.id',$request_id)
                ->select('ttr.department_code','ttr.module','ttr.travaler_id','ttr.billed_to_client','ttr.request_id','ttr.project_code','ttr.practice_unit_code','tod.ticket_required','tod.forex_required')->first();
                $anticipated_amount = DB::table('trf_travel_anticipated_details as tad')
                ->join('trf_currency_conversion_rates as ccr','tad.anticipated_currency','ccr.parent_currency')
                ->select(DB::raw("SUM(tad.amount*ccr.conversion_rate) as amount"))
                ->where([['tad.request_id',$request['edit_id']],['tad.active',1]])
                ->value(DB::raw("SUM(tad.amount*ccr.conversion_rate) as amount"));
                $traveling_details = DB::table('trf_traveling_details')->where([['request_id', $request_id],['active', 1]])->first();
                $get_all_details=(array)$get_request_details;
                $get_all_details['amount'] = $anticipated_amount;
                $get_all_details['from_country'] = isset($traveling_details->from_country) ? $traveling_details->from_country : null;
                $approval_matrix_fields=$this->perform_fields_for_approval_matrix($get_all_details,$request['edit_id']);
            }
            else
            {
             $approval_matrix_fields=$this->perform_fields_for_approval_matrix($get_all_insert_update_details);
            }
            
            $traveller_id=DB::table('trf_travel_request')->where('id',$request_id)->value('travaler_id');
            if($request['action']!='save')
             $fetch_approval_flow=$this->fetch_approval_flow($approval_matrix_fields,$traveller_id);
            if(!$request_id_to_fetch_next_flow) {
                $request_id_to_fetch_next_flow = $request_id;
            }
             $next_action_details1=$this->fetch_next_process_flow($request_id_to_fetch_next_flow,$module,$request['action'],$department_mapping_code,$current_status,$comments,$from_mail_approval,$action_user, is_onsite_travel: $is_onsite_travel);
             if(!array_key_exists('status',$next_action_details1)){
                $message_text=$this->messages['en']['INVALID_ACTION'];
                return json_encode(['error'=>'INV_ACTION','message_text'=>$message_text]);
            }
            else{
                if($next_action_details['status']!=$next_action_details1['status']){
                    DB::table('trf_travel_request')->where('id',$request_id)->update(['status_id'=>$next_action_details1['status']]);
                    $next_action_details=$next_action_details1;
                }
            }
             
            //status tracker code for the request
             $status_tracker=[
                'request_id'=>$request_id,
                'old_status_code'=>$current_status,
                'new_status_code'=>$next_action_details['status'],
                'action' => $request['action'],
                'created_by' => Auth::User()?Auth::User()->aceid:$request['action_user'],
                'billed_to_client'=>$get_all_insert_update_details['billed_to_client'],
                'comments'=> $comments,
                'active'=>1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
             ];

             DB::table('trf_request_status_tracker')->insert($status_tracker);
             //dd($fetch_approval_flow);
             //temprory
             if($next_action_details['status']=='STAT_01'){
                $msg='Request saved successfully.';
             }else{
                $msg='Request submitted successfully.';
             }
	   # added to show budget result in UI
            $next_action_details['budget_exceed_msg'] = (isset($budget_result['message'])&&is_array($budget_result['message'])) ? implode(",",$budget_result['message']) : '';
             if(array_key_exists('message',$next_action_details)){
                if(array_key_exists($next_action_details['message'],$this->messages['en'])){
                    $next_action_details['message_text']=$this->messages['en'][$next_action_details['message']];
                }
                else if(array_key_exists('ERR',$next_action_details))
                {
                    return json_encode(['error'=>'SAV_ERR','message_text'=>'Error has been occurred. Please try again and write to help.is@aspiresys.com if issue persists.']);

                }    
             }
             if($is_onsite_travel) {
                $next_action_details['redirect_url'] = '/home';
             }
            return json_encode([
                'next_action_details'=>$next_action_details,
                'action'=>$request['action'],
                'request_id'=>$request_id
            ]);

        }
        catch(\Exception $e){
            dd($e);
            DB::rollBack();
            Log::error('Save or update function error:'.$e->getMessage());
            return json_encode(['error'=>'SAV_ERR','message_text'=>'Error has been occurred. Please try again and write to help.is@aspiresys.com if issue persists.']);
        }

    }


    /**
     * to generate the request unique code int the travel system
     * @param string $module_code to decide visa or travel request
     * @return string $request_code
     */

     public function get_request_unique_code($module_code){

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
        
          $req_code=$fy2.$this->MODULE[$module_code];
           //request id increament process
           if(count(DB::table('trf_travel_request')->where('request_id','LIKE','%'.$req_code.'%')->get()->toArray()))
           {
               $short_not=DB::table('trf_travel_request')->where('request_id','LIKE','%'.$req_code.'%')->orderBy('request_id', 'DESC')->take(1)->first()->request_id;
               
               $num=(int)explode($this->MODULE[$module_code],$short_not)[1] + 1;
               $num=str_pad($num, 4, '0', STR_PAD_LEFT);
               $request_code=$req_code.$num;
           }
           //if request code is new set request code from 0001
          else
          {
              $request_code=$req_code.'0001';
          }
          return $request_code;

     }

     public function additional_fields_to_add($table_name=null,$column_name=array()){
        switch($table_name){


            default: return array();

        }
        
     }
    /**
     * To save the uploaded file in temp folder
     * @author venkatesan.raj
     * 
     * @param mixed $request
     * 
     * @return mixed
     */
    public function save_uploaded_file(Request $request)
    { 
        try
        {
            $file = $request->file('file');
            $file_size = $file->getSize();
            $request_id = $request->input('request_id') ?? null;
            $file_type = $request->input('type');
            $module = $request->input('module');
            $request_for = $request->input('requestFor');
            $original_name = $file->getClientOriginalName();
            $extension = substr($original_name, strrpos($original_name, '.'));
            $name = implode('_',[session()->getId(),time().$extension]);
            $file_path = 'temp';
            $file->move(public_path($file_path), $name);
            DB::table('temporary_file_details')
                ->insert([
                    'request_id' => $request_id,
                    'module' => $module,
                    'request_for_code' => $request_for,
                    'file_type_id' => $file_type,
                    'original_name' => $original_name,
                    'system_name' => $name,
                    'active' => 1,
                    'created_by' => Auth::User()->aceid,
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s'),
                ]);
            return json_encode([
                'file_name' => $original_name,
                'name' => $name,
                'file_path' => $file_path.'/'.$name,
                'file_size' => $file_size
            ]);
        }
        catch (Exception $e)
        {
            Log::error("Error occured while upload the file");
            Log::error($e);
        }
    }
    /**
     * To delete the uploaded file from temp folder
     * @author venkatesan.raj
     * 
     * @param mixed $request
     * @return mixed
     */
    public function delete_uploaded_file(Request $request)
    {
        try
        {
            $file_path = $request->input('filePath');
            $dir_name = dirname($file_path);
            $file_name = basename($file_path);
            $request_id = $request->input('edit_id') ?? null;
            if(file_exists(public_path($file_path)))
            {
                if($dir_name == '/temp' || $dir_name == "temp")
                    unlink(public_path($file_path));
                if($request_id)
                    DB::table('trf_request_proof_file_details')->where('system_name', $file_name)->where('request_id', $request_id)->update(['active'=>0, 'updated_at' => date('Y-m-d h:i:s')]);
                else
                    DB::table('temporary_file_details')->where('system_name', $file_name)->update(['active'=>0, 'updated_at' => date('Y-m-d h:i:s')]);
                return ["file_name" => $file_name];
            }
            return ["error" => "File doesn't exists"];
        }
        catch(\Expection $e) 
        {
            Log::error("Err in delete_uploaded_file");
            Log::error($e);
        }
    }
/**
     * To perform_fields_for_approval_matrix rules\
     * @auth ganesh.veilsamy
     * @added on 17-Dec-2023
     * @param $request 
     * @return void
     */
    public function perform_fields_for_approval_matrix($request_details,$request_id=null){
        try{
            $existing_travel_details=[];
            if($request_id){
                $existing_travel_details=DB::table('trf_travel_request')->select('id as request_id','department_code as department_id'
                ,'module','project_code','travaler_id as requestor','practice_unit_code as du_id')->where('id',$request_id)->first();

                $existing_travel_details=(array)$existing_travel_details;
                $existing_travel_details['anticipated_cost']=(isset($request_details['anticipated_cost'])?$request_details['anticipated_cost']:0);
                $existing_travel_details['billed_to_client']=(isset($request_details['billed_to_client'])?$request_details['billed_to_client']:1);
                $existing_travel_details['from_country'] = DB::table('trf_traveling_details')->where([['request_id', $request_id],['active', 1]])->orderBy('id', 'asc')->value('from_country');

                if($existing_travel_details['module'] == "MOD_03") {
                    $existing_visa_details = (array)DB::table('vrf_visa_request_details')
                                                ->select('visa_type', 'visa_category')
                                                ->where([['request_id', $request_id],['active', 1]])->first();
                    $existing_travel_details = array_merge($existing_travel_details, $existing_visa_details);
                }

                //dd($existing_travel_details);
                //$existing_travel_details=DB::table('trf_travel_request')->select()->where
                // $existing_travel_details=DB::table('trf_travel_request')
                // ->select()
            }
            $required_details_for_approval_matrix=[
                'request_id'=>isset($request_id)?$request_id:$request_details['request_id'],
                'department_id'=>isset($request_details['department_code'])?$request_details['department_code']:$existing_travel_details['department_code'],
                'module'=>$request_details['module'],
                'billed_to_client'=>(isset($request_details['billed_to_client'])?$request_details['billed_to_client']:1),
                'ticket_required'=>isset($request_details['ticket_required'])?$request_details['ticket_required']:null,
                'forex_required'=>isset($request_details['forex_required'])?$request_details['forex_required']:null,
                'project_code'=>$request_details['project_code'],
                'requestor'=>(isset($request_details['travaler_id']))?$request_details['travaler_id']:Auth::User()->aceid,
                'anticipated_cost'=>(isset($request_details['amount'])?$request_details['amount']:0),
                'du_id'=>$request_details['practice_unit_code'],
                'comments'=>isset($request_details['comments'])?$request_details['comments']:'',
                'visa_type' => array_key_exists('visa_type', $request_details) ? $request_details['visa_type'] : ( array_key_exists('visa_type', $existing_travel_details) ? $existing_travel_details['visa_type'] : null ),
                'visa_category' => array_key_exists('visa_category', $request_details) ? $request_details['visa_category'] : ( array_key_exists('visa_category', $existing_travel_details) ? $existing_travel_details['visa_category'] : null ),
                'from_country' => array_key_exists('from_country', $request_details) ? $request_details['from_country'] : ( array_key_exists('from_country', $existing_travel_details) ? $existing_travel_details['from_country'] : null ),
                ];
                return $required_details_for_approval_matrix;
        }
        catch(\Exception $e){
            dd($e);
            Log::error("error in perform_fields_for_approval_matrix");
            Log::error($e);
        }
    }
        
    /**
     * fetch_approval_flow -  To fetch the approval flow for the respective departments
     * @author ganesh.veilsamy
     * @addedon 2-Jan-2024
     * @return void
     */
    public function fetch_approval_flow($approval_matrix_fields,$traveller_id=null){
        try{
            $process_department_code=$this->get_respective_dept_group($approval_matrix_fields['module'],$approval_matrix_fields['department_id']);
            $respective_rules=DB::table('trd_approval_matrix_rule_config')
                ->where('module',$approval_matrix_fields['module'])
                ->where('department_mapping_id',$process_department_code)
                ->where('active',1)
                ->pluck('rule_id')->toArray();
            $respective_conditions=$this->check_against_conditions($approval_matrix_fields,$respective_rules,'trf_approval_matrix_rule_conditions','rule_config_code','condition_code');
            $respective_sub_conditions=$this->check_against_conditions($approval_matrix_fields,[$respective_conditions],'trf_approval_matrix_rule_sub_conditions','condition_code','sub_rule_code');
            $assign_approvers=$this->assign_approvers_for_request($approval_matrix_fields,$respective_sub_conditions,$traveller_id);
        }
        catch(\Exception $e){
            Log::error("Error in fetch_approval_flow");
            Log::error($e);
        }
    }
    
    /**
     * get_respective_dept_group - Get the respective grouping id for the selected department
     * @author ganesh.veilsamy added on 3-jan-2024
     * @param  mixed $module
     * @param  mixed $department
     * @return void
     */
    public function get_respective_dept_group($module,$department){
        try{
            $process_department_code='';
            $department_mappings=DB::table('trd_approval_matrix_dept_grouping')
                ->where('module',$module)
                ->where('active',1)->pluck('department_codes','unique_key')->toArray();
            if(count($department_mappings)){
                foreach($department_mappings as $mapping_id=>$department_mapping){
                    $department_mapping_array=explode(',',$department_mapping);
                    if(in_array($department,$department_mapping_array))
                        $process_department_code=$mapping_id;
                }
            }
            return $process_department_code;
        }
        catch(\Exception $e){
            Log::error("err in get_respective_dept_group function");
            Log::error($e);
        }
    }    
    /**
     * check_against_conditions
     * @author ganesh.veilsamy added on 12-Jan-2024
     * @param  mixed $data_to_check
     * @param  mixed $respective_rules
     * @param  mixed $table_to_check
     * @param  mixed $column_to_check
     * @param  mixed $field_to_get
     * @return void
     */
    public function check_against_conditions($data_to_check,$respective_rules,$table_to_check,$column_to_check,$field_to_get){
        try{
            $related_rules=DB::table($table_to_check)
            ->whereIn($column_to_check,$respective_rules)
            ->select('field','condition','value',$field_to_get.' as rule_pass_code')
            ->where('active',1)->get();
            $matched_conditions=[];$matched_condition='';$pass_count='';
            foreach($related_rules as $rules){
                $rule_pass=0;
                if($rules->condition=='equal_to'){
                    if(array_key_exists($rules->field,$data_to_check)&&$data_to_check[$rules->field]==$rules->value)
                    {
                        $rule_pass=1;    
                    }
                }
                else if($rules->condition=='not_equal_to'){
                    if(array_key_exists($rules->field,$data_to_check)&&$data_to_check[$rules->field]!=$rules->value)
                    {
                        $rule_pass=1;    
                    }
                }
                else if($rules->condition=='in'){
                    $rule_value_array=explode(',',$rules->value);
                    if(array_key_exists($rules->field,$data_to_check)&&in_array($data_to_check[$rules->field],$rule_value_array))
                    {
                        $rule_pass=1;    
                    }
                }
                else if($rules->condition=='not_in'){
                    $rule_value_array=explode(',',$rules->value);
                    if(array_key_exists($rules->field,$data_to_check)&&!in_array($data_to_check[$rules->field],$rule_value_array))
                    {
                        $rule_pass=1;    
                    }
                }
                else if($rules->condition=='greater_than'){
                    if(array_key_exists($rules->field,$data_to_check)&&$data_to_check[$rules->field]>=$rules->value)
                    {
                        $rule_pass=1;    
                    }
                }
                else if($rules->condition=='lesser_than'){
                    if(array_key_exists($rules->field,$data_to_check)&&$data_to_check[$rules->field] < $rules->value)
                    {
                        $rule_pass=1;    
                    }
                }
                if($rule_pass){
                    if(array_key_exists($rules->rule_pass_code,$matched_conditions))
                    $matched_conditions[$rules->rule_pass_code]+=1;
                else
                    $matched_conditions[$rules->rule_pass_code]=1;
                }
            }
            foreach($matched_conditions as $key=>$count){
                if($matched_condition){
                    if($pass_count<$count){
                        $matched_condition=$key;
                        $pass_count=$count;
                    }
                }
                else{
                    $matched_condition=$key;
                    $pass_count=$count;
                }
            }
            return $matched_condition;
        }
        catch(\Exception $e){
            dd($e);
            Log::error("Err in check_against_conditions");
            Log::error($e);
        }
        
    }
    
    /**
     * assign_approvers_for_request - Find the respective approvers for the flows
     * @author ganesh.veilsamy added on 17-jan-2024
     * @param  mixed $request_details
     * @param  mixed $sub_request_details
     * @return void
     */
    public function assign_approvers_for_request($request_details,$sub_request_details,$traveller_id=null){
        try{
            $current_date=date('Y-m-d H:i:s');
            $hierarychy_approvers=[];$covered_users=[];
            $request_user=$request_details['requestor'];
            $next_process_check=1;
            $user_detail_prv_obj=new DetailsProvider();
            $traveler_roles=DB::table('trf_user_role_mapping')->where('aceid',$traveller_id)->pluck('role_code')->toArray();
            // to avoid adding requestor's reportees to the approval flow.
            $request_user_reportees = DB::table('users')->where([['ReportingToACEID',$traveller_id],['active',1]])->pluck('aceid')->toArray();
            $respective_rule_flow_users=DB::table('trf_approval_matrix_flow_conditions')
                ->where('sub_cond_code',$sub_request_details)
                ->where('active',1)
                ->orderBy('order')
                ->get();
            $existing_flow=DB::table('trf_approval_matrix_tracker')
                ->where('active',1)
                ->where('request_id',$request_details['request_id']) //request id has been updated by barath to id
                ->pluck('flow_code')->toArray();
            $role_mapping_for_users=[
                'AN_COST_FIN'=>'AN_COST_FIN',
                'AN_COST_FAC'=>'AN_COST_FAC',
                'AN_COST_VISA'=>'AN_COST_VISA',
                // 'CL_PR'=>'CLI_PTR',
                'BF_REV'=>'BF_REV',
                // 'GEO_H'=>'GEO_H',
                'TRV_PROC_TICKET'=>'TRV_PROC_TICKET',
                'TRV_PROC_FOREX'=>'TRV_PROC_FOREX',
                'DOM_TCK_ADM'=>'DOM_TCK_ADM',
                // 'DEP_H'=>'DEP_H',
                // 'PRO_OW'=>'PRO_OW',
                // 'PRO_OW_HIE'=>'PRO_OW_HIE',
                // 'DU_H'=>'DU_H',
                // 'DU_H_HIE'=>'DU_H_HIE',
                //'FIN_APP'=>'FIN_APP',
                'TRV_PROC_VISA'=>'TRV_PROC_VISA',
                'VIS_USR'=>'VIS_USR',
                'GM_REV'=>'GM_REV',
                'HR_PRT'=>'HR_PRT',
                'HR_REV'=>'HR_REV',
            ];
            $related_flows=DB::table('trf_approval_matrix_flow_conditions')
            ->where('sub_cond_code',$sub_request_details)
            ->where('active',1)->pluck('flow_user_code')->toArray();
            
            //Super approver roles fetch
            $super_approvers=DB::table('trf_user_role_mapping')->where('role_code','FIN_APP')->pluck('aceid')->toArray();
            $hierarychy_approvers['FIN_APP']=$super_approvers;
            $covered_users=array_merge($covered_users,$super_approvers);

            // To auto approve the request
            $auto_approval_users = DB::table('users')->where('active', 1)->whereIn('departmentID', $this->CONFIG['AUTO_APPROVAL_DEPTS'])->pluck('aceid')->toArray();

            if($request_details['module'] == "MOD_03") {
                $role_mapping_for_users['VIS_USR'] = $traveller_id;
            }

            
            if(in_array($request_user,$super_approvers) || in_array($request_user, $auto_approval_users))
                $next_process_check=0;

                $requested_department=$request_details['department_id'];
            //Department_heads and their hierarychy checks
            if($next_process_check){
               if(!in_array('GEO_H',$related_flows)){
                    $requested_department=$request_details['department_id'];
                    $department_head=DB::table('trd_departments')
                        ->where('code',$request_details['department_id'])
                        ->value('head');
                    $department_hierarychy=$this->process_user_hierarychies('DEP_H_HIE',$department_head,$requested_department,$user_detail_prv_obj,$covered_users,$traveller_id);
                    $hierarychy_approvers=array_merge($hierarychy_approvers,$department_hierarychy['hierarychy_approvers']);
                    $covered_users=array_merge($covered_users,$department_hierarychy['covered_users']);
                    if(in_array($request_user,$department_hierarychy['covered_users'])||$request_user==$department_head)
                        $next_process_check=0;
                    if($next_process_check){
                        $hierarychy_approvers['DEP_H']=[$department_head];
                        $covered_users[]=$department_head;
                    }
               }
            }
            //DU Unit head and their hierarychy checks
            if($next_process_check){
                if($request_details['du_id']){
                    if(array_intersect(['DEP_H'],$traveler_roles))
                        $next_process_check=0;
                    if($next_process_check){
                        $du_head=DB::table('trd_practice')
                            ->where([['code',$request_details['du_id']],['active',1]])
                            ->value('head');
                        if(!in_array($du_head,$covered_users)){
                            $du_hierarychy=$this->process_user_hierarychies('DU_H_HIE',$du_head,$requested_department,$user_detail_prv_obj,$covered_users,$traveller_id);
                            //dd($du_hierarychy['hierarychy_approvers']);
                            
                            $hierarychy_approvers=array_merge($hierarychy_approvers,$du_hierarychy['hierarychy_approvers']);
                            $covered_users=array_merge($covered_users,$du_hierarychy['covered_users']);
                            if($du_head==$request_user)
                                $next_process_check=0;
                            if($next_process_check && !in_array($du_head,$request_user_reportees))
                                $hierarychy_approvers['DU_H']=[$du_head];
                            $covered_users[]=$du_head;
                        }
                    }   
                }
            }

            //Project owner and their hierarychy check
            if($next_process_check){
                if(in_array('PRO_OW',$related_flows)||in_array('PRO_OW_HIE',$related_flows)){ 
                    if(!array_intersect(['DEP_H','DU_H_HIE','DU_H'],$traveler_roles)){
                        $project_owner=DB::table('trd_projects')
                        ->where('project_code',$request_details['project_code'])
                        ->value('project_owner');
                        if($project_owner){
                            $po_hierarychy=$this->process_user_hierarychies('PRO_OW_HIE',$project_owner,$requested_department,$user_detail_prv_obj,$covered_users,$traveller_id);
                            $hierarychy_approvers=array_merge($hierarychy_approvers,$po_hierarychy['hierarychy_approvers']);
                            $covered_users=array_merge($covered_users,$po_hierarychy['covered_users']);
                            if(!in_array($project_owner,$covered_users)&&$project_owner!=$request_user&&!in_array($request_user,$covered_users)){ 
                                $hierarychy_approvers['PRO_OW']=[$project_owner];
                                $covered_users[]=$project_owner;
                            }     
                        }
                    }         
                }
            }

             //client partner and geo head checks
             //added by barath on 18-feb-2024
             if($next_process_check){
                if(in_array('GEO_H',$related_flows)){ 
                    
                    $requested_department=$request_details['department_id'];
                    $geo_head=DB::table('client_partner_geo_head_mapping')->where('head_type','geo_head')
                        ->where('mapping_relation','department')->where('mapping_value',$requested_department)->where('active',1)
                        ->pluck('configured_user')->toArray();
                    if(in_array($request_user,$geo_head)){
                        $next_process_check=0;
                    }
                    else{
                        $hierarychy_approvers['GEO_H']=$geo_head;
                        $covered_users=array_merge($covered_users,$geo_head);
                    }
                }
                }    //in_array('CLI_PTR',$related_flows)||
                    if($next_process_check){
                        $client_partners=DB::table('client_partner_geo_head_mapping')->where('head_type','client_partner')->where('active',1)
                        ->pluck('configured_user')->toArray();
                        $user_hierarychy=$this->process_user_hierarychies('USR_H',$request_user,$requested_department,$user_detail_prv_obj,$covered_users,$traveller_id);

                       if(array_key_exists('hierarychy_approvers',$user_hierarychy)&&
                       array_key_exists('USR_H',$user_hierarychy['hierarychy_approvers'])&&
                       count($user_hierarychy['hierarychy_approvers']['USR_H'])){
                        foreach($user_hierarychy['hierarychy_approvers']['USR_H'] as $hie_user){
                            if(in_array($hie_user,$client_partners)&&!in_array($hie_user,$covered_users)){
                                $hierarychy_approvers['CLI_PTR'][]=$hie_user;
                            }
                            $covered_users[]=$hie_user;
                        }
                       }
                    }
            // Default approvers
            $default_approvers_flow_codes = $this->CONFIG["DEFAULT_APPROVERS_FLOW_CODES"];
            $default_approvers = $this->CONFIG["DEFAULT_APPROVERS"];
            if(isset($hierarychy_approvers) && is_array($hierarychy_approvers) && count($hierarychy_approvers)) {
                // Find all the approvers need to replace
                $finded_approvers = array_intersect_key($default_approvers, array_flip($covered_users));
                foreach( $finded_approvers as $actual_approver => $default_approver ) {
                    // Add the replaced approved in covered users list
                    $covered_users[] = $default_approver;
                    // Change the actual approver with default approver in configured flows
                    $hierarychy_approvers = array_combine(
                        array_keys($hierarychy_approvers),
                        array_map( function ($v, $k) use($actual_approver, $default_approver, $default_approvers_flow_codes) {
                            $index = array_search($actual_approver, $v);
                            if( in_array($k, $default_approvers_flow_codes) && $index !== false) $v[$index] = $default_approver;
                            return $v;
                        }, $hierarychy_approvers, array_keys($hierarychy_approvers) )
                    );
                }
            }
            $current_flow_codes=[];
            foreach($respective_rule_flow_users as $flows){
                // Condition for ticket and forex process
                $current_flow_codes[]=$flows->flow_user_code;
                $condition_for_ticket_process = !$request_details['ticket_required'];
                $condition_for_forex_process = !$request_details['forex_required'];
                if($request_details['module'] == 'MOD_01')
                    $overall_condition = in_array($flows->flow_user_code, ['TRV_PROC_FOREX']) && $condition_for_forex_process;
                else if($condition_for_ticket_process && $condition_for_forex_process)
                    $overall_condition = in_array($flows->flow_user_code, ['TRV_PROC_FOREX']) && $condition_for_forex_process;
                else
                    $overall_condition = (in_array($flows->flow_user_code, ['TRV_PROC_FOREX']) && $condition_for_forex_process)
                                        || (in_array($flows->flow_user_code, ['DOM_TCK_ADM', 'TRV_PROC_TICKET']) && $condition_for_ticket_process);
                    
                if($overall_condition){
                    continue;
                }
                if(array_key_exists($flows->flow_user_code,$role_mapping_for_users)&&!in_array($flows->flow_user_code,$existing_flow))
                {
                    $configured_approver=$this->mapped_approver_config($role_mapping_for_users[$flows->flow_user_code],$flows->flow_user_code,$request_details);

            
                    DB::table('trf_approval_matrix_tracker')->insert([
                        'request_id'=>$request_details['request_id'],
                        'flow_code'=>$flows->flow_user_code,
                        'respective_role_or_user'=>$configured_approver,//$role_mapping_for_users[$flows->flow_user_code],
                        'is_completed'=>0,
                        'comments'=>$request_details['comments'],
                        'active'=>1,
                        'updated_by'=>Auth::User()->aceid,
                        'created_at'=>$current_date,
                        'updated_at'=>$current_date
                    ]);
                }
                else{
                    if(array_key_exists($flows->flow_user_code,$hierarychy_approvers)
                    &&$hierarychy_approvers[$flows->flow_user_code]
                    &&count($hierarychy_approvers[$flows->flow_user_code])
                    &&!in_array($flows->flow_user_code,$existing_flow))
                    {
                        foreach($hierarychy_approvers[$flows->flow_user_code] as $flow_user){
                            $configured_approver=$this->mapped_approver_config($flow_user,$flows->flow_user_code,$request_details);

                            DB::table('trf_approval_matrix_tracker')->insert([
                                'request_id'=>$request_details['request_id'],
                                'flow_code'=>$flows->flow_user_code,
                                'respective_role_or_user'=>$configured_approver,//$flow_user,
                                'is_completed'=>0,
                                'comments'=>$request_details['comments'],
                                'active'=>1,
                                'updated_by'=>Auth::User()->aceid,
                                'created_at'=>$current_date,
                                'updated_at'=>$current_date
                            ]);
                        }
                        
                    }
                }
            }
            $flows_to_remove=array_diff($existing_flow,$current_flow_codes);
            if(count($flows_to_remove)){
                DB::table('trf_approval_matrix_tracker')->where('active',1)
                ->where('request_id',$request_details['request_id'])
                ->whereIn('flow_code',$flows_to_remove)->update(['active'=>0]);
            }
            return 1;
        }
        catch(\Exception $e){
            Log::error("err in assign_approvers");
            Log::error($e);
            return 0;
        }   
    }
    
    /**
     * process_user_hierarychies
     * @author ganesh.veilsamy added on 17-jan-2024
     * @param  mixed $hierarychy_name
     * @param  mixed $approver
     * @param  mixed $requested_department
     * @param  mixed $user_detail_prv_obj
     * @param  mixed $covered_users
     * @return array
     */
    public function process_user_hierarychies($hierarychy_name,$approver,$requested_department,$user_detail_prv_obj,$covered_users,$traveller_id=null){
        $hierarychy_approvers=[];
        $finded_approver=$approver;
        $request_user=$traveller_id;//$traveller_id;//Auth::User()->aceid;
        $user_detail_prv_obj=new DetailsProvider();
        $department_heads=DB::table('trd_departments')->where('active',1)->pluck('head')->toArray();
        while($finded_approver!=''){
            $user_detail=$user_detail_prv_obj->fetch_user_attributes($finded_approver);
	    $user_detail=$user_detail['user_attributes'];
            if(isset($user_detail->DepartmentId)&&$user_detail->DepartmentId==$requested_department){
                if($user_detail->ReportingToACEID){
                    if(!in_array($user_detail->ReportingToACEID,$covered_users)){
                        $covered_users[]=$user_detail->ReportingToACEID;
                        $hierarychy_approvers[$hierarychy_name][]=$user_detail->ReportingToACEID;
                        if($user_detail->ReportingToACEID==$request_user)
                        $hierarychy_approvers=[];
                        $finded_approver=$user_detail->ReportingToACEID;
                    }
                    else{
                        $finded_approver='';
                    }
                } else {
                    $finded_approver = '';
                }
                
            }
            else{
                $finded_approver='';
            }
        }
        return ['hierarychy_approvers'=>$hierarychy_approvers,'covered_users'=>$covered_users];
    }
    
    /**
     * fetch_next_process_flow - To fetch the next flow of the request for the respective action
     * @author ganesh.veilsamy added on 17-Jan-2024
     * @param  mixed $module
     * @param  mixed $action
     * @param  mixed $request_details
     * @return array
     */
    public function fetch_next_process_flow($request_id,$module,$action,$department_mapping,$current_status_id,$comments=null,$from_mail_approval=null,$action_user=null,$rule_code=null, $updated_flow=null, $is_onsite_travel = false){
        try{
            $next_process_status='';$next_process_mail='';
            $role_mapping_for_users=[
                'AN_COST_FIN'=>'AN_COST_FIN',
                'AN_COST_FAC'=>'AN_COST_FAC',
                'AN_COST_VISA'=>'AN_COST_VISA',
                'BF_REV'=>'BF_REV',
                'TRV_PROC_TICKET'=>'TRV_PROC_TICKET',
                'TRV_PROC_FOREX'=>'TRV_PROC_FOREX',
                'TRV_PROC_VISA'=>'TRV_PROC_VISA',
                'DOM_TCK_ADM'=>'DOM_TCK_ADM',
                'VIS_USR'=>'VIS_USR',
                'GM_REV'=>'GM_REV',
                'HR_PRT'=>'HR_PRT',
                'HR_REV'=>'HR_REV',
            ];

            if($is_onsite_travel) {
                $action = 'desk_review_fac';
                $current_status_id = 'STAT_02';
            }

            // DB::enableQueryLog();
            if($module=="MOD_03") {
                $related_action_details=DB::table('vrf_visa_flow_config')
                ->where('rule_code',$rule_code)->where('action',$action)->where('active',1)
                ->where('department_mapping_code',$department_mapping)->first();
            } else {
                $related_action_details=DB::table('trf_approval_matrix_permissions')
                ->where('module',$module)->where('action',$action)->where('active',1)
                ->where('department_mapping_code',$department_mapping)->first();
            }
            //->toArray();
                $related_action_details=(array)$related_action_details;
            // dd(DB::getQueryLog());
                 
            if($related_action_details){
                $permitted_status=explode('|',$related_action_details['current_status']);
                $permitted_roles=explode('|',$related_action_details['roles_involved']);
                if(!$from_mail_approval){
                    if(in_array($action,['desk_review_fac','desk_review_fin', 'desk_review_visa']) 
                    && Auth::User()->has_any_role_code('AN_COST_FAC') 
                    && Auth::User()->has_any_role_code('AN_COST_FIN')
                    ){
                        array_push($permitted_roles,'AN_COST_FAC','AN_COST_FIN');
                    }
                }
                // var_dump(Auth::User()->has_any_role_code($permitted_roles));
                 //dd(Auth::User()->has_any_role_code($permitted_roles));
                 $role_check=0;
                 if(Auth::User()&&Auth::User()->has_any_role_code($permitted_roles))
                 {
                    $auth_user=Auth::user()->aceid;
                    $role_check=1;
                 }
                 elseif($from_mail_approval){
                    $auth_user=$action_user;
                    $role_check=1;
                 }
                 elseif($is_onsite_travel) {
                    $auth_user = Auth::User()->aceid;
                    $role_check = 1;
                 }
                if(($role_check)&&in_array($current_status_id,$permitted_status)){
                    $role_based_access = array_intersect($role_mapping_for_users,$permitted_roles);
                    $mark_completed = DB::table('trf_approval_matrix_tracker')->where('request_id',$request_id)->whereIn('flow_code',$permitted_roles);
                    if(!count($role_based_access)){
                        $mark_completed = $mark_completed->where('respective_role_or_user',$auth_user);
                    }
                    if(!in_array($action,['save_visa_process','save_ticket_process','save_forex_process', 'visa_user_save', 'onsite_hr_save','offshore_hr_save', 'petition_process_save', 'rfe_process_save', 'rfe_progress','visa_approval_save', 'visa_entry_save'])){//hard coded for save visa process
                        if($module == "MOD_03" && $mark_completed->count() > 1 ) {
                            $id_to_be_update = $mark_completed->where('is_completed', 0)->orderBy('id')->value('id');
                            $flow_to_be_update = $mark_completed->where('is_completed', 0)->orderBy('id')->value('flow_code');
                            if( is_null($updated_flow) || !in_array($updated_flow, ['VIS_USR', 'GM_REV', 'HR_REV']) )
                                $mark_completed->where('id', $id_to_be_update)->update([
                                    'is_completed'=>1,
                                    'comments'=>$comments
                                ]);
                        }
                        else
                            $mark_completed = $mark_completed->update([
                                'is_completed'=>1,
                                'comments'=>$comments
                            ]);
                    }
                    
                    if(array_key_exists('dependent_status_code',$related_action_details)&&$related_action_details['dependent_status_code']){
                        $dependent_status_array=explode('|',$related_action_details['dependent_status_code']);
                        $dependent_flows_array=explode('|',$related_action_details['dependent_roles_involved']);
                        $dependent_mails_array=explode('|',$related_action_details['dependent_mails_involved']);
                        for($i=0;$i<count($dependent_status_array);$i++){
                            if(!$next_process_status){
                                if(array_key_exists($i,$dependent_flows_array)){
                                    //  DB::enableQueryLog();
                                    $dependent_status_details=DB::table('trf_approval_matrix_tracker')
                                    ->where('active',1)
                                    ->where('flow_code',$dependent_flows_array[$i])
                                    ->where('request_id',$request_id)
                                    ->where('is_completed',0)->first();                                
                                    $dependent_status_details=(array)$dependent_status_details;
                                    // var_dump(DB::getQueryLog());
                                    if($dependent_status_details&&count($dependent_status_details)){
                                        $next_process_status=$dependent_status_array[$i];
                                        $next_process_mail=array_key_exists($i, $dependent_mails_array) ? $dependent_mails_array[$i] : null;
                                    }
                                } 
                            }
                        }
                        //dd($next_process_status,$next_process_mail);
                    }
                    else{
                        $next_process_status=$related_action_details['status_code'];
                        $next_process_mail=$related_action_details['mails_involved'];
                    }
                    if(!$next_process_status){
                        $next_process_status=$related_action_details['status_code'];
                        $next_process_mail=$related_action_details['mails_involved'];
                    }
                    $next_process_message=$related_action_details['success_msg'];
                    $next_process_url=$related_action_details['redirect_url'];
                }
                
                
            }
            if(!$next_process_status){
                return (['error'=>'INVALID_ACTION']);
            }
            else{
                return ['status'=>$next_process_status,'mail'=>$next_process_mail,'message'=>$next_process_message,'redirect_url'=>$next_process_url, 'updated_flow' => isset($flow_to_be_update) ? $flow_to_be_update : null];
            }

        }
        catch(\Exception $e){
            Log::error("err in fetch_next_process_flow");
            Log::error($e);
            return (['error'=>'EXC_ERROR']);
        }       
    }
        /**
     * To save the request proof details
     * @author venkatesan.raj
     *  
     * @param string $request_id
     * @param array $proof_details
     * 
     * @return void
     */
    public function save_proof_details($request_id, $proof_details)
    {
        try
        {
            $aceid = Auth::User()->aceid;
            extract($proof_details);

            // Insert or update the file details
            DB::beginTransaction();
            $new_file_name_index = [];
            $dir_to_upload_files = DB::table('trd_proof_type as trd')->whereIn('unique_key',$proof_type)->pluck('upload_path', 'unique_key')->toArray();
            $request_code = DB::table('trf_travel_request')->where([['id', $request_id],['active',1]])->value('request_id');
            $module = DB::table('trf_travel_request')->where([['id', $request_id],['active',1]])->value('module');
            DB::table('trf_request_proof_file_details')->where('request_id', $request_id)->update(['active' => 0]);
            for($index = 0; $index < count($proof_type); $index++)
            {
                // Moving the file
                if(!$proof_type[$index])
                    break;
                $old_file_name = $proof_file_path[$index];
                $file_type_id = $proof_type[$index];
                $request_for_code = $proof_request_for[$index];
                $file_type_name = $this->CONFIG["FILENAME"][$file_type_id];
                $extension = substr($old_file_name, strrpos($old_file_name, '.'));
                $new_file_name_segments = [$request_code, $aceid, $this->CONFIG["FILENAME"][$file_type_id].$extension];
                if(in_array($request_for_code, $this->CONFIG["REQUEST_FOR_FAMILY"])){
                    $file_index = array_key_exists($file_type_id, $new_file_name_index) ? $new_file_name_index[$file_type_id] + 1 : 1;
                    $new_file_name_segments = [$request_code, $aceid, $this->CONFIG["FILENAME"][$file_type_id], $file_index.$extension];
                    $new_file_name_index[$file_type_id] = $file_index;
                }
                $new_file_name = implode("_", $new_file_name_segments);
                $dir_name = $dir_to_upload_files[$file_type_id];
                $old_file_path = "temp/".$old_file_name;
                $new_file_path = $dir_name.'/'.$new_file_name;
                if(file_exists($old_file_path))
                    rename($old_file_path, $new_file_path);

                // Insert or update in DB
                $original_name = DB::table('temporary_file_details')->where([['system_name', $old_file_name],['active',1]])->value('original_name');
                if(is_null($original_name))
                    $original_name = DB::table('trf_request_proof_file_details')->where([['system_name', $old_file_name]])->value('original_name');
                if(!file_exists($new_file_path))
                    $new_file_name = $old_file_name;

                $reference_id = is_numeric($file_reference_id[$index]) ? $file_reference_id[$index] : null;
                $data = [
                    'request_id' => $request_id,
                    'module' => $module,
                    'request_for_code' => $request_for_code,
                    'file_type_id' => $file_type_id,
                    'original_name' => $original_name,
                    'system_name' => $new_file_name,
                    'active' => 1,
                    'created_by' => $aceid,
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s'),
                ];
                $condition = [
                    'id' => $reference_id,
                ];
                if(DB::table('trf_request_proof_file_details')->where($condition)->count()){
                    unset($data['created_at']);
                    DB::table('trf_request_proof_file_details')->where($condition)->update($data);
                }else{
                    $file_reference_id[$index] = DB::table('trf_request_proof_file_details')->insertGetID($data);
                }
            }
            DB::commit();

            // Insert ot update the proof details
            DB::beginTransaction();
            DB::table('trf_travel_request_proof_details')->where('request_id', $request_id)->update(['active' => 0]);
            for($index = 0; $index < count($proof_type); $index++)
            {
                if(!$proof_type[$index])
                    break;
                $proof_type_id = $proof_type[$index];
                $request_for_code = $proof_request_for[$index];
                $reference_id = $file_reference_id[$index];
                $proof_attribute_list = DB::table('trd_proof_additional_details')->where([['proof_type_id',$proof_type_id],['active',1]])->pluck('id', 'input_name')->toArray();
                foreach($proof_attribute_list as $input_name => $attribute_id)
                {
                    $data = [
                        'value' => $$input_name[$index],
                        'active' => 1,
                        'created_at' => date('Y-m-d h:i:s'),
                        'updated_at' => date('Y-m-d h:i:s')
                    ];
                    $condition = [
                        'request_id' => $request_id,
                        'proof_type_id' => $proof_type_id,
                        'proof_attr_id' => $attribute_id,
                        'request_for_code' => $request_for_code,
                        'request_proof_file_id' => $reference_id,
                    ];
                    if(DB::table('trf_travel_request_proof_details')->where($condition)->count()){
                        unset($data['created_at']);
                        DB::table('trf_travel_request_proof_details')->where($condition)->update($data);
                    }else{
                        $data = array_merge($data, $condition);
                        DB::table('trf_travel_request_proof_details')->insert($data);
                    }
                }
            }
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            Log::error('Err in save_proof_details');
            Log::error($e);
        }
    }

    public function domestic_accesss(Request $request){
        $get_users=DB::table('users')->where('active',1)->pluck('aceid')->toArray();
        $domestic_access=[];
        foreach($get_users as $user){
            $access=[
                'aceid'=>$user,
                'role_code'=> 'DOM_REQ',
                'active'=>1
            ];
            array_push($domestic_access,$access);
        }
        //dd($domestic_access);
        DB::table('trf_user_role_mapping')->insert($domestic_access);
    }
    /**
     * To run the command which update the user details and proof details
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return object
     */
    public function load_user_other_details(Request $request)
    {
        try
        {
            $token = $request->input('session_token');
            $token = $token ? Crypt::decryptString($token) : null;
            if($token && $token == session()->get('user_other_details'))
            {
                Artisan::call('proof-details:cron');
                session()->forget('user_other_details');
                return json_encode(['success' => 'Proof details loaded successfully']);
            }
            else
            {
                return json_encode(['error' => 'Error occurred in loading proof details']);
            }
        }
        catch(\Exception $e)
        {
            Log::error('Err in load_user_other_details');
            Log::error($e);
        }
    }
    /**
     * To extend the travelling dates
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function extend_travel_dates(Request $request)
    {
        try
        {
            $request_id = $request->input('edit_id');
            $data = $request->input('data');
            DB::beginTransaction();
            foreach($data as $key => $value)
            {
                // Values to be inserted in tracker
                $values_to_insert = [          
                    'active' => 1,
                    'updated_by' => Auth::User()->aceid,
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s'),
                ];

                if(array_key_exists('from_date', $value)){
                    $value['from_date'] = date('Y-m-d h:i:s', strtotime($value['from_date']));
                    $values_to_insert['new_from_date'] = $value['from_date'];
                }
                if(array_key_exists('to_date', $value)){
                    $value['to_date'] = date('Y-m-d h:i:s', strtotime($value['to_date']));
                    $values_to_insert['new_to_date'] = $value['to_date'];
                }


                $reference_id = $value['reference_id'];
                // Remove the null values being updated
                $value = array_filter($value);
                // Remove the reference id from array
                unset($value['reference_id']);
                if(count($value) && DB::table('trf_traveling_details')->where(['id' => $reference_id])->exists()){
                    // Fetch the already existsing record.
                    $existing_traveling_details = (array)DB::table('trf_traveling_details')
                                                        ->select('id as traveling_details_id','request_id', 'from_date as old_from_date', 'to_date as old_to_date')
                                                        ->where('id', $reference_id)->first();

                    //update the traveling details table
                    DB::table('trf_traveling_details')->where('id', $reference_id)->update($value);
                    // dd(DB::getQueryLog());

                    //insert the changes in tracker
                    $values_to_insert = array_merge($values_to_insert, $existing_traveling_details);
                    DB::table('trf_travel_dates_tracker')->insert($values_to_insert);
                }
            }
            DB::commit();
            return json_encode(['message'=>'Travel dates has been updated successfully']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Err in extend_travel_dates');
            Log::error($e);
            return json_encode(['message'=>'Error occurred while updating the travel dates']);

        }
    }

public function check_permissions_on_request($request_details){
        try{
            $has_view_access=false;
            $user_permissions=Auth::User()->respective_roles_code();
            $role_permission_chart=$this->role_based_config;
            $status_id=$request_details->status_id;
            if(Auth::User()->aceid==$request_details->travaler_id||Auth::User()->aceid==$request_details->created_by)
                $has_view_access=true;
            if(count($user_permissions)){
                foreach($user_permissions as $permission){
                    if(array_key_exists($permission,$role_permission_chart)){
                        if(array_key_exists('to_check',$role_permission_chart[$permission])){
                            if(in_array($status_id,$role_permission_chart[$permission]['to_check'])){
                                $location_based_access = ['AN_COST_FIN', 'AN_COST_FAC', 'AN_COST_VISA', 'GM_REV', 'HR_REV', 'TRV_PROC_TICKET', 'TRV_PROC_FOREX', 'TRV_PROC_VISA'];
                                if($permission == "HR_PRT" && $request_details->module == "MOD_03") {
                                    $is_hr_partner = DB::table('vrf_visa_request_details')->where([['request_id', $request_details->id],['hr_partner', Auth::User()->aceid]])->exists();
                                    $has_view_access = $request_details->created_by == Auth::User()->has_any_role_code('HR_PRT') && (Auth::User()->aceid || $is_hr_partner) ? true : $has_view_access;
                                }
                                if( in_array($permission, $location_based_access) ) {
                                    $detailsProvider = new DetailsProvider();
                                    $reviewers = (array)$detailsProvider->get_travel_desk_user_details($request_details->id, [$permission]);
                                    $has_view_access = in_array(Auth::User()->aceid, $reviewers) ? true : $has_view_access;
                                } else {
                                    $has_view_access = true;
                                }
                            }
                        }
                        if(array_key_exists('checked',$role_permission_chart[$permission])){
                            if(in_array($status_id,$role_permission_chart[$permission]['checked'])){
                                $location_based_access = ['AN_COST_FIN', 'AN_COST_FAC', 'AN_COST_VISA', 'GM_REV', 'HR_REV', 'TRV_PROC_TICKET', 'TRV_PROC_FOREX', 'TRV_PROC_VISA'];
                                if($permission == "HR_PRT" && $request_details->module == "MOD_03") {
                                    $is_hr_partner = DB::table('vrf_visa_request_details')->where([['request_id', $request_details->id],['hr_partner', Auth::User()->aceid]])->exists();
                                    $has_view_access = $request_details->created_by == Auth::User()->aceid || $is_hr_partner ? true : $has_view_access;
                                }
                                if( in_array($permission, $location_based_access) && !$has_view_access ) {
                                    $detailsProvider = new DetailsProvider();
                                    $reviewers = (array)$detailsProvider->get_travel_desk_user_details($request_details->id, [$permission]);
                                    $has_view_access = in_array(Auth::User()->aceid, $reviewers) ? true : $has_view_access;
                                } else {
                                    $has_view_access = true;
                                }
                            }
                        }
                        if(in_array($status_id,$role_permission_chart[$permission]))
                            $has_view_access=true;
                    }
                }
            }
            return $has_view_access;
        }
        catch(\Exception $e){
            Log::error($e);
        }
    }
    /**
     * To block the user
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return mixed
     */
    public function block_user(Request $request)
    {
        try
        {
            $aceid = $request->input('aceid');
            $date = date('Y-m-d h:i:s');
            $system_name = 'TRAVEL';

            $blocked_users = DB::table('trf_blocked_users');
            $data = [
                'active' => 1,
                'created_by' => Auth::User()->aceid,
                'created_at' => $date,
                'updated_at' => $date,
                'system_name' => $system_name
            ];
            $condition = [
                'aceid' => $aceid,
            ];

            if($blocked_users->where($condition)->exists()){
                unset($data['created_at']);
            }
            $blocked_users->updateOrInsert($condition, $data);

            $message =  "The user has been blocked successfully.";
            return json_encode( ['message' => $message] );
        }
        catch(\Exception $e)
        {
            Log::error('Error in block_user');
            Log::error($e);
        }
    }
    /**
     * To unblock a user
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return mixed
     */
    public function unblock_user(Request $request)
    {
        try
        {
            $aceid = $request->input('aceid');
            $date = date('Y-m-d h:i:s');
            $system_name = 'TRAVEL';

            $blocked_users = DB::table('trf_blocked_users');
            $data = [
                'active' => 0,
                'updated_at' => $date,
                'system_name' => $system_name
            ];
            $condition = [
                'aceid' => $aceid,
            ];
            
            $blocked_users->where($condition)->update($data);

            $message = "The user has been unblocked successfully.";
            return json_encode(['message' => $message]);
        }
        catch (\Exception $e)
        {
            Log::error('Error in unblock_error');
            Log::error($e);
        }
    }

    /**
     * @param String $flowUser flow user aceid or either it can be a role_code
     * @param int $request_id $request_id
     * @return array ['mapped_approver'=>boolean,'configured_approver'=> String]
     */
    public function mapped_approver_config($flow_user,$flow_user_code,$request_details=null){
        Log::info($flow_user,$this->CONFIG['DEFAULT_APPROVERS_FLOW_CODES']);
        $approver='';
        try{
            //$request_details=DB::table('trf_travel_request')->where('id',$request_id)->first();
            //dd($request_details);
            if(in_array($flow_user_code,$this->CONFIG['DEFAULT_APPROVERS_FLOW_CODES'])){
                $approver= DB::table('configured_approval_mapping')
                ->where([['active',1],['module',$request_details['module']]])
                ->where(function($query) use($request_details,$flow_user,$flow_user_code){
                    if($flow_user_code=='DEP_H'){
                        $query->where([['dept',$request_details['department_id']]])
                        ->orWhere('original_approver',$flow_user);
                    }
                    else if($flow_user_code=='DU_H'){
                        $query->where([['dept',$request_details['du_id']]])
                        ->orWhere('original_approver',$flow_user);
                    }else{
                        $query->Where('original_approver',$flow_user);
                    }
                    
                })
                ->value('mapped_approver');
            }

            if($approver){
                return $approver;
            }else{
                return $flow_user;
            }

        }catch(\Exception $e){
            Log::error('Error occured during the mapped approver config: '.$e->getMessage());
            return $flow_user;
        }
    }
    /**
     * Get users having date extend access for given location
     * @author venkatesan.raj
     * 
     * @param array $params << request_id, module, from_country >>
     * 
     * @return array
     */
    public function get_users_can_extend_dates($params=[])
    {
        try {
            if(empty($params)) return [];
            $provider = new DetailsProvider();
            $params['config_name'] = 'travel_date_extend_access';
            $params['role'] = 'TRV_EXT';
            $accessible_users = $provider->get_values_against_rule($params);
            return explode('|', $accessible_users);
        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To cancel the travel request
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function cancel_travel(Request $request)
    {
        try {
            $request_id = $request->input('edit_id') ?? null;
            $action = $request->input('action') ?? null;
            $reason_for_cancel = $request->input('reason_for_cancel') ?? null;
            $aceid = Auth::User()->aceid;

            $travel_request = DB::table('trf_travel_request')->where('id', $request_id);
            $travel_request_details = $travel_request->first();
            $traveler_dept_id = $travel_request_details->department_code;
            $module_id = $travel_request_details->module;
            $current_status = $travel_request_details->status_id;
            $request_code = $travel_request_details->request_id;
            $dept_grp = $this->get_respective_dept_group($module_id, $traveler_dept_id);

            $detailsProvider = new DetailsProvider();

            $approval_matrix_permissions = DB::table('trf_approval_matrix_permissions')
                                            ->where('module', $module_id)
                                            ->where('department_mapping_code', $dept_grp)
                                            ->where('action', $action)
                                            ->first();

            if(empty($approval_matrix_permissions)) {
                throw new \Exception('Flow not configured for current action : '.$action);
            }

            $configured_status = explode( '|', $approval_matrix_permissions->current_status );

            if(!in_array($current_status, $configured_status)) {
                throw new \Exception('Flow not configured for current status : '.$current_status);
            }

            // Update the request status
            $next_status = $approval_matrix_permissions->status_code;
            $mail = $approval_matrix_permissions->mails_involved;
            $data = [
                'status_id' => $next_status,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $travel_request->update($data);

            // Add entry to status tracker
            $data = [
                'request_id' => $request_id,
                'old_status_code' => $current_status,
                'new_status_code' => $next_status,
                'action' => $action,
                'created_by' => $aceid,
                'comments' => $reason_for_cancel,
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            DB::table('trf_request_status_tracker')->insert($data);

            // remove utilized budget
            $detailsProvider->remove_budget_utilized($request_code);

            return response()->json([
                'request_id' => $request_id,
                'action' => $action,
                'mail' => $mail
            ]);

        } catch (\Exception $e) {
            Log::error('Error in '.__FUNCTION__);
            Log::error($e);
            return response()->json([
                'error' => 'An error occurred while cancelling the travel request. Please try again. If the issue persists, contact help.mis@aspiresys.com'
            ]);
        }
    }
    /**
     * To check the whether the user's origin is onsite or offshore
     * @author venkatesan.raj
     * 
     * @param string $request_id
     * @param string $origin optional
     * 
     * @return bool
     */
    public function is_onsite_travel($request_id, $origin = null)
    {
        try {
            $offshore_countries = [ "COU_014" ];
            if(empty($origin)) {
                $traveling_details = DB::table('trf_traveling_details')->where([['request_id', $request_id],['active', 1]])->first();
                return isset($traveling_details->from_country) && !in_array($traveling_details->from_country, $offshore_countries );
            }
            return !in_array($origin, $offshore_countries);
            
        } catch (\Exception $e) {
            Log::error("Errro in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
}
