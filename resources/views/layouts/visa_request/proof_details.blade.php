@php
    $action_enabled = in_array($status_id, [0,"STAT_01", "STAT_28"]);
    $table_visible = true;
    if(!in_array($status_id, [0,"STAT_01", "STAT_28"]))
        $table_visible = isset($proof_details) && is_array($proof_details) && count($proof_details)
@endphp  
<link rel="stylesheet" href="{{ asset('css/file-upload.css') }}">
<script src="{{ asset('js/file-upload.js') }}"></script>
@if(!isset($edit_id) || $table_visible)
<div class="row table-row">
    <table class="table table-responsive" id="proof-details-table">
        <thead>
            <tr>
                @php($input_key = "INP_026")
                @if(in_array($input_key, $visible_fields))
                    @if(array_key_exists($input_key, $field_attr))
                        <th class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</th>
                    @endif
                @endif
                @php($input_key = "INP_027")
                @if(in_array($input_key, $visible_fields))
                    @if(array_key_exists($input_key, $field_attr))
                        <th class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</th>
                    @endif
                @endif
                @php($input_key = "INP_028")
                @if(in_array($input_key, $visible_fields))
                    @if(array_key_exists($input_key, $field_attr))
                        <th class="{{ $mandatory_class_list[$input_key] }}">
                            {{ $field_attr[$input_key]['lable_name'] }}
                            <img src="{{ asset('images/info.svg') }}" alt="info icon for file upload" data-html="true" data-toggle="tooltip" data-placement="right" data-original-title="Documents allowed: png, jpg, jpeg, pdf <br> Maximum file size allowed: 5MB">
                        </th>
                    @endif
                @endif
                @php($input_key = "INP_029")
                @if(in_array($input_key, $visible_fields))
                    @if(array_key_exists($input_key, $field_attr))
                        <th class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</th>
                    @endif
                @endif
                @php($input_key = "INP_030")
                @if(in_array($input_key, $visible_fields))
                    @if(array_key_exists($input_key, $field_attr))
                        <th class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</th>
                    @endif
                @endif
                @php($input_key = "INP_031")
                @if(in_array($input_key, $visible_fields))
                    @if(array_key_exists($input_key, $field_attr))
                        <th class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</th>
                    @endif
                @endif
                @php($input_key = "INP_032")
                @if(in_array($input_key, $visible_fields))
                    @if(array_key_exists($input_key, $field_attr))
                        <th class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</th>
                    @endif
                @endif
                @php($input_key = "INP_033")
                @if(in_array($input_key, $visible_fields))
                    @if(array_key_exists($input_key, $field_attr))
                        <th class="{{ $mandatory_class_list[$input_key] }}">{{ $field_attr[$input_key]['lable_name'] }}</th>
                    @endif
                @endif
                @if($action_enabled)
                    <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if(isset($proof_details) && is_array($proof_details) && count($proof_details))
                @foreach($proof_details as $details)
                    @php(extract($details))
                    <?php
                        $proof_value = isset($proof_value) ? json_decode($proof_value, true) : [];
                        extract($proof_value);
                        $proof_name = isset($proof_name) ? $proof_name : null;
                        $proof_number = isset($proof_number) ? $proof_number : null;
                        $proof_type = isset($proof_type) ? $proof_type : null;
                        $proof_issue_date = ($proof_type!="pancard")?(isset($proof_issue_date) ? date('d-M-Y', strtotime($proof_issue_date)) : null):null;
                        $proof_expiry_date = ($proof_type!="pancard")?(isset($proof_expiry_date) ? date('d-M-Y', strtotime($proof_expiry_date)) : null):null;
                        $proof_issued_place = ($proof_type!="pancard")?(isset($proof_issued_place) ? $proof_issued_place : null):null;
                        $original_name = isset($original_name) ? $original_name : null;
                        $system_name = isset($system_name) ? $system_name : null;
                        
                        $proof_type_id = isset($proof_type_id) ? $proof_type_id : null;
                        $proof_request_for = isset($proof_request_for) ? $proof_request_for : null;
                        $proof_request_for = $proof_request_for == "employee" ? "self" : $proof_request_for;
                        $request_for_code = isset($request_for_code) ? $request_for_code : null;
                        $request_for_code = $request_for_code == "RF_13" ? "RF_05" : $request_for_code;
                        $file_reference_id = isset($file_reference_id) ? $file_reference_id : null;
                        $upload_path = isset($upload_path) ? $upload_path : null;
                        $file_path = "$upload_path/$system_name";
                        $file_size = file_exists(public_path($file_path)) ? filesize(public_path($file_path)) : 0;
                    ?>
                    <tr>
                        @php($input_key = "INP_026")
                        @if(in_array($input_key, $visible_fields))
                            @if(array_key_exists($input_key, $field_attr))
                                @if(in_array($input_key, $editable_fields))
                                    <td>{{ Form::select($field_attr[$input_key]["input_name"], ['' => 'Select']+$select_options[$input_key], $proof_type_id, $field_attr[$input_key]["attributes"]) }}</td>
                                @else
                                    <td>{{ $proof_type ?? "NA" }}</td>
                                @endif
                            @endif
                        @endif
                        @php($input_key = "INP_027")
                        @if(in_array($input_key, $visible_fields))
                            @if(array_key_exists($input_key, $field_attr))
                                @if(in_array($input_key, $editable_fields))
                                    <td>{{ Form::select($field_attr[$input_key]["input_name"], ['' => 'Select']+$select_options[$input_key], $request_for_code, $field_attr[$input_key]["attributes"]) }}</td>
                                @else
                                    <td>{{ $proof_request_for ?? "NA" }}</td>
                                @endif
                            @endif
                        @endif
                        @php($input_key = "INP_028")
                        @if(in_array($input_key, $visible_fields))
                            @if(array_key_exists($input_key, $field_attr))
                                @if(in_array($input_key, $editable_fields))
                                    <td>
                                        <div class="file-wrapper">
                                            <div class="file-info">
                                                <span class="info">{{ $system_name ? "1 file selected" : "" }}</span>
                                                <img src="{{ asset('images/layer.svg') }}" alt="file upload icon" class="upload-icon">
                                            </div>
                                            {{ Form::file($field_attr[$input_key]["input_name"], $field_attr[$input_key]["attributes"]) }}
                                            <input type="hidden" class="proof_file_path proof-details-fields" value="{{$system_name}}" name="proof_file_path">
                                            @if($system_name)
                                                <div class="file-upload-container">
                                                    <div class="row-item">
                                                        <div class="file-name">
                                                            <a href="/{{ $file_path }}" download="{{ $original_name }}" target="_blank">{{$original_name}}</a>
                                                        </div>
                                                        <div class="file-action">
                                                            <span class="file-size">{{ $file_size }}</span>
                                                            <img src="{{ asset('images/close.svg') }}" class='file-remove'>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                @else
                                    <td><a href="/{{ $file_path }}" download="{{ $original_name }}" target="_blank">{{$original_name}}</a></td>
                                @endif
                            @endif
                        @endif
                        @php($input_key = "INP_029")
                        @if(in_array($input_key, $visible_fields))
                            @if(array_key_exists($input_key, $field_attr))
                                @if(in_array($input_key, $editable_fields))
                                    <td>{{ Form::text($field_attr[$input_key]["input_name"], $proof_number, $field_attr[$input_key]["attributes"]) }}</td>
                                @else
                                    <td>{{ $proof_number ?? "NA" }}</td>
                                @endif
                            @endif
                        @endif
                        @php($input_key = "INP_030")
                        @if(in_array($input_key, $visible_fields))
                            @if(array_key_exists($input_key, $field_attr))
                                @if(in_array($input_key, $editable_fields))
                                    <td>{{ Form::text($field_attr[$input_key]["input_name"], $proof_name, $field_attr[$input_key]["attributes"]) }}</td>
                                @else
                                    <td>{{ $proof_name }}</td>
                                @endif
                            @endif
                        @endif
                        @php($input_key = "INP_031")
                        @if(in_array($input_key, $visible_fields))
                            @if(array_key_exists($input_key, $field_attr))
                                @if(in_array($input_key, $editable_fields))
                                    <td>{{ Form::text($field_attr[$input_key]["input_name"], $proof_issue_date, $field_attr[$input_key]["attributes"]) }}</td>
                                @else
                                    <td>{{ $proof_issue_date }}</td>
                                @endif
                            @endif
                        @endif
                        @php($input_key = "INP_032")
                        @if(in_array($input_key, $visible_fields))
                            @if(array_key_exists($input_key, $field_attr))
                                @if(in_array($input_key, $editable_fields))
                                    <td>{{ Form::text($field_attr[$input_key]["input_name"], $proof_expiry_date, $field_attr[$input_key]["attributes"]) }}</td>
                                @else
                                    <td>{{ $proof_expiry_date }}</td>
                                @endif
                            @endif
                        @endif
                        @php($input_key = "INP_033")
                        @if(in_array($input_key, $visible_fields))
                            @if(array_key_exists($input_key, $field_attr))
                                @if(in_array($input_key, $editable_fields))
                                    <td>{{ Form::text($field_attr[$input_key]["input_name"], $proof_issued_place, $field_attr[$input_key]["attributes"]) }}</td>
                                @else
                                    <td>{{ $proof_issued_place }}</td>
                                @endif
                            @endif
                        @endif
                        @if($action_enabled)
                            <td>
                                <div class="proof-action-btn-grp">
                                    <button class="proof-action-btn delete-btn">
                                        <img src="{{ asset('images/delete.svg') }}" alt="Delete proof details button">
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr>
                    @php($input_key = "INP_026")
                    @if(in_array($input_key, $visible_fields))
                        @if(array_key_exists($input_key, $field_attr))
                            @if(in_array($input_key, $editable_fields))
                                <td>{{ Form::select($field_attr[$input_key]["input_name"], ['' => 'Select']+$select_options[$input_key], null, $field_attr[$input_key]["attributes"]) }}</td>
                            @else
                                <td></td>
                            @endif
                        @endif
                    @endif
                    @php($input_key = "INP_027")
                    @if(in_array($input_key, $visible_fields))
                        @if(array_key_exists($input_key, $field_attr))
                            @if(in_array($input_key, $editable_fields))
                                <td>{{ Form::select($field_attr[$input_key]["input_name"], ['' => 'Select']+$select_options[$input_key], null, $field_attr[$input_key]["attributes"]) }}</td>
                            @else
                                <td></td>
                            @endif
                        @endif
                    @endif
                    @php($input_key = "INP_028")
                    @if(in_array($input_key, $visible_fields))
                        @if(array_key_exists($input_key, $field_attr))
                            @if(in_array($input_key, $editable_fields))
                                <td>
                                    <div class="file-wrapper">
                                        <img src="{{ asset('images/layer.svg') }}" alt="file upload icon" class="upload-icon">
                                        {{ Form::file($field_attr[$input_key]["input_name"], $field_attr[$input_key]["attributes"]) }}
                                        <input type="hidden" class="proof_file_path proof-details-fields" value="" name="proof_file_path">
                                    </div>
                                </td>
                            @else
                                <td></td>
                            @endif
                        @endif
                    @endif
                    @php($input_key = "INP_029")
                    @if(in_array($input_key, $visible_fields))
                        @if(array_key_exists($input_key, $field_attr))
                            @if(in_array($input_key, $editable_fields))
                                <td>{{ Form::text($field_attr[$input_key]["input_name"], null, $field_attr[$input_key]["attributes"]) }}</td>
                            @else
                                <td></td>
                            @endif
                        @endif
                    @endif
                    @php($input_key = "INP_030")
                    @if(in_array($input_key, $visible_fields))
                        @if(array_key_exists($input_key, $field_attr))
                            @if(in_array($input_key, $editable_fields))
                                <td>{{ Form::text($field_attr[$input_key]["input_name"], null, $field_attr[$input_key]["attributes"]) }}</td>
                            @else
                                <td></td>
                            @endif
                        @endif
                    @endif
                    @php($input_key = "INP_031")
                    @if(in_array($input_key, $visible_fields))
                        @if(array_key_exists($input_key, $field_attr))
                            @if(in_array($input_key, $editable_fields))
                                <td>{{ Form::text($field_attr[$input_key]["input_name"], null, $field_attr[$input_key]["attributes"]) }}</td>
                            @else
                                <td></td>
                            @endif
                        @endif
                    @endif
                    @php($input_key = "INP_032")
                    @if(in_array($input_key, $visible_fields))
                        @if(array_key_exists($input_key, $field_attr))
                            @if(in_array($input_key, $editable_fields))
                                <td>{{ Form::text($field_attr[$input_key]["input_name"], null, $field_attr[$input_key]["attributes"]) }}</td>
                            @else
                                <td></td>
                            @endif
                        @endif
                    @endif
                    @php($input_key = "INP_033")
                    @if(in_array($input_key, $visible_fields))
                        @if(array_key_exists($input_key, $field_attr))
                            @if(in_array($input_key, $editable_fields))
                                <td>{{ Form::text($field_attr[$input_key]["input_name"], null, $field_attr[$input_key]["attributes"]) }}</td>
                            @else
                                <td></td>
                            @endif
                        @endif
                        @if($action_enabled)
                            <td>
                                <div class="proof-action-btn-grp">
                                    <button class="proof-action-btn delete-btn">
                                        <img src="{{ asset('images/delete.svg') }}" alt="Delete proof details button">
                                    </button>
                                </div>
                            </td>
                        @endif
                    @endif
                </tr>
            @endif
        </tbody>
        @if($action_enabled)
            <tfoot>
                <tr>
                    <td colspan="9"><img src="{{ asset('images/add.svg') }}" alt="" id="add_proof_btn"></td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
@endif
<div class="visa-stamping-notes">
    <div class="row">
        <div class="col-md-12">
            <p class="visa-stamping-notes-content"><span class="visa-stamping-notes-header">Note : </span>As per process, We can sponser the visa cost for your spouse and two children. Since you have choose more than two children the travel cost has to be borne by you.</p>
        </div>
    </div>
</div>
