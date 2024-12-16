@extends('header')
<?php //dd($visible_fields) ?>
@section('title', 'Travel Request')
@php $page_navigation_no=1.1; $tab_navigation="us_visa_process";@endphp
<link type="text/css" href="{{asset('/css/visa_process.css')}}" rel="stylesheet">
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" rel="stylesheet">
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/visa_process.js') }}"></script>
<!-- <script type="text/javascript" src="{{ URL::asset('js/html2canvas.js') }}"></script> -->
<script type="text/javascript" src="{{ URL::asset('js/html2canvas.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jsPDF.umd.min.js')}}"></script>
<script type="text/javascript" src="{{ URL::asset('js/dompurify.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/pdfconvert.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/docxtemplater.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/pizzip.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/pizzip-utils.js') }}"></script>
<script type="text/javascript" src="{{URL::asset('js/visa_file_upload.js')}}"></script>
<div class="ui-wait" style="display: none;">
  <img src="/images/spin-2-loading.gif" class = "loading-gif">
</div>


<div class="visa_process_detail">
    <div class="container-fluid" id="visa-process-detail">
@if(isset($error))
<!-- To print the error incase if there is any error -->
        <div class="tab-content">
            <div class="row" style="margin-top:155px;">
            <h3 style='font-size:12px;font-family: mont-semibold;color:red;margin: top 155px;'>{{$error}}</h3>
