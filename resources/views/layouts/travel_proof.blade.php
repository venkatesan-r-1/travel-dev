@php
    $visible_fields = array_key_exists('visible_fields',$field_details) ? $field_details['visible_fields'] : [];
    $field_attr = array_key_exists('field_attr',$field_details) ? $field_details['field_attr'] : [];
    $editable_fields = array_key_exists('editable_fields',$field_details) ? $field_details['editable_fields'] : [];
    $field_attr = json_decode(json_encode($field_attr),true);
    $field_attr = array_map( fn($e) => array_key_exists('attributes', $e) ? array_replace($e,['attributes' => (array)json_decode($e['attributes'],true)]) : $e, $field_attr );

    $proof_type_list = isset($field_details) ? $field_details['select_options']['proof_type'] : [];
    $request_for = isset($field_details) ? $field_details['select_options']['request_for'] : [];
    $request_for_list = isset($field_details) ? $field_details['select_options']['request_for'] : [];
    $traveller_address = null;
    $phone_no = null;
    $email = null;
    $dob = null;
    $status=null;
    $nationality=null;
    $visa_travel_flag=0;
    $travel_visa_proof_mapping=['PR_TY_03_01'=>'PR_TY_02_01' ,'PR_TY_03_02'=>'PR_TY_02_02'];
    $travel_visa_request_for_mapping=['RF_08'=>'RF_05','RF_14'=>'RF_06'];
    
    if(isset($edit_id))
    {
        if(isset($request_details))
        {
            $traveller_address = property_exists($request_details, 'traveller_address') ? $request_details->traveller_address : '';
            $phone_no = property_exists($request_details, 'phone_no') ? $request_details->phone_no : '';
            $email = property_exists($request_details, 'email') ? $request_details->email : '';
            $dob =property_exists($request_details, 'dob') ? ( $request_details->dob ? date('d-M-Y', strtotime($request_details->dob)) : 'NA' ) : '';
            $status = property_exists($request_details, 'status_id') ? ($request_details->status_id ? $request_details->status_id : null) : null;
            $nationality = property_exists($request_details, 'nationality') ? ($request_details->nationality ? $request_details->nationality : null) : null;
        }
        if(isset($proof_details))
        {
            $proof_details = json_decode(json_encode($proof_details),true);
        }
    }
    if(isset($visa_link_details)) {
        $traveller_address = array_key_exists('address', $visa_link_details) ? $visa_link_details['address'] : null;
        $dob = array_key_exists('date_of_birth', $visa_link_details) ? $visa_link_details['date_of_birth'] : null;

        //used to fetch proof details only when the request fetch form the visa page
        //added by barath on 25-Jul-2024
        if(isset($visa_link_details['proof_related_details'])){
            $proof_details= json_decode(json_encode($visa_link_details['proof_related_details']),true);
            $visa_travel_flag=1;
            $edit_id=1;
        }
        
    }
