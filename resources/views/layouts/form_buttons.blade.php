@php
$visible_fields=$field_details['visible_fields'];
//below code has been edited by barath on 24-Jul-2024
//for back button Url redirect and whether to display or not.
 $back_button_url='\home';$back_button=0;
if(array_intersect($visible_fields,['bf_reject','fin_reject'])){
    $back_button_url='\review';
    $back_button=1;
}
if(array_intersect($visible_fields,['desk_review_fac','desk_review_fin','desk_review_visa'])){
    $back_button_url='\traveldesk';
    $back_button=1;
}
if(array_intersect($visible_fields,['project_owner_reject','bu_head_approve','cp_approve','du_head_approve','geo_head_approve','project_owner_hie_approve'])){
    $back_button_url='\approval';
    $back_button=1;
}
if(array_intersect($visible_fields,['submit'])){
    $back_button_url='\home';
    $back_button=1;
}

if(isset($request_details) && !in_array($request_details->status_id, ['STAT_12','STAT_13'])){
    $back_button=1;
}


if(isset($edit_id)){
    $approval_tracker=json_decode(json_encode($approval_tracker_details),true);
$users_involved=array_reduce(array_values($approval_tracker),function($carry,$item){
    $carry[$item['user_involved']]=$item['is_completed'];
   return $carry;
},[]);
}else{
    $approval_tracker_details=[];
    $users_involved=[];
    array_push($visible_fields,'reset_btn');
}
$previous_url=isset($edit_id)?url()->previous():'/home';
if($previous_url==url()->current()) $previous_url='/home';
@endphp
<div class="container-fluid">
        <div class="request-action-btn-grp">
            <div class="btn-container">
                @if($back_button)
                <a href={{$previous_url}}><button class="secondary-button"  style="margin-right: 10px;float:left;">Back</button></a>
                @endif

                @if(in_array('bf_reject', $visible_fields))
                    <button value="bf_reject" name="bf_reject_btn" id="bf_reject_btn" class="request_action_buttons secondary-button">Reject</button>
                @endif
                @if(in_array('bf_review', $visible_fields))
                    <button value="bf_review" name="bf_review_btn" id="bf_review_btn" class="request_action_buttons primary-button">Review</button>
                @endif
                @if(in_array('bu_head_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="bu_head_approve" name="bu_head_approve_btn" id="bu_head_approve_btn" class="request_action_buttons primary-button">Approve</button>
                @endif
                @if(in_array('bu_head_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="bu_head_reject" name="bu_head_reject_btn" id="bu_head_reject_btn" class="request_action_buttons secondary-button">Reject</button>
                @endif
                @if(in_array('cp_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="cp_approve" name="cp_approve_btn" id="cp_approve_btn" class="request_action_buttons primary-button">Approve</button>
                @endif
                @if(in_array('cp_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="cp_reject" name="cp_reject_btn" id="cp_reject_btn" class="request_action_buttons secondary-button">Reject</button>
                @endif
                @if(in_array('desk_review_fac', $visible_fields) && (array_key_exists('AN_COST_FAC',$users_involved) && !$users_involved['AN_COST_FAC']) && $is_td_visible)
                    <button value="desk_review_fac" name="desk_review_fac_btn" id="desk_review_fac_btn" class="request_action_buttons primary-button">Review</button>
                @endif
                @if(in_array('desk_review_fin', $visible_fields) && (array_key_exists('AN_COST_FIN',$users_involved) &&!$users_involved['AN_COST_FIN']) && $is_td_visible)
                    <button value="desk_review_fin" name="desk_review_fin_btn" id="desk_review_fin_btn" class="request_action_buttons primary-button">Review</button>
                @endif
                @if(in_array('desk_review_visa', $visible_fields) && (array_key_exists('AN_COST_VISA',$users_involved) &&!$users_involved['AN_COST_VISA']) && $is_td_visible)
                    <button value="desk_review_visa" name="desk_review_fin_btn" id="desk_review_fin_btn" class="request_action_buttons primary-button">Review</button>
                @endif
                @if(in_array('du_head_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="du_head_approve" name="du_head_approve_btn" id="du_head_approve_btn" class="request_action_buttons primary-button">Approve</button>
                @endif
                @if(in_array('du_head_hie_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="du_head_hie_approve" name="du_head_hie_approve_btn" id="du_head_hie_approve_btn" class="request_action_buttons primary-button">Approve</button>
                @endif
                @if(in_array('du_head_hie_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) && !$users_involved[Auth::User()->aceid])
                    <button value="du_head_hie_reject" name="du_head_hie_reject_btn" id="du_head_hie_reject_btn" class="request_action_buttons secondary-button">Reject</button>
                @endif
                @if(in_array('du_head_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="du_head_reject" name="du_head_reject_btn" id="du_head_reject_btn" class="request_action_buttons secondary-button">Reject</button>
                @endif
                @if(in_array('fin_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="fin_approve" name="fin_approve_btn" id="fin_approve_btn" class="request_action_buttons primary-button">Approve</button>
                @endif
                @if(in_array('fin_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="fin_reject" name="fin_reject_btn" id="fin_reject_btn" class="request_action_buttons secondary-button">Reject</button>
                @endif
                <!-- @if(in_array('forex_process', $visible_fields) )
                    <button value="forex_process" name="forex_process_btn" id="forex_process_btn" class=" primary-button">Forex Process</button>
                @endif                 -->
                @if(in_array('geo_head_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="geo_head_approve" name="geo_head_approve_btn" id="geo_head_approve_btn" class="request_action_buttons primary-button">Approve</button>
                @endif
                @if(in_array('geo_head_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="geo_head_reject" name="geo_head_reject_btn" id="geo_head_reject_btn" class="request_action_buttons secondary-button">Reject</button>
                @endif
                @if(in_array('project_owner_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                <button value="project_owner_approve" name="project_owner_approve_btn" id="project_owner_approve_btn" class="request_action_buttons primary-button">Approve</button>
                @endif
                @if(in_array('project_owner_hie_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved))&&!$users_involved[Auth::User()->aceid])
                    <button value="project_owner_hie_approve" name="project_owner_hie_approve_btn" id="project_owner_hie_approve_btn" class="request_action_buttons primary-button">Approve</button>
                @endif
                @if(in_array('project_owner_hie_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved))&&!$users_involved[Auth::User()->aceid])
                    <button value="project_owner_hie_reject" name="project_owner_hie_reject_btn" id="project_owner_hie_reject_btn" class="request_action_buttons secondary-button">Reject</button>
                @endif
                @if(in_array('project_owner_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                    <button value="project_owner_reject" name="project_owner_reject_btn" id="project_owner_reject_btn" class="request_action_buttons secondary-button">Reject</button>
                @endif
                @if (in_array('reset_btn',$visible_fields))
                    <button  type="reset" name="reset" value="Reset" id="reset" class="secondary-button" >Reset</button>
                @endif
                @if(in_array('save', $visible_fields))
                    <button value="save" name="save_btn" id="save_btn" class="request_action_buttons secondary-button" style="margin-right: 10px;">Save</button>
                @endif
                @if(in_array('submit', $visible_fields))
                    <button value="submit" name="submit_btn" id="submit_btn" class="request_action_buttons primary-button">Submit</button>
                @endif
                {{-- @if(in_array('ticket_process', $visible_fields) )
                    <button value="ticket_process" name="ticket_process_btn" id="ticket_process_btn" class="request_action_buttons primary-button">Ticket process</button>
                @endif --}}
                {{-- @if(in_array('visa_process', $visible_fields))
                <button value="visa_process" name="visa_process_btn" id="visa_process_btn" class="request_action_buttons primary-button">Visa process</button>
                @endif  --}}
                @if(isset($can_cancel_travel) && $can_cancel_travel)
                    <button value="cancel_travel" name="cancel_travel_btn" id="cancel_travel_btn" class="secondary-button" data-toggle="modal" data-target="#cancelTravelModal" redirectURL = "{{ url()->previous() }}">Cancel travel</button>
                @endif
            </div>
        </div>
    </div>
    @include('layouts.cancel_travel')