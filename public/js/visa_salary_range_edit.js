$(document).on('change', '#salary_range_from, #salary_range_to', function () {
    toggleUpdateBtn();
});

$(document).on('click', '#edit_salary_range_btn', function() {
    var hasError = false;
    let data={};
    data["edit_id"]=$('#edit_id').val(); data['salary_range_from']=''; data['salary_range_to']='';
    $('#onsite-hr-review-section').find('input,select,textarea').each(function () {
        var name = $(this).attr('name');
        var value = $(this).val().trim();
        if($(this).hasClass('currency_format')){
            value=value.replace(/\,/g, '');
          }
        var validated = validateFields(name, value);
        if(validated.isValid) {
            data[name] = value;
        } else {
            $(this).addClass('has_error');
            displayErrorMessage(validated.errorCode);
            hasError = true;
        }
    });
    if ( timeoutId ) clearTimeout(timeoutId);
    timeoutId = clearAlert();
    if(!hasError) {
        $(this).attr('disabled', true);
        openConfirmationBox({
            content: 'Are you sure want to update?',
            primaryBtn: 'Update',
            secondaryBtn: 'Cancel',
            primaryAction: updateSalaryRange,
            primaryActionParams: [data],
            secondaryAction: function () {
                $(this).removeAttr('disabled');
            }
        });
    }
});

// Toggle update button based on the input
function toggleUpdateBtn () {
    if(
        $('#salary_range_from').val() != $('#salary_range_from').prop('defaultValue') ||
        $('#salary_range_to').val() != $('#salary_range_to').prop('defaultValue')
     ) {
        $('#edit_salary_range_btn').removeAttr('disabled');
     } else {
        $('#edit_salary_range_btn').attr('disabled', true);
     }
}
// Update salary range
function updateSalaryRange(data)
{
    postData({
        url: "/update_salary_range",
        method: 'post',
        data: data,
        dataType: 'json',
        successCallback: function (response) {
            if(response.error) {
                $('.alert-danger').html('').show().html(response.error);
                setTimeout(
                    () => ('.alert-danger').hide(),
                    5000
                );
                return;
            }
            localStorage.clear();
            localStorage.setItem('message', response.message);
            setTimeout(
                () => window.open(response.redirect_url),
                500
            );
        },
        errorCallback: function (response) {
            alert('Error occured while updating the salary range');
        }
    });
}