</div></div>
<!-- Error end -->
@else
<!-- To validate and assign the server variables -->
@php
if(isset($saved_request_details)){
    $employee_value=isset($saved_request_details)?(property_exists($saved_request_details,'employee_aceid')?$saved_request_details->employee_aceid:''):'';
    $employee_text=isset($saved_request_details)?(property_exists($saved_request_details,'employee_name')?$saved_request_details->employee_name:''):'';
    $employee_first_name=isset($employee_value)?DB::table('users')->where('aceid',$employee_value)->value('firstname'):'';
    $visa_type_id=isset($saved_request_details)?(property_exists($saved_request_details,'visa_type_id')?$saved_request_details->visa_type_id:''):'';
    $request_type_id=isset($saved_request_details)?(property_exists($saved_request_details,'request_type_id')?$saved_request_details->request_type_id:''):'';
    $client_code=isset($saved_request_details)?(property_exists($saved_request_details,'client_code')?$saved_request_details->client_code:''):'';
    $petition_id=isset($saved_request_details)?(property_exists($saved_request_details,'petition_id')?$saved_request_details->petition_id:''):'';
    $hr_remarks=isset($saved_request_details)?(property_exists($saved_request_details,'hr_remarks')?$saved_request_details->hr_remarks:''):'';
    $employee_mail=isset($saved_request_details)?(property_exists($saved_request_details,'employee_mail')?$saved_request_details->employee_mail:'-'):'-';
    $employee_manager_id=isset($saved_request_details)?(property_exists($saved_request_details,'employee_manager_id')?$saved_request_details->employee_manager_id:''):'';
    $employee_dept_id=isset($saved_request_details)?(property_exists($saved_request_details,'employee_dept_id')?$saved_request_details->employee_dept_id:''):'';
    $practice = isset($saved_request_details)?(property_exists($saved_request_details,'practice')?$saved_request_details->practice:"NA"):"NA";
    $employee_name=isset($saved_request_details)?(property_exists($saved_request_details,'employee_name')?$saved_request_details->employee_name:''):'';
    $visa_type_name=isset($saved_request_details)?(property_exists($saved_request_details,'visa_type')?$saved_request_details->visa_type:''):'';
    $request_type_name=isset($saved_request_details)?(property_exists($saved_request_details,'request_type')?$saved_request_details->request_type:''):'';
    $client_name=isset($saved_request_details)?(property_exists($saved_request_details,'client_name')?$saved_request_details->client_name:''):'';
    $petition_name=isset($saved_request_details)?(property_exists($saved_request_details,'petition_name')?$saved_request_details->petition_name:''):'';
    $employee_dept=isset($saved_request_details)?(property_exists($saved_request_details,'employee_dept')?$saved_request_details->employee_dept:''):'';
    $manager_name=isset($saved_request_details)?(property_exists($saved_request_details,'manager_name')?$saved_request_details->manager_name:''):'';
    $gender=isset($saved_request_details)?(property_exists($saved_request_details,'gender')?$saved_request_details->gender:''):'';
    $gender_id=isset($saved_request_details)?(property_exists($saved_request_details,'gender_id')?$saved_request_details->gender_id:''):'';
    $first_name=isset($saved_request_details)?(property_exists($saved_request_details,'first_name')?$saved_request_details->first_name:''):'';
    $last_name=isset($saved_request_details)?(property_exists($saved_request_details,'last_name')?$saved_request_details->last_name:''):'';
    $dob=property_exists($saved_request_details,'dob')?($saved_request_details->dob?$saved_request_details->dob:''):'';
    $doj=property_exists($saved_request_details,'doj')?($saved_request_details->doj?$saved_request_details->doj:''):'';
    $address=property_exists($saved_request_details,'address')?($saved_request_details->address?$saved_request_details->address:''):'';
    $passport_no=property_exists($saved_request_details,'passport_no')?($saved_request_details->passport_no?$saved_request_details->passport_no:''):'';
    $education_details_id=isset($saved_request_details)?(property_exists($saved_request_details,'education_details_id')?$saved_request_details->education_details_id:''):'';
    $education_category_id=isset($saved_request_details)?(property_exists($saved_request_details,'education_category_id')?$saved_request_details->education_category_id:''):'';
    $education=property_exists($saved_request_details,'education')?($saved_request_details->education?$saved_request_details->education:''):'';
    $employee_remarks=property_exists($saved_request_details,'employee_remarks')?($saved_request_details->employee_remarks?$saved_request_details->employee_remarks:''):'';
    $education_category=property_exists($saved_request_details,'education_category')?($saved_request_details->education?$saved_request_details->education_category:''):'';
    $minimum_wage=property_exists($saved_request_details,'minimum_wage')?($saved_request_details->minimum_wage?$saved_request_details->minimum_wage:''):'';
    $work_location=property_exists($saved_request_details,'work_location')?($saved_request_details->work_location?$saved_request_details->work_location:''):'';
    $job_title_id=property_exists($saved_request_details,'job_titile_id')?($saved_request_details->job_titile_id?$saved_request_details->job_titile_id:''):'';
    $filing_type_id=property_exists($saved_request_details,'filing_type_id')?($saved_request_details->filing_type_id?$saved_request_details->filing_type_id:''):'';
    $job_title=property_exists($saved_request_details,'job_title')?($saved_request_details->job_title?$saved_request_details->job_title:''):'';
    $filing_type=property_exists($saved_request_details,'filing_type')?($saved_request_details->filing_type?$saved_request_details->filing_type:''):'';
    $gm_remarks=property_exists($saved_request_details,'gm_remarks')?($saved_request_details->gm_remarks?$saved_request_details->gm_remarks:''):'';
    // changes made by dinakar on 18th Nov 2022
    $salary_range_from=property_exists($saved_request_details,'salary_range_from')?($saved_request_details->salary_range_from?$saved_request_details->salary_range_from:''):'';
    $salary_range_to=property_exists($saved_request_details,'salary_range_to')?($saved_request_details->salary_range_to?$saved_request_details->salary_range_to:''):'';
    $us_job_title=property_exists($saved_request_details,'us_job_title')?($saved_request_details->us_job_title?$saved_request_details->us_job_title:''):'';
    $us_job_title_id=property_exists($saved_request_details,'us_job_title_id')?($saved_request_details->us_job_title_id?$saved_request_details->us_job_title_id:''):'';
    $band_detail=property_exists($saved_request_details,'band_detail')?($saved_request_details->band_detail?$saved_request_details->band_detail:'-'):'-';

    $india_experience=property_exists($saved_request_details,'india_experience')?($saved_request_details->india_experience?$saved_request_details->india_experience:''):'';
    $overall_experience=property_exists($saved_request_details,'overall_experience')?($saved_request_details->overall_experience?$saved_request_details->overall_experience:''):'';
    $ind_exp_year='';$ind_exp_month='';$overall_exp_year='';$overall_exp_month='';
    $ind_exp_year_in_num='';$ind_exp_month_in_num='';$overall_exp_year_in_num='';$overall_exp_month_in_num='';
    if(property_exists($saved_request_details,'india_experience')&&$saved_request_details->india_experience){
      $india_experience_split=explode('.',$saved_request_details->india_experience);
      if($india_experience_split[0]){
        $ind_exp_year=$india_experience_split[0]." Year(s) ";
        $ind_exp_year_in_num=$india_experience_split[0];
      }
      if($india_experience_split[0] == '0')
      {
        $ind_exp_year_in_num=$india_experience_split[0];
      }
      if($india_experience_split[1]){
        $ind_exp_month=$india_experience_split[1]." Month(s) ";
        $ind_exp_month_in_num=$india_experience_split[1];
      }
      if($india_experience_split[1] == '0')
      {
        $ind_exp_month_in_num=$india_experience_split[1];
      }
    }
    if(property_exists($saved_request_details,'overall_experience')&&$saved_request_details->overall_experience){
      $overall_experience_split=explode('.',$saved_request_details->overall_experience) ; 
      if($overall_experience_split[0]){
        $overall_exp_year=$overall_experience_split[0]." Year(s) ";
        $overall_exp_year_in_num=$overall_experience_split[0];
      }
      if($overall_experience_split[0] == '0'){
        $overall_exp_year_in_num=$overall_experience_split[0];
      }
      if($overall_experience_split[1]){
        $overall_exp_month=$overall_experience_split[1]." Month(s) ";
        $overall_exp_month_in_num=$overall_experience_split[1];
      }
      if($overall_experience_split[1] == '0')
      {
        $overall_exp_month_in_num=$overall_experience_split[1];
      }
    }
    $acceptance_by_user=property_exists($saved_request_details,'acceptance_by_user')?($saved_request_details->acceptance_by_user?1:0):0;
    $us_salary=property_exists($saved_request_details,'us_salary')?($saved_request_details->us_salary?$saved_request_details->us_salary:''):'';
    $one_time_bonus=property_exists($saved_request_details,'one_time_bonus')?($saved_request_details->one_time_bonus?$saved_request_details->one_time_bonus:''):'';
    $one_time_bonus_payout_date=property_exists($saved_request_details,'one_time_bonus_payout_date')?($saved_request_details->one_time_bonus_payout_date?$saved_request_details->one_time_bonus_payout_date:''):'';
    $next_salary_revision_on=property_exists($saved_request_details,'next_salary_revision_on')?($saved_request_details->next_salary_revision_on?$saved_request_details->next_salary_revision_on:''):'';
    $us_manager_id=property_exists($saved_request_details,'us_manager_id')?($saved_request_details->us_manager_id?$saved_request_details->us_manager_id:''):'';
    $inszoom_id=property_exists($saved_request_details,'inszoom_id')?($saved_request_details->inszoom_id?$saved_request_details->inszoom_id:''):'';
    $entity_id=property_exists($saved_request_details,'entity_id')?($saved_request_details->entity_id?$saved_request_details->entity_id:''):'';
    $attorneys_id=property_exists($saved_request_details,'attorneys_id')?($saved_request_details->attorneys_id?$saved_request_details->attorneys_id:''):'';
    $petition_file_date=property_exists($saved_request_details,'petition_file_date')?($saved_request_details->petition_file_date?$saved_request_details->petition_file_date:''):'';
    $receipt_no=property_exists($saved_request_details,'receipt_no')?($saved_request_details->receipt_no?$saved_request_details->receipt_no:''):'';
    $petition_start_date=property_exists($saved_request_details,'petition_start_date')?($saved_request_details->petition_start_date?$saved_request_details->petition_start_date:''):'';
    $petition_end_date=property_exists($saved_request_details,'petition_end_date')?($saved_request_details->petition_end_date?$saved_request_details->petition_end_date:''):'';
    $visa_interview_type_id=property_exists($saved_request_details,'visa_interview_type_id')?($saved_request_details->visa_interview_type_id?$saved_request_details->visa_interview_type_id:''):'';
    $visa_interview_type=property_exists($saved_request_details,'visa_interview_type')?($saved_request_details->visa_interview_type?$saved_request_details->visa_interview_type:''):'';
    $visa_ofc_date=property_exists($saved_request_details,'visa_ofc_date')?($saved_request_details->visa_ofc_date?$saved_request_details->visa_ofc_date:''):'';
    $visa_interview_date=property_exists($saved_request_details,'visa_interview_date')?($saved_request_details->visa_interview_date?$saved_request_details->visa_interview_date:''):'';
    $visa_status_id=property_exists($saved_request_details,'visa_status_id')?($saved_request_details->visa_status_id?$saved_request_details->visa_status_id:''):'';
    $visa_status=property_exists($saved_request_details,'visa_status')?($saved_request_details->visa_status?$saved_request_details->visa_status:''):'';
    $travel_date=property_exists($saved_request_details,'travel_date')?($saved_request_details->travel_date?$saved_request_details->travel_date:''):'';
    $travel_location=property_exists($saved_request_details,'travel_location')?($saved_request_details->travel_location?$saved_request_details->travel_location:''):'';
    $traveling_type_id=property_exists($saved_request_details,'traveling_type_id')?($saved_request_details->traveling_type_id?$saved_request_details->traveling_type_id:''):'';
    $traveling_type=property_exists($saved_request_details,'traveling_type')?($saved_request_details->traveling_type?$saved_request_details->traveling_type:''):'';
    $i_94_record_number=property_exists($saved_request_details,'record_number')?($saved_request_details->record_number?$saved_request_details->record_number:''):'';
    $most_recent_date_of_entry=property_exists($saved_request_details,'most_recent_doe')?($saved_request_details->most_recent_doe?$saved_request_details->most_recent_doe:''):'';
    $admit_until_date=property_exists($saved_request_details,'admit_until')?($saved_request_details->admit_until?$saved_request_details->admit_until:''):'';
    $gc_to_be_initiated_on=property_exists($saved_request_details,'gc_initiated_on')?($saved_request_details->gc_initiated_on?$saved_request_details->gc_initiated_on:''):'';
    $is_file_exists = file_exists(public_path("offer_letter/".$edit_id."_".$employee_value."_offer_letter.pdf"));
    $offer_letter_file_name = $is_file_exists ? $edit_id."_".$employee_value."_offer_letter.pdf":'';
    $immigration_offer_letter_file_name = $is_file_exists ? $edit_id."_".$employee_value."_immigration_offer_letter.pdf":'';
    $word_document_file_name = $is_file_exists ? $edit_id."_".$employee_value."_offer_letter.docx":'';
    $offer_letter_label = $is_file_exists?"Offer letter":"";
    $immigration_offer_letter_label = $is_file_exists?"Immigration offer letter":'';
    $word_document_label = $is_file_exists?"Word document":"";
    $green_card_title = property_exists($saved_request_details,'green_card_title')?($saved_request_details->green_card_title?1:0):0;
    $status = DB::table('visa_process_request_details as vprd')->join('visa_process_status as vps','vprd.status_id','=','vps.id')->where('vprd.request_code','=',$edit_id)->value('vps.name');

    $offer_letter_path = property_exists($saved_request_details,'offer_letter_path')?($saved_request_details->offer_letter_path?explode(",",$saved_request_details->offer_letter_path):""):"";
    $word_document_path = isset($offer_letter_path) && is_array($offer_letter_path)?($offer_letter_path[1]??""):"";
    $offer_letter_path = isset($offer_letter_path) && is_array($offer_letter_path)?$offer_letter_path[0]??"":"";
    $immigration_offer_letter_path = property_exists($saved_request_details,'immigration_offer_letter_path')?($saved_request_details->immigration_offer_letter_path?$saved_request_details->immigration_offer_letter_path:""):"";
    $offer_letter_path = $offer_letter_path?$offer_letter_path:($is_file_exists?"../../offer_letter/".$offer_letter_file_name:"");
    $immigration_offer_letter_path = $immigration_offer_letter_path?$immigration_offer_letter_path:($is_file_exists?"../../offer_letter/".$immigration_offer_letter_file_name:"");
    $word_document_path = $word_document_path?$word_document_path:($is_file_exists?"../../offer_letter/".$word_document_file_name:"");
    $hr_review_remarks = isset($hr_review_remarks)?$hr_review_remarks:"";
    $created_at = property_exists($saved_request_details,'created_at')?($saved_request_details->created_at?$saved_request_details->created_at:""):"";
    $currency_notation = property_exists($saved_request_details,'currency_notation')?($saved_request_details->currency_notation?$saved_request_details->currency_notation:""):"";

    $us_manager_aceid = property_exists($saved_request_details,'us_manager_aceid')?($saved_request_details->us_manager_aceid?$saved_request_details->us_manager_aceid:""):"";
    $petitioner_entity_id = property_exists($saved_request_details,'petitioner_entity_id')?($saved_request_details->petitioner_entity_id?$saved_request_details->petitioner_entity_id:""):"";
    $visa_attorneys_id = property_exists($saved_request_details,'visa_attorneys_id')?($saved_request_details->visa_attorneys_id?$saved_request_details->visa_attorneys_id:""):"";
    $status_crossed=[];
    if(isset($saved_request_details)&&property_exists($saved_request_details,'process_request_id'))
    {
        $status_crossed=DB::table('visa_process_status_tracker')->where('process_request_id',$saved_request_details->process_request_id)->pluck('new_status')->toArray();
        $status_crossed=array_unique($status_crossed);
    }

    //Added for file details...
    $file_count = [];
    foreach($visa_process_file_details as $key => $value){
      $$key = $value;
      $file_count[$key] = is_array($value) ? count($value) : 0;
    }
    $passport_file_count = array_key_exists('passport_file_details',$file_count) ? $file_count['passport_file_details'] : 0;
    $passport_file_name = $passport_file_count != 0 ? ( $passport_file_count == 1 ? $passport_file_details[0]['originalName'] : $passport_file_count." files uploaded") : "";
    $cv_file_count = array_key_exists('cv_file_details',$file_count) ? $file_count['cv_file_details'] : 0;
    $cv_file_name = $cv_file_count != 0 ? ( $cv_file_count == 1 ? $cv_file_details[0]['originalName'] : $cv_file_count." files uploaded") : "";
    $degree_file_count = array_key_exists('degree_file_details',$file_count) ? $file_count['degree_file_details'] : 0;
    $degree_file_name = $degree_file_count != 0 ? ( $degree_file_count == 1 ? $degree_file_details[0]['originalName'] : $degree_file_count." files uploaded" ) : "";
    $petition_file_count = array_key_exists('petition_file_details',$file_count) ? $file_count['petition_file_details'] : 0;
    $petition_file_name = $petition_file_count != 0 ? ( $petition_file_count == 1 ? $petition_file_details[0]['originalName'] : $petition_file_count." files uploaded" ) : "";
    $visa_file_count = array_key_exists('visa_file_details',$file_count) ? $file_count['visa_file_details'] : 0;
    $visa_file_name = $visa_file_count != 0 ? ( $visa_file_count == 1 ? $visa_file_details[0]['originalName'] : $visa_file_count." files uploaded" ) : "";
}

