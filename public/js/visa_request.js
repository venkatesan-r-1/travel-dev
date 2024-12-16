$(document).ready(function () { 
    proof_mandatory_symbol();
    const dmaxRowCount = 9;
    let dcount = 0;
    $('.proof-details-fields[name="proof_request_for"]').each(function () {
        if($(this).val()=="RF_14"){
            dcount++;

        }
    });
    
    if(dcount >= dmaxRowCount) $('#proof-details-table tfoot').hide();
    
    $( "[name='proof_type']" ).each(function() {
        if($(this).val()) {
            hideProofDetailsFields($(this),false);
        }
    });
    // To show last active tab
    if($('#progressbar').length)
        {
          var last_active_tab = $(this).find('#progressbar .active').last();
        //   $(this).find('#progressbar .active').removeClass('current');
        //   last_active_tab.addClass('current');
          $('.visa-request-section').hide();
          $('.'+last_active_tab.attr('id').replace('tab','section')).show();
          $('.all-section').show();
        }
      
    // Adding datepicker
    $('.date').datepicker({
        format: "dd-M-yyyy",
        autoclose: true,
    });
    var startDate = $('#from_date').val() ? new Date($('#from_date').val().replace(/-/g, '/')) : new Date();
    var endDate = $('#to_date').val() ? new Date($('#to_date').val().replace(/-/g, '/')) : null;
    startDate.setHours(0,0,0,0);
if($("#edit_id").val()){
    endDate = endDate < new Date() && new Date();
    startDate = startDate < new Date() && new Date();
    toggleOrigin( getVisaConfig('request_for', $('#request_for').val()) );
}

    if(endDate) endDate.setHours(0,0,0,0);
    $('#from_date').datepicker('setStartDate', new Date()).datepicker('setEndDate', endDate).on('changeDate', function () {
        var today = new Date();
        var endDate = new Date( $('#to_date').val().replace(/-/g, '/') );
if($("#edit_id").val()){
        if( endDate < new Date() ) {
            endDate = new Date();
            $('#to_date').val(null);
        } 
}

        today.setHours(0,0,0,0);
        endDate.setHours(0,0,0,0);
        if(new Date( $(this).val().replace(/-/g, '/') ) < today || new Date( $(this).val().replace(/-/g, '/') ) > endDate)
            $(this).val(null);
        $('#to_date').datepicker('setStartDate', new Date( $(this).val().replace(/-/g, '/') ));
        $(this).trigger('blur');
    });
    $( '[name="proof_expiry_date"]' ).datepicker({format: 'dd-M-yyyy', autoclose: true}).datepicker('setStartDate', new Date());
    $( '[name="proof_issue_date"]' ).datepicker({format: 'dd-M-yyyy', autoclose: true}).datepicker('setEndDate', new Date());
    $('#to_date').datepicker('setStartDate', startDate).on('changeDate', function () {
        var startDate = $('#from_date').val() ? new Date( $('#from_date').val().replace(/-/g, '/') ) : new Date();
        startDate.setHours(0,0,0,0);
        if(new Date( $(this).val().replace(/-/g, '/') ) < startDate )
            $(this).val(null);
        $('#from_date').datepicker('setEndDate', new Date( $(this).val().replace(/-/g, '/') ));
        $(this).trigger('blur');
    });
    $('#date_of_birth').datepicker('setEndDate', new Date());
    $('#date_of_joining').datepicker('setEndDate', new Date());
    $('#one_time_bonus_payout_date').datepicker('setStartDate', new Date()).on('changeDate', function () {
        let selectedDate = new Date( $(this).val() );
        selectedDate.setHours(0,0,0,0);
        let startDate = new Date();
        startDate.setHours(0,0,0,0);
        if(selectedDate < startDate)
            $(this).val(null).trigger('blur');
    });
    $('#next_salary_revision_on').datepicker('setStartDate', new Date()).on('changeDate', function () {
        let selectedDate = new Date( $(this).val() );
        selectedDate.setHours(0,0,0,0);
        let startDate = new Date();
        startDate.setHours(0,0,0,0);
        if(selectedDate < startDate)
            $(this).val(null).trigger('blur');
    });
    $('#travel_date').datepicker('setStartDate', new Date()).on('changeDate', function () {
        let selectedDate = new Date( $(this).val() );
        selectedDate.setHours(0,0,0,0);
        let startDate = new Date( new Date() );
        startDate.setHours(0,0,0,0);
        if(startDate > selectedDate)
            $(this).val(null).trigger('blur');
    });
    const maxYears = getVisaConfig('petition_year_diff');
    let petitionStartDate = $('#petition_start_date').val() && new Date($('#petition_start_date').val().replace(/-/g, '/'));
    let petitionEndDate = $('#petition_start_date').val() && new Date($('#petition_start_date').val().replace(/-/g, '/'));
    if(petitionStartDate)
        petitionEndDate.setFullYear(petitionStartDate.getFullYear() + maxYears);
    $('#petition_start_date').on('changeDate', function () {
        petitionStartDate = new Date($(this).val().replace(/-/g, '/'));
        petitionEndDate = new Date($(this).val().replace(/-/g, '/'));
        petitionEndDate.setFullYear( petitionStartDate.getFullYear() + maxYears);
        $('#petition_end_date').val(null).datepicker('setStartDate', petitionStartDate);
        $('#petition_end_date').datepicker('setEndDate', petitionEndDate);
    });
    $('#petition_end_date').datepicker('setStartDate', petitionStartDate).datepicker('setEndDate', petitionEndDate);
    const visa_interview_date_diff = getVisaConfig('visa_interview_date_diff');
    let visaOfcDate = $('#visa_ofc_date').val() && new Date( $('#visa_ofc_date').val().replace(/-/g, '/') );
    if(visaOfcDate) visaOfcDate.setDate(visaOfcDate.getDate() + visa_interview_date_diff);
    $('#visa_ofc_date').on('changeDate', function () {
        visaOfcDate = new Date ( $(this).val().replace(/-/g, '/') );
        visaOfcDate.setDate(visaOfcDate.getDate() + visa_interview_date_diff);
        $('#visa_interview_date').datepicker('setStartDate', visaOfcDate);
    });
    $('#visa_interview_date').datepicker('setStartDate', visaOfcDate);
    $('#visa_ofc_date').datepicker('setEndDate', $('#visa_interview_date').val());
    $('#visa_interview_date').on('changeDate', function () {
        $('#visa_ofc_date').datepicker('setEndDate', $('#visa_interview_date').val());
    });

    // Adding select2
    $('.select-plugin').select2().on('select2:open', function () {
        $('.select2-search__field').attr('placeholder', 'Search');
    });
    //remove tooltip from select2
    $('.select2-container').on('mouseenter', function () {
        $(this).find('.select2-selection__rendered').removeAttr('title');
    });
    
    // Adding tooltip
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    // Disable request for in case of request for employee
    let request_for = getVisaConfig('request_for', $('#request_for').val());
    if(request_for == "employee")
        $('#request_for').attr('disabled', true);
    // Disable origin from To country
    let origin = $('#origin').val();
    if(origin) {
        $('#to_country').children().each(function () {
            if($(this).val() == origin) $(this).attr('disabled', true);
        })
    }

    // Adding placeholders
    if($('#india_experience_years').length) {
        $('#india_experience_years').attr('placeholder', 'Years');
        $('#india_experience_months').attr('placeholder', 'Months');
        $('#overall_experience_years').attr('placeholder', 'Years');
        $('#overall_experience_months').attr('placeholder', 'Months');
    }
    if($('#salary_range_from').length) {
        $('#salary_range_from').attr('placeholder', 'From');
        $('#salary_range_to').attr('placeholder', 'To');
    }
    if($('#edit_id').val()) {        
        if(getVisaConfig('request_for', $('#request_for').val()) == "self") {            
            var option_length=$('#proof_type_1').children('option').length-1;
            var self_count=0;
            $('#proof-details-table tbody tr').each(function () {                 
                if(getVisaConfig('request_for', $(this).find('[name="proof_request_for"]').val()) == "self"){
                    self_count++;
                }
            });
            if(self_count==option_length){
                $('#proof-details-table').find('tfoot').hide();
            }
        }
        let flag = !$('#request_for').length || ($('#request_for').length && ['self', 'family'].includes(getVisaConfig('request_for', $('#request_for').val())))
        if( flag && $('#proof-details-table').length && !$('.proof-details-fields[name="proof_type"]').val() ) {
            loadProofDetails();
        } else {
            let requestFor = getVisaConfig("request_for", $('#hidden_request_for').val());
            $('#date_of_birth').attr('disabled', true);
            $('#address').attr('disabled', true);
            $('#date_of_joining').attr('disabled', true);
            if((requestFor == "self" || requestFor == "employee") && isOffshoreuser($('#origin').val() || $('#hidden_origin').val())){
                $('#proof-details-table').find('.proof-details-fields').each(function () {  
                    if(!$(this).hasClass('custom-file-upload') && !['proof_name'].includes( $(this).attr('name') ))
                        $(this).attr('disabled', true)
                    else
                        $(this).removeAttr('disabled');
                });
                $('#proof-details-table').find('tfoot').hide();
                $('#proof-details-table thead tr th:last-child, #proof-details-table tbody tr td:last-child').hide();
            }
        }
        //auto fill aspire exp
        if( $('#date_of_joining').val() ) autoFillAspireExperience( $('#date_of_joining').val() );
        // Disable proof details based on request for
        disableProofDetailsFields();
    }
    postData({
        url: "/load_project_details",
        data: {project: $('#project_name').val(), department_id: $('#department').val(),edit_id:$('#edit_id').val()},
        successCallback: updateProjectDetails,
    });
    if($('#project_name').val() && $('#department').val()) {
        postData({
            url: "/load_project_details",
            data: {project: $('#project_name').val(), department_id: $('#department').val(),edit_id:$('#edit_id').val()},
            successCallback: updateProjectDetails,
        });
    }
    // To show long term visa request related fields
    $('.long_term_hide').find('input,select,textarea').each(function () {
        let visaType = getVisaConfig('visa_type', $('#visa_type').val());
        if(visaType == "long_term") {
            $('.long_term_hide').show();
            $(this).removeClass('not_required_field');
        } else {
            $('.long_term_hide').hide();
            $(this).addClass('not_required_field');
        }  
    })

    $('.short_term_hide').find('input,select,textarea').each(function () {
        let visaType = getVisaConfig('visa_type', $('#visa_type').val());
        if(visaType != "long_term") {
            $('.short_term_hide').show();
            $(this).removeClass('not_required_field');
        } else {
            $('.short_term_hide').hide();
            $(this).addClass('not_required_field');
            clearProofDetails();
        }
    })

    // TO show or hide the Billed to client div based on the content inside
    if($("#billed_to_client").find('.col-md-3').length){
        $('#bill_to_client.form-section').show();
    }
    else{
        $('#bill_to_client.form-section').hide();
    }

    var is_action_required=$('.request_action_buttons').length;
    if(is_action_required)
        $('#bill_to_client').show();
    else
        $('#bill_to_client').hide();


    // remove one time bonus payout date from mandatory fields
    let oneTimeBonus = $('#one_time_bonus').val();
    toggleOneBonusPayoutDate(oneTimeBonus);

    // Dependent details
    if($('[name="dependency_details"]').length && !$('[name="dependency_details"]').val()) {
        $('.dependent-details-div').hide();
        $('.dependent-details-div').find('.remove-dependent-name').hide();
    } else {
        if($('[name="dependency_details"]').length > 3) {
            $('.visa-stamping-notes').show();
        }
    }

    // if($("#billed_to_client").find('.col-md-3').length){
    //     $('#bill_to_client.form-section').show();
    // }

    if ( $(".file-view-link").length ) {
        const sizeText = $(".file-view-link").closest('.file-wrapper').find('.file-upload-container span.file-size').each(function () {
            $(this).text( getSize( parseInt($(this).text()) ) );
        })
    }

    //du validation
    department_validation($(document).find('#department').val(),$(document).find('#project_name').val());
    if($('.visa_currency').text().length >0){
        currency_type=$('.visa_currency').text();
        currency_format_changes_for_same_row(currency_type,'minimum_wage');
    }
    if($('.input-group-text').text().length >0){
        currency_type=$('.input-group-text').text();
        currency_format_changes_for_same_row(currency_type,'salary_range_from');
    }
    if($('.input-group-text').text().length >0){
        currency_type=$('.input-group-text').text();
        currency_format_changes_for_same_row(currency_type,'salary_range_to');
    }
    if($('.input-group-text').text().length >0){
        currency_type=$('.input-group-text').first().text();
        currency_format_changes_for_same_row(currency_type,'us_salary');
    }
    if($('.input-group-text').text().length >0){
        currency_type=$('.input-group-text').first().text();
        currency_format_changes_for_same_row(currency_type,'one_time_bonus');
    }

 $("#visa_page_view_btn").on('click',function(){
        var view_type=$(this).val();
        alter_visa_request_view(view_type);
    });

    // Toggle proof details validation based on location
    if($('#edit_id').val()) toggleValidationForOnsiteUsers( $('#proof-details-table tbody tr:first-child') );
    if(get_visa_status_config($("#visa_status_id").val())){

        
        $(document).find("#submit").attr("disabled","disabled");
    }else{
        if( $('.system-message').length == 0 )
            $(document).find("#submit").removeAttr("disabled","disabled");
        //$("#request_action_buttons").removeAttr("disabled");
    }

/**
 * code by barath on 11-sep-2024
 * to hide filing details for country other than US and
 * visa type longterm
 */
    country=$('#to_country').val()?$('#to_country').val():$('#to_country').text();
    visa_type=$('#visa_type').val()?$('#visa_type').val():$('#visa_type').text();
    filingFieldVisible(visa_type,country);

});
$(document).on("change", "#visa_status_id", function () {
    if(get_visa_status_config($("#visa_status_id").val())){
        
        $(document).find("#submit").attr("disabled","disabled");
    }else{
        if( $('.system-message').length == 0 )
        $(document).find("#submit").removeAttr("disabled","disabled");
        //$("#request_action_buttons").removeAttr("disabled");
    }
});



