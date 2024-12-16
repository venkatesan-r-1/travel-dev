<?php 
//dd($request_details);
$visible_fields=$field_details['visible_fields'];
$editable_fields=$field_details['editable_fields'];
$field_attr=$field_details['field_attr'];
$options=$field_details['select_options'];
$billed_to_client='';
if(isset($edit_id)){
    $billed_to_client=property_exists($request_details,'billed_to_client')?$request_details->billed_to_client:'';
}

?>

<div class="form-section" style="margin-bottom=10px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12" style="margin-bottom:5px;margin-top:15px;">
                <h3>{{ $module != 'MOD_03' ? 'Travel details' : 'Visa details' }}</h3>            
            </div>
        </div>
    </div>
    <div class="container-fluid form-content" style="padding-bottom:5px">
        <div id="travel_section" style="padding-bottom:5px">
        <div class="row">
            <?php $field_name = "INP_010" ?>
            @if(in_array($field_name, $visible_fields))
                <div class="col-md-3 col-sm-12">
                    @if(array_key_exists($field_name,$field_attr))
                    <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label><br>
                        @if(in_array($field_name, $editable_fields))
                        <?php $attributes = json_decode($field_attr[$field_name]['attributes'], true); 
                        $project=isset($edit_id) ? $request_details->project_code : (isset($visa_link_details['project_code'])? $visa_link_details['project_code'] : null);
                        $project=$project ?? $default_project;
                        ?>
                            {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select'] +$options['project_code'], $project,$attributes);}}
                        @else
                            <p>{{$request_details->project_name}}</p>
                        @endif
                    @endif
                </div>
            @endif
            <?php $field_name = "INP_009"; ?>
            @if(in_array($field_name, $visible_fields))
                <div class="col-md-3 col-sm-12">
                    @if(array_key_exists($field_name,$field_attr))
                    <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label><br>
                        @if(in_array($field_name, $editable_fields))
                        <?php $attributes = json_decode($field_attr[$field_name]['attributes'], true); 
                        $department=isset($edit_id) ? $request_details->department_code :  null ;
                        $department_hidden = $visa_link_details['department_code'] ?? null ?>
                            <input type="hidden" id="department_hidden" class="not_required_field" value="{{ $department_hidden }}" />
                            {{ Form::select($field_attr[$field_name]['input_name'], ['' => 'Select'] +$options['departments'],$department, $attributes) }}
                        @else
                            <p>{{$request_details->department_name}}</p>
                        @endif
                    @endif
                </div>
            @endif

            <?php $field_name = "INP_011"; ?>
            @if(in_array($field_name, $visible_fields))
                <div class="col-md-3 col-sm-12">
                    @if(array_key_exists($field_name,$field_attr))
                    <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label><br>
                        @if(in_array($field_name, $editable_fields))
                        <?php $attributes = json_decode($field_attr[$field_name]['attributes'], true);
                        $customer=isset($edit_id) ? $request_details->customer_code : null ?>
                            {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select'] +$options['customer_name'],$customer, $attributes); }}
                        @else
                            <p>{{$request_details->customer_name}}</p>
                        @endif
                    @endif
                </div>
            @endif
            <?php $field_name = "INP_012"; ?>
            @if(in_array($field_name, $visible_fields))
                <div class="col-md-3 col-sm-12">
                    @if(array_key_exists($field_name,$field_attr))
                    <label>{{$field_attr[$field_name]['lable_name']}}</label><br>
                        @if(in_array($field_name, $editable_fields))
                        <?php $attributes = json_decode($field_attr[$field_name]['attributes'], true); 
                        if($project&&in_array($project,['CUST_PROJ_007'])&&isset($request_details->dept_sl_flag)&&$request_details->dept_sl_flag==1){
                            $attributes['disabled']=false;
                        }
                        $delivery=isset($edit_id) ? $request_details->practice_unit_code : null;?>
                            {{ Form::select($field_attr[$field_name]['input_name'],[''=>'Select'] +$options['practice_unit_code'],$delivery, $attributes); }}
                        @else
                            <p>{{$request_details->practice_unit_name ? $request_details->practice_unit_name : 'NA'}}</p>
                        @endif
                    @endif
                </div>
            @endif
            <?php $field_name = "INP_049" ?>
            @if(in_array($field_name, $visible_fields))
                <div class="col-md-3 col-sm-12">
                    @if(array_key_exists($field_name,$field_attr))
                        <label class="{{ in_array($field_name, $editable_fields) ? 'required-field' : '' }}">{{$field_attr[$field_name]['lable_name']}}</label><br>
                        @if(in_array($field_name, $editable_fields))
                        <?php $attributes = json_decode($field_attr[$field_name]['attributes'], true);
                        $entity=isset($user_entity) ? $user_entity:null ?>
                            {{ Form::text($field_attr[$field_name]['input_name'],$entity, $attributes); }}
                        @else
                            <p>{{$request_details->requestor_entity ?? 'NA'}}</p>
                        @endif
                    @endif
                </div>
            @endif
            <!-- <?php $billable_view="billable_view";?>
            @if(in_array($billable_view,$visible_fields) && !in_array($billed_to_client,['',NULL]))
             <div class="col-md-3 col-sm-12">
                <label>Billable</label>
                <p>{{$billed_to_client==1?'Yes':'No'}}</p>
             </div>
            @endif -->
        </div>
    </div>
