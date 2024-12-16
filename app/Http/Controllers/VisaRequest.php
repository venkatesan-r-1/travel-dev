<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Schema;
use View;
use Auth;
use Log;
use Crypt;
use Arr;
use App\Traits\EncryptionTrait;

class VisaRequest extends Controller
{
    use EncryptionTrait;
    public function __construct()
    {
        parent::__construct();
        $this->module_id = "MOD_03";
        $this->provider = new DetailsProvider();
        $this->travelRequest = new TravelRequest();
    }
    
    /**
     * To redirect to request page
     * @author venkatesan.raj
     * 
     * @param string $id optional
     * 
     * @return View
     */
    public function redirect_to_request($id = null)
    {
        try
        {
            $edit_id = isset($id) ? Crypt::decrypt($id) : null;
            if(isset($edit_id)) {
                $request_details=DB::table('trf_travel_request')->where('id',$edit_id)->first();
                $has_view_access=$this->travelRequest->check_permissions_on_request($request_details);
                // Provide access to related request reviewers...
                if(!$has_view_access) {
                    $related_request_ids = $this->provider->get_linked_request_by_module($edit_id, $this->module_id, true);
                    foreach($related_request_ids as $rel_id) {
                        $related_request_details = DB::table('trf_travel_request')->where('id', $rel_id)->first();
                        $has_view_access = $this->travelRequest->check_permissions_on_request($related_request_details);
                        if($has_view_access) {
                            $has_view_access = true; break;
                        }
                    }
                }
                if(!$has_view_access)
                    return view('layouts.unauthorised');
                $request_full_details = $this->provider->request_full_details($edit_id);
                $status_id = $request_full_details['request_details']->status_id;
                $traveler_id = $request_full_details['request_details']->travaler_id;
                $params = [
                    'visa_type' => $request_full_details['visa_details']->visa_type_code,
                    'visa_category' => $request_full_details['visa_details']->visa_category_code,
                    "status" => $status_id,
                    "role" => null,
                ];
                $rule_code = $this->get_matched_rule(Arr::only($params+['visa_flow' => 'NA'], ["visa_type", "visa_category","visa_flow"]));
                $visa_flow = array_key_exists($rule_code, $this->visa_config["visa_flow"]) ? $this->visa_config["visa_flow"][$rule_code] : null;
                $status_id = $request_full_details['request_details']->status_id;
                $configured_tabs = (array)$this->get_configured_tabs($rule_code, $status_id);
                $visible_tabs = array_key_exists('visible_tabs', $configured_tabs) ? $configured_tabs['visible_tabs'] : [];
                $tab_classes = array_key_exists('tab_classes', $configured_tabs) ? $configured_tabs['tab_classes'] : [];
                $field_details = $this->get_field_details($params, $edit_id);
                $request_for = $request_full_details['request_details']->request_for_code;
                $origin = json_decode(json_encode($request_full_details['travelling_details']),true)[0]['from_country'];
                $user_details=null;
            } else {
                $request_full_details = [];
                $params = [
                    "default" => "NA",
                ];
                $traveler_id=Auth::User()->aceid;
                $rule_code = $this->get_matched_rule($params+['visa_flow'=>'NA']);
                $visa_flow = array_key_exists($rule_code, $this->visa_config["visa_flow"]) ? $this->visa_config["visa_flow"][$rule_code] : null;
                $configured_tabs = (array)$this->get_configured_tabs($rule_code);
                $visible_tabs = array_key_exists('visible_tabs', $configured_tabs) ? $configured_tabs['visible_tabs'] : [];
                $tab_classes = array_key_exists('tab_classes', $configured_tabs) ? $configured_tabs['tab_classes'] : [];
                $field_details = $this->get_field_details(['status' => 0]);
                $user_details = $this->provider->get_visa_user_details(Auth::User()->aceid);
                $visa_flow = "default";
                $status_id=0;
            }
            $section_details = (array)$this->get_section_details($status_id, $edit_id);
            $visible_sections = isset($section_details) && array_key_exists('visible_sections', $section_details) ? $section_details['visible_sections'] : [];
            $info_messages = isset($section_details) && array_key_exists('info_messages', $section_details) ? $section_details['info_messages'] : [];
            $band_detail=null;
            if($status_id == "STAT_29")
                $band_detail = DB::table('users')->where([ ['aceid', $traveler_id,],['active', 1] ])->value('LevelName'); 
            $related_travel_ids = null;
            if(in_array( $status_id, ["STAT_12", "STAT_14"])) {
                $related_travel_ids = $this->provider->get_linked_request_by_module($edit_id, $this->module_id);
            }
            // Showing hr partner in UI for testing purpose
            $hr_partner=null; $salary_range_edit_access=false;
            $converted_amount=0;
            if(isset($edit_id)) {
                $hr_partner = DB::table('vrf_visa_request_details')->where('request_id', $edit_id)->value('hr_partner');
                $hr_partner = DB::table('users')->where('aceid', $hr_partner)->value('username');
                // provide access to edit salary range
                if( $this->can_edit_salary_range($status_id) ){
                    $configured_visible_fields = $this->visa_config['salary_range_visible_fields'];
                    $configured_editable_fields = $this->visa_config['salary_range_editable_fields'];
                    if( isset($field_details) && is_array($field_details) && count($field_details) ) {
                        if(array_key_exists('visible_fields', $field_details))
                            $field_details['visible_fields'] = array_merge( (array)$field_details['visible_fields'], $configured_visible_fields );
                        if(array_key_exists('editable_fields', $field_details))
                            $field_details['editable_fields'] = array_merge( (array)$field_details['editable_fields'], $configured_editable_fields );
                        $salary_range_edit_access=true;
                    }
                }
                $curency_conversion = DB::table('trf_currency_conversion_rates')->where('active',1)->where('conversion_currency','CUR_12')->get();
                
                $inr_conversion=[];
                foreach($curency_conversion as $currency){
                    $inr_conversion[$currency->parent_currency]=$currency->conversion_rate;
                    
                    
                }
                $anticipated=$request_full_details["anticipated_details"];
                foreach($anticipated as $each_item){
                    if(isset($inr_conversion[$each_item->anticipated_currency])){
                        $converted_amount+=$inr_conversion[$each_item->anticipated_currency]*$each_item->amount;

                    }else{
                        $converted_amount+=$each_item->amount;

                    }
                   
                }
            }
            $origin_based_inputs=$this->country_based_input;
            $country_specific_label_name=$this->country_specific_label_name;
            $request_page_details = compact("converted_amount","edit_id", "visa_flow", "request_full_details", "visible_tabs", "tab_classes", "field_details", "user_details", "visible_sections", "info_messages", "band_detail", "visa_flow", "hr_partner", "salary_range_edit_access", "related_travel_ids","origin_based_inputs","country_specific_label_name");
            // Offer letter configuration
            $offer_letter_details = [];
            if($status_id == "STAT_37") {
                $offer_letter_details = $this->provider->get_hr_admin_details($edit_id);
            }
            $request_page_details = compact("converted_amount", "edit_id", "visa_flow", "request_full_details", "visible_tabs", "tab_classes", "field_details", "user_details", "visible_sections", "info_messages", "band_detail", "visa_flow", "hr_partner", "salary_range_edit_access", "related_travel_ids", "offer_letter_details", "country_specific_label_name");
            return View::make("layouts.visa_request.request", $request_page_details);
        }
        catch (\Exception $e)
        {
            Log::error("Error in redirect_to_request");
            Log::error($e);
        }
    }