@endphp
<link rel="stylesheet" href="{{ asset('css/file-upload.css') }}">
<script src="{{ asset('js/file-upload.js') }}"></script>
    <div class="">
        <div class="form-section fields_div">
            <div class="container-fluid">
			    <div class="row">
				    <div class="col-md-12" style="margin-bottom:5px;margin-top: 20px;">
					    <h3>Proof details</h3>
				    </div>
			    </div>
		    </div>
            <div class="container-fluid form-content">
                <div class="row additional_traveller_details">
                    @php($field_name = "INP_022")
                    @if(in_array($field_name, $visible_fields))
                            <div class="{{ $module == 'MOD_03' ? 'col-md-2 col-sm-12' : 'col-md-3 col-sm-12' }}">
                                @if(array_key_exists($field_name,$field_attr))
                                    <label for="{{ $field_attr[$field_name]['attributes']['id'] }}" class="form-label">{{$field_attr[$field_name]['lable_name']}}</label>
                                    @if(in_array($field_name, $editable_fields))
                                        {{ Form::textarea($field_attr[$field_name]['input_name'],$traveller_address,$field_attr[$field_name]['attributes']) }}
                                    @else
                                        <p>{{ $traveller_address }}</p>
                                    @endif
                                @endif
                        </div>
                    @endif
                    @php($field_name = "INP_023")
                    @if(in_array($field_name, $visible_fields))
                    <div class="{{ $module == 'MOD_03' ? 'col-md-2 col-sm-12' : 'col-md-3 col-sm-12' }}">
                                @if(array_key_exists($field_name,$field_attr))
                                    <label for="{{ $field_attr[$field_name]['attributes']['id'] }}" class="form-label">{{$field_attr[$field_name]['lable_name']}}</label>
                                    @if(in_array($field_name, $editable_fields))
                                        {{ Form::text($field_attr[$field_name]['input_name'],$email,$field_attr[$field_name]['attributes']) }}
                                    @else
                                        <p>{{ $email }}</p>
                                    @endif
                                @endif
                        </div>
                    @endif
                    @php($field_name = "INP_024")
                    @if(in_array($field_name, $visible_fields))
                    <div class="{{ $module == 'MOD_03' ? 'col-md-2 col-sm-12' : 'col-md-3 col-sm-12' }}">
                                @if(array_key_exists($field_name,$field_attr))
                                    <label for="{{ $field_attr[$field_name]['attributes']['id'] }}" class="form-label">{{$field_attr[$field_name]['lable_name']}}</label>
                                    @if(in_array($field_name, $editable_fields))
                                        {{ Form::text($field_attr[$field_name]['input_name'],$phone_no,$field_attr[$field_name]['attributes']); }}
                                    @else
                                        <p>{{ $phone_no }}</p>
                                    @endif
                                @endif
                        </div>
                    @endif
                    @php($field_name = "INP_025")
                    @if(in_array($field_name, $visible_fields))
                    <div class="{{ $module == 'MOD_03' ? 'col-md-2 col-sm-12' : 'col-md-3 col-sm-12' }}">
                                @if(array_key_exists($field_name,$field_attr))
                                    <label for="{{ $field_attr[$field_name]['attributes']['id'] }}" class="form-label">{{$field_attr[$field_name]['lable_name']}}</label>
                                    @if(in_array($field_name, $editable_fields))
                                        {{ Form::text($field_attr[$field_name]['input_name'],$dob,$field_attr[$field_name]['attributes']); }}
                                    @else
                                        <p>{{ $dob }}</p>
                                    @endif
                                @endif
                        </div>
                    @endif
                    @php($field_name = "INP_076")
                    @if(in_array($field_name, $visible_fields) && $module == 'MOD_03')
                            <div class="col-md-2">
                                @if(array_key_exists($field_name,$field_attr))
                                    <label for="{{ $field_attr[$field_name]['attributes']['id'] }}" class="form-label">{{$field_attr[$field_name]['lable_name']}}</label>
                                    @if(in_array($field_name, $editable_fields))
                                        {{ Form::text($field_attr[$field_name]['input_name'],$nationality,$field_attr[$field_name]['attributes']); }}
                                    @else
                                        <p>{{ $nationality ? $nationality : '-' }}</p>
                                    @endif
                                @endif
                        </div>
                    @endif
                </div>
                <div class="row table-row">
                    <table class="table table-responsive" id="proof-details-table">
                        <thead>
                            @php($field_name='INP_026')
                            @if(in_array($field_name, $visible_fields))
                                @if(array_key_exists($field_name, $field_attr))
                                    <th>{{ $field_attr[$field_name]['lable_name'] }}</th>
                                @endif
                            @endif
                            @php($field_name='INP_027')
                            @if(in_array($field_name, $visible_fields))
                                @if(array_key_exists($field_name, $field_attr))
                                    <th>{{ $field_attr[$field_name]['lable_name'] }}</th>  
                                @endif
                            @endif
                            @php($field_name='INP_028')
                            @if(in_array($field_name, $visible_fields))
                                @if(array_key_exists($field_name, $field_attr))                                    
                                    <th>{{ $field_attr[$field_name]['lable_name'] }} <img src="{{ asset('images/info.svg') }}" alt="info icon for file upload" data-html="true" data-toggle="tooltip" data-placement="right" data-original-title="Documents allowed: png, jpg, jpeg, pdf <br> Maximum file size allowed: 5MB"></th>  
                                @endif
                            @endif
                            @php($field_name='INP_029')
                            @if(in_array($field_name, $visible_fields))
                                @if(array_key_exists($field_name, $field_attr))
                                    <th>{{ $field_attr[$field_name]['lable_name'] }}</th>  
                                @endif
                            @endif
                            @php($field_name='INP_030')
                            @if(in_array($field_name, $visible_fields))
                                @if(array_key_exists($field_name, $field_attr))
                                    <th>{{ $field_attr[$field_name]['lable_name'] }}</th>  
                                @endif
                            @endif
                            @php($field_name='INP_031')
                            @if(in_array($field_name, $visible_fields))
                                @if(array_key_exists($field_name, $field_attr))
                                    <th>{{ $field_attr[$field_name]['lable_name'] }}</th>
                                @endif
                            @endif
                            @php($field_name='INP_032')
                            @if(in_array($field_name, $visible_fields))
                                @if(array_key_exists($field_name, $field_attr))
                                    <th>{{ $field_attr[$field_name]['lable_name'] }}</th> 
                                @endif
                            @endif
                            @php($field_name='INP_033')
                            @if(in_array($field_name, $visible_fields))
                                @if(array_key_exists($field_name, $field_attr))
                                    <th>{{ $field_attr[$field_name]['lable_name'] }}</th>  
                                @endif
                            @endif
                            @if($status == null || $status == "STAT_01")
                                <th>Action</th>
                            @endif
                        </thead>
                        <tbody>
                            @if(isset($edit_id) && count($proof_details))
                                @foreach($proof_details as $proof_detail)
                                    <tr class="proof_details_section">
                                        @php( $proof_type = array_key_exists('proof_type', $proof_detail) ? $proof_detail['proof_display_name'] : null )
                                        @php( $proof_type_id = array_key_exists('proof_type_id', $proof_detail) ? $proof_detail['proof_type_id'] : null )
                                        @php( $request_for_code = array_key_exists('request_for_code', $proof_detail) ? $proof_detail['request_for_code'] : null )
                                        @php( $proof_request_for = array_key_exists('proof_request_for', $proof_detail) ? $proof_detail['proof_request_for'] : null )
                                        @php( $additional_attr = [ 'disabled' => in_array($request_for_code, ['RF_01','RF_05','RF_08']) && false ? true : false ] )
                                        @php( $proof_type_id = $visa_travel_flag?$travel_visa_proof_mapping[$proof_type_id]:$proof_type_id )
                                        @php( $request_for_code = $visa_travel_flag?$travel_visa_request_for_mapping[$request_for_code]:$request_for_code)
                                        
                                        <td>
                                            @php($field_name = "INP_026")
                                            @if(in_array($field_name, $visible_fields))
                                                @if(array_key_exists($field_name,$field_attr))
                                                    @if(in_array($field_name, $editable_fields))
                                                        {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select']+$proof_type_list,$proof_type_id,$field_attr[$field_name]['attributes'] + $additional_attr) }}
                                                    @else
                                                        <p>{{ $proof_type }}</p>
                                                    @endif
                                                @endif 
                                            @endif
                                        </td>
                                        <td>
                                            @php( $additional_attr = [ 'disabled' => in_array($request_for_code, ['RF_01','RF_05','RF_08']) && false ? true : false ] )
                                            @php($field_name = "INP_027")
                                            @if(in_array($field_name, $visible_fields))
                                                @if(array_key_exists($field_name,$field_attr))
                                                    @if(in_array($field_name, $editable_fields))
                                                        {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select']+$request_for,$request_for_code,$field_attr[$field_name]['attributes'] + $additional_attr) }}
                                                    @else
                                                        <p>{{ $proof_request_for }}</p>
                                                    @endif
                                                @endif 
                                            @endif
                                        </td>
                                        <td>
                                            <?php
                                                $display_name = array_key_exists('original_name', $proof_detail) ? $proof_detail['original_name'] : null;
                                                $file_name = array_key_exists('system_name', $proof_detail) ? $proof_detail['system_name'] : null;
                                                $file_path = is_null($file_name) ? null : "proof_file_uploads/".$file_name;
                                                $file_size = !is_null($file_path) ? (file_exists(public_path($file_path)) ? filesize(public_path($file_path)) : 0 ):0;
                                                $file_reference_id = array_key_exists('file_reference_id', $proof_detail) ? $proof_detail['file_reference_id'] : "";
                                            ?>
                                            @php($field_name = "INP_028")
                                            @if(in_array($field_name, $visible_fields))
                                                @if(array_key_exists($field_name, $field_attr))
                                                    @if(in_array($field_name, $editable_fields))
                                                        <div class="file-wrapper">
                                                            <div class="file-info">
                                                                <span class="info">{{ $file_name ? "1 file selected" : "" }}</span>
                                                                <img src="{{ asset('images/layer.svg') }}" alt="file upload icon" class="upload-icon">
                                                            </div>
                                                            {{ Form::file($field_attr[$field_name]['input_name'],$field_attr[$field_name]['attributes'] + ['file-reference-id' => $file_reference_id]) }}
                                                            <input type="hidden" value="{{$file_name}}" name="proof_file_path" class="proof-details-fields">
                                                            @if($file_name)
                                                                <div class="file-upload-container">
                                                                    <div class="row-item">
                                                                        <div class="file-name">
                                                                            <a href="/{{ $file_path }}" download="{{ $display_name }}" target="_blank">{{$display_name}}</a>
                                                                        </div>
                                                                        <div class="file-action">
                                                                            <span class="file-size">{{ $file_size }}</span>
                                                                            <img src="{{ asset('images/close.svg') }}" class='file-remove'>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <a href="/{{ $file_path }}" download="{{ $display_name }}" target="_blank">{{$display_name}}</a>
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                        @php( $proof_value = array_key_exists('proof_value', $proof_detail) ? json_decode($proof_detail['proof_value'],true) : [] )
                                        <td>
                                            @php( $proof_number = array_key_exists('proof_number', $proof_value) ? $proof_value['proof_number'] : null )
                                            @php( $additional_attr = [ 'disabled' => in_array($request_for_code, ['RF_01','RF_05','RF_08']) && false ? true : false ] )
                                            @php($field_name = "INP_029")
                                            @if(in_array($field_name, $visible_fields))
                                                @if(array_key_exists($field_name,$field_attr))
                                                    {{-- @php($updated_class = str_replace(" not_required","",$field_attr[$field_name]['attributes']['class']))
                                                    @php( $field_attr[$field_name]['attributes']['class'] = isset($proof_number) ? $updated_class : $field_attr[$field_name]['attributes']['class']." not_required" ) --}}
                                                    @if(in_array($field_name, $editable_fields))
                                                        {{ Form::text($field_attr[$field_name]['input_name'],$proof_number,$field_attr[$field_name]['attributes'] + $additional_attr) }}
                                                    @else
                                                        <p>{{ $proof_number }}</p>
                                                    @endif
                                                @endif 
                                            @endif
                                        </td>
                                        <td>
                                            @php( $proof_name = array_key_exists('proof_name', $proof_value) ? $proof_value['proof_name'] : null )
                                            @php( $additional_attr = [ 'disabled' => in_array($request_for_code, ['RF_01','RF_05','RF_08']) && false ? true : false ] )
                                            @php($field_name = "INP_030")
                                            @if(in_array($field_name, $visible_fields))
                                                @if(array_key_exists($field_name,$field_attr))
                                                    {{-- @php($updated_class = str_replace(" not_required","",$field_attr[$field_name]['attributes']['class']))
                                                    @php( $field_attr[$field_name]['attributes']['class'] = isset($proof_name) ? $updated_class : $updated_class." not_required" ) --}}
                                                    @if(in_array($field_name, $editable_fields))
                                                        {{ Form::text($field_attr[$field_name]['input_name'],$proof_name,$field_attr[$field_name]['attributes'] + $additional_attr) }}
                                                    @else
                                                        <p>{{ $proof_name }}</p>
                                                    @endif
                                                @endif 
                                            @endif                                    
                                        </td>
                                        <td>
                                            @php( $proof_issue_date = array_key_exists('proof_issue_date', $proof_value) ? ($proof_value['proof_issue_date'] ? date('d-M-Y', strtotime($proof_value['proof_issue_date'] )) : null) : null )
                                            @php( $additional_attr = [ 'disabled' => in_array($request_for_code, ['RF_01','RF_05','RF_08']) && false ? true : false ] )
                                            @php($field_name = "INP_031")
                                            @if(in_array($field_name, $visible_fields))
                                                @if(array_key_exists($field_name,$field_attr))
                                                    {{-- @php($updated_class = str_replace(" not_required","",$field_attr[$field_name]['attributes']['class']))
                                                    @php( $field_attr[$field_name]['attributes']['class'] = isset($proof_issue_date) ? $updated_class : $updated_class." not_required" ) --}}
                                                    @if(in_array($field_name, $editable_fields))
                                                        {{ Form::text($field_attr[$field_name]['input_name'],$proof_issue_date,$field_attr[$field_name]['attributes']+$additional_attr) }}
                                                    @else
                                                        <p>{{ $proof_issue_date }}</p>
                                                    @endif
                                                @endif 
                                            @endif                                    
                                        </td>
                                        <td>
                                            @php( $proof_expiry_date = array_key_exists('proof_expiry_date', $proof_value) ? ($proof_value['proof_expiry_date'] ? date('d-M-Y', strtotime($proof_value['proof_expiry_date'] )) : null) : null )
                                            @php( $additional_attr = [ 'disabled' => in_array($request_for_code, ['RF_01','RF_05','RF_08']) && false ? true : false ] )
                                            @php($field_name = "INP_032")
                                            @if(in_array($field_name, $visible_fields))
                                                @if(array_key_exists($field_name,$field_attr))
                                                    {{-- @php($updated_class = str_replace(" not_required","",$field_attr[$field_name]['attributes']['class']))
                                                    @php( $field_attr[$field_name]['attributes']['class'] = isset($proof_expiry_date) ? $updated_class : $updated_class." not_required" ) --}}
                                                    @if(in_array($field_name, $editable_fields))
                                                        {{ Form::text($field_attr[$field_name]['input_name'],$proof_expiry_date,$field_attr[$field_name]['attributes'] + $additional_attr) }}
                                                    @else
                                                        <p>{{ $proof_expiry_date }}</p>
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @php( $proof_issued_place = array_key_exists('proof_issued_place', $proof_value) ? $proof_value['proof_issued_place'] : null )
                                            @php( $additional_attr = [ 'disabled' => in_array($request_for_code, ['RF_01','RF_05','RF_08']) && false ? true : false ] )
                                            @php($field_name = "INP_033")
                                            @if(in_array($field_name, $visible_fields))
                                                @if(array_key_exists($field_name,$field_attr))
                                                    {{-- @php($updated_class = str_replace(" not_required","",$field_attr[$field_name]['attributes']['class']))
                                                    @php( $field_attr[$field_name]['attributes']['class'] = isset($proof_issued_place) ? $updated_class : $updated_class." not_required" ) --}}
                                                    @if(in_array($field_name, $editable_fields))
                                                        {{ Form::text($field_attr[$field_name]['input_name'],$proof_issued_place,$field_attr[$field_name]['attributes']+$additional_attr) }}
                                                    @else
                                                        <p>{{ $proof_issued_place }}</p>
                                                    @endif
                                                @endif 
                                            @endif
                                        </td>
                                        @if($status == null || $status == "STAT_01")
                                            <td>
                                                <div class="proof-action-btn-grp">
                                                    <button class="proof-action-btn delete-btn">
                                                        <img src="{{ asset('images/delete.svg') }}" alt="Delete proof details button">
                                                    </button>
                                                    {{-- <button class="proof-action-btn update-btn">
                                                        <img src="{{ asset('images/update.svg') }}" alt="Update proof details button">
                                                    </button> --}}
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr class="proof_details_section">
                                    <td>
                                        @php($field_name = "INP_026")
                                        @if(in_array($field_name, $visible_fields))
                                            @if(array_key_exists($field_name,$field_attr))
                                                @if(in_array($field_name, $editable_fields))
                                                    {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select']+$proof_type_list,null,$field_attr[$field_name]['attributes']) }}
                                                @else
                                                    <p>-</p>
                                                @endif
                                            @endif 
                                        @endif
                                    </td>
                                    <td>
                                        @php($field_name = "INP_027")
                                        @if(in_array($field_name, $visible_fields))
                                            @if(array_key_exists($field_name,$field_attr))
                                                @if(in_array($field_name, $editable_fields))
                                                    {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select']+$request_for_list,null,$field_attr[$field_name]['attributes']) }}
                                                @else
                                                    <p>-</p>
                                                @endif
                                            @endif 
                                        @endif
                                    </td>
                                    <td>
                                        @php($field_name = "INP_028")
                                        @if(in_array($field_name, $visible_fields))
                                            @if(array_key_exists($field_name, $field_attr))
                                                @if(in_array($field_name, $editable_fields))
                                                    <div class="file-wrapper">
                                                        <img src="{{ asset('images/layer.svg') }}" alt="file upload icon" class="upload-icon">
                                                        {{ Form::file($field_attr[$field_name]['input_name'],$field_attr[$field_name]['attributes']+['file-reference-id' => '']) }}
                                                        <input type="hidden" class="proof_file_path proof-details-fields" value="" name="proof_file_path">
                                                    </div>
                                                @else
                                                    <p>-</p>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($field_name = "INP_029")
                                        @if(in_array($field_name, $visible_fields))
                                            @if(array_key_exists($field_name,$field_attr))
                                                @if(in_array($field_name, $editable_fields))
                                                    {{ Form::text($field_attr[$field_name]['input_name'],null,$field_attr[$field_name]['attributes']) }}
                                                @else
                                                    <p>-</p>
                                                @endif
                                            @endif 
                                        @endif
                                    </td>
                                    <td>
                                        @php($field_name = "INP_030")
                                        @if(in_array($field_name, $visible_fields))
                                            @if(array_key_exists($field_name,$field_attr))
                                                @if(in_array($field_name, $editable_fields))
                                                    {{ Form::text($field_attr[$field_name]['input_name'],null,$field_attr[$field_name]['attributes']) }}
                                                @else
                                                    <p>-</p>
                                                @endif
                                            @endif 
                                        @endif                                    
                                    </td>
                                    <td>
                                        @php($field_name = "INP_031")
                                        @if(in_array($field_name, $visible_fields))
                                            @if(array_key_exists($field_name,$field_attr))
                                                @if(in_array($field_name, $editable_fields))
                                                    {{ Form::text($field_attr[$field_name]['input_name'],null,$field_attr[$field_name]['attributes']) }}
                                                @else
                                                    <p>-</p>
                                                @endif
                                            @endif 
                                        @endif                                    
                                    </td>
                                    <td>
                                        @php($field_name = "INP_032")
                                        @if(in_array($field_name, $visible_fields))
                                            @if(array_key_exists($field_name,$field_attr))
                                                @if(in_array($field_name, $editable_fields))
                                                    {{ Form::text($field_attr[$field_name]['input_name'],null,$field_attr[$field_name]['attributes']) }}
                                                @else
                                                    <p>-</p>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($field_name = "INP_033")
                                        @if(in_array($field_name, $visible_fields))
                                            @if(array_key_exists($field_name,$field_attr))
                                                @if(in_array($field_name, $editable_fields))
                                                    {{ Form::text($field_attr[$field_name]['input_name'],null,$field_attr[$field_name]['attributes']) }}
                                                @else
                                                    <p>-</p>
                                                @endif
                                            @endif 
                                        @endif
                                    </td>
                                    @if($status == null || $status == "STAT_01")
                                        <td>
                                            <div class="proof-action-btn-grp">
                                                <button class="proof-action-btn delete-btn">
                                                    <img src="{{ asset('images/delete.svg') }}" alt="Delete proof details button">
                                                </button>
                                                {{-- <button class="proof-action-btn update-btn">
                                                    <img src="{{ asset('images/update.svg') }}" alt="Update proof details button">
                                                </button> --}}
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endif
                        </tbody>
                        @if($status == null || $status == 'STAT_01')
                            <tfoot>
                                <tr>
                                    <td colspan="9"><img src="{{ asset('images/add.svg') }}" alt="" id="add_proof_btn"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                    <div id="proof_error"></div>
                </div>
            </div>
        </div>
   
    <!-- </div> -->
    <script>
        $(document).ready(function () {
            // Adding tooltip
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });
        }); 
    </script>