$(document).on("change", "#visa_type", function () {
    if(!$(this).val() || $(this).val()=="VIS_001"){
        $(".common_tab").css("display", "none");
        $(".short_term").css("display", "block");


    }else if($(this).val()=="VIS_002"){
        $(".common_tab").css("display", "none");
        if($(this).val()!="VIS_CAT_005")
        $(".long_term").css("display", "block");
        else
        $(".longh1b").css("display", "block");

        /**
         * code by barath on 11-sep-2024
         * to hide filing details for country other than US and
         * visa type longterm
         */
        country=$('#to_country').val();
        visatype=$('#visa_type').val();
        filingFieldVisible(visatype,country);

        if( $(this).val() == "VIS_002" && $('#to_country').val() == "COU_025" )
            $('#mexicoAlertModal').modal('show');
    }
    
});
$(document).on("change", "#visa_category", function () {
    if(($(this).val()=="VIS_CAT_005") && ($("#visa_type").val()=="VIS_002")){
        $(".common_tab").css("display", "none");
        $(".longh1b").css("display", "block");


    }else if($(this).val()!="VIS_CAT_005"){
        $(".common_tab").css("display", "none");
        if($("#visa_type").val()=="VIS_002"){
            $(".long_term").css("display", "block");

        }else if(!$(this).val() || $("#visa_type").val()=="VIS_001"){
            $(".short_term").css("display", "block");

        }


    }
    
});




