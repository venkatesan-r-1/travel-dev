@php
    $previous_comments=null;
    if(isset($status_details)){
        $status = array_filter($status_details, fn($k) => !in_array($k, ['remarks_details', 'remarks', 'approval_matrix', 'status_flow_code_mapping']), ARRAY_FILTER_USE_KEY);
        $visa_process_comments = array_column(array_filter($status, fn($v) => $v["old_status_code"] == "STAT_12"), 'comments');
        $visa_process_comments_string = implode("\n",$visa_process_comments );
    }
    if(in_array($status_id, ['STAT_14','STAT_12']))//$visa_flow == "short_term" && 
     array_push($visible_sections, "visa_process"); 

    $label_name = array_key_exists($to_country,$country_specific_label_name)?$country_specific_label_name[$to_country]:[];
@endphp
@php($section_name="visa_process")
@if(in_array('visa_process', $visible_sections) && $is_td_visible)
    @if(in_array('visa_process_waiting_msg', $visible_fields))
        <div class="row visa-request-section {{ $visa_flow == "short_term" ? "visa_stamping_section" : "completed_section"  }}">
            <div class="waiting">
                <img src="{{ asset('images/pending-icon.svg') }}" alt="" class="pending-icon">
                <span>{{ $waiting_message }}</span>
            </div>
        </div>
    @else
        <div class="visa-request-section container-fluid form-content {{ $visa_flow == "short_term" ? "visa_stamping_section" : "completed_section"  }}" id="visa-process-section">    
            <div class="row">
                @php($input_key="INP_131")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ ($label_name&&array_key_exists($input_key,$label_name))?$label_name[$input_key]:$field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $record_number, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $record_number ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key="INP_132")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ ($label_name&&array_key_exists($input_key,$label_name))?$label_name[$input_key]:$field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $most_recent_doe, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $most_recent_doe ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key="INP_133")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ ($label_name&&array_key_exists($input_key,$label_name))?$label_name[$input_key]:$field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $admit_until, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $admit_until ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key="INP_134")
                @if(in_array($input_key, $visible_fields) && isset($origin_based_inputs[$to_country]))
                    <div class="col-md-2">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $gc_initiated_on, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $gc_initiated_on ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @if(isset($visa_flow) )
                    @php($input_key = "INP_135")
                    @if(in_array($input_key, $visible_fields))
                        <div class="col-md-2">
                            @if(array_key_exists($input_key, $field_attr))
                                <label for="{{$field_attr[$input_key]['attributes']['id']}}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                                @if(in_array($input_key, $editable_fields))
                                    {{ Form::textarea($field_attr[$input_key]['input_name']), null, $field_attr[$input_key]['attributes'] }}
                                @endif
                            @endif
                        </div>
                    @endif
                    @if($status_id == "STAT_14" || ($status_id == "STAT_12" && $visa_process_comments_string) )
                        <div class="col-md-2">
                            <label for="">Previous comments</label>
                            <textarea disabled>{{ $visa_process_comments_string }}</textarea>
                        </div>
                    @endif
                @endif
            </div>
        </div>
   @endif        
@endif
