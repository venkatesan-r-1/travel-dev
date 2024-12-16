@php
    $visible_fields = array_key_exists('visible_fields', $field_details) ? $field_details['visible_fields'] : [];
    $editable_fields = array_key_exists('editable_fields', $field_details) ? $field_details['editable_fields'] : [];
    $field_attr =array_key_exists('field_attr', $field_details) ? $field_details['field_attr'] : [];
    $options =array_key_exists('select_options', $field_details) ? $field_details['select_options'] : [];
    $module ='MOD_02';
    $request_for ='';
    $dob='';
    $requestor_remarks = '';

    if(isset($edit_id))
    {
        if(isset($request_details))
        {
            $module = property_exists($request_details, 'module') ? $request_details->module : '';
            $request_for = property_exists($request_details, 'request_for') ? $request_details->request_for : '';
            $dob =property_exists($request_details, 'dob') ? ( $request_details->dob ? date('d-M-Y', strtotime($request_details->dob)) : 'NA' ) : '';
            $no_of_member = property_exists($request_details, 'no_of_members') ? explode("&",$request_details->no_of_members) : '';
            $status_details = isset($status_details) ? $status_details : [];
            $remarks_details = array_key_exists('remarks_details', $status_details) ? $status_details['remarks_details'] : [];
            $requestor_remarks = array_key_exists('requestor_remarks', $remarks_details) ? $remarks_details['requestor_remarks'] : '';
        }
     
    }else{
        $no_of_member=explode("&","");

    }
 
    $accommodation_required=isset($edit_id)?$request_details->accommodation_required:'';
    $ticket_required=isset($edit_id)?$request_details->ticket_required:'';
    $family_traveling=isset($edit_id)?$request_details->family_traveling:'';
    $forex_required=isset($edit_id)?$request_details->forex_required:'';
    $laptop_required=isset($edit_id)?$request_details->laptop_required:'';
    $insurance_required=isset($edit_id)?$request_details->insurance_required:'';
    $no_of_members=isset($edit_id)?$request_details->no_of_members:'';
    $prefered_accommodation=isset($edit_id)?$request_details->prefered_accommodation:'';
    $forex_currency=isset($edit_id)?$request_details->forex_currency:'';
    $working_from=isset($edit_id)?$request_details->working_from:'';
    $no_of_members_string='';
    if($no_of_members){
        $mem_array=explode('&',$no_of_members);
        if(array_key_exists(0,$mem_array)&&$mem_array[0])
            $no_of_members_string.=$mem_array[0]." Adult(s)";
        if(array_key_exists(1,$mem_array)&& $mem_array[1])
        {
            if($no_of_members_string)
                $no_of_members_string.=" & ";
            $no_of_members_string.=$mem_array[1]." Child(s)";    
        }
    }
    $no_of_members=$no_of_members_string;


@endphp
<?php 
    $worklocation=$options['worklocation'];
    $currency=$options['currency'];
?>

