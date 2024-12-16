<?php

//dd($field_details);
$visible_fields=$field_details['visible_fields'];
$editable_fields=$field_details['editable_fields'];
$field_attr=$field_details['field_attr'];
//dd($field_attr);
$options=$field_details['select_options'] ?? null;
//$options=isset($field_details['select_options']);

$field_name=["INP_002","INP_003","INP_004","INP_005","INP_042","INP_046","INP_047"];
if(isset($edit_id) && count($travelling_details)){
    $loop_count = count($travelling_details);

}else{
    $loop_count = 1;
}
$additional_attributes = [];
if(isset($can_extend_date) && $can_extend_date)
{
    $date_related_fields = ['INP_004','INP_005'];
    $visible_fields = array_merge($visible_fields, ['travel_extend_btn']);
    $editable_fields = array_merge($editable_fields, $date_related_fields);
    if(in_array($travel_type_id, ['TRV_01_01', 'TRV_02_01', 'TRV_03_01']))
    {
        $additional_attributes['disabled'] = 'true';
        $additional_attributes['traveling_type'] = 'one-way';
    }
    $field_attr_date = array_intersect_key($field_attr, array_flip($date_related_fields));
    $attributes_mapping = array_combine( array_keys($field_attr_date), array_column($field_attr_date, 'attributes') );
    $attributes_mapping = array_map( function ($e) {
        $attr = json_decode($e, true);
        $attr['class'] = is_array($attr) && array_key_exists('input_class', $attr) ? $attr['input_class']." extendable_dates" : "extendable_dates";
        return json_encode($attr);
    }, $attributes_mapping );
    foreach($field_attr_date as $inp_key => $attr) {
        $field_attr_date[$inp_key]['attributes'] = $attributes_mapping[$inp_key];
    }
    $field_attr = array_replace($field_attr, $field_attr_date);
}
    // To upload ticket and its cost
    $ticket_upload_related_fields = ['INP_006'];
    $can_view_ticket = []; $can_edit_ticket = [];
    $allowed_status = ['STAT_12', 'STAT_13'];
    $allowed_users = ['DOM_TCK_ADM', 'TRV_PROC_TICKET'];
    $allowed_modules = ["MOD_01", "MOD_02"];
    if(isset($request_details) && in_array($request_details->status_id, $allowed_status) && in_array($request_details->module, $allowed_modules,)){
        if(Auth::User()->has_any_role_code($allowed_users) && !$is_ticket_processed && $request_details->status_id === "STAT_12" ) {
            $can_edit_ticket = $ticket_upload_related_fields;
            $can_view_ticket = $ticket_upload_related_fields;
        } else if($is_ticket_processed || $request_details->status_id === "STAT_13") {
            $can_view_ticket = $ticket_upload_related_fields;
        }
    }

    $ticket_related_details = [];
    if(isset($travelling_details))
    {
        $ticket_travel_details = json_decode(json_encode($travelling_details),true);
        $ticket_related_fields = ['ticket_display_name', 'ticket_file_location', 'ticket_cost', 'ticket_file_reference_id'];
        $ticket_related_details = array_map(fn($e) => array_intersect_key($e, array_flip($ticket_related_fields)), $ticket_travel_details);
        $ticket_related_details = array_filter(array_map(fn($e) => array_filter($e, fn($d) => $d !== null), $ticket_related_details));
    }
    // data from visa_link
    if(isset($traveling_details_obj)) $travelling_details[0] = $traveling_details_obj;