// Handling events...
// Loading icon
$(document).ajaxStart(function () {
    $('.ui-wait').show();
}).ajaxStop(function () {
    $('.ui-wait').hide();
});

// Add datepikcer
$(document).on('focus', '.date', function () {
    $(this).datepicker({
        format: "dd-M-yyyy",
        autoclose: true,
    });
});

// Prevent arrow keys on datepicker
$(document).on('keyup', '.date', function (e) {
    const arrowsKeys = [37, 38, 39, 40];
    if(arrowsKeys.includes(e.which))
        return false;
    return true; 
});

//Show last active tab
$(document).on('click','#progressbar li.active', function(){
    var selected_tab=$(this).attr('id');
    var target_section=selected_tab.replace('tab','section');
    $('.visa-request-section').hide();
    $('.'+target_section).show();
    if($(this).hasClass('current')){
        $('.all-section').show();
    }
    $('#progressbar .current').removeClass('current');
    $(this).addClass('current');
});

// Handle request for
$(document).on("change", "#request_for", function () {
    clearProofDetails();
    $('.prev_proof_error').remove();
    if ( !$('.system-message error').length );
        $('.request_action_buttons').removeAttr('disabled', true);
    loadUserDetails();
    if( ['self', 'family'].includes(getVisaConfig('request_for', $('#request_for').val())) )
        loadProofDetails();
    toggleOrigin(getVisaConfig('request_for', $(this).val()));
});
// Handle change in visa type
$(document).on("change", "#visa_type", function () {
    clearProofDetails();
    loadEmployeeList($(this).val());
    loadvisaCategory( $(this).val(), $('#origin').val(), $('#to_country').val() );
    /**
         * code by barath on 11-sep-2024
         * to hide filing details for country other than US and
         * visa type longterm
         */
    country=$('#to_country').val();
    visatype=$(this).val();
    filingFieldVisible(visatype,country);
});
// Handle origin change
$(document).on("change", "#origin", function () {
    let origin = $(this).val();
    $('#to_country').children().each(function () {
        if($(this).val() == origin) $(this).attr('disabled', true);
        else $(this).removeAttr('disabled');
    })
    $('#to_country').select2();
});


// Handle change in country
$(document).on("change", "#to_country", function () {
    visa_type=$('#visa_type').val();
    country=$(this).val();
    filingFieldVisible(visa_type,country);
    loadvisaCategory( $('#visa_type').val(), $('#origin').val(), $('#to_country').val() );
    loadCities( $(this).val() );
    if( visa_type == "VIS_002" && country == "COU_025" )
        $('#mexicoAlertModal').modal('show');
});
// Hanlde change in project
$(document).on("change", "#project_name", function () {
    if($(this).val()=='CUST_PROJ_007')
        $('#department').removeAttr('disabled').trigger('change');
    postData({
        url: "/load_project_details",
        data: {project: $(this).val(), department_id: $('#department').val(), employee: $("#employee").val(),edit_id:$('#edit_id').val()},
        successCallback: updateProjectDetails,
    }); 
});
// Handle request for employee
$(document).on('change', "#employee", function () {
    loadUserDetails($(this).val());
});

$(document).on('click','#back', function () {
    location.href=$('#previous-url').val();
})

// File upload related changes
$(document).on('click', '.upload-icon', function () {
    $(this).closest('.file-wrapper').find('.custom-file-upload').trigger('click');
});
$(document).on('input','.custom-file-upload', function () {
    let count = $(this).closest('.file-wrapper').find(".file-upload-container").children().length;
    $(this).attr('count',count);
    if( $(this).attr('name') == "cv_file_upload" ) {
        uploadFile($(this), {multiple: true, allowedFormats: ['png', 'jpg', 'jpeg', 'pdf', 'docx']});
    } else if($(this).attr('name') == "degree_file_upload") {
        uploadFile($(this), {multiple: true, allowedFormats: ['docx','png','jpg','pdf']});
    } else if($(this).attr('name') == "petition_file_upload") {
        uploadFile($(this), {multiple: true, allowedFormats: ['png', 'jpg', 'jpeg', 'pdf', 'docx']});
    } else if($(this).attr('name') == "visa_file_upload") {
        uploadFile($(this), {multiple: true, allowedFormats: ['png', 'jpg', 'jpeg', 'pdf', 'docx']});
    } else {
        uploadFile($(this));
    }
});
$(document).on('click', '.file-remove', function () {
    var data = {filePath: $(this).closest('.row-item').find('.file-name a').attr('href') };
    if($('#edit_id').val() != '')
        data['edit_id'] = $('#edit_id').val();
    deleteFile(data, $(this));
});

$(document).on('click', '.file-info .info, .file-view-link', function (e) {
    e.stopPropagation();
    var dropdownMenu = $(this).closest('.file-wrapper').find('.file-upload-container');
    $('.file-upload-container').each(function () {
        if($(this)[0] !== dropdownMenu[0]){
            $(this).removeClass('show');
        }
    })
    if(dropdownMenu.length){
        dropdownMenu.toggleClass('show');
    }
});

$(document).on('click', function (event) {
    if(!$('.file-upload-container').is(event.target) && $('.file-upload-container').has(event.target).length === 0){
        $('.file-upload-container').removeClass('show');
    }
});

$(document).on('change', "[name='proof_type']", function () {
    if($(this).val()) {
        hideProofDetailsFields($(this));
        toggleRequestForSelf($(this));
    }
});

$(document).on('input', ".proof-details-fields", function()  { toggleValidationForOnsiteUsers($(this).closest('tr')); });

// add new row
$(document).on('click', "#add_proof_btn", addRow);
// remove row
$(document).on('click', '.proof-action-btn.delete-btn', removeRow);

//remove has_error class
$(document).on('focus click', '.has_error', function () {
    $(this).removeClass('has_error');
    if ( $(this).closest('.radio_required').length ) {
        $(this).closest('.radio_required').find('.alert_required').hide();
    }
})

$(document).on('change', '#education_category_id', function () {
    postData({
        url: "/load_education_qualification",
        data: {category: $(this).val()},
        successCallback: updateEducationQualificationList,
    })
});

// Auto fill aspire experience;
$(document).on('change', '#date_of_joining', function () {
    autoFillAspireExperience($(this).val());
});

//slider
$(document).on('click', '.slider', function () {
    $(this).closest('.switch').find('input').prop('checked', (index,value) => !value );
});

