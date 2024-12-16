<div class="row">
    <div id="section1" class="card-content fields_div initiation_section" style = "display:none;">
    @if(isset($edit_id)&&$edit_id)
    <input type='hidden' name="edit_id" value="{{$edit_id}}" />
    @endif
        <div class="row">
            @if (in_array('employee_aceid',$visible_fields))
                <div class='col-md-2 col-sm-12 col-xs-12'>
                @if(in_array('employee_aceid',$editable_fields))
                    <label class="required-field">Employee</label>
                    <?php echo Form::select('employee_id',[''=>'Select']+$users,$employee_value,['id'=>'employee_id','class'=>'form-control myselect','required']); ?>
                @else
                    <label>Employee</label>
                    <p id="user_text">{{$employee_name}} - {{$employee_value}}</p>
                @endif
                </div>
                <div class='col-md-2 col-sm-12 col-xs-12'>
                    <label class="">Email</label>
                    <p id="emp_email">{{$employee_mail}}</p>
                </div>
                <div class='col-md-2 col-sm-12 col-xs-12'>
                    <label class="">Department</label>
                    <p id="emp_dept">{{$employee_dept}}</p>
                </div>
                <div class='col-md-2 col-sm-12 col-xs-12'>
                    <label class="">Practice</label>
                    <p id="emp_practice">{{$practice}}</p>
                </div>
                <div class='col-md-2 col-sm-12 col-xs-12'>
                    <label class="">Primary manager</label>
                    <p id="emp_manager">{{$manager_name}}</p>
                </div>
            @endif
        </div>
        <div class="row">
            @if (in_array('visa_type_id',$visible_fields))
                <div class='col-md-2 col-sm-12 col-xs-12'>
                @if(in_array('visa_type_id',$editable_fields))
                    <label class="required-field">Visa type</label>
                    <?php echo Form::select('visa_type_id',[''=>'Select']+$visa_type,$visa_type_id,['id'=>'visa_type_id','class'=>'form-control','required']); ?>
                @else
                <label class="">Visa type</label>
                    <p id="visa_type_text">{{$visa_type_name}}</p>
                @endif
                </div>
            @endif
            @if (in_array('request_type_id',$visible_fields))
                <div class='col-md-2 col-sm-12 col-xs-12'>
                @if(in_array('request_type_id',$editable_fields))
                    <label class="required-field">Request type</label>
                    <?php echo Form::select('request_type_id',[''=>'Select']+$request_type,$request_type_id,['id'=>'request_type_id','class'=>'form-control','required']); ?>
                @else
                    <label class="">Request type</label>
                    <p>{{$request_type_name}}</p>
                @endif
                </div>
            @endif
            @if (in_array('client_code',$visible_fields))
                <div class='col-md-2 col-sm-12 col-xs-12'>
                @if(in_array('client_code',$editable_fields))
                    <label class="required-field">Client name</label>
                    <?php echo Form::select('client_code',[''=>'Select']+$customers,$client_code,['id'=>'client_code','class'=>'form-control myselect','required']); ?>
                @else
                    <label class="">Client name</label>
                    <p>{{$client_name}}</p>
                @endif
                </div>
            @endif
            @if (in_array('petition_id',$visible_fields))
                <div class='col-md-2 col-sm-12 col-xs-12'>
                @if(in_array('petition_id',$editable_fields))
                    <label class="required-field">Petition to be filed for</label>
                    <?php echo Form::select('petition_id',[''=>'Select']+$petition_type,$petition_id,['id'=>'petition_id','class'=>'form-control','required']); ?>
                @else
                    <label class="">Petition to be filed for</label>
                    <p>{{$petition_name}}</p>
                @endif
                </div>
            @endif
            @if (in_array('remarks',$visible_fields))
                <div class='col-md-2 col-sm-12 col-xs-12'>
                    <label class="">Remarks</label>
                @if(in_array('remarks',$editable_fields))
                    <?php echo Form::textarea('remarks',$hr_remarks,['id'=>'remarks','class'=>'form-control']); ?>
                @else
                    <p>{{$hr_remarks}}</p>
                @endif
                </div>
            @endif
        </div>
    </div>
</div>
