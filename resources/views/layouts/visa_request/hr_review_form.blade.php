@php
    $hide_col = in_array("INP_106", $editable_fields) ? "payout-date hide" : "";
@endphp
@php($section_name="onsite_salary_negotiation")
@if(in_array($section_name, $visible_sections))
    @if( in_array('onsite_hr_review_waiting_msg', $visible_fields) )
        <div class="row">
            <div class="waiting visa-request-section hr_review_section">
                <img src="{{ asset('images/pending-icon.svg') }}" alt="" class="pending-icon">
                <span>{{$waiting_message}}</span>
            </div>
        </div>
    @elseif(in_array('onsite_hr_review_completed_msg', $visible_fields))
        @if(in_array('offshore_hr_review_waiting_msg', $visible_fields))
            <div class="row">
                <div class="waiting visa-request-section hr_review_section">
                    <img src="{{ asset('images/pending-icon.svg') }}" alt="" class="pending-icon">
                    <span>{{$waiting_message}}</span>
                </div>
            </div>
        @elseif(in_array('offshore_hr_review_completed_msg', $visible_fields))
            <div class="row">
                <div class="completed visa-request-section hr_review_section">
                    <img src="{{asset('images/completed-icon.svg')}}" alt="" class="completed-icon">
                    <span>{{$completed_messages[$section_name]}}</span>
                </div>
            </div>
        @elseif(in_array('offshore_hr_review_reject_msg', $visible_fields))
            <div class="row">
                <div class="closed visa-request-section hr_review_section">
                    <img src="{{asset('images/closed-icon.svg')}}" alt="" class="closed-icon">
                    <span>{{$reject_message}}</span>
                </div>
            </div>
        @endif
    @else
        <div id="onsite-hr-review-section" class="visa-request-section hr_review_section container-fluid form-content">
            <div class="row">
                @php($input_key = "INP_094")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                        <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                <div class="field-wrap">
                                    @php($id = $field_attr[$input_key]['attributes']['id'])
                                    @php($field_attr[$input_key]['input_name'] = $id."_years")
                                    @php($field_attr[$input_key]['attributes']['id'] = $id."_years")
                                    {{ Form::text($field_attr[$input_key]['input_name'], $india_experience_years, $field_attr[$input_key]['attributes']) }}
                                    @php($field_attr[$input_key]['input_name'] = $id."_months")
                                    @php($field_attr[$input_key]['attributes']['id'] = $id."_months")
                                    @php($fileds_attr[$input_key]['placeholder'] = 'Months')
                                    {{ Form::text($field_attr[$input_key]['input_name'], $india_experience_months, $field_attr[$input_key]['attributes']) }}
                                </div>
                            @else
                                <p>{{ $india_experience_years ?? 0 }} year(s) {{ $india_experience_months ?? 0 }} month(s)</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_095")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                <div class="field-wrap">
                                    @php($id = $field_attr[$input_key]['attributes']['id'])
                                    @php($field_attr[$input_key]['input_name'] = $id."_years")
                                    @php($field_attr[$input_key]['attributes']['id'] = $id."_years")
                                    {{ Form::text($field_attr[$input_key]['input_name'], $overall_experience_years, $field_attr[$input_key]['attributes']) }}
                                    @php($field_attr[$input_key]['input_name'] = $id."_months")
                                    @php($field_attr[$input_key]['attributes']['id'] = $id."_months")
                                    {{ Form::text($field_attr[$input_key]['input_name'], $overall_experience_months, $field_attr[$input_key]['attributes']) }}
                                </div>
                            @else
                                <p>{{ $overall_experience_years ?? 0 }} year(s) {{ $overall_experience_months ?? 0 }} month(s)</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_136")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                <input type="hidden" name={{ $field_attr[$input_key]['input_name'] }} value="{{ $band_detail }}">
                            @endif
                            <p>{{ $band_detail ?? "NA" }}</p>
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_100")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            <div class="field-wrap">
                            @if(in_array($input_key, $editable_fields))
                                <div class="input-group mb-2">
                                    <div class="input-group-addon">
                                        <span class="input-group-text">{{ $visa_currency_notation }}</span>
                                    </div>
                                    {{ Form::text($field_attr[$input_key]['input_name'], $salary_range_from, $field_attr[$input_key]['attributes']) }}
                                </div>
                                
                            @else
                                <p id="salary_range_text">{{ $visa_currency_notation }} {!! number_format((float)$salary_range_from, 2) !!} to {{ $visa_currency_notation }} {!! number_format((float)$salary_range_to, 2) ?? "NA" !!}</p>
                            @endif
                        @endif
                        @php($input_key = "INP_101")
                        @if(array_key_exists($input_key, $field_attr))
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $salary_range_to, $field_attr[$input_key]['attributes']) }}
                            @endif
                            </div>
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_102")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xl-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::select($field_attr[$input_key]['input_name'], ['' => 'Select']+$select_options[$input_key], $us_job_title_id, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $us_job_title }}</p>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @php($section_name="offshore_salary_negotiation")
        @if( in_array($section_name, $visible_sections) )
            @if(in_array('offshore_hr_review_waiting_msg', $visible_fields))
                <div class="row">
                    <div class="waiting visa-request-section hr_review_section">
                        <img src="{{ asset('images/pending-icon.svg') }}" alt="" class="pending-icon">
                        <span>{{$waiting_message}}</span>
                    </div>
                </div>
            @elseif(in_array('offshore_hr_review_reject_msg', $visible_fields))
                <div class="row">
                    <div class="closed visa-request-section hr_review_section">
                        <img src="{{asset('images/closed-icon.svg')}}" alt="" class="closed-icon">
                        <span>{{$reject_message}}</span>
                    </div>
                </div>
            @else
                <div id="offshore-hr-review-section" class="visa-request-section hr_review_section container-fluid form-content">
                    <div class="row">
                        @php($input_key = "INP_103")
                        @if(in_array($input_key, $visible_fields))
                            <div class="col-md-2 col-sm-12 col-xs-12">
                                @if(array_key_exists($input_key, $field_attr))
                                    <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label> <br />
                                    @if(in_array($input_key, $editable_fields))
                                        <div class="switch">
                                            <input type="checkbox" name={{$field_attr[$input_key]['input_name']}} id="{{$field_attr[$input_key]['attributes']['id']}}" {{ $undertaken_completed == 1 ? 'checked' : "" }} />
                                            <div class="slider round"></div>
                                        </div>
                                    @else
                                        <p>{{ $undertaken_completed == 1 ? "Yes" : "No" }}</p>
                                    @endif
                                @endif
                            </div>
                        @endif
                        @php($input_key = "INP_104")
                        @if(in_array($input_key, $visible_fields))
                            <div class="col-md-2 col-sm-12 col-xs-12">
                                @if(array_key_exists($input_key, $field_attr))
                                    <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                                    @if(in_array($input_key, $editable_fields))
                                        <div class="input-group mb-2">
                                            <div class="input-group-addon">
                                                <span class="input-group-text">{{ $visa_currency_notation }}</span>
                                            </div>
                                            {{ Form::text($field_attr[$input_key]['input_name'], $us_salary, $field_attr[$input_key]['attributes']) }}
                                        </div>
                                    @else
                                        <p>{{ $visa_currency_notation }}  {!! number_format((float)$us_salary, 2) ?? "NA" !!}</p>
                                    @endif
                                @endif
                            </div>
                        @endif
                        @php($input_key = "INP_105")
                        @if(in_array($input_key, $visible_fields))
                            <div class="col-md-2 col-sm-12 col-xs-12">
                                @if(array_key_exists($input_key, $field_attr))
                                    <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                                    @if(in_array($input_key, $editable_fields))
                                        <div class="input-group mb-2">
                                            <div class="input-group-addon">
                                                <span class="input-group-text">{{ $visa_currency_notation }}</span>
                                            </div>
                                            {{ Form::text($field_attr[$input_key]['input_name'], $one_time_bonus, $field_attr[$input_key]['attributes']) }}
                                        </div>
                                    @else
                                        <p>{{ $visa_currency_notation }}  {!! number_format((float)$one_time_bonus, 2) ?? "NA" !!}</p>
                                    @endif
                                @endif
                            </div>
                        @endif
                        @php($input_key = "INP_106")
                        @if(in_array($input_key, $visible_fields))
                            <div class="col-md-2 col-sm-12 col-xs-12 {{ $hide_col }}">
                                @if(array_key_exists($input_key, $field_attr))
                                    <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                                    @if(in_array($input_key, $editable_fields))
                                        {{ Form::text($field_attr[$input_key]['input_name'], $one_time_bonus_payout_date, $field_attr[$input_key]['attributes'] )}}
                                    @else
                                        <p>{{ $one_time_bonus_payout_date ?? "NA" }}</p>
                                    @endif
                                @endif
                            </div>
                        @endif
                        @php($input_key = "INP_107")
                        @if(in_array($input_key, $visible_fields))
                            <div class="col-md-2 col-sm-12 col-xs-12">
                                @if(array_key_exists($input_key, $field_attr))
                                    <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                                    @if(in_array($input_key, $editable_fields))
                                        {{ Form::text($field_attr[$input_key]['input_name'], $next_salary_revision_on, $field_attr[$input_key]['attributes'] ) }}
                                    @else
                                        <p>{{ $next_salary_revision_on ?? "NA" }}</p>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    @endif
@endif
