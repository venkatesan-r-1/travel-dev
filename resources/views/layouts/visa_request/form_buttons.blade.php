@php
    if(isset($edit_id)){
        $approval_roles = ['PRO_OW', 'PRO_OW_HIE', 'DU_H', 'DU_H_HIE', 'DEP_H', 'CLI_PTR', 'GEO_H', 'FIN_APP'];
        $approval_tracker=json_decode(json_encode($approval_tracker_details),true);
        $users_involved=array_reduce(array_values($approval_tracker),function($carry,$item) use($approval_roles){
            if( in_array( $item['flow_code'], $approval_roles ))
                $carry[$item['user_involved']]=$item['is_completed'];
            return $carry;
        },[]);
    }else{
        $approval_tracker_details=[];
        $users_involved=[];
    }
    $previous_url = isset($edit_id) ? url()->previous() : '/home';
    if($previous_url == url()->current()) $previous_url = '/home';
@endphp
<div class="container-fluid button-section">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            @if(in_array('back', $visible_fields))
                <input type="hidden" id="previous-url" value="{{$previous_url}}">
                <button id="back" class="secondary-button" value="back">Back</button>
            @endif
            @if (in_array('reset_btn',$visible_fields))
                <button  type="reset" name="reset" value="Reset" id="reset" class="secondary-button" >Reset</button>
            @endif
            @if(in_array('save', $visible_fields))
                <button id="save" class="request_action_buttons secondary-button" value="save">Save</button>
            @endif
            @if(in_array('visa_user_save', $visible_fields))
                <button id="save" class="request_action_buttons secondary-button" value="visa_user_save">Save</button>
            @endif
            @if(in_array('onsite_hr_save', $visible_fields))
                <button id="save" class="request_action_buttons secondary-button" value="onsite_hr_save">Save</button>
            @endif
            @if(in_array('offshore_hr_save', $visible_fields))
                <button id="save" class="request_action_buttons secondary-button" value="offshore_hr_save">Save</button>
            @endif
            @if(in_array('petition_process_save', $visible_fields))
                <button id="save" class="request_action_buttons secondary-button" value="petition_process_save">Save</button>
            @endif
            @if(in_array('rfe_process_save', $visible_fields))
                <button id="save" class="request_action_buttons secondary-button" value="rfe_process_save">Save</button>
            @endif
            @if(in_array('visa_approval_save', $visible_fields))
                <button id="save" class="request_action_buttons secondary-button" value="visa_approval_save">Save</button>
            @endif
            @if(in_array('visa_entry_save', $visible_fields))
                <button id="save" class="request_action_buttons secondary-button" value="visa_entry_save">Save</button>
            @endif
            @if(in_array('rfe_progress', $visible_fields))
                <button id="save" class="request_action_buttons secondary-button" value="rfe_progress">RFE progress</button>
            @endif
            @if(in_array('petition_reject', $visible_fields))
                <button id="save" class="request_action_buttons secondary-button" value="petition_reject">Reject</button>
            @endif
            @if(in_array('petition_reject_rfe', $visible_fields))
                <button id="save" class="request_action_buttons secondary-button" value="petition_reject_rfe">Reject</button>
            @endif
            @if(in_array('save_visa_process', $visible_fields) && $is_td_visible)
                <button id="save" class="request_action_buttons secondary-button" value="save_visa_process">Save</button>
            @endif
            @if(in_array('submit', $visible_fields))
                <button id="submit" class="request_action_buttons primary-button" value="submit">Submit</button>
            @endif
            @if(in_array('send_for_review', $visible_fields))
                <button id="submit" class="request_action_buttons primary-button" value="send_for_review">Submit</button>
            @endif
            @if(in_array('desk_review_visa', $visible_fields) && $is_td_visible)
                <button id="submit" class="request_action_buttons primary-button" value="desk_review_visa">Review</button>
            @endif
            @if(in_array('onsite_hr_review', $visible_fields))
                <button id="submit" class="request_action_buttons primary-button" value="onsite_hr_review">Submit</button>
            @endif
            @if(in_array('offshore_hr_review', $visible_fields))
                <button id="submit" class="request_action_buttons primary-button" value="offshore_hr_review">Submit</button>
            @endif
            @if(in_array('petition_approve', $visible_fields))
                <button id="submit" class="request_action_buttons primary-button" value="petition_approve">Approve</button>
            @endif
            @if(in_array('petition_approve_rfe', $visible_fields))
                <button id="submit" class="request_action_buttons primary-button" value="petition_approve_rfe">Approve</button>
            @endif
            @if(in_array('visa_action', $visible_fields))
                <button id="submit" class="request_action_buttons primary-button" value="visa_action">Submit</button>
            @endif
            @if(in_array('send_for_process', $visible_fields))
                <button id="submit" class="request_action_buttons primary-button" value="send_for_process">Submit</button>
            @endif
            @if(in_array('publish', $visible_fields))
                <button id="submit" class="request_action_buttons primary-button" value="publish">Publish</button>
            @endif
            @if(in_array('visa_process', $visible_fields) && $is_td_visible)
                <button id="submit" class="request_action_buttons primary-button" value="visa_process">Done</button>
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
            @if(in_array('cp_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid] )
                <button value="cp_reject" name="cp_reject_btn" id="cp_reject_btn" class="request_action_buttons secondary-button">Reject</button>
            @endif
            @if(in_array('du_head_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid] )
                <button value="du_head_approve" name="du_head_approve_btn" id="du_head_approve_btn" class="request_action_buttons primary-button">Approve</button>
            @endif
            @if(in_array('du_head_hie_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid] )
                <button value="du_head_hie_approve" name="du_head_hie_approve_btn" id="du_head_hie_approve_btn" class="request_action_buttons primary-button">Approve</button>
            @endif
            @if(in_array('du_head_hie_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                <button value="du_head_hie_reject" name="du_head_hie_reject_btn" id="du_head_hie_reject_btn" class="request_action_buttons secondary-button">Reject</button>
            @endif
            @if(in_array('du_head_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid] )
                <button value="du_head_reject" name="du_head_reject_btn" id="du_head_reject_btn" class="request_action_buttons secondary-button">Reject</button>
            @endif
            @if(in_array('fin_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid] )
                <button value="fin_approve" name="fin_approve_btn" id="fin_approve_btn" class="request_action_buttons primary-button">Approve</button>
            @endif
            @if(in_array('fin_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid] )
                <button value="fin_reject" name="fin_reject_btn" id="fin_reject_btn" class="request_action_buttons secondary-button">Reject</button>
            @endif
            @if(in_array('geo_head_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                <button value="geo_head_approve" name="geo_head_approve_btn" id="geo_head_approve_btn" class="request_action_buttons primary-button">Approve</button>
            @endif
            @if(in_array('geo_head_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                <button value="geo_head_reject" name="geo_head_reject_btn" id="geo_head_reject_btn" class="request_action_buttons secondary-button">Reject</button>
            @endif
            @if(in_array('project_owner_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                <button value="project_owner_approve" name="project_owner_approve_btn" id="project_owner_approve_btn" class="request_action_buttons primary-button">Approve</button>
            @endif
            @if(in_array('project_owner_hie_approve', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                <button value="project_owner_hie_approve" name="project_owner_hie_approve_btn" id="project_owner_hie_approve_btn" class="request_action_buttons primary-button">Approve</button>
            @endif
            @if(in_array('project_owner_hie_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                <button value="project_owner_hie_reject" name="project_owner_hie_reject_btn" id="project_owner_hie_reject_btn" class="request_action_buttons secondary-button">Reject</button>
            @endif
            @if(in_array('project_owner_reject', $visible_fields) && in_array(Auth::User()->aceid,array_keys($users_involved)) &&!$users_involved[Auth::User()->aceid])
                <button value="project_owner_reject" name="project_owner_reject_btn" id="project_owner_reject_btn" class="request_action_buttons secondary-button">Reject</button>
            @endif
            @if(in_array('edit_salary_range', $visible_fields))
                <button value="edit_salary_range" name="edit_salary_range_btn" id="edit_salary_range_btn" class="primary-button" disabled="disabled">Update</button>
            @endif
        </div>
    </div>
</div>