//payout date visiblity
$(document).on('input', '#one_time_bonus', function () {
    toggleOneBonusPayoutDate($(this).val());
})

// Link to travel request
$(document).on('click', '#travel_link', function () {
    window.open(
        $(this).val(),
        '_blank'
    );
})

//handle travel type change
$(document).on('change', '#traveling_type_id', function () {
    if($(this).val())
    {
        const type = getVisaConfig('travel_type', $(this).val());
        if(type == "alone"){
            let addBtn = $('#add-dependent-name').show().clone();
            $('.dependent-details-div:not(:first)').remove();
            if($('.dependent-details-div .field-wrap').find('#add-dependent-name').length == 0)
                addBtn.appendTo('.dependent-details-div .field-wrap');
            $('.dependent-details-div').hide().find('input').val(null);
        } 
        else $('.dependent-details-div').show();
    }
})
// add new dependent field
$(document).on('click', '#add-dependent-name', function() {
    addDependentName($(this));
});
// remove dependent field
$(document).on('click', '.remove-dependent-name', function () {
    removeDependentName($(this));
});
// du validation
$(document).on('change','#department',function(){
    var department=$(this).val();
    var project_name=$('#project_name').val();
    if(department){
        department_validation(department,project_name);
    }
    // else{
        $('#delivery_unit').val('');
        $('#delivery_unit').select2().trigger('change');
    // }
});
// close the alert box
$(document).on('click', '.alert_close', function () {
    $('.alert-danger').hide();
});

// 
$(document).on('keydown', '.date', function (event) {
    event.preventDefault();
    return false;
});

function department_validation(department,project_code){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type:'POST',
        data:{department:department,project_code:project_code},
        url:'/department_du_validation',
        dataType: 'JSON',
        success:function(response){
            if(response.du_disabled){
                $(document).find('#delivery_unit').attr('disabled',true);
                $('#delivery_unit').select2('enable',false);
                $(document).find('#delivery_unit').addClass('not_required_field');
            }
            else{
                $(document).find('#delivery_unit').attr('disabled',false);
                $('#delivery_unit').select2('enable');
                $(document).find('#delivery_unit').removeClass('not_required_field');
            }
        },
        error:function(error){
            alert('error occured');
        }
    });
}

function addDependentName(element)
{
    const labelNames = ['First', 'Second', 'Third', 'Fourth'];
    let count = $('.dependent-details-div').length;
    const maxCount = 4;
    const curDiv = element.closest('.dependent-details-div');
    curDiv.clone().insertAfter('.dependent-details-div:last').find('input').val(null);
    element.remove();
    const nextDiv = curDiv.next();
    nextDiv.find('.remove-dependent-name').show();
    nextDiv.find('label').text(`${labelNames[count-1]} child`);
    if(count+1 > maxCount) $('#add-dependent-name').hide();
    if(count > 2) $('.visa-stamping-notes').show();
}

function removeDependentName(element)
{
    const labelNames = ['First', 'Second', 'Third', 'Fourth'];
    let count = $('.dependent-details-div').length;
    const addBtn = $('#add-dependent-name').show().clone();
    element.closest('.dependent-details-div').remove();
    if( $('.dependent-details-div:last').has('#add-dependent-name').length == 0 )
        addBtn.appendTo('.dependent-details-div:last .field-wrap');
    if(count < 2) $('.remove-dependent-name').hide();
    labelNames.forEach( (v, i) => {
        $(`.dependent-details-div:eq(${i+1}) label`).text(`${v} child`);
    } )
    if(count-1 <= 3) $('.visa-stamping-notes').hide();

}

function toggleOneBonusPayoutDate(oneTimeBonus)
{
    if(oneTimeBonus) {
        $('.payout-date').removeClass('hide');
        $('#one_time_bonus_payout_date').removeClass('not_required_field');
    } else {
        $('.payout-date').addClass('hide');
        $('#one_time_bonus_payout_date').addClass('not_required_field');
    }
}

function autoFillAspireExperience(doj)
{
    const today = new Date(); const dateOfJoing = new Date(doj);
    let yearDiff = today.getFullYear() - dateOfJoing.getFullYear();
    let monthsDiff = today.getMonth() - dateOfJoing.getMonth();
    let dateDiff = today.getDate() - dateOfJoing.getDate();
    if(dateDiff < 0 && monthsDiff == 0) {
        monthsDiff--;
    }
    if(monthsDiff >= 12) {
        yearDiff++;
        monthsDiff -= 12;
    } else if (monthsDiff < 0){
        yearDiff--;
        monthsDiff += 12;
    }
    $('#india_experience_years').val(yearDiff).attr('disabled', true);
    $('#india_experience_months').val(monthsDiff).attr('disabled', true);
}

//Send data using ajax
function postData(options)
{
    let fallBackResponse = null;
    if(!options.url)
        return;   
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: options.url,
        method: options.method ?? "post",
        dataType: options.datatype ?? "json",
        data: options.data ?? {},
        async: options.async ?? true,
        success: options.successCallback ?? function (response) { fallBackResponse = response; },
        error: options.errorCallback ?? function () { alert("Error occured"); },
    });

    if (fallBackResponse)  return fallBackResponse;
}

// To load the user details for self request
function loadUserDetails(aceid=null)
{
    postData({
        url: "/load_visa_user_details",
        data: {aceid: aceid},
        successCallback: updateUserData,
        async: false,
    });
}
// load employee list
function loadEmployeeList(visaType)
{
    postData({
        url: "/load_employee_list",
        data: {visa_type: visaType},
        async: true,
        successCallback: showEmployeeList,
    });
}
// To load visa catagories based on 
function loadvisaCategory(visaType, fromCountry, toCountry)
{
    postData({
        url: "/load_visa_category",
        data: { visa_type: visaType, from_country: fromCountry, to_country: toCountry  },
        async: true,
        successCallback: updatevisaCategory,
    })   
}
function loadCities( country )
{
    postData({
        url: "/load_city",
        async: true,
        data: {country: country},
        successCallback: updateCity,
    });
}
// Update the fields from response data
function updateUserData(response)
{
    const entity = response.entity || null;
    const origin = response.origin || null;
    const default_project = response.default_project || null;
    const projects = response.projects || null;
    const department = response.DepartmentId || null;
    $('#requestor_entity').val(entity);
    $('#origin').val(origin).trigger('change');
    // disable reportee country from to country list
    $('#to_country').children().each(function () {
        if( $(this).val() == origin )
            $(this).attr('disabled',true);
        else
        $(this).removeAttr('disabled');
    });
    if(projects) {
        $('#project_name').children().each(function () {
            if($(this).val())   $(this).remove();
        });
        for(let key in projects)
            $('#project_name').append(`<option value=${key}>${projects[key]}</option>`);
        if(default_project) {
            $('#department').val(department);
            $('#project_name').val(default_project).trigger('change');
        }
        else {
            $('#customer_name').removeAttr('disabled').val(null).trigger('change');
            $('#department').removeAttr('disabled').val(null).trigger('change');
            $('#delivery_unit').removeAttr('disabled').val(null).trigger('change');
        }
    }
    // $('#project_code').val(default_project).trigger('change');
}
// show employee list
function showEmployeeList(response)
{
    const visa_type = getVisaConfig("visa_type", $('#visa_type').val())
    if(visa_type == "long_term") {
        $('.long_term_hide').each(function () {
            $(this).show();
            $(this).find('input,select,textarea').removeClass('not_required_field');
        });
        $('.short_term_hide').each(function () {
            $(this).hide();
            $(this).find('input,select,textarea').addClass('not_required_field').val(null).trigger('change');
            clearProofDetails();
        });
    
        const default_request_for = response.default_request_for;
        const request_for_list = response.request_for_list;
        const employee = response.employee;
        if(default_request_for) {
            $('#request_for').children().each(function () {
                if( $(this).val() ) $(this).remove();
            });
            for(let key in request_for_list)
                    $('#request_for').append(`<option value=${key}>${request_for_list[key]}</option>`);
            $('#request_for').val(default_request_for).attr('disabled', true).trigger('change');
            $('#employee').children().each(function () {
                if( $(this).val() ) $(this).remove();
            });
            for(let key in employee)
                    $('#employee').append(`<option value=${key}>${employee[key]}</option>`);
        }
    } else {
        $('.long_term_hide').each(function () {
            $(this).hide();
            $(this).find('input,select,textarea').addClass('not_required_field').val(null).trigger('change');
        });
        $('.short_term_hide').each(function () {
            $(this).show();
            $(this).find('input,select,textarea').removeClass('not_required_field');
        });
        $('#request_for').attr('disabled', false);
        const request_for_list = response.request_for_list;
        $('#request_for').children().each(function () {
            if( $(this).val() ) $(this).remove();
        });
        for(let key in request_for_list)
                $('#request_for').append(`<option value=${key}>${request_for_list[key]}</option>`);
        
    }

    /**
         * code by barath on 11-sep-2024
         * to hide filing details for country other than US and
         * visa type longterm
         */
    country=$('#to_country').val();
    visatype=$('#visa_type').val();
    filingFieldVisible(visatype,country);
}
// Update the visa catagories
function updatevisaCategory(response)
{
    const visaCategories = response.visa_category;
    $('#visa_category').children().each(function () {
        if($(this).val())   $(this).remove();
    });
    for(let key in visaCategories) {
        $('#visa_category').append(`<option value=${key}>${visaCategories[key]}</option>`);
    }
}

