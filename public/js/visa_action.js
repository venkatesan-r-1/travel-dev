// handling form submit
let timeoutId = null;
$(document).on('click', '.request_action_buttons', function () {
    let data = {}; let hasError = false;
    // undertaken completed
    if($('#acceptance_by_user').is(':checked'))
        $('#acceptance_by_user').val(1);
    else
        $('#acceptance_by_user').val(0);
    // traveling type id
    if(getVisaConfig('travel_type', $('#traveling_type_id').val()) != "family") {
        $('[name="dependency_details"]').addClass('not_required_field');
    } else {
        $('[name="dependency_details"]').removeClass('not_required_field');
    }
    // interview_type
    if(getVisaConfig('interview_type', $('#visa_interview_type_id').val()) != "regular") {
        $('[name="visa_ofc_date"]').addClass('not_required_field');
        $('[name="visa_interview_date"]').addClass('not_required_field');
    } else {
        $('[name="visa_ofc_date"]').removeClass('not_required_field');
        $('[name="visa_interview_date"]').removeClass('not_required_field');
    }
    // billed_to_client
    let billed_to_client=null;
    if($('[name="billed_to_client"]').length) {
        if($('#bill_to_client_1').is(':checked'))
            billed_to_client = 1;
        else if($('#bill_to_client_2').is(':checked'))
            billed_to_client = 0;
        $('[name="billed_to_client"]').val(billed_to_client);
    }
    // removing reporting manager from mandatory field list if it is empty
    let managerCount = $('[name="us_manager_id"]').children().filter(function () {
        return $(this).val();
    }).toArray().length;
    if(managerCount == 0) $('[name="us_manager_id"]').addClass('not_required_field');

    let action = $(this).val();
    if(action == "offshore_hr_review" && $('#acceptance_by_user').val() == 0)
            action = 'offshore_hr_reject';
    if(action == "visa_action")
            action = $('#visa_status_id').val() == 2 ? "visa_reject" : "visa_approve";
    data['action']=action;
    data['billed_to_client']=billed_to_client;
    data['travel_request_for']='';
    data['module']='';
    data['origin']='';
    data['visa_type']='';
    data['request_for_code']='';
    data['visa_category']='';
    data['filing_type']='';
    data['date_of_birth']='';
    data['address']='';
    data['education_category_id']='';
    data['education_details_id']='';
    data['date_of_joining']='';
    data['india_experience_years']='';
    data['india_experience_months']='';
    data['overall_experience_years']='';
    data['overall_experience_months']='';
    data['cv_file_path']='';
    data['degree_file_path']='';
    data['proof_type']=[];
    data['proof_request_for']=[];
    data['proof_number']=[];
    data['proof_issue_date']=[];
    data['proof_expiry_date']=[];
    data['proof_issued_place']=[];
    data['proof_file_path']=[];
    data['proof_name']=[];
    data['to_country']='';
    data['to_city']='';
    data['from_date']='';
    data['to_date']='';
    data['visa_number']=[];
    data['department_code']='';
    data['project_code']='';
    data['customer_name']='';
    data['practice_unit_code']='';
    data['requestor_entity']='';
    data['remarks']='';
    data['common_action_comments']=$(".common_action_comments").val()?$(".common_action_comments").val():'';
    data['comments']=[];
    data['file_reference_id']=[];
    data['anticipated_row_id']=[];
    data['anticipated_currency']=[];
    data['master_category']=[];
    data['category']=[];
    data['sub_category']=[];
    data['amount']=[];
    data['anticipated_comments']=[];
    data['excluded_row']=[];
    data['approver_anticipated_amount']='';
    data['approver_currency_code']='';
    data['on_behalf']='';
    data['minimum_wage']='';
    data['work_location']='';
    data['band_detail']='';data['salary_range_from']='';data['salary_range_to']='';data['us_job_title_id']='';
    data['edit_id'] = $('#edit_id').val();
    data['acceptance_by_user']=''; data['us_salary']=''; data['one_time_bonus']=''; data['one_time_bonus_payout_date']; data['next_salary_revision_on']='';
    data['us_manager_id']=''; data['inszoom_id']=''; data['entity_id']=''; data['attorneys_id']=''; data['petition_file_date']=''; data['receipt_no']=''; data['petition_start_date']=''; data['petition_end_date']=''; data['petition_file_path']='';
    data['entry_type_id']=''; data['visa_interview_type_id']=''; data['visa_ofc_date']=''; data['visa_interview_date']=''; data['visa_status_id']='';
    data['record_number']=''; data['most_recent_doe']=''; data['admit_until']=''; data['gc_initiated_on']='';
    data['travel_date']=''; data['travel_location']=''; data['visa_number']=''; data['visa_file_path']=''; data['traveling_type_id']='';data['dependency_details']=[];
    data['visa_currency']=''; data['visa_job_titile']=''; data['visa_process_comments']='';
    data["behalf_of"] = $('#employee').val() || null;

    // basic request page
    $('#visa-details-section').find('input, select, textarea').each(function () {
        let name = $(this).attr('name');
        let value = $(this).val();
        value = value && value.trim(); 
        let exceptionList = getExceptionList(action);
        if($(this).hasClass('not_required_field'))
            exceptionList.push(name);
        var validated = validateFields(name, value, exceptionList);
        if ( validated.isValid ) {    
            data[name] = value;
        } else {
            console.log("Empty field : " + name);
            $(this).addClass("has_error");
            if( $(this).hasClass("select-plugin") ) 
                $(this).parent().find(".select2-container").addClass("has_error");
            if( $(this).hasClass(".custom-file-upload") )
                $(this).closest('.file-wrapper').addClass("has_error");
            hasError = true;
            displayErrorMessage(validated.errorCode);
        }
    });

    // personal info page
    $('#personal-info-section').find('input, select, textarea').each(function () {
        let name = $(this).attr('name');
        let value = $(this).val();
        value = value && value.trim(); 
        let exceptionList = getExceptionList(action);
        if($(this).hasClass('not_required_field') || $(this).hasClass('not_required'))
            exceptionList.push(name);
        var validated = validateFields(name, value, exceptionList);
        if ( validated.isValid ) {    
            data[name] = value;
        } else {
            console.log("Empty field : " + name);
            $(this).addClass("has_error");
            if( $(this).hasClass("select-plugin") ) 
                $(this).parent().find(".select2-container").addClass("has_error");
            if( $(this).hasClass("custom-file-upload") )
                $(this).closest('.file-wrapper').find('.file-info').addClass("has_error");
            hasError = true;
            displayErrorMessage(validated.errorCode);
        }
    });

    const proofDetailsFields = ["proof_type", "proof_request_for", "proof_file_path", "proof_number", "proof_name", "proof_issue_date", "proof_expiry_date", "proof_issued_place", "file_reference_id"];
    proofDetailsFields.forEach( (name) => {
        const table = $('#proof-details-table');
        if(name == "file_reference_id") {
            data[name] = table.find(`[name="proof_file_upload"]`).map(function () { return $(this).attr('file_reference_id') ?? ""; }).toArray();
        } else {
            data[name] = table.find(`[name=${name}]`).map(function () { return $(this).val() ?? ""; }).toArray();
        }
    } );

    // anticipated cost section
    var travel_desk_row_count = 0;
      $("#anticipated_cost_body tr").each(function(k,v){
        if(!$(this).hasClass('travel-desk-completed')){
          var excluded_row = parseInt($(this).attr('data-excluded_row'));
          var row_no = $(this).attr('data-row_no');
          $(this).find('input,select,textarea').each(function() {
              var value = $(this).val();
              if($(this).hasClass('currency_format')){
                value=value.replace(/\,/g, '');
              }
              var name = $(this).attr('name');
              if($.inArray(name,['master_category','category','anticipated_currency','amount']) !==-1 && !value && !excluded_row){
                if($(this).hasClass('select-plugin'))
                  $(this).parent().find('.select2-container').addClass('has_error');
                else
                  $(this).addClass('has_error');
                  hasError = true;
              }
              data['anticipated_row_id'][travel_desk_row_count] = row_no;
              data['excluded_row'][travel_desk_row_count] = excluded_row;
              data[name][travel_desk_row_count] = value;
          });
          travel_desk_row_count++;
        }
      });

            // immigration review section
            $('#immigration-review-section').find('input, select, textarea').each(function () {
                let name = $(this).attr('name');
                let value = $(this).val();
                value = value && value.trim(); 
                if($(this).hasClass('currency_format')){
                    value=value.replace(/\,/g, '');
                  }
                let exceptionList = getExceptionList(action);
                if($(this).hasClass('not_required_field') || $(this).hasClass('not_required'))
                    exceptionList.push(name);
                var validated = validateFields(name, value, exceptionList);
                if ( validated.isValid ) {    
                    data[name] = value;
                } else {
                    console.log("Empty field : " + name);
                    $(this).addClass("has_error");
                    if( $(this).hasClass("select-plugin") ) 
                        $(this).parent().find(".select2-container").addClass("has_error");
                    if( $(this).hasClass("custom-file-upload") )
                        $(this).closest('.file-wrapper').find('.file-info').addClass("has_error");
                    hasError = true;
                    displayErrorMessage(validated.errorCode);
                }
            });
    
            // onsite hr salary discussion
            $('#onsite-hr-review-section').find('input, select, textarea').each(function () {
                let name = $(this).attr('name');
                let value = $(this).val();
                value = value && value.trim(); 
                if($(this).hasClass('currency_format')){
                    value=value.replace(/\,/g, '');
                  }
                let exceptionList = getExceptionList(action);
                if($(this).hasClass('not_required_field') || $(this).hasClass('not_required'))
                    exceptionList.push(name);
                var validated = validateFields(name, value, exceptionList);
                if ( validated.isValid ) {    
                    data[name] = value;
                } else {
                    console.log("Empty field : " + name);
                    $(this).addClass("has_error");
                    if( $(this).hasClass("select-plugin") ) 
                        $(this).parent().find(".select2-container").addClass("has_error");
                    if( $(this).hasClass("custom-file-upload") )
                        $(this).closest('.file-wrapper').find('.file-info').addClass("has_error");
                    hasError = true;
                    displayErrorMessage(validated.errorCode);
                }
            });
    
            // offshore hr salary discussion
            $('#offshore-hr-review-section').find('input, select, textarea').each(function () {
                let name = $(this).attr('name');
                let value = $(this).val();
                value = value && value.trim(); 
                if($(this).hasClass('currency_format')){
                    value=value.replace(/\,/g, '');
                  }
                let exceptionList = getExceptionList(action);
                if($(this).hasClass('not_required_field') || $(this).hasClass('not_required')) {
                    if(Array.isArray(exceptionList)) exceptionList.push(name);   
                }
                var validated = validateFields(name, value, exceptionList);
                if ( validated.isValid ) {    
                    data[name] = value;
                } else {
                    console.log("Empty field : " + name);
                    $(this).addClass("has_error");
                    if( $(this).hasClass("select-plugin") ) 
                        $(this).parent().find(".select2-container").addClass("has_error");
                    if( $(this).hasClass("custom-file-upload") )
                        $(this).closest('.file-wrapper').find('.file-info').addClass("has_error");
                    hasError = true;
                    displayErrorMessage(validated.errorCode);
                }
            });
    
            // petition process
            $('#petition-process-section').find('input, select, textarea').each(function () {
                let name = $(this).attr('name');
                let value = $(this).val();
                value = value && value.trim(); 
                let exceptionList = getExceptionList(action);
                if($(this).hasClass('not_required_field') || $(this).hasClass('not_required')) {
			if(Array.isArray(exceptionList))
				exceptionList.push(name);
		}
                var validated = validateFields(name, value, exceptionList);
                if ( validated.isValid ) {    
                    data[name] = value;
                } else {
                    console.log("Empty field : " + name);
                    $(this).addClass("has_error");
                    if( $(this).hasClass("select-plugin") ) 
                        $(this).parent().find(".select2-container").addClass("has_error");
                    if( $(this).hasClass("custom-file-upload") )
                        $(this).closest('.file-wrapper').find('.file-info').addClass("has_error");
                    hasError = true;
                    displayErrorMessage(validated.errorCode);
                }
            });
    
            // visa process
            $('#visa-approval-section').find('input, select, textarea').each(function () {
                let name = $(this).attr('name');
                let value = $(this).val();
                value = value && value.trim(); 
                let exceptionList = getExceptionList(action);
                if($(this).hasClass('not_required_field') || $(this).hasClass('not_required')) {
                    if(Array.isArray(exceptionList)) exceptionList.push(name);   
                }
                var validated = validateFields(name, value, exceptionList);
                if ( validated.isValid ) {    
                    data[name] = value;
                } else {
                    console.log("Empty field : " + name);
                    $(this).addClass("has_error");
                    if( $(this).hasClass("select-plugin") ) 
                        $(this).parent().find(".select2-container").addClass("has_error");
                    if( $(this).hasClass("custom-file-upload") )
                        $(this).closest('.file-wrapper').find('.file-info').addClass("has_error");
                    hasError = true;
                    displayErrorMessage(validated.errorCode);
                }
            });
    
            // visa details
            $('#visa-detail-section').find('input, select, textarea').each(function () {
                let name = $(this).attr('name');
                let value = $(this).val();
                value = value && value.trim(); 
                let exceptionList = getExceptionList(action);
                if($(this).hasClass('not_required_field') || $(this).hasClass('not_required'))
                    exceptionList.push(name);
                var validated = validateFields(name, value, exceptionList)
                if ( validated.isValid ) {    
                    if(name == "dependency_details")
                        data[name].push(value);
                    else
                        data[name] = value;
                } else {
                    console.log("Empty field : " + name);
                    $(this).addClass("has_error");
                    if( $(this).hasClass("select-plugin") ) 
                        $(this).parent().find(".select2-container").addClass("has_error");
                    if( $(this).hasClass("custom-file-upload") )
                        $(this).closest('.file-wrapper').find('.file-info').addClass("has_error");
                    hasError = true;
                    displayErrorMessage(validated.errorCode);
                }
            });

            // visa details
            $('#visa-process-section').find('input, select, textarea').each(function () {
                let name = $(this).attr('name');
                let value = $(this).val();
                value = value && value.trim(); 
                let exceptionList = getExceptionList(action);
                if($(this).hasClass('not_required_field') || $(this).hasClass('not_required'))
                    exceptionList.push(name);
                var validated = validateFields(name, value, exceptionList);
                if ( validated.isValid ) {    
                    data[name] = value;
                } else {
                    console.log("Empty field : " + name);
                    $(this).addClass("has_error");
                    if( $(this).hasClass("select-plugin") ) 
                        $(this).parent().find(".select2-container").addClass("has_error");
                    if( $(this).hasClass("custom-file-upload") )
                        $(this).closest('.file-wrapper').find('.file-info').addClass("has_error");
                    hasError = true;
                    displayErrorMessage(validated.errorCode);
                }
            });

            // visa stamping
            $('#visa-stamping-section').find('input, select, textarea').each(function () {
                let name = $(this).attr('name');
                let value = $(this).val();
                value = value && value.trim();
                let exceptionList = getExceptionList(action);
                if($(this).hasClass('not_required_field') || $(this).hasClass('not_required'))
                    exceptionList.push(name);
                var validated = validateFields(name, value, exceptionList);
                if ( validated.isValid ) {    
                    data[name] = value;
                } else {
                    console.log("Empty field : " + name);
                    $(this).addClass("has_error");
                    if( $(this).hasClass("select-plugin") ) 
                        $(this).parent().find(".select2-container").addClass("has_error");
                    if( $(this).hasClass("custom-file-upload") )
                        $(this).closest('.file-wrapper').find('.file-info').addClass("has_error");
                    hasError = true;
                    displayErrorMessage(validated.errorCode);
                }
            });

            // billable section
            $('#billed_to_client').find('input, select, textarea').each(function () {
                let name = $(this).attr('name');
                let value = $(this).val();
                value = value && value.trim();
                if($(this).hasClass('currency_format')){
                    value=value.replace(/\,/g, '');
                }
                let exceptionList = getExceptionList(action);
                if($(this).hasClass('not_required_field') || $(this).hasClass('not_required'))
                    exceptionList.push(name);
                var validated = validateFields(name, value, exceptionList);
                if ( validated.isValid ) {    
                    if(name == "dependency_details")
                        data[name].push(value);
                    else
                        data[name] = value;
                } else {
                    console.log("Empty field : " + name);
                    $(this).addClass("has_error");
                    if( $(this).hasClass("select-plugin") ) 
                        $(this).parent().find(".select2-container").addClass("has_error");
                    if( $(this).hasClass("custom-file-upload") )
                        $(this).closest('.file-wrapper').find('.file-info').addClass("has_error");
                    if($(this).closest('.radio_required').length) {
                        $(this).closest('.radio_required').find('.alert_required').css('display', 'inline-block');
                    }
                    hasError = true;
                    displayErrorMessage(validated.errorCode);
                }
            });
      
    
    if ( timeoutId ) clearTimeout(timeoutId);
    timeoutId = clearAlert();
    if(!hasError) {
        $('.request_action_buttons').attr('disabled', true);
        const saveActions = ["save", "visa_user_save", "onsite_hr_save", "offshore_hr_save", "petition_process_save", "rfe_process_save", "visa_approval_save", "visa_entry_save", "save_visa_process"];
        if( !saveActions.includes(action) ) {
            var options = customizeConfirmationBoxContent(data['action'], saveRequestDetails, data);
            openConfirmationBox(options);
        } else {
            saveRequestDetails(data);
        }        
    }

});

