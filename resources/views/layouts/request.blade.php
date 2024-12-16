@extends('header')
@php
	$page_navigation_no=$module == "MOD_03" ? 'visa_request' : 'travel_request';
@endphp
@section('title', 'Travel system')
@section('content')

@php
    $travel_request_for_list = [''=>'Select','MOD_01'=>'Domestic','MOD_02'=>'International'];
    $travel_request_for_attr = ['disabled'=>false];
    $visa_request_for_code = ['RF_08'=>'RF_05','RF_12'=>'RF_05','RF_13'=>'RF_05','RF_14'=>'RF_06'];
    if($module == "MOD_03")
    {
        $travel_request_for_list = [''=>'Select', 'MOD_03'=>'Visa'];
        $travel_request_for_attr['disabled'] = true;
    }
    $visible_fields = array_key_exists('visible_fields', $field_details) ? $field_details['visible_fields'] : [];
    $editable_fields = array_key_exists('editable_fields', $field_details) ? $field_details['editable_fields'] : [];
    $field_attr =array_key_exists('field_attr', $field_details) ? $field_details['field_attr'] : [];
    $options =array_key_exists('select_options', $field_details) ? $field_details['select_options'] : [];
    $behalf_of_users = array_key_exists('behalf_of', $options) ? ['' => 'Select'] + $options['behalf_of'] : ['' => 'Select'];
    $behalf_of_section_class = 'behalf_of_section';

    $field_name=["INP_062","INP_001","INP_063","INP_007"];
    if($module != 'MOD_03')
    $module ='';
    $request_for_value ='';
    $request_for_code='';
    $status_id = '';
    $status = '';
    $travaler_id = null;
    $traveler_name = "";
    if(isset($edit_id))
    {
        if(isset($request_details))
        {
            $module = property_exists($request_details, 'module') ? $request_details->module : '';
            $request_for_value = property_exists($request_details, 'request_for') ? $request_details->request_for : '';
            $request_for_code = property_exists($request_details, 'request_for_code') ? $request_details->request_for_code : '';
            $status_id = isset($request_details) && $request_details ? (property_exists($request_details, 'status_id') ? $request_details->status_id : null): null;
            $status = DB::table('trd_status')->where([['unique_key', $status_id],['active',1]])->value('name') ?? null;
            $travaler_id = property_exists($request_details, 'travaler_id') ? ( $request_details->travaler_id ? $request_details->travaler_id : null ) : null;
            $traveler_name = property_exists($request_details, 'traveler_name') ? ( $request_details->traveler_name ? $request_details->traveler_name : null ) : null;
            $related_id = property_exists($request_details, 'related_id') ? ( $request_details->related_id ? $request_details->related_id : null ) : null;
            if($is_behalf_of_request && !in_array($status_id, [0,'STAT_01']))
                $behalf_of_section_class = "";

        }
    }
    $travel_type_id=isset($travelling_details) && count($travelling_details) ? $travelling_details[0]->travel_type_id : null;
    $travel_type=isset($travelling_details) && count($travelling_details) ? $travelling_details[0]->travel_type : null;

    if(isset($visa_link_details) && is_array($visa_link_details))
    {
        $visa_request_id = array_key_exists('visa_request_id', $visa_link_details) ? $visa_link_details['visa_request_id'] : null;
        $module = array_key_exists('module', $visa_link_details) ? $visa_link_details['module'] : null;
        $request_for_code = array_key_exists('request_for_code', $visa_link_details) ? $visa_request_for_code[$visa_link_details['request_for_code']] : null;
        $travel_type_id = array_key_exists('travel_type', $visa_link_details) ? $visa_link_details['travel_type'] : null;
    }
    $is_td_visible=1;
    $review_statuses = ['STAT_02','STAT_12','STAT_29', 'STAT_31', 'STAT_33', 'STAT_37', 'STAT_38'];
    if(isset($edit_id)&&Auth::User()->has_any_role_code(['AN_COST_FIN','AN_COST_VISA','AN_COST_FAC']) && (in_array($status_id,$review_statuses))){
        $dp_obj=new \App\Http\Controllers\DetailsProvider();
        $respective_team_anticipate=$dp_obj->get_travel_desk_user_details($request_details->travel_request_id,['AN_COST_FIN','AN_COST_VISA','AN_COST_FAC'],$user_id=null);
        if(!in_array(Auth::User()->aceid,$respective_team_anticipate))
            $is_td_visible=0;
    }
    if(isset($related_visa_request ) && is_array($related_visa_request) && count($related_visa_request) ) {
        $related_visa_request_content = array_map(fn($e) => "<a href=$e->url target='_blank'>$e->request_id</a>", $related_visa_request);
        $related_visa_request_content = implode(', ', $related_visa_request_content);
    }
