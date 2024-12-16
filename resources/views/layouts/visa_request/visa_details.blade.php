@php
    $flows_to_hide_fields = ["default", "short_term"];
    $visa_type_statuses = [0, 'STAT_01'];
    $class_to_hide_fields = in_array($visa_flow, $flows_to_hide_fields) ? "long_term_hide" : "";
    if(in_array($status_id, $visa_type_statuses))
       $class_to_hide_fields = "long_term_hide";
@endphp
@if(in_array("initiation", $visible_sections))
    <div id="visa-details-section" class="container-fluid form-content visa-request-section initiation_section">
        <div class="row">
            @php($input_key = "INP_086")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 col-sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::select($field_attr[$input_key]['input_name'], [''=>'Select']+$select_options[$input_key], $visa_type_code, $field_attr[$input_key]['attributes']) }}
                        @else
                            @if($status_id == "STAT_28")
                                <input type="hidden" id="hidden_visa_type" value="{{ $visa_type_code }}" />
                            @endif
                            <p id="{{ $field_attr[$input_key]['attributes']['id'] }}">{{ $visa_type ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_001")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 col-sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::select($field_attr[$input_key]['input_name'], [''=>'Select']+$select_options[$input_key], $request_for_code, $field_attr[$input_key]['attributes']) }}
                        @else
                            <p>{{ $request_for ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_137")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 col-sm-12 col-xs-12 {{$class_to_hide_fields}}">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::select($field_attr[$input_key]['input_name'], [''=>'Select']+$select_options[$input_key], $travaler_id, $field_attr[$input_key]['attributes']) }}
                        @else
                            <p>{{ $traveler_name ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_063")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 col-sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::select($field_attr[$input_key]['input_name'], [''=>'Select']+$select_options[$input_key], $origin, $field_attr[$input_key]['attributes']) }}
                        @else
                            <input type="hidden" id="origin_hidden" value="{{ $origin }}">
                            <p>{{ $origin_name ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            
            @php($input_key = "INP_049")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 -sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::text($field_attr[$input_key]['input_name'], $requestor_entity, $field_attr[$input_key]['attributes']) }}
                        @else
                            <p>{{ $requestor_entity ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_042")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 col-sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::select($field_attr[$input_key]['input_name'], [''=>'Select']+$select_options[$input_key], $to_country, $field_attr[$input_key]['attributes']) }}
                        @else
                            <p id="{{ $field_attr[$input_key]['attributes']['id'] }}">{{ $to_country_name ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_088")
            @if(in_array($input_key, $visible_fields))
            <div class="col-md-2 col-sm-12 col-xs-12 {{ $class_to_hide_fields }}">
                @if(array_key_exists($input_key, $field_attr))
                    <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                    @if(in_array($input_key, $editable_fields))
                        {{ Form::select($field_attr[$input_key]['input_name'], [''=>'Select']+$select_options[$input_key], $filing_type_id, $field_attr[$input_key]['attributes']) }}
                    @else
                        <p id="{{ $field_attr[$input_key]['attributes']['id'] }}">{{ $filing_type ?? "NA" }}</p>
                    @endif
                @endif
            </div>
            @endif
            @php($input_key = "INP_087")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 -sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::select($field_attr[$input_key]['input_name'], [''=>'Select']+$select_options[$input_key], $visa_category_code, $field_attr[$input_key]['attributes']) }}
                        @else
                            <p>{{ $visa_category ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_003")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 -sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::select($field_attr[$input_key]['input_name'], ['' => 'Select']+$select_options[$input_key], $to_city, $field_attr[$input_key]['attributes']) }}
                        @else
                            <p>{{ $to_city_name ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_004")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 -sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::text($field_attr[$input_key]['input_name'], $from_date, $field_attr[$input_key]['attributes']) }}
                        @else
                            <p>{{ $from_date ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_005")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 -sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::text($field_attr[$input_key]['input_name'], $to_date, $field_attr[$input_key]['attributes']) }}
                        @else
                            <p>{{ $to_date ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_010")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 -sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::select($field_attr[$input_key]['input_name'], ['' => 'Select']+$select_options[$input_key] ,$project_code, $field_attr[$input_key]['attributes']) }}
                        @else
                            <p>{{ $project_name ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_011")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 -sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::select($field_attr[$input_key]['input_name'], ['' => 'Select']+$select_options[$input_key], $customer_code , $field_attr[$input_key]['attributes']) }}
                        @else
                            <p>{{ $customer_name ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_009")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 -sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::select($field_attr[$input_key]['input_name'], [''=>'Select']+$select_options[$input_key], $department_code, $field_attr[$input_key]['attributes']) }}
                        @else
                            <p>{{ $department_name ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key = "INP_012")
            @if(in_array($input_key, $visible_fields))
                <div class="col-md-2 -sm-12 col-xs-12">
                    @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                        @if(in_array($input_key, $editable_fields))
                            {{ Form::select($field_attr[$input_key]['input_name'], [''=>'Select']+$select_options[$input_key],  $practice_unit_code, $field_attr[$input_key]['attributes']) }}
                        @else
                            <p>{{ $practice_unit_name ?? "NA" }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($input_key="INP_125")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12 {{ $class_to_hide_fields }}">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::select($field_attr[$input_key]["input_name"], ['' => 'Select']+$select_options[$input_key], $traveling_type_id, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $traveling_type ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
        </div>
    </div>
@endif
@include('layouts.visa_request.mexico_alert_modal')
