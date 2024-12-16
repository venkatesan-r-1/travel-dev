<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use \Curl\Curl;
use App\Models\trf_travel_request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Project;
use Arr;
use Crypt;
use App\Traits\EncryptionTrait;


class DetailsProvider extends Controller
{
    use EncryptionTrait;
     /**
     * To list all the travel purpose from the DB
     * Added by monisha.thirumalai
     * @param String  $module
     * @param Integer $active_alone optional
     * @return Array
     */
    public function list_travel_purpose($module,$active_alone=1){
    try{
        $travel_purposes=DB::table('trd_travel_purpose')->where('module',$module)->where('active',$active_alone)->pluck('name','unique_key')->toArray();
        return $travel_purposes;
    }
    catch(\Exception $ex ){
            Log::info($ex);
    }
    }
    /**
     * To list all the departments from the DB
     * Added by monisha.thirumalai
     * @param Integer $active_alone optional
     * @return Array
     */
    public function list_departments($active_alone=1){
        try{
            $department=DB::table('trd_departments')->where('active','=',$active_alone)->orderby('name','ASC')->pluck('name','code')->toArray();
            return $department;
        }catch(\Exception $ex ){
                Log::info($ex);
        }
    }
    public function list_projects($for_page='request',$requestor_dept = null, $active_alone=1){
        try{
            $projects=DB::table('trd_projects')->where('active','=',$active_alone);
            if($for_page=='request'){
                if(is_null($requestor_dept))
                    $requestor_dept=Auth::User()->DepartmentId;
                if(in_array($requestor_dept,$this->FUNC_SALES_DEPT_CODES))
                    $projects=$projects->where('project_department',$requestor_dept);
            }
            $projects=$projects->orderByRaw("project_code = 'CUST_PROJ_007' DESC")->orderBy('project_name', 'asc')->pluck('project_name','project_code')->toArray();
            if(in_array($requestor_dept,$this->FUNC_SALES_DEPT_CODES)){
                $cust_projects=DB::table('trd_projects')->whereIn('project_code',$this->CONFIG['CUSTOM_PROJECT'])
                ->where('active',1)->orderByRaw("project_code = 'CUST_PROJ_007' DESC")->orderBy('project_name', 'asc')->pluck('project_name','project_code')->toArray();
                $projects=array_merge($cust_projects, $projects);
            }
            return $projects;           
        }catch(\Exception $ex ){
            Log::error($ex);
        }
    }

    public function load_project_details(Request $request){
        try{
            $employee = $request->input('employee');
            if($employee) {
                $user_department = User::where('aceid', $employee)->value('DepartmentId');
            }else if($request->edit_id){
                $user_department=$request->department_id;
            }else{
                $user_department=Auth::User()->DepartmentId;
            }
            $project=$request->project;
            $selected_department=$user_department;
            $department=[];$customer=[];$delivery_unit=[];
            if(in_array($project,['CUST_PROJ_007'])){
                $selected_department=$user_department;
                $customer=DB::table('trd_projects')->where([['project_code',$project],['active',1]])
                ->pluck('customer_name','customer_code')->toArray();
                $department=DB::table('trd_departments')->where([['active',1],['visible',1]]);
                if(!in_array($selected_department,$this->FUNC_SALES_DEPT_CODES)){
                    $delivery_unit=DB::table('trd_practice as p')
                                    ->leftJoin('users as u', function ($join) { $join->on('u.aceid', 'p.head')->where('u.active', 1); })
                                    ->select('p.code as code','p.name', DB::raw("CONCAT_WS(' - ', p.name, u.username) as unit_name"))
                                    ->where([['p.active','=',1],['p.type','=','delivery'],['p.name', '!=', '']])->pluck('unit_name','code')->toArray();
                    $delivery_unit = array_filter($delivery_unit);
                }
                else{
                    $department=$department->where('sl_flag',0);
                }
                $department=$department->pluck('name','code')->toArray();
            }
            else{
                $project_detail=DB::table('trd_projects')->where([['project_code',$project],['active',1]])->first();
                if($project_detail){
                    $customer = DB::table('trd_projects')->where([['project_code','=',$project],['active','=',1]])
                    ->orderBy('customer_name', 'asc')
                    ->pluck('customer_name','customer_code')->toArray(); 
                    $delivery_unit=DB::table('trd_practice as p')
                        ->leftJoin('users as u','u.aceid','=','p.head')
                        ->where('p.code',$project_detail->project_unit)
                        ->select(DB::raw('CONCAT(p.name,"-",u.username) as "name"'),'code')
                        ->orderBy('name', 'asc')
                        ->pluck('name','code')->toArray();
                    $selected_department=$project_detail->project_department;
                    $department=DB::table('trd_departments')->where([['active',1],['code',$project_detail->project_department]])->orderBy('name', 'asc')->pluck('name','code')->toArray();
                }
            }
            return json_encode(['department'=>$department,'customer'=>$customer,'delivery_unit'=>$delivery_unit,'selected_department'=>$selected_department]);
        }catch(\Exception $ex ){
            Log::error($ex);
        }
    }
    
    /**
     * To list all the travel types
     * @author monisha.thirumalai
     * @param string  $module
     * @param int $active_alone optional
     * @return Array
     */
    public function list_travel_type( $module,$active_alone = 1)
    {
        try
        {
            $travel_type=DB::table('trd_travel_types')->where('module',$module)->where('active',$active_alone)->pluck('type','unique_key')->toArray();
            return $travel_type;
        }
        catch (\Exception $e)
        {
	    Log::error('err in list_travel_type');
            Log::error($e);
        }
    }
    /**
     * To list all the country from the DB
     * Added by monisha.thirumalai
     * @param  Integer $active_alone optional
     * @return Array
     */
    public function list_country($active_alone=1){
        try{
            $country_list = DB::table('trd_country_details')->where('active','=',$active_alone)->orderby('name','ASC')->pluck('name','unique_key')->toArray();
            return $country_list;
        }
        catch(\Exception $ex ){
                Log::error($ex);
        }
    }
    /**
     * To load the city based on the country choosen
     * Added by monisha.thirumalai
     * @param  mixed $request
     * @return object
     */
    public function load_city(Request $request){
        try{
            $travel_purpose = $request->input('travel_purpose') ?? "PUR_02_02";
            $travel_purpose_visa_type_mapping = ["PUR_02_02" => "VIS_001", "PUR_02_03" => "VIS_002", "default" => "VIS_001"];
            $visa_type = array_key_exists($travel_purpose, $travel_purpose_visa_type_mapping) ? $travel_purpose_visa_type_mapping[$travel_purpose] : $travel_purpose_visa_type_mapping["default"];
            $country=$request->country;
            // $mapping_city=DB::table('trf_visa_country_mapping')->where('mapping_country_code',$country)->value('source_country_code');
            // if($mapping_city){
            //     $country=$mapping_city;
            // }
            $city=$this->list_city($country);
                // $visa_details=DB::table('trd_visa_details')->where([['aceid',Auth::User()->aceid],['visa_country_code',$country],['trd_visa_details.active',1]])
                // ->pluck('visa_number','visa_type')->toArray();
            return json_encode(['city'=>$city,'visa_not_required_countries'=>$this->visa_not_required_countries]);
        }
        catch(\Exception $ex ){
                Log::info($ex);
        }
    }
    /**
     * To list the city list for given country
     * 
     * @param string $country
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_city($country, $active_alone=true)
    {
        try
        {
            if(is_null($country)) return [];
            $city_list = DB::table('trd_country_city')->where('country_code','=',$country);
            if($active_alone) $city_list = $city_list->where('active','=',1);
            return $city_list->pluck('name','unique_key')->toArray();
        }
        catch(\Exception $e)
        {
            Log::error("Error in list_city");
            Log::error($e);
        }
    }
    /**
     * To list all the visa from DB
     * Added by monisha.thirumalai
     * @param  Integer $active_alone optional
     * @param string $module optional
     * @return Array
     */
    public function list_visa_type($active_alone=1, $module = null){
        try{
            $exclude_visa_type = ['VIS_002'];
            $visa_type = DB::table('trd_visa_type')->where('active','=',$active_alone)->pluck('name','unique_key')->toArray();
            if($module == "MOD_03" && !Auth::User()->has_any_role_code(['HR_PRT', 'PR_MAN']))
                $visa_type = Arr::except($visa_type, $exclude_visa_type);
            return $visa_type;
        }
        catch(\Exception $ex ){
                Log::info($ex);
        }
    }
    /**
     * To load the user entity details
     * Added by monisha.thirumalai
     * @param string  $user_name
     * @param int $active_alone optional
     * @return Array
     */
    public function list_entity_details($user_name,$active_alone=1){
        try{
            $entity=DB::table('users')
            ->join('trd_entity','trd_entity.unique_key','users.SourceCompanyID')
            ->where([
                ['users.username',$user_name],
                ['trd_entity.active','=',$active_alone]
            ])
            ->value('trd_entity.entity_name');
            return $entity;
        }
        catch(\Exception $ex ){
                Log::info($ex);
        }
    }

    /**
	 * to load project based on the department selected
     * * Added by monisha.thirumalai
     * @param  mixed $request
     * @return object
	*/
    public function load_project(Request $request){
		$department_code=$request->department;
        $projects=DB::table('trd_projects')->where([['project_department',$department_code],['active',1]])
			->pluck('project_name','project_code')->toArray();
		$custom_projects=DB::table('trd_projects')->where([['project_code','CUST_PROJ_007'],['active',1]])
			->pluck('project_name','project_code')->toArray();
		
		$projects=array_merge($projects,$custom_projects);
		return json_encode(['projects'=>$projects]);
	}

	/**
	 * to load customer and delievery unit & practice based on the department and project selected
     * * * Added by monisha.thirumalai
     * @param  mixed $request
     * @return object
	*/
	public function load_customer_du(Request $request){
		$department_code=$request->department;
		$project_code=$request->project;
		
		$customers=DB::table('trd_projects')->where([['project_department','=',$department_code],['project_code','=',$project_code],['active','=',1]])
        ->pluck('customer_name','customer_code')->toArray();
		
        $custom_projects=DB::table('trd_projects')
        ->where([['project_code','CUST_PROJ_007'],['active',1]])
        ->pluck('customer_name','customer_code')->toArray();
            
        $customer = empty($customers) ? $custom_projects : $customers;

        $delivery_unit=DB::table('trd_projects')
        ->join('trd_practice', 'trd_projects.project_unit', '=', 'trd_practice.code')
        ->where([['project_department','=',$department_code],['project_code','=',$project_code],['trd_practice.active','=',1],['type','=','delivery']])
        ->pluck('name','code')->toArray();

        // if(empty($delivery_unit)){
		// 	$delivery_unit=DB::table('trd_practice')->where('active','=',1)->orderBy('name','asc')->pluck('name','code')->toArray();
        //     $customer=DB::table('trd_projects')->whereIn('project_code',['CUST_PROJ_007'])
		//     ->pluck('customer_name','customer_code')->toArray();
		// }

        $customer_project=DB::table('trd_projects')->where('project_code','=',$project_code)->whereIn('project_code',['CUST_PROJ_007'])
		->pluck('customer_name','customer_code')->toArray();
		
		return json_encode(['customer'=>$customer,'delivery'=>$delivery_unit,'customer_project'=>$customer_project]);
	}
/**
     * To list all the request for details from the DB
     * @author venkatesan.raj
     *
     * @param string  $module
     * @param int $active_alone optional
     * @param bool $behalf_of_employee optional
     *
     * @return array
     */
    public function list_request_for( $module, $active_alone = 1, $behalf_of_employee = false)
    {
        try
        {
            $employee_behalf = ["RF_13"];
            $request_for_list = [];
            $is_behalf_of_user = $this->check_behalf_of_status(Auth::User()->aceid)['behalf_of_user'];
            $request_for_list = DB::table('trf_request_for')->where([['module',$module]]);
            if($active_alone)
                $request_for_list = $request_for_list->where('active',1);
            if(!$is_behalf_of_user)
                $request_for_list = $request_for_list->whereNotIn('unique_key', $this->CONFIG['ON_BEHALF_LIST']);
            $request_for_list = $request_for_list->orderBy('id','asc')->pluck('request_for','unique_key')->toArray();
            if($behalf_of_employee)
                return $request_for_list;
            return Arr::except($request_for_list, $employee_behalf);
        }
        catch (\Exception $e)
        {
            Log::error('Err in list_request_for');
            Log::error($e);
        }
    }

     /**
     * To list all the request for details from the DB
     * @author sasi.kuppuswamy
     *
     * @param string  $module
     * @param int $active_alone optional
     *
     * @return array
     */
    public function list_user_orgin($traveller_id, $active_alone = 1 )
    {
    
        try
        {
        $users_orgin='';
         $location_mapping=[
            'Australia'=>'COU_011','Client Location'=>'COU_014','Bangalore'=>'COU_014','Canada'=>'COU_016','Finland'=>'COU_009','Hyderabad'=>'COU_014','Ireland'=>'COU_029','Kochi'=>'COU_014','Malaysia'=>'COU_027','Mandaveli'=>'COU_014','Mexico'=>'COU_025','Middle East'=>'COU_003','Navalur'=>'COU_014','Netherlands'=>'COU_022','Poland'=>'COU_035','Singapore'=>'COU_002','Siruseri'=>'COU_014','Sri Lanka'=>'COU_036','UK'=>'COU_004','US'=>'COU_001','USA'=>'COU_001'
         ];
         
         $user_location=DB::table('users')->where('aceid',$traveller_id)->value('OfficeLocation');
         //dd($traveller_id);
            //if(array_key_exists(Auth::User()->OfficeLocation,$location_mapping))
            if(array_key_exists($user_location,$location_mapping))
                $users_orgin=$location_mapping[$user_location];
            return $users_orgin;
        }
        catch (\Exception $e)
        {
            Log::info($e);
            return '';
            
        }
    }


    /**
     * To list all the proof types
     * @author venkatesan.raj
     *
     * @param string  $module
     * @param string  $from_country
     * @param int $active_alone optional
     *
     * @return Array
     */
    public function list_proof_type( $module, $from_country, $active_alone=1 )
    {
        try
        {
            $proof_type_list = [];

            $proof_type_list = DB::table('trd_proof_type')->where([['module',$module]]);

            if($active_alone)
                $proof_type_list = $proof_type_list->where('active',1);
            $proof_type_list = $proof_type_list->orderBy('id','asc')->pluck('display_name','unique_key')->toArray();
            return $proof_type_list;
        }
        catch (Exception $e)
        {
            Log::info($e);
        }
    }

    /**
     * To list all the proof details for the particular user
     * @author venkatesan.raj
     * 
     * @param mixed $request
     * 
     * @return mixed
     */
    public function list_respective_user_proof_details( Request $request )
    {
        try
        {
            $module = $request->input('module');
            $request_for = $request->input('request_for');
            $from_country = $request->input('origin');
            $aceid = $request->input("aceid") ?? Auth::User()->aceid;
            $active_alone = $request->input('active_alone') ?? 1;
            $proof_type = $request->input('proof_type') ?? null;
            $additional_params = $request->input('additional_params') ?? null;
            $proof_type_list = (is_null($proof_type)) ? array_keys($this->list_proof_type($module, $active_alone)) : [$proof_type];
            if($module == "MOD_03") {
                $edit_id = $request->input('edit_id') ? Crypt::decrypt($request->input('edit_id')) : null;
                if($edit_id){
                    $travel_details = (array)DB::table("trf_travel_request")->where('id', $edit_id)->first();
                    $travelling_details = (array)DB::table('trf_traveling_details')->where('request_id', $edit_id)->first();
                    $travelling_details = json_decode(json_encode($travelling_details),true);
                    $request_for = $travel_details['request_for_code'];
                    $from_country = is_null($from_country) ? $travelling_details['from_country'] : $from_country;
                    $aceid = $travel_details['travaler_id'];
                }
            }

            $mandatory_field_config = [
                'MOD_01' => [
                    'COU_014' => [
                        //'PR_TY_01_01' => ['proof_number','proof_name'],
                        //'PR_TY_01_02' => ['proof_number','proof_name'],
                    ],
                    'default' => [
                        'PR_TY_01_04' => ['proof_number','proof_name','proof_issue_date','proof_expiry_date','proof_issued_place']
                    ]
                ],
                'MOD_02' => [
                    'COU_014' => [
                        'PR_TY_02_01' => ['proof_number','proof_name','proof_issue_date','proof_expiry_date','proof_issued_place'],
                        'PR_TY_02_02' => ['proof_number','proof_name'],
                    ],
                    'default' => [
                        'PR_TY_02_01' => ['proof_number','proof_name','proof_issue_date','proof_expiry_date','proof_issued_place'],
                    ],
                ],
                "MOD_03" => [
                    "COU_014" => [
                        "PR_TY_03_01" => ["proof_number","proof_name","proof_issue_date","proof_expiry_date","proof_issued_place"],
                        'PR_TY_03_02' => ['proof_number','proof_name'],
                    ],
                    'default' => [
                        "PR_TY_03_01" => ["proof_number","proof_name","proof_issue_date","proof_expiry_date","proof_issued_place"],
                    ],
                ],
            ];
            // Origin based mandatory check config
            $orgin_mandatory_check = [
                'COU_014' => ['MOD_01' => true, 'MOD_02' => true, 'MOD_03' => true],
            ];

            $mandatory_status = isset($orgin_mandatory_check[$from_country][$module]) ? $orgin_mandatory_check[$from_country][$module] : false;
            //$mandatory_status = true;

            $mandatory_field_list = array_key_exists($module, $mandatory_field_config)?$mandatory_field_config[$module] : [];
            $mandatory_field_list = array_key_exists($from_country, $mandatory_field_list)?$mandatory_field_list[$from_country]:(count($mandatory_field_list) ? [] : []);
            if($additional_params == "MANDATORY_FIELDS_ONLY")
                return compact('mandatory_field_list','mandatory_status');
            $mandatory_check = [];
            $proof_details = [];
            $visible_fields = [];
            $user_details = [];
            if($module == 'MOD_01')
                $relevent_request_for_self = $this->CONFIG['REQUEST_FOR_SELF_DOM'];
            else if($module == 'MOD_02')
                $relevent_request_for_self = $this->CONFIG['REQUEST_FOR_SELF_IN'];
            else
                $relevent_request_for_self = $this->CONFIG['REQUEST_FOR_SELF_VIS'];
            if($module == "MOD_03" && $request_for="RF_13") {
                $request_for = $this->CONFIG['REQUEST_FOR_SELF_VIS'];
            }
            foreach($proof_type_list as $proof_type_id)
                $visible_fields[$proof_type_id] = DB::table('trd_proof_additional_details')->where('proof_type_id', $proof_type_id)->pluck('input_name')->toArray();
            if($additional_params == "VISIBLE_FIELDS_ONLY")
                return ['visible_fields' => $visible_fields];
            // To check whether the user is in blocked user list
            $is_blocked_user = false;
            if(in_array($module, ['MOD_02']) && in_array($request_for, $this->CONFIG['REQUEST_FOR_SELF']) && $this->check_blocked_users($aceid))
                $is_blocked_user = true;
            if( in_array($request_for, [$this->CONFIG['REQUEST_FOR_SELF_DOM'], $this->CONFIG["REQUEST_FOR_FAMILY_DOM"], $this->CONFIG['REQUEST_FOR_SELF_IN'], $this->CONFIG["REQUEST_FOR_FAMILY_IN"], $this->CONFIG['REQUEST_FOR_SELF_VIS'], $this->CONFIG['REQUEST_FOR_FAMILY_VIS']]) )
            {
                $users = (array)DB::table('users')->where([['aceid',$aceid],['active',1]])->first();
                $dob = array_key_exists('DateOfBirth', $users) ? $users['DateOfBirth'] : null;
                $dob = $dob ? date('d-M-Y', strtotime($dob)) : null;
                $doj = array_key_exists('JoiningDate', $users) ? $users['JoiningDate'] : null;
                $doj = $dob ? date('d-M-Y', strtotime($doj)) : null;
                $address = DB::table('trf_user_detail_mapping')->where('attribute', $this->CONFIG["USRATTR"]["ADDRESS"])->where('aceid',$aceid)->value('mapping_value');
                $phone_no = DB::table('trf_user_detail_mapping')->where('attribute', $this->CONFIG["USRATTR"]["PHONE_NO"])->where('aceid',$aceid)->value('mapping_value');
                $email = DB::table('users')->where('aceid',$aceid)->value('email');
                $nationality = DB::table('trf_user_detail_mapping')->where('attribute', $this->CONFIG["USRATTR"]["NATIONALITY"])->where('aceid',$aceid)->value('mapping_value');
                $user_details =compact('dob', 'doj', 'address', 'phone_no', 'email', 'nationality');
                // To get the previously submitted data
                $fields_not_found = array_keys( array_filter( $user_details, fn($e) => !$e )) ;
                if( count( $fields_not_found ) ) {
                    $user_details_saved = $this->get_user_filled_details($aceid, $fields_not_found);
                    if( is_array( $user_details_saved ) && count( $user_details_saved ) ) 
                        $user_details = array_replace($user_details, $user_details_saved);
                }
                foreach ($proof_type_list as $proof_type_id)
                {
                    $details = DB::table('trd_proof_additional_details as tpad')
                                ->leftJoin("trf_user_detail_mapping as tudm", function($join){ $join->on("tpad.attribute_id","tudm.attribute")->where("tudm.active",1); })
                                ->select("tpad.input_name","tudm.mapping_value")
                                ->where('tpad.proof_type_id',$proof_type_id)->where('tudm.aceid',$aceid);
                    if($active_alone)
                        $details = $details->where('tpad.active',$active_alone);
                    $details = json_decode(json_encode($details->get()->toArray()),true);
                    $mapping = array_reduce($details, function ($c, $i) {$c[$i['input_name']] = $i['mapping_value']; return $c; }, []);
                    $proof_detail = ['proof_type' => $proof_type_id, 'proof_request_for' => $relevent_request_for_self];
                    foreach ($mapping as $key => $value)
                        $proof_detail[$key] = $value;
                    if(array_key_exists('proof_issue_date', $proof_detail))
                        $proof_detail['proof_issue_date'] = $proof_detail['proof_issue_date'] ? date('d-M-Y', strtotime($mapping['proof_issue_date'])) : null;
                    if(array_key_exists('proof_expiry_date', $proof_detail))
                        $proof_detail['proof_expiry_date'] = $proof_detail['proof_expiry_date'] ? date('d-M-Y', strtotime($mapping['proof_expiry_date'])) : null;
                    foreach($visible_fields[$proof_type_id] as $fields){
                        if(!array_key_exists($fields, $proof_detail))
                            $proof_detail[$fields] = null;
                    }
                    array_push($proof_details, $proof_detail);
                    $fields = array_key_exists($proof_type_id,$mandatory_field_list) ? $mandatory_field_list[$proof_type_id] : [];
                    $failed_fields = [];
                    foreach($fields as $field){
                        if(is_null($proof_detail[$field]))
                            array_push($failed_fields, $field);
                    }
                    if(count($failed_fields)) $mandatory_check[$proof_type_id] = $failed_fields;
                }
                if(is_array($mandatory_field_list) && count($mandatory_field_list))
                    $proof_details = array_values(array_filter($proof_details, fn($e) => array_key_exists($e["proof_type"],$mandatory_field_list)));
                $proof_details = array_map(fn($e) => array_merge($e, ['proof_file_details' => $this->get_proof_file_details($aceid, $module, $e['proof_type'])]), $proof_details );
            }
            if($module == "MOD_01" && count($mandatory_field_list) == 0)
            {
                $record_not_exists = false;
                $fully_avail_data = [];
                foreach($proof_details as $key => $value){
                    $proof_type = $value['proof_type'];
                    $proof_detail = array_filter($value, fn($e) => in_array(array_search($e, $value), $visible_fields[$proof_type], true));
                    if(in_array(null, $proof_detail, true)){
                        if (count($fully_avail_data) == 0) $record_not_exists = true;
                    }
                    else{
                        $record_not_exists = false;
                        array_push($fully_avail_data, $key);
                    }
                }
                if($record_not_exists)
                    $mandatory_check = array_fill(0, count($proof_type_list), null);
                else
                    $proof_details = array_values(array_filter($proof_details, fn($e) => in_array(array_search($e, $proof_details), $fully_avail_data, true)));
            }
            return compact("user_details","proof_details","visible_fields","mandatory_field_list","mandatory_check","mandatory_status", "is_blocked_user");
    
        }
        catch (\Exception $e)
        {
            Log::error("Err in list_respective_user_proof_details");
            Log::error($e);
        }
    }
    