@endphp
<!-- Header start -->
<div class="tab-content">
    <div class="row request-row" style="margin-top:155px;">
        <!-- <div class="alert alert-danger resultDiv" style="display:none;"><span class='close-btn'>x</span></div>
        <div class="alert alert-success resultDiv" style="display:none;"><span class='close-btn'>x</span></div> -->
        <div class="col-md-6 col-sm-6 col-xs-12 card-header">
            <h3 style="font-size:12px;font-family: mont-semibold;">US Visa request
                @if(isset($edit_id)&&$edit_id)
                <span style="color:#2E4496">  - {{$edit_id}} </span>
                @endif
        </h3>
        </div>
        @if(count($status_details) && end($status_details)['new_status']!=1)
          <div class="col-md-6 col-sm-6 col-xs-12 status-tracker-button">
            <button type="button" class="primary-button status_bar_btn" data-toggle="modal" data-target="#status_popup" id="status-tracker-button">
              <span></span>
              Status tracker
            </button>
            <span id="mb-status-tracker-button" class="status_bar_btn" data-toggle="modal" data-target="#status_popup">
              <img src="{{asset('images/message-circle.svg')}}" alt="" data-toggle="tooltip" data-placement="top" data-original-title="Status tracker">
            </span>
          </div>
        @endif
    </div>