    /**
     * To get the input field details based on current status and action role
     * @author venkatesan.raj
     * 
     * @param array $params
     * @param string $request_id optional
     * 
     * @return array
     */
    public function get_field_details($params, $request_id=null)
    {
        try
        {
            $rule_code = $this->get_matched_rule(Arr::except($params,['visa_category']), true, $request_id);
            $status = array_key_exists('status', $params) ? $params['status'] : 0;
            $field_details = (array)DB::table('vrf_visa_input_fields_mapping')
                                ->select("visible_fields", "editable_fields", "disabled_fields")
                                ->where([['rule_code', $rule_code],['active', 1]])->first();
            $visible_fields = array_key_exists('visible_fields', $field_details) ? explode(',', $field_details['visible_fields']) : [];
            $editable_fields = array_key_exists('editable_fields', $field_details) ? explode(',', $field_details['editable_fields']) : [];
            $disabled_fields = array_key_exists('visible_fields', $field_details) ? explode(',', $field_details['visible_fields']) : [];

            // Adding anticipated cost related fields for Approvers
            if(count($editable_fields))
                $editable_fields = $this->provider->add_anticipated_cost($editable_fields, $request_id, $status);

            if($visible_fields && count($visible_fields)) {
                $field_attr = DB::table("trd_input_fields as tif")
                                ->leftJoin("trf_input_fields_attr as tifa", "tifa.input_code", "tif.unique_key")
                                ->select('unique_key', 'lable_name', 'input_name', DB::raw("JSON_OBJECTAGG(attr_name, attr_value) as attributes"))
                                ->whereIn('unique_key', $visible_fields)->where('tif.active', 1)
                                ->groupBy('unique_key', 'lable_name', 'input_name')->get()->toArray();
                $field_attr = json_decode(json_encode($field_attr),true);
                $field_attr = array_combine(
                    array_column($field_attr, 'unique_key'),
                    array_map( function ($e) {
                        if(array_key_exists('attributes', $e))
                            $e['attributes'] = json_decode($e['attributes'], true);
                        return $e;
                    }, $field_attr ),
                );
                $select_options = array_map( fn($e) => array_key_exists('input_name', $e) ? $this->get_select_options($e["input_name"], $request_id) : null,  Arr::only($field_attr, $editable_fields) );
                $dept_id = isset($request_id) ? DB::table('trf_travel_request')->where('id', $request_id)->value('department_code') : null;
                $select_options['anticipated_currency'] = $this->provider->list_currency();
                $select_options['approver_currency_code'] = $this->provider->list_currency();
                $select_options['master_category'] = $this->provider->list_master_category($dept_id,$this->module_id);
                $select_options['category'] = count($select_options['master_category']) && count($select_options['master_category'])
                ? $this->provider->list_category(array_keys($select_options['master_category']),$request_id) 
                : $this->provider->list_category();
            } else {
                $field_attr = [];
                $select_options = [];
            }
            return compact("visible_fields", "editable_fields", "disabled_fields", "field_attr", "select_options");
        }
        catch (\Exception $e)
        {
            Log::error("Error in get_field_details");
            Log::error($e);
        }
    }

    /**
     * To get the select options for the given input name
     * @author venkatesan.raj
     * 
     * @param string $input_name
     * @param string $request_id optional
     * 
     * @return array
     */
    public function get_select_options($input_name, $request_id=null)
    {
        try
        {
            if(isset($request_id)) {
                $travel_details = (array)DB::table('trf_travel_request')->where([ ['id', $request_id],['active',1] ])->first();
                $visa_details = (array)DB::table('vrf_visa_request_details')->where([['request_id', $request_id],['active', 1]])->first();
                $education_category = DB::table('visa_process_employee_details')->where([ ['process_request_id', $request_id] ])->value('education_category_id');
                $traveling_details = (array)DB::table('trf_traveling_details')->where([['request_id', $request_id],['active', 1]])->first();
                $visa_type = $visa_details['visa_type']; $from_country = $traveling_details['from_country']; $to_country = $traveling_details['to_country'];       
                $travaler_id = $travel_details['travaler_id'];
                $travaler_dept_id = User::where([['active', 1],['aceid', $travaler_id]])->value('DepartmentId');
                $dept_id= in_array($travaler_dept_id, $this->FUNC_SALES_DEPT_CODES) ? $travaler_dept_id : $travel_details['department_code'];
                $band_level = DB::table('users')->where('aceid', $travaler_id)->value('LevelName');
                $created_by = $travel_details['created_by'];
                $project_request = new \Illuminate\Http\Request();
                $project_request->replace(['project'=>$travel_details['project_code'],'department_id'=>$dept_id,'edit_id'=>$request_id]);
            }
            return match ($input_name) {
                "visa_type" => $this->provider->list_visa_type(1, $this->module_id),
                "request_for_code" => isset($request_id) && $created_by != $travaler_id ? $this->provider->list_request_for($this->module_id, 1, true) :$this->provider->list_request_for($this->module_id),
                "origin" => $this->provider->list_country(),
                "to_country" => $this->provider->list_country(),
                "to_city" => isset($request_id) ? $this->provider->list_city($to_country) : [],
                "project_code" => isset($dept_id) ? $this->provider->list_projects('request', $dept_id) : $this->provider->list_projects(),
                "customer_name" => [],
                "department_code" => $this->provider->list_departments(),
                "practice_unit_code" => isset($project_request) ? (array)json_decode($this->provider->load_project_details($project_request))->delivery_unit : [],
                "visa_category" => isset($request_id) ? $this->provider->list_visa_category($visa_type, $from_country, $to_country) : [],
                "filing_type_id" => $this->provider->list_visa_filing_type(),
                "education_category_id" => $this->provider->list_education_category(),
                'education_details_id' => isset($education_category) ? $this->provider->list_education_details($education_category) : [],
                "entry_type_id" => $this->provider->list_visa_entry_type(),
                "employee" => isset($request_id) && $created_by != $travaler_id ? $this->provider->list_reporting_users($created_by) : [],
                "proof_request_for" => $this->provider->list_request_for($this->module_id), // isset($request_id) && $created_by != $travaler_id ? $this->provider->list_request_for($this->module_id, 1, true) : $this->provider->list_request_for($this->module_id),
                "proof_type" => isset($request_id) ? $this->provider->list_proof_type($this->module_id, $from_country) : $this->provider->list_proof_type($this->module_id, null),
                "us_job_title_id" => isset($band_level) ? $this->provider->list_job_titles($band_level) : [],
                "us_manager_id" => isset($to_country) ? $this->provider->list_reporting_managers($to_country) : [],
                "entity_id" => $this->provider->list_petitioner_entity($to_country),
                "attorneys_id" => $this->provider->list_attorneys($to_country),
                "entry_type_id" => $this->provider->list_visa_entry_types(),
                "visa_interview_type_id" => $this->provider->list_visa_interview_types(),
                "visa_status_id" => $this->provider->list_visa_status(),
                "traveling_type_id" => $this->provider->list_visa_travel_type(),
                "visa_currency" => $this->provider->list_currency(),
                "job_titile_id" => $this->provider->list_immigration_job_titles(),
                default => [],
            };
        }
        catch (\Exception $e)
        {
            Log::error("Error in get_select_options");
            Log::error($e);
        }
    }