// Update project details
function updateProjectDetails(response)
{
    $('.ui-wait').hide();
    department=response.department;
    customers=response.customer;
    delivery=response.delivery_unit;
    selected_department=response.selected_department;
    $('#department').children().each(function () {
        if( $(this).val() ) $(this).remove();
    });
    $.each(department, function(key, value) {   
        $('#department').append($("<option></option>").attr("value",key).text(value));
        $("#department option[value='"+selected_department+"']").attr('selected',true);
        if(Object.keys(department).length==1){
            $("#department option[value='"+key+"']").attr('selected',true);
            $('#department').attr('disabled','disabled');
            $('#department').parent().find('.select2-container').removeClass('has_error');
        }
    });
    department_validation($("#department").val(),$("#project_name").val());
    $('#customer_name').children().each(function () {
        if( $(this).val() ) $(this).remove();
    });
    $.each(customers, function(key, value) {   
        $('#customer_name').append($("<option></option>").attr("value",key).text(value));
        if(Object.keys(customers).length==1){
            $("#customer_name option[value='"+key+"']").attr('selected',true);
            $('#customer_name').attr('disabled','disabled');
            $('#customer_name').parent().find('.select2-container').removeClass('has_error');	
        } 
    }); 
    var delivery_unit = $('#delivery_unit').val();
    $('#delivery_unit').children().each(function () {
        if( $(this).val() ) $(this).remove();
    })
    $.each(delivery, function (key, value) {
        $('#delivery_unit').append($("<option></option>").attr("value", key).text(value));                
    });
    $('#delivery_unit').val(delivery_unit);
    if(Object.keys(delivery).length == 0) {
            $('#delivery_unit').attr('disabled','disabled');
    }else if(Object.keys(delivery).length == 1) {
            var key = Object.keys(delivery)[0];
            $("#delivery_unit option[value='" + key + "']").prop('selected', true);
            $('#delivery_unit').attr('disabled','disabled');
            $('#delivery_unit').parent().find('.select2-container').removeClass('has_error');
    }else{
        $('#delivery_unit').removeAttr('disabled');
    }
}
// update the city details
function updateCity(response)
{
    const cities = response.city;
    $('#to_city').children().each(function () {
        if( $(this).val() ) $(this).remove();
    })
    for(let key in cities) 
        $('#to_city').append(`<option value="${key}">${cities[key]}</option>"`);
}

function hideProofDetailsFields(element, clearValues=true)
{
    let response = fetchProofDetails("VISIBLE_FIELDS_ONLY");
    let proof_details_visible_fields = response.visible_fields;
    if(!element.val())
        return;
    let visible_fields = proof_details_visible_fields[element.val()].concat([ "proof_type", "proof_request_for", "proof_file_upload", "proof_file_path"] );
    if(clearValues) {
        element.closest("tr").find(".file-info .info").html("");
        element.closest("tr").find(".file-upload-container").empty().removeClass("show");
    }
    element.closest("tr").find(".proof-details-fields").each(function () {
        if (!visible_fields.includes($(this).attr("name"))) {
            $(this).addClass("not_required");
            if(!$(this).hasClass('custom-file-upload'))
            $(this).attr("disabled", true);
        } else {
            $(this).removeClass("not_required").removeClass("has_error");
            if(clearValues)
                $(this).removeAttr("disabled");
        }
        if (clearValues && element[0] != $(this)[0]) $(this).val(null);
    });
}

function fetchProofDetails(additional_params = null)
{
    const visaType = getVisaConfig('visa_type', $('#visa_type').val());
    if(['default', 'short_term'].includes(visaType))
        var data = {module: getVisaConfig("module_id"), request_for: $('#request_for').val(), origin: $('#origin').val() , additional_params: additional_params};
    else
        var data = {module: getVisaConfig("module_id"), edit_id: $('#edit_id').val(), additional_params: additional_params};
    return postData({
        url: "/list_respective_user_proof_details",
        data: data,
        async: false,
    });
}

