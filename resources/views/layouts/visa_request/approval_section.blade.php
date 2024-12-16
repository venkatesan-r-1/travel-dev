@php($section_name="approval")
<?php
    $only_status_details = [];
    $approval_status_array=[];
    if(isset($status_details)) {
        $only_status_details = Arr::except($status_details, ['remarks_details', 'remarks', 'approval_matrix', 'status_flow_code_mapping']);
        $approval_status = [
            "Approved" => ['STAT_04', 'STAT_05', 'STAT_06', 'STAT_07', 'STAT_08', 'STAT_09', 'STAT_10', 'STAT_11', 'STAT_24', 'STAT_25','STAT_29', 'STAT_33', 'STAT_12'],
            "Rejected" => ['STAT_15', 'STAT_16', 'STAT_17', 'STAT_18', 'STAT_19', 'STAT_20', 'STAT_21', 'STAT_22', 'STAT_26', 'STAT_27'],
        ];
        $overall_status = array_merge(...array_values($approval_status));
        $approval_status_details = array_filter($only_status_details, fn($e) => (
            in_array( $e["old_status_code"], $overall_status) &&
            in_array( $e["new_status_code"], $overall_status ) &&
            ( $e["old_status_code"] != $e["new_status_code"] || !in_array($e["new_status_code"], ['STAT_29', 'STAT_33']) )
        ));
        $approval_status_array = array_map( function ($v) use($approval_status) {
            if( in_array( $v["new_status_code"], $approval_status["Approved"] ))
                $status = "Approved";
            if( in_array( $v["new_status_code"], $approval_status["Rejected"] ))
                $status = "Rejected";
            return [
                "action_by_label" => $v["action_by_label"],
                "action_by" => $v["action_by"],
                "billed_to_client" => isset($v["billed_to_client"]) ? ( $v["billed_to_client"] == 1 ? "Yes" : "No" ) : "NA",
                "comments" => $v["comments"] ? $v["comments"] : "NA",
                "status" => $status,
            ];
        }, $approval_status_details );
        $approval_status_array = array_filter($approval_status_array);
    }  
?>
@if( count(array_filter($approval_status_array)) )
    <div class="visa-request-section gm_review_approval_section approval_section container-fluid form-content">
        @foreach ($approval_status_array as $v)
            <div class="row">
                <div class="col-md-3">
                    <label for="">{{ $v["action_by_label"] }}</label>
                    <p>{{ $v["action_by"] }}</p>
                </div>
                <div class="col-md-3">
                    <label for="">Billable to customer</label>
                    <p>{{ $v["billed_to_client"] }}</p>
                </div>
                <div class="col-md-3">
                    <label for="">Comments</label>
                    <p>{{ $v["comments"] }}</p>
                </div>
                <div class="col-md-3">
                    <label for="">Status</label>
                    <p>{{  $v["status"]}}</p>
                </div>
            </div>
        @endforeach
    </div>
@endif
@if(in_array('approval_waiting_msg', $visible_fields))
    <div class="row">
        <div class="waiting visa-request-section gm_review_approval_section approval_section">
            <img src="{{ asset('images/pending-icon.svg') }}" alt="pending-icon" class="pending-icon">
            <span>{{$waiting_message}}</span>
        </div>
    </div>
@endif
