@if(isset($status_details) && is_array($status_details) && count($status_details) && $status_id != 'STAT_01')
@php
    if(array_key_exists('remarks_details', $status_details))        
        unset($status_details['remarks_details']);
    if(array_key_exists('remarks', $status_details))
        unset($status_details['remarks']);
    if(array_key_exists('approval_matrix', $status_details)) {
        $approval_tracker = $status_details['approval_matrix'];
    }
    if(array_key_exists('status_flow_code_mapping', $status_details)) {
        $status_flow_code_mapping = $status_details['status_flow_code_mapping'];
    }
    $status_details = array_filter($status_details, fn($k) => $k != "approval_matrix", ARRAY_FILTER_USE_KEY);
    $status_details = array_filter($status_details, fn($k) => $k != "status_flow_code_mapping", ARRAY_FILTER_USE_KEY);
    @endphp
    <div class="modal fade" id="statusTrackerModal" tabindex="-1" role="dialog" aria-labelledby="statusTrackerModalTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusTrackerModalTitle">Status tracker</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="form-label">Status</label>
                            <p>{{ $status }}</p>
                        </div>
                    </div>
                    @foreach($status_details as $value)
                        @php
                            $ticket_processed = $is_ticket_processed && $value['action']=='ticket_process';
                            $forex_processed = $is_forex_processed && $value['action']=='forex_process';
                        @endphp
                        @if(( $value['old_status_code'] == 0 && $value['new_status_code'] == 'STAT_01' ) || (in_array($value['old_status_code'],['STAT_01','STAT_12','STAT_28', 'STAT_29', 'STAT_30', 'STAT_31', 'STAT_33', 'STAT_35', 'STAT_38']) && $value['new_status_code'] == $value['old_status_code'] && !( $ticket_processed || $forex_processed ) ))
                            @continue
                        @endif
                        <div class="row">
                            <div class="col-md-3">
                                <label for="form-label">{{ array_key_exists('action_by_label', $value) ? ( $value["action_by_label"] ? $value["action_by_label"] : "Action by" ) : "Action by" }}</label>
                                <p>{{ array_key_exists('action_by', $value) ? ($value['action_by'] ? $value['action_by'] : '-') : '-' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label for="form-label">{{ array_key_exists('action_on_label', $value) ? ( $value["action_on_label"] ? $value["action_on_label"] : "Action on" ) : "Action on" }}</label>
                                <p>{{ array_key_exists('action_on', $value) ? ($value['action_on'] ? $value['action_on'] : '-') : '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="form-label">Comments</label>
                                <p class="statusTrackerComments">{{ array_key_exists('comments', $value) ? ($value['comments'] ? $value['comments'] : 'NA') : 'NA' }}</p>
                            </div>
                            <div class="col-md-2">
                                @php($billable = array_key_exists('billed_to_client',$value) ? (is_null($value['billed_to_client']) ? null : $value['billed_to_client']) : null)
                                @if(!is_null($billable))
                                    <label for="form-label">Billable</label>
                                    {{-- <p>{{ array_key_exists('billed_to_client',$value) ? (is_null($value['billed_to_client']) ? '-' : ($value['billed_to_client']==1 ? 'Yes' : 'No') ): '-' }}</p> --}}
                                    <p>{{$billable === 1 ? "Yes" : "No"}}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @if(isset($approval_tracker) && $approval_tracker)
                        <div class="row">
                            <div class="col-md-3">
                                @foreach($approval_tracker as $flow_code => $user_name)
                                    <label>{{$status_flow_code_mapping[$status_id][$flow_code]}}</label>
                                    <p>{{$user_name}}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
