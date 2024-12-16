@php
    $visible_fields = array_key_exists('visible_fields',$field_details) ? $field_details['visible_fields'] : [];
    $field_attr = array_key_exists('field_attr',$field_details) ? $field_details['field_attr'] : [];
    $editable_fields = array_key_exists('editable_fields',$field_details) ? $field_details['editable_fields'] : [];
    $field_attr = json_decode(json_encode($field_attr),true);
    $field_attr = array_map( fn($e) => array_key_exists('attributes', $e) ? array_replace($e,['attributes' => (array)json_decode($e['attributes'],true)]) : $e, $field_attr );
    $visa_process_list = isset($field_details) ? $field_details['select_options']['visa_process'] : [];
    $visa_type_list = isset($field_details) ? $field_details['select_options']['visa_type_code'] : [];

    $visa_process = null;
    $visa_process_code = null;
    $visa_type = null;
    $visa_type_code = null;
    $visa_renewal_options = null;
    $exiting_date = null;
    if(isset($edit_id))
    {
        if(isset($request_details))
        {
            $visa_process = property_exists($request_details, 'visa_process') ? ( $request_details->visa_process ? $request_details->visa_process : '-' ) : '-';
            $visa_process_code = property_exists($request_details, 'visa_process_code') ? ( $request_details->visa_process_code ? $request_details->visa_process_code : '' ) : '';
            $visa_type = property_exists($request_details, 'visa_type') ? ($request_details->visa_type ? $request_details->visa_type : '-') : '-';
            $visa_type_code = property_exists($request_details, 'visa_type_code') ? ($request_details->visa_type_code ? $request_details->visa_type_code : '-') : '-';
            $visa_renewal_options = property_exists($request_details, 'visa_renewal_options') ? $request_details->visa_renewal_options : null;
            $visa_renewal_options = is_null($visa_renewal_options) ? null : $visa_renewal_options;
            $exiting_date = property_exists($request_details, 'exiting_date') ? ($request_details->exiting_date ? $request_details->exiting_date : null) : null;
        }
    }