$(document).on('click', '#need_assistance_btn', function () {
    data={};
    data['action']=$(this).val();
    data['edit_id'] = $('#edit_id').val();
    $.ajax({
      type:'POST',
      data:data,
      url:'/save_need_assistance_details', 
      dataType: 'json',         
      async:false,
      success:function(data,textStatus, jqXHR){ 
          if(data.next_action_details.mails_involved){
            $.ajax({
              type:'POST',
              url:'/send_mails',
              dataType:'json',
              data:{mail_name:data.next_action_details.mails_involved,request_id:data.request_id,action:data.action,mail_flag:"visa"},
              success:function(response){
                if(response.message) {
                    $('.alert-danger').html('');
                    $(".alert-danger").html(response.message).append('<span class="alert_close"><img title="Close alert" src="/img/close-red.svg" class=""></span>');
                    $(".alert-danger").show();
                    setTimeout(function() {
                        $(".alert-danger").hide();
                      }, 10000);
                }
              },
            });
          }
          setTimeout(function() {
            window.open(data.next_action_details.redirect_url,'_self');
          }, 500);
      },
    });
})

// validate fields
function validateFields(name, value, exceptionList = [])
{
    const fileInputNames = ["proof_file_upload", "cv_file_upload", "degree_file_upload", "petition_file_upload", "visa_file_upload"];
    if(fileInputNames.includes(name))
        value = $(`[name=${name.replace('_upload', '_path')}]`).val();
    if(typeof exceptionList == "object" && exceptionList.hasOwnProperty("except")){
        if(!exceptionList.except.includes(name))
            return {isValid: true};
    }
    else if(exceptionList.includes(name))
        return {isValid: true};
    switch(name) {
        case "minimum_wage":
            if(!value)
                return {isValid: false, errorCode: "ERR01"};
            else if(parseInt(value) == 0)
                return {isValid: false, errorCode: "ERR03"};
            return {isValid: true};
        case "salary_range_from":
            var minimum_wage = parseInt( $('#minimum_wage_text').text().replace(/[^0-9.]/g, '') );
            if(!value)
                return {isValid: false, errorCode: "ERR01"};
            else if(parseInt(value.replace(/[^0-9.]/g, '')) < minimum_wage)
                return {isValid: false, errorCode: "ERR04"};
            else if(parseInt(value) == 0)
                return {isValid: false, errorCode: "ERR03"};
            return {isValid: true};
        case "salary_range_to":
            if(!value)
                return {isValid: false, errorCode: "ERR01"};
            else if(parseInt(value) < parseInt($('#salary_range_from').val().replace(/[^0-9.]/g, '')))
                return {isValid: false, errorCode: "ERR05"};
            else if(parseInt(value) == 0)
                return {isValid: false, errorCode: "ERR03"};
            return {isValid: true};
        case "us_salary":
            var [salary_range_from_text, salary_range_to_text] = $('#salary_range_text').text().split("to");
            var salary_range_from = parseInt(salary_range_from_text.replace(/[^0-9.]/g, ''));
            var salary_range_to = parseInt(salary_range_to_text.replace(/[^0-9.]/g, ''));
            if(!value)
                return {isValid: false, errorCode: "ERR01"};
            else if(parseInt(value) == 0)
                return {isValid: false, errorCode: "ERR03"};
            else if (parseInt(value) < salary_range_from || parseInt(value) > salary_range_to)
                return {isValid: false, errorCode: "ERR06"};
            return {isValid: true};
        case "receipt_no":
            if(!value)
                return {isValid: false, errorCode: "ERR01"};
            else if(value.length != 13)
                return {isValid: false, errorCode: "ERR07"};
            return {isValid: true};
        case "visa_status_id":
            let allowedVisaStatus = getVisaConfig('allowedVisaStatus');
            if(!value)
                return {isValid: false, errorCode: "ERR01"};
            else if(!allowedVisaStatus.includes(value))
                return {isValid: false, errorCode: "ERR09"};
            return {isValid: true};
        case "offer_letter_paths":
            if(!value)
                return {isValid: false, errorCode: "ERR08"};
            return {isValid: true};
        default :
            if(!value)
                return {isValid: false, errorCode: "ERR01"};
            return {isValid: true};
    }   
}

