<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class oldDataCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-import:cron {delete?}';

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
        try {
            $need_to_truncate = $this->argument('delete') == 1 ? true : false;
            if($need_to_truncate) $this->truncate_config_tables();
            $this->info("Start time : ".now());
            $old_travel_details = $this->get_travel_details();
            DB::beginTransaction();
            foreach($old_travel_details as $travel_details) {
                $data = array_filter($travel_details);
                $request_id = DB::table('trf_travel_request')->insertGetId(Arr::except($data, 'id'));
                $traveling_details = $this->get_traveling_details($data['id'], $request_id, $data['module'], $data['travaler_id']);
                $other_details = $this->get_other_travel_details($data['id'], $request_id, $data['module']);
                $proof_file_details = $this->get_proof_file_details($data['id'], $request_id, $data['module']);
                $approval_matrix_details = $this->get_approval_matrix_details($data['id'], $request_id);
                $status_details = $this->get_status_details($data['id'], $request_id, $data['status_id']);
                $forex_load_details = $this->get_forex_load_details($data['request_id'], $request_id);
                if(!empty($traveling_details))
                    DB::table('trf_traveling_details')->insert($traveling_details);
                if(!empty($other_details))
                    DB::table('trf_travel_other_details')->insert($other_details);
                if(!empty($proof_file_details)){
                    foreach($proof_file_details as $file_details) {
                        $file_reference_id = DB::table('trf_request_proof_file_details')->insertGetId(Arr::except($file_details, ['new_proof_details']));
                        $new_proof_details = isset($file_details['new_proof_details']) ? $file_details['new_proof_details'] : [];
                        if( count($new_proof_details) ) {
                            $new_proof_details = array_map( fn($e) => array_merge($e, ['request_proof_file_id' => $file_reference_id]), $new_proof_details );
                            DB::table('trf_travel_request_proof_details')->insert($new_proof_details);
                        }
                    }
                }
                if(!empty($approval_matrix_details))
                    DB::table('trf_approval_matrix_tracker')->insert($approval_matrix_details);
                if(!empty($status_details))
                    DB::table('trf_request_status_tracker')->insert($status_details);
                if(!empty($forex_load_details)) 
                    DB::table('trf_forex_load_details')->insert($forex_load_details);
            }
            DB::commit();
            $this->info("End time : ".now());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error occured while running the old data import");
            Log::error($e);
            $this->error("Error occured while running the old data import");
        }
        
    }

    /**
     * To get the old travel details
     * @author venkatesan.raj
     * 
     * @param void
     * 
     * @return array
     */
    public function get_travel_details()
    {
        try {
            $connection = DB::connection('old_db');
            $travel_details = $connection->table('travel_request')->orderBy('id')->get()->toArray();
            $new_travel_details = [];
            $request_for_list = $this->get_key_mapping('request_for_code');
            $travel_purpose_list = $this->get_key_mapping('travel_purpose_id');
            $missed_users = []; $missed_projects = []; $missed_departments = [];
            foreach($travel_details as $travel_detail)  {
                $id = $travel_detail->id;
                $is_domestic = $travel_detail->is_domestic;
                $is_visa = $travel_detail->request_type_id === 1;
                if($is_visa) continue;
                $request_id = $travel_detail->request_id;
                $travaler_id = $connection->table('users')->where('id', $travel_detail->requestor_id)->value('aceid');
                if( !DB::table('users')->where('aceid', $travaler_id)->exists() )
                    $missed_users[] = $travaler_id;
                $module = $is_domestic ? 'MOD_01' : 'MOD_02';
                $request_for_code = isset($request_for_list[$module]) ? $request_for_list[$module] : null;
                if($is_domestic) {
                    $request_for_id = $connection->table('behalf_of_details')->where('travel_request_id', $id)->value('list_id');
                    $request_for_code = isset($request_for_code[$request_for_id]) ? $request_for_code[$request_for_id] : null;
                }
                $purpose_id = $travel_detail->travel_purpose_id;
                $travel_purpose_id = isset($travel_purpose_list[$module]) ? $travel_purpose_list[$module] : null;
                if(is_array($travel_purpose_id)) 
                    $travel_purpose_id = isset($travel_purpose_id[$purpose_id]) ? $travel_purpose_id[$purpose_id] : null;
                $project_code = $travel_detail->project_code;
                if( !DB::table('trd_projects')->where('project_code', $project_code)->exists() ){
                    $missed_projects[] = $project_code;
                    $project_code = 'CUST_PROJ_007';
                }
                $department_id = $travel_detail->department_id;
                $department_code = $connection->table('department')->where('id', $department_id)->value('code');
                if( !$connection->table('department')->where([['id', $department_id],['type', 'department']])->exists() ) {
                    $department_code = $connection->table('project_customer_details')->where(function ($query) use($department_code) {
                        $query->where('project_practice_code', $department_code)
                            ->orWhere('project_unit_code', $department_code);
                    })->value('project_department_code') ?? '';
                }
                    
                if( !DB::table('trd_departments')->where('code', $department_code)->exists() ){
                    $missed_departments[] = $department_code;
                }
                $status_id = $this->get_new_status( $travel_detail->status_id, $id);
                $requestor_entity = $travel_detail->entity;
                $active = $travel_detail->active;
                $created_by = $travaler_id;
                $created_at = $travel_detail->created_at ? date('Y-m-d h:i:s', strtotime($travel_detail->created_at)) : ( $travel_detail->requested_on ? date('Y-m-d h:i:s', strtotime($travel_detail->requested_on)) : '' );
                $updated_at = $travel_detail->updated_at ?? '';
                $new_travel_details[] = compact('id','module', 'request_id', 'travaler_id', 'request_for_code', 'travel_purpose_id', 'travel_purpose_id', 'project_code', 'department_code', 'status_id', 'requestor_entity', 'active', 'created_by', 'created_at', 'updated_at');
            }
            $missed_projects = array_filter(array_unique($missed_projects));
            $this->add_missed_projects($missed_projects);
            $missed_departments = array_filter(array_unique($missed_departments));
            $this->add_missed_departments($missed_departments);
            $missed_users = array_filter(array_unique($missed_users));
            $this->add_missed_users($missed_users);
            return $new_travel_details;
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To get the old traveling details in new format
     * @author venkatesan.raj
     * 
     * @param string $old_request_id
     * @param string $new_request_id
     * @param string $module
     * 
     * @return array
     */
    public function get_traveling_details($old_request_id, $new_request_id, $module, $traveler)
    {
        try {
            $connection = DB::connection('old_db');
            $origin = $this->find_origin($traveler);
            if($module == "MOD_02") {
                $traveling_details = $connection->table('traveling_details')->where('travel_request_id', $old_request_id)->get()->toArray();
                
            } else if ($module == 'MOD_01') {
                $traveling_details = $connection->table('domestic_traveling_details')->where('travel_request_id', $old_request_id)->get()->toArray();
            }
            $travel_type_list = $this->get_key_mapping('travel_type_id');
            $travel_type_list = isset($travel_type_list[$module]) ? $travel_type_list[$module] : [];
            $request_id = $new_request_id;
            $new_traveling_details = [];
            $first_iteration = true;
            foreach($traveling_details as $traveling_detail) {
                $travel_type_id = $traveling_detail->travel_type_id;
                $travel_type_id = isset($travel_type_list[$travel_type_id]) ? $travel_type_list[$travel_type_id] : null;
                $country_id = $traveling_detail->country_id ?? null;
                $country_name = $connection->table('country')->where('id', $country_id)->value('name') ?? null;
                $to_country = DB::table('trd_country_details')->where('name', $country_name)->value('unique_key') ?? null;
                if($module == 'MOD_01') {
                    $from_country = $origin;
                    $from_city = $traveling_detail->departure_city;
                    $to_city = $traveling_detail->arrival_city;
                } else if($module == 'MOD_02') {
                    if( $first_iteration) {
                        $from_country = $origin;
                    } else {
                        $from_country = $to_country;
                    }
                    $from_city = null;
                    $to_city = $traveling_detail->destination;
                }
                $from_city = DB::table('trd_country_city')->where('name', $from_city)->value('unique_key') ?? (string)$from_city;
                $to_city = DB::table('trd_country_city')->where('name', $to_city)->value('unique_key') ?? (string)$to_city;
                $from_date = $traveling_detail->from_date ? date('Y-m-d', strtotime($traveling_detail->from_date)) : null;
                $to_date = $traveling_detail->to_date ? date('Y-m-d', strtotime($traveling_detail->to_date)) : null;
                $active = 1;
                $new_traveling_details[] = compact('request_id', 'travel_type_id', 'from_country', 'to_country', 'from_city', 'to_city', 'from_date', 'to_date', 'active');
                $first_iteration = false;
            }
            return $new_traveling_details;

        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To get the old other travel details in new format
     * @author venkatesan.raj
     * 
     * @param string $old_request_id
     * @param string $new_request_id
     * @param string $module
     * 
     * @return array
     */
    public function get_other_travel_details($old_request_id, $new_request_id, $module)
    {
        try {
            $connection = DB::connection('old_db');
            $request_id = $new_request_id;
            $worklocation_list = $this->get_key_mapping('worklocation');
            $worklocation_list = isset($worklocation_list[$module]) ? $worklocation_list[$module] : [];
            if($module == 'MOD_02') {
                $travel_other_details = $connection->table('other_traveler_details as otd')
                                                    ->leftJoin('traveling_details as td', 'td.travel_request_id', 'otd.travel_request_id')
                                                    ->leftJoin('currency as c', 'c.id', 'otd.currency_id')
                                                    ->leftJoin('forex_requirement_details as frd', 'frd.travel_request_id', 'otd.travel_request_id')
                                                    ->select('td.ticket_required', 'otd.forex_required', 'c.type as currency_code', 'otd.accomodation as accomodation_required', 'otd.work_location', 'otd.laptop', 'otd.insurance', 'otd.family_travel', 'otd.no_members as no_of_members', 'frd.email', 'frd.phone_no', 'frd.nationality', 'frd.address as traveller_address')
                                                    ->where('otd.travel_request_id', $old_request_id)->first();
            } else if($module == 'MOD_01') {
                $travel_other_details = $connection->table('domestic_other_travel_details as dotd')
                                                    ->leftJoin('aadhar_mapping as am', 'am.travel_request_id', 'dotd.travel_request_id')
                                                    ->leftJoin('aadhar_details as ad', 'ad.id', 'am.aadhar_detail_id')
                                                    ->select('dotd.ticket_required', 'dotd.accomodation_required', 'dotd.accomodation_details', 'dotd.family_travel', DB::raw("CONCAT_WS('&', dotd.family_no, dotd.child_no) as no_of_members"), 'dotd.aadhar_address as traveller_address', 'dotd.phone_no', 'dotd.work_location', 'dotd.insurance', 'ad.date_of_birth')
                                                    ->where('dotd.travel_request_id', $old_request_id)->first();
            }
            if(empty($travel_other_details)) return [];
            $ticket_required = $travel_other_details->ticket_required;
            $forex_required = $travel_other_details->forex_required ?? null;
            $currency_name = $travel_other_details->currency_code ?? null;
            $currency_code = DB::table('trd_currency')->where('currency', trim($currency_name))->value('currency_code') ??null;
            $accommodation_required = $travel_other_details->accomodation_required;
            $prefered_accommodation = $travel_other_details->accomodation_details ?? null;
            $work_location = $travel_other_details->work_location;
            $working_from = isset($worklocation_list[$work_location]) ? $worklocation_list[$work_location] : null;
            $laptop_required = $travel_other_details->laptop ?? null;
            $insurance_required = $travel_other_details->insurance;
            $family_traveling = $travel_other_details->family_travel;
            $no_of_members = $travel_other_details->no_of_members;
            $traveller_address = $travel_other_details->traveller_address ?? null;
            $phone_no = $travel_other_details->phone_no ?? null;
            $email = $travel_other_details->email ?? null;
            $nationality = $travel_other_details->nationality ?? null;
            $dob = isset($travel_other_details->date_of_birth) && $travel_other_details->date_of_birth ? date('Y-m-d', strtotime($travel_other_details->date_of_birth)) : null;
            $active = 1;
            return compact('request_id', 'ticket_required', 'forex_required', 'currency_code', 'accommodation_required', 'prefered_accommodation', 'working_from', 'laptop_required', 'insurance_required', 'family_traveling', 'no_of_members', 'traveller_address', 'email', 'dob', 'phone_no', 'nationality', 'active');
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To get the old proof details in new format
     * @author venkatesan.raj
     * 
     * @param string $old_request_id
     * @param string $new_request_id
     * @param string $module
     * 
     * @return array
     */
    public function get_proof_file_details($old_request_id, $new_request_id, $module)
    {
        try {
            $connection = DB::connection('old_db');
            $request_id = $new_request_id;
            $proof_type_list = $this->get_key_mapping('proof_type');
            $proof_type_list = isset( $proof_type_list[$module] ) ? $proof_type_list[$module] : [];
            $request_for_list = $this->get_key_mapping('proof_request_for');
            $request_for_list = isset($request_for_list[$module]) ? $request_for_list[$module] : [];
            $proof_attr_list = $this->get_key_mapping('proof_attr');
            $proof_attr_list = isset($proof_attr_list[$module]) ? $proof_attr_list[$module] : [];
            if($module == 'MOD_02') {
                $request_for_code = is_array($request_for_list) ? null : $request_for_list;
                $proof_file_details = $connection->table('traveler_file_details as tfd')
                                        ->leftJoin('forex_requirement_details as frd', 'frd.travel_request_id', 'tfd.travel_request_id')
                                        ->leftJoin('traveler_passport_details as tpd', 'tpd.travel_request_id', 'tfd.travel_request_id')
                                        ->select('tfd.*', 'frd.name_in_passport', 'frd.pan_no', 'tpd.passport_no', 'tpd.issue_date as passport_issue_date', 'tpd.expiry_date as passport_expiry_date', 'tpd.place_of_issue as passport_issued_place')
                                        ->where('tfd.travel_request_id', $old_request_id)->get()->toArray();
                $new_file_details = [];
                foreach($proof_file_details as $file_details) {
                    $file_type_id = isset( $proof_type_list[$file_details->file_type] ) ? $proof_type_list[$file_details->file_type] : null;
                    $original_name = $file_details->file_name;
                    $system_name = $file_details->file_name;
                    $active = 1;
                    $created_at = $file_details->created_at ? date('Y-m-d h:i:s', strtotime($file_details->created_at)) : null;
                    $updated_at = $file_details->updated_at ? date('Y-m-d h:i:s', strtotime($file_details->updated_at)) : null;
                    $proof_attr_list1 = isset($proof_attr_list[$file_type_id]) ? $proof_attr_list[$file_type_id] : [];
                    $proof_details = Arr::only((array)$file_details, array_keys($proof_attr_list1));
                    $new_proof_details = array_map(
                        fn($v, $k)  => array(
                            'request_id' => $request_id,
                            'proof_type_id' => $file_type_id,
                            'proof_attr_id' => $proof_attr_list1[$k],
                            'request_for_code' => $request_for_code,
                            'value' => (string)$v,
                            'active' => 1,
                            'created_at' => $created_at,
                            'updated_at' => $updated_at
                        ),
                        $proof_details,
                        array_keys($proof_details)
                    );
                    $new_file_details[] = compact('request_id', 'module', 'request_for_code', 'file_type_id', 'original_name', 'system_name', 'active', 'created_at', 'updated_at', 'new_proof_details');   
                }
                return $new_file_details;
            } else if ($module == 'MOD_01') {
                $proof_file_details = $connection->table('aadhar_mapping as am')
                                                    ->leftJoin('aadhar_details as ad', 'ad.id', 'am.aadhar_detail_id')
                                                    ->leftJoin('aadhar_file_details as afd', 'afd.id', 'ad.file_id')
                                                    ->select('am.proof_type', 'am.aadhar_type', 'afd.file_name', 'ad.name', 'ad.aadhar_no', 'ad.active', 'afd.created_at', 'afd.updated_at')
                                                    ->where('travel_request_id', $old_request_id)->get()->toArray();
                $new_file_details = []; $new_proof_details=[];
                foreach($proof_file_details as $file_details) {
                    $aadhar_type = $file_details->aadhar_type;
                    $request_for_code = isset($request_for_list[$aadhar_type]) ? $request_for_list[$aadhar_type] : null;
                    $file_type_id = isset($proof_type_list[$file_details->proof_type]) ? $proof_type_list[$file_details->proof_type] : null;
                    $original_name = $file_details->file_name;
                    $system_name = $file_details->file_name;
                    $active = 1;
                    $created_at = $file_details->created_at ? date('Y-m-d h:i:s', strtotime($file_details->created_at)) : null;
                    $updated_at = $file_details->updated_at ? date('Y-m-d h:i:s', strtotime($file_details->updated_at)) : null;
                    $proof_attr_list1 = isset($proof_attr_list[$file_type_id]) ? $proof_attr_list[$file_type_id] : [];
                    $proof_active = $file_details->active;
                    $proof_details = Arr::only((array)$file_details, array_keys($proof_attr_list1));
                    $new_proof_details = array_map(
                        fn($v, $k)  => array(
                            'request_id' => $request_id,
                            'proof_type_id' => $file_type_id,
                            'proof_attr_id' => $proof_attr_list1[$k],
                            'request_for_code' => $request_for_code,
                            'value' => (string)$v,
                            'active' => $proof_active,
                            'created_at' => $created_at,
                            'updated_at' => $updated_at
                        ),
                        $proof_details,
                        array_keys($proof_details)
                    );
                    $new_file_details[] = compact('request_id', 'module', 'request_for_code', 'file_type_id', 'original_name', 'system_name', 'active', 'created_at', 'updated_at', 'new_proof_details');
                }
                return $new_file_details;
            }
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To get the old approval matrix tracker details in new format
     * @author venkatesan.raj
     * 
     * @param string $old_request_id
     * @param string $new_request_id
     * 
     * @return array
     */
    public function get_approval_matrix_details($old_request_id, $new_request_id)
    {
        try {
            $connection = DB::connection('old_db');
            $request_id = $new_request_id;
            $level_order = '"'.implode('", "', $this->get_key_mapping('level_order') ).'"';
            $flow_codes = $this->get_key_mapping('flow_code');
            $approval_details = $connection->table('approval_configured_users')->where('travel_request_id', $old_request_id)->orderByRaw("FIELD(level_name, $level_order)")->get()->toArray();
            $approval_matrix = [];
            foreach($approval_details as $ad) {
                $approval_matrix[] =  [
                    'request_id' => $request_id,
                    'flow_code' => isset($flow_codes[$ad->level_name]) ? $flow_codes[$ad->level_name] : null,
                    'respective_role_or_user' => $ad->configured_approver,
                    'is_completed' => $ad->is_reviewed,
                    'comments' => $ad->comments,
                    'active' => 1,
                    'created_at' => isset($ad->created_at) ? date('Y-m-d h:i:s', strtotime($ad->created_at)) : null,
                    'updated_at' => isset($ad->updated_at) ? date('Y-m-d h:i:s', strtotime($ad->updated_at)) : null
                ];
            }
            $ticket_processed = $this->is_ticket_processed($old_request_id);
            $forex_processed = $this->is_forex_processed($old_request_id);
            if($this->is_ticket_required($old_request_id)){
                $process_details = $connection->table('request_status_details')->where([['travel_request_id', $request_id],['status_id', 9 ]])->first();
                $comments = $process_details ? $process_details->comments : null;
                $created_at = $updated_at = $process_details ? ( $process_details->updated_on ? date('Y-m-d h:i:s', strtotime($process_details->updated_on)) : null ) : null;
                $approval_matrix[] = [
                    'request_id' => $request_id,
                    'flow_code' => 'TRV_PROC_TICKET',
                    'respective_role_or_user' => 'TRV_PROC_TICKET',
                    'is_completed' => $ticket_processed ? 1 : 0,
                    'comments' => $comments,
                    'active' => 1,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                ];
            }
            if($this->is_forex_required($old_request_id)){
                $process_details = $connection->table('request_status_details')->where([['travel_request_id', $request_id],['status_id', 10 ]])->first();
                $comments = $process_details ? $process_details->comments : null;
                $created_at = $updated_at = $process_details ? ( $process_details->updated_on ? date('Y-m-d h:i:s', strtotime($process_details->updated_on)) : null ) : null;
                $approval_matrix[] = [
                    'request_id' => $request_id,
                    'flow_code' => 'TRV_PROC_FOREX',
                    'respective_role_or_user' => 'TRV_PROC_FOREX',
                    'is_completed' => $forex_processed ? 1 : 0,
                    'comments' => $comments,
                    'active' => 1,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                ];
            }
            return $approval_matrix;
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To get the old status tracker details in new format
     * @author venkatesan.raj
     * 
     * @param string $old_request_id
     * @param string $new_request_id
     * @param string $status_id
     * 
     * @return array
     */
    public function get_status_details($old_request_id, $new_request_id, $status_id)
    {
        try {
            $connection = DB::connection('old_db');
            $request_id = $new_request_id;
            $level_order = '"'.implode('", "', $this->get_key_mapping('level_order') ).'"';
            $status_mapping = $this->get_key_mapping('status_mapping');
            $approval_details = $connection->table('approval_configured_users')->where([['travel_request_id', $old_request_id],['is_reviewed', 1]])->orderByRaw("FIELD(level_name, $level_order)")->get()->toArray();
            $travel_details = $connection->table('travel_request as tr')
                                            ->leftJoin('users as u', 'u.id', 'tr.requestor_id')
                                            ->select('tr.*', 'u.aceid as created_by')
                                            ->where('tr.id', $old_request_id)->first();
            $created_at = $updated_at = $travel_details->requested_on ? date('Y-m-d h:i:s', strtotime($travel_details->requested_on)) : null;
            $old_status_code = '0'; $new_status_code = 'STAT_01'; $action = 'save';
            $new_status_details[] = [
                'request_id' => $request_id,
                'old_status_code' => $old_status_code,
                'new_status_code' => $new_status_code,
                'action' => $action,
                'billed_to_client' => null,
                'comments' => $travel_details->comment,
                'created_by' => $travel_details->created_by,
                'active' => 1,
                'created_at' => $created_at,
                'updated_at' => $updated_at
            ];
            $level_name = null; $last_key = null;
            foreach($approval_details as $ad) {
                $level_name = $ad->level_name;
                $new_status_code = isset($status_mapping[$level_name]) ? $status_mapping[$level_name] : $new_status_code;
                $last_key = array_key_last($new_status_details); $action = $this->get_action_value($old_status_code, $new_status_code);
                $new_status_details[$last_key]['new_status_code'] = $new_status_code;
                $new_status_details[$last_key]['action'] = $action;
                $old_status_code = $new_status_code;
                $comments = $ad->comments;
                $created_by = $ad->configured_approver;
                $created_at = $ad->created_at ? date('Y-m-d h:i:s', strtotime($ad->created_at)) : null;
                $updated_at = $ad->created_at ? date('Y-m-d h:i:s', strtotime($ad->created_at)) : null;
                $new_status_details[] = [
                    'request_id' => $request_id,
                    'old_status_code' => $old_status_code,
                    'new_status_code' => $new_status_code,
                    'action' => $action,
                    'billed_to_client' => $ad->billable_or_not,
                    'comments' => $comments,
                    'created_by' => $created_by,
                    'active' => 1,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                ];
                
            }
            if( isset( $reject_status_mapping[$level_name] ) && $reject_status_mapping[$level_name] == $status_id ) {
                $last_key = array_key_last($new_status_details);
                $action = $this->get_action_value($old_status_code, $status_id);
                $new_status_details[$last_key]['new_status_code'] = $status_id;
                $new_status_details[$last_key]['action'] = $action;
            } else {
                $last_key = array_key_last($new_status_details);
                $action = $this->get_action_value($old_status_code, $status_id);
                $new_status_code = $status_id == 'STAT_13' ? 'STAT_12' : $status_id;
                $new_status_details[$last_key]['new_status_code'] = $new_status_code;
                $new_status_details[$last_key]['action'] = $action;
                $old_status_code = $new_status_code;
            }
            $vtf_status_details = $connection->table('vtf_status_details as vsd')
                                                ->leftJoin('users as u', 'u.user_name', 'vsd.added_by')
                                                ->select('vsd.*', 'u.aceid as created_by')
                                                ->where('vsd.travel_request_id', $old_request_id)->whereIn('vsd.activity', ['ticket', 'forex'])
                                                ->orderBy('id')->get()->toArray();
            $last_process = collect($vtf_status_details)->pluck('id')->last();
            $last_ticket_process = collect($vtf_status_details)->filter(fn($e) => $e->activity == 'ticket')->pluck('id')->last();
            $last_forex_process = collect($vtf_status_details)->filter(fn($e) => $e->activity == 'forex')->pluck('id')->last();
            $ticket_processed = $this->is_ticket_processed($old_request_id);
            $forex_processed = $this->is_forex_processed($old_request_id);
            foreach($vtf_status_details as $vsd) {
                $new_status_code = 'STAT_12';
                if( $ticket_processed && $forex_processed && $vsd->id == $last_process )
                    $new_status_code = 'STAT_13';
                $action = $this->get_action_value($old_status_code, $new_status_code);
                if($action == 'process') {
                    if($vsd->id == $last_ticket_process)
                        $action = 'ticket_process';
                    else if($vsd->id == $last_forex_process)
                        $action = 'forex_process';
                    else
                        $action = $vsd->activity == 'ticket' ? 'save_ticket_process' : 'save_forex_process';
                }
                $last_key = array_key_last($new_status_details); $action = $this->get_action_value($old_status_code, $new_status_code);
                $new_status_details[$last_key]['new_status_code'] = $new_status_code;
                $new_status_details[$last_key]['action'] = $action;
                $created_at = $updated_at = $vsd->added_on ? date('Y-m-d h:i:s', strtotime($vsd->added_on)) : null;
                $new_status_details[] = [
                    'request_id' => $request_id,
                    'old_status_code' => $old_status_code,
                    'new_status_code' => $new_status_code,
                    'action' => $action,
                    'billed_to_client' => null,
                    'comments' => $vsd->comment,
                    'active' => 1,
                    'created_by' => $vsd->created_by,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                ];
                $old_status_code = $new_status_code;                    
            }
            return $new_status_details;
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To get the old status tracker details in new format
     * @author venkatesan.raj
     * 
     * @param string $old_request_id
     * @param string $new_request_id
     * @param string $module
     * 
     * @return array
     */
    public function get_forex_load_details($old_request_id, $new_request_id)
    {
        try {
            $connection = DB::connection('old_db');
            $request_id = $new_request_id;
            $forex_load_details = $connection->table('forex_load_details as fld')
                                    ->leftJoin('currency as c', 'c.id', 'fld.currency_id')
                                    ->select('fld.*', 'c.type as currency_code', DB::raw('"forex_load" as transaction_type'))
                                    ->where('request_id', $old_request_id)->get()->toArray();
            $forex_return_details = $connection->table('forex_return_details as frd')
                                        ->leftJoin('currency as c', 'c.id', 'frd.currency_id')
                                        ->select('frd.*', 'c.type as currency_code', DB::raw('"forex_return" as transaction_type'))
                                        ->where('request_id', $old_request_id)->get()->toArray();
            $forex_load_details = array_merge($forex_load_details, $forex_return_details);
            $new_forex_load_details = [];
            $payment_type_list = $this->get_key_mapping('payment_type') ?? [];
            foreach($forex_load_details as $load_details) {
                $currency_code = $load_details->currency_code;
                $currency_code = DB::table('trd_currency')->where('currency', trim($currency_code))->value('currency_code') ?? null;
                $new_forex_load_details[] = [
                    'request_id' => $request_id,
                    'transaction_type' => $load_details->transaction_type,
                    'transaction_date' => $load_details->created_on ? date('Y-m-d h:i:s', strtotime($load_details->created_on)) : null,
                    'mode_code' => isset($payment_type_list[$load_details->pay_type]) ? $payment_type_list[$load_details->pay_type] : null,
                    'currency_code' => $currency_code,
                    'amount' => $load_details->amount,
                    'comments' => $load_details->comment,
                    'active' => 1
                ];
            }
            return $new_forex_load_details;

        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To map the unique keys of old table and new table
     * @author venkatesan.raj
     * 
     * @param string $field_name
     * 
     * @return array 
     */
    public function get_key_mapping($field_name)
    {
        try {
            return match ($field_name) {
                'request_for_code' => [
                    'MOD_01' => [
                        1 => 'RF_01',
                        2 => 'RF_04',
                        3 => 'RF_03',
                        4 => 'RF_02'
                    ],
                    'MOD_02' => 'RF_05',
                    'MOD_03' => 'RF_08'
                ],
                'proof_request_for' => [
                    'MOD_01' => [
                        1 => 'RF_01',
                        2 => 'RF_03',
                        3 => 'RF_04',
                        4 => 'RF_02'
                    ],
                    'MOD_02' => 'RF_05',
                    'MOD_03' => 'RF_08'
                ],
                'travel_purpose_id' => [
                    'MOD_01' => [
                        1 => 'PUR_01_01',
                        4 => 'PUR_01_02'
                    ],
                    'MOD_02'  => [
                        1 => 'PUR_02_02',
                        2 => 'PUR_02_02',
                        3 => 'PUR_02_03',
                        4 => 'PUR_02_03'
                    ],
                    'MOD_03' => null
                ],
                'travel_type_id' => [
                    'MOD_01' => [
                        1 => 'TRV_01_01',
                        2 => 'TRV_01_02',
                        3 => 'TRV_01_03'
                    ],
                    'MOD_02' => [
                        1 => 'TRV_02_01',
                        2 => 'TRV_02_02',
                        3 => 'TRV_02_03'
                    ]
                ],
                'worklocation' => [
                    'MOD_01' => [
                        1 => 'WP_01',
                        2 => 'WP_04',
                        3 => 'WP_05'
                    ],
                    'MOD_02' => [
                        1 => 'WP_06',
                        2 => 'WP_02',
                        3 => 'WP_03'
                    ]
                ],
                'proof_type' => [
                    'MOD_01' => [
                        1 => 'PR_TY_01_01',
                        2 => 'PR_TY_01_04',
                        3 => 'PR_TY_01_02',
                        4 => 'PR_TY_01_03'
                    ],
                    'MOD_02' => [
                        'pancard_file' => 'PR_TY_02_02',
                        'passport_file' => 'PR_TY_02_01',
                    ],
                    'MOD_03' => [

                    ]
                ],
                'proof_attr' => [
                    'MOD_01' => [
                        'PR_TY_01_01' => [
                            'name' => 2,
                            'aadhar_no' => 1
                        ],
                        'PR_TY_01_02' => [
                            'name' => 4,
                            'aadhar_no' => 3
                        ],
                        'PR_TY_01_03' => [
                            'name' => 6,
                            'aadhar_no' => 5
                        ],
                        'PR_TY_01_04' => [//passport
                            'name' => 11,
                            'aadhar_no' => 10
                        ]
                    ],
                    'MOD_02' => [
                        'PR_TY_02_01' => [
                            'name_in_passport' => 16,
                            'passport_no' => 15,
                            'passport_issue_date' => 17,
                            'passport_expiry_date' => 18,
                            'passport_issued_place' => 19
                        ],
                        'PR_TY_02_02' => [
                            'pan_no' => 20,
                        ]
                    ],
                    'MOD_03' => [

                    ]
                ],
                'payment_type' => [
                    1 => 'PAY_001',
                    2 => 'PAY_002'
                ],
                'flow_code' => [  
                    'client_partner' => 'CLI_PTR',
                    'dept_head' => 'DEP_H',
                    'du_owner' => 'DU_H',
                    'geo_owner' => 'GEO_H',
                    'project_owner' => 'PRO_OW',
                    'super_head' => 'FIN_APP',
                ],
                'level_order' => ['project_owner', 'du_owner', 'dept_head', 'client_partner', 'geo_owner', 'super_head'],
                'status_id' => [
                    1 => 'STAT_10',
                    2 => 'STAT_12',
                    3 => 'STAT_22',
                    4 => 'STAT_01',
                    5 => 'STAT_02',
                    6 => 'STAT_02',
                    7 => 'STAT_02',
                    8 => 'STAT_14',
                    9 => 'STAT_12',
                    10 => 'STAT_12',
                    11 => 'STAT_13',
                    12 => 'STAT_08',
                    13 => 'STAT_17',
                    14 => 'STAT_11',
                    15 => 'STAT_19',
                    16 => 'STAT_21',
                ],
                'status_mapping' => [
                    'project_owner' => 'STAT_04',
                    'du_owner' => 'STAT_08',
                    'dept_head' => 'STAT_10',
                    'client_partner' => 'STAT_24',
                    'geo_owner' => 'STAT_25',
                    'super_head' => 'STAT_11'
                ],
                'reject_status_mapping' => [
                    'project_owner' => 'STAT_17',
                    'du_owner' => 'STAT_19',
                    'dept_head' => 'STAT_21',
                    'client_partner' => 'STAT_26',
                    'geo_owner' => 'STAT_27',
                    'super_head' => 'STAT_22'
                ],
                'status_action_mapping' => [
                    'submit' => ['old_status_code' => '0', 'new_status_code' => 'STAT_04|STAT_08|STAT_10|STAT_11|STAT_24|STAT_25'],
                    'project_owner_approve' => ['old_status_code' => 'STAT_04', 'new_status_code' => 'STAT_08|STAT_10|STAT_11|STAT_12'],
                    'project_owner_reject' => ['old_status_code' => 'STAT_04', 'new_status_code' => 'STAT_17'],
                    'du_head_approve' => ['old_status_code' => 'STAT_08', 'new_status_code' => 'STAT_10|STAT_11|STAT_12'],
                    'du_head_reject' => ['old_status_code' => 'STAT_08', 'new_status_code' => 'STAT_19'],
                    'bu_head_approve' => ['old_status_code' => 'STAT_10', 'new_status_code' => 'STAT_11|STAT_12' ],
                    'bu_head_reject' => ['old_status_code' => 'STAT_10', 'new_status_code' => 'STAT_21'],
                    'cp_approve' => ['old_status_code' => 'STAT_24', 'new_status_code' => 'STAT_25|STAT_11|STAT_12'],
                    'cp_reject' => ['old_status_code' => 'STAT_24', 'new_status_code' => 'STAT_26'] ,
                    'geo_head_approve' => ['old_status_code' => 'STAT_25', 'new_status_code' => 'STAT_11|STAT_12'],
                    'geo_head_reject' => ['old_status_code' => 'STAT_25', 'new_status_code' => 'STAT_27'],
                    'fin_approve'  => ['old_status_code' => 'STAT_11', 'new_status_code' => 'STAT_12'],
                    'fin_reject' => ['old_status_code' => 'STAT_11', 'new_status_code' => 'STAT_22'],
                    'process' => ['old_status_code' => 'STAT_12', 'new_status_code' => 'STAT_12|STAT_13'],
                ],
                default => []
            };
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To add the missed users which are not found in new DB
     * @author venkatesan.raj
     * 
     * @param array $user_list
     * 
     * @return void
     */
    public function add_missed_users($user_list)
    {
        try {
            $connection = DB::connection('old_db');
            $old_user_details  = $connection->table('users')->whereIn('aceid', $user_list)->get()->toArray();
            $data = [];
            if(count($old_user_details)) {
                foreach($old_user_details as $user_details) {
                    $department_id = $user_details->department_id;
                    $department_id = $connection->table('department')->where('id', $department_id)->value('code');
                    $practice_id = $user_details->practice_id;
                    $practice_id = $connection->table('practice')->where('id', $practice_id)->value('code');

                    $data[] = [
                        'aceid' => $user_details->aceid,
                        'username' => $user_details->user_name,
                        'email' => $user_details->mail,
                        'password' => $user_details->password,
                        'FirstName' => $user_details->firstname,
                        'OfficeLocation' => $user_details->location,
                        'DesignationName' => $user_details->designation,
                        'DepartmentId' => $department_id,
                        'PracticeId' => $practice_id,
                        'ReportingToACEID' => $user_details->manager_aceid,
                        'ReportingToFirstName' => $user_details->manager,
                        'LevelName' => $user_details->level,
                        'JoiningDate' => $user_details->date_of_joining ? date('Y-m-d h:i:s', strtotime($user_details->date_of_joining)) : null,
                        'active' => 0
                    ];
                }
            }
            User::insert($data);
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To add the inactive projects missed in new DB which are found in old DB
     * @author venkatesan.raj
     * 
     * @param array $project_codes
     * 
     * @return void
     */
    public function add_missed_projects($project_codes)
    {
        try {
            $connection = DB::connection('old_db');
            $old_project_details = $connection->table('project_customer_details')->whereIn('project_code', $project_codes)->get()->toArray();
            $data = [];
            if( count($old_project_details) ) {
                foreach($old_project_details as $project_details) {
                    $project_owner = $connection->table('users')->where('user_name', $project_details->project_owner)->value('aceid');
                    $data[] = [
                        'project_code' => $project_details->project_code,
                        'project_owner' => $project_owner,
                        'project_name' => $project_details->project_name,
                        'customer_code' => $project_details->customer_code,
                        'customer_name' => $project_details->customer_name,
                        'project_practice' => $project_details->project_practice,
                        'project_department' => $project_details->project_department_code,
                        'project_unit' => $project_details->project_unit_code,
                        'start_date' => null,
                        'end_date' => null,
                        'active' => $project_details->active,
                        'created_at' => date('Y-m-d h:i:s', strtotime( $project_details->created_at )),
                        'updated_at' => date('Y-m-d h:i:s', strtotime( $project_details->updated_at ))
                    ];
                }
                Project::insert($data);
            }
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To add the inactive departments missed in new DB which are found in old DB
     * @author venkatesan.raj
     * 
     * @param array $departments
     * 
     * @return void
     */
    public function add_missed_departments($department_codes)
    {
        try {
            $connection = DB::connection('old_db');
            $old_department_details = $connection->table('department')->where('type', 'department')->whereIn('code', $department_codes)->get()->toArray();
            $data = [];
            if( count($old_department_details) ) {
                foreach($old_department_details as $department_details) {
                    $head = $connection->table('users')->where('user_name', $department_details->head)->value('aceid');
                    $data[] = [
                        'code' => $department_details->code,
                        'name' => $department_details->name,
                        'head' => $head,
                        'sl_flag' => $department_details->sl_flag,
                        'short_notation' => $department_details->short_notation,
                        'visible' => $department_details->approval_visible,
                        'active' => $department_details->active,
                    ];
                }
            }
            Department::insert($data);
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To get the new status id
     * @author venkatesan.raj
     * 
     * @param string $status_id
     * @param string $request_id
     * 
     * @return string
     */
    public function get_new_status($status_id, $request_id)
    {
        try {   
            $connection = DB::connection('old_db');
            $status_list = $this->get_key_mapping('status_id');
            $level_order = "'".implode("', '", $this->get_key_mapping('level_order'))."'";
            $status_mapping = $this->get_key_mapping('status_mapping');
            if($status_id == 1) {
                $level_name = $connection->table('approval_configured_users')->where([['travel_request_id', $request_id], ['is_reviewed', 0]])->orderByRaw("FIELD('level_name', $level_order)")->value('level_name');
                $new_status_code = isset($status_mapping[$level_name]) ? $status_mapping[$level_name] : 'STAT_10';
            } else {
                $new_status_code = isset($status_list[$status_id]) ? $status_list[$status_id] : null;
            }
            $new_status_code == 'FAIL' ? 'STAT_10' : $new_status_code;
            return $new_status_code;
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To find the origin of the given user
     * @author venkatesan.raj
     * 
     * @param string $aceid
     * 
     * @return string
     */
    public function find_origin($aceid)
    {
        try {
            $connection = DB::connection('old_db');
            $origin_location_mapping = [
                'Chennai' => 'COU_014',
                'Ireland' => 'COU_029',
                'Hyderabad' => 'COU_014',
                'Middle East' => 'COU_003',
                'US' => 'COU_001',
                'Gurgaon' => 'COU_014',
                'Client Location' => 'COU_014',
                'Singapore' => 'COU_002',
                'Bangalore' => 'COU_014',
                'Netherlands' => 'COU_022',
                'UK' => 'COU_004',
                'Finland' => 'COU_009',
                'Sweden' => 'COU_012',
                'Mexico' => 'COU_025',
                'Belgium' => 'COU_018',
                'Canada' => 'COU_016',
                'Kochi' => 'COU_014',
                'Malaysia' => 'COU_027',
                'Poland' => 'COU_035',
                'Philippines' => 'COU_010',
                'Australia' => 'COU_011',
                'Sri Lanka' => 'COU_036',
                'Egypt' => 'COU_008',
                'Siruseri' => 'COU_014',
                'Cardiff' => 'COU_004'
            ];
            $location = $connection->table('users')->where('aceid', $aceid)->value('location');
            return isset($origin_location_mapping[$location]) ? $origin_location_mapping[$location] : null;
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To find the action name for status tracker
     * @author venkatesan.raj
     * 
     * @param string $old_status_code
     * @param string $new_status_code
     * 
     * @return string
     */
    public function get_action_value($old_status_code, $new_status_code)
    {
        try {
            $status_action_mapping = $this->get_key_mapping('status_action_mapping');
            $status_action_mapping = collect($status_action_mapping)->search( fn($e) => $e['old_status_code'] == $old_status_code && in_array($new_status_code,  explode('|', $e['new_status_code'])) );
            return $status_action_mapping;
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;   
        }
    }
    /**
     * To check whether the ticket is required or not
     * @author venkatesan.raj
     * 
     * @param $request_id
     * 
     * @return bool
     */
    public function is_ticket_required($request_id)
    {
        try {
            $connection = DB::connection('old_db');
            $is_domestic = $connection->table('travel_request')->where('id', $request_id)->value('is_domestic');
            if($is_domestic) {
                return $connection->table('domestic_other_travel_details')->where([['travel_request_id', $request_id],['ticket_required', '1']])->exists();
            } else {
                return $connection->table('traveling_details')->where([['travel_request_id', $request_id],['ticket_required', '1']])->exists();
            }
            return $connection->table('request_status_details')->where([['travel_request_id', $request_id],['status_id', 9]])->exists();
            
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To check whether the ticket is processed or not
     * @author venkatesan.raj
     * 
     * @param $request_id
     * 
     * @return bool
     */
    public function is_ticket_processed($request_id)
    {
        try {
            $connection = DB::connection('old_db');
            if(!$this->is_ticket_required($request_id))
                return true;
            return $connection->table('request_status_details')->where([['travel_request_id', $request_id],['status_id', 9]])->exists();
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To check whether the forex is required or not
     * @author venkatesan.raj
     * 
     * @param $request_id
     * 
     * @return bool
     */
    public function is_forex_required($request_id)
    {
        try {
            $connection = DB::connection('old_db');
            $is_domestic = $connection->table('travel_request')->where('id', $request_id)->value('is_domestic');
            if($is_domestic) {
                return false;
            } else {
                return $connection->table('other_traveler_details')->where([['travel_request_id', $request_id],['forex_required', '1']])->exists();
            }
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To check whether the forex is processed or not
     * @author venkatesan.raj
     * 
     * @param $request_id
     * 
     * @return bool
     */
    public function is_forex_processed($request_id)
    {
        try {
            $connection = DB::connection('old_db');
            if(!$this->is_forex_required($request_id))
                return true;
            return $connection->table('request_status_details')->where([['travel_request_id', $request_id],['status_id', 10]])->exists();
            
        } catch (\Exception $e) {
            Log::error("Error occured in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    // need to removed
    public function truncate_config_tables ()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('trf_travel_request')->truncate();
        DB::table('trf_traveling_details')->truncate();
        DB::table('trf_travel_other_details')->truncate();
        DB::table('trf_request_proof_file_details')->truncate();
        DB::table('trf_travel_request_proof_details')->truncate();
        DB::table('trf_request_status_tracker')->truncate();
        DB::table('trf_approval_matrix_tracker')->truncate();
        DB::table('trf_forex_load_details')->truncate();
    }
}
