@php
    $can_view_immigration_tab = $visa_flow != "short_term";
@endphp
@php($section_name="gm_review")
@if(in_array($section_name, $visible_sections))
    @if( in_array('gm_review_waiting_msg', $visible_fields) )
        <div class="row">
            <div class="waiting visa-request-section gm_review_section gm_review_approval_section">
                <img src="{{ asset('images/pending-icon.svg') }}" alt="" class="pending-icon">
                <span>{{ $waiting_message }}</span>
            </div>
        </div>
    @else
    @if($travaler_id != Auth::User()->aceid || Auth::User()->has_any_role_code(['AN_COST_VISA']))
        @include('layouts.anticipated_cost')
    @endif
        @if($can_view_immigration_tab)
            @if( in_array('gm_review_completed_msg', $visible_fields) )
                <div class="row">
                    <div class="completed visa-request-section gm_review_section gm_review_approval_section">
                        <img src="{{ asset('images/completed-icon.svg') }}" alt="" class="completed-icon">
                        <span>{{ $completed_messages["immigration_review"] }}</span>
                    </div>
                </div>
            @else
                <div id="immigration-review-section" class="container-fluid form-content visa-request-section gm_review_section gm_review_approval_section">
                    <div class="row">
                        @php($input_key = "INP_139")
                        @if(in_array($input_key, $visible_fields))
                            <div class="col-md-3">
                                @if(array_key_exists($input_key, $field_attr))
                                    <label for="{{$field_attr[$input_key]['attributes']['id']}}">{{$field_attr[$input_key]['lable_name']}}</label>
                                    @if(in_array($input_key, $editable_fields))
                                        {{ Form::select($field_attr[$input_key]['input_name'], ['' => 'Select'] + $select_options[$input_key], $visa_currency, $field_attr[$input_key]['attributes'] )}}
                                    @else
                                        <p class="visa_currency">{{ $visa_currency_notation  ?? "NA" }}</p>
                                    @endif
                                @endif
                            </div>
                        @endif
                        @php($input_key = "INP_098")
                        @if(in_array($input_key, $visible_fields))
                            <div class="col-md-3">
                                @if(array_key_exists($input_key, $field_attr))
                                    <label for="{{$field_attr[$input_key]['attributes']['id']}}" class="{{ $mandatory_class_list[$input_key] }}">{{$field_attr[$input_key]['lable_name']}}</label>
                                    @if(in_array($input_key, $editable_fields))
                                        {{ Form::text($field_attr[$input_key]['input_name'], $minimum_wage, ['class' => $field_attr[$input_key]['attributes']['class'], 'id' => $field_attr[$input_key]['attributes']['id']]) }}
                                        <!-- {{ Form::text($field_attr[$input_key]['input_name']), $minimum_wage, $field_attr[$input_key]['attributes'] }} -->
                                    @else
                                        <p id="minimum_wage_text" class="minimum_wage currency_format">{{ $visa_currency_notation }} {!! ($minimum_wage) !!}</p>
                                    @endif
                                @endif
                            </div>
                        @endif
                        @php($input_key = "INP_099")
                        @if(in_array($input_key, $visible_fields))
                            <div class="col-md-3">
                                @if(array_key_exists($input_key, $field_attr))
                                    <label for="{{$field_attr[$input_key]['attributes']['id']}}" class="{{ $mandatory_class_list[$input_key] }}">{{$field_attr[$input_key]['lable_name']}}</label>
                                    @if(in_array($input_key, $editable_fields))
                                        {{ Form::text($field_attr[$input_key]['input_name']), $work_location, $field_attr[$input_key]['attributes'] }}
                                    @else
                                        <p>{{ $work_location ?? "NA" }}</p>
                                    @endif
                                @endif
                            </div>
                        @endif
                        @php($input_key = "INP_140")
                        @if(in_array($input_key, $visible_fields))
                            <div class="col-md-3">
                                @if(array_key_exists($input_key, $field_attr))
                                    <label for="{{$field_attr[$input_key]['attributes']['id']}}" class="{{ $mandatory_class_list[$input_key] }}">{{$field_attr[$input_key]['lable_name']}}</label>
                                    @if(in_array($input_key, $editable_fields))
                                        {{ Form::select($field_attr[$input_key]['input_name'], ['' => 'Select'] + $select_options[$input_key], $job_title_id, $field_attr[$input_key]['attributes']) }}
                                    @else
                                        <p>{{ $job_title ?? "NA" }}</p>
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