?>
<div class="table-section" id="traveling_section">
    {{-- <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3>Travelling details</h3>
            </div>
        </div>
    </div> --}}
    <div class="container-fluid table-content">
        <div class="tr_details_container">
            <table class="table">
                <thead>
                    <tr class="{{ count($can_view_ticket) ? 'ticket_upload_row' : '' }}">
                        @foreach($field_attr as $inp_key=>$fields)
                            @if(in_array($inp_key,$field_name))
                                @if(in_array($inp_key, $visible_fields))
                                    <th style="width:15%" class="{{ in_array($inp_key, $editable_fields) ? 'required-field' : '' }}">{{$fields['lable_name']}}</th>
                                @endif
                            @endif
                        @endforeach
                        {{-- Ticket upload block --}}
                        @if(count($can_view_ticket))
                            @php $input_name = 'INP_006'; @endphp
                            @if (in_array($input_name, $can_view_ticket))
                                <th class="{{ in_array($input_name, $can_edit_ticket) ? 'required-field' : '' }}">
                                @if(array_key_exists($input_name, $field_attr))
                                    {{ $field_attr[$input_name]['lable_name']  }}
                                    <img src="{{ asset('images/info.svg') }}" alt="info icon for file upload" data-html="true" data-toggle="tooltip" data-placement="right" data-original-title="Documents allowed: png, jpg, jpeg, pdf <br> Maximum file size allowed: 5MB">
                                @endif
                                </th>
                            @endif
                            @php $input_name = 'INP_085'; @endphp
                            @if (in_array($input_name, $can_view_ticket))
                                <th class="{{ in_array($input_name, $can_edit_ticket) ? 'required-field' : '' }}">
                                @if(array_key_exists($input_name, $field_attr))
                                    {{ $field_attr[$input_name]['lable_name'] }}
                                @endif
                                </th>
                            @endif
                        @endif
                        {{-- Block ends here --}}
                        @if(!isset($request_details) || $request_details->status_id=="STAT_01")
                        <th style="display:none;width:5%" id="action-th">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="tr_details_row">
                    @for($row=0; $row<$loop_count; $row++)
                        <tr id="travelling_details-{{$row + 1}}" class='travelling_details' tr_row_id="{{isset($travelling_details) ? $travelling_details[$row]->travelling_details_row_id:''}}">
                        @foreach($field_attr as $inp_key=>$fields)
                            @if(in_array($inp_key,$field_name))
                                @if(in_array($inp_key, $visible_fields))
                                    <?php $attributes = json_decode($fields['attributes'], true);?>
                                    <td class="{{$attributes['input_class']}}">
                                        @if(in_array($inp_key, $editable_fields))
                                            <input type="hidden" name="hidden_{{$fields['input_name']}}" value="{{isset($travelling_details)? $travelling_details[$row]->{$fields['input_name']} : '' }}">
                                            @if($attributes['type'] == 'select')
                                                {{ Form::select($fields['input_name'], ['' => 'Select'] +$options[$fields['input_name']],
                                                    isset($travelling_details)? $travelling_details[$row]->{$fields['input_name']}:null, 
                                                    $attributes) }}
                                            @elseif($attributes['type'] == 'text')
                                                @php
                                                    if($inp_key == "INP_005")
                                                        $attributes = $attributes+$additional_attributes;
                                                @endphp
                                                {{ Form::text($fields['input_name'],
                                                    isset($travelling_details)? $travelling_details[$row]->{$fields['input_name']}:null,
                                                    $attributes); }}
                                                @if($inp_key == "INP_046")
                                                    @php
                                                        $to_country = isset( $travelling_details[$row]->to_country) ? ( $travelling_details[$row]->to_country ) : null;
                                                        $visa_number_list = isset($options["visa_number"]) ? $options["visa_number"] : [];
                                                        $visa_number_list = isset($visa_number_list[$to_country]) ? (array)$visa_number_list[$to_country] : [];
                                                        $visa_number_list = array_combine($visa_number_list, $visa_number_list);
                                                        $visa_number_class = isset($visa_number_list) && is_array($visa_number_list) && count($visa_number_list) > 1 ? "visa_number_alt multiple_visa" : "visa_number_alt";
                                                        $visa_number_alt = isset($travelling_details[$row]->visa_number) ? $travelling_details[$row]->visa_number : null;
                                                    @endphp
                                                    {{ Form::select("visa_number_alt", ['' => 'Select']+$visa_number_list, $visa_number_alt , ['class' => $visa_number_class]) }}
                                                @endif
                                            @endif
                                        @else
                                            @if(in_array($fields['input_name'],['from_country','to_country','from_city','to_city']))
                                                {{isset($travelling_details[$row]->{$fields['input_name']})? $travelling_details[$row]->{$fields['input_name'].'_name'}:'-'}}
                                            @else
                                                {{ isset($travelling_details) && $fields['input_name'] == 'visa_type_code' ? $travelling_details[$row]->visa_name : ($travelling_details[$row]->{$fields['input_name']} ?? '-') }}                                            @endif
                                        @endif  
                                    </td>
                                @endif
                            @endif
                        @endforeach
                        {{-- Ticket upload block --}}
                        @if (count($can_view_ticket))
                            @php $input_name = 'INP_006'; @endphp
                            @if (in_array($input_name, $can_view_ticket))
                                <td>
                                @if (array_key_exists($input_name, $field_attr))
                                    @php $attr = json_decode($field_attr[$input_name]['attributes'],true); @endphp
                                    @if (in_array($input_name, $can_edit_ticket))
                                        @if(isset($ticket_related_details) && is_array($ticket_related_details) && count($ticket_related_details))
                                            <?php
                                                $detail = array_key_exists( $row , $ticket_related_details ) ? $ticket_related_details[$row] : null;
                                                $file_path = $detail && array_key_exists( 'ticket_file_location', $detail ) ? $detail['ticket_file_location'] : null;
                                                $display_name = $detail && array_key_exists( 'ticket_display_name', $detail ) ? $detail['ticket_display_name'] : null;
                                                clearstatcache();
                                                $display_name =explode(",",$display_name);
                                                $ticket_file_path =explode(",",$file_path);
                                               // $file_name = isset($file_path) ? basename($file_path) : null;
                                                //$file_size = is_file(public_path($file_path)) ? filesize(public_path($file_path)) : null;
                                                $ticket_file_reference_id = $detail && array_key_exists( 'ticket_file_reference_id', $detail ) ? $detail['ticket_file_reference_id'] : null;
                                                $add_attr = ['ticket_file_reference_id'=> $ticket_file_reference_id];
                                            ?>
                                            <div class="file-wrapper">
                                                <div class="file-info">
                                                    <span class="info">{{count($display_name)}} file selected</span>
                                                    <img src="{{ asset('images/layer.svg') }}" alt="file upload icon" class="upload-icon">
                                                </div>
                                                {{ Form::file($field_attr[$input_name]['input_name'], $attr+$add_attr) }}
                                                <input type="hidden" name="ticket_file_path" value="{{$file_path}}">
                                                <div class="file-upload-container">
                                                    @foreach($ticket_file_path as $key => $ticket_file)
                                                        <?php
                                                        $file_path = $ticket_file;
                                                        $file_name = isset($file_path) ? basename($file_path) : null;
                                                        $file_name = isset($display_name[$key]) ? $display_name[$key] : null;
                                                        $file_size = is_file(public_path($file_path)) ? filesize(public_path($file_path)) : null;
                                                        ?>
                                                            <div class="row-item">
                                                                <div class="file-name">
                                                                    <a href="{{ $file_path }}" download="{{ $file_name }}" target="_blank">{{ $file_name }}</a>
                                                                </div>
                                                                <div class="file-action">
                                                                    <spap class="file-size">{{ $file_size }}</spap>
                                                                    <img class="file-remove" src="{{ URL::asset('images/close.svg') }}" alt="file-remove-icon"  >
                                                                </div>
                                                            </div>
                                                    @endforeach    
                                                </div>
                                            </div>        
                                        @else
                                            <div class="file-wrapper">
                                                <img src="{{ asset('images/layer.svg') }}" alt="file upload icon" class="upload-icon">
                                                {{ Form::file($field_attr[$input_name]['input_name'], $attr) }}
                                                <input type="hidden" name="ticket_file_path" class="not_required_field">
                                            </div>
                                        @endif
                                    @else
                                        @if(isset($ticket_related_details) && is_array($ticket_related_details) && count($ticket_related_details))
                                            <?php
                                                $detail = array_key_exists( $row , $ticket_related_details ) ? $ticket_related_details[$row] : null;
                                                $file_path = $detail && array_key_exists( 'ticket_file_location', $detail ) ? $detail['ticket_file_location'] : null;
                                                $display_name = $detail && array_key_exists( 'ticket_display_name', $detail ) ? $detail['ticket_display_name'] : null;
                                                clearstatcache();
                                                $display_name =explode(",",$display_name);
                                                //dd($display_name);
                                                $ticket_file_path =explode(",",$file_path);
                                               // $file_name = isset($file_path) ? basename($file_path) : null;
                                                //$file_size = is_file(public_path($file_path)) ? filesize(public_path($file_path)) : null;
                                                $ticket_file_reference_id = $detail && array_key_exists( 'ticket_file_reference_id', $detail ) ? $detail['ticket_file_reference_id'] : null;
                                                $add_attr = ['ticket_file_reference_id'=> $ticket_file_reference_id];
                                            ?>
                                            <div class="file-wrapper">
                                        <p class="file-view-link" style="color: #337ab7; cursor: pointer">{{count($display_name)}} file selected</p>
                                        <div class="file-upload-container">
                                            @foreach($ticket_file_path as $key => $ticket_file)
                                                        <?php
                                                        $file_path = $ticket_file;
                                                        $file_name = isset($file_path) ? basename($file_path) : null;
                                                        $file_name = isset($display_name[$key]) ? $display_name[$key] : null;
                                                        $file_size = is_file(public_path($file_path)) ? filesize(public_path($file_path)) : null;
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



                                        @else
                                            {{ '-' }}
                                        @endif
                                    @endif
                                @endif
                                </td>
                            @endif
                            @php $input_name = 'INP_085'; @endphp
                            @if (in_array($input_name, $can_view_ticket))
                                <td>
                                @if (array_key_exists($input_name, $field_attr))
                                    @php $attr = json_decode($field_attr[$input_name]['attributes'],true); @endphp
                                    @if (in_array($input_name, $can_edit_ticket))
                                        <?php
                                            $detail = array_key_exists( $row , $ticket_related_details ) ? $ticket_related_details[$row] : null;
                                            $ticket_cost = $detail && array_key_exists('ticket_cost', $detail) ? $detail['ticket_cost'] : null;
                                        ?>
                                        {{ Form::text($field_attr[$input_name]['input_name'], $ticket_cost, $attr) }}
                                    @else
                                        <?php
                                            $detail = array_key_exists( $row , $ticket_related_details ) ? $ticket_related_details[$row] : null;
                                            $ticket_cost = $detail && array_key_exists('ticket_cost', $detail) ? $detail['ticket_cost'] : null;
                                        ?>
                                        {{ $ticket_cost ?? "NA" }}
                                    @endif
                                @endif
                                </td>
                            @endif
                        @endif
                        {{-- Block ends here --}}
                        @if(!isset($request_details) || $request_details->status_id=="STAT_01")
                            <td class="action-cell" style="display:none">
                                <img src='/images/delete.svg' class="remove_img disabled" id="delete_btn" />
                            </td>
                        @endif    
                        </tr>
                    @endfor
                </tbody>
                @if(!isset($request_details) || $request_details->status_id=="STAT_01")
                <tfoot class="foot-section">
                    <tr>
                        <td colspan="8" class="action-cell" style="display:none">
                            <img src='/images/add.svg' class="add_img disabled" id="add_btn" />
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
            @if(in_array('travel_extend_btn', $visible_fields))
                @php( $redirect_url = url()->previous() != url()->current() ? url()->previous() : '/report' )
                <button id="travel_extend_btn" value="travel_extend" class="primary-button" style="display:block; margin: 5px 0 0 auto;" disabled redirect_url="{{ $redirect_url }}">Extend</button>
            @endif
        </div>
        <div class="visa_error"></div>
    </div>
</div>