    /**
     * This function will return the current finantial year 
     * no params required
     * @return string
     */
    public function get_current_financial_year($as_string=0){
        if ( date('m') >= 4 ) 
            $finacial_year = date('Y').'-'.(date('Y') + 1);
        else 
            $finacial_year = (date('Y') - 1).'-'.date('Y');
        if($as_string)
        return $finacial_year;
        else
        return json_encode([$finacial_year=>$finacial_year]);
    }

    /**
     * This function will return the list of currency 
     * no params required
     * @return string
     */
    public function list_worklocation($module,$active=1){
        $worklocation=DB::table('worklocation')->where('module',$module)->where('active',$active)->pluck('work_place','unique_key')->toArray();
       //dd($worklocation);
        return $worklocation;
    }  
    /**
     * This function will return the list of currency 
     * no params required
     * @return string
     */
    public function list_currency($active=1){
        $currency=DB::table('trd_currency')->where('active',$active)->pluck('currency','currency_code')->toArray();

        return $currency;
    } 
    // public function list_country($active=1){
    //     $country=DB::table('country')->where('active',$active)->pluck('name','id')->toArray();

    //     return $country;
    // }    
    public function load_from_city(Request $request){
        $origin=$request->origin_value;
        $from_city=DB::table('trd_country_city')->where('country_code', $request->origin_value)->where('active','=',1)->pluck('name','unique_key')->toArray();
        return json_encode(['from_city'=>$from_city]);
    }

    /**
     * This method will return list of financial years 
     * 
     * Two optional parameters listed down.
     * 
     * @param string $start_date => from which year to start from.
     * @param string $end_date => end of the year
     * 
     */
    public static function list_financial_years($start_date='01-03-2016',$end_date=null){
        if(!$end_date){
            if ( date('m') >= 4 ) 
                $end_year = date('Y') + 1;
            else 
                $end_year = date('Y');
        }else{
            if(date('m',strtotime($end_date))>=4)
                $end_year = date('Y',strtotime($end_date)) + 1;
            else
                $end_year = date('Y',strtotime($end_date));
        }
            if(date('m',strtotime($start_date))>=4)
                $start_year=date('Y',strtotime($start_date)) + 1;
            else
                $start_year=date('Y',strtotime($start_date));
        $fiscal_years = [];
        for($start_year;$start_year<$end_year;$start_year++){
            $fiscal_years[$start_year.'-'.($start_year+1)] = $start_year.'-'.($start_year+1);
        }
        
        if(!count($fiscal_years))
            $fiscal_years[$start_year.'-'.($start_year+1)] = $start_year.'-'.($start_year+1);
        
        krsort($fiscal_years);
        return $fiscal_years;
    }

    /**
     * Method used to list master categories
     * Created by dinakar
     * 
     * @param String (optional) $department id
     * @param Int (optional) active
     * @return Array
     */
    public function list_master_category($dept_id=null,$module=null,$active=1){
        try{
            $master_category = DB::table('trd_master_category')->select('name','unique_key')->where('active',$active);
             if($dept_id){
                $master_category = $master_category->where('dept_id',$dept_id);
            }
            if($module=='MOD_03')
                $master_category = $master_category->where('name','Visa');

            $master_category = $master_category->pluck('name','unique_key')->toArray();
            return $master_category;
        }catch (\Exception $e) {
            Log::error("Err in list_master_category");
            Log::error($e);
            return json_encode(['error'=>$e->getMessage()]);
        }
    }
    /**
     * Method used to list Category
     * Created by dinakar
     * 
     * @param String (optional) $department id
     * @param Int (optional) active
     * @return Array
     */
    public function list_category($master_category=null,$request_id=null,$active=1){
        try{
            $category = DB::table('trd_category')->select('name','unique_key')->where('active',$active);
            if($master_category){
                $category = $category->whereIn('master_category_id',$master_category);
            }
            else{
                $category = $category->where('master_category_id','MAS_005');
            }
            //dd($master_category);
                // $visa_categories=['Legal Fees','Filing Fees','Premium Processing Charges','Postage & Courier','Others'];
            
            if($request_id){
                $requires = DB::table('trf_travel_other_details')->where('request_id',$request_id)->select('ticket_required','accommodation_required','forex_required')->first();
                $module=DB::table('trf_travel_request')->where('id',$request_id)->value('module');

                $module_short_notation = $module=='MOD_03' ? 'VIS' : 'TRV';
                $module_based_master_category = DB::table('trd_master_category')->whereIn('unique_key',$master_category)->where('short_notation',$module_short_notation)->pluck('unique_key')->toArray();
                $category = $category->whereIn('master_category_id',$module_based_master_category);
                
                if( ( $requires && !$requires->ticket_required) || $module=='MOD_03')
                    $category = $category->where('name','!=','Tickets');
                if( ($requires && !$requires->accommodation_required) || $module=='MOD_03')
                    $category = $category->where('name','!=','Accomodation');
                if($module=='MOD_03')//added only for domestic and international travel and remove the visa
                    $category = $category->where('name','!=','Perdiem');
                if($module!='MOD_03'){
                    // $category = $category->whereNotIn('name',$visa_categories);
                    
                }
                
            }
            $category = $category->pluck('name','unique_key')->toArray();
            return $category;
        }catch (\Exception $e) {
            Log::error("Err in list_category");
            Log::error($e);
            return json_encode(['error'=>$e->getMessage()]);
        }
    }
    /**
     * Method used to load related categories in ajax call
     * Created by dinakar
     * 
     * @param Request 
     * @return Array
     */
    public function load_related_select_options(Request $request){
        try{
            if(isset($request->name)){
                switch ($request->name){
                    case 'department' :
                        if(isset($request->value)){
                            $list = $this->list_master_category($request->value);
                            return $list;
                        }
                        break;
                    case 'master_category' :
                        if(isset($request->value)){
                            $list = $this->list_category($request->value,$request->request_id);
                            return $list;
                        }
                        break;
                    default :
                        break;
                }
            }
            
        }catch (\Exception $e) {
            Log::error("Err in load_related_select_options");
            Log::error($e);
            return json_encode(['error'=>$e->getMessage()]);
        }
    }
    /**
     * Method to fetch users attribute mappings
     * Added by dinakar on 08 Jan 2024
     * 
     * @param String (aceid)
     * @return Object 
     */
    public function fetch_user_attributes($aceid='ACE8445',$other_details=0){
        try{
            $user_attributes = DB::table('users')->where('aceid',$aceid)->first();
            if($other_details){
                $user_attributes = DB::table('trf_user_detail_mapping')
                ->select('aceid',DB::raw("JSON_OBJECTAGG(attribute_name,mapping_value) as user_attributes"))
                ->where([['aceid',$aceid],['active',1]])->groupBy('aceid')->pluck('user_attributes','aceid')->toArray();
            }
            return ['user_attributes'=>$user_attributes];
            // return json_encode(['user_attributes'=>$user_attributes]);
        }catch (\Exception $e) {
            Log::error("Err in fetch_user_attributes");
            Log::error($e);
            return json_encode(['error'=>$e->getMessage()]);
        }
    }
    /**
     * Method to do budget verification.
     * Added by dinakar on 08 Jan 2024
     */
    public function budget_verification($form_details,$request_id,$status_id){
        try{
            // dd($form_details,$request_id,$status_id);
            $currency_master = $this->list_currency();
            $bill_row=[];$anticipated_cost_details = (object)[];
            $request_id = isset($request_id) && $request_id ? $request_id : null;
            $trf_travel_request_details = DB::table('trf_travel_request')->select('department_code','module','request_id','travaler_id','billed_to_client')->where('id',$request_id)->first();
            $travel_date = DB::table('trf_traveling_details')->where('request_id',$request_id)->value(DB::raw("DATE_FORMAT(from_date,'%b-%y')"));
            if($form_details){
                $is_blocked=false;
                // this if is to update budget result while waiting for process status
                // i.e details should be updated only in DB.
                if($status_id && in_array($status_id,['STAT_12','STAT_29','STAT_33'])){
                    $anticipated_cost_details=DB::table('trf_travel_anticipated_details as anticipated')->leftJoin('trf_travel_request as dept','dept.id','anticipated.request_id')->where([['anticipated.request_id',$request_id],['anticipated.active',1]])->select('anticipated.*','dept.department_code as department')->get();
                    $anticipated_cost_details=json_decode(json_encode($anticipated_cost_details),true);
                    $conversion_rates = DB::table('trf_currency_conversion_rates')->where('active',1)->pluck('conversion_rate','parent_currency')->toArray();
                    foreach($anticipated_cost_details as $key=>$anticipated_row_details){
                        $bill_row[$key]['bill_row'] = $anticipated_row_details;
                        $bill_row[$key]['cost_code']=$this->get_costcode($anticipated_row_details,$trf_travel_request_details->travaler_id);
                        $bill_row[$key]['bill_month'] = $travel_date;
                        $bill_row[$key]['amount'] = $anticipated_row_details['amount'] ? $anticipated_row_details['amount'] * $conversion_rates[$anticipated_row_details['anticipated_currency']] : null;
                        $bill_row[$key]['actual_amount'] = $anticipated_row_details['amount'] ? $currency_master[$anticipated_row_details['anticipated_currency']]." ".$anticipated_row_details['amount'] : null;
                        $bill_row[$key]['billable'] = $trf_travel_request_details->billed_to_client;
                    }
                    // Calculation based on approver anticipated cost
                    $approver_anticipated_cost=DB::table('trf_travel_request')->where([['id',$request_id],['active',1]])->value('approver_anticipated_amount');
                    $approver_currency_code=DB::table('trf_travel_request')->where([['id',$request_id],['active',1]])->value('approver_currency_code');
                    if($approver_anticipated_cost){
                        $rate = array_key_exists($approver_currency_code, $conversion_rates) ? $conversion_rates[$approver_currency_code] : 1;
                        $approver_anticipated_cost = $approver_anticipated_cost * $rate;
                        $total_anticipated_amount = (float)array_sum( array_filter( array_column($bill_row, 'amount'), 'is_numeric') );
                        if($total_anticipated_amount != 0)
                            $deviation_percentage = ( (float) $approver_anticipated_cost / $total_anticipated_amount ) * 100;
                        else
                            $deviation_percentage = 100;
                        foreach($bill_row as $index => $details){
                            $amount = array_key_exists('amount', $details) ? ($details['amount'] ? $details['amount'] : null) : null;
                            if(isset($amount) && $amount && is_numeric($amount)){
                                $actual_amount = (float)($deviation_percentage * $amount)/100;
                                $bill_row[$index]['amount'] = round($actual_amount, 2); 
                            }
                        }
                    }
                    // $is_blocked= $trf_travel_request_details->billed_to_client==0?true:false;
                    $is_blocked = true;
                }else{
                    $conversion_rates = DB::table('trf_currency_conversion_rates')->where('active',1)->pluck('conversion_rate','parent_currency')->toArray();
                    $details_to_costcode = [];
                    foreach($form_details['master_category'] as $key=>$val){
                        $details_to_costcode['department'] = DB::table('trf_travel_request')->where('id',$request_id)->value('department_code');
                        $details_to_costcode['master_category'] = $val;
                        $details_to_costcode['category'] = $form_details['category'][$key];
                        $details_to_costcode['sub_category'] = $form_details['sub_category'][$key];

                        $bill_row[$key]['row_no'] = $form_details['anticipated_row_id'][$key];
                        $bill_row[$key]['excluded_row'] = $form_details['excluded_row'][$key];
                        $bill_row[$key]['bill_row'] = $details_to_costcode;
                        $bill_row[$key]['cost_code']=$this->get_costcode($details_to_costcode,$trf_travel_request_details->travaler_id);
                        $bill_row[$key]['bill_month'] = $travel_date;
                        $bill_row[$key]['amount'] = $form_details['amount'][$key] ? $form_details['amount'][$key] * $conversion_rates[$form_details['anticipated_currency'][$key]] : null;
                        $bill_row[$key]['actual_amount'] = $form_details['amount'][$key] ? $currency_master[$form_details['anticipated_currency'][$key]]." ".$form_details['amount'][$key] : null;
                        $bill_row[$key]['billable'] = $trf_travel_request_details->billed_to_client;
                    }
                }
                $category = [
                    'bill_row' => $bill_row,
                    'consumed_type' => isset($form_details['module'])&&$form_details['module']=='MOD_03'?'Visa':'Travel',
                    'request_code' => $trf_travel_request_details->request_id,
                    'created_by' => $trf_travel_request_details->travaler_id,
                    'travel_request_id' => $trf_travel_request_details->request_id
                ];
                // dd($category,$is_blocked);
                ini_set('max_execution_time',7200);
                $curl=new Curl();
                $curl->setHeader('Content-Type', 'application/json');
                $curl->setBasicAuthentication('PAYMENTSSTAGE', 'PAYMENTSSTAGE@123');
                $curl->setDefaultTimeout(120);
                $data = json_encode(array (
                        'category' => $category,
                        'is_insert' => $is_blocked
                ));
                $curl->post("http:/localhost:8001/api/budget_validation",$data);
                if ($curl->error) {
                    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
                } 
                else{
                    $details = $curl->response;
                    $budget_result = []; $budget_success=[]; $message=[];
                    if(property_exists($details,'overall_result') && $details->overall_result){
                        if($status_id && in_array($status_id,['STAT_12'])){
                            foreach($anticipated_cost_details as $key=>$anticipated_row_details){
                                $budget_success[]=1;
                                $message[] = null;
                            }
                        }else{
                            if(is_array($form_details['excluded_row']))
                            foreach($form_details['excluded_row'] as $key=>$excluded_row){
                                if($excluded_row=="1"){
                                    $budget_success[]=null;
                                    $message[] = null;
                                }else{
                                    $budget_success[]=1;
                                    $message[] = null;
                                }
                            }
                        }
                        return [
                            'budget_success' => $budget_success,
                            'message'=>$message,
                        ];
                    }
                    elseif(property_exists($details,'data')){
                        $messages = '';
                        foreach($details->data as $index=> $result_row){ 
                            // this if is to update budget result while waiting for process status
                            // i.e details should be updated only in DB.
                            if($status_id && in_array($status_id,['STAT_12'])){
                                DB::table('trf_travel_anticipated_details')->where('id',$result_row->bill_row->bill_row->id)->update([
                                    'budget_success' => 0,
                                    'message' => $result_row->message
                                ]);
                                $budget_success[] = $result_row->message ? 0 : 1;
                                $message[] = ($index + 1) . '. ' . $result_row->message;
                            }else{
                                $excluded_row = $form_details['excluded_row'][$index] ?? null;
                                if($excluded_row == "1") {
                                    $budget_success[] = null;
                                    $message[] = null;
                                }else{
                                    $budget_success[] = $result_row->message ? 0 : 1;
                                    $message[] = ($index + 1) . '. ' . $result_row->message;
                                }
                            }
                        }
                        return [
                            'budget_success' => $budget_success,
                            'message'=>$message,
                        ];
                    }else{
                        return [
                            'message'=>'Something went wrong. Please write to help.mis@aspiresys.com for further assistance.'
                        ];
                    }
                    
                }
            }
        }catch (\Exception $e) {
            Log::error($e);
            throw $e;
            return json_encode(['error'=>$e->getMessage()]);
        }
    }
    /**
     * Method used to remove the blocked budget, if the request is rejected after budget blocked
     * 
     * @param String
     */
    public function remove_budget_utilized($request_code){
        try{
            if(isset($request_code)){
                ini_set('max_execution_time',7200);
                $curl=new Curl();
                $curl->setHeader('Content-Type', 'application/json');
                $curl->setBasicAuthentication('PAYMENTSSTAGE', 'PAYMENTSSTAGE@123');
                $curl->setDefaultTimeout(120);
                $data = json_encode(array (
                        'request_code' => $request_code,
                        'utilized_type' => 'blocked'
                ));
                $curl->post("http://localhost:8001/api/utilized_budget_removal",$data);
                if ($curl->error) {
                    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
                } 
                else{
                    $details = $curl->response;
                }
            }
        }catch (\Exception $e) {
            Log::error($e);
            return json_encode(['error'=>$e->getMessage()]);
        }
    }
    /**
     * Method used to generate costcode; inputs departments, master_category, category etc
     * Created by dinakar on 11 Jan 2024
     * 
     * @param Object/Array 
     * @return String
     */
    public function get_costcode($details,$aceid=null){
        try{
            if($details){
                $user_location = DB::table('users')->where('aceid',$aceid)->pluck('OfficeLocation')->toArray();
                $costcode = [
                    'department' => '',
                    'master_category' => '',
                    'category' => '',
                    'sub_category' => '-',
                    'country' => count(array_keys(array_intersect($this->location_config_for_costcode,$user_location)))
                    ?array_keys(array_intersect($this->location_config_for_costcode,$user_location))[0]:'GBL/GBL'
                    // 'location' => 'SIR'
                ];
                foreach($details as $key=>$val){
                    switch($key){
                        case 'department' :
                            $costcode[$key] = DB::table('trd_departments')->where([['code',$val],['active',1]])->value(DB::raw('CASE WHEN sl_flag = 1 THEN code ELSE short_notation END'));
                            break;
                        case 'master_category' :
                            $costcode[$key] = DB::table('trd_master_category')->where([['unique_key',$val],['active',1]])->value('short_notation');
                            break;
                        case 'category' :
                            $costcode[$key] = DB::table('trd_category')->where([['unique_key',$val],['active',1]])->value('short_notation');
                            break;
                    }
                }
                return implode("/",$costcode);
            }
        }catch (\Exception $e) {
            Log::info($e);
            return json_encode(['error'=>$e->getMessage()]);
        }
    }

    /**
     * This method is used to get the rules matched 
     * for Input fields / any other configs required
     * 
     * @param String (table name)
     * @param Object (Optional; To chech based on particular request)
     * 
     * @return Array (matched rules)
     */
    public function get_rules_matched($table,$optional_param=null){
        try{
        $module = ($optional_param && array_key_exists('module',$optional_param)) ? $optional_param['module'] : null ;
        $status = ($optional_param && array_key_exists('status',$optional_param)) ? $optional_param['status'] : null ;
        $rules_matched = [];
        $criteria = DB::table($table)
        ->select('rule_id',DB::raw('COUNT(field) as fields_count'),DB::raw('GROUP_CONCAT(field) as fields'),DB::raw('GROUP_CONCAT(cond) as cond'),DB::raw("GROUP_CONCAT(value,';') as value"))
        ->where('active',1)->groupBy('rule_id')->get();

        foreach($criteria as $c){
            $fields = explode(",",$c->fields);
            $cond = explode(",",$c->cond);
            $values = explode(";",$c->value);
            $obtained_count = 0;
            
            foreach($fields as $key => $field){
                switch ($field){
                    case 'role' :
                        $has_role = Auth::user()->has_any_role_code(array_filter(explode(",",$values[$key])));
                        if($has_role)
                            $obtained_count += 1;
                        break;
                    case 'status' :
                        if(in_array($status,explode(",",$values[$key])))
                            $obtained_count += 1;
                        break;
                    case 'module':
                        if(in_array($module,explode(",",$values[$key])))
                            $obtained_count += 1;
                        break;
                    case 'default':
                        $obtained_count += 1;
                        break;
                    default :
                        break;
                }
            }
            if($c->fields_count == $obtained_count)
                array_push($rules_matched,$c->rule_id);
        }
        return $rules_matched;
        }catch (\Exception $e) {
            Log::info($e);
            return json_encode(['error'=>$e->getMessage()]);
        }
    }