    /**
     * To save the request details
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function save_request_details(Request $request)
    {
        try
        {
            //Configurations
            $visa_request_related_tables = [
                'visa_process_request_details',
                'visa_process_review_details',
                'visa_process_employee_details',
                'visa_process_job_details',
                'visa_process_review_details',
                'visa_process_travel_details',
                'visa_process_dependency_details',
                'visa_process_tracking_details',
            ];
            $tables_have_mutliple_entries = [
                'trf_travel_anticipated_details',
                // 'visa_process_dependency_details',
            ];
            $table_reference_keys = [
                'trf_travel_anticipated_details' => 'anticipated_row_id',
                // 'visa_process_dependency_details' => null,
            ];
            $file_uploads = [
                'cv_file_path',
                'degree_file_path',
                'petition_file_path',
                'visa_file_path',
            ];
            
            $edit_id = $request->input("edit_id");
            $request_id = isset($edit_id) ? Crypt::decrypt($edit_id) : null;
            $action = $request->input('action');
            $action_user=null; 
            $from_mail_approval = $request->input('mail_approval');
            if($from_mail_approval){
                $action_user = $request->input('action_user');
            }
            $module = $this->module_id;
            if (isset($request_id)) {
                $request_related_details = DB::table('trf_travel_request')->where('id', $request_id)->first();
                $saved_request_details = (array)DB::table('trf_travel_request')->where('id', $request_id)->first();
                $request_code = array_key_exists('request_id', $saved_request_details) ? $saved_request_details['request_id'] : null;
                $module = array_key_exists('module', $saved_request_details) ? $saved_request_details['module'] : null;
                $current_status = array_key_exists('status_id', $saved_request_details) ? $saved_request_details['status_id'] : null;
                $department = $request->input('department_code') ?? array_key_exists('department_code', $saved_request_details) ? $saved_request_details['department_code'] : null;
                $visa_request_details = (array)DB::table('vrf_visa_request_details')->where([['request_id', $request_id],['active', 1]])->first();
                $visa_type = $request->input('visa_type') ? $request->input('visa_type') : ( array_key_exists('visa_type',$visa_request_details) ? $visa_request_details['visa_type'] : null );
                $visa_category = $request->input('visa_category') ? $request->input('visa_category') : ( array_key_exists('visa_category', $visa_request_details) ? $visa_request_details['visa_category'] : null );
            } else {
                $request_related_details=null;
                $request_code = $this->generate_request_code();
                $request_id = null;
                $current_status = 0;
                $department = $request->input('department_code');
                $visa_type = $request->input('visa_type');
                $visa_category = $request->input('visa_category');
            }

            if($request->input('behalf_of')) {
                $traveler_id = $request->input('behalf_of');
            } else {
                if(isset($from_mail_approval)) {
                    $traveler_id = '-';
                } else {
                    $traveler_id = in_array($current_status,[0,'STAT_01']) ? Auth::User()->aceid : $request_related_details->travaler_id;
                }
            }

            $comments=null;
            if( $request->input('common_action_comments') )
                $comments = $request->input('common_action_comments');
            if( $request->input('visa_process_comment') )
                $comments = $request->input('visa_process_comment');
            
            $department_mapping_code = $this->travelRequest->get_respective_dept_group($module, $department);
            $rule_code_to_fetch_next_action = $this->get_matched_rule(compact("visa_type", "visa_category")+['visa_flow'=>'NA']);
            $next_action_details = $this->travelRequest->fetch_next_process_flow($request_id, $module, $action, $department_mapping_code, $current_status, $comments, $from_mail_approval, $action_user, rule_code: $rule_code_to_fetch_next_action);
            if(!array_key_exists('status',$next_action_details)){
                $message_text=$this->messages['en']['INVALID_ACTION'];
                return json_encode(['error'=>'INV_ACTION','message_text'=>$message_text]);
            }

            // move the uploaded files from temp folder to actual folder
            $uploaded_file_paths = array_filter($request->only($file_uploads));
            $upload_file_details = count($uploaded_file_paths) ? $this->move_temp_files($uploaded_file_paths, $request_code, $traveler_id) : [];

            $key = $this->get_new_key();

            $request_details = [
                'status_id' => $next_action_details['status'],
                'request_id' => $request_id,
                'travaler_id' => $traveler_id,
                'module' => $module,
                'visa_type' => $request->input('visa_type'),
                'from_country' => $request->input('origin'),
                'to_country' => $request->input('to_country'),
                'requestor_entity' => $request->input('requestor_entity'),
                'request_for_code' => $request->input('request_for_code'),
                'visa_category' => $request->input('visa_category'),
                'to_city' => $request->input('to_city'),
                'from_date' => $request->input('from_date') ? date('Y-m-d', strtotime($request->input('from_date'))) : null,
                'to_date' => $request->input('to_date') ? date('Y-m-d', strtotime($request->input('to_date'))) : null,
                'project_code' => $request->input('project_code'),
                'customer_name' => $request->input('customer_name'),
                'department_code' => $request->input('department_code'),
                'practice_unit_code' => $request->input('practice_unit_code'),
                'filing_type_id' => $request->input('filing_type_id'),
                'date_of_birth' => $request->input('date_of_birth') ? date('Y-m-d h:i:s', strtotime($request->input('date_of_birth'))) : null,
                'address' => $request->input('address'),
                'education_category_id' => $request->input('education_category_id'),
                'education_details_id' => $request->input('education_details_id'),
                'date_of_joining' => $request->input('date_of_joining') ? date('Y-m-d h:i:s', strtotime($request->input('date_of_joining'))) : null,
                'india_experience' => implode('.',[ $request->input('india_experience_years'), $request->input('india_experience_months') ]),
                'overall_experience' => implode('.',[ $request->input('overall_experience_years'), $request->input('overall_experience_months') ]),
                'cv_file_path' => array_key_exists('cv_file_path', $upload_file_details) ? $upload_file_details['cv_file_path'] : null,
                'degree_file_path' => array_key_exists('degree_file_path', $upload_file_details) ? $upload_file_details['degree_file_path'] : null,
                'master_category' => $request->input('master_category'),
                'category' => $request->input('category'),
                'sub_category' => $request->input('sub_category'),
                'anticipated_row_id' => $request->input('anticipated_row_id'),
                'anticipated_currency' => $request->input('anticipated_currency'),
                'amount' => $request->input('amount'),
                'excluded_row' => $request->input('excluded_row'),
                'budget_success' => $request->input('budget_success'),
                'message' => $request->input('message'),
                'anticipated_comments' => $request->input('anticipated_comments'),
                'minimum_wage' => $request->input('minimum_wage'),
                'work_location' => $request->input('work_location'),
                'band_detail' => $request->input('band_detail'),
                'salary_range_from' => $request->input('salary_range_from') ? DB::raw("AES_ENCRYPT('".$request->input('salary_range_from')."', UNHEX(SHA2('".$key."', 512)))") : null,
                'salary_range_to' => $request->input('salary_range_to') ? DB::raw("AES_ENCRYPT('".$request->input('salary_range_to')."', UNHEX(SHA2('".$key."', 512)))") : null,
                'us_job_title_id' => $request->input('us_job_title_id'),
                'acceptance_by_user' => $request->input('acceptance_by_user'),
                'us_salary' => $request->input('us_salary') ? DB::raw("AES_ENCRYPT('".$request->input('us_salary')."', UNHEX(SHA2('".$key."', 512)))") : null,
                'one_time_bonus' => $request->input('one_time_bonus') ? DB::raw("AES_ENCRYPT('".$request->input('one_time_bonus')."', UNHEX(SHA2('".$key."', 512)))") : null,
                'one_time_bonus_payout_date' => $request->input('one_time_bonus_payout_date') ? date('Y-m-d h:i:s', strtotime($request->input('one_time_bonus_payout_date'))) : null,
                'next_salary_revision_on' => $request->input('next_salary_revision_on') ? date('Y-m-d h:i:s', strtotime($request->input('next_salary_revision_on')) ) : null,
                'us_manager_id' => $request->input('us_manager_id'),
                'inszoom_id' => $request->input('inszoom_id'),
                'entity_id' => $request->input('entity_id'),
                'attorneys_id' => $request->input('attorneys_id'),
                'petition_file_date' => $request->input('petition_file_date') ? date('Y-m-d h:i:s', strtotime($request->input('petition_file_date'))) : null,
                'receipt_no' => $request->input('receipt_no'),
                'petition_start_date' => $request->input('petition_start_date') ? date('Y-m-d h:i:s', strtotime($request->input('petition_start_date'))) : null,
                'petition_end_date' => $request->input('petition_end_date') ? date('Y-m-d h:i:s', strtotime($request->input('petition_end_date'))) : null,
                'petition_file_path' => array_key_exists('petition_file_path', $upload_file_details) ? $upload_file_details['petition_file_path'] : null,
                'entry_type_id' => $request->input('entry_type_id'),
                'visa_interview_type_id' => $request->input('visa_interview_type_id'),
                'visa_ofc_date' => $request->input('visa_ofc_date') ? date('Y-m-d h:i:s', strtotime($request->input('visa_ofc_date'))) : null,
                'visa_interview_date' => $request->input('visa_interview_date') ? date('Y-m-d h:i:s', strtotime($request->input('visa_interview_date'))) : null,
                'visa_status_id' => $request->input('visa_status_id'),
                'travel_date' => $request->input('travel_date') ? date('Y-m-d h:i:s', strtotime($request->input('travel_date'))) : null,
                'travel_location' => $request->input('travel_location'),
                'visa_number' => $request->input('visa_number'),
                'visa_file_path' => array_key_exists('visa_file_path', $upload_file_details) ? $upload_file_details['visa_file_path'] : null,
                'traveling_type_id' => $request->input('traveling_type_id'),
                'dependency_details' => $request->input('dependency_details'),
                'record_number' => $request->input('record_number'),
                'most_recent_doe' => $request->input('most_recent_doe') ? date('Y-m-d h:i:s', strtotime($request->input('most_recent_doe'))) : null,
                'admit_until' => $request->input('admit_until') ? date('Y-m-d h:i:s', strtotime($request->input('admit_until'))) : null,
                'gc_initiated_on' => $request->input('gc_initiated_on') ? date('Y-m-d h:i:s', strtotime($request->input('gc_initiated_on'))) : null,
                'approver_anticipated_amount' => $request->input('approver_anticipated_amount'),
                'approver_currency_code' => $request->input('approver_currency_code'),
                'visa_currency' => $request->input('visa_currency'),
                'job_titile_id' => $request->input('job_titile_id'),
            ];
            $common_details = [
                'action' => $action,
                'created_by' => $from_mail_approval ? "-" : Auth::User()->aceid,
                'active' => 1,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ];

            $params = [
                'visa_type' => isset($request_details['visa_type']) ? $request_details['visa_type'] : $visa_type,
                'visa_category' => isset($request_details['visa_category']) ? $request_details['visa_category'] : $visa_category,
                'status' => $current_status,
                'action' => $common_details['action'],
                'role' => null,
            ];

            if(isset($from_mail_approval)) {
                $get_editable_fields=['INP_064','INP_068','INP_077'];
            } else {
                $field_details = (array)$this->get_field_details($params, $request_id);
                $editable_fields = array_key_exists('editable_fields',$field_details) ? $field_details['editable_fields'] : [];
                $get_editable_fields=DB::table('trd_input_fields')->whereIn('unique_key',$editable_fields)->pluck('input_name')->toArray();
            }

            $is_origin_found = array_search('origin', $get_editable_fields);
            if($is_origin_found !== false){
                $get_editable_fields[$is_origin_found] = "from_country";
            }

            $hr_parter_required_status = ['STAT_28'];
            if( in_array($next_action_details["status"], $hr_parter_required_status) ) {
                $request_details["hr_partner"] = $this->provider->get_hr_partner($traveler_id);
                $get_editable_fields[]="hr_partner";
                $this->provider->assign_role('HR_PRT', $request_details["hr_partner"]);
            }

            if(isset($from_mail_approval)){
                if(!is_null($request->input('billed_to_client')))
                    $get_editable_fields[]='billed_to_client';
            }
            else{
                $billable_key=array_search('billed_to_client',$get_editable_fields); 
                $billed_to_client = $request->input('billed_to_client');
                if(isset($edit_id)&&Auth()->User()->has_any_role_code($this->CONFIG['BILLABLE_CHOOSE_ACCESS'])&&in_array($request_related_details->status_id,$this->CONFIG['BILLABLE_ENABLED_STATUS'])){
                    if(property_exists($request_related_details,'billed_to_client')&&is_null($request_related_details->billed_to_client)){
                        $request_details['billed_to_client']=isset($billed_to_client)?$billed_to_client:null;
                        $get_editable_fields[]='billed_to_client';
                    }
                    else if(Auth::User()->has_any_role_code(['BF_REV'])&&$request_related_details->status_id=="STAT_05"){
                        $request_details['billed_to_client']=isset($billed_to_client)?$billed_to_client:null;
                        $get_editable_fields[]='billed_to_client';
                    }
                    else if($request_related_details->created_by==Auth::User()->aceid&&$request_related_details->status_id=='STAT_01'){
                        $request_details['billed_to_client']=isset($billed_to_client)?$billed_to_client:null;
                        $get_editable_fields[]='billed_to_client'; 
                    }
                    else{
                        if($billable_key !== false)
                        unset($get_editable_fields[$billable_key]);
                    }
                    
                }
                else if(!isset($edit_id)&&Auth()->User()->has_any_role_code($this->CONFIG['BILLABLE_CHOOSE_ACCESS'])){
                    $request_details['billed_to_client']=isset($billed_to_client)?$billed_to_client:null;
                    $get_editable_fields[]='billed_to_client';
                }
                else
                {
                    if($billable_key !== false)
                        unset($get_editable_fields[$billable_key]);
                }
            }

            $budget_result = [];
            if(in_array($next_action_details['status'],['STAT_29','STAT_33']) || in_array($action,['desk_review_visa'])){
                $budget_result = $this->provider->budget_verification($request_details,$request_id,$next_action_details['status']);
                if(count($budget_result)){
                    $request_details['budget_success'] = isset($budget_result['budget_success'])?$budget_result['budget_success']:[];
                    $request_details['message'] = isset($budget_result['message'])?$budget_result['message']:[];
                    $get_editable_fields[]="budget_success";
                    $get_editable_fields[]="message";
                }
            }
            if(in_array($next_action_details['status'],['STAT_32','STAT_34','STAT_36'])){
                $this->provider->remove_budget_utilized($request_code);
            }
            # budget validations ends

            $input_with_tables=DB::table('trd_input_fields')
                                ->where('active',1)->select('table_to_insert')
                                ->orderByRaw("FIELD(table_to_insert,'trf_travel_anticipated_details','trf_travel_request','trf_traveling_details','trf_travel_other_details','vrf_visa_request_details',
                                'visa_process_request_details','visa_process_review_details','visa_process_employee_details'
                                ,'visa_process_job_details','visa_process_travel_details','visa_process_dependency_details','visa_process_tracking_details' ) asc")->whereIn('unique_key', $editable_fields)
                                ->groupBy('table_to_insert')->pluck('table_to_insert')->toArray();
            DB::beginTransaction();
            foreach(array_filter($input_with_tables) as $table_name) {
                if(Schema::hasTable($table_name)) {
                    $fields = Schema::getColumnListing($table_name);
                    $save_request_details = Arr::only($request_details, $get_editable_fields);
                    $values_to_insert = Arr::only(array_merge($save_request_details, $common_details), $fields);
                    if($table_name == "trf_travel_request") {
                        $data = Arr::except($values_to_insert, ['request_id']);
                        $condition = Arr::only($values_to_insert, ['request_id']);
                        $condition['request_id'] = $request_code;
                        $exceptional_list=['created_by','created_at'];
                        if(DB::table($table_name)->where($condition)->exists()) {
                            if(in_array($next_action_details['status'],['STAT_02','STAT_28']) && $current_status!=$next_action_details['status']){
                               unset( $exceptional_list[1]);
                            }
                            DB::table($table_name)->where($condition)->update(Arr:: except($data, $exceptional_list)); //['created_by', 'created_at']
                        }
                        else 
                            $request_id = DB::table($table_name)->insertGetId( array_merge($data, $condition) );
                    } else if(in_array($table_name, $tables_have_mutliple_entries)) {
                        $table_reference_key = $table_reference_keys[$table_name];
                        if(is_null($table_reference_key)){
                            if(in_array($table_name, $visa_request_related_tables))
                                $ids_to_check = DB::table($table_name)->where('process_request_id', $request_id)->pluck('id')->toArray();
                            else
                                $ids_to_check = DB::table($table_name)->where('process_request_id', $request_id)->pluck('id')->toArray();
                        }   
                        else 
                            $ids_to_check = $request_details[ $table_reference_key ];
                        $array_values_to_insert = array_filter( $values_to_insert, 'is_array' );
                        $max_count = count(max($array_values_to_insert));
                        $ids_to_check = array_pad($ids_to_check, $max_count, null);
                        $array_values_to_insert['id'] = $ids_to_check;
                        $common_values_to_insert = Arr::except($values_to_insert, array_keys($array_values_to_insert));
                        $values_to_insert_array = array_map( fn(...$e) => array_combine(array_keys($array_values_to_insert),$e), ...array_values($array_values_to_insert) );
                        DB::table($table_name)->whereIn('id', $ids_to_check)->update(['active' => 0]);
                        foreach($values_to_insert_array as $values) {
                            $reference_id = in_array($table_name, $visa_request_related_tables) ? 'process_request_id' : 'request_id';
                            $values[$reference_id] = $request_id;
                            $data = Arr::except(array_merge($values,$common_values_to_insert) , ['id']);
                            $condition = Arr::only(array_merge($values,$common_values_to_insert), ['id']);
                            if(DB::table($table_name)->where($condition)->exists())
                                $data = Arr::except($data, ['created_by', 'created_at']);
                            DB::table($table_name)->updateOrInsert($condition, $data);
                        }
                    } else {
                        $reference_id = in_array($table_name, $visa_request_related_tables) ? 'process_request_id' : 'request_id';
                        $values_to_insert[$reference_id] = $request_id;
                        $data = Arr::except($values_to_insert, [$reference_id]);
                        $condition = Arr::only($values_to_insert, [$reference_id]);
                        if(DB::table($table_name)->where($condition)->exists())
                            $data = Arr::except($data, ['created_by', 'created_at']);
                        DB::table($table_name)->updateOrInsert($condition, $data);
                    }
                }
            }
            $proof_type = $request->input('proof_type');
            if(isset($proof_type) && in_array('proof_type',$get_editable_fields)){
                $proof_related_details=[
                'proof_type' => $request->input('proof_type'),
                'proof_number' => $request->input('proof_number'),
                'proof_request_for' => $request->input('proof_request_for'),
                'proof_issued_place' =>$request->input('proof_issued_place'),
                'proof_issue_date' => $request->input('proof_issue_date'),
                'proof_expiry_date' => $request->input('proof_expiry_date'),
                'proof_file_path'=>$request->input('proof_file_path'),
                'proof_name'=>$request->input('proof_name'),
                'file_reference_id'=>$request->input('file_reference_id'),
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
                $return_value=$this->travelRequest->save_proof_details($request_id,$proof_related_details);
            }
            // status tracker
            $tracker_details=[
                'request_id' => $request_id,
                'old_status_code' => $current_status,
                'new_status_code' => $next_action_details['status'],
                'action' => $action,
                'billed_to_client' => $request->input('billed_to_client'),
                'comments' => $comments,
                'created_by' => Auth::User()->aceid?Auth::User()->aceid:$request->input('action_user'),
                'active' => 1,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ];
            DB::table('trf_request_status_tracker')->insert($tracker_details);
            
            if(in_array($current_status, [0, 'STAT_01', 'STAT_28'])) $this->provider->assign_role('VIS_USR', $traveler_id);

            if(in_array($next_action_details['status'], ['STAT_14'])) $this->save_visa_details($request_id);

            DB::commit();
            $request_details['request_id'] = $request_id;
            if(isset($edit_id)) {
                $get_request_details=DB::table('trf_travel_request as ttr')
                ->join('vrf_visa_request_details as vrd','vrd.request_id','ttr.id')
                ->where('ttr.id',$request_id)
                ->select('ttr.department_code','ttr.module','ttr.travaler_id','ttr.billed_to_client','ttr.request_id','ttr.project_code','ttr.practice_unit_code','vrd.visa_type', 'vrd.visa_category')->first();
                $anticipated_amount = DB::table('trf_travel_anticipated_details as tad')
                ->join('trf_currency_conversion_rates as ccr','tad.anticipated_currency','ccr.parent_currency')
                ->select(DB::raw("SUM(tad.amount*ccr.conversion_rate) as amount"))
                ->where([['tad.request_id',$request_id],['tad.active',1]])
                ->value(DB::raw("SUM(tad.amount*ccr.conversion_rate) as amount"));
                $get_all_details=(array)$get_request_details;
                $get_all_details['amount'] = $anticipated_amount;
                $approval_matrix_fields=$this->travelRequest->perform_fields_for_approval_matrix($get_all_details,$request_id);
            } else {
                $approval_matrix_fields = $this->travelRequest->perform_fields_for_approval_matrix(array_merge($request_details, $common_details));
            }
            $save_actions = ["save", "visa_user_save", "onsite_hr_save", "offshore_hr_save", "petition_process_save", "visa_aprpoval_save", "visa_entry_save", "save_visa_process"];
            if(!in_array($action, $save_actions)) {
                $fetch_approval_flow = $this->travelRequest->fetch_approval_flow($approval_matrix_fields, $traveler_id);
                $rule_code = $this->get_matched_rule(compact("visa_type", "visa_category")+['visa_flow'=>'NA']);
                $updated_flow = array_key_exists('updated_flow', $next_action_details) ? $next_action_details['updated_flow'] : null;
                $next_action_details_after = $this->travelRequest->fetch_next_process_flow($request_id, $module, $action, $department_mapping_code, $current_status, $comments, $from_mail_approval, $action_user, rule_code: $rule_code, updated_flow: $updated_flow);
                if(!array_key_exists('status', $next_action_details_after)) {
                    $message_text=$this->messages['en']['INVALID_ACTION'];
                    return json_encode(['error'=>'INV_ACTION','message_text'=>$message_text]);
                } else {
                    if($next_action_details['status']!=$next_action_details_after['status']){
                        DB::table('trf_travel_request')->where('id',$request_id)->update(['status_id'=>$next_action_details_after['status']]);
                        $next_action_details=$next_action_details_after;
                    }
                }
            }
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
             return json_encode([
                'next_action_details'=>$next_action_details,
                'action'=>$action,
                'request_id'=>$request_id
            ]);
        }
        catch (\Exception $e)
        {
            DB::rollback();
            Log::error("Error in save_request_details");
            Log::error($e);
            return json_encode(["error" => "", "message_text" => "Error has been occurred. Please try again and write to help.is@aspiresys.com if issue persists."]);
        }
    }

    /**
     * To generate unique code for the request
     * @author venkatesan.raj
     * 
     * @param void
     * 
     * @return string
     */
    public function generate_request_code()
    {
        try
        {
            $year = date('y'); $month = date('m');
            if($month > 3) $year++;
            $prefix = ($year-1).$year;
            $short_notation = $this->MODULE[$this->module_id];
            $request_code_format = $prefix.$short_notation;
            $number = 1;
            $request_details = DB::table('trf_travel_request')->where('request_id', 'LIKE', "%{$request_code_format}%");
            if($request_details->exists()) {
                $latest_request_code = $request_details->orderBy('id', 'desc')->value('request_id');
                $number = intval( str_replace($request_code_format, '', $latest_request_code) ) + 1;
            }
            $suffix = str_pad($number, 4, 0, STR_PAD_LEFT);
            $request_code = $request_code_format.$suffix;
            return $request_code;
        }
        catch (\Exception $e)
        {
            Log::error("Error in generate_request_code");
            Log::error($e);
        }
    }

