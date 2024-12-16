@php
    $previous_anticiapted_details = [];
    $approver_currency = null;
    $approver_anticipated_amount = null;
    if(isset($edit_id))
    {
        $previous_anticiapted_details = json_decode(json_encode($anticipated_details),true);
        $approver_currency = property_exists($request_details, 'approver_currency') ? $request_details->approver_currency : 'NA';
        $approver_anticipated_amount = property_exists($request_details, 'approver_anticipated_amount') ? $request_details->approver_anticipated_amount : 'NA';
    }
    $previous_anticiapted_currency_list = array_column($previous_anticiapted_details, 'anticipated_currency');
    $previous_anticiapted_currency_code = array_reduce($previous_anticiapted_currency_list, function ($carry, $item) {
        if(is_null($carry) || $carry == $item){
            $carry = $item;
        }else{
            $carry = "";
        }
        return $carry;
    });
    if($previous_anticiapted_currency_code == ""){
        $need_to_disable = false; 
    }else{
        $need_to_disable = true;
    }
    $approver_currency_code = $previous_anticiapted_currency_code;
    $attribute_replacement = ['disabled' => $need_to_disable];
    $visible_fields = array_key_exists('visible_fields', $field_details) ? $field_details['visible_fields'] : [];
    $editable_fields = array_key_exists('editable_fields', $field_details) ? $field_details['editable_fields'] : [];
    $anticipate_cost_related_fields = ["INP_082", "INP_083"];
    $anticipate_cost_related_fields = array_intersect($anticipate_cost_related_fields, $editable_fields);
    $select_options = array_key_exists('select_options', $field_details) ? $field_details['select_options'] : [];
    $approver_currency_list = array_key_exists('approver_currency_code', $select_options) ? $select_options['approver_currency_code'] : [];
@endphp
@if($billable_edit||!isset($edit_id)
||(isset($edit_id)&&in_array($request_details->status_id,$config_variables->CONFIG['COMMENTS_ENABLED_STATUS'])))
<div class="bill-to-client form-section" id="bill_to_client">
        <div class="container-fluid form-content" id="billed_to_client" style="margin-bottom:5px;margin-top:30px">
            <div class="row">
                @if($billable_edit)
                    <div class="col-md-3 col-sm-12 radio_required">
                        <label class="form-label">Billed to client</label>&nbsp;<span class="alert_required" style="display:none;color:red">required</span><br>
                        <label><input type="radio" id="bill_to_client_1" name="billed_to_client" {{$bill_to_client===1?'checked':''}} value=1 >Yes</label>
                        <label><input type="radio" id="bill_to_client_2" name="billed_to_client" {{$bill_to_client===0?'checked':''}} value=0 >No</label>
                    </div>
                @endif
                @if(is_array($anticipate_cost_related_fields) && count($anticipate_cost_related_fields))
                    @php($input_key = 'INP_082')
                    @if(in_array($input_key, $visible_fields))
                        <div class="col-md-3 col-sm-12" style="display:none;">
                            @if(array_key_exists($input_key, $field_attr))
                                <label class="form-label" for="{{$field_attr[$input_key]['attributes']['id']}}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                                @if(in_array($input_key, $editable_fields))
                                    {{ Form::select($field_attr[$input_key]['attributes']['id'], ['' => 'Select'] + $approver_currency_list, "CUR_12", array_replace($field_attr[$input_key]['attributes'],$attribute_replacement))}}
                                @endif
                            @endif
                        </div>
                    @endif
                    @php($input_key = 'INP_083') 
                    @if(in_array($input_key, $visible_fields))
                        <div class="col-md-3 col-sm-12" style="display:none;">
                            @if(array_key_exists($input_key, $field_attr))
                                <label class="form-label" for="{{$field_attr[$input_key]['attributes']['id']}}">{{ $field_attr[$input_key]['lable_name'] }}</label>
                                @if(in_array($input_key, $editable_fields))
                                    {{ Form::text($field_attr[$input_key]['attributes']['id'], $converted_amount, $field_attr[$input_key]['attributes'])}}
                                @endif
                            @endif
                        </div>
                    @endif
                @endif
                @if(!isset($edit_id)||(isset($edit_id)&&in_array(Auth::User()->aceid, [$request_details->travaler_id, $request_details->created_by])&&$request_details->status_id=='STAT_01'))
                    <div class="col-md-3 col-sm-12 radio_required">
                        <label for="INP_064" class="form-label">Remarks</label>
                        {{ Form::textarea('comments',$remarks,['class'=>'common_action_comments', 'cols'=>50,'rows'=>10]); }}
                    </div>
                @elseif(isset($edit_id)&&array_key_exists($request_details->status_id,$comments_enable_actions)&&Auth::User()->has_any_role_code($comments_enable_actions[$request_details->status_id])) 
                    <div class="col-md-3 col-sm-12 radio_required">
                        <label for="INP_064" class="form-label">Comments</label>
                        {{ Form::textarea('comments',$remarks,['class'=>'form-control common_action_comments','cols'=>50,'rows'=>10]); }}
                    </div>
                @endif
            </div>
        </div>
</div>
@endif
