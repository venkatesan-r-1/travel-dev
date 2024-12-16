@php($section_name = 'petition_process')
@if(in_array($section_name, $visible_sections))
    @if(in_array('petition_process_waiting_msg', $visible_fields) || in_array('rfe_progress_waiting_msg', $visible_fields))
        <div class="row">
            <div class="waiting visa-request-section petition_process_section">
                <img src="{{asset('images/pending-icon.svg')}}" alt="" class="pending-icon">
                <span>
                    {{$waiting_message}}
                    @if(isset($remarks) && $remarks)
                    <div class="petition-comments">
                        <label for="pre-comments">Comments : </label>
                        <span>{{$remarks}}</span>
                    </div>
                    @endif
                </span>
            </div>
        </div>    
    @elseif(in_array('petition_process_completed_msg', $visible_fields))
        <div class="row">
            <div class="completed visa-request-section petition_process_section">
                <img src="{{asset('images/completed-icon.svg')}}" alt="" class="completed-icon">
                <span>{{$completed_messages[$section_name]}}</span>
            </div>
        </div>
    @elseif(in_array('petition_process_reject_msg', $visible_fields))
        <div class="row">
            <div class="closed visa-request-section petition_process_section">
                <img src="{{asset('images/closed-icon.svg')}}" alt="" class="closed-icon">
                <span>{{$reject_message}}</span>
            </div>
        </div>
    @else
        <div id="petition-process-section" class="visa-request-section petition_process_section container-fluid form-content">
            <div class="row">
                @php($input_key = "INP_108")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{$field_attr[$input_key]['lable_name']  }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::select($field_attr[$input_key]['input_name'], ['' => 'Select']+$select_options[$input_key], $us_manager_id, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $us_manager ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_109")
                @if(in_array($input_key, $visible_fields) && isset($origin_based_inputs[$origin]))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{$field_attr[$input_key]['lable_name']  }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $inszoom_id, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $inszoom_id ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_110")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{$field_attr[$input_key]['lable_name']  }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::select($field_attr[$input_key]['input_name'], ['' => 'Select']+$select_options[$input_key], $entity_id, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $entity ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_111")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{$field_attr[$input_key]['lable_name']  }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::select($field_attr[$input_key]['input_name'], ['' => 'Select']+$select_options[$input_key], $attorneys_id, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $attorneys ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_112")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{$field_attr[$input_key]['lable_name']  }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $petition_file_date, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $petition_file_date ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_113")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{$field_attr[$input_key]['lable_name']  }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $receipt_no, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $receipt_no ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_114")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{$field_attr[$input_key]['lable_name']  }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $petition_start_date, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $petition_start_date ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_115")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{$field_attr[$input_key]['lable_name']  }}</label>
                            @if(in_array($input_key, $editable_fields))
                                {{ Form::text($field_attr[$input_key]['input_name'], $petition_end_date, $field_attr[$input_key]['attributes']) }}
                            @else
                                <p>{{ $petition_end_date ?? "NA" }}</p>
                            @endif
                        @endif
                    </div>
                @endif
                @php($input_key = "INP_116")
                @if(in_array($input_key, $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(array_key_exists($input_key, $field_attr))
                            <label for="{{ $field_attr[$input_key]['attributes']['id'] }}" class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                            <img src="{{ asset('images/info.svg') }}" alt="info icon for file upload" data-html="true" data-toggle="tooltip" data-placement="right" data-original-title="Documents allowed: png, jpg, jpeg, pdf, docx <br> Maximum file size allowed: 5MB">
                            <?php
                                $input_name = $field_attr[$input_key]['input_name'];
                                $petition_file_names = isset($petition_file_path) ? explode(',', $petition_file_path) : [];
                                $file_count = isset($petition_file_names) ? count($petition_file_names) : 0;
                                $display_name = isset($file_count) ? $file_count." files selected" : null;
                                $file_size = isset($petition_file_path) && file_exists(public_path($petition_file_path)) ? filesize(public_path($petition_file_path)) : 0;
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
                                    <input type="hidden" name="{{$input_name}}" value="{{$petition_file_path}}">
                                    @if($file_count)
                                        <div class="file-upload-container">
                                            @foreach($petition_file_names as $petition_file_name)
                                                <?php
                                                    $file_path = $petition_file_name;
                                                    $file_name = basename($petition_file_name);
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
                                            @foreach($petition_file_names as $petition_file_name)
                                                <?php
                                                    $file_path = $petition_file_name;
                                                    $file_name = basename($petition_file_name);
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
        </div>
    @endif
@endif
