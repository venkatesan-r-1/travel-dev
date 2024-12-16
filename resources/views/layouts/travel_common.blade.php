@php
    $is_visa_request = false;
    if($module == 'MOD_03')
        $is_visa_request = true;

$field_attr=$field_details['field_attr'];
$options=$field_details['select_options'];
$bill_to_client=null;
$billable_edit=0;
$requestor_remarks='';

// if(isset($edit_id)&&in_array($request_details->status_id,['STAT_01'])){
//     $remarks_details = array_key_exists('remarks_details', $status_details) ? $status_details['remarks_details'] : [];
//     $requestor_remarks = array_key_exists('requestor_remarks', $remarks_details) ? $remarks_details['requestor_remarks'] : '';
// }


if(isset($edit_id)){
    $bill_to_client=property_exists($request_details,'billed_to_client')?$request_details->billed_to_client:'';
}
$config_variables=new App\Http\Controllers\ConfigController;
$remove_billable_access = [];
// $remove_billable_access = ['PRO_OW','PRO_OW_HIE'];
$intial_billble_choose_access=array_diff($config_variables->CONFIG['BILLABLE_CHOOSE_ACCESS'], $remove_billable_access);
if(isset($edit_id)&&Auth()->User()->has_any_role_code($config_variables->CONFIG['BILLABLE_CHOOSE_ACCESS'])&&in_array($request_details->status_id,$config_variables->CONFIG['BILLABLE_ENABLED_STATUS'])){
    if(Auth::User()->has_any_role_code(['BF_REV'])&&$request_details->status_id=="STAT_05")
        $billable_edit=1;
    else if(property_exists($request_details,'billed_to_client')&&is_null($request_details->billed_to_client)&&!Auth::User()->has_any_role_code(['BF_REV']))
        $billable_edit=1;
    else if(property_exists($request_details,'billed_to_client')&&$request_details->created_by==Auth::User()->aceid&&$request_details->status_id=='STAT_01'&&Auth()->User()->has_any_role_code($intial_billble_choose_access))
        $billable_edit=1;
}

else if(!isset($edit_id)&&Auth()->User()->has_any_role_code($intial_billble_choose_access))
    $billable_edit=1;

$comments_enable_actions=$config_variables->CONFIG['COMMENTS_ENABLE_FOR_ROLES'];

if(isset($visa_link_details) && is_array($visa_link_details)) {
    $traveling_details_obj = (object)[
        'travelling_details_row_id' => null,
        'to_country' => array_key_exists('to_country', $visa_link_details) ? $visa_link_details['to_country'] : null,
        'to_city' => array_key_exists('to_city', $visa_link_details) ? $visa_link_details['to_city'] : null,
        'from_date' => array_key_exists('from_date', $visa_link_details) ? $visa_link_details['from_date'] : null,
        'to_date' => null,
        'visa_number' => array_key_exists('visa_number', $visa_link_details) ? $visa_link_details['visa_number'] : null,
        'visa_type_code' => array_key_exists('visa_type', $visa_link_details) ? $visa_link_details['visa_type'] : null,
    ];
    $field_details['select_options']['to_city'] = array_key_exists('to_city_list', $visa_link_details) ? (array)$visa_link_details['to_city_list'] : null;
    $project_code = array_key_exists('project_code', $visa_link_details) ? $visa_link_details['project_code'] : null;
    $requestor_entity = array_key_exists('requestor_entity', $visa_link_details) ? $visa_link_details['requestor_entity'] : null;
}
@endphp
@include('layouts.traveling_details')
@include('layouts.travel_details')
@include('layouts.other_details')
@if($is_visa_request)
    @include('layouts.visa_details')
@endif
@include('layouts.travel_proof')
@if(isset($request_details) && !in_array($request_details->status_id,['STAT_01']) && Auth::User()->has_any_role_code(['PRO_OW','PRO_OW_HIE','DU_H','DU_H_HIE','DEP_H','FIN_APP','GEO_H','CLI_PTR','BF_REV','AN_COST_FIN','AN_COST_FAC','AN_COST_VISA']))
@include('layouts.anticipated_cost')
@endif
@include('layouts.bill_to_client')
<br>
@if(isset($request_details) && in_array($request_details->status_id, ['STAT_12','STAT_13']))
@include('layouts.process')
@endif

@include('layouts.form_buttons')