function loadProofDetails()
{
    if( !$('.proof-details-fields[name="proof_type"]').length ) return;
    if( !isOffshoreuser($('#origin').val() || $('#hidden_origin').val() ) )
        disableProofRequestFor();
    const response = fetchProofDetails();
    const userDetails = response.user_details;
    const proofDetails = response.proof_details;
    const mandatory_status = response.mandatory_status;
    if(!mandatory_status){
        $('.proof-details-fields').addClass('not_required_field');
        return;
    }
    if(!$('#date_of_birth').val()) $('#date_of_birth').val(userDetails?.dob).attr('disabled',true);
    if(!$('#address').val()) $('#address').val(userDetails?.address).attr('disabled',true);
    if(!$('#date_of_joining').val()) $('#date_of_joining').val(userDetails?.doj).attr('disabled',true);
    // if(!$('#date_of_joining').val()) $('#date_of_joining').datepicker({format: 'd-M-Y', autoclose: true}).datepicker('update', new Date(userDetails?.doj)).trigger('change').attr('disabled',true);
    var isUserDetailsFound = false;
    let visaType = getVisaConfig('visa_type', $('#visa_type').val())
                ||  getVisaConfig('visa_type', $('#hidden_visa_type').val());
    if(visaType == 'short_term'){
        if(userDetails?.dob&&userDetails?.address) isUserDetailsFound=true
    }else{
        if(userDetails?.dob&&userDetails?.address&&userDetails?.doj) isUserDetailsFound=true
    }
    let requestFor = getVisaConfig('request_for', $('#request_for').val()) || getVisaConfig('request_for', $('#hidden_request_for').val());
    var proofFound = true;
    if(["self","employee","family"].includes(requestFor) && mandatory_status) {
        proofDetails.forEach( (e) => {
            const mandatory_field_list = response.mandatory_field_list;
            const proofCount = e.length; const mandatoryProofCount = Object.values(mandatory_field_list).length;
            if(proofCount < mandatoryProofCount) {
                proofFound = false;
            } else {
                let proofType = e["proof_type"]
                if(mandatory_field_list.hasOwnProperty(proofType) && proofFound) {
                    proofFound = mandatory_field_list[proofType].every( (v) => e[v] );
                }
            }
        } );
    }
    if((!proofFound && !$('.prev_proof_error').length) || !isUserDetailsFound) {
        $('#action_prevent_error').append('<div class="system-message error prev_proof_error">'
            +'<img src="/images/error.svg" alt="error">'
            +'<span>You are unable to continue as proof details are not in IDM. Please connect with your HR partner to update the same in IDM</span></div>');
        $('.request_action_buttons').attr('disabled', true);
        if(!proofFound)
            return;
    }
    proofDetails.forEach( (details, index) => {
        if(index) cloneRow($('#proof-details-table').find('tbody tr:last'));
        for(let name in details){
            var field = $(`.proof-details-fields[name="${name}"]:eq(${index})`);
            if(field.hasClass('date')) {
                var id = field.attr('id');
                $('#'+id).datepicker('setDate', details[name]);
                $('#'+id).val(details[name]);
            } else {
                field.val(details[name]);
            }
            if(name == "proof_type") {
                hideProofDetailsFields( field, false );
            }
            if(requestFor == 'self' || requestFor == 'employee' || getVisaConfig('request_for',details['proof_request_for']) == "self") {
                if( !['proof_name'].includes( field.attr('name') ) ) field.attr('disabled', true);
                $(`.proof-action-btn-grp:eq(${index})`).hide();
            }
        }
        if(Object.keys(details.proof_file_details).length) {
            display( details.proof_file_details,  $(`.proof-details-fields[name="proof_file_upload"]:eq(${index})`) );
        }
    } );
    if(requestFor == "self" || requestFor == "employee") {
        $('#proof-details-table tfoot').hide();
    } else if(requestFor == "family"){
        cloneRow($('#proof-details-table tbody tr:last'));
    }
}

// add new row
function addRow()
{
    const table = $('#proof-details-table');
    const lastRow = table.find('tbody tr:last');
    let allFieldsFilled = true;
    lastRow.find('.proof-details-fields').each(function () {
        if( !$(this).hasClass('not_required') && !$(this).hasClass('custom-file-upload') && !$(this).val()  ) {
            allFieldsFilled = false
            $(this).addClass('has_error');
            if($(this).closest('.file-wrapper').length) {
                $(this).closest('.file-wrapper').addClass('has_error');
            }
        }
    });
    if(allFieldsFilled) {
        lastRow.find('[name="proof_type"] option').prop('disabled',false);
        lastRow.find('[name="proof_request_for"] option').prop('disabled',false);
        cloneRow(lastRow);
        disableProofType();
        disableProofRequestFor();
        showVisaInfoMsg();
    } else {
        const message = getVisaConfig("errorMessage", "ERR02");
        const closeIcon = `<span class="alert_close"><img title="Close alert" src="/img/close-red.svg" /></span>`;
        $('.alert-danger').show().html(message).append(closeIcon);
        let timeoutId = setTimeout( () => $('.alert-danger').hide(), 10000 );
        $('.alert_close').on('click', function () {
            clearTimeout(timeoutId);
            $('.alert-danger').hide();
        })
    }
    $( '[name="proof_expiry_date"]' ).datepicker({format: 'dd-M-yyyy', autoclose: true}).datepicker('setStartDate', new Date());
    $( '[name="proof_issue_date"]' ).datepicker({format: 'dd-M-yyyy', autoclose: true}).datepicker('setEndDate', new Date());
}

// delete a row
function removeRow()
{
    let element = $(this);
    let currentRequestFor = element.parent('tr').find('.proof-details-fields[name="proof_request"]').val();
    let requestFor = getVisaConfig('request_for', $('#request_for').val()) || getVisaConfig('request_for', $('#hidden_request_for').val() );
    let row = $(this).closest('tr')
    let count = $('#proof-details-table tbody tr').length;
    if(['self', 'employee'].includes(requestFor) || getVisaConfig('request_for', currentRequestFor) == "self") {
        if(count == 1)
            clearRow(row);
        else 
            row.remove();
        toggleProofAddBtn();
    } else if(requestFor == "family") {
        let selfCount = $('#proof-details-table tbody tr').filter(function () {
           return getVisaConfig('request_for', $(this).find('.proof-details-fields[name="proof_request_for"]').val()) == "self";
        }).toArray().length;
        if(count == selfCount+1)
            clearRow(row);
        else
            row.remove();
    }
    showVisaInfoMsg();
    toggleValidationForOnsiteUsers( $('#proof-details-table tbody tr:first-child') );
}

//clear the proof details fields value
function clearRow(element) {
    element.find('.proof-details-fields').each(function () { 
        $(this).val("");
        $(this).removeClass('has_error');
        $(this).removeClass('not_required');
        $(this).removeAttr('disabled');
        if( $(this).hasClass('custom-file-upload') ) $(this).attr('count', 0);
     });
    element.find(".file-info .info").html("");
    element.find(".file-upload-container").empty().removeClass("show");
    element.find(".upload-icon").removeClass("uploaded");
}

function cloneRow(lastRow)
{
    const newRow = lastRow.clone();
    let isOffshoreUser = isOffshoreuser( $('#origin').val() || $('#hidden_origin').val() );
    if(!isOffshoreUser)
        newRow.find('.proof-details-fields').each(function () { $(this).addClass('not_required_field') });
    clearRow(newRow);
    newRow.find('.proof-details-fields').each(function () {
        var id = $(this).attr('id');
        var name = $(this).attr('name');
        var value = $(this).val();
        if(id) {
            no = parseInt( /[0-9]+$/.exec(id)?.toString() ) || "";
            var newId = id.replace(no, no+1);
            $(this).attr('id', newId);
        }
    });
    var requestFor = getVisaConfig('request_for', $('#request_for').val()) || getVisaConfig('request_for', $('#hidden_request_for').val());
    if( ['self', 'employee'].includes(requestFor) ) {
        var selectedProofTypes = $('.proof-details-fields[name="proof_type"]').map( function () { return $(this).val() } ).toArray();
        $(this).children().each(function () {
            if(selectedProofTypes.includes($(this).val())) $(this).attr('disabled',true);
        });
        var canAdd = $(this).children().filter(function () { return $(this).val() && !$(this).attr('disabled') }).toArray().length;
        if(canAdd) $('#proof-details-table tfoot').hide();
    } else if( ['family'].includes(requestFor) ) {
        const maxRowCount = 9;
        let count = $('#proof-details-table tbody tr').filter(function () { return getVisaConfig('request_for', $(this).find('.proof-details-fields[name="proof_request_for"]').val()) == requestFor }).toArray().length;
        if(count+1 == maxRowCount) $('#proof-details-table tfoot').hide();
    }
    newRow.find('.proof-action-btn-grp').show();
    newRow.find( '[name="proof_expiry_date"]' ).datepicker({format: 'dd-M-yyyy', autoclose: true}).datepicker('setStartDate', new Date());
    newRow.find( '[name="proof_issue_date"]' ).datepicker({format: 'dd-M-yyyy', autoclose: true}).datepicker('setEndDate', new Date());
    newRow.insertAfter( lastRow );
}

