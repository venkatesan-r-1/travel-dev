@php
    $flows_to_hide_fields = ["long_term"];
    $visa_type_statuses = [0, 'STAT_01'];
    $class_to_hide_fields = in_array($visa_flow, $flows_to_hide_fields) ? "short_term_hide" : "";
    if(in_array($status_id, $visa_type_statuses))
        $class_to_hide_fields = "short_term_hide";
@endphp
@php($section_name="personal_info")
@if(in_array($section_name, $visible_sections))
    @if( in_array('personal_info_waiting_msg', $visible_fields) )
        <div class="row">
            <div class="waiting visa-request-section initiation_section">
                <img src="{{ asset('images/pending-icon.svg') }}" alt="" class="pending-icon">
                <span>{{ $waiting_message }}</span>
            </div>
        </div>
    @else
        <div id="personal-info-section" class="container-fluid form-content visa-request-section initiation_section {{$class_to_hide_fields}}">
            <div class="row">
                @php($input_key = "INP_089")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $dob, $field_attr[$input_key]['attributes'])}}
                            @else
                                <p>{{ $dob ?? '-' }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_090")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::textarea($field_attr[$input_key]['input_name'], $address, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $address ?? '-' }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_092")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            <div class="field-wrap">
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::select($field_attr[$input_key]['input_name'], [''=>'Select']+$select_options[$input_key], $education_category_id, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $education_category ?? "NA" }}</p>
                            @endif
                        @endif
                        @php($input_key = "INP_091")
                        @if(array_key_exists($input_key, $field_attr))
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::select($field_attr[$input_key]['input_name'], [''=>'Select']+$select_options[$input_key], $education_details_id, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $education_details ?? "NA" }}</p>
                            @endif
                            </div>
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_093")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $doj, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $doj }}</p>
                            @endif
                        @endif
                    </div>    
                @endif
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
                @php($input_key = "INP_096")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            <img src="{{ asset('images/info.svg') }}" alt="info icon for file upload" data-html="true" data-toggle="tooltip" data-placement="right" data-original-title="Documents allowed: png, jpg, jpeg, pdf, docx <br> Maximum file size allowed: 5MB">
                            <?php
                                $input_name = $field_attr[$input_key]['input_name'];
                                $cv_file_names = isset($cv_file_path) ? explode(',', $cv_file_path) : [];
                                $file_count = isset($cv_file_names) ? count($cv_file_names) : 0;
                                $display_name = isset($file_count) ? $file_count." files selected" : null;
                                $file_size = isset($cv_file_path) && file_exists(public_path($cv_file_path)) ? filesize(public_path($cv_file_path)) : 0;
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
                                    <input type="hidden" name="{{$input_name}}" value="{{$cv_file_path}}">
                                    @if($file_count)
                                        <div class="file-upload-container">
                                            @foreach($cv_file_names as $cv_file_name)
                                                <?php
                                                    $file_path = $cv_file_name;
                                                    $file_name = basename($cv_file_name);
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
                                            @foreach($cv_file_names as $cv_file_name)
                                                <?php
                                                    $file_path = $cv_file_name;
                                                    $file_name = basename($cv_file_name);
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
                @php($input_key = "INP_097")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            <img src="{{ asset('images/info.svg') }}" alt="info icon for file upload" data-html="true" data-toggle="tooltip" data-placement="right" data-original-title="Documents allowed: docx, png, jpg, pdf <br> Maximum file size allowed: 5MB">
                            <?php
                                $input_name = $field_attr[$input_key]['input_name'];
                                $degree_file_names = isset($degree_file_path) ? explode(',', $degree_file_path) : [];
                                $file_count = isset($degree_file_names) ? count($degree_file_names) : 0;
                                $display_name = isset($file_count) ? $file_count." files selected" : null;
                                $file_size = isset($degree_file_path) && file_exists(public_path($degree_file_path)) ? filesize(public_path($degree_file_path)) : 0;
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
                                    <input type="hidden" name="{{$input_name}}" value="{{$degree_file_path}}">
                                    @if($file_count)
                                        <div class="file-upload-container">
                                            @foreach($degree_file_names as $degree_file_name)
                                                <?php
                                                    $file_path = $degree_file_name;
                                                    $file_name = basename($degree_file_name);
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
                                            @foreach($degree_file_names as $degree_file_name)
                                                <?php
                                                    $file_path = $degree_file_name;
                                                    $file_name = basename($degree_file_name);
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


            </div>
            @include('layouts.visa_request.proof_details')
        </div>
    @endif
@endif