    public function fields_visibility_editablity_details($module, $status, $role, $request_id=null){
        try{
            $optional_param=[
                'module'=>$module,
                'status'=>$status,
            ];
            $rules_matched = $this->get_rules_matched('trf_input_fields_display_rule_conditions',$optional_param);
            $inputs_matched = DB::table('trf_input_fields_display_rule_mappings')->whereIn('rule_id',$rules_matched)->where('active',1)
            ->select('rule_id','visible_fields','editable_fields')->orderBy('precedence','DESC')->take(1)->get()->toArray();

            $visible_fields=count($inputs_matched)?explode(",",$inputs_matched[0]->visible_fields):[];
            $editable_fields=count($inputs_matched)?explode(",",$inputs_matched[0]->editable_fields):[];

            // Adding anticipated cost related fields for Approvers
            $editable_fields = $this->add_anticipated_cost($editable_fields, $request_id, $status);

            $rule_id=count($inputs_matched)?explode(",",$inputs_matched[0]->rule_id):[];
            $input_with_attr = DB::table('trd_input_fields as tif')
            ->join('trf_input_fields_attr as tifa',function($j){
                $j->on('tif.unique_key','tifa.input_code')->where('tifa.active',1);
            })->select('tif.unique_key','tif.lable_name','tif.input_name',DB::raw("JSON_OBJECTAGG(tifa.attr_name, tifa.attr_value) AS attributes"))
            ->where('tif.active',1)->whereIn('tif.unique_key',$visible_fields)
            ->groupBy('tif.unique_key','tif.lable_name','tif.input_name')->orderBy('tif.order_value')->get();
            $dept_id = DB::table('trf_travel_request')->where('id',$request_id)->value('department_code');
            $project = DB::table('trf_travel_request')->where('id',$request_id)->value('project_code');
            $countries=DB::table('trf_traveling_details')->where('request_id',$request_id)->pluck('to_country');
            $origin=DB::table('trf_traveling_details')->where('request_id',$request_id)->value('from_country');

            if($request_id)
            $traveller_id=DB::table('trf_travel_request')->where('id',$request_id)->value('travaler_id');
            else
            $traveller_id=Auth::user()->aceid;

            // Check the whether the request is behalf of request or not
            extract($this->check_behalf_of_status(Auth::User()->aceid, $request_id));

            $select_options['travel_purpose'] = $this->list_travel_purpose($module);
            $select_options['travel_type'] = $this->list_travel_type($module);
            $select_options['to_country'] = $this->list_country();
            $select_options['visa_type_code'] = $this->list_visa_type();
            $select_options['requestor_entity'] = $this->list_entity_details(Auth::user()->username);
            $select_options['request_for'] = $this->list_request_for($module);
            $select_options['user_orgin'] = $select_options['to_country']; // $this->list_user_orgin(Auth::user()->username);
            $select_options['proof_type'] = $this->list_proof_type($module, $select_options['user_orgin']);
            $select_options['worklocation'] = $this->list_worklocation($module);
            $select_options['currency'] = $this->list_currency();
            $select_options['approver_currency_code'] = $this->list_currency();
            $select_options['master_category'] = $this->list_master_category($dept_id,$module);
            if($behalf_of_user)
                $select_options['behalf_of'] = $this->list_behalf_of_users();
            
            $select_options['category'] = count($select_options['master_category']) && count($select_options['master_category'])
                ? $this->list_category(array_keys($select_options['master_category']),$request_id) 
                : $this->list_category();
            $from_city_request = new \Illuminate\Http\Request();
            $from_city_request->replace(['origin_value' => $origin]);
            $select_options['from_city']=(array)json_decode($this->load_from_city($from_city_request))->from_city;
            $cities = [];
            foreach ($countries as $country) {
                $city_request = new \Illuminate\Http\Request();
                $city_request->replace(['country' => $country]);
            
                $response = json_decode($this->load_city($city_request));
            
                if (isset($response->city)) {
                    $cities = array_merge($cities, (array) $response->city);
                }
            }
            $select_options['to_city_inter'] = $cities;
            $select_options['to_city']= $module=='MOD_01' ? $select_options['from_city'] : $select_options['to_city_inter'];
            $project_request = new \Illuminate\Http\Request();
            $project_request->replace(['project'=>$project]);
            $select_options['departments']=(array)json_decode($this->load_project_details($project_request))->department;
            $select_options['customer_name']=(array)json_decode($this->load_project_details($project_request))->customer;
            $select_options['practice_unit_code']=(array)json_decode($this->load_project_details($project_request))->delivery_unit;
            $select_options['anticipated_currency'] = $this->list_currency();
            $select_options['mode_code'] = $this->list_forex_process_mode();
            $select_options['currency_code'] = $this->list_currency();
            if($behalf_of_request){
                $dept = DB::table('trf_travel_request')->where([['id', $request_id],['active', 1]])->value('department_code');
                $select_options['project_code']=$this->list_projects('request', $dept);
            }else{
                $select_options['project_code']=$this->list_projects();
            }
            $select_options['visa_process'] = $this->list_visa_process();
            $select_options['visa_number'] = [];
            if(isset($request_id) && $module=="MOD_02"){
                $countries = DB::table('trf_traveling_details')->where([['request_id', $request_id],['active', 1]])->distinct()->pluck('to_country')->toArray();
                $travel_purpose = DB::table('trf_travel_request')->where('id', $request_id)->value('travel_purpose_id');
                $travel_purpose_visa_type_mapping = ["PUR_02_02" => "VIS_001", "PUR_02_03" => "VIS_002", "default" => "VIS_001"];
                $visa_type = array_key_exists($travel_purpose, $travel_purpose_visa_type_mapping) ? $travel_purpose_visa_type_mapping[$travel_purpose] : $travel_purpose_visa_type_mapping["default"];
                $aceid = $traveller_id;
                $select_options['visa_number'] = $this->get_visa_number_against_countries(compact("countries", "aceid", "visa_type"));
            }
            
            return [
                'visible_fields' => $visible_fields,
                'editable_fields' => $editable_fields,
                'field_attr' => $input_with_attr,
                'select_options' => $select_options,
                'rule_id' => $rule_id
            ];
        }catch (\Exception $e) {
            Log::info($e);
            return json_encode(['error'=>$e->getMessage()]);
        }
    }


    public function testing(Request $request){
        return $this->list_financial_years('12-Apr-2017','09-Mar-2020');
    }