@endphp
<?php 
// if(isset($country))
// $country=json_decode(json_encode($country),true);
// if(isset($request_for))
// $request_for=json_decode(json_encode($request_for),true);
// if(isset($orgin))
// $orgin=json_decode(json_encode($orgin),true);

// if(isset($module)){
//     $module=$module;
// }else{
//     $module='MOD_02';
// }

// if(isset($request_details) && $request_details){
//     $travel_request_details=json_decode(json_encode($request_details),true);
    
// }
//$country=[];
//dd($request_details);
?>
<link rel="stylesheet" href="{{ URL::asset('css/jquery-confirm.min.css') }}">
<script type="text/javascript" src="{{ URL::asset('js/jquery-confirm.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/confirmation-box.js') }}"></script>
<script src="{{ asset('js/handleRefresh.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/travel_request.js?V=1.0') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/travel_action.js') }}"></script>
@if($can_extend_date)
    <script src="{{ URL::asset('js/travel_extend.js') }}"></script>
@endif

	<div class="form-section" id="request_page_section">
        <!-- Show proof, visa and entity error -->
<div class="container-fluid">
<div class="row">
<div id="action_prevent_error" class='col-md-12'></div>
</div>
</div>
		<div class="container-fluid">
		    <div class="row">
                <div class="col-md-12" style="margin-bottom:5px;margin-top:20px;">
                    <h3>{{ $module == "MOD_03" ? "Visa" : "Travel" }} request {!! isset($edit_id) ? ' - <span style="color:#2E4496">'.$request_details->request_code.' ('.$request_details->status_name.')</span>' : '' !!}</h3>
                    @if( isset($related_visa_request_content) )
                        <div class="col related_request_section">
                            <label for="">Related visa request</label>
                            <p>{!! $related_visa_request_content !!}</p>
                        </div>
                    @endif
                    @if(isset($status_details) && count($status_details) && $status_id != 'STAT_01')
                        <button type="button" class="primary-button" id="status-tracker-btn" data-toggle="modal" data-target="#statusTrackerModal">
                            Status tracker
                        </button>
                    @endif
                </div>
            </div>
		</div>
		<div class="container-fluid form-content" id="request_section">
			<div class="row">
            @if(!isset($edit_id))
                <input type='hidden' id='default_travel_flag' value="1" />
        @else      
                <input type='hidden' id='default_travel_flag' value="0" />  
                   
        @endif
            @php $field_name = "INP_062"; @endphp
                @if(in_array($field_name, $visible_fields))
                    <div class="{{ $module == 'MOD_03' ? 'col-md-2 col-sm-12' : 'col-md-3 col-sm-12' }}" style="display: {{ $module == 'MOD_03' ? 'none' : 'block' }};">
                        @if(array_key_exists($field_name,$field_attr))
                            @php $field_at=(array)json_decode($field_attr[$field_name]['attributes']); @endphp
                        
                            <label for="{{ $field_at['id'] }}" class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>
                            @if(in_array($field_name, $editable_fields))
                                {{ Form::select($field_attr[$field_name]['input_name'],$travel_request_for_list,$module,(array)json_decode($field_attr[$field_name]['attributes'])+$travel_request_for_attr); }} 
                            @else
                                <p>{{$request_details->module_name}}</p>
                            @endif
                        @endif

                    </div>
                @endif
                <input type="hidden" id= "visa_request_id" name="visa_request_id" value="{{ isset($visa_request_id) ? $visa_request_id : null }}" data-val="{{ isset($related_id) ? $related_id : null }}">
                <input type='hidden' id='default_country' value="{{$default_origin}}" />
                <input type='hidden' id='edit_id' value="{{$edit_id}}" />
                @php $field_name = "INP_001"; @endphp
                    @if(in_array($field_name, $visible_fields))
                        <div class="{{ $module == 'MOD_03' ? 'col-md-2 col-sm-12' : 'col-md-3 col-sm-12' }}">
                            @if(array_key_exists($field_name,$field_attr))
                            @php $field_at=(array)json_decode($field_attr[$field_name]['attributes']); @endphp
                                <label for="{{ $field_at['id'] }}" class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>
                                @if(in_array($field_name, $editable_fields))
                                    {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select']+$options['request_for'],$request_for_code,(array)json_decode($field_attr[$field_name]['attributes'])); }} 
                                @else
                                    <p>{{$request_details->request_for}}</p>
                                @endif
                            @endif
                    </div>
                    @endif
                @php $field_name = "INP_084"; @endphp
                @if(in_array($field_name, $visible_fields))
                    <div class="col-md-3 {{ $behalf_of_section_class }}">
                        @if(array_key_exists($field_name, $field_attr))
                            @php $field_at=(array)json_decode($field_attr[$field_name]['attributes']); @endphp
                            <label for="{{ $field_at['id'] }}" class = "{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{ $field_attr[$field_name]['lable_name'] }}</label>
                            @if(in_array($field_name, $editable_fields))
                                {{ Form::select($field_attr[$field_name]['input_name'], $behalf_of_users, $travaler_id, $field_at) }}
                            @else
                                <p>{{ $traveler_name }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                

                @php $field_name = "INP_063"; @endphp
                    @if(in_array($field_name, $visible_fields))
                        <div class="{{ $module == 'MOD_03' ? 'col-md-2 col-sm-12' : 'col-md-3 col-sm-12' }}">
                            @if(array_key_exists($field_name,$field_attr))
                            @php 
                                $field_at=(array)json_decode($field_attr[$field_name]['attributes']); 
                            @endphp
                            
                                <label for="{{ $field_at['id'] }}" class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>
                                @if(in_array($field_name, $editable_fields))
                                {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select']+$options['user_orgin'],$orgin,(array)json_decode($field_attr[$field_name]['attributes'])); }} 
                                @else
                                    <p>{{$options['user_orgin'][$orgin]}}</p>
                                @endif
                            @endif
                        </div>
                    @endif
                @php $field_name = "INP_142";  @endphp
                    @if(in_array($field_name, $visible_fields) && ( $module == 'MOD_02' || in_array($status_id, ['', '0', 'STAT_01']) ))
                        <div class="{{ $module == 'MOD_03' ? 'col-md-2 col-sm-12' : 'col-md-3 col-sm-12' }} origin_city">
                            @if(array_key_exists($field_name,$field_attr))
                            @php 
                                $field_at=(array)json_decode($field_attr[$field_name]['attributes']); 
                                $origin_city_list=[''=>'Select']+$origin_city_list;
                                
                                    

                               
                            @endphp
                            
                                <label for="{{ $field_at['id'] }}" class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>
                                @if(in_array($field_name, $editable_fields))
                                {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select']+$origin_city_list,$origin_city,(array)json_decode($field_attr[$field_name]['attributes'])); }} 
                                @else
                                    <p>{{($origin_city!="")?$origin_city_list[$origin_city]:"NA"}}</p>
                                @endif
                            @endif
                        </div>
                    @endif

                @php $field_name = "INP_007"; @endphp
                    @if(in_array($field_name, $visible_fields))
                        <div class="{{ $module == 'MOD_03' ? 'col-md-2 col-sm-12' : 'col-md-3 col-sm-12' }}">
                            @if(array_key_exists($field_name, $field_attr))
                                @php $field_at=(array)json_decode($field_attr[$field_name]['attributes']); @endphp
                                <label for="{{ $field_at['id'] }}" class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>
                                @if(in_array($field_name, $editable_fields))
                                    {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select']+$options['travel_type'],$travel_type_id,(array)json_decode($field_attr[$field_name]['attributes'])); }} 
                                @else
                                    <p>{{$travel_type}}</p>
                                @endif
                            @endif
                    </div>
                    @endif
                    <?php $field_name = "INP_008" ?>
            @if(in_array($field_name, $visible_fields))
                <div class="{{ $module == 'MOD_03' ? 'col-md-2 col-sm-12' : 'col-md-3 col-sm-12' }}">
                    @if(array_key_exists($field_name,$field_attr))
                    <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label><br>
                        @if(in_array($field_name, $editable_fields))
                        <?php $attributes = json_decode($field_attr[$field_name]['attributes'], true); 
                        $travel_purpose=isset($edit_id) ? $request_details->travel_purpose_id : null ?>
                            {{ Form::select($field_attr[$field_name]['input_name'], ['' => 'Select'] + $options['travel_purpose'], $travel_purpose, $attributes) }}
                        @else
                            <p>{{$request_details->travel_purpose}}</p>
                        @endif
                    @endif
                </div>
            @endif
			</div>	
            					
		</div>

		</div>

 <div class="travel_common">
    @if(isset($edit_id))
     @include('layouts.travel_common')
    @endif 

 </div>   
 
 @if(isset($approval_flow) && $approval_flow)
 <div class="form-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3>approval_flow</h3>
            </div>
        </div>
    </div>
 <div class="container-fluid form-content">
    <div class="col-md-12">
    
    @foreach($approval_flow as $flow)
        <div class="col-md-3"><span style="color:var(--is-purple)">{{$flow->user_role}}</span> : {{$flow->username}}</div>
    @endforeach
    
    </div>
 </div>
</div>
 @endif
 @include('layouts.travel_status_tracker')
@endsection