// get fields not need to validate
function getExceptionList(action) {
    let exceptionList = ["comments", "visa_process_comments"]
    if(action.includes('_reject'))
        action = 'reject';
    switch (action) {
        case "reject":
            return {except: ["comments"]};
        case "offshore_hr_review":
        case "offshore_hr_save":
            return exceptionList.concat(['one_time_bonus', 'next_salary_revision_on']);
        case "petition_process_save":
            return exceptionList.concat(['petition_start_date','us_manager_id','inszoom_id','attorneys_id','petition_file_date','receipt_no', 'petition_end_date', 'petition_file_upload','petition_file_path']);
        case "rfe_process_save":
        case "rfe_progress":
            return exceptionList.concat(['petition_start_date', 'petition_end_date', 'petition_file_upload','petition_file_path']);
        case "visa_approval_save":
            return exceptionList.concat(['visa_status_id']);
        default:
            return exceptionList;
    }
}

// save the form data
function saveRequestDetails(data = null)
{
    postData({
        url: "/save_request_details",
        data: data,
        successCallback: redirectPage,
        errorCallback: enableActionButtons,
    }); // src: visa_request.js
}

function redirectPage(data) {
    if(data.error){
        $('.alert-danger').html('');
        $(".alert-danger").html(data.message_text);
        $(".alert-danger").show();
        $('.request_action_buttons').removeAttr('disabled');
      } else {
        $(".alert-danger").hide();
        localStorage.clear();
        localStorage.setItem('message', data.next_action_details.message_text);
        if(data.next_action_details.mail){
            postData({
                type: 'POST',
                url: '/send_mails',
                dataType: 'json',
                aysnc: true,
                data: {mail_name: data.next_action_details.mail, request_id: data.request_id, action: data.action,mail_flag:"visa"},
                successCallback: function () { return true },
                errorCallback: enableActionButtons,
            });
        }
        setTimeout(function () {
            window.open(data.next_action_details.redirect_url, '_self');
        }, 500);
      }
    window.open(redirect_url, '__self');
}

