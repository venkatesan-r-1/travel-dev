$(document).ready(function () {
    var session_token = $('#user_details_token').val();
    if(session_token)
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/load_user_other_details',
            method: 'post',
            dataType: 'json',
            data: {'session_token':session_token},
            success: (response) => { console.log(response); $('#user_details_token').val(null); },
            error: (response) => console.log(response),
        });
    }
    // var basic_details = {
    //     project : $(document).find("#project_name").val(),
    //     department : $(document).find("#department").val(),
    // };
});
$(document).on('click','#reset',function(){
    $('#visa-details-section,#personal-info-section,#bill_to_client,#request_page_section,#traveling_section,#travel_section,#other_details,#additional_traveller_details,#proof-details-table')
    .find('input,textarea,radio,select').each(function(){
        if($(this).attr('name') && $.inArray($(this).attr('name'), ["origin","department_code","practice_unit_code","requestor_entity"]) === -1 ){
            if($(this).attr('type') == 'radio')
                $(this).prop('checked',false);
            else
                $(this).val('');
    
            if($(this).hasClass("select-plugin"))
                $(this).select2().trigger('change');
            if($(this).hasClass("date")){
                if($(this).attr('name') && $.inArray($(this).attr('name'), ["date_of_birth"]) !== -1 ){
                    $(this).datepicker('setEndDate', new Date());
                }else{
                    $(this).datepicker('setStartDate', new Date());
                    $(this).datepicker('setEndDate', null);
                }
            }
            if($(this).hasClass("proof_file_path")){
                $(this).siblings(".file-upload-container").find(".row-item").remove();
                $(this).siblings(".file-info").find(".info").text('');
                $(this).siblings(".file-info").find(".upload-icon").removeClass('uploaded');
                $(this).siblings("[name=proof_file_upload]").removeAttr('disabled');
            }
            $("#proof-details-table").find("tbody select,input").removeAttr('disabled');
        }
        if($(this).attr('name') && $.inArray($(this).attr('name'), ["date_of_birth","address"]) !== -1 ){
            $(this).removeAttr('disabled');
        }
    })
    $(document).find('.has_error').each(function () {
        if( $(this).hasClass("custom-file-upload") )
            $(this).closest('.file-wrapper').find('.file-info').removeClass("has_error");
        $('input[name="family_traveling"]').closest('.radio_required').find('.alert_required').hide();
        $('input[name="accommodation_required"]').closest('.radio_required').find('.alert_required').hide();
        $('input[name="insurance_required"]').closest('.radio_required').find('.alert_required').hide();


        $('input[name="forex_required"]').closest('.radio_required').find('.alert_required').hide();
        $('input[name="ticket_required"]').closest('.radio_required').find('.alert_required').hide();
        $('input[name="laptop_required"]').closest('.radio_required').find('.alert_required').hide();
        $(this).removeClass('has_error');
    });
})
