@php
    $ticket_process_comments = [];
    $need_to_process = isset($is_ticket_processed) ? !$is_ticket_processed : false;
    $can_process = $need_to_process && Auth::User()->has_any_role_code(['DOM_TCK_ADM','TRV_PROC_TICKET']);
    foreach($status_details as $details)
    {
        if(is_array($details) && key($details) != 'remarks_details'){
            $old_status = array_key_exists('old_status_code', $details) ? $details['old_status_code'] : null;
            $new_status = array_key_exists('new_status_code', $details) ? $details['new_status_code'] : null;
            $action = array_key_exists('action', $details) ? $details['action'] : null;
            if( in_array($old_status, ['STAT_12']) && in_array($new_status, ['STAT_12','STAT_13']) && in_array($action, ['save_ticket_process','ticket_process'])){
                $comments = array_key_exists('comments', $details) ? $details['comments'] : null;
                if($comments) array_push($ticket_process_comments, $details['comments']);
            }
            // if( $old_status == "STAT_12" && $new_status == "STAT_12" && $action == "ticket_process" )
            //     $need_to_process = false;
        }
    }
    $ticket_process_comments = array_filter($ticket_process_comments);
@endphp
<div class="row">
    @if(!$need_to_process)
        <div class="col-md-3 col-sm-12">
            <label class="form-label">Status</label>
            <p>Ticket processed</p>
        </div>
    @endif
    <?php $field_name = "INP_064" ?>
    @if(in_array($field_name, $visible_fields) && $can_process)
            <div class="col-md-3 col-sm-12">
            @if(array_key_exists($field_name,$field_attr))
                <label class="form-label">{{$field_attr[$field_name]['lable_name']}}</label><br>
                <?php $attributes = json_decode($field_attr[$field_name]['attributes'], true); ?>
                    @if(in_array($field_name, $editable_fields))
                        {{ Form::textarea($field_attr[$field_name]['input_name'],null,  $attributes); }}
                    @else
                        <p>-</p>
                    @endif
            @endif
        </div>
    @endif
    @if($ticket_process_comments && count($ticket_process_comments))
        <div class="col-md-3 col-sm-12">
            <label for="ticket-process-previous-comments">Previous comments</label>
            <textarea name="ticket-process-previous-comments" id="ticket-process-previous-comments" cols="30" rows="30" readonly>{{ implode("\n\n", $ticket_process_comments) }}</textarea>
        </div>
    @else
        <div class="col-md-3 col-sm-12">
            <label for="ticket-process-previous-comments">Previous comments</label>
            <textarea name="ticket-process-previous-comments" id="ticket-process-previous-comments" cols="30" rows="30" readonly></textarea>
        </div>
    @endif
</div>
<div class="btn-container">
    <a href={{url()->previous()}}><button class="secondary-button">Back</button></a>
@if(in_array('save_ticket_process', $visible_fields) && $can_process)
    <button value="save_ticket_process" name="save_ticket_process_btn" id="ticket_process_btn" class="request_action_buttons secondary-button">Save</button>
@endif
@if(in_array('ticket_process', $visible_fields) && $can_process)
    <button value="ticket_process" name="ticket_process_btn" id="ticket_process_btn" class="request_action_buttons primary-button">Ticket process</button>
@endif
</div>