// Display the error message
function displayErrorMessage(errorCode)
{
    const message = getVisaConfig("errorMessage", errorCode);
    const closeIcon = `<span class="alert_close"><img title="Close alert" src="/img/close-red.svg" class=""></span>`;
    $('.alert-danger').show().html(message).append(closeIcon);
}
// Clear the error message
function clearAlert()
{
    const timeoutId = setTimeout( () => $('.alert-danger').hide(), 10000 );
    $('.alert_close').on('click', function () {
        clearTimeout(timeoutId);
        $('.alert-danger').hide();
    })
    return timeoutId;
}

// To cutomize the confirmation box
function customizeConfirmationBoxContent(action, callback, ...params)
{
    var approvalRelatedActions = ['bu_head_approve','cp_approve','du_head_approve','du_head_hie_approve','fin_approve','geo_head_approve','project_owner_approve','project_owner_hie_approve','petition_approve','petition_approve_rfe'];
    var rejectRelatedActions = ['bu_head_reject','cp_reject','du_head_hie_reject','du_head_reject','fin_reject','geo_head_reject','project_owner_hie_reject','project_owner_reject',"bf_reject",'petition_reject','petition_reject_rfe'];
    var reviewRelatedActions = ['desk_review_fac','desk_review_fin','desk_review_visa','bf_review'];
    var processRelatedActions = ['visa_process'];
    var submitRelatedActions = ['submit', 'send_for_review', 'send_for_process', 'onsite_hr_review', 'offshore_hr_review', 'rfe_progress','visa_approve'];

    if( submitRelatedActions.includes(action) ){
      return {
        content: 'Are you sure want to submit?',
        primaryBtn: 'Submit',
        secondaryBtn: 'Cancel',
        primaryAction: callback,
        primaryActionParams: params,
        secondaryAction: enableActionButtons,
      }
    }
    else if(action == "offshore_hr_reject") {
        return {
            content: 'Employee is not undertaken the offer. Are you sure to continue?',
            primaryBtn: 'Submit',
            secondaryBtn: 'Cancel',
            primaryAction: callback,
            primaryActionParams: params,
            secondaryAction: enableActionButtons,
          }
    }
    else if(action == "publish") {
        return {
            content: 'Are you sure want to publish?',
            primaryBtn: 'Submit',
            secondaryBtn: 'Cancel',
            primaryAction: callback,
            primaryActionParams: params,
            secondaryAction: enableActionButtons,
          }
    }else if(action == 'visa_reject') {
        return {
            content: 'The visa status is selected as "Visa denied". Are you sure want to continue?',
            primaryBtn: 'Submit',
            secondaryBtn: 'Cancel',
            primaryAction: callback,
            primaryActionParams: params,
            secondaryAction: enableActionButtons,
          }
    } else if(reviewRelatedActions.includes(action)){
      return {
        content: 'Are you sure want to submit?',
        primaryBtn: 'Review',
        secondaryBtn: 'Cancel',
        primaryAction: callback,
        primaryActionParams: params,
        secondaryAction: enableActionButtons,
      }
    }else if(approvalRelatedActions.includes(action)){
      return {
        content: 'Are you sure want to approve?',
        primaryBtn: 'Approve',
        secondaryBtn: 'Cancel',
        primaryAction: callback,
        primaryActionParams: params,
        secondaryAction: enableActionButtons,
      }
    }else if(rejectRelatedActions.includes(action)){
      return {
        content: 'Are you sure want to reject?',
        primaryBtn: 'Reject',
        secondaryBtn: 'Cancel',
        primaryAction: callback,
        primaryActionParams: params,
        secondaryAction: enableActionButtons,
      }
    }else if(processRelatedActions.includes(action)){
      return {
        content: 'Are you sure want to process?',
        primaryBtn: 'Process',
        secondaryBtn: 'Cancel',
        primaryAction: callback,
        primaryActionParams: params,
        secondaryAction: enableActionButtons,
      }
    }else{
      return {};
    }
}

// To enable the request action button on cancel action
function enableActionButtons()
{
  $('.request_action_buttons').removeAttr('disabled');
}