    /**
     *  To get the gm reviewers
     *  @author venkatesan.raj
     * 
     *  @param string $from_country
     *  @param string $to_country
     *  @param bool $active_alone optional
     * 
     *  @return array
     */
    public function list_gm_reviewers($from_country, $to_country, $active_alone = true)
    {
        try
        {
            $params = compact("from_country", "to_country");
            $rule_code = $this->get_matched_rule($params+['reviewer_mapping'=>'NA'], $active_alone);
            $gm_reviewers = DB::table("vrf_visa_gm_reviewers_mapping")->where('rule_code', $rule_code);
            if($active_alone) $gm_reviewers = $gm_reviewers->where('active', 1);
            $gm_reviewers = $gm_reviewers->pluck('reviewer_aceid')->toArray();
            return $gm_reviewers;
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_gm_reviewers");
            Log::error($e);
        }
    }

    /**
     *  To get the hr reviewers
     *  @author venkatesan.raj
     * 
     *  @param string $from_country
     *  @param string $to_country
     *  @param bool $active_alone optional
     * 
     *  @return array
     */
    public function list_hr_reviewers($from_country, $to_country, $active_alone = true)
    {
        try
        {
            $params = compact("from_country", "to_country");
            $rule_code = $this->get_matched_rule($params+['reviewer_mapping'=>'NA'], $active_alone);
            $hr_reviewers = DB::table("vrf_visa_gm_reviewers_mapping")->where('rule_code', $rule_code);
            if($active_alone) $hr_reviewers = $hr_reviewers->where('active', 1);
            $hr_reviewers = $hr_reviewers->pluck('reviewer_aceid')->toArray();
            return $hr_reviewers;
        }
        catch (\Exception $e)
        {
            Log::error("Error in list_hr_reviewers");
            Log::error($e);
        }
    }