function updateEducationQualificationList(response)
{
    const options  = response.education_details;
    $('#education_details_id').children().each(function () {
        if ( $(this).val() ) $(this).remove();
    });
    for(let key in options) {
        $('#education_details_id').append(`<option value=${key}>${options[key]}</option>`)
    }
}

function clearProofDetails()
{
    const table = $('#proof-details-table');
    const firstRow = table.find('tbody tr:first-child');
    const tableRows = table.find('tbody tr:not(:first-child)');
    tableRows.remove();
    firstRow.find('.proof-details-fields').each(function () {
        $(this).removeClass('not_required');
        $(this).removeAttr('disabled');
        $(this).val(null);
        if( $(this).attr('name') == 'proof_request_for' ) {
            $(this).children().each( function () {
                if( getVisaConfig('request_for', $(this).val()) == "employee" )
                    $(this).remove();
            } );
        }
        if($(this).hasClass('custom-file-upload')){
            $(this).attr('count',0);
            let wrapper = $(this).closest('.file-wrapper');
            wrapper.find(".file-info .info").html("");
            wrapper.find(".file-upload-container").empty().removeClass("show");
        }
    });
    $('#proof-details-table thead tr th:last-child, #proof-details-table tbody tr td:last-child').show();
    firstRow.find('.proof-action-btn-grp').show();
    table.find('tfoot').show();
}
// Show visa info message for family travel
function showVisaInfoMsg()
{
    const maxCount = 3;
    var requestFor = $('#request_for').val() || $('#hidden_request_for').val();
    var count = $('#proof-details-table .proof-details-fields[name="proof_request_for"]').filter(function () {
        return getVisaConfig('request_for', $(this).val()) == "family";
    }).toArray().length;
    if(getVisaConfig('request_for', requestFor) == "family" && count+1 > maxCount) {
        $('.visa-stamping-notes').show();
    } else {
        $('.visa-stamping-notes').hide();
    }
}

/**
 * Validations
 */
$('.numbers').on('input', function() {
    const pattern = /[^0-9]/g;
    let filteredValue = $(this).val().replace(pattern, '');
    $(this).val(filteredValue);
});
$('.months').on('input', function () {
    const pattern = /^[0-9]/g;
    let filteredValue = $(this).val().replace(pattern, '').substring(2);
});
$(document).on('input', 'input[name="amount"],.amount', function () {
    const maxLimit = 9;
    let value = $(this).val().replace(/\.(?=.*\.)/g,'');    
    let number; let decimal;let dotExists = value.includes('.');
    if(dotExists)
        [number, decimal] = value.split('.');
    else
        number = value;
    let filteredValue = number.replace(/[^0-9]+/g, '').substring(0, maxLimit);
    let remainingLength = maxLimit - filteredValue.length;
    let filteredDecimal;
    if(decimal)
        filteredDecimal = decimal.replace(/[^0-9]+/g,'').substring(0, remainingLength);
    if(dotExists && remainingLength && filteredValue)
        filteredValue = [filteredValue, filteredDecimal].join('.');
    $(this).val(filteredValue);
});
$(document).on('change', 'input[name="minimum_wage"],select[name="visa_currency"]', function () {
    currency_type=$(this).find('option:selected').text();
    currency_format_changes_for_same_row(currency_type,'minimum_wage');
});
$(document).on('change', 'select[name="proof_request_for"]', function () {
    var proof_val=$(this).closest("tr").find('[name="proof_type"]').val();
    //$(this).closest("tr").find('[name="proof_type"]').val("");
    var cur_proof_type=$(this);
    if($(this).val()=="RF_08"){
        $(this).closest("tr").find('[name="proof_type"]').val("");
        var flag=1;
        $('[name="proof_request_for"]').each(function(){
            if($(this).closest("tr").find('[name="proof_type"]').val() && $(this).val()=="RF_08"){
                if($(this).closest("tr").find('[name="proof_type"]').val()==proof_val){
                    flag=0;
                }
                cur_proof_type.closest("tr").find('[name="proof_type"] option[value="'+$(this).closest("tr").find('[name="proof_type"]').val()+'"]').prop('disabled',true);

            }
            

        });
        if(flag==1)
        $(this).closest("tr").find('[name="proof_type"]').val(proof_val);
    }else{
        $('[name="proof_type"] option').prop('disabled',false);
    }
   // $(this).closest("tr").find('[name="proof_type"]').val(proof_val);

});
$(document).on('change', 'input[name="approver_anticipated_amount"],select[name="approver_currency_code"],select[name="currency_code"]', function () {
    currency_type=$(this).find('option:selected').text();
    currency_format_changes_for_same_row(currency_type,'approver_anticipated_amount');
});

$(document).on('change', 'input[name="salary_range_from"],input[name="salary_range_to"],input[name="us_salary"],input[name="one_time_bonus"]', function () {
    currency_type=$('.input-group-text').text();
    currency_format_changes(currency_type);
});
 
$(document).on('focusout','input.currency_format',function(e) {
    if($('select[name="approver_currency_code"]').length > 0){
        var currency_type=$('select[name="approver_currency_code"] option:selected').text();
        input_class='approver_anticipated_amount';
    }
    if($('select[name="visa_currency"]').length > 0){
        var currency_type=$('select[name="visa_currency"] option:selected').text();
        input_class='minimum_wage';
    }
    if($(this).hasClass('salary_range_from')){
        var currency_type=$('.input-group-text').text();
        input_class='salary_range_from';
    }
    if($(this).hasClass('salary_range_to')){
        var currency_type=$('.input-group-text').text();
        input_class='salary_range_to';
    }
    if($(this).hasClass('us_salary')){
        var currency_type=$('span.input-group-text').first().text();
        input_class='us_salary';
    }
    if($(this).hasClass('one_time_bonus')){
        var currency_type=$('.input-group-text').first().text();
        input_class='one_time_bonus';
    }
    console.log(currency_type);
    if(currency_type && input_class)
    currency_format_changes_for_same_row(currency_type,input_class);
});
function currency_format_changes_for_same_row(currency_type,input_class){
    $('.'+input_class+'.currency_format').each(function(){
      CurrencyFormat({
        entity:$(this),
        allowDecimal:true,
        decimalPoint:2,
        prefix:'',
        currencyType:currency_type
      });
    });
  }

  function currency_format_changes(currency_type){
    $('.currency_format').each(function(){
      CurrencyFormat({
        entity:$(this),
        allowDecimal:true,
        decimalPoint:2,
        prefix:'',
        currencyType:currency_type
      });
    });
  }

