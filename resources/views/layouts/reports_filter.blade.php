<div class="filter-section" style="display:none">
    <div class="row">
        <div class="col-md-2">
            <label>Request type</label><br>
                {{Form::select('request_type_filter',[],NULL,['class'=>'multi-select','id'=>'request_type_filter','multiple'=>""])}}   
        </div>
        <div class="col-md-2">
            <label>Employee details</label><br>
                {{Form::select('employee_details_filter',[],NULL,['class'=>'multi-select','id'=>'employee_details_filter','multiple'=>""])}}   
        </div>
        <div class="col-md-2">
            <label>From</label><br>
                <input type="text" id="travel_from_date" class="form-control date" >
        </div>
        <div class="col-md-2">
            <label>To</label><br>
                <input type="text" id="travel_to_date" class="form-control date">
        </div>
        <div class="col-md-2">
            <label>Department</label><br>
                {{Form::select('department_filter',[],NULL,['id'=>'department_filter','multiple'=>""])}}   
            </div>
        <div class="col-md-2">
            <label>Project</label><br>
                {{Form::select('project_filter',[],NULL,['id'=>'project_filter','multiple'=>""])}}   
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-2">
            <label>Customer</label><br>
            {{Form::select('customer_filter',[],NULL,['id'=>'customer_filter','multiple'=>""])}}   
        </div>
        <div class="col-md-2">
            <label>Delivery unit</label><br>
            {{Form::select('delivery_unit_filter',[],NULL,['id'=>'delivery_unit_filter','multiple'=>""])}}   
        </div>
        <div class="col-md-2">
            <label>Status</label><br>
            {{Form::select('status_filter',[],NULL,['id'=>'status_filter','multiple'=>""])}}   
        </div>

    </div>
    <div class="btn-container">
        <button type="button" class="secondary-button" id="filter_reset">Cancel</button>
        <button type="button" class="primary-button" id="filter_apply">Apply</button>
    </div>
</div>
<style>
.filter-section{
box-shadow: 0px 1px 6px rgba(0,0,0,0.2);
padding: 15px 15px;
margin: 0px 30px 15px;
}
</style>