<link type="text/css" href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
	<div id="other_details" style="margin-top:10px">
        <div class="row">
			@php $field_name = "INP_014"; @endphp
                @if(in_array($field_name, $visible_fields))
                    <div class="col-md-3 col-sm-12 radio_required" >
                        @if(array_key_exists($field_name,$field_attr))
                            <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>&nbsp;<span class="alert_required" style="display:none;color:red">required</span><br>
                            @if(in_array($field_name, $editable_fields))
                                <label><input type="radio" id="ticket_required_1" name="{{$field_attr[$field_name]['input_name']}}" {{$ticket_required==1?'checked':''}} value=1 >Yes</label>
                                <label><input type="radio" id="ticket_required_2" name="{{$field_attr[$field_name]['input_name']}}" {{$ticket_required==0?'checked':''}} value=0 >No</label>
                            @else
                                <p>{{$ticket_required?'Yes':($ticket_required==0 ? 'No' : '-')}}</p>
                            @endif
                        @endif
                    </div>
                @endif
            @php $field_name = "INP_015"; @endphp
                @if(in_array($field_name, $visible_fields))
                    <div class="col-md-3 col-sm-12 radio_required" id="family_travel" style="<?php echo isset($edit_id) ? ($request_details->ticket_required ? 'display:block' : 'display:none') : 'display:block'; ?>">
                        @if(array_key_exists($field_name,$field_attr))
                        <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>&nbsp;<span class="alert_required" style="display:none;color:red">required</span><br>
                            @if(in_array($field_name, $editable_fields))

                                <label><input type="radio" id="family_travel_1" name="{{$field_attr[$field_name]['input_name']}}" {{$family_traveling==1?'checked':''}} value=1 >Yes</label>
                                <label><input type="radio" id="family_travel_2" name="{{$field_attr[$field_name]['input_name']}}" {{$family_traveling==0?'checked':''}} value=0 >No</label>
                                <label style="margin-bottom: 0px !important;"><input id="no_adult" class="l2 non-text" placeholder="Adult" max="9" style="display: none;max-width:50px;" name="adult" type="number" max="9" value={{count($no_of_member)?$no_of_member[0]:''}} disabled="disabled"></label>
                                <label style="margin-bottom: 0px !important;"><input id="no_child" class="l2 non-text" placeholder="Child " max="9" style="display: none;max-width:50px;" name="child" type="number" max="9" value={{count($no_of_member)==2?$no_of_member[1]:''}} disabled="disabled"></label>
                                
                            @else
                            <p>{{ $family_traveling ? 'Yes' : ($family_traveling == 0 ? 'No' : '-') }}&emsp;{{ $family_traveling == 1 ? ('count : '.$no_of_members) : '' }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                    {{--@if($module=="MOD_02")--}}
                @php $field_name = "INP_048"; @endphp
                    @if(in_array($field_name, $visible_fields))
                        <div class="col-md-3 col-sm-12 radio_required">
                            @if(array_key_exists($field_name,$field_attr))
                                <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>&nbsp;<span class="alert_required" style="display:none;color:red">required</span><br>
                                @if(in_array($field_name, $editable_fields))
                                    <label><input type="radio" id="forex_required_1" name="{{$field_attr[$field_name]['input_name']}}" {{$forex_required==1?'checked':''}} value=1 >Yes</label>
                                    <label><input type="radio" id="forex_required_2" name="{{$field_attr[$field_name]['input_name']}}" {{$forex_required==0?'checked':''}} value=0 >No</label>

                                @else
                                    <p>{{$forex_required?'Yes':($forex_required==0?'No':'-')}}</p>
                                @endif
                            @endif
                        </div>
                    @endif
                @php $field_name = "INP_043"; @endphp
                    @if(in_array($field_name, $visible_fields))
                        <div class="col-md-3 col-sm-12" id="currency_div" style="<?php echo isset($edit_id) ? ($request_details->forex_required==1 ? 'display:block' : 'display:none') : 'display:none'; ?>" >
                            @if(array_key_exists($field_name,$field_attr))
                            @php $field_at=(array)json_decode($field_attr[$field_name]['attributes']); @endphp
                            <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>
                                @if(in_array($field_name, $editable_fields))                            
                                    {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select']+$currency,$forex_currency,$field_at); }} 
                                @else
                                    <p>{{$forex_required?(array_key_exists($forex_currency, $currency) ? $currency[$forex_currency] : '-'):'-'}}</p>
                                @endif
                            @endif
                        </div>
                    @endif
                    {{--@endif--}}	
            @php $field_name = "INP_018"; @endphp
                @if(in_array($field_name, $visible_fields))
                    <div class="col-md-3 col-sm-12 radio_required" id="accommodation" style="<?php echo isset($edit_id) ? ($request_details->ticket_required==1 ? 'display:block' : 'display:none') : 'display:block'; ?>" >
                        @if(array_key_exists($field_name,$field_attr))
                        <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>&nbsp;<span class="alert_required" style="display:none;color:red">required</span><br>
                            @if(in_array($field_name, $editable_fields))
                                <label><input type="radio" id="accommodation_1" name="{{$field_attr[$field_name]['input_name']}}" {{$accommodation_required==1?'checked':''}} value=1 >Yes</label>
                                <label><input type="radio" id="accommodation_2" name="{{$field_attr[$field_name]['input_name']}}" {{$accommodation_required==0?'checked':''}} value=0 >No</label>
                            @else
                                <p>{{$accommodation_required?'Yes':($accommodation_required==0?'No':'-')}}</p>
                            @endif
                        @endif
                    </div>
                @endif
            @php $field_name = "INP_019"; @endphp
                @if(in_array($field_name, $visible_fields))
                    <div class="col-md-3 col-sm-12" id="preferred_address_div" style="<?php echo isset($edit_id) ? ($request_details->accommodation_required==1 ? 'display:block' : 'display:none') : 'display:none'; ?>">
                        @if(array_key_exists($field_name,$field_attr))
                        @php $field_at=(array)json_decode($field_attr[$field_name]['attributes']);        
                                 @endphp
                                 <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>
                            @if(in_array($field_name, $editable_fields))
                                {{ Form::textarea($field_attr[$field_name]['input_name'],$prefered_accommodation,$field_at); }}
                            @else
                                <p>{{isset($prefered_accommodation)?$prefered_accommodation:'-'}}</p>
                            @endif
                        @endif
                    </div>
                @endif
            @php $field_name = "INP_020"; @endphp
                    @if(in_array($field_name, $visible_fields))
                    @php $field_at=(array)json_decode($field_attr[$field_name]['attributes']);@endphp
                            <div class="col-md-3 col-sm-12">
                                @if(array_key_exists($field_name,$field_attr))
                                    <label for="INP_020" class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>
                                    @if(in_array($field_name, $editable_fields))
                                    {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select']+$worklocation,$working_from,$field_at); }} 
                                    @else
                                        <p>{{$worklocation[$working_from]}}</p>
                                    @endif
                                @endif
                            </div>
                    @endif				
            @php $field_name = "INP_045"; @endphp
                @if(in_array($field_name, $visible_fields))
                        <div class="col-md-3 col-sm-12 radio_required">
                            @if(array_key_exists($field_name,$field_attr))
                                <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>&nbsp;<span class="alert_required" style="display:none;color:red">required</span><br>
                                @if(in_array($field_name, $editable_fields))
                                    <label><input type="radio" id="laptop_required_1" name="{{$field_attr[$field_name]['input_name']}}" {{$laptop_required==1?'checked':''}} value=1 >Yes</label>
                                    <label><input type="radio" id="laptop_required_2" name="{{$field_attr[$field_name]['input_name']}}" {{$laptop_required==0?'checked':''}} value=0 >No</label>
                                @else
                                    <p>{{$laptop_required?'Yes':($laptop_required==0?'No':'-')}}</p>
                                @endif
                            @endif
                        </div>
                @endif
            @php $field_name = "INP_044"; @endphp
                    @if(in_array($field_name, $visible_fields))
                            <div class="col-md-3 col-sm-12 radio_required" id="insurance" style="<?php echo isset($edit_id) ? ($request_details->ticket_required ? 'display:block' : 'display:none') : 'display:none'; ?>">
                                @if(array_key_exists($field_name,$field_attr))
                                <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label>&nbsp;<span class="alert_required" style="display:none;color:red">required</span><br>
                                    @if(in_array($field_name, $editable_fields))
                                        <label><input type="radio" id="insurance_required_1" name="{{$field_attr[$field_name]['input_name']}}" {{$insurance_required==1?'checked':''}} value=1 >Yes</label>
			                            <label><input type="radio" id="insurance_required_2" name="{{$field_attr[$field_name]['input_name']}}" {{$insurance_required==0?'checked':''}} value=0 >No</label>
                                    @else
                                        <p>{{$insurance_required?'Yes':($insurance_required==0?'No':'-')}}</p>
                                    @endif
                                @endif
                            </div>
                    @endif    
        </div>					
	</div>		
    @if(in_array("INP_073", $visible_fields))				
    @else
    </div>
    @endif
	

	
