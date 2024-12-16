@extends('header')
<script type="text/javascript" src="{{ URL::asset('js/handleRefresh.js') }}"></script>
@php
    $page_navigation_no = "visa_request";
    $visible_fields = []; $editable_fields = []; $mandatory_class_list = [];
    if(isset($field_details) && is_array($field_details) && count($field_details)) {
        $visible_fields = array_key_exists('visible_fields', $field_details) ? $field_details['visible_fields'] : [];
        $editable_fields = array_key_exists('editable_fields', $field_details) ? $field_details['editable_fields'] : [];
        $field_attr = array_key_exists('field_attr', $field_details) ? $field_details['field_attr'] : [];
        $select_options = array_key_exists('select_options', $field_details) ? $field_details['select_options'] : [];
        $non_mandatory_field_list = ["INP_105", "INP_107", "INP_135"];
        $mandatory_class_list = array_combine( $visible_fields, array_map(fn($e) => in_array($e, $editable_fields) && !in_array($e, $non_mandatory_field_list) ? "required-field" : "", $visible_fields) );
    }
    $edit_id = isset($edit_id) ? Crypt::encrypt($edit_id) : null;
    $band_detail = isset($band_detail) ? $band_detail : null;
    extract($request_full_details);
    $travelling_details = isset($travelling_details) ? json_decode(json_encode($travelling_details), true) : [];
    $travelling_details = array_key_exists(0, $travelling_details) ? $travelling_details[0] : [];
    if(isset($proof_related_details) && is_array($proof_related_details)) {
        $proof_details = array_key_exists("proof_details", $proof_related_details) ? $proof_related_details["proof_details"] : null;
    }
    $travaler_id=null; $traveler_name=null; $request_code=null; $status=null;
    $visa_type_code=null; $visa_type=null; $visa_category_code=null; $visa_category=null; $filing_type_id=null;
    $filing_type=null; $project_name=null;  $department_name=null; $customer_name=null;
    $origin_name=null; $to_country=null; $to_country_name=null; $to_city=null; $to_city_name=null;
    $from_date=null; $to_date=null; $request_for_code=null; $request_for=null;$practice_unit_name=null;
    $dob=null; $address=null; $doj=null; $education_category_id=null; $education_category=null; $education_details_id=null;
    $education_details=null; $aspire_experience=null; $overall_experience=null; $cv_file_path=null; $cv_file_names=[];
    $degree_file_path=null; $degree_file_names=[];$aspire_experience_years=null; $aspire_experience_months=null; $overall_experience_years=null; $overall_experience_months=null;
    $dob=null; $address=null; $status_id=null; $module=null; $bill_to_client=null; $minimum_wage=null; $work_location=null;
    $salary_range_from=null; $salary_range_to=null; $us_job_title_id=null; $us_job_title=null;
    $undertaken_completed=1; $us_salary=null; $one_time_bonus=null; $one_time_bonus_payout_date=null; $next_salary_revision_on=null;
    $us_manager_id=null; $us_manager=null; $inszoom_id=null; $petitioner_entity_id=null; $petitioner_entity=null; $attorneys_id=null; $attorneys=null; $petition_file_date=null; $receipt_no=null; $petition_start_date=null; $petition_end_date=null; $petition_file_path=null; $petition_file_names=[];
    $entry_type_id=null; $entry_type=null; $visa_interview_type_id=null; $visa_interview_type=null; $visa_ofc_date=null; $visa_interview_date=null; $visa_status_id=null; $visa_status=null;
    $travel_date=null; $travel_location=null; $visa_number=null; $visa_file_path=null; $visa_file_names=[]; $traveling_type_id=null;
    $first_name=null; $last_name=null; $job_title=null; $green_card_title=0; $offer_letter_path_pdf=null; $offer_letter_path_word=null; $immigration_offer_letter=null;
    $record_number=null; $most_recent_doe=null; $admit_until=null; $gc_initiated_on=null; $job_title_id=null; $job_title=null; $visa_currency=null; $visa_currency_notation=null;
    $offer_letter_template=null; $hr_admin_aceid=null; $hr_admin_name=null; $hr_admin_designation=null; $hr_admin_department=null; $hr_admin_mail=null; $signature_location=null;
    $origin_based_input=null;

    $project_code = isset($user_details) && is_array($user_details) ? ( array_key_exists("default_project", $user_details) ? $user_details["default_project"] : null ) : null;
    $department_code = [];
    $customer_code = isset($user_details) && is_array($user_details) ? ( array_key_exists("default_customer", $user_details) ? $user_details["default_customer"] : null ) : null;
    $practice_unit_code = isset($user_details) && is_array($user_details) ? ( array_key_exists('default_du', $user_details) ? $user_details['default_du'] : null ) : null;
    $origin = isset($user_details) && is_array($user_details) ? ( array_key_exists("origin", $user_details) ? $user_details["origin"] : null ) : null;
    $requestor_entity = isset($user_details) && is_array($user_details) ? ( array_key_exists("entity", $user_details) ? $user_details["entity"] : null ) : null;    
    if(isset($edit_id)) {
        if(isset($request_details) && $request_details) {
            $request_code = property_exists($request_details, 'request_code') ? $request_details->request_code : null;
            $module = property_exists($request_details, 'module') ? $request_details->module : null;
            $status_id = property_exists($request_details, 'status_id') ? $request_details->status_id : null;
            $status = property_exists($request_details, 'status_name') ? $request_details->status_name : null;
            $travaler_id = property_exists($request_details, 'travaler_id') ? $request_details->travaler_id : null;
            $traveler_name = property_exists($request_details, 'traveler_name') ? $request_details->traveler_name : null;
            $request_for_code = property_exists($request_details, 'request_for_code') ? $request_details->request_for_code : null;
            $request_for = property_exists($request_details, 'request_for') ? $request_details->request_for : '-';
            $requestor_entity = property_exists($request_details, 'requestor_entity') ? $request_details->requestor_entity : null;
            $project_code = property_exists($request_details, 'project_code') ? $request_details->project_code : null;
            $project_name = property_exists($request_details, 'project_name') ? $request_details->project_name : '-';
            $department_code = property_exists($request_details, 'department_code') ? $request_details->department_code : null;
            $department_name = property_exists($request_details, 'department_name') ? $request_details->department_name : '-';
            $customer_code = property_exists($request_details, 'customer_code') ? $request_details->customer_code : null;
            $customer_name = property_exists($request_details, 'customer_name') ? $request_details->customer_name : '-';
            $practice_unit_code = property_exists($request_details, 'practice_unit_code') ? $request_details->practice_unit_code : null;
            $practice_unit_name = property_exists($request_details, 'practice_unit_name') ? $request_details->practice_unit_name : '-';
            $bill_to_client = property_exists($request_details, 'billed_to_client') ? $request_details->billed_to_client : '-';
        }
        if(isset($visa_details) && $visa_details) {
            $visa_type_code = property_exists($visa_details, 'visa_type_code') ? $visa_details->visa_type_code : null;
            $visa_type = property_exists($visa_details, 'visa_type') ? $visa_details->visa_type : '-';
            $visa_category_code = property_exists($visa_details, 'visa_category_code') ? $visa_details->visa_category_code : null;
            $visa_category = property_exists($visa_details, 'visa_category') ? $visa_details->visa_category : '-';
            $filing_type_id = property_exists($visa_details, 'filing_type_id') ? $visa_details->filing_type_id : null;
            $filing_type = property_exists($visa_details, 'filing_type') ? $visa_details->filing_type : '-';
            $dob = property_exists($visa_details, 'date_of_birth') ? ( $visa_details->date_of_birth ? date('d-M-Y', strtotime($visa_details->date_of_birth)): null ) : null;
            $doj = property_exists($visa_details, 'date_of_joining') ? ( $visa_details->date_of_joining ? date('d-M-Y', strtotime($visa_details->date_of_joining)): null ) : null;
            $address = property_exists($visa_details, 'address') ? $visa_details->address : null;
            $education_category_id = property_exists($visa_details, 'education_category_id') ? $visa_details->education_category_id : null;
            $education_category = property_exists($visa_details, 'education_category') ? $visa_details->education_category : null;
            $education_details_id = property_exists($visa_details, 'education_details_id') ? $visa_details->education_details_id : null;
            $education_details = property_exists($visa_details, 'education_details') ? $visa_details->education_details : null;
            $india_experience = property_exists($visa_details, 'india_experience') ? $visa_details->india_experience : null;
            $overall_experience = property_exists($visa_details, 'overall_experience') ? $visa_details->overall_experience : null;
            [$india_experience_years , $india_experience_months] = str_contains($india_experience, ".") ? explode(".", $india_experience) : [null, null];
            [$overall_experience_years , $overall_experience_months] = str_contains($overall_experience, ".") ? explode(".", $overall_experience) : [null, null];
            $cv_file_path = property_exists($visa_details, 'cv_file_path') ? $visa_details->cv_file_path : null;
            $degree_file_path = property_exists($visa_details, 'degree_file_path') ? $visa_details->degree_file_path : null;
            $minimum_wage = property_exists($visa_details, 'minimum_wage') ? $visa_details->minimum_wage : null;
            $work_location = property_exists($visa_details, 'work_location') ? $visa_details->work_location : null;
            $salary_range_from = property_exists($visa_details, 'salary_range_from') ? $visa_details->salary_range_from : null;
            $salary_range_to = property_exists($visa_details, 'salary_range_to') ? $visa_details->salary_range_to : null;
            $us_job_title_id = property_exists($visa_details, 'us_job_title_id') ? $visa_details->us_job_title_id : null;
            $us_job_title = property_exists($visa_details, 'us_job_title') ? $visa_details->us_job_title : null;
            $band_detail = property_exists($visa_details, 'band_detail') ? ($visa_details->band_detail ? $visa_details->band_detail : $band_detail) : $band_detail;
            $undertaken_completed = property_exists($visa_details, 'acceptance_by_user') ? ($visa_details->acceptance_by_user===NULL? 1 : ($visa_details->acceptance_by_user===1 ? 1 :0) ) : 1;
            $us_salary = property_exists($visa_details, 'us_salary') ? $visa_details->us_salary : null;
            $one_time_bonus = property_exists($visa_details, 'one_time_bonus') ? $visa_details->one_time_bonus : null;
            $one_time_bonus_payout_date = property_exists($visa_details, 'one_time_bonus_payout_date') ? $visa_details->one_time_bonus_payout_date : null;
            $next_salary_revision_on = property_exists($visa_details, 'next_salary_revision_on') ? $visa_details->next_salary_revision_on : null;
            $us_manager_id = property_exists($visa_details, 'us_manager_id') ? $visa_details->us_manager_id : null;
            $us_manager = property_exists($visa_details, 'us_manager') ? $visa_details->us_manager : null;
            $inszoom_id = property_exists($visa_details, 'inszoom_id') ? $visa_details->inszoom_id : null;
            $entity_id = property_exists($visa_details, 'entity_id') ? $visa_details->entity_id : null;
            $entity = property_exists($visa_details, 'entity') ? $visa_details->entity : null;
            $attorneys_id = property_exists($visa_details, 'attorneys_id') ? $visa_details->attorneys_id : null;
            $attorneys = property_exists($visa_details, 'attorneys') ? $visa_details->attorneys : null;
            $petition_file_date = property_exists($visa_details, 'petition_file_date') ? $visa_details->petition_file_date : null;
            $receipt_no = property_exists($visa_details, 'receipt_no') ? $visa_details->receipt_no : null;    
            $petition_start_date = property_exists($visa_details, 'petition_start_date') ? $visa_details->petition_start_date : null;    
            $petition_end_date = property_exists($visa_details, 'petition_end_date') ? $visa_details->petition_end_date : null;    
            $petition_file_path = property_exists($visa_details, 'petition_file_path') ? $visa_details->petition_file_path : null;
            $entry_type_id = property_exists($visa_details, 'entry_type_id') ? $visa_details->entry_type_id : null;
            $entry_type = property_exists($visa_details, 'entry_type') ? $visa_details->entry_type : null;
            $visa_interview_type_id = property_exists($visa_details, 'visa_interview_type_id') ? $visa_details->visa_interview_type_id : null;
            $visa_interview_type = property_exists($visa_details, 'visa_interview_type') ? $visa_details->visa_interview_type : null;
            $visa_ofc_date = property_exists($visa_details, 'visa_ofc_date') ? $visa_details->visa_ofc_date : null;
            $visa_interview_date = property_exists($visa_details, 'visa_interview_date') ? $visa_details->visa_interview_date : null;
            $visa_status_id = property_exists($visa_details, 'visa_status_id') ? $visa_details->visa_status_id : null;
            $visa_status = property_exists($visa_details, 'visa_status') ? $visa_details->visa_status : null;
            $travel_date = property_exists($visa_details, 'travel_date') ? $visa_details->travel_date : null;
            $travel_location = property_exists($visa_details, 'travel_location') ? $visa_details->travel_location : null;
            $visa_number = property_exists($visa_details, 'visa_number') ? $visa_details->visa_number : null;
            $visa_file_path = property_exists($visa_details, 'visa_file_path') ? $visa_details->visa_file_path : null;
            $traveling_type_id = property_exists($visa_details, 'traveling_type_id') ? $visa_details->traveling_type_id : null;
            $traveling_type = property_exists($visa_details, 'traveling_type') ? $visa_details->traveling_type : null;
            $first_name = property_exists($visa_details, 'Firstname') ? $visa_details->Firstname : null;
            $green_card_title = property_exists($visa_details, 'green_card_title') ? $visa_details->green_card_title : null;
            $offer_letter_paths = property_exists($visa_details, 'offer_letter_path') ? $visa_details->offer_letter_path : null;
            $immigration_offer_letter_path = property_exists($visa_details, 'immigration_offer_letter_path') ? $visa_details->immigration_offer_letter_path : null;
            [$offer_letter_path_pdf, $offer_letter_path_word] = str_contains($offer_letter_paths, ",") ? explode(",", $offer_letter_paths) : [null, null];
            $is_file_exists = isset($offer_letter_path_pdf) ? file_exists(public_path($offer_letter_path_pdf)) : false;
            $record_number = property_exists($visa_details, 'record_number') ? $visa_details->record_number : null;
            $most_recent_doe = property_exists($visa_details, 'most_recent_doe') ? $visa_details->most_recent_doe : null;
            $admit_until = property_exists($visa_details, 'admit_until') ? $visa_details->admit_until : null;
            $gc_initiated_on = property_exists($visa_details, 'gc_initiated_on') ? $visa_details->gc_initiated_on : null;
            $job_title = property_exists($visa_details, 'job_title') ? $visa_details->job_title : null;
            $job_title_id = property_exists($visa_details, 'job_title_id') ? $visa_details->job_title_id : null;
            $visa_currency = property_exists($visa_details, 'visa_currency') ? $visa_details->visa_currency : null;
            $visa_currency_notation = property_exists($visa_details, 'visa_currency_notation') ? $visa_details->visa_currency_notation : null;
        }
        if(isset($travelling_details) && $travelling_details) {
            $origin = array_key_exists("from_country", $travelling_details) ? $travelling_details["from_country"] : null;
            $origin_name = array_key_exists("from_country_name", $travelling_details) ? $travelling_details["from_country_name"] : null;
            $to_country = array_key_exists("to_country", $travelling_details) ? $travelling_details["to_country"] : null;
            $to_country_name = array_key_exists("to_country_name", $travelling_details) ? $travelling_details["to_country_name"] : null;
            $to_city = array_key_exists("to_city", $travelling_details) ? $travelling_details["to_city"] : null;
            $to_city_name = array_key_exists("to_city_name", $travelling_details) ? $travelling_details["to_city_name"] : null;
            $from_date = array_key_exists("from_date", $travelling_details) ? $travelling_details["from_date"] : null;
            $to_date = array_key_exists("to_date", $travelling_details) ? $travelling_details["to_date"] : null;
        }
        if(isset($proof_details)){
            $proof_details = json_decode(json_encode($proof_details),true);
        }
        if(isset($offer_letter_details)) {
            $offer_letter_template = array_key_exists('offer_letter_template', $offer_letter_details) ? $offer_letter_details['offer_letter_template'] : null;
            $hr_admin_aceid = array_key_exists('hr_admin_aceid', $offer_letter_details) ? $offer_letter_details['hr_admin_aceid'] : null;
            $hr_admin_name = array_key_exists('hr_admin_name', $offer_letter_details) ? $offer_letter_details['hr_admin_name'] : null;
            $hr_admin_designation = array_key_exists('hr_admin_designation', $offer_letter_details) ? $offer_letter_details['hr_admin_designation'] : null;
            $hr_admin_department = array_key_exists('hr_admin_department', $offer_letter_details) ? $offer_letter_details['hr_admin_department'] : null;
            $hr_admin_mail = array_key_exists('hr_admin_mail', $offer_letter_details) ? $offer_letter_details['hr_admin_mail'] : null;
            $signature_location = array_key_exists('signature_location', $offer_letter_details) ? $offer_letter_details['signature_location'] : null;
        }
    }

    

        // config variables
        $config_variables=new App\Http\Controllers\ConfigController;
        $billable_edit=0;
        $remove_billable_access = [];
        $intial_billble_choose_access=array_diff($config_variables->CONFIG['BILLABLE_CHOOSE_ACCESS'], $remove_billable_access);
        if(isset($edit_id)&&Auth()->User()->has_any_role_code($config_variables->CONFIG['BILLABLE_CHOOSE_ACCESS'])&&in_array($request_details->status_id,$config_variables->CONFIG['BILLABLE_ENABLED_STATUS'])){
            if(Auth::User()->has_any_role_code(['BF_REV'])&&$request_details->status_id=="STAT_05")
                $billable_edit=1;
            else if(property_exists($request_details,'billed_to_client')&&is_null($request_details->billed_to_client)&&!Auth::User()->has_any_role_code(['BF_REV']))
                $billable_edit=1;
            else if(property_exists($request_details,'billed_to_client')&&$request_details->created_by==Auth::User()->aceid&&$request_details->status_id=='STAT_01'&&Auth()->User()->has_any_role_code($intial_billble_choose_access))
                $billable_edit=1;
        }
        else if(!isset($edit_id)&&Auth()->User()->has_any_role_code($intial_billble_choose_access))
            $billable_edit=1;

        $comments_enable_actions=$config_variables->CONFIG['COMMENTS_ENABLE_FOR_ROLES'];
        $remarks = isset($status_details) ? $status_details['remarks'] : null;
        // dd($remarks);
        $reject_message = array_key_exists('reject_message', $info_messages) ? $info_messages['reject_message'] : [];
        $waiting_message = array_key_exists('waiting_message', $info_messages) ? $info_messages['waiting_message'] : [];
        $completed_messages = array_key_exists('completed_messages', $info_messages) ? $info_messages['completed_messages'] : [];

        $is_td_visible=1;
        $review_statuses = ['STAT_02','STAT_12','STAT_29', 'STAT_31', 'STAT_33', 'STAT_37', 'STAT_38'];
    if(isset($edit_id)&&Auth::User()->has_any_role_code(['AN_COST_FIN','AN_COST_VISA','AN_COST_FAC']) && (in_array($status_id,$review_statuses))){
        $dp_obj=new \App\Http\Controllers\DetailsProvider();
        $respective_team_anticipate=$dp_obj->get_travel_desk_user_details($request_details->travel_request_id,['AN_COST_FIN','AN_COST_VISA','AN_COST_FAC'],$user_id=null);
        if(!in_array(Auth::User()->aceid,$respective_team_anticipate))
            $is_td_visible=0;
    }
    $related_travel_request = null;
    if(isset($related_travel_ids) && is_array($related_travel_ids) && count($related_travel_ids) ) {
        $related_travel_request = array_map(fn($e) => "<a href=$e->url target='_blank'>$e->request_id</a>", $related_travel_ids);
        $related_travel_request = implode(', ', $related_travel_request);
    }
