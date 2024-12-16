<?php
    //dd($status_details);
    $visa_related_status_check=['STAT_12','STAT_14'];
    $status_tracker_details=array();
    $comments='';$display_comments='';
    if(isset($status_details)){
        $status_tracker_details=json_decode(json_encode($status_details),true);
        unset($status_tracker_details['remarks_details']);
        //dd($status_tracker_details);
        $comments=array_column(array_filter($status_tracker_details, function($item) use ($visa_related_status_check,$comments){
            if(in_array($item["old_status_code"],$visa_related_status_check))
                return $comments=$item['comments'];
            else
                return $comments.='';
        }),'comments');

        if(count($comments)){
            foreach($comments as $comment){
                $display_comments.=$comment."\n";
            }
        }

    } 

    
?>


<div class="row">
    <?php $field_name = "INP_064" ?>
    @if(in_array($field_name, $visible_fields))
        <div class="col-md-3 col-sm-3">
            @if(array_key_exists($field_name,$field_attr))
                <label class="required-field">{{$field_attr[$field_name]['lable_name']}}</label><br>
                <?php $attributes = json_decode($field_attr[$field_name]['attributes'], true); ?>
                    @if(in_array($field_name, $editable_fields))
                        {{ Form::textarea($field_attr[$field_name]['input_name'],null,  $attributes) }}
                    @else
                        <p>-</p>
                    @endif
            @endif
        </div>
    @endif
        <div class="col-md-6">
        @if($display_comments)
                <label>Previous comments</label><br>
                <textarea readonly="readonly" disabled="disabled">{{$display_comments}}</textarea>
        @endif
        </div>
        <div class="col-md-3" style="margin-top:35px;">
            @if(in_array('save_visa_process', $visible_fields))
                <button value="save_visa_process" name="save_visa_process_btn" id="visa_process_btn" class="request_action_buttons secondary-button">Save</button>
            @endif
            @if(in_array('visa_process', $visible_fields))
                <button value="visa_process" name="visa_process_btn" id="visa_process_btn" class="request_action_buttons primary-button">Visa process</button>
            @endif 
        </div>
    

    
</div>