<?php
$field_name=["INP_050","INP_051","INP_052","INP_053","INP_054"];  
if(isset($edit_id) && count($forex_details)){
    $loop_count = count($forex_details);
}else{
    $loop_count = 1;
}
$can_process = Auth::User()->has_any_role_code(['TRV_PROC_FOREX']);
?>
@php
    $forex_process_comments = [];
    //To calculate the total advance amount for all currency format
    $forex_details_array = json_decode(json_encode($forex_details),true);
    $total_advance_amount = [];
    foreach($forex_details_array as $detail)
    {
        $transaction_type = array_key_exists('transaction_type', $detail) ? $detail['transaction_type'] : null;
        $currency = array_key_exists('currency', $detail) ? $detail['currency'] : null;
        $amount = array_key_exists('amount', $detail) ? (float)$detail['amount'] : null;
        if(array_key_exists($currency, $total_advance_amount)){
            if($transaction_type == 'forex_load')
                $total_advance_amount[$currency] += $amount;
            if($transaction_type == 'forex_return')
                $total_advance_amount[$currency] -= $amount;

        }
        else{
            if($transaction_type == 'forex_load')
                $total_advance_amount[$currency] = $amount;
            if($transaction_type == 'forex_return')
                $total_advance_amount[$currency] = -$amount;
        }
    }
    $total_advance_amount = is_array($total_advance_amount) && count($total_advance_amount) ?
    implode(', ', array_map(fn($k,$v) => "$k $v", array_keys($total_advance_amount),$total_advance_amount))
    : '-';
    foreach($status_details as $details)
    {
        if(is_array($details) && key($details) != 'remarks_details'){
            $old_status = array_key_exists('old_status_code', $details) ? $details['old_status_code'] : null;
            $new_status = array_key_exists('new_status_code', $details) ? $details['new_status_code'] : null;
            $action = array_key_exists('action', $details) ? $details['action'] : null;
            if( in_array($old_status, ['STAT_12','STAT_13']) && in_array($new_status, ['STAT_12','STAT_13']) && in_array($action, ['save_forex_process','forex_process', 'update_forex_process'])){
                $comments = array_key_exists('comments', $details) ? $details['comments'] : null;
                if($comments) array_push($forex_process_comments, $details['comments']);
            }
        }
    }
    $forex_process_comments = array_filter($forex_process_comments);
    if(isset($edit_id))
        $forex_processed = DB::table('trf_approval_matrix_tracker')->where([['request_id', $edit_id],['active', 1],['flow_code', 'TRV_PROC_FOREX'],['is_completed', 1]])->exists();