    /**
     * To return the rule matched the given params
     * @author venkatesan.raj
     * 
     * @param array $params
     * @param bool $active_alone optional
     * @param string $request_id optional
     * 
     * @return string
     */
    public function get_matched_rule($params, $active_alone = true, $request_id = null)
    {
        try
        {
            $rules_details = DB::table('vrf_visa_rule_conditions')
                                ->select(
                                    "rule_code",
                                    DB::raw("JSON_ARRAYAGG(JSON_OBJECT('field', mapped_field, 'condition', mapped_condition, 'values', mapped_value)) as cond_list")
                                )->groupBy('rule_code')->get()->toArray();
            $rules_details = json_decode(json_encode($rules_details), true);
            $rule_condition_mapping = array_combine(
                array_column($rules_details, 'rule_code'),
                array_column($rules_details, 'cond_list')
            );
            $matched_rules = [];$max_count=0;
            foreach($rule_condition_mapping as  $rule_code => $cond_list) {
                $cond_array = json_decode($cond_list, true);
                $is_passed = false; $passed_count = 0;
                foreach($cond_array as $cond) {
                    if(array_key_exists($cond["field"], $params) && $this->match_params_against_condition($cond, $params[ $cond["field"] ], $request_id)) {
                        $is_passed = true;
                        $passed_count++;
                    } else {
                        $is_passed = false;
                        break;
                    }
                }
                if($is_passed){
                    if($max_count == $passed_count){
                        array_push($matched_rules, $rule_code);
                    } else if($passed_count > $max_count) {
                        $matched_rules = [$rule_code];
                        $max_count = $passed_count;
                    }
                } 
            }
            $matched_rule_code = DB::table("vrd_visa_rule_config")->whereIn("unique_key", $matched_rules);
            if($active_alone) $matched_rule_code = $matched_rule_code->where('active', 1);
            $matched_rule_code = $matched_rule_code->orderBy('precedence', 'desc')->value('unique_key');
            return $matched_rule_code;
        }
        catch (\Exception $e)
        {
            Log::error("Error in get_matched_rule");
            Log::error($e);
        }
    }

