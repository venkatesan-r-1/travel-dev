<?php //dd($status_details);?>
<?php
    $new_status_list = array_column($status_details,'new_status');
    //dd($new_status_list);
    $status_label_mapping = [
        2 => "Initiated by",
        3 => "Employee",
        4 => "Reviewed by",
        5 => "US HR partner",
        6 => "HR partner",
        7 => "Petition approved by",
        8 => "Petition denied by",
        9 => "Visa approved by",
        10 => "Visa denied by",
        11 => "Offer published by",
        12 => "Immigration reviewer",
        13 => "RFE in progress",
        14 => "Rejected by"
    ];
    $status_user_mapping = array_combine($new_status_list,array_column($status_details,'username'));
    $status_submission_date_mapping = array_combine($new_status_list,array_column($status_details,'created_at'));
    $status_comments_mapping = array_combine($new_status_list,array_column($status_details,'remarks'));
?>
@if(count($status_details) && end($status_details)['new_status']!=1)
<div class="modal fade" id="status_popup" tabindex="-1" role="dialog" aria-labelledby="status_popup_title" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="status_popup_title">Status tracker
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button></h5>  
      </div>
      <div class="modal-body status_popup_body">
      <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <label>Status</label>
                <p><img src='{{asset("images/$status-icon.svg")}}' class="status-icon"> {{$status}}</p>
            </div>
        </div>
       @foreach($new_status_list as $next_status)
       @if($next_status > 1)
            <div class="row">
              <div class="col-md-3 col-sm-3 col-xs-12">
                    <label>{{$status_label_mapping[$next_status]}}</label>
                    <p>{{$status_user_mapping[$next_status]}}</p>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12">
                    <label>Submitted on</label>
                    <p>{{$status_submission_date_mapping[$next_status]}}</p>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <label>Comments</label>
                    <p>{{$status_comments_mapping[$next_status]==""?"NA":$status_comments_mapping[$next_status]}}</p>
                </div>
            </div>
        @endif
        @endforeach	
      </div>
    </div>
  </div>
</div>
@endif