@endphp
<label>Forex reload</label>
<table class="table" id="load_list" style="width:100%;">
	<thead class="thead-inverse">
        <tr>
            <th style="width:10%"></th>
            @foreach($field_attr as $inp_key=>$fields)
                @if(in_array($inp_key,$field_name))
                    @if(in_array($inp_key, $visible_fields))
                        <th style="width:10%" class="required-field">{{$fields['lable_name']}}</th>
                    @endif
                @endif
            @endforeach
            @if(in_array($request_details->status_id,['STAT_12','STAT_13']) && Auth::User()->has_any_role_code(['TRV_PROC_FOREX']))
            <th style="width:10%">Action</th>
            @endif
        </tr>
    </thead>
    <tbody id="tr_load_list_body">
    @for($row=0; $row<$loop_count; $row++)
    @if(!count($forex_details) || (count($forex_details) && $forex_details[$row]->transaction_type=="forex_load"))
        @if(!count($forex_details) && !Auth::User()->has_any_role_code(['TRV_PROC_FOREX']))
        <tr id="tr_load_list_row-1" class="forex_list" forex_row_id="{{isset($forex_details) && count($forex_details) ? $forex_details[$row]->forex_details_row_id:''}}">
            <td colspan="5">No data found</td>
        </tr>
        @else
        <tr id="tr_load_list_row-1" class="forex_list" forex_row_id="{{isset($forex_details) && count($forex_details) ? $forex_details[$row]->forex_details_row_id:''}}">
        <input type="hidden" name="transaction_type" value="forex_load">
            <td></td>
            @foreach($field_attr as $inp_key=>$fields)
                @if(in_array($inp_key,$field_name))
                    @if(in_array($inp_key, $visible_fields))
                    <?php $attributes = json_decode($fields['attributes'], true);?>
                        <td>
                            @if(in_array($inp_key, $editable_fields))
                                @if($attributes['type'] == 'select')
                                    {{ Form::select($fields['input_name'], ['' => 'Select'] +$options[$fields['input_name']],
                                                    isset($forex_details) && count($forex_details) ? $forex_details[$row]->{$fields['input_name']}:null, 
                                                    $attributes) }}    
                                @elseif($attributes['type'] == 'text')
                                {{ Form::text($fields['input_name'],
                                    isset($forex_details) && count($forex_details)? $forex_details[$row]->{$fields['input_name']}:null,$attributes); }}  
                                @else
                                    {{ Form::textarea($fields['input_name'],isset($forex_details) && count($forex_details) ? $forex_details[$row]->{$fields['input_name']}:null,$attributes); }} 
                                @endif
                            @else
                            @php
                                $forex_field_name = $fields['input_name'];                                    
                                if($forex_field_name == "mode_code")
                                    $forex_field_name = "mode";
                                if($forex_field_name == "currency_code")
                                    $forex_field_name = "currency";
                            @endphp
                            {{isset($forex_details[$row]->$forex_field_name)? $forex_details[$row]->$forex_field_name:'-'}}
                            @endif  
                        </td>
                    @endif
                @endif
            @endforeach
            @if(in_array($request_details->status_id,['STAT_12','STAT_13']) && Auth::User()->has_any_role_code(['TRV_PROC_FOREX']))
            <td><img src="/images/delete.svg" class="load_delete" id="load_delete"></img></td>
            @endif
        </tr>
        @endif
        @endif
    @endfor
    </tbody>
    @if(in_array($request_details->status_id,['STAT_12','STAT_13']) && Auth::User()->has_any_role_code(['TRV_PROC_FOREX']))
    <tfoot class="foot-section">
        <tr>
            <td colspan="8">
            <img src='/images/add.svg' class="add_img" id="load_add_btn" />
            </td>
        </tr>
    </tfoot>
    @endif
