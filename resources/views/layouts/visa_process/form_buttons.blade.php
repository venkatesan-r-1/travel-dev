
<div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 button-visa_process">
            @if (in_array('back_btn',$visible_fields))
                <button  type="back" name="back" value="Back" id="back" class="secondary-button" >Back</button>
            @endif
            @if (in_array('reset_btn',$visible_fields))
                <button  type="reset" name="reset" value="Reset" id="reset" class="secondary-button" >Reset</button>
            @endif
            @if (in_array('save_btn',$visible_fields))
                <button  type="submit" name="save" value="save" id="save" class="secondary-button request_action_buttons">Save</button>
            @endif
            @if (in_array('submit_btn',$visible_fields))
                <button  type="submit" name="submit" value="submit" id="submit"  class="primary-button request_action_buttons">Submit</button>
            @endif
            @if (in_array('rfe_btn',$visible_fields))
                <button  type="submit" name="rfe" value="rfe" id="rfe"  class="secondary-button request_action_buttons" >RFE</button>
            @endif
            @if (in_array('deny_btn',$visible_fields))
                <button  type="submit" name="deny" value="deny" id="deny"  class="secondary-button request_action_buttons" >Petition Deny</button>
            @endif
            @if (in_array('approve_btn',$visible_fields))
                <button  type="submit" name="approve" value="approve" id="submit"  class="primary-button request_action_buttons" >Petition Approve</button>
            @endif
            @if (in_array('publish_btn', $visible_fields))
                <button  type="submit" name="publish" value="publish" id="submit"  class="secondary-button request_action_buttons" >Publish</button>
            @endif
            @if( in_array('update_btn', $visible_fields) )
                <button type="submit" name="update" value="update" id="update" class="primary-button request_action_buttons">Update</button>
            @endif
        </div>
    </div>