</div>
<!-- Header end -->
<!-- Navigation start -->
<ul id="progressbar">
    <li class="active current" id="initiation_tab">Initiation</li>
    <li class="<?php echo in_array(2,$status_crossed)?'active':''?>" id="personal_info_tab">Personal info</li>
    <li class="<?php echo in_array(3,$status_crossed)?'active':''?>" id="gm_review_tab">Immigration team   - Review</li>
    <li class="<?php echo (in_array(4,$status_crossed)||in_array(5,$status_crossed))?'active':''?>" id="hr_review_tab">
        Salary discussion
    </li>
    <li class="<?php echo in_array(6,$status_crossed)?'active':''?>" id="petition_process_tab">Petiton process</li>
    <li class="<?php echo in_array(7,$status_crossed)?'active':''?>" id="visa_stamping_tab">Visa stamping</li>
    <li class="<?php echo in_array(11,$status_crossed)?'active':''?>" id="completed_tab">Completed</li>
</ul>
<!-- Navigation end -->
<!-- End veriables assign -->

    @include('layouts.visa_process.hr_form')
    @include('layouts.visa_process.employee_form')
    @include('layouts.visa_process.gm_review_form')
    @include('layouts.visa_process.hr_review_form') <!-- changes made by dinakar on 18th Nov 2022 -->
    @include('layouts.visa_process.petition_process_form')
    @include('layouts.visa_process.visa_stamping_form')
    @include('layouts.visa_process.gm_tracking_form')
    @include('layouts.visa_process.form_buttons')
    @include('layouts.visa_process.status')
    @include('layouts.visa_process.visa_process_offer_letter')
@endif
</div>
</div>
@endsection
