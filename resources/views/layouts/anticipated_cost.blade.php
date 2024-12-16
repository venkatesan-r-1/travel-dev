@php
    $page_navigation_no='';
    $visible_fields = $field_details['visible_fields'];
    $editable_fields = $field_details['editable_fields'];
    $field_attr = $field_details['field_attr'];
    $field_visible_status=['STAT_02'];
    $default_options = [
        'master_category' => [],
        'category' => [],
        'sub_category' => [],
    ];
    $options = $field_details['select_options'];
    $options = array_merge($default_options, $options);
    $approver_anticipated_details = [];
    $container_class = $module == "MOD_03" ? "visa-request-section gm_review_section gm_review_approval_section" : "";
    $sub_container_class = $module == "MOD_03" ? "form-content" : "";

    if(isset($edit_id)){
        $approver_anticipated_details = array_filter(array_intersect_key(get_object_vars($request_details), array_flip(['approver_currency_code', 'approver_currency', 'approver_anticipated_amount'])));
        $approval_tracker=json_decode(json_encode($approval_tracker_details),true);
        $users_involved=array_reduce(array_values($approval_tracker),function($carry,$item){
        $carry[$item['user_involved']]=$item['is_completed'];
        return $carry;
    },[]);
    }else{
        $approval_tracker_details=[];
        $users_involved=[];
    }

    $anticipated_section_fields = ['INP_034','INP_035','INP_036','INP_037','INP_038','INP_039'];
    $visible_fields = array_intersect($visible_fields,$anticipated_section_fields);
    $field_attr=array_replace(array_flip($anticipated_section_fields),$field_attr);
    
    $details = [];

     if($module=='MOD_03'){
        $loop_count = 1;
     }
     else {
        $loop_count = count($options['category']);   
    }

    //if the anticipated details has been already saved it should get the count from the DB
    if(isset($anticipated_details) && count($anticipated_details)){
            $loop_count = count($anticipated_details);
        } 
        $budget_error=false;
    foreach($anticipated_details as $key=>$val){
        $details[$key] = (array)$val;

        if(!is_null($bill_to_client) && $bill_to_client == 0 && isset($details[$key]['budget_success']) && $details[$key]['budget_success'] == 0) {
            $budget_error= true;
        }
    }
    $approver_flag = isset($approver_anticipated_details) && is_array($approver_anticipated_details) && count($approver_anticipated_details) ? true : false;
    $approver_anticipated_details_str = null;
    if($approver_flag)
    {
        $approver_currency = array_key_exists('approver_currency',$approver_anticipated_details) ? $approver_anticipated_details['approver_currency'] : null;
        $approver_anticipated_amount = array_key_exists('approver_anticipated_amount',$approver_anticipated_details) ? $approver_anticipated_details['approver_anticipated_amount'] : null;
        $approver_anticipated_details_str = implode(": ", [$approver_currency, $approver_anticipated_amount]);
        $approver_anticipated_details_str = formatAmount($approver_anticipated_details_str);
    }

    $option_list = array_values($options['category']);
    $categories_list=[];$option_category=[];

    // $ordered_categories_list=DB::table('trd_category')->where('active',1)->pluck('name')->toArray();
    // $ordered_categories_list=array_unique($ordered_categories_list);
    $ordered_categories_list=['Tickets','Accomodation','Conveyance','Perdiem','Others','Legal Fees','Postage & Courier','Filing Fees','Premium Processing Charges'];
    $category_for_request= array_intersect($ordered_categories_list,$option_list);
    usort($details, function ($a, $b) use ($category_for_request) {
        $pos_a = array_search($a['category_name'], $category_for_request);
        $pos_b = array_search($b['category_name'], $category_for_request);
        return $pos_a - $pos_b;
    });
    foreach ($category_for_request as $key => $value) {
        array_push($categories_list,$value);
        $option_category[array_search($value,$options['category'])] = $value;
    }
    $options['category']=$option_category;
    //var_dump(array_values($options['category']));
    
    $total_amount = [];
    if($details && is_array($details) && count($details))
    {
       foreach($details as $value)
       {
            $currency = array_key_exists('anticipated_currency_name', $value) ? $value['anticipated_currency_name'] : null;
            $amount = array_key_exists('amount', $value) ? (float)$value['amount'] : 0;
            if($currency && array_key_exists($currency, $total_amount))
                $total_amount[$currency] += $amount;
            else if($amount)
                $total_amount[$currency] = $amount;
       }
    }
    $total_amount_value = json_encode($total_amount);
    $total_amount = implode(' ', array_map(fn($k,$v) => "$k: ".round($v,2), array_keys($total_amount),$total_amount));
    $total_amount = $total_amount == "" ? 0 : $total_amount;
    // To hide the section before reviewed
    $is_visible = 1;
    if(count(array_intersect($editable_fields, $anticipated_section_fields)) == 0)
        $is_visible = count(json_decode(json_encode($anticipated_details),true));

    $tooltip_content = $module != "MOD_03" ? "The anticipated cost for the particular request. If the travel request is for return / multiple route, the sum of amount should be entered for the each categories." : "The anticipated cost for the particular visa request.";
    // Long term travel
    $long_term_travel = false;
    if( isset($edit_id) && $status_id == 'STAT_02' && isset($request_details->travel_purpose_id) && $request_details->travel_purpose_id == 'PUR_02_03' && Auth::User()->has_any_role_code(['AN_COST_FIN']))
        $long_term_travel = true;