@endphp
    <div class="visa_section" style="margin-top:10px;margin-bottom:5px">
        <div class="row">
            @php($field_name = "INP_073")
            @if(in_array($field_name, $visible_fields))
                <div class="col-md-3">
                    @if(array_key_exists($field_name, $field_attr))
                        <label for="{{ $field_attr[$field_name]['attributes']['id'] }}" for="form-label">{{ $field_attr[$field_name]['lable_name'] }}</label>
                        @if(in_array($field_name, $editable_fields))
                            {{ Form::select($field_attr[$field_name]['input_name'], [''=>'Select']+$visa_process_list, $visa_process_code, $field_attr[$field_name]['attributes']) }}
                        @else
                            <p>{{ $visa_process }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($field_name = 'INP_079')
            @if(in_array($field_name, $visible_fields))
                <div class="col-md-3">
                    @if(array_key_exists($field_name, $field_attr))
                        <label for="{{ $field_attr[$field_name]['attributes']['id'] }}" for="form-label">{{ $field_attr[$field_name]['lable_name'] }}</label>
                        @if(in_array($field_name, $editable_fields))
                            {{ Form::select($field_attr[$field_name]['input_name'], [''=>'Select']+$visa_type_list, $visa_type_code, $field_attr[$field_name]['attributes']) }}
                        @else
                            <p>{{ $visa_type }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($field_name = 'INP_074')
            @if(in_array($field_name, $visible_fields))
                <div class="col-md-3 visa_renewal_options_col visa_renewal_related_col">
                    @if(array_key_exists($field_name, $field_attr))
                        <label class="form-label">{{ $field_attr[$field_name]['lable_name'] }}</label>
                        @if(in_array($field_name, $editable_fields))
                            <input type="radio" name="{{ $field_attr[$field_name]['input_name'] }}" id="visa_renewal_option_1" {{ $visa_renewal_options === 0 ? 'checked' : '' }}>
                            <label for="visa_renewal_option_1" class="form-label">Within country</label>
                            <input type="radio" name="{{ $field_attr[$field_name]['input_name'] }}" id="visa_renewal_option_2" {{ $visa_renewal_options === 1 ? 'checked' : '' }}>
                            <label for="visa_renewal_option_2" class="form-label">Exiting country</label>
                        @else
                            <p>{{ $visa_renewal_options === 0 ? 'Within country' : ( $visa_renewal_options === 1 ? 'Exiting country' : 'NA' ) }}</p>
                        @endif
                    @endif
                </div>
            @endif
            @php($field_name = 'INP_075')
            @if(in_array($field_name, $visible_fields))
                <div class="col-md-3 visa_renewal_related_col">
                    @if(array_key_exists($field_name, $field_attr))
                        <label class="form-label">{{ $field_attr[$field_name]['lable_name'] }}</label>
                        @if(in_array($field_name, $editable_fields))
                            {{ Form::text($field_attr[$field_name]['input_name'], $exiting_date, $field_attr[$field_name]['attributes']) }}
                        @else
                            <p>{{ $exiting_date ? $exiting_date : 'NA' }}</p>
                        @endif
                    @endif
                </div>
            @endif            
        </div>
    </div>
</div>
<style>
    .visa_renewal_options_col label:first-child
    {
        display: block;
    }
    .visa_renewal_related_col
    {
        display: none;
    }
</style>
<script>
    $(document).ready(function () {
        $('[name="visa_renewal_options"]').addClass('not_required_field');
        $('#exiting_date').addClass('not_required_field');
        if($('#visa_process').val() == "VIS_PRO_002")
        {
            $('.visa_renewal_related_col').show();
            $('[name="visa_renewal_options"]').removeClass('not_required_field');
        }
        else
        {
            $('.visa_renewal_related_col').hide();
            $('[name="visa_renewal_options"]').addClass('not_required_field');
            $('#exiting_date').addClass('not_required_field');
        }
        if($('#visa_renewal_option_2').is(':checked'))
        {
            $('input[name="visa_renewal_options"]').val(1);
            $('#exiting_date').removeAttr('disabled');
            $('#exiting_date').removeClass('not_required_field');
        }
        if($('#visa_renewal_option_1').is(':checked'))
        {
            $('input[name="visa_renewal_options"]').val(0);
            $('#exiting_date').attr('disabled', true);
            $('#exiting_date').addClass('not_required_field');
        }
        if($('#visa_process').length == 0)
            $('.visa_renewal_related_col').show();
    });
    $(document).on('change', '#visa_process', function () {
        if($(this).val() == "VIS_PRO_002")
        {
            $('.visa_renewal_related_col').show();
            $('[name="visa_renewal_options"]').removeClass('not_required_field');
        }
        else
        {
            $('.visa_renewal_related_col').hide();
            $('#visa_renewal_option_1').val(null).removeAttr('checked');
            $('#visa_renewal_option_2').val(null).removeAttr('checked');
            $('#exiting_date').val(null);
            $('[name="visa_renewal_options"]').addClass('not_required_field');
            $('#exiting_date').addClass('not_required_field');
        }
    });
    $(document).on('click', '#visa_renewal_option_2', function () {
        if($(this).is(':checked'))
        {
            $('input[name="visa_renewal_options"]').val(1);
            $('#exiting_date').removeAttr('disabled');
            $('#exiting_date').removeClass('not_required_field');
        }
    });
    $(document).on('click', '#visa_renewal_option_1', function () {
        if($(this).is(':checked'))
        {
            $('input[name="visa_renewal_options"]').val(0);
            $('#exiting_date').attr('disabled', true);
            $('#exiting_date').val(null).addClass('not_required_field');
        }
    });
    $(document).on('select2:select', '.select-plugin' ,function () {
        $(this).trigger('change');
    })
</script>