function toggleRequestForSelf(element)
{
    let proofType = element.val();
    let selectedProofTypes = $('#proof-details-table tbody tr .proof-details-fields[name="proof_type"]').map(function () {
        let currentProofRequestType = $(this).closest('tr').find('.proof-details-fields[name="proof_request_for"]').val();
        if( currentProofRequestType == "RF_08" && element[0] != $(this)[0] && $(this).val() ) return $(this).val();
    }).toArray();
    if( selectedProofTypes.includes(proofType) )
        element.closest('tr').find(`.proof-details-fields[name="proof_request_for"] option[value='RF_08']`).attr('disabled', true);
    else
        element.closest('tr').find(`.proof-details-fields[name="proof_request_for"] option[value='RF_08']`).removeAttr('disabled');
}
function alter_visa_request_view(view_type){
    if(view_type=='list_view'){
        $('#progressbar li.active').each(function(){
            var target_tab=$(this).attr('id');
            $('.'+target_tab+'_header').show();
            var target_section = target_tab.replace('tab', 'section');
            $('.' + target_section).show();
            $('#visa_page_view_btn').val('grid_view').attr('src','/images/grid-view.svg').attr('alt','Grid view');
            $('#progressbar').hide();
        });
    }
    else{
        $('#progressbar').show();
        $('.visa_page_view_header').hide();
        $('.visa-request-section').hide();
        $('#progressbar li.current').trigger('click');
        $('#visa_page_view_btn').val('list_view').attr('src','/images/list-view.svg').attr('alt','List view');;
    }
    if( getVisaConfig( 'visa_type', $('#visa_type').val() ) == "long_term" )
        $('.short_term_hide').hide();
  }
  // To disable the proof details fields
  function disableProofDetailsFields()
  {
    let table = $('#proof-details-table');
    let isEditable = table.find('.proof-details-fields[name="proof_type"]').length;
    let isOffshoreUser = isOffshoreuser( $('#origin').val() || $('#hidden_origin').val() );
    if(isEditable && isOffshoreUser){
        let response = fetchProofDetails("MANDATORY_FIELDS_ONLY");
        let mandatoryFields = response?.mandatory_field_list;
        mandatoryFields = Object.keys(mandatoryFields);
        table.find('.proof-details-fields[name="proof_type"]').each(function () {
            let currentProofType = $(this).val()
            let currentRow = $(this).closest('tr');
            let currentRequestFor = currentRow.find('.proof-details-fields[name="proof_request_for"]').val();
            if(getVisaConfig('request_for', currentRequestFor) == "self" && mandatoryFields.includes(currentProofType)){
                currentRow.find('.proof-details-fields').each(function () { if(!['proof_name'].includes( $(this).attr('name') ) && !$(this).hasClass('custom-file-upload')) $(this).attr('disabled', true); })
                currentRow.find('.proof-action-btn-grp').hide();
            }
            
        });
        if(!isOffshoreUser)
            $('.proof-details-fields').addClass('not_required_field');
    }
  }
  // Check whether the origin is onsite or offshore
  function isOffshoreuser(origin)
  {
    let offshoreLocations = getVisaConfig('offshoreLocations');
    return offshoreLocations.includes(origin);
  }
  
  // Toggle validation for onsite users
  function toggleValidationForOnsiteUsers(row)
  {
    if(!isOffshoreuser($('#origin').val() || $('#hidden_origin').val())){
        let fields = row.find('.proof-details-fields');
        let haveValues = fields.map(function () { return $(this).val(); }).toArray().some(e => e);
        if(haveValues)
            fields.each(function () { $(this).removeClass('not_required_field') });
        else
            fields.each(function () { $(this).addClass('not_required_field'); });
    }
  }

  // To disable the proof request for
  function disableProofRequestFor()
  {
    let table = $('#proof-details-table');
    let row = table.find('tbody tr');
    let requestFor = getVisaConfig('request_for', $('#request_for').val());
    if(requestFor == "self") {
        row.find('.proof-details-fields[name="proof_request_for"]').children().each(function () {
            if( $(this).val() && getVisaConfig('request_for', $(this).val()) != "self" )
                $(this).attr('disabled', true);
        })
    }
  }

  // To disable the proof type
  function disableProofType()
  {
    let table = $('#proof-details-table');
    let row = table.find('tbody tr:last-child');
    let requestFor = getVisaConfig('request_for', $('#request_for').val());
    if(requestFor == "self") {
        let selectedProofTypes = table.find('.proof-details-fields[name="proof_type"]').map(function () {
            if( $(this).val()) return $(this).val();
        }).toArray();
        row.find('.proof-details-fields[name="proof_type"]').children().each(function () {
            if( $(this).val() && selectedProofTypes.includes( $(this).val() && !$(this).is(':selected') ) )
                $(this).attr('disabled', true);
            else
                $(this).removeAttr('disabled');
        })
        let count = row.find('.proof-details-fields[name="proof_type"]').children().filter(function () { return $(this).val() && !selectedProofTypes.includes( $(this).val() )  }).toArray().length;
        if(count <= 1) $('#proof-details-table tfoot').hide();
        else $('#proof-details-table tfoot').hide();
    }
  }
  function toggleProofAddBtn()
  {
    let proofTypeCount = $('#proof-details-table tbody tr .proof-details-fields[name="proof_type"]').children().filter( function () { return $(this).val(); } ).toArray().length;
        let selectedProofTypeCount = $('#proof-details-table tbody tr .proof-details-fields[name="proof_type"]').filter( function () { return $(this).val(); } ).toArray().length;
        console.log(proofTypeCount, selectedProofTypeCount, proofTypeCount - selectedProofTypeCount)
        if(proofTypeCount - selectedProofTypeCount >= 1)
            $('#proof-details-table tfoot').show();
        else
            $('#proof-details-table tfoot').hide();
  }
  $(document).on('keyup', '.date', function (e) {
	if(e.keyCode == '8')
  	$(this).val('').datepicker('update');
})

$(document).on("change", "#origin", function () {
    proof_mandatory_symbol();

    
});
function proof_mandatory_symbol(){
    if(!isOffshoreuser($('#origin').val() || $('#hidden_origin').val())){
        
        $('#proof-details-table thead tr th').each(function () {
           
            if($(this).hasClass("required-field")){
                $(this).removeClass("required-field")

            }
        })

    }else{
        $('#proof-details-table thead tr th').each(function () {
            if(!$(this).hasClass("required-field")){
                $(this).addClass("required-field")

            }
        })

    }
}


setTimeout(function(){
    $(document).on('input change select2:select', 'input, select, textarea', function (e) {
        handleRefresh();
    });

},500);

function toggleOrigin(request_for)
{
    if(["self", "employee"].includes(request_for)) {
        $('#origin').prop('disabled', true);
    } else {
        $('#origin').prop('disabled', false);
    }
}

function filingFieldVisible(visa_type,country,exception_list=[]){

    if(visa_filing_eligible_country.includes(country) && visa_filing_eligible_type.includes(visa_type)){
        $(document).find('#filing_type_id').closest(".long_term_hide").show();
        if($(document).find('#filing_type_id').hasClass('not_required_field')){
            $(document).find('#filing_type_id').removeClass('not_required_field');
        }
        $(document).find('#filing_type_id').closest("div").show();
    }else{
        $(document).find('#filing_type_id').closest(".long_term_hide").hide();
        $(document).find('#filing_type_id').closest("div").hide();
            if(!$(document).find('#filing_type_id').hasClass('not_required_field')){
                $(document).find('#filing_type_id').addClass('not_required_field');
            }
            $(document).find('#filing_type_id').val('');
    }
    return exception_list;

}