//To check whether the respective_person_have access to view the anticipated cost details
@endphp
@if(count($visible_fields) && $is_visible && $is_td_visible)
<div class="table-section {{$container_class}}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12" style="margin-top:15px;margin-bottom:5px">
                <h3 class="traveldesk-header">Anticipated cost
                    <img src="/images/info.svg" alt="Anticipate cost details info" data-html="true" data-toggle="tooltip" data-placement="right" data-original-title="{{$tooltip_content}}">
                    @if(!$billable_edit&&!is_null($bill_to_client))
                         <span style="color: #ff8d6d;">(Billable - {{$bill_to_client==1?"Yes":"No"}})</span>
                    @endif
                    @if($budget_error)
                    <div class="budget_error system-message warning" style="display:block">
					<img src="/images/warning.svg" alt="Warning">
					<span>The budget has been exceeded / not available for the below categories</span>
                    </div>
                    @else
                    <div class="budget_error" style="display:none"></div>
                    @endif

                </h3>
            </div>
        </div>
    </div>
    <div class="container-fluid table-content">
        <div class="anticipated_cost_container">
            <table class="table">
                <thead>
                    <tr>
                        @foreach($field_attr as $inp_key=>$fields)
                            @if(in_array($inp_key, $visible_fields))
                                <th style="width: {{ $inp_key !== 'INP_039' ? '15%' : '' }}" class="{{ !in_array($inp_key,['INP_036','INP_039']) && in_array($inp_key, $editable_fields) ? 'required-field' : '' }}">{{$fields['lable_name']}}</th>
                            @endif
                        @endforeach
                        @if($module=='MOD_03' && in_array($request_details->status_id,$field_visible_status))
                            <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="anticipated_cost_body">
                    @for($row=0; $row<$loop_count; $row++)
                        @php
                            $excluded_row = 0;
                            $check_editable_based_on_traveldesk=$editable_fields;
                            $to_mark_completion_class='';
                            $categories_list_item= array_key_exists($row, $categories_list) ? $categories_list[$row] : null;
                            if(Auth::user()->has_any_role_code(['AN_COST_FAC']) && !in_array($categories_list_item,['Perdiem','Others'])){
                                $excluded_row = 0;
                                if(array_key_exists('AN_COST_FIN',$users_involved) && $users_involved['AN_COST_FAC']){// to make the fields only visible
                                    $check_editable_based_on_traveldesk=[];
                                    $details[$row]['anticipated_row_id']='';
                                    $to_mark_completion_class='travel-desk-completed';
                                }
                               
                            }elseif(Auth::user()->has_any_role_code(['AN_COST_FIN']) && in_array($categories_list_item,['Perdiem','Others'])){
                                $excluded_row = 0;
                                if(array_key_exists('AN_COST_FIN',$users_involved) && $users_involved['AN_COST_FIN']){// to make the fields only visible
                                    $check_editable_based_on_traveldesk=[];
                                    $details[$row]['anticipated_row_id']='';
                                    $to_mark_completion_class='travel-desk-completed';
                                }
                               
                            }else{
                                $excluded_row = 1;
                                if(((array_key_exists('AN_COST_FAC',$users_involved) && $users_involved['AN_COST_FAC']) || (array_key_exists('AN_COST_FAC',$users_involved) && $users_involved['AN_COST_FIN']))){
                                    $check_editable_based_on_traveldesk=[];
                                    $details[$row]['anticipated_row_id']='';
                                    $to_mark_completion_class='travel-desk-completed';
                                }else{
                                    $check_editable_based_on_traveldesk=$editable_fields;
                                    $to_mark_completion_class='';
                                }
                            }
                            if(!$billable_edit && !is_null($bill_to_client) && $bill_to_client == 0 && isset($details[$row]['budget_success']) && $details[$row]['budget_success'] == 0) {
                                $row_class = 'row-error';
                            }else{
                                $row_class = '';
                            }
                            $td_count = 0;
                            if(Auth::User()->has_any_role_code(['AN_COST_VISA']) && $module == "MOD_03")
                                $excluded_row = 0;
                        @endphp
                    <tr id="anticipated_cost_row-1" class="{{$to_mark_completion_class}} {{$row_class}}"  data-excluded_row="{{$excluded_row}}" data-row_no="{{isset($details[$row]['anticipated_row_id']) ? $details[$row]['anticipated_row_id'] : ''}}">
                        @foreach($field_attr as $inp_key=>$fields)
                            @php
                                $attributes = !is_array($fields['attributes']) ? json_decode($fields['attributes'], true) : $fields['attributes'];
                                if($excluded_row)
                                    $attributes['disabled'] = true;
                                else if(in_array('readonly',array_keys($attributes)))
                                    $attributes['disabled']=true;
                                else
                                    $attributes['disabled'] = false;
                                
                                if($module=='MOD_03' && in_array($inp_key,['INP_035'])){
                                    unset($attributes['disabled']);
                                    unset($attributes['readonly']);
                                }
                                if($long_term_travel && $categories_list_item == "Perdiem") {
                                    $attributes['disabled'] = true;
                                }
                            @endphp
                            @if(in_array($inp_key, $visible_fields))
                                <td>
                                {{-- Check if td_count is 0 to show the error image 
                                @if($td_count == 0)
                                @if(isset($details[$row]['budget_success']) && $details[$row]['budget_success'] == 0)
                                    <img class="cost-row-error tool-tip" data-toggle="tooltip" title="{{ $details[$row]['message']}}" src="/images/row_error.svg" alt="" style="display:none" />
                                    @if(!$billable_edit && !is_null($bill_to_client) && $bill_to_client == 0)
                                        <img class="cost-row-error tool-tip" data-toggle="tooltip" title="{{ $details[$row]['message'] }}" src="/images/row_error.svg" alt="" style="display:block" />
                                    @endif
                                @else
                                    <img class="cost-row-error tool-tip" data-toggle="tooltip" style="display:none" src="/images/row_error.svg" alt="" />
                                @endif
                                @endif
                                --}}
                                {{-- Increment the td_count --}}
                                @php $td_count++; @endphp
                                    @if(in_array($inp_key, $check_editable_based_on_traveldesk))
                                    {{-- $editable_fields --}}
                                        @if($attributes['type'] == 'select')
                                            @php
                                                $field_option = array_key_exists($fields['input_name'],$options) ? $options[$fields['input_name']] : [];
                                                if(!in_array($inp_key, ['INP_034','INP_035'])){
                                                    $field_option = [''=>'Select']+$field_option;
                                                }
                                                if(in_array($inp_key, ['INP_035']) && $module == 'MOD_03'){
                                                    $field_option = ['' => 'Select']+$field_option;
                                                }
                                                if($long_term_travel && $categories_list_item == "Perdiem" && $inp_key == 'INP_037') {
                                                    $details[$row][$fields['input_name']] = 'CUR_12';
                                                }
                                            @endphp
                                            {{ Form::select($fields['input_name'],$field_option,
                                                isset($details[$row][$fields['input_name']])?$details[$row][$fields['input_name']]:null,
                                                $attributes); }} 
                                        @elseif($attributes['type'] == 'text')
                                            @php
                                                if($long_term_travel && $categories_list_item == "Perdiem" && $inp_key == 'INP_038') {
                                                    $details[$row][$fields['input_name']] = 0;
                                                }
                                            @endphp
                                            {{ Form::text($fields['input_name'],
                                                isset($details[$row][$fields['input_name']])?$details[$row][$fields['input_name']]:null,
                                                $attributes); }} 
                                        @else
                                            {{ Form::textarea($fields['input_name'],
                                                isset($details[$row][$fields['input_name']])?$details[$row][$fields['input_name']]:null,
                                                $attributes); }} 
                                        @endif
                                    @else
                                    <?php 
                                    $class='';
                                    if (str_contains($attributes['class'], 'currency_format')) {
                                        $class .= 'currency_format';
                                    }
                                    if (str_contains($attributes['class'], 'anticipated_amount')) {
                                        $class .= ' anticipated_amount';
                                    }
                                    ?>
                                    
                                        <p class="{{ $class }}" name="{{ $fields['input_name'] }}">{{isset($details[$row][$fields['input_name'].'_name'])?$details[$row][$fields['input_name'].'_name']:'-'}}</p>
                                    @endif  
                                </td>
                            @endif
                            @php 
                                if(in_array($inp_key, ['INP_035'])){
                                    array_shift($options[$fields['input_name']]);
                                }
                            @endphp
                        @endforeach
                        @if($module=='MOD_03' && in_array($request_details->status_id,$field_visible_status))
                        <td><img class="delete_row" src="{{ asset('images/delete.svg') }}"></td>
                        @endif
                    </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr class="anticipated-amount-details-row">
                        <td colspan="3">
                            @if($approver_flag && ($module != "MOD_03"))
                                <label class="form-label">Approver anticipated amount - </label>
                                <span class=""> {{$approver_anticipated_details_str}}</span>
                            @endif
                        </td>
                        <td colspan="3">
                            <label class="form-label">Total amount - </label> 
                            <span id="total-anticipated-amount" previousvalue = "{{ $total_amount_value }}">{{ $total_amount }}</span>
                        </td>   
                    </tr>
                    @if(($module=='MOD_03' && in_array($request_details->status_id,$field_visible_status)))
                        <tr>
                            <td colspan="8">
                            <img src='/images/add.svg' class="add_img add_new_row"  />
                            </td>
                        </tr>
                    @endif
                </tfoot>
                
            </table>
        </div>
    </div>