    /**
     * To check the given params matchs the condition
     * @author venketasan.raj
     * 
     * @param array condition_array,
     * @param string param,
     * @param string request_id
     * @param bool check_role_alone optional
     * 
     * @return bool
     */
    public function match_params_against_condition($condition_array, $param, $request_id, $check_role_alone=false)
    {
        try
        {
            extract($condition_array);
            $casting_needed_conditions = ['in', 'not_in', 'between'];
            if(in_array($condition, $casting_needed_conditions)) {
                $list = explode("|", $values);
                [$start, $end] = $list;
            }
            switch ($field)
            {
                case "role" :
                    if($condition == "equal_to" && $values == "VIS_USR" && !$check_role_alone ) {  
                        return DB::table('trf_travel_request')->where([['id', $request_id],['travaler_id', Auth::User()->aceid]])->exists();
                    }else if($condition == "equal_to" && $values == "HR_PRT" && !$check_role_alone ) {
                        $created_by = DB::table('trf_travel_request')->where([['id', $request_id],['created_by', Auth::User()->aceid]])->exists();
                        $hr_partner = DB::table('vrf_visa_request_details')->where([['request_id', $request_id],['hr_partner', Auth::User()->aceid]])->exists();
                        return Auth::User()->has_any_role_code('HR_PRT') && ($created_by || $hr_partner);
                    } else if($condition == "equal_to" && $values == "AN_COST_VISA" && !$check_role_alone) {
                        $accessible_users = $this->provider->get_travel_desk_user_details($request_id, ['AN_COST_VISA']);
                        return ($accessible_users&&in_array(Auth::User()->aceid, $accessible_users)) ? true : false;
                    } else if($condition == "equal_to" && $values == "GM_REV" && !$check_role_alone) {
                        $accessible_users = $this->provider->get_travel_desk_user_details($request_id, ['GM_REV']);
                        return ($accessible_users&&in_array(Auth::User()->aceid, $accessible_users)) ? true : false;
                    } else if($condition == "equal_to" && $values == "HR_REV" && !$check_role_alone) {
                        $accessible_users = $this->provider->get_travel_desk_user_details($request_id, ['HR_REV']);
                        return ($accessible_users&&in_array(Auth::User()->aceid, $accessible_users)) ? true : false;
                    } else if($condition == "equal_to" && $values == "TRV_PROC_VISA" && !$check_role_alone) {
                        $accessible_users = $this->provider->get_travel_desk_user_details($request_id, ['TRV_PROC_VISA']);
                        return ($accessible_users&&in_array(Auth::User()->aceid, $accessible_users)) ? true : false;
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
        }
        catch (\Exception $e)
        {
            Log::error("Error in match_params_against_condition");
            Log::error($e);
        }
    }

    /**
     * To get the tab configuration for given flow
     * @author venkatesan.raj
     * 
     * @param string $rule_code
     * @param string $status_id optional
     * 
     * @return array
     */
    public function get_configured_tabs($rule_code, $status_id = 0)
    {
        try
        {
            $tabs_status_mapping = [
                'initiation_tab' => [0, 'STAT_01','STAT_28'],
                'gm_review_tab' => ['STAT_02'],
                'gm_review_approval_tab' => ['STAT_02', 'STAT_04','STAT_05','STAT_06','STAT_08','STAT_09','STAT_10','STAT_11', 'STAT_24', 'STAT_25', 'STAT_26', 'STAT_27', 'STAT_15', 'STAT_16', 'STAT_17', 'STAT_18', 'STAT_19', 'STAT_20', 'STAT_21', 'STAT_22', 'STAT_26', 'STAT_27'],
                'approval_tab' => ['STAT_04','STAT_05','STAT_06','STAT_08','STAT_09','STAT_10','STAT_11', 'STAT_24', 'STAT_25', 'STAT_26', 'STAT_27', 'STAT_15', 'STAT_16', 'STAT_17', 'STAT_18', 'STAT_19', 'STAT_20', 'STAT_21', 'STAT_22', 'STAT_26', 'STAT_27'],
                'hr_review_tab' => ['STAT_29','STAT_30','STAT_32'],
                'petition_process_tab' => ['STAT_31','STAT_34','STAT_38'],
                'visa_stamping_tab' => ['STAT_33', 'STAT_35', 'STAT_36','STAT_37'],
                'completed_tab' => ['STAT_12', 'STAT_14'],
            ];
            // Adding statuses to visa_stamping_tab in case of short term visa request
            if($rule_code == "VIS_RUL_002") $tabs_status_mapping['visa_stamping_tab'] = array_merge($tabs_status_mapping['visa_stamping_tab'], ['STAT_12', 'STAT_14']); 
            $configured_tabs = DB::table("vrd_visa_tab_config")->where([['rule_code', $rule_code],['active', 1]])->value("visible_tabs");
            $visible_tabs = explode(",", $configured_tabs);
            $tab_classes = []; $status_crossed = false;
            $all_visible_tabs = Arr::only($tabs_status_mapping, $visible_tabs);
            foreach($all_visible_tabs as $key => $value) {
                if( $status_crossed ) $tab_classes[$key] = "";
                else if ( in_array($status_id, $value) ) {
                    $status_crossed = true;
                    $tab_classes[$key] = "active current";
                } else {
                    $tab_classes[$key] = "active";
                }
            }
            
            return compact("visible_tabs", "tab_classes");
        }
        catch(\Exception $e)
        {
            Log::error("Error in get_configured_tabs");
            Log::error($e);
        }
    }
    /**
     * To move files from temp folder to actual folder
     * @author venkatesan.raj
     * 
     * @param array $file_details
     * @param string $request_code,
     * @param string $traveler_id,
     * 
     * @param array $file_path,
     */
    public function move_temp_files($file_details, $request_code, $traveler_id)
    {
        try
        {
            $temp_folder = 'temp/';
            $upload_paths = $this->visa_config['upload_path']; $file_name_formats = $this->visa_config['file_name_format'];
            $file_related_details = [];
            foreach($file_details as $file_type => $file_names) {
                $file_names = explode(",", $file_names); $new_file_names=[];
                DB::table('temporary_file_details')->whereIn('system_name', $file_names)->update(['active' => 0]);
                foreach($file_names as $index => $file_name) {
                    $upload_path = array_key_exists($file_type, $upload_paths) ? $upload_paths[$file_type] : $upload_paths['default'];
                    $file_name_format = array_key_exists($file_type, $file_name_formats) ? $file_name_formats[$file_type] : $file_name_formats['default'];
                    $extension = array_key_exists('extension', pathinfo($file_name)) ? pathinfo($file_name)['extension'] : null;
                    $new_file_name = implode('_',[$request_code, $traveler_id, $file_name_format, ($index+1).'.'.$extension]);
                    $source_file_path = public_path($temp_folder.$file_name);
                    $destination_file_path = public_path("$upload_path/$new_file_name");
                    if(file_exists($source_file_path))
                        rename($source_file_path, $destination_file_path);
                    else if(file_exists(public_path("$upload_path/$file_name")))
                        rename(public_path("$upload_path/$file_name") , $destination_file_path);
                    array_push($new_file_names, "$upload_path/$new_file_name");
                }
                $file_related_details[$file_type] = implode(',', $new_file_names);
            }
            return $file_related_details;
        }
        catch (\Exception $e)
        {
            Log::error("Error in move temp files");
            Log::error($e);
        }
    }
    /**
     * To upload the offer letter
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function upload_offer_letter(Request $request)
    {
        $edit_id = $request->input('request_code');
        $green_card_title = $request->input('green_card_title');
        $request_id = isset($edit_id) ? Crypt::decrypt($edit_id) : null;
        $request_code = DB::table('trf_travel_request')->where('id', $request_id)->value('request_id');
        $word_file_name = $request->input('word_filename');
        $immigration_filename = $request->input('immigration_filename');
        $filename = $request->input('filename');
        $uploaded_files = $request->file();
        $upload_paths = $this->visa_config['upload_path'];
        $response = []; $filepath=""; $immigration_filepath=""; $word_filepath="";
        foreach($uploaded_files as $file_type => $file) {
            if($file_type == "pdf") $file_name = $filename;
            if($file_type == "immigration_offer_letter") $file_name = $immigration_filename;
            if($file_type == "word") $file_name = $word_file_name;
            $edit_id = strstr($file_name, '_', true);
            $file_name = str_replace($edit_id, $request_code, $file_name);
            $upload_path = array_key_exists($file_type, $upload_paths) ? $upload_paths[$file_type] : "offer_letter";
            $file->move(public_path($upload_path), $file_name);
            if($file_type == "pdf") $filepath = "/$upload_path/$file_name";
            if($file_type == "immigration_offer_letter") $immigration_filepath = "/$upload_path/$file_name";
            if($file_type == "word") $word_filepath = "/$upload_path/$file_name";
        }
        $data = [
            'green_card_title' => $green_card_title,
            'offer_letter_path' => implode(',', [$filepath, $word_filepath]),
            'immigration_offer_letter_path' => $immigration_filepath,
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s'),
        ];
        $condition = [
            'process_request_id' => $request_id
        ];
        if(DB::table('visa_process_tracking_details')->where($condition)->exists())
            $data = Arr::except($data, ['created_at']);
        DB::table('visa_process_tracking_details')->updateOrInsert($condition, $data);
        return json_encode([
            'offer_letter_path' => $filepath,
            'immigration_offer_letter_path' => $immigration_filepath,
            'word_offer_letter_path' => $word_filepath,
        ]);
    }
    /**
     * To link the visa to travel request
     * @author venkatesan.raj
     * 
     * @param string $id
     * 
     * @return Redirect
     */
    public function link_to_travel($id)
    {
        try
        {
            session()->put('visa_request_id', $id);
            return redirect('/request');
        }
        catch (\Exception $e)
        {
            Log::error("Error in link_to_travel");
            Log::error($e);
        }
    }
        /**
     * To  the visa to travel request
     * @author venkatesan.raj
     * 
     * @param string $id
     * 
     * @return Redirect
     */
    public function get_travel_link_details($request_id)
    {
        try
        {
            $visa_request_for = "RF_08";
            $default_module = "MOD_02"; $default_request_for = "RF_05";
            $mandatory_proof_types = ['PR_TY_03_01', 'PR_TY_03_02'];

            $details = (array)DB::table('trf_travel_request as tr')
                        ->leftJoin('trf_traveling_details as td', 'td.request_id', 'tr.id')
                        ->leftJoin('vrf_visa_request_details as vr', 'vr.request_id', 'tr.id')
                        ->leftJoin('visa_process_employee_details as ed', 'ed.process_request_id', 'tr.id')
                        ->select('tr.project_code','tr.request_for_code', 'tr.department_code', 'tr.practice_unit_code', 'tr.requestor_entity','td.from_country', 'td.to_country', 'td.to_city', DB::raw('DATE_FORMAT(td.from_date, "%d-%b-%Y") as from_date') ,DB::raw('DATE_FORMAT(td.to_date, "%d-%b-%Y") as to_date'), 'td.visa_type_code', 'vr.visa_type', 'vr.visa_number', DB::raw('DATE_FORMAT(ed.date_of_birth, "%d-%b-%Y") as date_of_birth'), 'ed.address')
                        ->where('tr.id', $request_id)->first();

            
            $travaler_id = DB::table('trf_travel_request')->where('id', $request_id)->value('travaler_id');
            $from_country = DB::table('trf_traveling_details')->where('id', $request_id)->value('from_country');

            $proof_related_details = $proof_details=DB::select("
            SELECT JSON_OBJECTAGG(pad.input_name,trpd.value)as proof_value, rpfd.original_name, rpfd.system_name,tpt.display_name as proof_display_name, tpt.proof_type, tpt.unique_key as proof_type_id, trfc.request_for as proof_request_for, trfc.unique_key as request_for_code, tpt.upload_path
            FROM trf_travel_request_proof_details as trpd
            LEFT JOIN trf_request_proof_file_details AS rpfd ON trpd.request_id = rpfd.request_id AND trpd.proof_type_id = rpfd.file_type_id AND trpd.request_for_code = rpfd.request_for_code AND trpd.request_proof_file_id = rpfd.id AND rpfd.active = 1
            LEFT JOIN trd_proof_type AS tpt ON tpt.unique_key = trpd.proof_type_id
            LEFT JOIN trf_request_for AS trfc ON trfc.unique_key = trpd.request_for_code
            LEFT JOIN trd_proof_additional_details as pad on pad.id=trpd.proof_attr_id
            WHERE trpd.request_id = ? AND trpd.active = 1
            GROUP BY trpd.request_proof_file_id
        ",[$request_id]);

            $proof_details = json_decode(json_encode($proof_related_details), true);
                  
            if($from_country == "COU_001") { 
                $proof_types_avail = array_column(
                    array_filter(
                        $proof_details,
                        fn($e) => is_array($e) && array_key_exists('request_for_code', $e) && $e['request_for_code'] == $visa_request_for
                    ),
                    'proof_type_id',
                );
                $proof_types_req = array_diff($mandatory_proof_types, $proof_types_avail);
                $proof_details = DB::table('trf_user_detail_mapping as udm')
                    ->leftJoin('trd_proof_additional_details as pad', function ($join) { $join->on('pad.attribute_id', 'udm.attribute')->where('pad.active', 1); })
                    ->leftJoin('trd_proof_type as pt', function ($join) { $join->on('pt.unique_key', 'pad.proof_type_id')->where('pt.active', 1); })
                    ->leftJoin('trf_request_for as rf', function ($join) use($visa_request_for) { $join->where('rf.unique_key',$visa_request_for); })
                    ->select(DB::raw("JSON_OBJECTAGG(pad.input_name, udm.mapping_value) as proof_value",), 'pad.proof_type_id', 'pt.proof_type', 'pt.display_name as proof_display_name', 'pt.upload_path', 'rf.unique_key as request_for_code', 'rf.request_for as proof_request_for')
                    ->where([['udm.aceid', $travaler_id], ['udm.active', 1]])->whereIn('pad.proof_type_id', $proof_types_req)
                    ->groupBy('pad.proof_type_id')->get()->toArray();

                foreach($proof_details as $key => $value) {
                    if(!$value && !property_exists($value, 'proof_type_id')) continue;
                    $file_details = $this->provider->get_proof_file_details($travaler_id, $this->module_id, $value->proof_type_id);
                    $proof_details[$key]->original_name = is_array($file_details) && array_key_exists('file_name', $file_details) ? $file_details['file_name'] : null;
                    $proof_details[$key]->system_name = is_array($file_details) && array_key_exists('name', $file_details) ? $file_details['name'] : null;
                }

                $proof_related_details = array_merge($proof_related_details, $proof_details);
            }
    
            $details['to_city_list'] = $this->provider->list_city($details['to_country']);
            $details['module'] = "MOD_02";
            //$details['request_for_code'] = "RF_05";
            $details['travel_type'] = "TRV_02_01";
            $details['proof_related_details'] = $proof_related_details;
            return $details;
        }
        catch (\Exception $e)
        {
            Log::error("Error in link_to_travel");
            Log::error($e);
        }
    }
    /**
     * To get section details
     * @author venkatesan.raj
     * 
     * @param string $status_id
     * @param string $request_id optional
     * 
     * @return array
     */
    public function get_section_details($status_id, $request_id=null) 
    {
        try
        {
            $configured_visible_sections = $this->visa_config["visible_sections"];
            $waiting_messages = $this->visa_config["waiting_messages"];
            $completed_messages = $this->visa_config["completed_messages"];
            $reject_messages = $this->visa_config["rejected_messages"];
            $visible_sections = array_keys(array_filter($configured_visible_sections, fn($e) => in_array($status_id, $e)));
            $info_messages = [];
            if(isset($request_id)) {
                if(array_key_exists($status_id, $waiting_messages))
                    $info_messages['waiting_message'] =  $waiting_messages[$status_id];
                if( array_key_exists($status_id, $reject_messages) )
                    $info_messages['reject_message'] = $reject_messages[$status_id];
                
                $info_messages['completed_messages']['immigration_review'] = $completed_messages['immigration_review'];
                $info_messages['completed_messages']['onsite_salary_negotiation'] = $completed_messages['onsite_salary_negotiation'];
                $info_messages['completed_messages']['petition_process'] = $completed_messages['petition_process'];
                $info_messages['completed_messages']['visa_approval'] = $completed_messages['visa_approval'];
            }
            return compact('visible_sections', 'info_messages');
        }
        catch (\Exception $e)
        {
            Log::error("Error in get_section_details");
            Log::error($e);
        }
    }

    /**
     * To save the visa details for travel purpose
     * @author venkatesan.raj
     * 
     * @param string request_id
     * 
     * @return bool
     */
    public function save_visa_details($request_id)
    {
        try
        {
            $visa_details = (array)DB::table('trf_travel_request as tr')
                                ->leftJoin('trf_traveling_details as td', 'td.request_id', 'tr.id')
                                ->leftJoin('vrf_visa_request_details as vr', 'vr.request_id', 'tr.id')
                                ->select('tr.travaler_id', 'td.to_country', 'vr.visa_number', 'vr.visa_type')
                                ->where('tr.id', $request_id)->first();
            
            if($visa_details){
                $travel_visa_details = DB::table('trd_visa_details');
                if(!$travel_visa_details->where([[ 'aceid', $visa_details['travaler_id']], ['visa_country_code', $visa_details['to_country'] ], ['visa_type', $visa_details['visa_type'] ] ] )->exists()) {
                    // $travel_visa_details->insert([
                    //     'aceid' => $visa_details['travaler_id'],
                    //     'visa_country_code' => $visa_details['to_country'],
                    //     'visa_number' => $visa_details['visa_number'],
                    //     'visa_type' => $visa_details['visa_type'],
                    //     'active' => 1,
                    //     'created_at' => date('Y-m-d h:i:s'),
                    //     'updated_at' => date('Y-m-d h:i:s'),
                    // ]);
                    // Log::info("Visa details added");
                }
            }
            return false;   
        }
        catch (\Exception $e)
        {
            Log::error("Error in save_visa_process");
            Log::error($e);
        }
    }
    /**
     * To check whether the user can update the salary range
     * @author venkatesan.raj
     * 
     * @param string $status_id
     * @param string $aceid optional
     * 
     * @return bool
     */
    public function can_edit_salary_range($status_id, $aceid=null) {
        try
        {
            if(is_null($aceid)) $aceid = Auth::User()->aceid;
            $configured_status = $this->visa_config['salary_range_edit_status'];
            $configured_role = $this->visa_config['salary_range_edit_role'];
            $user_roles = DB::table('trf_user_role_mapping')->where([['aceid', $aceid],['active', 1]])->distinct()->pluck('role_code')->toArray();
            if( in_array($status_id, $configured_status) &&  count(array_intersect($configured_role, $user_roles)))
                return true;
            return false;
        }
        catch (\Exception $e)
        {
            Log::error("Error in can_edit_salary_range");
            Log::error($e);
            throw $e;
        }
    }
    /**
     * To update the salary range after hr reviewer submitted the request
     * @author venkatesan.raj
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function update_salary_range(Request $request)
    {
        try
        {
            $key = $this->get_new_key();
            $edit_id = $request->input('edit_id');
            if(is_null($edit_id)) throw new \Exception("Edit id is null");
            $request_id = Crypt::decrypt($edit_id);
            $data_to_update = [
                'salary_range_from' => $request->input('salary_range_from') ? DB::raw("AES_ENCRYPT('".$request->input('salary_range_from')."', UNHEX(SHA2('".$key."', 512)))") : null,
                'salary_range_to' => $request->input('salary_range_to') ? DB::raw("AES_ENCRYPT('".$request->input('salary_range_to')."', UNHEX(SHA2('".$key."', 512)))") : null,
                'updated_at' => date('Y-m-d h:i:s'),
            ];
            $condition_to_update = [
                'process_request_id' => $request_id,
            ];
            $request_details = (array)DB::table('visa_process_review_details')->select('salary_range_from', 'salary_range_to')->where($condition_to_update)->first();
            DB::table('visa_process_review_details')->where($condition_to_update)->update($data_to_update);
            $data_to_insert = [
                'visa_process_id' => 1,
                'process_request_id' => $request_id,
                'old_salary_range_from' => $request_details['salary_range_from'],
                'old_salary_range_to' => $request_details['salary_range_to'],
                'new_salary_range_from' => $request->input('salary_range_from'),
                'new_salary_range_to' => $request->input('salary_range_to'),
                'updated_by' => Auth::User()->aceid,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ];
            DB::table('visa_process_salary_range_status_tracker')->insert($data_to_insert);
            return json_encode([
                "message" => "Salary range has been updated successfully",
                "redirect_url" => "/hr_review",
            ]);
        }
        catch(\Exception $e)
        {
            Log::error("Error in update_salary_range");
            Log::error($e);
            return json_encode(["error" => "Error occured while updating the salary range. Please try again or contact help.mis@aspiresys.com for further assistance."]);
        }
    }

    public function save_need_assistance_details(Request $request){
        $request_id=Crypt::decrypt($request->edit_id);
        $action=$request['action'];

        $request_details=DB::table('vrf_need_assistance_log')->insert([
            'request_id' =>  $request_id,
            'created_by' => Auth::user()->aceid,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $next_action_details=DB::table('vrf_visa_flow_config')->where('action',$action)->where('active',1)->first();
        return json_encode([
            'next_action_details'=>$next_action_details,
            'action'=>$action,
            'request_id'=>$request_id
        ]);
    }
}