    public function get_user_based_requests($respective_roles,$fin_year,$condition=null,$to_check=null)
    {
    try{    
    $final_ids=[];
    $fin_dates=$this->financial_year_dates($fin_year);
    if(in_array("requestor",$respective_roles)){
    $related_ids=DB::table('trf_travel_request')->where(function ($query){
    $query->where([['travaler_id',  Auth::User()->aceid],['status_id', '!=', 'STAT_01']])
    ->orWhere('created_by',Auth::User()->aceid);
    })->where('active',1)
    ->whereDate('created_at','>=',$fin_dates['start_date'])
    ->whereDate('created_at','<=',$fin_dates['end_date'])->pluck('id')->toArray();
    $final_ids=array_merge($related_ids,$final_ids);
    }
    if(in_array("travel_reimbursement",$respective_roles)){
        // $related_ids=DB::table('trf_travel_request')->where(function ($query){
        // $query->where('travaler_id', '=', Auth::User()->aceid);
        // //->orWhere('created_by',Auth::User()->aceid);
        // })->where('active',1)
        // ->whereDate('created_at','>=',$fin_dates['start_date'])
        // ->whereDate('created_at','<=',$fin_dates['end_date']);

        $related_ids=DB::table('trf_travel_request as ttr')
        ->join('trf_traveling_details as ttd','ttd.request_id','ttr.id')
        ->where(function ($query){
            $query->where('ttr.travaler_id', '=', Auth::User()->aceid);
            // ->orWhere('ttr.created_by',Auth::User()->aceid);
        })->where('ttr.active',1)
        ->where('ttr.created_at','>=',$fin_dates['start_date'])
        ->where('ttr.created_at','<=',$fin_dates['end_date'])
        ->whereRaw("CASE WHEN ttd.to_date IS NOT NULL 
        THEN (DATE(now()) <= DATE_ADD(ttd.to_date, INTERVAL ".$this->reimburseDuration['MONTH']." MONTH)) 
        ELSE (DATE(now()) <= DATE_ADD(ttd.from_date, INTERVAL ".$this->reimburseDuration['MONTH']." MONTH)) 
        END");
        if(isset($condition)){
            $related_ids=$related_ids->whereIn('status_id',$condition['status'])->whereIn('module',$condition['module']);
        }
        $related_ids=$related_ids->pluck('ttr.id')->toArray();
        $final_ids=array_merge($related_ids,$final_ids);
    }
    if(in_array("approver",$respective_roles)){
    $related_ids=DB::table('trf_approval_matrix_tracker')
    ->join('trf_travel_request as req','req.id','trf_approval_matrix_tracker.request_id')
    ->where([['respective_role_or_user',Auth::User()->aceid],['trf_approval_matrix_tracker.active',1]])
    //->whereIn('req.status_id',[''])
    ->whereDate('req.created_at','>=',$fin_dates['start_date'])
    ->whereDate('req.created_at','<=',$fin_dates['end_date']);
    if(isset($condition)){
        $related_ids=$related_ids->whereIn('req.status_id',$condition['status'])->whereIn('trf_approval_matrix_tracker.flow_code',$condition['role'])->where('trf_approval_matrix_tracker.is_completed',$condition['is_completed']);  
    }
    
    $related_ids=$related_ids->pluck('req.id')->toArray();
    $final_ids=array_merge($related_ids,$final_ids);
    }
    if(in_array("travel_desk",$respective_roles)){
        $related_ids=DB::table('trf_approval_matrix_tracker as tracker')
        ->leftJoin('trf_travel_request as req','req.id','tracker.request_id')
        ->leftJoin('trf_traveling_details as trv','req.id','trv.request_id')
        ->leftJoin('trf_user_role_mapping as urd','urd.role_code','tracker.respective_role_or_user')
        ->where('urd.aceid',Auth::User()->aceid)
        ->whereDate('req.created_at','>=',$fin_dates['start_date'])
        ->whereDate('req.created_at','<=',$fin_dates['end_date']);
        if(isset($condition)){
            $related_ids=$related_ids->whereIn('req.status_id',$condition['status'])
            ->whereIn('tracker.respective_role_or_user',$condition['role'])
            ->whereIn('req.id',$condition['related_ids'])
            ->where(function ($query) use($condition) {
                if( $condition['for_checked'] ) {
                    $query->where('tracker.is_completed',$condition['is_completed'])
                        ->orWhere('req.status_id', 'STAT_23');
                } else {
                    $query->where('tracker.is_completed',$condition['is_completed']);
                }
            });
        }
        $related_ids=$related_ids->pluck('req.id')->toArray();
        
        $final_ids=array_merge($related_ids,$final_ids);
    }
    if(in_array("hr_review",$respective_roles)){
        $is_hr_reviewer = false; $is_hr_partner = false;
        if( Auth::User()->has_any_role_code('HR_PRT') )
            $is_hr_partner = true;
            
        if( Auth::User()->has_any_role_code('HR_REV') )
            $is_hr_reviewer = true;

        if($is_hr_reviewer && $to_check) {
            $hr_reviewer_related_ids = DB::table('trf_travel_request')->whereIn('status_id', ['STAT_29', 'STAT_37'])->whereIn('id', $condition['related_ids'])->distinct()->pluck('id')->toArray();
            $condition['related_ids'] = $hr_reviewer_related_ids;
        }

        if($is_hr_partner){
            $hr_partner_related_ids = DB::table('trf_travel_request as tr')
                                            ->leftJoin('vrf_visa_request_details as vr', 'vr.request_id', 'tr.id') // module - MOD_03
                                            ->whereIn('tr.module', ['MOD_03'])
                                            ->where(function ($query) {
                                                $query->where('tr.created_by', Auth::User()->aceid)
                                                    ->orWhere('vr.hr_partner', Auth::User()->aceid);
                                            });
            if($to_check) $hr_partner_related_ids = $hr_partner_related_ids->whereIn('tr.status_id', ['STAT_30']);
            $hr_partner_related_ids = $hr_partner_related_ids->distinct()->pluck('tr.id')->toArray();
            $condition['related_ids'] = array_key_exists('related_ids',$condition ) ? array_unique(array_merge($condition['related_ids'], $hr_partner_related_ids )) : $hr_partner_related_ids;
        }
            
        $related_ids=DB::table('trf_approval_matrix_tracker as tracker')
        ->leftJoin('trf_travel_request as req','req.id','tracker.request_id')
        ->leftJoin('trf_traveling_details as td','td.request_id','req.request_id')
        ->leftJoin('trf_user_role_mapping as urd','urd.role_code','tracker.respective_role_or_user')
        ->leftJoin('vrf_visa_request_details as vrd', 'vrd.request_id', 'req.id')
        ->where('urd.aceid',Auth::User()->aceid)
        ->whereDate('req.created_at','>=',$fin_dates['start_date'])
        ->whereDate('req.created_at','<=',$fin_dates['end_date']);
        if(isset($condition)){
            $related_ids=$related_ids->whereIn('req.status_id',$condition['status'])->whereIn('tracker.respective_role_or_user',$condition['role'])->where('tracker.is_completed',$condition['is_completed'])->where('req.module', $condition['module'])->whereIn('req.id', $condition['related_ids']);
        }

        $related_ids=$related_ids->pluck('req.id')->toArray();

        
        $final_ids=array_merge($related_ids,$final_ids);
        }
        if(in_array("gm_review",$respective_roles)){
            $related_ids=DB::table('trf_approval_matrix_tracker as tracker')
            ->leftJoin('trf_travel_request as req','req.id','tracker.request_id')
            ->leftJoin('trf_traveling_details as td','td.request_id','req.request_id')
            ->leftJoin('trf_user_role_mapping as urd','urd.role_code','tracker.respective_role_or_user')
            ->where('urd.aceid',Auth::User()->aceid)
            ->whereDate('req.created_at','>=',$fin_dates['start_date'])
            ->whereDate('req.created_at','<=',$fin_dates['end_date']);
            if(isset($condition)){
                $related_ids=$related_ids->whereIn('req.status_id',$condition['status'])->whereIn('tracker.respective_role_or_user',$condition['role'])->where('tracker.is_completed',$condition['is_completed'])
                ->where('req.module', $condition['module'])->whereIn('req.id', $condition['related_ids']);
            }
            $related_ids=$related_ids->pluck('req.id')->toArray();
            
            $final_ids=array_merge($related_ids,$final_ids);
            }
    if(in_array("bf_reviewer",$respective_roles)){
        $related_ids=DB::table('trf_approval_matrix_tracker as tracker')
        ->leftJoin('trf_travel_request as req','req.id','tracker.request_id')
        ->leftJoin('trf_user_role_mapping as urd','urd.role_code','tracker.respective_role_or_user')
        
        ->where([['urd.aceid',Auth::User()->aceid],['tracker.active',1]])
        ->whereDate('req.created_at','>=',$fin_dates['start_date'])
        ->whereDate('req.created_at','<=',$fin_dates['end_date']);
        if(isset($condition)){
            $related_ids=$related_ids->whereIn('req.status_id',$condition['status'])->whereIn('tracker.respective_role_or_user',$condition['role'])->where('tracker.is_completed',$condition['is_completed']);  
        }
        $related_ids=$related_ids->pluck('req.id')->toArray();
        $final_ids=array_merge($related_ids,$final_ids);
    }
    if(in_array("workbench",$respective_roles)){
            $related_ids=DB::table('trf_approval_matrix_tracker as tracker')
            ->leftJoin('trf_travel_request as req','req.id','tracker.request_id')
            ->leftJoin('trf_traveling_details as trv','req.id','trv.request_id')
            ->leftJoin('trf_user_role_mapping as urd','urd.role_code','tracker.respective_role_or_user');
            $related_ids=$related_ids->where('urd.aceid',Auth::User()->aceid)
            ->whereDate('req.created_at','>=',$fin_dates['start_date'])
            ->whereDate('req.created_at','<=',$fin_dates['end_date']);
            if(isset($condition)){
                $related_ids=$related_ids->whereIn('req.status_id',$condition['status'])
                ->whereIn('tracker.respective_role_or_user',$condition['role'])
                ->whereIn('req.id',$condition['related_ids'])
                ->where(function ($query) use($condition) {
                    if( $condition['for_checked'] ) {
                        $query->where('tracker.is_completed',$condition['is_completed'])
                            ->orWhere('req.status_id', 'STAT_23');
                    } else {
                        $query->where('tracker.is_completed',$condition['is_completed']);
                    }
                });
            }
            $related_ids=$related_ids->pluck('req.id')->toArray();
            
            $final_ids=array_merge($related_ids,$final_ids);
    }
    if(in_array("reports",$respective_roles)){
    $related_ids=DB::table('trf_travel_request')
    ->leftJoin('trf_approval_matrix_tracker','trf_approval_matrix_tracker.request_id','trf_travel_request.id');
    if(isset($condition)){
        $roles=$this->CONFIG['report_configure'];
        if(Auth::User()->has_any_role_code($roles)) {
            $reports_role = array_filter($roles, fn($role) => Auth::User()->has_any_role_code($role));
            $related_ids=$this->get_travel_desk_user_details($travel_request_id=null,$reports_role,Auth::User()->aceid);
        }
        elseif(Auth::User()->has_any_role_code('REP_ACC')){
        $related_ids=$related_ids->whereIn('status_id',$condition['status'])->whereIn('module',$condition['module'])
                ->whereDate('trf_travel_request.created_at','>=',$fin_dates['start_date'])
                ->whereDate('trf_travel_request.created_at','<=',$fin_dates['end_date'])
                ->pluck('trf_travel_request.id')->toArray();
        }else{
            $related_ids=$related_ids
            ->whereIn('status_id',$condition['status'])->whereIn('module',$condition['module'])->where('respective_role_or_user',Auth::User()->aceid)
                ->whereDate('trf_travel_request.created_at','>=',$fin_dates['start_date'])
                ->whereDate('trf_travel_request.created_at','<=',$fin_dates['end_date'])
                ->pluck('trf_travel_request.id')->toArray();
        }
    }
    $final_ids=array_merge($related_ids,$final_ids);
    }
    if(in_array("visa_reports",$respective_roles)){
        $related_ids=DB::table('trf_travel_request')
        ->leftJoin('trf_approval_matrix_tracker','trf_approval_matrix_tracker.request_id','trf_travel_request.id');
        if(isset($condition)){
            if(Auth::User()->has_any_role_code('GM_REV')){
                $related_ids=$this->get_travel_desk_user_details($travel_request_id=null,['GM_REV'],Auth::User()->aceid);

            }elseif(Auth::User()->has_any_role_code('HR_REV')){
                $related_ids=$this->get_travel_desk_user_details($travel_request_id=null,['HR_REV'],Auth::User()->aceid);
            }
            elseif(Auth::User()->has_any_role_code('HR_PRT')){
                $related_ids=DB::table('trf_travel_request as tr')
                    ->leftJoin('vrf_visa_request_details as vr', 'vr.request_id', 'tr.id')
                    ->whereIn('tr.module', ['MOD_03'])
                    ->where(function ($query) {
                    $query->where('tr.created_by', Auth::User()->aceid)
                        ->orWhere('vr.hr_partner', Auth::User()->aceid);
                })->whereDate('tr.created_at','>=',$fin_dates['start_date'])
                ->whereDate('tr.created_at','<=',$fin_dates['end_date'])
                ->distinct()->pluck('tr.id')->toArray();

            }
            elseif(Auth::User()->has_any_role_code('REP_ACC')){
            $related_ids=$related_ids->whereIn('status_id',$condition['status'])->where('module',$condition['module'])
            ->whereDate('trf_travel_request.created_at','>=',$fin_dates['start_date'])
            ->whereDate('trf_travel_request.created_at','<=',$fin_dates['end_date'])
            ->pluck('trf_travel_request.id')->toArray();
            }else{
                $related_ids=$related_ids->whereIn('status_id',$condition['status'])->where('module',$condition['module'])->where('respective_role_or_user',Auth::User()->aceid)
                    ->whereDate('trf_travel_request.created_at','>=',$fin_dates['start_date'])
                    ->whereDate('trf_travel_request.created_at','<=',$fin_dates['end_date'])
                    ->pluck('trf_travel_request.id')->toArray();
            }
        }
        $final_ids=array_merge($related_ids,$final_ids);
        }
    return $final_ids;
    }catch(\Exception $ex ){
        Log::info($ex);
    }
    }
    public function get_home_details(Request $request){
    try{    
    if(isset($request['fin_year']))
    $fin_year=$request['fin_year'];
    else
    $fin_year=$this->get_current_financial_year('as_string');
    $fin_dates=$this->financial_year_dates($fin_year);
    $icons=$this->images_for_status;
    $related_ids=$this->get_user_based_requests(['requestor'],$fin_year);
    $requestList=$this->request_brief_details(
    [
    ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$related_ids]
    ],
    );
    $financial_years=$this->list_financial_years();
    return view('layouts.travel_home',['requestList'=>$requestList,'selected_years'=>$fin_year,'financial_years'=>$financial_years,'icons'=>$icons]);
    }catch(\Exception $ex ){
        Log::info($ex);
    }
    }
    public function get_travel_reimbursement(Request $request){
        try{    
        if(isset($request['fin_year']))
        $fin_year=$request['fin_year'];
        else
        $fin_year=$this->get_current_financial_year('as_string');
        $fin_dates=$this->financial_year_dates($fin_year);
        $icons=$this->images_for_status;
        $condition['status']=['STAT_12','STAT_13'];
        $condition['module']=['MOD_01','MOD_02'];
        $related_ids=$this->get_user_based_requests(['travel_reimbursement'],$fin_year,$condition);
        $requestList=$this->request_brief_details(
        [
        ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$related_ids],
     
        //['method'=>'whereIn','column'=>'trf_traveling_details.travel_type_id','condition'=>'','value'=>['TRV_01_02','TRV_01_03','TRV_02_02','TRV_02_03']]
        ],
        );
        $financial_years=$this->list_financial_years();
        return view('layouts.travel_reimbursement',['requestList'=>$requestList,'selected_years'=>$fin_year,'financial_years'=>$financial_years,'icons'=>$icons]);
        }catch(\Exception $ex ){
            Log::info($ex);
        }
    }
    public function get_approver_details(Request $request){
    try{    
    if(isset($request['fin_year']))
    $fin_year=$request['fin_year'];
    else
    $fin_year=$this->get_current_financial_year('as_string');
    $icons=$this->images_for_status;
    $fin_dates=$this->financial_year_dates($fin_year);
    $model=new User();
    $roles=$model->respective_roles_code();
    $rule_array_to_approve_related_id=[];$rule_array_approved_related_id=[];$multiple_approver_hie=[];
    $approver_role=['DEP_H','DU_H_HIE','DU_H','PRO_OW','PRO_OW_HIE','FIN_APP','GEO_H','CLI_PTR'];
    $approval_related_roles=array_intersect($approver_role,$roles);   
    foreach($approval_related_roles as $approval_related_role){
        if(array_key_exists($approval_related_role,$this->role_based_config)){
            $condition['status']=$this->role_based_config[$approval_related_role]['to_check'];
            $condition['role']=[$approval_related_role];
            $condition['is_completed']=0;
            $to_approve_ids=$this->get_user_based_requests(['approver'],$fin_year,$condition);
            $rule_array_to_approve_related_id=array_merge($rule_array_to_approve_related_id,$to_approve_ids);
            $condition['is_completed']=1;
            $approved_ids=$this->get_user_based_requests(['approver'],$fin_year,$condition);
            $multiple_approver_hie=DB::table('trf_approval_matrix_tracker')->whereIn('request_id',$approved_ids)->where('is_completed',1)->pluck('request_id')->toArray();
            $condition['status']=$this->role_based_config[$approval_related_role]['checked'];
            $approved_ids=$this->get_user_based_requests(['approver'],$fin_year,$condition);
            $rule_array_approved_related_id=array_merge($rule_array_approved_related_id,$approved_ids,$multiple_approver_hie);
        }
    }
    $rule_array_to_approve=[
     ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$rule_array_to_approve_related_id]

    ];
    $rule_array_approved=[
    ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$rule_array_approved_related_id]

    ];
    $approver_to_check=$this->request_brief_details($rule_array_to_approve)->toArray();
    $approver_checked=$this->request_brief_details($rule_array_approved)->toArray();
    $financial_years=$this->list_financial_years();
    return view('layouts.approver',['approver_to_check'=>$approver_to_check,'approver_checked'=>$approver_checked,'selected_years'=>$fin_year,'financial_years'=>$financial_years,'icons'=>$icons]);
    }catch(\Exception $ex ){
        Log::info($ex);
    }
    }
    public function get_travel_desk_details(Request $request){
    try{    
    if(isset($request['fin_year']))
    $fin_year=$request['fin_year'];
    else
    $fin_year=$this->get_current_financial_year('as_string');
    $icons=$this->images_for_status;
    $model=new User();
    $roles=$model->respective_roles();
    $role= DB::table('trd_roles as urm') 
    ->whereIn('urm.name', $roles)
    ->pluck('urm.unique_key')->toArray();
    $traveldesk_role=['AN_COST_FIN','AN_COST_FAC'];
    $traveldesk_role = array_filter( $role, fn($e) => in_array($e, $traveldesk_role) );
  
   $status_to_check=[];$status_checked=[];
   foreach($traveldesk_role as $value){
    $traveldesk_status=$this->role_based_config[$value];
    $status_to_check=array_unique(array_merge($status_to_check,$traveldesk_status['to_check']));
    $status_checked=array_unique(array_merge($status_checked,$traveldesk_status['checked']));
   }
    $related_ids=$this->get_travel_desk_user_details($travel_request_id=null,$traveldesk_role,Auth::User()->aceid);
    $condition['role']=$traveldesk_role;
    $condition['status']= $status_to_check;
    $condition['is_completed']=0;
    $condition['related_ids']=$related_ids;
    $condition["for_checked"]=false;
 
    $tocheck_related_id=$this->get_user_based_requests(['travel_desk'],$fin_year,$condition);
   // dd($tocheck_related_id);
    $condition['status']= $status_checked;
    $condition['is_completed']=1;
    $condition["for_checked"]=true;
    $checked_related_id=$this->get_user_based_requests(['travel_desk'],$fin_year,$condition);
   
    $traveldesk_array_to_approve=[

    ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$tocheck_related_id]

    ];
    $travel_desk_array_approved=[
    ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$checked_related_id]

    ];
    $travel_desk_to_check=$this->request_brief_details($traveldesk_array_to_approve)->toArray();
    $travel_desk_checked=$this->request_brief_details($travel_desk_array_approved)->toArray();
  
    $financial_years=$this->list_financial_years();
    
    return view('layouts.travel_desk',['travel_desk_to_check'=>$travel_desk_to_check,'travel_desk_checked'=>$travel_desk_checked,'selected_years'=>$fin_year,'financial_years'=>$financial_years,'icons'=>$icons]);
    }catch(\Exception $ex ){
        Log::info($ex);
    }

    }
    public function get_bfreview_details(Request $request){
        try{    
        if(isset($request['fin_year']))
        $fin_year=$request['fin_year'];
        else
        $fin_year=$this->get_current_financial_year('as_string');
        $icons=$this->images_for_status;
        $bfreview_role=[];
        if(Auth::User()->has_any_role_code(['BF_REV']))
            $bfreview_role=['BF_REV'];
      
       $status=[];
        $status=$this->role_based_config['BF_REV'];
               
        $condition['role']=$bfreview_role;
         $condition['status']= $status['to_check'];
         $condition['is_completed']=0;
     
        $tocheck_related_id=$this->get_user_based_requests(['bf_reviewer'],$fin_year,$condition);
        $condition['status']= $status['checked'];
        $condition['is_completed']=1;
        $checked_related_id=$this->get_user_based_requests(['bf_reviewer'],$fin_year,$condition);
       
        $traveldesk_array_to_approve=[
    
        ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$tocheck_related_id]
    
        ];
        $travel_desk_array_approved=[
        ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$checked_related_id]
    
        ];
        $reviewList=$this->request_brief_details($traveldesk_array_to_approve)->toArray();
        $reviewedList=$this->request_brief_details($travel_desk_array_approved)->toArray();
      
        $financial_years=$this->list_financial_years();
        
        return view('layouts.review',['reviewList'=>$reviewList,'reviewedList'=>$reviewedList,'selected_years'=>$fin_year,'financial_years'=>$financial_years,'icons'=>$icons]);
        }catch(\Exception $ex ){
            Log::info($ex);
        }
    
        }
    public function get_workbench_details(Request $request){
    try{    
    if(isset($request['fin_year']))
    $fin_year=$request['fin_year'];
    else
    $fin_year=$this->get_current_financial_year('as_string');
    $icons=$this->images_for_status;
    $model=new User();
    $roles=$model->respective_roles();
    $role= DB::table('trd_roles as urm') 
    ->whereIn('urm.name', $roles)
    ->pluck('urm.unique_key')->toArray();
    $workbench_role=['TRV_PROC_TICKET','TRV_PROC_VISA','TRV_PROC_FOREX','DOM_TCK_ADM'];
    $workbench_role = array_filter( $role, fn($e) => in_array($e, $workbench_role) );
  
   $status=[];
   foreach($workbench_role as $value){
    $workbench_status=$this->role_based_config[$value];
    $status=array_unique(array_merge($status,$workbench_status));


   }
   $related_ids=$this->get_travel_desk_user_details($travel_request_id=null,$workbench_role,Auth::User()->aceid);
    $condition['role']=$workbench_role;
     $condition['status']= array_filter($status, fn ($e) => !in_array($e, ['STAT_23']));
     $condition['is_completed']=0;
     $condition['related_ids']=$related_ids;
     $condition["for_checked"]=false;

    $tocheck_related_id=$this->get_user_based_requests(['workbench'],$fin_year,$condition);
    $condition['status'] = $status;
    $condition['is_completed']=1;
    $condition["for_checked"]=true;
    $condition["status"] = $status;
    $checked_related_id=$this->get_user_based_requests(['workbench'],$fin_year,$condition);
   
    $workbench_array_to_approve=[

    ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$tocheck_related_id]

    ];
    $workbench_desk_array_approved=[
    ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$checked_related_id]

    ];
    $workbench_tocheck=$this->request_brief_details($workbench_array_to_approve)->toArray();
    $workbench_checked=$this->request_brief_details($workbench_desk_array_approved)->toArray();
   // dd($process_list);
   $financial_years=$this->list_financial_years();
    
    return view('layouts.workbench',['workbench_tocheck'=>$workbench_tocheck,'workbench_checked'=>$workbench_checked,'selected_years'=>$fin_year,'financial_years'=>$financial_years,'icons'=>$icons]);
    }catch(\Exception $ex ){
        Log::info($ex);
    }
    }
    public function get_report_details(Request $request){
    try{    
    if(isset($request['fin_year']))
    $fin_year=$request['fin_year'];
    else
    $fin_year=$this->get_current_financial_year('as_string');
    $status=$this->role_based_config["REP_ACC"];
    $condition['status']= $status;
    $condition['module']=['MOD_01','MOD_02'];
    $related_ids=$this->get_user_based_requests(["reports"],$fin_year,$condition);
    $full_list=$this->reports_full_details([
        ['method'=>'whereIn','column'=>'tr.id','condition'=>'','value'=>$related_ids]

    ]);
   
    $financial_years=$this->list_financial_years();
    $icons=$this->images_for_status;

    // dd($full_list);
    return view('layouts.reports',['full_list'=>$full_list,'selected_years'=>$fin_year,'financial_years'=>$financial_years,'icons'=>$icons]);
    }catch(\Exception $ex ){
        Log::info($ex);
    }
    }
    /**
     * To get all the request details for HR partner and HR reviewer
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return View
     */
    public function get_hr_review_details(Request $request)
    {
        try
        {
            $fin_year = $request->input('fin_year');
            if(is_null($fin_year))
                $fin_year=$this->get_current_financial_year('as_string');
            $icons=$this->images_for_status;
            $model=new User();
            $roles=$model->respective_roles();
            $role= DB::table('trd_roles as urm') 
                    ->whereIn('urm.name', $roles)
                    ->pluck('urm.unique_key')->toArray();
            $hr_review_roles=['HR_REV', 'HR_PRT'];
            $hr_review_roles = array_filter( $role, fn($e) => in_array($e, $hr_review_roles) );
            $status_to_check=[];$status_checked=[];
            foreach($hr_review_roles as $value){
                $hr_review_status=$this->role_based_config[$value];
                $status_to_check=array_unique(array_merge($status_to_check,$hr_review_status['to_check']));
                $status_checked=array_unique(array_merge($status_checked,$hr_review_status['checked']));
            }
            $related_ids=$this->get_travel_desk_user_details($travel_request_id=null,$hr_review_roles,Auth::User()->aceid);
            $condition['role']=$hr_review_roles;
            $condition['status']= $status_to_check;
            $condition['is_completed']=0;
            $condition['module']='MOD_03';
            $condition['related_ids']=$related_ids;
      
            $tocheck_related_id=$this->get_user_based_requests(['hr_review'],$fin_year,$condition,1);
            // dd($tocheck_related_id);
            $condition['status']= $status_checked;
            $condition['is_completed']=1;
            $checked_related_id=$this->get_user_based_requests(['hr_review'],$fin_year,$condition,0);
   
            $hr_review_array_to_review=[
                ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$tocheck_related_id]
            ];
            $hr_review_array_reviewed=[
                ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$checked_related_id]
            ];
            $hr_review_to_check=$this->request_brief_details($hr_review_array_to_review)->toArray();
            $hr_review_checked=$this->request_brief_details($hr_review_array_reviewed)->toArray();
  
            $financial_years=$this->list_financial_years();
    
            return view('layouts.hr_review',['hr_review_tocheck'=>$hr_review_to_check,'hr_review_checked'=>$hr_review_checked,'selected_years'=>$fin_year,'financial_years'=>$financial_years,'icons'=>$icons]);

        }
        catch (\Exception $e)
        {
            Log::error("Error occured in get_hr_review_details");
            Log::error($e);
        }
    }
        /**
     * To get all the request details for GM reviewer
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return View
     */
    public function get_gm_review_details(Request $request)
    {
        try
        {
            $fin_year = $request->input('fin_year');
            if(is_null($fin_year))
                $fin_year=$this->get_current_financial_year('as_string');
            $icons=$this->images_for_status;
            $model=new User();
            $roles=$model->respective_roles();
            $role= DB::table('trd_roles as urm') 
                    ->whereIn('urm.name', $roles)
                    ->pluck('urm.unique_key')->toArray();
            $gm_review_roles=['GM_REV','AN_COST_VISA'];
            $gm_review_roles = array_filter( $role, fn($e) => in_array($e, $gm_review_roles) );
            $status_to_check=[];$status_checked=[];
            foreach($gm_review_roles as $value){
                $gm_review_status=$this->role_based_config[$value];
                $status_to_check=array_unique(array_merge($status_to_check,$gm_review_status['to_check']));
                $status_checked=array_unique(array_merge($status_checked,$gm_review_status['checked']));
            }
            $related_ids=$this->get_travel_desk_user_details($travel_request_id=null,$gm_review_roles,Auth::User()->aceid);
            $condition['role']=$gm_review_roles;
            $condition['status']= $status_to_check;
            $condition['is_completed']=0;
            $condition['module']="MOD_03";
            $condition['related_ids']=$related_ids;
            $tocheck_related_id=$this->get_user_based_requests(['gm_review'],$fin_year,$condition);
            // dd($tocheck_related_id);
            $condition['status']= $status_checked;
            $condition['is_completed']=1;
            $checked_related_id=$this->get_user_based_requests(['gm_review'],$fin_year,$condition);
   
            $gm_review_array_to_review=[
                ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$tocheck_related_id]
            ];
            $gm_review_array_reviewed=[
                ['method'=>'whereIn','column'=>'trf_travel_request.id','condition'=>'','value'=>$checked_related_id]
            ];
            $gm_review_to_check=$this->request_brief_details($gm_review_array_to_review)->toArray();
            $gm_review_checked=$this->request_brief_details($gm_review_array_reviewed)->toArray();
  
            $financial_years=$this->list_financial_years();
    
            return view('layouts.gm_review',['gm_review_tocheck'=>$gm_review_to_check,'gm_review_checked'=>$gm_review_checked,'selected_years'=>$fin_year,'financial_years'=>$financial_years,'icons'=>$icons]);

        }
        catch (\Exception $e)
        {
            Log::error("Error occured in get_gm_review_details");
            Log::error($e);
        }
    }
    public function request_brief_details($filter_creterias){
    try{    
    $request_brief_details=DB::table('trf_travel_request')
    ->leftJoin('trf_travel_other_details as tod','tod.request_id','trf_travel_request.id')
    ->leftJoin('trf_traveling_details','trf_traveling_details.request_id','=','trf_travel_request.id')
    ->leftJoin('users','users.aceid','trf_travel_request.travaler_id')
    ->leftJoin('trd_departments','trd_departments.code','trf_travel_request.department_code')
    ->leftJoin('trd_projects','trd_projects.project_code','trf_travel_request.project_code')
    ->leftJoin('trd_modules','trd_modules.unique_key','trf_travel_request.module')
    ->leftJoin('trd_country_details','trd_country_details.unique_key','trf_traveling_details.to_country')
    ->leftJoin('trd_status','trd_status.unique_key','trf_travel_request.status_id')
    ->leftJoin('trf_approval_matrix_tracker','trf_approval_matrix_tracker.request_id','=','trf_travel_request.id')
    ->leftJoin('trd_country_city as to_city','to_city.unique_key','trf_traveling_details.to_city')
    ->leftJoin('trd_country_city as from_city','from_city.unique_key','trf_traveling_details.from_city')
    ->leftJoin('trd_country_details as to_country','to_country.unique_key','trf_traveling_details.to_country')
    ->leftJoin('trd_country_details as from_country','from_country.unique_key','trf_traveling_details.from_country')
    ->leftJoin('vrf_visa_request_details as vr', 'vr.request_id', 'trf_travel_request.id')
    ->leftJoin('trd_visa_type as vt', 'vt.unique_key', 'vr.visa_type')
    ->select('trf_travel_request.id','trf_travel_request.request_id','trf_travel_request.travaler_id','users.username',
    'trf_travel_request.created_at','trd_departments.name as dep_name','trd_projects.project_name','trf_approval_matrix_tracker.is_completed', 'trf_travel_request.module' ,DB::raw("CASE WHEN trf_travel_request.module = 'MOD_03' THEN CONCAT(trd_modules.module_name, ' - ', COALESCE(vt.name, '') ) ELSE trd_modules.module_name END as module_name"),
    'trd_country_details.name','tod.forex_required','trf_traveling_details.from_date','trf_traveling_details.to_date','trd_status.name as status_name','trf_traveling_details.travel_type_id','trf_travel_request.status_id',
    'trf_traveling_details.from_city','trf_traveling_details.to_city','trf_traveling_details.to_country','to_city.name as to_city_name','from_city.name as from_city_name','from_country.name as from_country_name','to_country.name as to_country_name'
     //DB::raw("max(trf_traveling_details.from_date)"),DB::raw("max(trf_traveling_details.to_date)"),DB::raw("count(trf_traveling_details.from_date)"),
    // DB::raw("max(trd_country_details.name)")
   );
    
    if(count($filter_creterias)){
    foreach($filter_creterias as $filters){
    if($filters['condition'])
    $request_brief_details=$request_brief_details->{$filters['method']}($filters['column'],$filters['condition'],$filters['value']);
    else
    $request_brief_details=$request_brief_details->{$filters['method']}($filters['column'],$filters['value']);
    }
    }
    $request_brief_details=$request_brief_details->where('trf_traveling_details.active',1)->where('trf_travel_request.active',1)->orderBy('trf_travel_request.id','DESC')->groupBy('trf_travel_request.id')->get();
    
   // dd($request_brief_details);
    return $request_brief_details;
    }catch(\Exception $ex ){
        Log::info($ex);
    }
    }
    public function financial_year_dates($fin_year){
    try{    
    $year_data=explode('-',$fin_year);
    $start_date=$year_data[0].'-04-01';
    $end_date=$year_data[1].'-03-31';
    return ['start_date'=>$start_date,'end_date'=>$end_date];
    }catch(\Exception $ex ){
        Log::info($ex);
    }
    }
        public function get_travel_desk(){
        $ace_ids=DB::table('trf_approval_matrix_tracker as tracker')
        ->leftJoin('trf_user_role_mapping as urd','urd.role_code','tracker.respective_role_or_user')
        ->leftJoin('trf_travel_request as req','req.id','trf_approval_matrix_tracker.request_id')
        ->where('urd.aceid',Auth::User()->aceid)
        ->value('urd.aceid');
        return $ace_ids;
    }

    /**
     * @param int $request_id
     * to fetch the details all the details related to user request
     * @return array 
     */
    public function request_full_details($request_id=null){
        try{
            $key = $this->get_new_key();

            $request_details=DB::table('trf_travel_request as tr')
            ->leftJoin('trf_travel_other_details as tod','tod.request_id','tr.id')
            // ->leftJoin('trf_travel_request_proof_details as trpd','trpd.request_id','tr.id')
            // ->leftJoin('trf_traveling_ticket_file_details as ttfd','ttfd.request_id','tr.id')
            // ->leftJoin('trf_traveling_visa_file_details as tvfd','tvfd.request_id','tr.id')
            ->leftJoin('trd_modules as tm','tm.unique_key','tr.module')
            ->leftJoin('trd_travel_purpose as tp','tp.unique_key','tr.travel_purpose_id')
            ->leftJoin('trd_departments as dept','dept.code','tr.department_code')
            ->leftJoin('trd_practice as practice','practice.code','tr.practice_unit_code')
            ->leftJoin('users as du_user','du_user.aceid','=','practice.head')
            ->leftJoin('trd_projects as project','project.project_code','tr.project_code')
            ->leftJoin('trd_status as status','status.unique_key','tr.status_id')
            ->leftJoin('users as created','created.aceid','tr.created_by')
            ->leftJoin('users as traveler','traveler.aceid','tr.travaler_id')
            ->leftJoin('users as man', 'man.aceid', 'traveler.ReportingToAceId')
            ->leftJoin('trf_request_for as request_for','request_for.unique_key','tr.request_for_code')
            ->leftJoin('trd_projects as projects','projects.project_code','tr.project_code')
            ->leftJoin('trd_currency as tc', 'tc.currency_code', 'tr.approver_currency_code')
            ->where('tr.id',$request_id)
            ->select('tr.id as travel_request_id','tr.module','tm.module_name','tr.request_id as request_code','tr.travaler_id','traveler.username as traveler_name','tr.created_by','created.username as created_by_name',DB::raw('CONCAT_WS(" - ", practice.name,du_user.username) as practice_unit_name'),
            'tr.project_code','project.project_name','tr.department_code','dept.name as department_name','tr.practice_unit_code',/*'practice.name as practice_unit_name',*/'tr.travel_purpose_id','tp.name as travel_purpose','tr.request_for_code','request_for.request_for as request_for',
            'tr.status_id','tr.requestor_entity','tr.billed_to_client','status.name as status_name','traveler.FirstName as traveler_full_name','traveler.OfficeLocation as traveler_location',
            'tod.ticket_required','tod.forex_required','tod.currency_code as forex_currency','tod.accommodation_required',
            'tod.prefered_accommodation','tod.working_from','tod.laptop_required','tod.insurance_required','tod.family_traveling',
            'tod.no_of_members','tod.traveller_address','tod.phone_no','tod.email','tod.dob','tod.nationality','projects.customer_code','projects.customer_name','dept.sl_flag as dept_sl_flag','tc.currency as approver_currency', 'tr.approver_currency_code', 'tr.approver_anticipated_amount','man.username as primary_manager')
            ->groupBy('travel_request_id')
            ->first();

            $visa_details = DB::table('trf_travel_request as tr')
                            ->leftJoin('users as u', 'u.aceid', 'tr.travaler_id')
                            ->leftJoin('vrf_visa_request_details as tvrd','tvrd.request_id','tr.id')
                            ->leftJoin('vrd_visa_entries_master as venm', 'venm.unique_key', 'tvrd.entry_type_id')
                            ->leftJoin('trd_visa_process as tvp','tvp.unique_key','tvrd.visa_process')
                            ->leftJoin('trd_visa_type as tvt','tvt.unique_key','tvrd.visa_type')
                            ->leftJoin('vrd_visa_category_master as vcm', 'vcm.unique_key', 'tvrd.visa_category')
                            ->leftJoin('visa_process_job_details as vpjd', 'vpjd.process_request_id', 'tr.id')
                            ->leftJoin('visa_filing_master as vfm', 'vfm.id', 'vpjd.filing_type_id')
                            ->leftJoin('visa_process_employee_details as vped', 'vped.process_request_id', 'tr.id')
                            ->leftJoin('education_category_master as ecm', 'ecm.id', 'vped.education_category_id')
                            ->leftJoin('education_details_master as edm', 'edm.id', 'vped.education_details_id')
                            ->leftJoin('visa_process_review_details as vprv', 'vprv.process_request_id', 'tr.id')
                            ->leftJoin('visa_immigration_job_title_master as ijtm', 'ijtm.id', 'vprv.us_job_title_id')
                            ->leftJoin('users as reporting_manager', 'reporting_manager.aceid', 'vprv.us_manager_id')
                            ->leftJoin('visa_petitioner_entity_master as vpem', 'vpem.id', 'vprv.entity_id')
                            ->leftJoin('visa_attorneys_master as vam', 'vam.id', 'vprv.attorneys_id')
                            ->leftJoin('vrd_visa_entries_master as vem', 'vem.unique_key', 'tvrd.entry_type_id')
                            ->leftJoin('visa_interview_type_master as vitm', 'vitm.id', 'vprv.visa_interview_type_id')
                            ->leftJoin('visa_status_master as vsm', 'vsm.id', 'vprv.visa_status_id')
                            ->leftJoin('visa_process_travel_details as vptd', 'vptd.process_request_id', 'tr.id')
                            ->leftJoin('visa_travel_type_master as vttm', 'vttm.id', 'vptd.traveling_type_id')
                            ->leftJoin('visa_process_tracking_details as vptr','vptr.process_request_id', 'tr.id')
                            ->leftJoin('visa_job_titile_master as vjtm', 'vjtm.id', 'vpjd.job_titile_id')
                            ->leftJoin('trd_currency as c', 'c.currency_code', 'tvrd.visa_currency')
                            ->where('tr.id',$request_id)
                            ->select('u.Firstname', 'vcm.name as visa_category', 'vcm.unique_key as visa_category_code', 'tvrd.entry_type_id','tvrd.visa_currency', 'tvrd.visa_number', 'venm.name as entry_type', 'vpjd.filing_type_id', 'vfm.filing_type', DB::raw('DATE_FORMAT(date_of_birth, "%d-%b-%Y") as date_of_birth'), 'vped.address', 'vped.education_category_id', 'ecm.shortterm as education_category', 'vped.education_details_id', 'edm.qualification as education_details', DB::raw('DATE_FORMAT(date_of_joining, "%d-%b-%Y") as date_of_joining'), 'vped.india_experience', 'vped.overall_experience', 'vped.cv_file_path', 'vped.degree_file_path', 'tvrd.visa_process as visa_process_code','tvp.name as visa_process','tvrd.visa_type as visa_type_code','tvt.name as visa_type','tvrd.visa_renewal_options',DB::raw("DATE_FORMAT(tvrd.exiting_date, '%d-%b-%Y') as exiting_date"),'vpjd.minimum_wage', 'vpjd.work_location','vped.band_detail',DB::raw("CAST( AES_DECRYPT(vprv.salary_range_from, UNHEX( SHA2( '".$key."', 512 ) ) ) AS  DECIMAL) as salary_range_from"), DB::raw("CAST( AES_DECRYPT(vprv.salary_range_to, UNHEX( SHA2( '".$key."', 512 ) ) ) AS  DECIMAL) as salary_range_to"), 'vprv.us_job_title_id', 'ijtm.name as us_job_title', 'vprv.acceptance_by_user', DB::raw("CAST( AES_DECRYPT(vprv.us_salary, UNHEX( SHA2( '".$key."', 512 ) ) ) AS  DECIMAL) as us_salary"), DB::raw("CAST( AES_DECRYPT(vprv.one_time_bonus, UNHEX( SHA2( '".$key."', 512 ) ) ) AS  DECIMAL) as one_time_bonus"), DB::raw("DATE_FORMAT(vprv.one_time_bonus_payout_date, '%d-%b-%Y') as one_time_bonus_payout_date"), DB::raw("DATE_FORMAT(vprv.next_salary_revision_on, '%d-%b-%Y') as next_salary_revision_on"), "vprv.us_manager_id", "reporting_manager.username as us_manager",'vprv.inszoom_id', 'vprv.entity_id', 'vpem.petitioner_entity as entity', 'vprv.attorneys_id', 'vam.name as attorneys', 'vprv.receipt_no', DB::raw('DATE_FORMAT(vprv.petition_file_date, "%d-%b-%Y") as petition_file_date'), DB::raw('DATE_FORMAT(vprv.petition_start_date, "%d-%b-%Y") as petition_start_date'), DB::raw('DATE_FORMAT(vprv.petition_end_date, "%d-%b-%Y") as petition_end_date'), 'vprv.petition_file_path', 'tvrd.entry_type_id', 'vem.name as entry_type', 'vprv.visa_interview_type_id', 'vitm.type as visa_interview_type', DB::raw('DATE_FORMAT(vprv.visa_ofc_date, "%d-%b-%Y") as visa_ofc_date'), DB::raw('DATE_FORMAT(vprv.visa_interview_date, "%d-%b-%Y") as visa_interview_date'), 'vprv.visa_status_id', 'vsm.status as visa_status', DB::raw('DATE_FORMAT(vptd.travel_date, "%d-%b-%Y") as travel_date'),'vptd.travel_location', 'vprv.visa_file_path', 'vptd.traveling_type_id', 'vttm.name as traveling_type', 'vptr.offer_letter_path','vptr.immigration_offer_letter_path','vptr.record_number',DB::raw('DATE_FORMAT(vptr.most_recent_doe, "%d-%b-%Y") as most_recent_doe'),DB::raw('DATE_FORMAT(vptr.admit_until, "%d-%b-%Y") as admit_until'),DB::raw('DATE_FORMAT(vptr.gc_initiated_on, "%d-%b-%Y") as gc_initiated_on'), 'vptr.green_card_title', 'tvrd.visa_currency', 'c.currency as visa_currency_notation', 'vpjd.job_titile_id as job_title_id', 'vjtm.name as job_title')
                            ->first();
            
            $visa_dependency_details = DB::table('visa_process_dependency_details')
                                            ->where([['process_request_id', $request_id],['active', 1]])
                                            ->orderBy('id')->pluck('dependency_details')->toArray();

                                            $travelling_details=DB::table('trf_traveling_details as td')
                                            ->leftJoin('trd_country_details as to_country','to_country.unique_key','td.to_country')
                                            ->leftJoin('trd_country_city as to_city','to_city.unique_key','td.to_city')
                                            ->leftJoin('trd_country_details as from_country','from_country.unique_key','td.from_country')
                                            ->leftJoin('trd_country_city as from_city','from_city.unique_key','td.from_city')
                                            ->leftJoin('trd_travel_types as travel_types','travel_types.unique_key','td.travel_type_id')
                                            ->leftJoin('trd_visa_type as visa_type','visa_type.unique_key','td.visa_type_code')
                                            ->leftJoin('trf_traveling_ticket_file_details as ticket_file',function($join){$join->on('ticket_file.traveling_id', 'td.id')->where('ticket_file.active',1);})
                                            ->where([['td.request_id',$request_id],['td.active',1]])
                                            ->select('td.id as travelling_details_row_id','td.from_country','from_country.name as from_country_name','td.to_country','to_country.name as to_country_name',
                                            'td.from_city', DB::raw(" IFNULL(from_city.name, td.from_city) as from_city_name "), 'td.to_city', DB::raw(" IFNULL(to_city.name, td.to_city) as to_city_name ") ,DB::raw('DATE_FORMAT(td.from_date, "%d-%b-%Y") as from_date'),DB::raw('DATE_FORMAT(td.to_date, "%d-%b-%Y") as to_date'),'td.visa_number','td.visa_type_code','visa_type.name as visa_name','td.visa_expiry_date'
                                            ,'travel_types.unique_key as travel_type_id','travel_types.type as travel_type',DB::raw('GROUP_CONCAT(ticket_file.original_file_name) as ticket_display_name'),DB::raw("GROUP_CONCAT(CONCAT('/ticket_uploads/',ticket_file.system_name)) as ticket_file_location"),'ticket_file.cost as ticket_cost',DB::raw('GROUP_CONCAT(ticket_file.id) as ticket_file_reference_id'))
                                            ->groupBy('td.id')->get();
            

            $anticipated_details=DB::table('trf_travel_anticipated_details as ad')
            ->leftJoin('trd_master_category as mc','mc.unique_key','ad.master_category')
            ->leftJoin('trd_category as category','category.unique_key','ad.category')
            ->leftJoin('trd_currency as currency','currency.currency_code','ad.anticipated_currency')
            ->where([['ad.request_id',$request_id],['ad.active',1]])
            ->select('mc.name as master_category_name','mc.unique_key as master_category','category.name as category_name','category.unique_key as category','ad.sub_category as sub_category_name','ad.sub_category','currency.currency as anticipated_currency_name'
            ,'currency.currency_code as anticipated_currency','ad.amount',
            'ad.amount as amount_name','ad.anticipated_comments','ad.anticipated_comments as anticipated_comments_name','ad.travel_date','ad.id as anticipated_row_id'
            ,'budget_success','message')->get();


            $proof_details=DB::select("
                SELECT JSON_OBJECTAGG(pad.input_name,trpd.value) as proof_value, rpfd.original_name, rpfd.system_name, tpt.proof_type,tpt.display_name as proof_display_name, tpt.unique_key as proof_type_id, trfc.request_for as proof_request_for, trfc.unique_key as request_for_code, trpd.request_proof_file_id as file_reference_id, tpt.upload_path
                FROM trf_travel_request_proof_details as trpd
                LEFT JOIN trf_request_proof_file_details AS rpfd ON trpd.request_id = rpfd.request_id AND trpd.proof_type_id = rpfd.file_type_id AND trpd.request_for_code = rpfd.request_for_code AND trpd.request_proof_file_id = rpfd.id AND rpfd.active = 1
                LEFT JOIN trd_proof_type AS tpt ON tpt.unique_key = trpd.proof_type_id
                LEFT JOIN trf_request_for AS trfc ON trfc.unique_key = trpd.request_for_code
                LEFT JOIN trd_proof_additional_details as pad on pad.id=trpd.proof_attr_id
                
                WHERE trpd.request_id = ? AND trpd.active = 1
                GROUP BY trpd.request_proof_file_id
            ",[$request_id]);

            // dd($request_id);

            $billable_details=DB::table('trf_request_status_tracker as status_tracker')
            ->leftJoin('trf_approval_matrix_tracker as approval_tracker','approval_tracker.respective_role_or_user','status_tracker.created_by')
            ->leftJoin('trd_roles','trd_roles.unique_key','approval_tracker.flow_code')
            ->leftJoin('users','users.aceid','status_tracker.created_by')
            ->where([['status_tracker.request_id',$request_id]])
            ->whereNotNull('billed_to_client')
            ->select('status_tracker.billed_to_client','trd_roles.name as role_name','users.username as approver_name')
            ->groupBy('approver_name')
            ->get();

            
            // dd($proof_details);
            
            /*$proof_details=DB::table('trf_travel_request_proof_details as proof_details')
            ->leftJoin('trf_request_proof_file_details as proof_files',function($query){
                $query->on('proof_details.request_id','proof_files.request_id')
                ->where([['proof_details.proof_type_id','proof_file.file_type_id'],['proof_details.request_for_code','proof_file.request_for_code']])
                ->where('proof_details.request_proof_file_id','proof_file.id');
            })
            ->where('proof_details.request_id',25)
            ->select(DB::raw('JSON_OBJECTAGG(proof_details.proof_attr_id,proof_details.value) as proof_value'),'proof_details.proof_type_id',DB::raw('MAX(proof_files.original_name)'),'proof_files.system_name')
            // ->select('proof_details.proof_type_id','proof_files.original_name','proof_files.system_name')
            ->groupBy('proof_details.proof_type_id','proof_details.request_for_code')
            ->get();
            dd($proof_details);*/

            //need to save the request_proof_details id to the request_proof_file_details instead of the request id
            // $proof_details=DB::table('trf_request_proof_details as proof_details')
            // ->leftJoin('trf_request_proof_file_details as proof_file','proof_file.')
            $forex_details=DB::table('trf_forex_load_details as fd')
                ->leftJoin('trd_forex_process_mode as m','m.mode_code','fd.mode_code')
                ->leftJoin('trd_currency as c','c.currency_code','fd.currency_code')
                ->where([['fd.request_id',$request_id],['fd.active',1]])
                ->select('fd.id as forex_details_row_id',DB::raw('DATE_FORMAT(fd.transaction_date, "%d-%b-%Y") as transaction_date'),
                'transaction_type','m.mode_code','c.currency_code','m.mode','c.currency','amount', 'fd.comments')
                ->get();

                $approval_tracker=DB::table('trf_approval_matrix_tracker as tracker')
                ->leftJoin('users','users.aceid','tracker.respective_role_or_user')
                ->leftJoin('trd_roles as roles','roles.unique_key','tracker.flow_code')
                ->where('tracker.request_id',$request_id)
                ->where('tracker.active',1)
                ->select('roles.name as user_role','users.username')
                ->orderByRaw("FIELD(tracker.flow_code, 'AN_COST_FIN','AN_COST_FAC','AN_COST_VISA','PRO_OW','BF_REV','PRO_OW_HIE','DU_H','DU_H_HIE','DEP_H','FIN_APP','TRV_PROC_TICKET','TRV_PROC_FOREX') asc")
                ->get();

                $approval_tracker_details=DB::table('trf_approval_matrix_tracker as tracker')
                ->where('tracker.request_id',$request_id)
                ->where('tracker.active',1)
                ->select('tracker.flow_code','tracker.respective_role_or_user as user_involved','tracker.is_completed')
                ->get();
		// To check whether the ticket is processed or not
                $ticketProcessFlowcodes = ['TRV_PROC_TICKET','DOM_TCK_ADM'];
                $forexProcessFlowCodes = ['TRV_PROC_FOREX'];
                $approval_tracker_details_arr = $approval_tracker_details->toArray();
                $ticket_flow = isset($approval_tracker_details_arr) && is_array($approval_tracker_details_arr) ?  current(array_filter($approval_tracker_details_arr, fn($e) => in_array($e->flow_code, $ticketProcessFlowcodes))) : false;
                $forex_flow = isset($approval_tracker_details_arr) && is_array($approval_tracker_details_arr) ?  current(array_filter($approval_tracker_details_arr, fn($e) => in_array($e->flow_code, $forexProcessFlowCodes))) : false;
                $is_ticket_processed = $ticket_flow && property_exists($ticket_flow, 'is_completed') ? $ticket_flow->is_completed : false;
                $is_forex_processed = $forex_flow && property_exists($forex_flow, 'is_completed') ? $forex_flow->is_completed : false;

            return [
                'request_details'=>$request_details,
                'visa_details' => $visa_details,
                'dependency_details' => $visa_dependency_details,
                'travelling_details'=>$travelling_details,
                'proof_details'=>$proof_details,
                'anticipated_details'=>$anticipated_details,
                'billable_details'=>$billable_details,
                'forex_details'=>$forex_details,
                'approval_flow' => $approval_tracker,
                'approval_tracker_details'=> $approval_tracker_details,
                'status_details' => $this->get_status_details($request_id),
                'is_ticket_processed' => $is_ticket_processed,
                'is_forex_processed' => $is_forex_processed,
            ];

        }catch(\Exception $e){
            Log::error('error in the request full details function');
            Log::error($e);
            
            return '';

        }
    }
 /**
     * To list all the payment mode from the DB
     * Added by monisha.thirumalai
     * @param Integer $active_alone optional
     * @return Array
     */
    public function list_forex_process_mode($active_alone=1){
        try{
            $payment_type=DB::table('trd_forex_process_mode')->where('active','=',$active_alone)->pluck('mode','mode_code')->toArray();
            return $payment_type;
        }catch(\Exception $ex ){
                Log::info($ex);
        }
    }
    /**
     * To list all the visa process
     * @author venkatesan.raj
     * 
     * @param int active_alone optional
     * 
     * @return mixed
     */
    public function list_visa_process($active_alone=1)
    {
        try
        {
            $visa_process = DB::table('trd_visa_process');
            if($active_alone)
                $visa_process = $visa_process->where('active', $active_alone);
            return $visa_process->pluck('name', 'unique_key')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error('Err in list_visa_process');
            Log::error($e);
        }
    }
    /**
     * To list all the modules in the system
     * @author venkatesan.raj
     * 
     * @param int $active_alone optional
     * @param string $specification optional
     * 
     * @return mixed
     */
    public function list_travel_request_for( $specification = null, $active_alone = 1)
    {
        $travel_request_for = DB::table('trd_modules');
        if($active_alone)
            $travel_request_for = $travel_request_for->where('active', $active_alone);
        if(is_null($specification))
            $travel_request_for = $travel_request_for->whereIn('unique_key', ['MOD_01', 'MOD_02']);
        else{
            if($specification == 'visa')
                $travel_request_for = $travel_request_for->where('unique_key', 'MOD_03');
            else
                $travel_request_for = $travel_request_for->whereIn('unique_key', ['MOD_01', 'MOD_02']);
        }
        return $travel_request_for->pluck('module_name', 'unique_key')->toArray();
    }


    public function get_request_for(Request $request){
        //$request_for=DB::table('trf_request_for')->where('module',$request->module_name)->pluck('request_for','unique_key')->toArray();
        $request_for=$this->list_request_for($request->input('module_name'));
        $travel_type=DB::table('trd_travel_types')->where('module',$request->module_name)->pluck('type','unique_key')->toArray();
        $travel_purpose=DB::table('trd_travel_purpose')->where('module',$request->module_name)->where('active',1)->pluck('name','unique_key')->toArray();

        return json_encode(['request_for'=>$request_for,'travel_type'=>$travel_type,'travel_purpose'=>$travel_purpose]);
    }

    public function forex_actions(Request $request){

        $forex_array_to_insert=$request->data;
        $travleRequest = new TravelRequest();
            $new_request = new \Illuminate\Http\Request();
            $new_request->replace($forex_array_to_insert);
            $is_success = $travleRequest->save_or_update($new_request);
        return $is_success;
    }
    /**
     * To get all the status details against the request
     * @author venkatesan.raj
     * 
     * @param int $request_id
     * @param int active_alone optional
     * 
     * @return mixed
     */
    public function get_status_details($request_id, $active_alone = 1)
    {
        try
        {
            $status_details = DB::table('trf_request_status_tracker as trst')
                            ->leftJoin('users as u', 'u.aceid', 'trst.created_by')
                            ->select('u.username as action_by', DB::raw("DATE_FORMAT(trst.created_at,'%d-%b-%Y') as action_on"),'billed_to_client', 'comments', 'trst.old_status_code', 'trst.new_status_code', 'trst.action' )
                            ->where('trst.request_id', $request_id);//->whereRaw('trst.old_status_code != trst.new_status_code');
            if($active_alone)
                $status_details = $status_details->where('trst.active', $active_alone);
            $status_details = $status_details->orderby('trst.id')->get()->toArray();
            $remarks_details = array_combine(
                array_map(fn($e) => current(array_keys(array_filter($this->CONFIG['REMARKS'], fn($d) => in_array($e->old_status_code.'-'.$e->new_status_code, $d)))), $status_details),
                array_map(fn($e) => $e->comments, $status_details),
            );
            $status_details_array = json_decode(json_encode($status_details),true);
            $last_action_details = count($status_details_array) ? $status_details_array[array_key_last($status_details_array)] : [];
            $remarks = ( $last_action_details['old_status_code'] == $last_action_details["new_status_code"] ) || ( $last_action_details['old_status_code'] == 0 && $last_action_details["new_status_code"] == 'STAT_01' )
                        ? $last_action_details["comments"] : null;
            
            $status_details = json_decode(json_encode($status_details),true);

            $status_details = array_map(function($e){
                $element = $e;
                $new_status_code = array_key_exists("new_status_code", $element) ? $element["new_status_code"] : null;
                $old_status_code = array_key_exists("old_status_code", $element) ? $element["old_status_code"] : null;
                $action = array_key_exists("action", $element) ? $element["action"] : null;
                $overall_config = $this->CONFIG["STATUS_TRACKER"];
                $config_against_new_status = array_key_exists($new_status_code, $overall_config) ? $overall_config[$new_status_code] : $overall_config["default"];
                $config_against_old_status = array_key_exists($old_status_code, $config_against_new_status) ? $config_against_new_status[$old_status_code] : $config_against_new_status;
                $config_against_action = array_key_exists($action, $config_against_old_status) ? $config_against_old_status[$action] : $config_against_old_status;
                $action_by_label = array_key_exists("ROLE", $config_against_action) ? $config_against_action["ROLE"] : null;
                $action_on_label = array_key_exists("ACTION", $config_against_action) ? $config_against_action["ACTION"] : null;
                return array_merge( $element, compact("action_by_label", "action_on_label") );
            }, $status_details);

            // dd($status_details);
 
            $status_details['remarks_details'] = $remarks_details;
            $status_details['remarks'] = $remarks;

            $status_id=DB::table('trf_travel_request')->where('id',$request_id)->value('status_id');
            $status_flow_code_mapping=$this->CONFIG['status_flow_code_mapping'];
            if (isset($status_flow_code_mapping[$status_id])) {
                $flow_codes = array_keys($status_flow_code_mapping[$status_id]);
                $approval_matrix=DB::table('trf_approval_matrix_tracker as tracker')
                ->leftJoin('users as u','u.aceid','tracker.respective_role_or_user')
                ->leftJoin('trf_travel_request as trd','trd.id','tracker.request_id',)
                ->where(['tracker.request_id'=>$request_id],['is_completed',0],['tracker.active',1])
                ->whereIn('flow_code', $flow_codes)
                ->pluck('u.username','flow_code')->toArray();
            
                $status_details['approval_matrix']=$approval_matrix;
                $status_details['status_flow_code_mapping']=$this->CONFIG['status_flow_code_mapping'];

            }
            return $status_details;
        }
        catch (\Exception $e)
        {
            Log::error('Err in get_status_details');
            Log::error($e);
        }
    }
    public function travel_request_fetch(Request $request){
       // return 'testwe'; 
       //return $request->id;
       $request=DB::table('trf_travel_request')->where('request_id', $request->id)->value('id');
         $request_details=$this->request_full_details($request);
         return $request_details;
    }

     /**
     * To fetch the proof file details from already exiting record
     * 
     * @param string $aceid
     * @param string $module
     * @param string $proof_type
     * @param int $active_alone
     * 
     * @return mixed
     */
    public function get_proof_file_details($aceid, $module, $proof_type, $active_alone = 1)
    {
        try
        {
            $revelent_proof_types = current(array_filter($this->CONFIG["RELEVENT_PROOF_TYPE"], fn($e) => in_array($proof_type, $e)));
            $file_details = DB::table('trf_travel_request as ttr')
                            ->leftJoin('trf_request_proof_file_details as trpfd', function ($join) { $join->on('trpfd.request_id','ttr.id')->where('trpfd.active',1); })
                            ->select('trpfd.file_type_id', 'trpfd.system_name as name', 'trpfd.original_name as file_name')
                            ->where('ttr.travaler_id', $aceid)->whereIn('file_type_id', $revelent_proof_types)->whereNotIn('ttr.status_id', ['STAT_01'])
                            ->whereIn('trpfd.request_for_code', [$this->CONFIG["REQUEST_FOR_SELF_DOM"], $this->CONFIG["REQUEST_FOR_SELF_IN"], $this->CONFIG["REQUEST_FOR_SELF_VIS"]]);
            if($active_alone)
                $file_details = $file_details->where('ttr.active',1);
            $file_details = $file_details->latest('trpfd.updated_at')->first();
            if(!$file_details)
                return [];
            $proof_file_details = json_decode(json_encode($file_details),true);
            $directory = DB::table('trd_proof_type')->where('unique_key',$proof_type)->value('upload_path');
            $file_name = is_array($proof_file_details) && array_key_exists('name', $proof_file_details) ? $proof_file_details['name'] : null;
            $file_size = file_exists(public_path($directory.'/'.$file_name)) ? filesize(public_path($directory.'/'.$file_name)) : 0;
            $proof_file_details['file_path'] = $directory.'/'.$file_name;
            $proof_file_details['file_size'] = $file_size;
            return $proof_file_details;
        }
        catch (\Exception $e)
        {
            Log::error("Err in get_proof_file_details");
            Log::error($e);
        }
    }

    public function reports_full_details($filter_creterias){
        try{
        $full_details=DB::table('trf_travel_request as tr')
        ->leftJoin('trd_modules as tm','tm.unique_key','tr.module')
        ->leftJoin('trf_travel_other_details as tod','tod.request_id','tr.id')
        ->leftJoin('trf_request_for as request_for','request_for.unique_key','tr.request_for_code')
        ->leftJoin('trd_travel_purpose as tp','tp.unique_key','tr.travel_purpose_id')
        ->leftJoin('trd_projects as project','project.project_code','tr.project_code')
        ->leftJoin('trd_departments as dept','dept.code','tr.department_code')
        ->leftJoin('trd_practice as practice','practice.code','tr.practice_unit_code')
        ->leftJoin('trd_status as status','status.unique_key','tr.status_id')
        ->leftJoin('users as created','created.aceid','tr.created_by')
        ->leftJoin('users as traveler','traveler.aceid','tr.travaler_id')
        ->leftJoin('trf_traveling_details as td','td.request_id','tr.id')
        ->leftJoin('trd_country_details as to_country','to_country.unique_key','td.to_country')
        ->leftJoin('trd_country_city as to_city','to_city.unique_key','td.to_city')
        ->leftJoin('trd_country_details as from_country','from_country.unique_key','td.from_country')
        ->leftJoin('trd_country_city as from_city','from_city.unique_key','td.from_city')
        ->leftJoin('trd_travel_types as travel_types','travel_types.unique_key','td.travel_type_id')
        ->leftJoin('worklocation','worklocation.unique_key','tod.working_from')
        ->select('tr.id as travel_request_id','tr.request_id as request_id','tr.module','tm.module_name','tr.travaler_id','traveler.username as traveler_name','tr.created_by','created.username as created_by_name','tr.created_at',
        'tr.project_code','project.project_name','tr.department_code','dept.name as department_name','tr.practice_unit_code','practice.name as practice_unit_name','tr.travel_purpose_id','tp.name as travel_purpose','tr.request_for_code','request_for.request_for as request_for',
        'tr.status_id','status.name as status_name','tr.requestor_entity','tr.billed_to_client',
        'tod.ticket_required','tod.forex_required','tod.currency_code as forex_currency','tod.accommodation_required',
        'tod.prefered_accommodation','tod.working_from','worklocation.work_place','tod.laptop_required','tod.insurance_required','tod.family_traveling',
        'tod.no_of_members','tod.traveller_address','tod.phone_no','tod.email','tod.dob','tod.nationality','project.customer_code','project.customer_name',
        'td.travel_type_id','travel_types.type as travel_type','from_country.name as from_country_name','to_country.name as to_country_name','from_city.name as from_city_name','to_city.name as to_city_name'
        ,'td.from_date','td.to_date','td.id as traveling_id');

        if(count($filter_creterias)){
            foreach($filter_creterias as $filters){
            if($filters['condition'])
            $full_details=$full_details->{$filters['method']}($filters['column'],$filters['condition'],$filters['value']);
            else
            $full_details=$full_details->{$filters['method']}($filters['column'],$filters['value']);
            }
            }
        $full_details=$full_details->where('tr.active',1)->orderBy('tr.id','DESC')->groupBy('tr.id','td.id')->get();
        return $full_details;
        
        }catch(\Exception $e){
            dd($e);
        }
    }
public function route_details(Request $request){
    $travelling_details=DB::table('trf_traveling_details as td')
        ->leftJoin('trd_country_details as to_country','to_country.unique_key','td.to_country')
        ->leftJoin('trd_country_city as to_city','to_city.unique_key','td.to_city')
        ->leftJoin('trd_country_details as from_country','from_country.unique_key','td.from_country')
        ->leftJoin('trd_country_city as from_city','from_city.unique_key','td.from_city')
        ->leftJoin('trd_travel_types as travel_types','travel_types.unique_key','td.travel_type_id')
        ->where([['td.request_id',$request->requestId],['td.active',1]])
        ->select('td.id as travelling_details_row_id','td.from_country','from_country.name as from_country_name','td.to_country','to_country.name as to_country_name',
        'td.from_city','from_city.name as from_city_name','td.to_city','to_city.name as to_city_name',DB::raw('DATE_FORMAT(td.from_date, "%d-%b-%Y") as from_date'),DB::raw('DATE_FORMAT(td.to_date, "%d-%b-%Y") as to_date'),
        'travel_types.unique_key as travel_type_id','travel_types.type as travel_type')
        ->get();
    return json_encode(['travelling_details'=>$travelling_details]);
}
public function department_du_validation(Request $request){
    try{
        $department_selected=$request['department'];
        $project_code=$request['project_code'];
        $func_sales_dept=$this->FUNC_SALES_DEPT_CODES;
        if(in_array($department_selected,$func_sales_dept)){
            return json_encode(['du_disabled'=>true]);
        }
        else{
            if(in_array($project_code,$this->CONFIG['CUSTOM_PROJECT']))
            return json_encode(['du_disabled'=>false]);
            else
            return json_encode(['du_disabled'=>true]);
        }
    }
    catch(\Exception $e){
        dd($e);
        Log::error("error in department_du_validation");
        Log::error($e);
        return json_encode(['du_disabled'=>true]);
    }
    }
    /**
     * To add anticipated related fields in editable fields for DU head
     * @author venkatesan.raj
     * 
     * @param array $editable_fields
     * @param string $request_id
     * @param string $status_id
     * 
     * @return boolean
     */
    public function add_anticipated_cost($editable_fields, $request_id, $status_id)
    {
        try
        {
            $anticipated_cost_editable_fields = ["INP_082", "INP_083"];
            if(is_null($request_id))
                return array_diff($editable_fields, $anticipated_cost_editable_fields);
            $approver = Auth::User()->aceid;
            $traveler = DB::table('trf_travel_request')->where([['id', $request_id],['active', 1]])->value('travaler_id');
            $tracker_details = DB::table('trf_approval_matrix_tracker')->where([['request_id', $request_id],['active', 1]])->pluck('respective_role_or_user', 'flow_code')->toArray();
            $du_head = array_key_exists('DU_H', $tracker_details) ? $tracker_details['DU_H'] : null;
            $dept_head = array_key_exists('DEP_H', $tracker_details) ? $tracker_details['DEP_H'] : null;
            $client_partner = array_key_exists('CLI_PTR', $tracker_details) ? $tracker_details['CLI_PTR'] : null;
            $geo_head = array_key_exists('GEO_H', $tracker_details) ? $tracker_details['GEO_H'] : null;
            $need_to_add = DB::table('trf_travel_request')->where([['id', $request_id],['active', 1]])->where(function ($query) {
              $query->whereNull('approver_anticipated_amount')->orWhere('approver_anticipated_amount', '');
            })->exists();
            $allowed_users = DB::table('trf_user_role_mapping')->where([['aceid', $traveler],['active', 1]])->whereIn('role_code', ['DEP_H', 'GEO_H'])->exists();
            // If approver ( DU head or Dept head ) is not mapped for the current flow
            $approvers_need_to_check = ["DU_H", "DEP_H"];
            $approver_not_mapped = count ( array_intersect( $approvers_need_to_check, array_keys($tracker_details) ) ) == 0;
            if($need_to_add)
            {
                if($du_head == $approver && $status_id == "STAT_08")
                    return $editable_fields;
                else if($dept_head == $approver && $status_id == "STAT_10")
                    return $editable_fields;
                else if($client_partner == $approver && $status_id == "STAT_24")
                    return $editable_fields;    
                else if($geo_head == $approver && $status_id == "STAT_25")
                    return $editable_fields;
                else if(array_key_exists('BF_REV', $tracker_details) && $status_id == "STAT_05" && (in_array($traveler, [$du_head, $dept_head, $geo_head]) || $allowed_users || $approver_not_mapped))
                    return $editable_fields;
                else
                    return array_diff($editable_fields, $anticipated_cost_editable_fields);
            }
            else
                return array_diff($editable_fields, $anticipated_cost_editable_fields);
        }
        catch(\Exception $e)
        {
            Log::error("Err in add_anticipated_cost");
            Log::error($e);
        }
    }
    /**
     * To list the behalf of users
     * @author venkatesan.raj
     * 
     * @param string $aceid optional
     * @param int active_alone optional
     * 
     * @return object
     */
    public function list_behalf_of_users($aceid = null, $active_alone = null)
    {
        try
        {
            $aceid = $aceid ?? Auth::User()->aceid;
            $active_alone = $active_alone ? $active_alone : 1;
            $request_details = DB::table('trd_behalf_of_mapping as bo')
                                ->leftJoin('users as u', 'u.aceid', 'bo.behalf_of_user')
                                ->select(DB::raw("CONCAT(u.aceid, '-', u.username) as display_name"), 'u.aceid')
                                ->where('bo.configured_user', $aceid);
            if($active_alone)
                $request_details = $request_details->where('bo.active', $active_alone);
            $request_details = $request_details->orderBy('u.username')->pluck('display_name', 'u.aceid')->toArray();
            return $request_details;
        }
        catch (\Exception $e)
        {
            Log::error('Err in list_behalf_of_users');
            Log::error($e);
        }
    }
    /**
     * To load the origin for behalf of users
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return object
     */
    public function fetch_user_details_on_behalf(Request $request)
    {
        try
        {
            $aceid = $request->input('aceid') ?? Auth::User()->aceid;
            $user_details = DB::table('users as u')
                            ->leftJoin('trd_entity as e', 'e.unique_key', 'u.SourceCompanyID')
                            ->select('u.DepartmentID', 'e.entity_name')
                            ->where([['u.aceid', $aceid],['u.active', 1]])->first();
            // dd($user_details);
            $traveler_dept = $user_details->DepartmentID;
            $entity = $user_details->entity_name;
            if(isset($aceid)){
                $result = array();
                // To get the origin of the traveler
                $origin = $this->list_user_orgin($aceid);
                // To list the projects for the traveler
                $project_list = $this->list_projects("request", $traveler_dept);

                $default_project = null;
                if(in_array($traveler_dept, $this->FUNC_SALES_DEPT_CODES)){
                    $default_project = $this->CONFIG['DEFAULT_PROJECT']['FUNCTIONAL'];
                }

                $result = array_filter(compact('origin', 'project_list', 'default_project', 'traveler_dept', 'entity'));

                return json_encode($result);    
                
            }else{
                return null;
            }
        }
        catch (\Exception $e)
        {
            Log::error('Err in fetch_user_details_on_behalf');
            Log::error($e);
        }
    }
    /**
     * To check whether the request is behalf of request and the user has the access to raise the behalf of request
     * @author venkatesan.raj
     * 
     * @param string $aceid
     * @param string $request_id optional
     * 
     * @return array
     */
    public function check_behalf_of_status($aceid, $request_id = null)
    {
        try
        {
            $behalf_of_user = false; $behalf_of_request = false;
            if(DB::table('trd_behalf_of_mapping')->where([['configured_user', Auth::User()->aceid], ['active', 1]])->exists())
                $behalf_of_user = true;
            if(isset($request_id) && DB::table('trf_travel_request')->where([['id', $request_id],['active', 1]])->whereColumn('travaler_id', '!=', 'created_by')->exists())
                    $behalf_of_request = true;
            return compact('behalf_of_user', 'behalf_of_request');
        }
        catch(\Exception $e)
        {
            Log::error('Err in check_behalf_of_status');
            Log::error($e);
        }
    }
public function api_travel_details(Request $request){
    try{
        if($request['api_key']=='abcdefgh'){
            $conditions=[
                ['method'=>'whereIn','column'=>'tr.status_id','condition'=>'','value'=>['STAT_12','STAT_13','STAT_14']]
            ];
            $request_details=$this->reports_full_details($conditions);
            return $request_details;
        }
    }
    catch(\Exception $e){
        dd($e);
    }
}
    /**
     * To get the details of blocked users
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return mixed
     */
    public function get_block_users(Request $request)
    {
        try
        {
            // Get already blocked users
            $blocked_users = DB::table('trf_blocked_users')
                                ->where('active', 1)
                                ->distinct()->pluck('aceid')->toArray();
            
            $blocked_users_details = User::select('username', 'aceid')
                                        ->where('active',1)->whereIn('aceid', $blocked_users)
                                        ->get()->toArray();

            $blockable_user_details = User::select('username', 'aceid')
                                        ->where('active',1)->whereNotIn('aceid', $blocked_users)
                                        ->get()->toArray();

            $columns = [
                ['title' => 'ACE Number', 'data' => 'aceid'],
                ['title' => 'User name', 'data' => 'username'],
                ['title' => 'Action', 'data' => 'aceid', 'class' => 'action', 'orderable' => false],
            ];
            return json_encode([
                'can_block' => ['data' => $blockable_user_details, 'columns' => $columns],
                'blocked' => ['data' => $blocked_users_details, 'columns' => $columns],
            ]);

        }
        catch (\Exception $e)
        {
            Log::error('Error in get_blocked_users');
            Log::error($e);
        }
    }

    /**
     * To check whether user is in blocked user list
     * @author venkatesan.raj
     * 
     * @param string $aceid
     * 
     * @return bool
     */
    public function check_blocked_users($aceid)
    {
        try
        {
            return DB::table('trf_blocked_users')->where([['aceid', $aceid], ['active', 1]])->exists();
        }
        catch(\Exception $e)
        {
            Log::error('Error in check_blocked_users');
            Log::error($e);
        }
    }

    /**
     * To fetch user details from previously submitted request
     * @author venkatesan.raj
     * 
     * @param string $aceid
     * @param array $required_field
     * 
     * @return object
     */
    function get_user_filled_details($aceid, $required_fields)
    {
        try
        {
            //$required_fields ??= ['dob', 'address', 'phone_no', 'email', 'nationality'];
            $request_types = $this->CONFIG["REQUEST_FOR_SELF"];
            $statuses = ['STAT_01'];

            $request_id = DB::table('trf_travel_request')
                            ->where([['travaler_id', $aceid],['active', 1]])
                            ->whereIn('request_for_code', $request_types)->whereNotIn('status_id', $statuses)
                            ->latest('created_at')->value('id');
            $request_details = (array) DB::table('trf_travel_other_details')
                                ->select(DB::raw('DATE_FORMAT(dob, "%d-%b-%Y") as dob'), 'traveller_address as address', 'email', 'phone_no', 'nationality')
                                ->where([['request_id', $request_id], ['active', 1]])
                                ->first();
            $request_details = array_intersect_key($request_details, array_flip($required_fields));

            return $request_details;
        }
        catch (\Exception $e)
        {
            Log::error('Err in get_user_filled_details');
            Log::error($e);
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Below function returns request IDs which are greater than configured duration. 
     * To remove Blocked budget in Budget system
     * 
     * @return array
     */
    public function getExpiredRequests($aceid=null){
        try{
            
           // $query=($aceid==null)?"":" and ttr.travaler_id = ".$aceid;
            $expired_requests = DB::select("
                SELECT ttr.request_id
                FROM trf_travel_request as ttr
                JOIN trf_traveling_details as ttd on ttd.request_id = ttr.id
                WHERE ttr.active = 1 and
                    CASE WHEN ttd.to_date IS NOT NULL 
                    THEN (DATE(now()) > DATE_ADD(ttd.to_date, INTERVAL ".$this->reimburseDuration['MONTH']." MONTH)) 
                    ELSE (DATE(now()) > DATE_ADD(ttd.from_date, INTERVAL ".$this->reimburseDuration['MONTH']." MONTH)) 
                    END and
                ttr.status_id in ('STAT_12','STAT_13');
            ");
            return array_column($expired_requests,'request_id');
        }
        catch (\Exception $e)
        {
            Log::error('Err in getExpiredRequests');
            Log::error($e);
        }
    }
    public function travel_id_fetch(Request $request){
        // return 'testwe'; 
        //return $request->id;
        $requests = DB::select("
        SELECT ttr.request_id
        FROM trf_travel_request as ttr
        JOIN trf_traveling_details as ttd on ttd.request_id = ttr.id
        WHERE ttr.active = 1 and
            CASE WHEN ttd.to_date IS NOT NULL 
            THEN (DATE(now()) <= DATE_ADD(ttd.to_date, INTERVAL ".$this->reimburseDuration['MONTH']." MONTH)) 
            ELSE (DATE(now()) <= DATE_ADD(ttd.from_date, INTERVAL ".$this->reimburseDuration['MONTH']." MONTH)) 
            END and
        ttr.status_id in ('STAT_12','STAT_13') and ttr.module != 'MOD_03' and ttr.travaler_id = '".$request->aceid."';
    ");
        return array_column($requests,'request_id');
    
     }

    /**
     * To lost the details for long term visa
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function load_employee_list(Request $request)
    {
        try
        {
            $visa_type = $request->input("visa_type");
            $defualt_request_for=null; $request_for=null; $employee=null;
            if($visa_type == "VIS_002" && Auth::User()->has_any_role_code(['HR_PRT','PR_MAN'])){
                $employee = $this->list_reporting_users(Auth::User()->aceid);
                $defualt_request_for = 'RF_13';
                $request_for_list = $this->list_request_for('MOD_03',1,true);
            } else {
                $request_for_list = $this->list_request_for('MOD_03');
            }
            return [
                "default_request_for" => $defualt_request_for,
                "request_for_list" => $request_for_list,
                "employee" => $employee,
            ];
        }
        catch (\Exception $e)
        {
            Log::error("Error in load_employee_list");
            Log::error($e);
        }
    }

    /**
     * To lost the visa categories
     * @author venkatesan.raj
     * 
     * @param Request $request
     * @return 
     */
    public function load_visa_category(Request $request)
    {
        try
        {
            $visa_type = $request->input("visa_type");
            $from_country = $request->input("from_country");
            $to_country = $request->input("to_country");
            return [
                "visa_category" => $this->list_visa_category($visa_type, $from_country, $to_country),
            ];
        }
        catch(\Exception $e)
        {
            Log::error("Error in load_visa_category");
            Log::error($e);
        }
    }

    /**
     * To list the visa category based on from, to country and visa type
     * @author venkatesan.raj
     * 
     * @param string $visa_type
     * @param string $from_country
     * @param string $to_country
     * 
     * @return array
     */
    public function list_visa_category($visa_type, $from_country, $to_country)
    {
        try
        {  
            $excludable_categories = ['VIS_CAT_005'];
            $condition = [['active', 1],['visa_type', $visa_type]];
            $sub_condition = [['to_country', $to_country]];
            $details = DB::table('vrd_visa_category_master')->where($condition);
            $details_with_country = DB::table('vrd_visa_category_master')->where(array_merge($condition, $sub_condition));
            if($details_with_country->exists()){
                $details = $details_with_country->pluck('name', 'unique_key')->toArray();
                if(Auth::User()->has_any_role_code(['HR_PRT']))
                    return $details;
                return Arr::except($details, $excludable_categories);
            }
            else
                return $details->whereNull(['from_country', 'to_country'])->pluck('name', 'unique_key')->toArray();   
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_visa_category");
            Log::error($e);
        }
    }

    /**
     * Returns the user details
     * @author venkatesan,raj
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function load_visa_user_details(Request $request)
    {
        try
        {
            $aceid = $request->input('aceid') ?? Auth::User()->aceid;
            $active = $request->input('active') ?? true;
            return $this->get_visa_user_details($aceid, $active);

        }
        catch(\Exception $e)
        {
            Log::error("Err in load_visa_user_details");
            Log::error($e);
        }
    }

    /**
     * Returns the user details
     * @author venkatesan.raj
     * 
     * @param string $aceid
     * @param bool $active optional
     * 
     * @return array
     */
    public function get_visa_user_details( $aceid, $active = true )
    {
        try
        {
            $details = DB::table("users as u")
                        ->leftJoin("trd_entity as e", function ($join) { $join->on("e.unique_key", "u.SourceCompanyID")->where("e.active", 1);})
                        ->select("e.entity_name as entity","DepartmentId")
                        ->where("u.aceid", $aceid);
            if($active) $details = $details->where("u.active", 1);
            $user_details = (array) $details->first();
            $default_project = null;
            $user_department = $user_details["DepartmentId"];
            if(in_array($user_department, $this->FUNC_SALES_DEPT_CODES)){
                $default_project = $this->CONFIG['DEFAULT_PROJECT']['FUNCTIONAL'];
            }
            $projects = $this->list_projects("request", $user_department);
            $user_details["default_project"] = $default_project;
            $user_details["projects"] = $projects;
            $user_details["origin"] = $this->list_user_orgin($aceid);
            $user_details["default_customer"] = $default_project ? Project::where("project_code", $default_project)->value('customer_code') : null; 
            return $user_details;
        }
        catch(\Exception $e)
        {
            Log::error("Err in get_visa_user_details");
            Log::error($e);
        }
    }

    /**
     * To list the visa filing type
     * @author venkatesan.raj
     * 
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_visa_filing_type($active_alone = true)
    {
        try
        {
            $visa_filing_types = DB::table('visa_filing_master')->where('visa_process_id', 1);
            if($active_alone) $visa_filing_types = $visa_filing_types->where('active', 1);
            return $visa_filing_types->orderBy('id')->pluck('filing_type', 'id')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_visa_filing_type");
            Log::error($e);
        }
    }

    /**
     * To list all the visa entry types
     * @author venkatesan.raj
     * 
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_visa_entry_type($active_alone = true)
    {
        try
        {
            $visa_entry_types = DB::table('vrd_visa_entries_master');
            if($active_alone) $visa_entry_types = $visa_entry_types->where('active', 1);
            return $visa_entry_types->orderBy('id')->pluck('name', 'unique_key')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_visa_entry_type");
            Log::error($e);
        }
    }
    /**
     * To list the all reportees to the given manager
     * @author venkatesan.raj
     * 
     * @param string $aceid
     * 
     * @return array
     */
    public function list_reporting_users($aceid)
    {
        try
        {
            $user_roles = DB::table('trf_user_role_mapping')->where([ ['aceid', $aceid],['active', 1] ])->distinct()->pluck('role_code')->toArray();
            if(in_array('HR_PRT', $user_roles))
                return $this->list_reporting_users_hr($aceid);
            else if(in_array('PR_MAN', $user_roles))
                return $this->list_reporting_users_hr($aceid);
            else
                return [];
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_reporting_users");
            Log::error($e);
        }
    }
    /**
     * To list all the employees reporting to HR partner
     * @author venkatesan.raj
     * 
     * @param string $aceid
     * 
     * @return array
     */
    public function list_reporting_users_hr($aceid)
    {
        try
        {
            ini_set('max_execution_time',7200);

            $service_config = $this->service_url_config;
            $service_name = "USERLIST";
            $url = $service_config[$service_name]["url"];
            $username = $service_config[$service_name]["username"];
            $password = $service_config[$service_name]["password"];
            
            $curl=new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setBasicAuthentication($username, $password);
            $curl->setDefaultTimeout(720);
            $data = json_encode(array (
                    'ACENumber' => $aceid,
                    'RelationId' => 1
            ));
            $curl->post($url,$data);
            if ($curl->error) {
                throw new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n");
                return [];
            } 
            else {
                $usersDetailsList=$curl->response;
                $userDetails = property_exists($usersDetailsList, 'employees') ? $usersDetailsList->employees : [];
                if(empty($userDetails)) {
                    return [];
                }
                $user_details = array_combine(
                    array_map(fn($e) => $e->ACEID, $userDetails),
                    array_map(fn($e) => $e->UserName, $userDetails)
                );
                return array_unique(Arr::except($user_details, [$aceid]));
            }
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_reporting_users_hr");
            Log::error($e);
        }

    }
    /**
     * To list all the educational categories
     * @author venkatesan.raj
     * 
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_education_category($active_alone = true)
    {
        try
        {
            $education_category = DB::table('education_category_master');
            if($active_alone) $education_category->where('active',1);
            return $education_category->orderBy('id')->pluck('shortterm', 'id')->toArray();
        }
        catch (\Exception)
        {
            Log::error("Error in list_education_category");
            Log::error($e);
        }
    }
    /**
     * 
     */
    public function load_education_details(Request $request)
    {
        try
        {
            $education_category = $request->input('category');
            if(!$education_category) return [];
            return json_encode(['education_details' => $this->list_education_details($education_category)]);
        }
        catch (\Exception $e)
        {
            Log::error("Error in load_education_category");
            Log::error($e);
        }
    }
    /**
     * To list the education details
     * @author venkatesan.raj
     * 
     * @param string $education_category
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_education_details($education_category, $active_alone = true)
    {
        try
        {
            $education_details = DB::table('education_details_master')->where('category_id', $education_category);
            if($active_alone) $education_details = $education_details->where('active', 1);
            return $education_details->orderBy('id')->pluck('qualification', 'id')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_education_details");
            Log::error($e);
        }
    }
    /**
     * To list all the job titles based on the traveler's band level
     * @author venkatesan.raj
     * 
     * @param string $band_level
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_job_titles($band_level, $active_alone=true)
    {
        try
        {
            $job_titles = DB::table('visa_immigration_job_title_master')
                            ->where('level', $band_level);
            if($active_alone) $joj_titles = $job_titles->where('active',1);
            return $job_titles->orderBy('id')->pluck('name', 'id')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_job_titles");
            Log::error($e);
        }
    }
    /**
     * To list all reporting managers
     * @author venkatesan.raj
     * 
     * @param string $country_code
     * @param bool $active_alone optional
     * 
     * @return array
     */ 
    public function list_reporting_managers($country_code, $active_alone=true)
    {
        try
        {

            $country_location_mapping = [
                "COU_001" => ["US", "USA"],
                "COU_002" => ["Singapore"],
                "COU_003" => ["Middle East"],
                "COU_004" => ["UK"],
                "COU_009" => ["Finland"],
                "COU_011" => ["Australia"],
                "COU_014" => ["Siruseri", "Kochi", "Bangalore", "Hyderabad", "Mandaveli", "Navalur"],
                "COU_035" => ["Poland"],
                "COU_025" => ["Mexico"],
                "COU_036" => ["Sri Lanka"],
                "COU_022" => ["Netherlands"],
                "COU_016" => ["Canada"],
                "COU_027" => ["Malaysia"],
                "COU_029" => ["Ireland"],
            ];

            $country_names = array_key_exists($country_code, $country_location_mapping) ? $country_location_mapping[$country_code] : null;
            if(is_null($country_names)) {
                $reporting_managers = DB::table('users')->where('active', 1)
                                    ->orderBy('id', 'desc')->distinct()->pluck('ReportingToACEID')->toArray();
            } else {
                $reporting_managers = DB::table('users')
                                    ->whereIn('OfficeLocation', $country_names)->where('active', 1)
                                    ->orderBy('id', 'desc')->distinct()->pluck('ReportingToACEID')->toArray();
            }            
            // Incase reporting managers for the location is empty, managers of overall location will be loaded                        
            if( count($reporting_managers) == 0 ) {
                $reporting_managers = DB::table('users')->where('active', 1)
                                    ->orderBy('id', 'desc')->distinct()->pluck('ReportingToACEID')->toArray();
            }
            return DB::table('users')->where(function ($query) use($active_alone, $reporting_managers) {
                if($active_alone) $query->where('active', 1);
                $query->whereIn('aceid', $reporting_managers);
            })->orderBy('username', 'asc')->pluck('username','aceid')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_reporting_manager");
            Log::error($e);
        }
    }
    /**
     * To list all the petitioner entities
     * @author venkatesan.raj
     * 
     * @param string $visa_process_id
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_petitioner_entity($country_id='COU_014', $visa_process_id=1, $active_alone=true)
    {
        try
        {
            $mapped_country_list = DB::table('visa_petitioner_entity_master')->where('active',1)->distinct()->pluck('country_id')->toArray();
            if(!in_array($country_id,$mapped_country_list)) $country_id='COU_014';
            return DB::table('visa_petitioner_entity_master')
                    ->where(function ($query) use($visa_process_id, $active_alone, $country_id) {
                        if($active_alone) $query->where('active', 1);
                        $query->where('visa_process_id', $visa_process_id)->where('country_id',$country_id);
                    })->orderBy('id')->pluck('petitioner_entity', 'id')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_petitioner_entity");
            Log::error($e);
        }
    }
    /**
     * To list all the attorneys
     * @author venkatesan.raj
     * 
     * @param string $visa_process_id
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_attorneys($country_id='COU_014' ,$visa_process_id=1,  $active_alone=true)
    {
        try
        {
            $mapped_country_list = DB::table('visa_attorneys_master')->where('active',1)->distinct()->pluck('country_id')->toArray();
            if(!in_array($country_id,$mapped_country_list)) $country_id='COU_014';
            return DB::table('visa_attorneys_master')
                    ->where(function ($query) use($visa_process_id, $active_alone, $country_id) {
                        if($active_alone) $query->where('active', 1);
                        $query->where('visa_process_id', $visa_process_id)->where('country_id',$country_id);
                    })->orderBy('id')->pluck('name', 'id')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_attorneys");
            Log::error($e);
        }
    }
    /**
     * To list all the visa entry types
     * @author venkatesan.raj
     * 
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_visa_entry_types($active_alone = true)
    {
        try
        {
            return DB::table('vrd_visa_entries_master')
                    ->where(function ($query) use($active_alone) {
                        if($active_alone) $query->where('active', 1);
                    })->orderBy('id')->pluck('name', 'unique_key')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_visa_entry_types");
            Log::error($e);
        }
    }
    /**
     * To list all the visa interview types
     * @author venkatesan.raj
     * 
     * @param string $visa_process_id
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_visa_interview_types($visa_process_id = 1, $active_alone = true)
    {
        try
        {
            return DB::table('visa_interview_type_master')
                    ->where(function ($query) use($visa_process_id, $active_alone) {
                        if($active_alone) $query->where('active', 1);
                        $query->where('visa_process_id', $visa_process_id);
                    })->orderBy('id')->pluck('type', 'id')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_visa_interview_types");
            Log::error($e);
        }
    }
    /**
     * To list all the visa status
     * @author venkatesan.raj
     * 
     * @param string $visa_process_id
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_visa_status($visa_process_id = 1, $active_alone = true)
    {
        try
        {
            return DB::table('visa_status_master')
                    ->where(function ($query) use($visa_process_id, $active_alone) {
                        if($active_alone) $query->where('active', 1);
                        $query->where('visa_process_id', $visa_process_id);
                    })->orderBy('id')->pluck('status', 'id')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_visa_status");
            Log::error($e);
        }
    }
    /**
     * To list all the visa traveling types
     * @author venkatesan.raj
     * 
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function list_visa_travel_type($active_alone = true)
    {
        try
        {
            return DB::table('visa_travel_type_master')
                    ->where(function ($query) use($active_alone) {
                        if($active_alone) $query->where('active', 1);
                    })->orderBy('id')->pluck('name', 'id')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_visa_travel_type");
            Log::error($e);
        }
    }
    
    /**
     * To list the countries mapped against the reviewer
     * @author venkatesan.raj
     * 
     * @param string $aceid
     * @param string $role
     * 
     * @return array
     */
    public function get_countries_mapped_against_reviewer($aceid="ACE8235", $role="gm_reviewer")
    {
        try
        {
            $table_name = $role == "gm_reviewer" ? "vrf_visa_gm_reviewer_mapping" : "vrf_visa_hr_reviewer_mapping";
            $rule_code = DB::table($table_name)->where([['reviewer_aceid', $aceid],['active', 1]])->value('rule_code');
           $rule_conditions = DB::table('vrf_visa_rule_conditions')
                                ->where('rule_code', $rule_code)->whereIn('mapped_field', ['from_country', 'to_country'])->pluck('mapped_value', 'mapped_field')->toArray();
            return $rule_conditions;
        }
        catch (\Exception $e)
        {
            Log::error("Error in get_countries_mapped_against_reviewer");
            Log::error($e);
        }
    }
    /**
     * To list job titles
     * @author venkatesan.raj
     * 
     * @param bool $active_along optional
     * 
     * @return array
     */
    public function list_immigration_job_titles($active_alone = true)
    {
        try
        {
            return DB::table('visa_job_titile_master')
                    ->where(function ($query) use($active_alone) {
                        if($active_alone) $query->where('active', 1);
                    })->orderBy('name')->pluck('name', 'id')->toArray();
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_immigration_job_titles");
            Log::error($e);
        }
    }

    /**
     * To get the HR partner for the travaler
     * @author venkatesan.raj
     * 
     * @param string $travaler_id
     * 
     * @return string
     */
    public function get_hr_partner($aceid)
    {
        try
        {
            $service_config = $this->service_url_config;
            $service_name = "USERDETAILS";
            $url = $service_config[$service_name]["url"];
            $username = $service_config[$service_name]["username"];
            $password = $service_config[$service_name]["password"];
            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setBasicAuthentication($username, $password);
            $curl->setDefaultTimeout(120);
            $data = json_encode([
                'Date' => '',
                'ACENumber' => $aceid,
            ]);
            $curl->post($url,$data);
            if ($curl->error) {
                echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            } else {
                $relation_id = "RTR005";
                $response = $curl->response;
                $employees = isset($response) && property_exists($response, "employees") ? $response->employees : null;
                $employee = isset($employees) && array_key_exists(0, $employees) ? $employees[0] : null;
                $employee_relations = isset($employee) && property_exists($employee, "relations") ?  $employee->relations : null;
                $default_hr_partner = $this->visa_config['default_hr_partner'];
                if(isset($employee_relations)){
                    $hr_relations = current(array_filter($employee_relations, fn($e) => property_exists($e, "RelationIdentifier") && $e->RelationIdentifier == $relation_id ));
                    return $hr_relations && property_exists($hr_relations, "ReporteeACEID") ? $hr_relations->ReporteeACEID : $default_hr_partner;
                }
                else
                    return $default_hr_partner;
            }
        }
        catch (\Exception $e)
        {
            Log::error("Error in get_hr_partner");
            Log::error($e);
            return $e;
        }
    }

    public function get_travel_desk_user_details($travel_request_id=null,$related_roles,$user_id=null)
    {
        $related_user_conditions=DB::table('trf_travel_desk_group_user')->whereIn('related_role',$related_roles);
        if($user_id)
            $related_user_conditions=$related_user_conditions->where('aceid',$user_id);
        $related_user_conditions=$related_user_conditions->where('active',1)->pluck('grouping_code')->toArray();

        $related_user_conditions=array_unique($related_user_conditions);
        $matched_conditions=[];$matched_requests=[];
        foreach($related_user_conditions as $related_conditions){
            $related_condition_details=DB::table('trf_travel_desk_conditions')->where('grouping_code',$related_conditions)->where('active',1)->get();
            $related_ids=DB::table('trf_travel_request as req')
            ->leftJoin('trf_approval_matrix_tracker as tracker','req.id','tracker.request_id')
            ->leftJoin('trf_traveling_details as trv','req.id','trv.request_id')
            ->leftJoin('trf_user_role_mapping as urd','urd.role_code','tracker.respective_role_or_user');
            foreach($related_condition_details as $conditions){
                if($conditions->value){
                    if(in_array($conditions->condition,['whereIn','whereNotIn'])){
                        $value=explode(',',$conditions->value);
                    }
                    else
                        $value=$conditions->value;
                    $related_ids=$related_ids->{$conditions->condition}($conditions->field_name,$value);
                }
            }
            if($travel_request_id)
            $related_ids=$related_ids-> where('req.id',$travel_request_id);
            $related_ids=$related_ids->whereIn('tracker.flow_code',$related_roles)->pluck('req.id')->toArray();
            if(count($related_ids)){
                $matched_conditions[]=$related_conditions;
                $matched_requests=array_merge($matched_requests,$related_ids);
            }
        }
        if($travel_request_id){
            $related_users=DB::table('trf_travel_desk_group_user')
                ->whereIn('grouping_code',$matched_conditions)
                ->whereIn('related_role',$related_roles)
                ->where('active',1)->pluck('aceid')->toArray();
            return $related_users;
        }
        else if($user_id){
            return $matched_requests;
        }    
    }
    public function get_visa_report_details(Request $request){
        try{    
            if(isset($request['fin_year']))
            $fin_year=$request['fin_year'];
            else
            $fin_year=$this->get_current_financial_year('as_string');
            $status=$this->role_based_config["REP_ACC"];
            $condition['status']= $status;
            $condition['module']= 'MOD_03';
            $related_ids=$this->get_user_based_requests(["visa_reports"],$fin_year,$condition);
            $full_list=$this->visa_reports_full_details([
                ['method'=>'whereIn','column'=>'tr.id','condition'=>'','value'=>$related_ids]
        
            ]);
           
            $financial_years=$this->list_financial_years();
            $icons=$this->images_for_status;
        
            return View::make('layouts.visa_process.visa_reports',['full_list'=>$full_list,'selected_years'=>$fin_year,'financial_years'=>$financial_years,'icons'=>$icons]);
            }catch(\Exception $ex ){
                Log::info($ex);
            }
    }
    public function visa_reports_full_details($filter_creterias){
        $visa_details = DB::table('trf_travel_request as tr')
            ->leftJoin('trd_modules as tm','tm.unique_key','tr.module')
            ->leftJoin('trd_projects as project','project.project_code','tr.project_code')
            ->leftJoin('trd_departments as dept','dept.code','tr.department_code')
            ->leftJoin('trd_practice as practice','practice.code','tr.practice_unit_code')
            ->leftJoin('trd_status as status','status.unique_key','tr.status_id')
            ->leftJoin('users as traveler','traveler.aceid','tr.travaler_id')
            ->leftJoin('users as created','created.aceid','tr.created_by')
            ->leftJoin('trf_traveling_details as td','td.request_id','tr.id')
            ->leftJoin('trd_country_details as to_country','to_country.unique_key','td.to_country')
            ->leftJoin('trd_country_city as to_city','to_city.unique_key','td.to_city')
            ->leftJoin('trd_country_details as from_country','from_country.unique_key','td.from_country')
            ->leftJoin('trd_country_city as from_city','from_city.unique_key','td.from_city')
            ->leftJoin('trd_travel_types as travel_types','travel_types.unique_key','td.travel_type_id')
            ->leftJoin('vrf_visa_request_details as vrd','vrd.request_id','tr.id')
            ->leftJoin('trd_visa_type','trd_visa_type.unique_key','vrd.visa_type')
            ->leftJoin('vrd_visa_category_master as vcm','vcm.unique_key','vrd.visa_category')
            ->select('tr.id as travel_request_id','tr.request_id as request_id','tr.module','tm.module_name','tr.travaler_id','traveler.username as traveler_name','tr.created_by','created.username as created_by_name','tr.created_at',
                'tr.project_code','project.project_name','tr.department_code','dept.name as department_name','tr.practice_unit_code','practice.name as practice_unit_name',
                'tr.status_id','status.name as status_name','tr.requestor_entity',
                'project.customer_code','project.customer_name','from_country.name as from_country_name','to_country.name as to_country_name','from_city.name as from_city_name','to_city.name as to_city_name'
                ,'td.from_date','td.to_date','td.id as traveling_id','td.travel_type_id','travel_types.type as travel_type','trd_visa_type.name as visa_type_name','vcm.name as visa_category_name'
            );
            if(count($filter_creterias)){
                foreach($filter_creterias as $filters){
                    if($filters['condition'])
                        $full_details=$visa_details->{$filters['method']}($filters['column'],$filters['condition'],$filters['value']);
                    else
                        $full_details=$visa_details->{$filters['method']}($filters['column'],$filters['value']);
                }
            }
            $full_details=$visa_details->where('tr.active',1)->orderBy('tr.id','DESC')->groupBy('tr.id','td.id')->get();
            return $full_details;
    }
    
    /**
     * To get the linked request ids of travel / visa request based on module
     * @author venkatesan.raj
     * 
     * @param string $request_id
     * @param string $module
     * @param bool $id_alone false
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function get_linked_request_by_module($request_id, $module, $id_alone=false, $active_alone = true)
    {
        try
        {
            $modules = $this->CONFIG["MODULE"];
            $module = array_key_exists($module, $modules) ? $modules[$module] : null;
            if(is_null($request_id) || is_null($module)) return [];
            $travaler_id = DB::table('trf_travel_request as tr')->where('id', $request_id)->value('travaler_id');
            $travelling_details = DB::table('trf_traveling_details')->where([['request_id', $request_id],['active', 1]]);
            if( $module == "international" ) {
                $url_prefix = "/visa_request/";
                $countries = $travelling_details->distinct()->pluck('to_country', 'visa_number')->toArray();
                foreach($countries as $visa_number => $country) {
                    $related_countries = DB::table('trf_visa_country_mapping')->where([['source_country_code', $country],['active', 1]])->distinct()->pluck('mapping_country_code')->toArray();
                    $countries[$visa_number] = [$country, ...$related_countries];
                }
                $related_request_ids = DB::table('vrf_visa_request_details as vd')
                                        ->leftJoin('trf_traveling_details as td', function ($join) { $join->on('td.request_id', 'vd.request_id')->where('td.active', 1); })
                                        ->leftJoin('trf_travel_request as tr', 'tr.id', 'vd.request_id')
                                        ->where('tr.travaler_id', $travaler_id)->whereIn('td.to_country', array_merge(...array_values($countries)))->whereIn('vd.visa_number', array_keys($countries))
                                        ->orderBy("td.id")->distinct()->pluck('vd.request_id')->toArray();
            } else if ($module == "visa") {
                $url_prefix = "/request_full_details/";
                $country = $travelling_details->value('to_country');
                $related_countries = DB::table('trf_visa_country_mapping')->where([['source_country_code', $country],['active', 1]])->distinct()->pluck('mapping_country_code')->toArray();
                $country = [$country, ...$related_countries];
                $visa_number = DB::table('vrf_visa_request_details')->where([['request_id', $request_id],['active', 1]])->value('visa_number');
                if($visa_number){
                    $related_request_ids = DB::table('trf_travel_request as tr')
                                        ->leftJoin('trf_traveling_details as td', function ($join) { $join->on('td.request_id', 'tr.id')->where('td.active', 1); })
                                        ->where('tr.travaler_id', $travaler_id)->whereIn('td.to_country', $country)->where('td.visa_number', $visa_number)->whereNotIn('tr.status_id', ['STAT_01'])->distinct()->pluck('tr.id')->toArray();
                } else {
                    $related_request_ids = [];
                }
            }
            if($id_alone) {
                return $related_request_ids;
            }
            $related_request_details = DB::table('trf_travel_request')
                                            ->select('id', 'request_id')
                                            ->where(function ($query) use($active_alone, $related_request_ids) {
                                                $query->whereIn('id', $related_request_ids);
                                                if($active_alone) $query->where('active', 1);
                                            })->latest()->get()->toArray();

            // $related_request_details = array_map( fn($e) => $e && $e->url = property_exists($e, 'id') ? $url_prefix.Crypt::encrypt($e->id) : null, $related_request_details );
            $related_request_details = array_map( function($e) use($url_prefix) {
                if($e)
                    $e->url = property_exists($e, 'id') ? $url_prefix.Crypt::encrypt($e->id) : null;
                return $e;
            }, $related_request_details );
            return $related_request_details;
        }
        catch (\Exception $e)
        {
            Log::error("Error in ".__FUNCTION__);
            Log::error($e);
            throw $e;
        }
    }
    
    /**
     * Returns the HR admins details for generating the offer letter
     * @author venkatesan.raj
     * 
     * @param string $request_id
     * @param bool $active_alone optional
     * 
     * @return 
     */
    public function get_hr_admin_details($request_id, $active_alone = true)
    {
        try
        {
            // Get request details
            $request_details = DB::table('trf_travel_request as tr')
                                ->leftJoin('trf_traveling_details as td', function ($join) { $join->on('td.request_id', 'tr.id')->where('td.active', 1); })
                                ->leftJoin('visa_process_review_details as rd', 'rd.process_request_id', 'tr.id')
                                ->select('td.to_country as country_code', 'rd.entity_id as entity')
                                ->where([['tr.id', $request_id],['tr.active', 1]])->first();

            $country_code = $request_details->country_code ?? null;
            $entity = $request_details->entity ?? null;

            // Get offer letter configuration
            $offer_letter_config = DB::table('vrd_offer_letter_config as olc')
                                    ->leftJoin('users as u', function ($join) { $join->on('olc.hr_admin', 'u.aceid')->where('u.active', 1); })
                                    ->leftJoin('trd_departments as d', function ($join) { $join->on('u.DepartmentId', 'd.code')->where('d.active', 1); })
                                    ->select('olc.offer_letter_template', 'olc.hr_admin as hr_admin_aceid', 'u.username as hr_admin_name', 'u.DesignationName as hr_admin_designation', 'd.name as hr_admin_department', 'u.email as hr_admin_mail', 'olc.signature_location');

            
            $offer_letter_details = $offer_letter_config->where('olc.country_code', $country_code)->where(function ($query) use($entity) {
                                        $query->where('olc.entity', $entity)->orWhere('olc.entity', '');
                                    });
            // if the hr admin not mapped for given country
            if( !$offer_letter_details->exists() )
                $offer_letter_details = $offer_letter_config->orWhereNull('country_code');

            if($active_alone) $offer_letter_details->where('olc.active', 1);

            return (array) $offer_letter_details->orderBy('olc.entity', 'desc')->first();
        }
        catch (\Exception $e)
        {
            Log::error("Error in ".__FUNCTION__);
            Log::error($e);
            throw $e;
        }
    }

    /**
     * To load visa numbers
     * @author venkatesan.raj
     * 
     * @param Request $request << countries, travel_purpose, aceid >>
     * 
     * @return Response
     */
    public function load_visa_numbers(Request $request)
    {
        try
        {
            $countries = $request->input('countries');
            $travel_purpose = $request->input('travel_purpose');
            $request_for = $request->input('request_for');
            $travel_purpose_visa_type_mapping = ["PUR_02_02" => "VIS_001", "PUR_02_03" => "VIS_002", "default" => "VIS_001"];
            $request_for_mapping = ['RF_05' => 'RF_08', 'RF_06' => 'RF_14', 'default' => 'RF_08'];
            $visa_type = array_key_exists($travel_purpose, $travel_purpose_visa_type_mapping) ? $travel_purpose_visa_type_mapping[$travel_purpose] : $travel_purpose_visa_type_mapping["default"];
            $request_for = array_key_exists($request_for, $request_for_mapping) ? $request_for_mapping[$request_for] : $request_for_mapping["default"];
            $aceid = $request->input('aceid') ?? Auth::User()->aceid;
            $params = compact('countries', 'visa_type', 'aceid', 'request_for');
            if(is_array($countries))
                $result = $this->get_visa_number_against_countries( $params );
            else
                $result = [$countries => $this->get_visa_number( $params )];
            return [
                "visa_type" => $visa_type,
                "visa_number" => $result,
                "visa_not_required_countries"=>$this->visa_not_required_countries
            ];
        }
        catch (\Exception $e)
        {
            Log::error("Error in ".__FUNCTION__);
            Log::error($e);
            return json_encode(['error' => 'Error occured while fetching visa details']);
        }
    }

    /**
     * returns the visa numbers for the given countries
     * @author venkatesan.raj
     * 
     * @param arrray $params << array $countries string $visa_type string $aceid >>
     * 
     * @return array
     */
    public function get_visa_number_against_countries($params)
    {
        try
        {
            $aceid = array_key_exists('aceid', $params) ? $params['aceid'] : null;
            $countries = array_key_exists('countries', $params) ? $params['countries'] : [];
            $visa_type = array_key_exists('visa_type', $params) ? $params['visa_type'] : null;
            $request_for = array_key_exists('request_for', $params) ? $params['request_for'] : null;
            return array_combine(
                $countries,
                array_map(fn($e) => $this->get_visa_number(['countries' => $e, 'aceid' => $aceid, 'visa_type' => $visa_type, 'request_for' => $request_for]), $countries),
            );

        }
        catch (\Exception $e)
        {
            Log::error("Err in".__FUNCTION__);
            Log::error($e);
            throw $e;
        }
    }
    
    /**
     * returns the visa numbers for the given params.
     * @author venkatesan.raj
     * 
     * @param array $params << aceid, country, visa_type >>
     * @param bool $active_alone optional
     * 
     * @return array
     */
    public function get_visa_number($params, $active_alone = true)
    {
        try
        {
            $aceid = array_key_exists('aceid', $params) ? $params['aceid'] : null;
            $country = array_key_exists('countries', $params) ? $params['countries'] : null; // COU_003
            $visa_type = array_key_exists('visa_type', $params) ? $params['visa_type'] : null;
            $related_countries = DB::table('trf_visa_country_mapping')->where([['source_country_code', $country],['active', 1]])->distinct()->pluck('mapping_country_code')->toArray(); // [COU_030]
            $countries_order = "'".implode("', '", [$country, ...$related_countries])."'";
            $request_for = array_key_exists('request_for', $params) ? $params['request_for'] : null;

            // Get visa details from IDM
            $visa_details = []; $related_visa_details = [];
            if( $request_for != 'RF_14' ) {
                $visa_details = DB::table('trd_visa_details')
                                    ->select('visa_number')
                                    ->where([['aceid', $aceid],['visa_type', $visa_type],['visa_country_code', $country]])
                                    ->where(function ($query) use($active_alone) { if($active_alone) $query->where('active', 1); })
                                    ->pluck('visa_number')->toArray();
            
                $related_visa_details = DB::table('trd_visa_details')
                                        ->where([['aceid', $aceid],['visa_type', $visa_type]])->whereIn('visa_country_code', $related_countries)
                                        ->where(function ($query) use($active_alone) { if($active_alone) $query->where('active', 1); })
                                        ->orderByRaw("FIELD(visa_country_code, ".$countries_order.")")->latest()->groupBy('visa_country_code')
                                        ->pluck('visa_number')->toArray();
            }

            // If visa details not found in IDM
            if(  count($visa_details) == 0 && count($related_visa_details) == 0 ) {
                $visa_details = DB::table('trf_travel_request as tr')
                                    ->leftJoin('trf_traveling_details as td', function ($join) { $join->on('td.request_id', 'tr.id')->where('td.active', 1); })
                                    ->leftJoin('vrf_visa_request_details as vr', function ($join) { $join->on('vr.request_id', 'tr.id')->where('vr.active', 1); })
                                    ->select('vr.visa_number')
                                    ->where([['tr.travaler_id', $aceid],['vr.visa_type', $visa_type],['td.to_country',$country],['tr.request_for_code', $request_for]])->whereIn('tr.status_id', ['STAT_12', 'STAT_14'])
                                    ->where(function ($query) use($active_alone) { if($active_alone) $query->where('tr.active', 1); })
                                    ->pluck('visa_number')->toArray();

                $related_visa_details = DB::table('trf_travel_request as tr')
                                    ->leftJoin('trf_traveling_details as td', function ($join) { $join->on('td.request_id', 'tr.id')->where('td.active', 1); })
                                    ->leftJoin('vrf_visa_request_details as vr', function ($join) { $join->on('vr.request_id', 'tr.id')->where('vr.active', 1); })
                                    ->select('vr.visa_number')
                                    ->where([['tr.travaler_id', $aceid],['vr.visa_type', $visa_type], ['tr.request_for_code', $request_for]])->whereIn('td.to_country', $related_countries)->whereIn('tr.status_id', ['STAT_12', 'STAT_14'])
                                    ->where(function ($query) use($active_alone) { if($active_alone) $query->where('tr.active', 1); })
                                    ->orderByRaw("FIELD(td.to_country, ".$countries_order.")")->latest('vr.created_at')->groupBy('td.to_country')
                                    ->pluck('vr.visa_number')->toArray();
            }
            return count($visa_details) > 0 ? $visa_details : $related_visa_details;
        }
        catch (\Exception $e)
        {
            Log::error("Error in ".__FUNCTION__);
            Log::error($e);
            throw $e;
        }
    }

    public function visaNotRequiredCountries(Request $request){
        $status=0;
        if(isset($request->edit_id) && $request->edit_id){
            $status=DB::table('trf_travel_request')->where('id',$request->edit_id)->value('status_id');
        }

        Log::info($status);
        return ['visa_not_required_countries'=>$this->visa_not_required_countries,'status'=>($status=='STAT_01')?1:0];
    }

    /**
     * Returns matched rule for given params
     * @author venkatesan.raj
     * 
     * @param array $params
     * @param string $request_id 
     * 
     * @return string
     */
    public function get_matched_rule($params, $request_id = null, $active_alone=true)
    {
        try {
            $rules_details = DB::table('trf_overall_rule_conditions')
                                ->select(
                                    "rule_code",
                                    DB::raw("JSON_ARRAYAGG(JSON_OBJECT('field', mapped_field, 'condition', mapped_condition, 'values', mapped_value)) as rule")
                                )->groupBy('rule_code')->get()->keyBy('rule_code')->toArray();
            $rules_details = json_decode(json_encode($rules_details), true);
            $matched_rules = [];$max_passed_count=0;
            foreach($rules_details as  $rule_code => $rule) {
                $cond_array = json_decode($rule['rule'], true);
                $passed_count = 0;
                foreach($cond_array as $cond) {
                    if(array_key_exists($cond["field"], $params) && $this->match_params_against_condition($cond, $params[ $cond["field"] ], $request_id)) {
                        $passed_count++;
                    } else {
                        $passed_count = 0;
                        break;
                    }
                }
                if($passed_count > 0){
                    if($max_passed_count > $passed_count){
                        $matched_rules = [$rule_code];
                        $max_passed_count = $passed_count;
                    } else if($passed_count > $max_passed_count) {
                        $matched_rules[] = $rule_code;
                    }
                } 
            }
            $matched_rule_code = DB::table("trd_overall_rule_config")->whereIn("unique_key", $matched_rules);
            if($active_alone) $matched_rule_code = $matched_rule_code->where('active', 1);
            $matched_rule_code = $matched_rule_code->orderBy('precedence', 'desc')->value('unique_key');
            return $matched_rule_code;
        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__);
            Log::error($e);
            throw $e;
        }
    }
    /**
     * Returns true when params satifies the condition array
     * @author venkatesan.raj
     * 
     * @param array $condition_array
     * @param array $param
     * @param string $request_id
     * @param bool $check_role_alone optional
     */
    public function match_params_against_condition($condition_array, $param, $request_id, $check_role_alone = false)
    {
        try {
            extract($condition_array);
            $casting_needed_conditions = ['in', 'not_in', 'between'];
            if(in_array($condition, $casting_needed_conditions)) {
                $list = explode("|", $values);
                [$start, $end] = $list + [null, null];
            }
            // Helper function to check whether reviewer has access to request
            $check_accessible_users = function ($role) use($request_id) {
                $accessible_users = $this->provider->get_travel_desk_user_details($request_id, [$role]);
                return $accessible_users && in_array(Auth::User()->aceid, $accessible_users);
            };
            switch ($field)
            {
                case "role" :
                    $custom_roles = ["AN_COST_VISA", "GM_REV", "HR_REV", "TRV_PROC_VISA"];
                    if($condition == "equal_to" && $values == "VIS_USR" && !$check_role_alone ) {  
                        return DB::table('trf_travel_request')->where([['id', $request_id],['travaler_id', Auth::User()->aceid]])->exists();
                    }else if($condition == "equal_to" && $values == "HR_PRT" && !$check_role_alone ) {
                        return $this->check_hr_partner();
                    } else if($condition == "equal_to" && in_array($values, $custom_roles) && !$check_role_alone) {
                        $check_accessible_users($values);
                    } else if(Auth::User()->has_any_role_code(explode('|', $values))){
                        return true;
                    } else if( in_array('REQ', explode('|', $values)) ) {
                        return DB::table('trf_travel_request')->where([['id', $request_id],['created_by', Auth::User()->aceid]])->exists();
                    }
                    return false;
                
                default:
                    return match ($condition) {
                        "equal_to" => $values == $param,
                        "not_equal_to" => $values != $param,
                        "greater_than" => $value > $param,
                        "less_than" => $value < $param,
                        "greater_than_or_equal_to" => $value >= $param,
                        "less_than_or_equal_to" => $value <= $param,
                        "in" => in_array($param ,$list),
                        "between" => in_array($param, range($start, $end)),
                        default => false,
                    };
            }
        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__);
            Log::error($e);
            throw $e;
        }
    }
    /**
     * Returns true when given user is hr partner or requestor having hr partner role for the given request
     * @author venkatesan.raj
     * 
     * @param string $request_id
     * @param string $aceid optional
     * 
     * @return bool
     */
    public function check_hr_partner($request_id, $aceid = null)
    {
        try {
            $aceid = $aceid ?? Auth::User()->aceid;
            $role = "HR_PRT";
            $has_hr_role = DB::table('trf_user_role_mapping')->where([['aceid', $aceid],['role_code', $role]])->exists();
            if(!$has_hr_role) return false;
            return DB::table('trf_travel_request as tr')
                    ->leftJoin('vrf_visa_request_details as vr', function ($join) { $join->on('tr.id', 'vr.request_id')->where('vr.active', 1); })
                    ->where('tr.id', $request_id)->where(function ($query) use($aceid) {
                        $query->where('tr.created_by', $aceid)->orWhere('vr.hr_partner', $aceid);
                    })->exists();
        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__. " : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * Returns the value mapped against the params
     * @author venkatesan.raj
     * 
     * @param array $params
     * @param array $query_params
     * 
     * @return mixed
     */
    public function get_values_against_rule(array $params, array $query_params = [])
    {
        try {
            if(empty($params)) return null;
            // Get the rule matches the given params
            $rule_code = $this->get_matched_rule($params);
            $mapping_details = DB::table('trf_overall_rule_mapping')->where([['rule_code', $rule_code],['active', 1]])->first();
            if( empty($mapping_details) ) return null;
            $mapping_type = $mapping_details->mapping_type ?? null;
            $values = $mapping_details->mapping_value?? null;
            return match ($mapping_type) {
                "query" => DB::select($values, $query_params),
                "json" => json_decode($values, true),
                "array" => explode("|", $values),
                default => $values
            };
        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * Assign given to the given user
     * @author venkatesan.raj
     * 
     * @param string $role
     * @param string $aceid optional
     * 
     * @return bool
     */
    public function assign_role($role, $aceid = null)
    {
        try {
            $aceid = $aceid ?? Auth::User()->aceid;
            $data = [
                "active" => 1,
                "updated_at" => now()
            ];
            $condition = [
                "aceid" => $aceid,
                "role_code" => $role
            ];
            $role_mapping = DB::table('trf_user_role_mapping');
            if(!$role_mapping->where($condition)->exists()) {
                $data['created_at'] = now();
                $data['created_by'] = 'system';
            }
            $role_mapping->updateOrInsert($condition, $data);
        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * Returns true if the user has access to cancel the travel
     * @author venkatesan.raj
     * 
     * @param string $request_id
     * 
     * @return bool
     */
    public function can_cancel_travel($request_id)
    {
        try {
            if(empty($request_id)) {
                return false;
            }
            $roles = ['AN_COST_FAC', 'AN_COST_FIN'];
            $status = ['0', 'STAT_01', 'STAT_23', 'STAT_15', 'STAT_17', 'STAT_18', 'STAT_19', 'STAT_20', 'STAT_21', 'STAT_22', 'STAT_26', 'STAT_27'];
            $accessible_users = (array)$this->get_travel_desk_user_details($request_id, $roles);
            $travel_request = DB::table('trf_travel_request')->where('id', $request_id);
            return Auth::User()->has_any_role_code($roles) &&
                    $travel_request->whereNotIn('status_id', $status)->exists() &&
                    in_array(Auth::User()->aceid, $accessible_users);
        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * Return true if the travel is canceled
     * @author venkatesan.raj
     * 
     * @param string $request_id
     * 
     * @return bool
     */
    public function is_canceled_travel($request_id)
    {
       try {
            $status = "STAT_23";
            return DB::table('trf_travel_request')->where([['request_id', $request_id],['status', $status]])->exists();
        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
}