</div>
@endif
<style>
    img[disabled]{
        cursor: not-allowed;
        background-color: white !important;
    }
    .anticipated-amount-details-row td
    {
        border: none;
    }
</style>

<script>   
$(document).ready(function(){
    $('#anticipated_cost_body tr').each(function () {
    var currency_type = $(this).closest('tr').find('p[name="anticipated_currency"]').text();
    currency_format_changes($(this).closest('tr'),currency_type,'anticipated_amount');
    updateTotalAmount();
  });
});
$(document).on('click','.delete_row',function(){
    var tbody = $(this).closest('tbody');
    var current_tr = $(this).closest('tr');
    if(tbody.find('tr').length == 2){
        current_tr.remove();
        tbody.find('tr .delete_row').attr('disabled','disabled');
    }else if(tbody.find('tr').length > 1){
        current_tr.remove();
    }
    updateTotalAmount();
});
$(".add_new_row").click(function(){
    var tbody_id = $(this).closest('tr').parent().siblings('tbody').attr('id');
    clone_new_row(tbody_id);
});
function clone_new_row(id_name){
    $('#'+id_name+' tr:last .select-plugin').select2("destroy");
    var table_row_id = $('#'+id_name+' tr:last').attr('id');
    var row_count = table_row_id.split('-');
    if(row_count.length >= 2){
        row_count = parseInt(row_count[1])+1;

        var new_row = $('#'+id_name+' tr:last').clone();
        new_row.attr('id', id_name+'-'+row_count);
        new_row.attr('data-row_no', '');

        new_row.find('input,select,textarea').each(function() {
            $(this).val('');
        });
        $('#'+id_name).append(new_row);
        $('#'+id_name+' tr').each(function(){
            $(this).find('.select-plugin').select2();
            $(this).find('.delete_row').removeAttr('disabled');
        })
        new_row.find('select').val('');
	    new_row.find('select:not(select[name=master_category],select[name=category],select[name=anticipated_currency]) option:not(:first-child)').remove();
    }
}
// To sum all the amount in anticipated table
function updateTotalAmount()
{
    var totalAmount = {};
    var prevTotalAmount = $('#total-anticipated-amount').attr('previousvalue');  
    if (Object.keys(prevTotalAmount).length)
        totalAmount = JSON.parse(prevTotalAmount);
    $('.anticipated_cost_container table tbody tr').each(function () {
        var currency = $(this).find('select[name="anticipated_currency"] option:selected').text();
        if($(this).find('input[name="amount"]').val())
        var amount = $(this).find('input[name="amount"]').val().replace(/\,/g, '') ?? 0;
        else if($(this).find('input[name="amount"]').text())
        var amount = $(this).find('input[name="amount"]').text() ?? 0;

        if(amount && currency != 'Select')
        {
            if(currency in totalAmount)
                totalAmount[currency] += parseFloat(amount);
            else
                totalAmount[currency] = parseFloat(amount);
        }
    });
    var totalAmountStr = "";
    for(i in totalAmount)
    {
        let amount = totalAmount[i]%1 === 0 ? totalAmount[i] : totalAmount[i].toFixed(2);
        // totalAmountStr += `${i}: ${amount} `;
        formatted_amount=CurrencyFormat({amount:amount,currencyType:i});
        totalAmountStr += `${i}: ${formatted_amount} `;

    }
    $('#total-anticipated-amount').html(totalAmountStr ? totalAmountStr : 0);
}
$(document).on('change', 'input[name="amount"],select[name="anticipated_currency"]', function () {
    updateTotalAmount();
    currency_type=$(this).closest('tr').find('select[name="anticipated_currency"] option:selected').text();
    currency_format_changes($(this).closest('tr'),currency_type,"anticipated_amount");
})
function currency_format_changes(row,currency_type,input_class){
if(row && currency_type && input_class){
  row.find('.'+input_class+'.currency_format').each(function(){
    CurrencyFormat({
      entity:$(this),
      allowDecimal:true,
      decimalPoint:2,
      prefix:'',
      currencyType:currency_type
    });
  });
}
}
</script>
<?php 

