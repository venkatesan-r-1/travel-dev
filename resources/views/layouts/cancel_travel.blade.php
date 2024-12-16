<div class="modal fade" id="cancelTravelModal" tabindex="-1" role="dialog" aria-labelledby="cancelTravelModal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelTravelModal">Travel request cancellation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <label for="reason_for_cancel" class="form-label required-field">Reason for cancellation</label>
                        <textarea name="reason_for_cancel" id="reason_for_cancel" class="form-control form-fields"></textarea>
                    </div>    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="secondary-button" data-dismiss="modal" style="margin-right: 10px;">Close</button>
                <button type="button" class="primary-button" id="cancelTravelBtn" value="cancel_travel" disabled>Cancel travel</button>
            </div>
        </div>
    </div>
</div>