@endphp

@section('title', 'Travel system')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js" integrity="sha512-u3fPA7V8qQmhBPNT5quvaXVa1mnnLSXUep5PS1qo5NRzHwG19aHmNJnj1Q8hpA/nBWZtZD4r4AX6YOt5ynLN2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="{{ URL::asset('css/jquery-confirm.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('css/pdfconvert.css') }}">
<script src="{{ URL::asset('js/config.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/html2canvas.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jsPDF.umd.min.js')}}"></script>
<script type="text/javascript" src="{{ URL::asset('js/dompurify.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/pdfconvert.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/docxtemplater.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/pizzip.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/pizzip-utils.js') }}"></script>
<script src="{{ URL::asset('js/jquery-confirm.min.js') }}"></script>
<script src="{{ URL::asset('js/confirmation-box.js') }}"></script>
<script src="{{ URL::asset('js/visa_action.js') }}"></script>
<script src="{{ URL::asset('js/visa_request.js') }}"></script>
<script src="{{ URL::asset('js/mobile-responsive.js') }}"></script>
@if($salary_range_edit_access)
    <script src="{{ URL::asset('js/visa_salary_range_edit.js') }}"></script>
@endif
<div class="container-fluid visa-request-container">
    <input type="hidden" id="edit_id" name="edit_id", value="{{ $edit_id }}" />  
    <input type="hidden" id="hidden_request_for" value="{{ $request_for_code }}" />
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 title-section">
                <h3>
                    Visa request
                    @if(isset($edit_id))
                        <span style="color:#2E4496"> - {{ $request_code }} ({{ $status }})</span>
                    @endif
                </h3>
                <div class="special-action-btn" style="display:flex; gap: 5px;">
                    @if(isset($related_travel_ids))
                        <div class="col">
                            <label for="">Related travel id</label>
                            <p>{!! $related_travel_request !!}</p>
                        </div>
                    @endif
                    <img id="visa_page_view_btn" value="list_view" src="/images/list-view.svg" alt="List view" />
                    @if(in_array($status_id,['STAT_14','STAT_12']) && $travaler_id == Auth::User()->aceid)
                        <button class="primary-button" id="travel_link" value="{{"/travel_link/$edit_id"}}">Create travel request</button>
                    @endif
                    @if(isset($status_details) && count($status_details) && $status_id != 'STAT_01')
                        <button type="button" class="primary-button" id="status-tracker-btn" data-toggle="modal" data-target="#statusTrackerModal">Status tracker</button>
                        @include('layouts.travel_status_tracker')
                    @endif
                    @if($status_id == 'STAT_28' && $travaler_id == Auth::User()->aceid)
                        <button type="button" class="primary-button" id="need_assistance_btn" value="need_assistance">Need assistance</button>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div id="action_prevent_error" class='col-md-12'></div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <ul id="progressbar">
                    @if ( in_array( 'initiation_tab', $visible_tabs ) || !isset($status_id) || $status_id=="STAT_01")
                        @if((!isset($status_id) || $status_id=="STAT_01") && in_array( 'initiation_tab', $visible_tabs ))
                        <li id="initiation_tab" class="active {{$tab_classes['initiation_tab']}} common_tab short_term long_term longh1b">Initiation</li> 
                        @elseif(!isset($status_id) || $status_id=="STAT_01")  
                        <li id="initiation_tab" class="common_tab short_term long_term longh1b" style="display:none;">Initiation</li>  
                        @else
                        <li id="initiation_tab" class="active {{$tab_classes['initiation_tab']}}">Initiation</li> 
                        @endif

                    @endif
                    @if ( in_array( 'gm_review_tab', $visible_tabs ) || !isset($status_id) || $status_id=="STAT_01" )
                        @if((!isset($status_id) || $status_id=="STAT_01") && in_array( 'gm_review_tab', $visible_tabs ))
                        <li id="gm_review_tab" class="{{$tab_classes['gm_review_tab']}} common_tab long_term longh1b">Immigration team - review</li>
                        @elseif(!isset($status_id) || $status_id=="STAT_01")
                        <li id="gm_review_tab" class="common_tab long_term longh1b" style="display:none;">Immigration team - review</li>
                        @else
                        <li id="gm_review_tab" class="{{$tab_classes['gm_review_tab']}}">Immigration team - review</li>
                        @endif

                    @endif
                    @if ( in_array( 'gm_review_approval_tab', $visible_tabs ) || !isset($status_id) || $status_id=="STAT_01" )
                        @if((!isset($status_id) || $status_id=="STAT_01") && in_array( 'gm_review_approval_tab', $visible_tabs ))
                        <li id="gm_review_approval_tab" class="{{$tab_classes['gm_review_approval_tab']}} common_tab short_term">Immigration team - review</li>
                        @elseif(!$status_id || $status_id=="STAT_01")
                        <li id="gm_review_approval_tab" class="common_tab short_term" style="display:none;">Immigration team - review</li>
                        @else
                        <li id="gm_review_approval_tab" class="{{$tab_classes['gm_review_approval_tab']}}">Immigration team - review</li>
                        @endif


                    @endif 
                    @if ( in_array( 'approval_tab', $visible_tabs ) || !isset($status_id) || $status_id=="STAT_01" )
                        @if((!isset($status_id) || $status_id=="STAT_01") && in_array( 'approval_tab', $visible_tabs ))
                        <li id="approval_tab" class="{{ $tab_classes['approval_tab'] }} common_tab long_term">Approvals</li>
                        @elseif(!isset($status_id) || $status_id=="STAT_01")
                        <li id="approval_tab" class="common_tab long_term" style="display:none;">Approvals</li>
                        @else
                        <li id="approval_tab" class="{{ $tab_classes['approval_tab'] }}">Approvals</li>
                        @endif


                    @endif
                    @if ( in_array( 'hr_review_tab', $visible_tabs ) || !isset($status_id) || $status_id=="STAT_01" )
                        @if((!isset($status_id) || $status_id=="STAT_01") && in_array( 'approval_tab', $visible_tabs ))
                        <li id="hr_review_tab" class="{{$tab_classes['hr_review_tab']}} common_tab long_term longh1b">Salary discussion</li>
                        @elseif(!isset($status_id) || $status_id=="STAT_01")
                        <li id="hr_review_tab" class="common_tab long_term longh1b" style="display:none;">Salary discussion</li>
                        @else
                        <li id="hr_review_tab" class="{{$tab_classes['hr_review_tab']}}">Salary discussion</li>
                        @endif

                    @endif
                    @if ( in_array( 'petition_process_tab', $visible_tabs ) || !isset($status_id) || $status_id=="STAT_01" )
                        @if((!isset($status_id) || $status_id=="STAT_01") && in_array( 'petition_process_tab', $visible_tabs ))
                        <li id="petition_process_tab" class="{{ $tab_classes['petition_process_tab'] }} common_tab long_term longh1b">Petition process</li>
                        @elseif(!isset($status_id) || $status_id=="STAT_01")
                        <li id="petition_process_tab" class="common_tab long_term longh1b" style="display:none;">Petition process</li>
                        @else
                        <li id="petition_process_tab" class={{ $tab_classes['petition_process_tab'] }}>Petition process</li>
                        @endif
                        
                    @endif
                    @if ( in_array( 'visa_stamping_tab', $visible_tabs ) || !isset($status_id) || $status_id=="STAT_01" )
                        @if((!isset($status_id) || $status_id=="STAT_01") && in_array( 'visa_stamping_tab', $visible_tabs ))
                        <li id="visa_stamping_tab" class="{{ $tab_classes['visa_stamping_tab'] }} common_tab short_term long_term longh1b">Visa stamping</li>
                        @elseif(!isset($status_id) || $status_id=="STAT_01")
                        <li id="visa_stamping_tab" class="common_tab short_term long_term longh1b" style="display:none;">Visa stamping</li>
                        @else
                        <li id="visa_stamping_tab" class="{{ $tab_classes['visa_stamping_tab'] }}">Visa stamping</li>
                        @endif

                    @endif
                    @if ( in_array( 'completed_tab', $visible_tabs ) || !isset($status_id) || $status_id=="STAT_01" )
                        @if((!isset($status_id) || $status_id=="STAT_01") && in_array( 'completed_tab', $visible_tabs ))
                        <li id="completed_tab" class="{{ $tab_classes['completed_tab'] }} common_tab long_term longh1b">Completed</li>
                        @elseif(!isset($status_id) || $status_id=="STAT_01")
                        <li id="completed_tab" class="common_tab long_term longh1b" style="display:none;">Completed</li>
                        @else
                        <li id="completed_tab" class="{{ $tab_classes['completed_tab'] }}">Completed</li>
                        @endif

                    @endif
                </ul>
            </div>
        </div>
        <div class="form-section">
            @include('layouts.visa_request.visa_details')
            <div class="row initiation_tab_header visa_page_view_header" style="display:none"><div class='col-md-3'><h3>Personal info</h3></div></div>
            @include('layouts.visa_request.personal_info')
            <div class="row gm_review_tab_header visa_page_view_header" style="display:none"><div class='col-md-3'><h3>Immigration team - review</h3></div></div>
            @include('layouts.visa_request.gm_review_form')
            <div class="row approval_tab_header visa_page_view_header" style="display:none"><div class='col-md-3'><h3>Approvals</h3></div></div>
            @include('layouts.visa_request.approval_section')
            <div class="row hr_review_tab_header visa_page_view_header" style="display:none"><div class='col-md-3'><h3>Salary discussion</h3></div></div>
            @include('layouts.visa_request.hr_review_form')
            <div class="row petition_process_tab_header visa_page_view_header" style="display:none"><div class='col-md-3'><h3>Petition process</h3></div></div>
            @include('layouts.visa_request.petition_process')
            <div class="row visa_stamping_tab_header visa_page_view_header" style="display:none"><div class='col-md-3'><h3>Visa stamping</h3></div></div>
            @include('layouts.visa_request.visa_entry')
            @include('layouts.visa_request.visa_process_offer_letter')
            @include('layouts.visa_request.visa_process')
            @include('layouts.visa_request.billable_section')
        </div>
    </div>
    @include('layouts.visa_request.form_buttons')
    @if(isset($approval_flow) && $approval_flow)
        <div class="form-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h3>approval_flow</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid form-content">
            <div class="col-md-12">
                @foreach($approval_flow as $flow)
                    <div class="col-md-3"><span style="color:var(--is-purple)">{{$flow->user_role}}</span> : {{$flow->username}}</div>
                @endforeach
                @if(isset($hr_partner))
                    <div class="col-md-3"><span style="color:var(--is-purple)">HR partner</span> : {{ $hr_partner }}</div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