</table>
<br>
<label>Amount returned by employee</label>
<table class="table" id="return_list" style="width:100%;">
	<thead class="thead-inverse">
        <tr>
            @foreach($field_attr as $inp_key=>$fields)
                @if(in_array($inp_key,$field_name))
                    @if(in_array($inp_key, $visible_fields))
                        <th style="width:10%" class="required-field">{{$fields['lable_name']}}</th>
                    @endif
                @endif
            @endforeach
            @if(in_array($request_details->status_id,['STAT_12','STAT_13']) && Auth::User()->has_any_role_code(['TRV_PROC_FOREX']))
            <th style="width:10%">Action</th>
            @endif
        </tr>
    </thead>
    <tbody id="tr_return_list_body">
    @for($row=0; $row<$loop_count; $row++)
    @if(!count($forex_details) || (count($forex_details) && $forex_details[$row]->transaction_type=="forex_return"))
        @if(!count($forex_details) && !Auth::User()->has_any_role_code(['TRV_PROC_FOREX']))
        <tr id="tr_load_list_row-1" class="forex_list" forex_row_id="{{isset($forex_details) && count($forex_details) ? $forex_details[$row]->forex_details_row_id:''}}">
            <td colspan="5">No data found</td>
        </tr>
        @else
        <tr id="tr_return_list_row-1" class="forex_list" forex_row_id="{{isset($forex_details) && count($forex_details) ? $forex_details[$row]->forex_details_row_id:''}}">
        <input type="hidden" name="transaction_type" value="forex_return">
            @foreach($field_attr as $inp_key=>$fields)
                @if(in_array($inp_key,$field_name))
                    @if(in_array($inp_key, $visible_fields))
                        <?php $attributes = json_decode($fields['attributes'], true);?>
                        <td>
                            @if(in_array($inp_key, $editable_fields))
                                @if($attributes['type'] == 'select')
                                {{ Form::select($fields['input_name'], ['' => 'Select'] +$options[$fields['input_name']],
                                                    isset($forex_details) && count($forex_details) ? $forex_details[$row]->{$fields['input_name']}:null, 
                                                    $attributes) }}                                    
                                @elseif($attributes['type'] == 'text')
                                {{ Form::text($fields['input_name'],
                                    isset($forex_details) && count($forex_details)? $forex_details[$row]->{$fields['input_name']}:null,$attributes); }}  
                                @else
                                    {{ Form::textarea($fields['input_name'],isset($forex_details) && count($forex_details) ? $forex_details[$row]->{$fields['input_name']}:null,$attributes); }} 
                                @endif
                            @else
                            @php
                                $forex_field_name = $fields['input_name'];                                    
                                if($forex_field_name == "mode_code")
                                    $forex_field_name = "mode";
                                if($forex_field_name == "currency_code")
                                    $forex_field_name = "currency";
                            @endphp
                            {{isset($forex_details[$row]->$forex_field_name)? $forex_details[$row]->$forex_field_name:'-'}}
                            @endif 
                        </td> 
                    @endif
                @endif
            @endforeach
            @if(in_array($request_details->status_id,['STAT_12','STAT_13']) && Auth::User()->has_any_role_code(['TRV_PROC_FOREX']))
            <td><img src="/images/delete.svg" class="return_delete" id="return_delete"></img></td>
            @endif
        </tr>
        @endif
        @endif
    @endfor
    </tbody>
    @if(in_array($request_details->status_id,['STAT_12','STAT_13']) && Auth::User()->has_any_role_code(['TRV_PROC_FOREX']))
    <tfoot class="foot-section">
        <tr>
            <td colspan="8">
            <img src='/images/add.svg' class="add_img" id="return_add_btn" />
            </td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="row">
    <?php $field_name = "INP_061" ?>
        @if(in_array($field_name, $visible_fields))
        <div class="col-md-3 col-sm-12">
            @if(array_key_exists($field_name,$field_attr))
                <label class="required-field">{{$field_attr[$field_name]['lable_name']}}</label><br>
                <?php $attributes = json_decode($field_attr[$field_name]['attributes'], true); ?>
                    @if(in_array($field_name, $editable_fields))
                        {{ Form::textarea($field_attr[$field_name]['input_name'],null,  $attributes+['readonly'=>true]); }}
                    @else
                        <p>{{$total_advance_amount}}</p>
                    @endif
           @endif
        </div>
        @endif
    <?php $field_name = "INP_064" ?>
    @if(in_array($field_name, $visible_fields) && $can_process)
        <div class="col-md-3 col-sm-12 forex_list">
            @if(array_key_exists($field_name,$field_attr))
                <label class="required-field">{{$field_attr[$field_name]['lable_name']}}</label><br>
                <?php $attributes = json_decode($field_attr[$field_name]['attributes'], true); ?>
                    @if(in_array($field_name, $editable_fields))
                        {{ Form::textarea($field_attr[$field_name]['input_name'],null,  $attributes); }}
                    @else
                        <p>-</p>
                    @endif
           @endif
        </div>
    @endif
    @if(count($forex_process_comments))
        <div class="col-md-3 col-sm-12">
            <label class="form-label" for="forex-process-comments">Previous comments</label>
            <textarea name="forex-process-comments" id="forex-process-comments" cols="30" rows="10" readonly>{{ implode("\n\n", $forex_process_comments) }}</textarea>
        </div>
    @endif
    </div>
    <div class="btn-container">
        <a href={{url()->previous()}}><button class="secondary-button">Back</button></a>
    @if(in_array('save_forex_process', $visible_fields) && $can_process && !$forex_processed)
        <button value="save_forex_process" name="save_forex_process_btn" id="forex_process_btn" class="secondary-button">Save</button>
    @endif
    @if(in_array('forex_process', $visible_fields) && $can_process && !$forex_processed )
        <button value="forex_process" name="forex_process_btn" id="forex_process_btn" class=" primary-button">Forex Process</button>
    @endif
    @if(in_array('update_forex_process', $visible_fields) && $can_process && $forex_processed)
        <button value="update_forex_process" name="update_forex_process_btn" id="update_forex_process_btn" class="primary-button">Update</button>
    @endif
    </div>
</div>
