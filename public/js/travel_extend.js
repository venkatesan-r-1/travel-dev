$(document).ready(function () {
    // $('input[name="from_date"],input[name="to_date"]').addClass('extendable_dates');
});
//Datepicker related changes
$(document).on('change', 'input[name="from_date"],input[name="to_date"]', function () {
    var name = $(this).attr('name');
    var row = $(this).closest('tr');
    if(name == 'from_date')
    {
        var fromDate = $(this);
        var toDate = row.find('input[name="to_date"]');
        var fromDateValue = new Date(fromDate.val());
        toDate.datepicker('setStartDate', fromDateValue);
    }   
    else
    {
        var toDate = $(this);
        var fromDate = row.find('input[name="from_date"]');
        var toDateValue = new Date(toDate.val());
        fromDate.datepicker('setEndDate',toDateValue);
    }
    if($('input[name="to_date"]').attr('traveling_type') == 'one-way')
        $('input[name="to_adte"]').attr('disabled', true);
});
//Toggling the extend button on input
$(document).on('change', 'input[name="from_date"],input[name="to_date"]', function () {
    var row_id = $(this).closest('tr');
    var from_date = row_id.find('input[name="from_date"]');
    var to_date = row_id.find('input[name="to_date"]');
    if(checkModified(from_date) || checkModified(to_date))
    {
        $('#travel_extend_btn').removeAttr('disabled');
    }
    else
    {
        $('#travel_extend_btn').attr('disabled', true);
    }
});
//Updating the changes
$(document).on('click', '#travel_extend_btn', function(){
    $(this).attr('disabled',true);
    multipleRoute = $('.tr_details_container table tbody tr').length > 1 ? true : false;
    var isValid = valildateDateFields(multipleRoute)
    if(isValid.result)
    {
        var data = [];
        var table = $('.tr_details_container table');
        table.find('tbody tr').each(function (){
            var reference_id = $(this).attr('tr_row_id');
            var dateValues = {};
            dateValues['reference_id'] = reference_id;
            $(this).closest('tr').find('input[name="from_date"],input[name="to_date"]').each(function () {
                var name = $(this).attr('name');
                if(checkModified($(this)))
                    dateValues[name] = $(this).val();
            });
            data.push(dateValues);
        });
        if(data.length){
            var requestData = {'edit_id': $('#edit_id').val(), 'data': data};
            var options = {
                content: 'Are you sure want to submit?',
                primaryBtn: 'Extend',
                secondaryBtn: 'Cancel',
                primaryAction: extendDate,
                primaryActionParams: [requestData],
            }
            openConfirmationBox(options);
        }
        $(this).removeAttr('disabled');
    }
    else
    {
        $(this).removeAttr('disabled');
        $('.alert-danger').show().find('.message').html(isValid.message);
        setTimeout(() => $('.alert-danger').hide(), 10000);
    }
});
// To check whether the input_field updated or not
function checkModified(element)
{
    return element.val() == element.prop('defaultValue') ? false : true;
}
// To validate the input fields
function valildateDateFields(isMultipleRoute = false)
{
    var result = true;var message = "";
    $('.tr_details_container table tbody tr').find('input[name="from_date"],input[name="to_date"]').each(function () {
        if(!$(this).val() && $(this).attr('traveling_type') != 'one-way'){
            $(this).addClass('has_error');
            result = false;
        }
    });
    if(!result)
        message = "Please fill the travel dates";

    if(isMultipleRoute)
    {
        var travelDates = $('.tr_details_container table tbody tr').find('input[name="from_date"],input[name="to_date"]').map(function () { return new Date($(this).val()); }).toArray();
        var sortedOrNot = travelDates.every(function(value, index, array) { return index===0 || value >= array[index-1] } )
        if(result && !sortedOrNot){
            message = "Dates have been overlapping";
            result = false;
        }
    }
    return {result: result, message:message};
}

// update the date
function extendDate(requestData)
{
    handleRefresh(false);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '/extend_travel_dates',
        method: 'post',
        data: requestData,
        dataType: 'json',
        success: function (response) {
            window.open( $('#travel_extend_btn').attr('redirect_url'), '_self' );
        },
        error: function () {
            alert("Error occurred");
            $(this).removeAttr('disabled');
        }
    });

}
