@php($section_name="visa_approval")
@if(in_array($section_name, $visible_sections) )
    @if(in_array('visa_approval_waiting_msg',$visible_fields))
        <div class="row">
            <div class="waiting visa-request-section visa_stamping_section">
                <img src="{{asset('images/pending-icon.svg')}}" alt="" class="pending-icon">
                <span>{{ $waiting_message }}</span>
            </div>
        </div>
    @elseif(in_array('visa_approval_completed_msg',$visible_fields))
        <div class="row">
            <div class="completed visa-request-section visa_stamping_section">
                <img src="{{asset('images/completed-icon.svg')}}" alt="" class="completed-icon">
                <span>{{ $completed_messages[$section_name] }}</span>
            </div>
        </div>
    @elseif(in_array('visa_approval_reject_msg',$visible_fields))
        <div class="row">
            <div class="closed visa-request-section visa_stamping_section">
                <img src="{{asset('images/closed-icon.svg')}}" alt="" class="closed-icon">
                <span>{{ $reject_message }}</span>
            </div>
        </div>
    @else
        <div class="visa-request-section visa_stamping_section container-fluid form-content" id="visa-approval-section">
            <div class="row">
                @php($input_key="INP_117")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::select($field_attr[$input_key]["input_name"], [''=>'Select']+$select_options[$input_key], $entry_type_id,  $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $entry_type ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key="INP_118")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::select($field_attr[$input_key]["input_name"], [''=>'Select']+$select_options[$input_key], $visa_interview_type_id,  $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $visa_interview_type ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key="INP_119")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]["input_name"], $visa_ofc_date, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $visa_ofc_date ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key="INP_120")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]["input_name"], $visa_interview_date, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $visa_interview_date ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key="INP_121")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::select($field_attr[$input_key]["input_name"], ['' => 'Select']+$select_options[$input_key], $visa_status_id, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $visa_status ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
@endif
@php($section_name="visa_entry")
@if( in_array($section_name, $visible_sections) )
    @if(in_array('visa_details_waiting_msg',$visible_fields))
        <div class="row">
            <div class="waiting visa-request-section visa_stamping_section">
                <img src="{{asset('images/pending-icon.svg')}}" alt="" class="pending-icon">
                <span>{{ $waiting_message }}</span>
            </div>
        </div>
    @else
        <div id="visa-detail-section" class="visa-request-section visa_stamping_section container-fluid form-content">
            <div class="row">
                @php($input_key="INP_117")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            <p>{{ $entry_type ?? "NA" }}</p>
                        @endif
                    </div>
                @endif
                @php($input_key="INP_122")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]["input_name"], $travel_date, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $travel_date ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key="INP_123")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]["input_name"], $travel_location, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $travel_location ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key="INP_141")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]["input_name"], $visa_number, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $visa_number ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_124")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            <img src="{{ asset('images/info.svg') }}" alt="info icon for file upload" data-html="true" data-toggle="tooltip" data-placement="right" data-original-title="Documents allowed: png, jpg, jpeg, pdf, docx <br> Maximum file size allowed: 5MB">
                            <?php
                                $input_name = $field_attr[$input_key]['input_name'];
                                $visa_file_names = isset($visa_file_path) ? explode(',', $visa_file_path) : [];
                                $file_count = isset($visa_file_names) ? count($visa_file_names) : 0;
                                $display_name = isset($file_count) ? $file_count." files selected" : null;
                                $file_size = isset($visa_file_path) && file_exists(public_path($visa_file_path)) ? filesize(public_path($visa_file_path)) : 0;
                            ?>
                            @if(in_array($input_key, $editable_fields))
                                <div class="file-wrapper">
                                    @if($file_count)
                                        <div class="file-info">
                                            <span class="info">{{ $display_name }}</span>
                                    @endif
                                    <img src="{{ asset('images/layer.svg') }}" alt="file upload icon" class="upload-icon">
                                    @if($file_count)
                                        </div>
                                    @endif
                                    @php( $field_attr[$input_name]['attributes']['count'] = $file_count )
                                    {{ Form::file(str_replace("_path","_upload", $input_name), $field_attr[$input_key]['attributes']) }}
                                    <input type="hidden" name="{{$input_name}}" value="{{$visa_file_path}}">
                                    @if($file_count)
                                        <div class="file-upload-container">
                                            @foreach($visa_file_names as $visa_file_name)
                                                <?php
                                                    $file_path = $visa_file_name;
                                                    $file_name = basename($visa_file_name);
                                                    $file_size = file_exists(public_path($file_path)) ? filesize(public_path($file_path)) : 0;
                                                ?>
                                                <div class="row-item">
                                                    <div class="file-name">
                                                        <a href="/{{ $file_path }}" download="{{ $file_name }}" target="_blank">{{$file_name}}</a>
                                                    </div>
                                                    <div class="file-action">
                                                        <span class="file-size">{{ $file_size }}</span>
                                                        <img src="{{ asset('images/close.svg') }}" class='file-remove'>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>                            
                            @else
                                @if($file_count)
                                    <div class="file-wrapper">
                                        <p class="file-view-link" style="color: #337ab7; cursor: pointer">{{ $display_name }}</p>
                                        <div class="file-upload-container">
                                            @foreach($visa_file_names as $visa_file_name)
                                                <?php
                                                    $file_path = $visa_file_name;
                                                    $file_name = basename($visa_file_name);
                                                    $file_size = file_exists(public_path($file_path)) ? filesize(public_path($file_path)) : 0;
                                                ?>
                                                <div class="row-item">
                                                    <div class="file-name">
                                                        <a href="/{{ $file_path }}" download="{{ $file_name }}" target="_blank">{{$file_name}}</a>
                                                    </div>
                                                    <div class="file-action">
                                                        <span class="file-size">{{ $file_size }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>
                @endif
                {{-- @php($input_key="INP_125")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::select($field_attr[$input_key]["input_name"], ['' => 'Select']+$select_options[$input_key], $traveling_type_id, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $traveling_type ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif --}}
                {{-- @php($input_key="INP_126")
                @if(in_array($input_key, $visible_fields))
                    @if(isset($dependency_details) && is_array($dependency_details) &&count($dependency_details))
                        @php($index = 0)
                        @php($label_name_mapping = ["Spouse", "First child", "Second child", "Third child", "Fourth child"])
                        @foreach($dependency_details as $key => $value)
                            <div class="col-md-2 col-sm-12 col-xs-12 dependent-details-div">
                                @if(array_key_exists($input_key, $field_attr))
                                    <label for="{{ $field_attr[$input_key]['attributes']['id'] }}">{{ $label_name_mapping[$index++] }}</label>
                                    @if(in_array($input_key, $editable_fields))
                                        <div class="field-wrap">
                                            {{ Form::text($field_attr[$input_key]["input_name"], $value, $field_attr[$input_key]['attributes']) }}
                                            <img src="{{ asset('images/minus-circle.svg') }}" alt="" class="remove-dependent-name" style="display: {{ array_key_first($dependency_details) == $key ? 'none' : 'block'}};">
                                            @if($key == array_key_last($dependency_details))
                                                <img src="{{ asset('images/add.svg') }}" alt="" id="add-dependent-name" style="display: {{ $index == 5 ? 'none' : 'block'}};">
                                            @endif
                                        </div>
                                    @else
                                        <p>{{ $value }}</p>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="col-md-2 col-sm-12 col-xs-12 dependent-details-div">
                            @if(array_key_exists($input_key, $field_attr))
                                <label for="{{ $field_attr[$input_key]['attributes']['id'] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                                @if(in_array($input_key, $editable_fields))
                                    <div class="field-wrap">
                                        {{ Form::text($field_attr[$input_key]["input_name"], null, $field_attr[$input_key]['attributes']) }}
                                        <img src="{{ asset('images/minus-circle.svg') }}" alt="" class="remove-dependent-name">
                                        <img src="{{ asset('images/add.svg') }}" alt="" id="add-dependent-name">
                                    </div>
                                @else
                                    <p>-</p>
                                @endif
                            @endif
                        </div>
                    @endif
                @endif --}}
            </div>
        </div>
    @endif
@endif
@php($section_name="visa_stamping")
@if( in_array($section_name, $visible_sections) && $visa_flow != "short_term" )
    @if(in_array('visa_stamping_waiting_msg',$visible_fields))
        <div class="row">
            <div class="waiting visa-request-section visa_stamping_section">
                <img src="{{asset('images/pending-icon.svg')}}" alt="" class="pending-icon">
                <span>{{ $waiting_message }}</span>
            </div>
        </div>
    @else
        <div id="visa-stamping-section" class="visa-request-section visa_stamping_section container-fluid form-content">
            <div class="row">
                @php($input_key = "INP_102")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xl-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            <p>{{ $us_job_title }}</p>
                        @endif
                    </div>
                @endif
                @php($input_key="INP_127")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}">{{ $field_attr[$input_key]['lable_name'] }}</label> <br />
                            @if(in_array($input_key, $editable_fields) && !$is_file_exists)
                                <div class="switch">
                                    <input type="checkbox" name="{{ $field_attr[$input_key]['input_name'] }}" id="{{ $field_attr[$input_key]['attributes']['id'] }}">
                                    <div class="slider round"></div>
                                </div>
                            @else
                                <p>{{ $green_card_title == 1 ? "Yes" : "No" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                <input type="hidden" name="offer_letter_paths" value="{{$offer_letter_path_pdf}}">
                @if(Auth::User()->has_any_role_code(['HR_REV']) && $status_id == "STAT_37" && !$is_file_exists)
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        <input type="hidden" id="employee_name" value="{{$first_name}}">
                        <input type="hidden" id="doj" value="{{$doj}}">
                        <button name="generate_offer_letter" value="generate_offer_letter" id="generate"  class="secondary-button" style="margin-right:14px">Generate offer letter</button>
                    </div>
                @endif
                @php($input_key = "INP_128")
                @if(in_array($input_key, $visible_fields) && $is_file_exists)
                    <div class="col-md-2 col-sm-12 col-xs-12" style="display: {{ Auth::User()->aceid == $travaler_id ? "block" : "none" }};>
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            <a href="{{$offer_letter_path_pdf}}" target="_blank">{{ basename($offer_letter_path_pdf) }}</a>
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_129")
                @if(in_array($input_key, $visible_fields) && $is_file_exists)
                    <div class="col-md-2 col-sm-12 col-xs-12" style="display: {{ Auth::User()->has_any_role_code(['GM_REV']) ? "block" : "none"; }}">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            <a href="{{$immigration_offer_letter_path}}" target="_blank">{{ basename($immigration_offer_letter_path) }}</a>
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_130")
                @if(in_array($input_key, $visible_fields) && $is_file_exists)
                    <div class="col-md-2 col-sm-12 col-xs-12" style="display: {{ Auth::User()->aceid != $travaler_id ? "block" : "none" }}">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            <a href="{{$offer_letter_path_word}}" target="_blank">{{ basename($offer_letter_path_word) }}</a>
                        @endif
                    </div>
                @endif
                @if(in_array('password_info',$visible_fields))
                    <div class="visa-password-info" style="padding: 0px 15px;">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="visa-password-info-content"><span class="visa-password-info-header">Note : </span>Offer letter protected by an 12 character password and you need to enter it in this format to view. The first three letters of your password are the first 3 letters of your first name, followed by your date of joining in DDMMMYYYY format.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endif