function formatAmount($expenseSum)
{
    $expenseItems = explode(';', $expenseSum);
    $formattedItems = [];

    foreach ($expenseItems as $item) {
        $item = trim($item);

        if (empty($item)) {
            continue;
        }

        $parts = explode(' ', $item, 2);
        if (count($parts) < 2) {
            continue;
        }
        $currency = $parts[0];
        $amount = $parts[1];

        if ($currency == 'INR:') {
            // Convert amount to string
            $amount = (string)$amount;
            $afterDecimal = '';
            $lastThree = '';
            $otherNumbers = '';

            if (strpos($amount, '.') !== false) {
                $amountParts = explode('.', $amount);
                $beforeDecimal = $amountParts[0];
                $afterDecimal = substr($amountParts[1], 0, 2);
                if (strlen($afterDecimal) < 2) {
                    $afterDecimal .= '0';
                }
                $afterDecimal = '.' . $afterDecimal;

                $lastThree = substr($beforeDecimal, -3);
                $otherNumbers = substr($beforeDecimal, 0, -3);
            } else {
                $afterDecimal = '.00';
                $lastThree = substr($amount, -3);
                $otherNumbers = substr($amount, 0, -3);
            }

            if ($otherNumbers != '') {
                $lastThree = ',' . $lastThree;
            }

            $formattedAmount = $currency . ' ' . preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $otherNumbers) . $lastThree . $afterDecimal;
        } else {
            $formattedAmount = $currency . ' ' . number_format((float) $amount, 2, '.', ',');
        }

        $formattedItems[] = $formattedAmount;
    }

    return implode(';', $formattedItems);
}
?>