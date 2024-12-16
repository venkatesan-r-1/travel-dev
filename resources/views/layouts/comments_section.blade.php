<?php
$visible_fields=$field_details['visible_fields'];
$editable_fields=$field_details['editable_fields'];
$field_attr=$field_details['field_attr'];
$options=$field_details['select_options'];
?>

<?php $field_name = "INP_064" ?>
    @if(in_array($field_name, $editable_fields))
            <div class="col-md-3 col-sm-12">
            @if(array_key_exists($field_name,$field_attr))
                <label class="required-field">{{$field_attr[$field_name]['lable_name']}}</label><br>
                <?php $attributes = json_decode($field_attr[$field_name]['attributes'], true); ?>
                    @if(in_array($field_name, $editable_fields))
                        {{ Form::textarea($field_attr[$field_name]['input_name'],$requestor_remarks,  $attributes); }}
                    @endif
            @endif
        </div>
    @endif