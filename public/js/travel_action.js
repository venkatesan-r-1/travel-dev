$(document).ready(function(){
$("#alert_required").css('display','none');
});
var validate={};
$(document).on('click','#ticket_required_1', function() {
  if($('#ticket_required_1').is(':checked')){
    $('input[name="family_traveling"]').removeAttr("disabled");
    $('input[name="accommodation_required"]').removeAttr("disabled");
    $('input[name="insurance_required"]').removeAttr("disabled");
    }
})
$(document).on('click','.has_error', function() {
  $(this).removeClass("has_error");
});
$(document).on('focus', '.has_error', function () {
  $(this).removeClass('has_error');
});
$(document).on('click','#ticket_required_2', function() {
  if($('#ticket_required_2').is(':checked')){

    $('#preferred_address').val('');
    $('#preferred_address').prop("disabled", true);
    //$('input[name="family_traveling"]').removeAttr("disabled");
    $('input[name="family_traveling"]').closest('.radio_required').find('.alert_required').hide();
    $('input[name="accommodation_required"]').closest('.radio_required').find('.alert_required').hide();
    $('input[name="insurance_required"]').closest('.radio_required').find('.alert_required').hide();
    $('input[name="family_traveling"]').prop("checked",false);
    $('input[name="accommodation_required"]').prop("checked",false);
    $('input[name="insurance_required"]').prop("checked",false);
    $('input[name="accommodation_required"]').attr("disabled",true);
    $('input[name="insurance_required"]').attr("disabled",true);
    $('input[name="family_traveling"]').attr("disabled",true);
    $('#no_child').attr("disabled",true);
    $('#no_child').css("display","none");
    $('#no_adult').attr("disabled",true);
    $('#no_adult').css("display","none")
    }
})
$(document).on('click','input[type=radio]', function() {
  $(this).closest('.radio_required').find('.alert_required').hide();

})
$(document).on('change','#request_for',function(){ 
  if($.inArray($(this).find(":selected").val(),['','RF_05','RF_01','RF_08'])<0){
    $('.country').removeAttr("disabled");
  }else{
    var default_country=$('#default_country').val();
    console.log(default_country);
    $('.country').val(default_country).change();
    $('.country').attr("disabled",true);
    disableToCountry( $('#origin').val() );
  }
  
  // if($(this).find(":selected").val()!='RF_05'){
  //   alert('test');
  //    // alert('test');
  //   $('.country').removeAttr("disabled");

  // }else if($(this).find(":selected").val()!='RF_01'){
  //   $('.country').removeAttr("disabled");

  // }
  // else{
  //   var default_country=$('#default_country').val();
  //   console.log(default_country);
  //   $('.country').val(default_country).change();
  //   $('.country').attr("disabled",true);

  // }
  if(originChanged) {
    load_from_city($('#origin').val());
  }
  
});
$(document).on('click','input[name="accommodation_required"]', function() {
  if($('#accommodation_1').is(':checked')){
    //$('input[name="family_traveling"]').removeAttr("disabled");
    $('#preferred_address').prop("disabled", false);

    }
    if($('#accommodation_2').is(':checked')){
      //$('input[name="family_traveling"]').removeAttr("disabled");
      $('#preferred_address').val('');
      $('#preferred_address').prop("disabled", true);
  
      }
})
$(document).on('click','#forex_required_1', function() {
  if($('#forex_required_1').is(':checked')){
    $('#currency').removeAttr("disabled");
    
    }
})
$(document).on('click','#forex_required_2', function() {
  if($('#forex_required_2').is(':checked')){
    //$('input[name="family_traveling"]').removeAttr("disabled");
    $('#currency').val('').change();
    //$('#currency').prop("checked",false);
    $('#currency').parent().find('.select2-container').removeClass("has_error");
    $('#currency').attr("disabled",true);
    }
})
$(document).on('click','#family_travel_1', function() {
  if($('#family_travel_1').is(':checked')){
    $('#no_child').removeAttr("disabled");
    $('#no_child').css("display","block");
    $('#no_adult').removeAttr("disabled");
    $('#no_adult').css("display","block");
    
    }
})
$(document).on('click','#family_travel_2', function() {
  if($('#family_travel_2').is(':checked')){
    $('#no_child').attr("disabled",true);
    $('#no_child').css("display","none");
    $('#no_adult').attr("disabled",true);
    $('#no_adult').css("display","none");
    
    }
})

$(document).on('click','.request_action_buttons', function() {
    var data={};
    validate={};
    var error_flag=0;
    var error_count=0;
    var actionvalue=$(this);
    if($("#edit_id").length)
      data['edit_id']=$("#edit_id").val();

    // clear the visa request id if the details changed
    // var visa_request_id = $('#visa_request_id').val();

    data['action']=$(this).val();
    data['billed_to_client']='';
    data['travel_request_for']='';
    data['module']='';
    data['origin']='';
    data['origin_city']='';
    data['request_for_code']='';
    data['travel_type_id']='';
    data['to_country']=[];
    data['from_city']=[];
    data['to_city']=[];
    data['from_date']=[];
    data['to_date']=[];
    data['visa_number']=[];
    data['visa_type_code']=[];
    data['ticket_file_path']=[];
    data['ticket_cost']=[];
    data['ticket_file_reference_id']
    data['travel_purpose_id']='';
    data['department_code']='';
    data['project_code']='';
    data['customer_name']='';
    data['practice_unit_code']='';
    data['requestor_entity']='';
    data['ticket_required']='';
    data['family_traveling']='';
    data['forex_required']='';
    data['currency_code']='';
    data['accommodation_required']='';
    data['prefered_accommodation']='';
    data['working_from']='';
    data['laptop_required']='';
    data['insurance_required']='';
    data['remarks']='';
    data['traveller_address']='';
    data['email']='';
    data['phone_no']='';
    data['dob']='';
    data['proof_type']=[];
    data['proof_request_for']=[];
    data['proof_file']=[];
    data['proof_number']=[];
    data['proof_issue_date']=[];
    data['proof_expiry_date']=[];
    data['proof_issued_place']=[];
    data['proof_file_path']=[];
    data['proof_name']=[];
    data['adult']='';
    data['child']='';
    data['travelling_details_row_id']=[];
    data['common_action_comments']=$(".common_action_comments").val()?$(".common_action_comments").val():'';
    data['action']=$(this).val();
    data['forex_details_row_id']=[];
    data['transaction_date']=[];
    data['mode_code']=[];
    data['comments']=[];
    data['transaction_type']=[];
    data['visa_process']='';
    data['visa_renewal_options']='';
    data['exiting_date']='';
    data['nationality']='';
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
    // data['visa_request_id']=visa_request_id ?? '';
    
      $('.visa_section').find('input,select,textarea').each(function () {
        if($('#visa_renewal_option_2').is(':checked')){
          $('input[name="visa_renewal_options"]').val(1);
        }
        else if($('#visa_renewal_option_1').is(':checked')){
          $('input[name="visa_renewal_options"]').val(0);
        }
        else{
          $('input[name="visa_renewal_options"]').val(null);
        }
        var name = $(this).attr('name');
        var value = $(this).val();
        if(value)
        {
          data[name]=value;
        }
        else
        {
          var validate_success = validate_fields(name, value, actionvalue.val());
          if(!validate_success)
          {
            if($(this).hasClass('select-plugin'))
              $(this).parent().find('.select2-container').addClass('has_error');
            else
              $(this).addClass('has_error');
            error_flag=1;
            $('.request_action_buttons').removeAttr('disabled');
            $(".alert-danger").html("Please fill the mandatory fields");
            setTimeout(function() {
              $(".alert-danger").hide();
            }, 10000);
          }
          else
          {
              data[name]=value;
          }
          $('html','body').animate({scrollTop:0},500);
        }
      });

      $(".additional_traveller_details").find('input[type=text],input[type=hidden],input[type=checkbox],select,textarea').each(function(){
          var name = $(this).attr('name');
          var value = $(this).val();
          if(value)
          {
              data[name] = value;
          }
          else
          {
              var validate_success = validate_fields(name, value, actionvalue.val());
              if(!validate_success)
              { 
               
                  if($(this).hasClass('myselect'))
                      $(this).parent().find('.select2-container').addClass('has_error');
                  else if($(this).hasClass('custom-file-upload'))
                    $(this).closest('.file-wrapper').addClass('has_error');
                  else
                      $(this).addClass('has_error');
                  error_flag=1;
                  $('.request_action_buttons').removeAttr('disabled');
                  $(".alert-danger").html("Please fill the mandatory fields");
                  setTimeout(function() {
                      $(".alert-danger").hide();
                  }, 10000);
              }
              else
              {
                  data[name]=value;
              }
              $('html','body').animate({scrollTop:0},500);
          }
      });


      //to get the proof details values 
      var loop_count=0;
      $('.proof_details_section').each(function(){
        $(this).find('input[type=text],input[type=file],input[type=hidden],select,textarea').each(function(){
          var name=$(this).attr('name');
          var value=$(this).val();
          if( $(this).hasClass('custom-file-upload') ){
            name = $(this).next().attr('name');
            value = $(this).next().val();
          }
          if(data[name]!=undefined){
            if(value){
              data[name][loop_count]=value;
            } else {
              if($(this).attr('disabled')==undefined || !$(this).hasClass('not_required')){
                validation_success=validate_fields(name,value);
              }
              else{
                validation_success=1;
                data[name][loop_count]=null;
              }
                
              if(!validation_success)
              {
                if($(this).hasClass('select-plugin'))
                  $(this).parent().find('.select2-container').addClass('has_error');
                else if($(this).hasClass('custom-file-upload'))
                  $(this).closest('.file-wrapper').addClass('has_error');
                else
                $(this).addClass('has_error');
                error_flag=1;
              }
              else
              {
                data[name][loop_count] = value;
              }
            }
        }
        });
        loop_count++;
      });
      //To check all the mandatory proof details are filled
      var missed_proof_types = proof_details_mandatory_fields.filter(e => !data['proof_type'].includes(e))
      var proofDetailError = false;
      data['file_reference_id'] = $('.proof-details-fields.custom-file-upload').map(function () { return $(this).attr('file-reference-id') ?? null }).toArray();
      if(missed_proof_types.length)
      {
          if(!['RF_03', 'RF_04', 'RF_07', 'RF_09', 'RF_10', 'RF_11', 'RF_12'].includes($('#request_for').val()))
          {
            error_flag=1;
            proofDetailError = true;
          }
      }
    $("#travel_section").find('input[type=text],select').each(function(){
      var name=$(this).attr('name');
      var value=$(this).val();
      // if (!$(this).attr('disabled')) {
      if(value){
        if(data[name]!=undefined){
          data[name]=value;
        }
      }
      else{
        validation_success=validate_fields(name,value, actionvalue.val());
        if(!validation_success)
        {
          // alert(3);
          if($(this).hasClass('select-plugin'))
            $(this).parent().find('.select2-container').addClass('has_error');
          else
          $(this).addClass('has_error');
          error_flag=1;
        }
      }
      // }
    });
    var loop_count=0;
    $('.travelling_details').each(function(){
      data['travelling_details_row_id'].push($(this).attr('tr_row_id'));
      $(this).find('input[type=text],select,input[type=hidden]').each(function(){
      var name=$(this).attr('name');
      var value=$(this).val();
      if(data[name]!=undefined){
        if(value){
        data[name][loop_count]=value;
      }
        else{
          if($(this).attr('disabled')==undefined){
            validation_success=validate_fields(name,value, actionvalue.val());
          }
          else
            validation_success=1;
          if(!validation_success)
          {
            
            if($(this).hasClass('select-plugin'))
              $(this).parent().find('.select2-container').addClass('has_error');
            else if($(this).prev().hasClass('custom-file-upload'))
              $(this).closest('.file-wrapper').addClass('has_error');
            else
            $(this).addClass('has_error');
            error_flag=1;
          }
        }
    }
    });
    loop_count++;
  });

  data['ticket_file_reference_id'] = $('.tr_details_container .custom-file-upload').map(function () { return $(this).attr('ticket_file_reference_id') ?? '' }).toArray();

    $("#request_section").find('input[type=text],input[type=checkbox],select,textarea').each(function(){
        
        var name=$(this).attr('name');
        var value=$(this).val();
        //alert(value);
        if(value){
          if(data[name]!=undefined){
            data[name]=value;
          }
        }
        else{
          validation_success=validate_fields(name,value, actionvalue.val());
          if(!validation_success)
          {
            if($(this).hasClass('select-plugin'))
              $(this).parent().find('.select2-container').addClass('has_error');
            else
            $(this).addClass('has_error');
            error_flag=1;
            error_count++;
              //$('.request_action_buttons').removeAttr('disabled');
          }
  
        }
      });
      var validate=[];
      var family_flag=0;
      var family_count_flag=0;
      $("#other_details").find('input[type=radio],input[type=number],input[type=text],input[type=hidden],input[type=checkbox],select,textarea').each(function(){
        var name=$(this).attr('name');
        var value=$(this).val();
        

         if (!$(this).attr('disabled')) {

          var radioValue = $('input[name='+name+']:checked').val();
        //console.log(radioValue);
          //alert(name);
        if($(this).attr('type')!='radio'){

        
        if(value){
         // alert(name);
      
          if(data[name]!=undefined){
            // if($.inArray(name,['child','adult']) ){
            //   family_count_flag=1;
            // }
            data[name]=value;
          }
        }
        else{
          validation_success=validate_fields(name,value, actionvalue.val());
          if(!validation_success)
          {
            if($(this).hasClass('select-plugin')){
             // alert('tesr');
              $(this).parent().find('.select2-container').addClass('has_error');
            }
        
            else{
              $(this).addClass('has_error');

            }
          
            error_flag=1;
            error_count++;
              //$('.request_action_buttons').removeAttr('disabled');
          }
  
        }
      }else{
        if(radioValue==0 || radioValue==1){
           //alert(radioValue);
          // alert('test');
           if(data[name]!=undefined){
            // if(name=='family_traveling' && radioValue==1){
            //   family_flag=1;
            // }
             data[name]=radioValue;
           }
           validate.push(name);
           $(this).closest('.radio_required').find('.alert_required').hide();
         }
         else if((value!=1)&&(jQuery.inArray(name, validate)==-1)){
           validation_success=validate_fields(name,radioValue, actionvalue.val());
           if(!validation_success)
           {
            
                // alert(test);
                //console.log($(this).closest('.ticket_required').find('.alert_required').attr('class'));
                $(this).closest('.radio_required').find('.alert_required').show();
                error_count++;
           }
         // alert(value);
   
         }

      }
      }
      });

      $('#billed_to_client').find('input[type="radio"],select,input,textarea').each(function(){
        var name=$(this).attr('name');
        var value=$(this).val();
        if($(this).hasClass('currency_format')){
          value=value.replace(/\,/g, '');
        }
        var validate=[];
        if($(this).attr('type')!='radio' && data['action'].includes('_reject')){
          if(value){
            if(data[name]!=undefined){
              data[name]=value;
            }
          }
          else{
            validation_success=validate_fields(name,value, actionvalue.val());
            if(!validation_success)
            {
              if($(this).hasClass('select-plugin')){
                $(this).parent().find('.select2-container').addClass('has_error');
              }
              else{
                $(this).addClass('has_error');
              }
              error_flag=1;
              error_count++;
            }
          }
        }
        else if($(this).attr('type')=='radio' && (data['action'].includes('_approve')||data['action'].includes('bf_review')||data['action'].includes('submit')||data['action'].includes('save'))){
        var radioValue = Number($('input[name='+name+']:checked').val());
        if(radioValue===0 || radioValue===1){
          if(data[name]!=undefined){
            data[name]=radioValue;
          }
          validate.push(name);
          $(this).closest('.radio_required').find('.alert_required').hide();
        }else if((value!=1)&&(jQuery.inArray(name, validate)==-1)){
          validation_success=validate_fields(name,radioValue, actionvalue.val());
          if(!validation_success)
          {
               $(this).closest('.radio_required').find('.alert_required').show();
               error_count++;
          }
        }
      }
      else{
        if(value){
          if(data[name]!=undefined){
            data[name]=value;
          }
        }
        else{
          validation_success=validate_fields(name,value, actionvalue.val());
          if(!validation_success)
          {
            if($(this).hasClass('select-plugin')){
              $(this).parent().find('.select2-container').addClass('has_error');
            }
            else{
              $(this).addClass('has_error');
            }
            error_flag=1;
            error_count++;
          }
        }

      } 

    });
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
                  error_flag = 1;
              }
              data['anticipated_row_id'][travel_desk_row_count] = row_no;
              data['excluded_row'][travel_desk_row_count] = excluded_row;
              data[name][travel_desk_row_count] = value;
          });
          travel_desk_row_count++;
        }
        
      });
      var isMultipleRoute=true;
      if ($.inArray($('#travel_type').find(":selected").val(),['TRV_02_03','TRV_01_03','TRV_03_03'])>=0 && $('#tr_details_row tr').length<=1) {
        isMultipleRoute=false;
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
       });
       console.log(data);
       //return false;
       if(error_count==0 && !error_flag && isMultipleRoute){//error_count==0 !error_flag
        $('.request_action_buttons').attr('disabled',true);
        if(!['save','save_ticket_process','save_forex_process','save_visa_process'].includes(data['action'])){
          var options = customizeConfirmationBoxContent(data['action'], saveDetails, data);
          openConfirmationBox(options);
        }
        else
          saveDetails(data);
    }
    else if (!isMultipleRoute) {
      $('.alert-danger').html('');
      $(".alert-danger").html('Please add the multiple routes' + '<span class="alert_close"><img title="Close alert" src="/img/close-red.svg" class=""></span>');
      $('.alert-danger').html(data['error']).show();
      $(window).scrollTop(0);
      setTimeout(function() {
          $(".alert-danger").hide()
      }, 5000);
      $('.ui-wait').css('display', 'none');
  }
    else{
      $('.alert-danger').html('');
      $(".alert-danger").html('Please fill all the mandatory fields'+'<span class="alert_close"><img title="Close alert" src="/img/close-red.svg" class=""></span>');
      if(proofDetailError)
        $(".alert-danger").html('Please add all the mandatory proof details for further process'+'<span class="alert_close"><img title="Close alert" src="/img/close-red.svg" class=""></span>');
      $('.alert-danger').html(data['error']).show();
      $(window).scrollTop(0);
      setTimeout(function() {
        $(".alert-danger").hide()
      }, 5000);
      $('.ui-wait').css('display','none');
    } 
});



$(document).on('click','#forex_approval_save,#forex_process_btn,#update_forex_process_btn',function(){
   // alert("Working");
    var data={};
    var error_flag = 0;
    var actionvalue = $(this);
    data['action']=$(this).val();
    data['common_action_comments']=$('#forex_process .common_action_comments').val() ? $('#forex_process .common_action_comments').val() : null;
    data['forex_details_row_id']=[];
    data['transaction_date']=[];
    data['mode_code']=[];
    data['currency_code']=[];
    data['amount']=[];
    data['comments']=[];
    data['transaction_type']=[];
    data['edit_id'] = $("#edit_id").val();
    var loop_count=0;
    var needToValidate = [];
    $('.forex_list').each(function(){
      data['forex_details_row_id'].push($(this).attr('forex_row_id'));
      $(this).find('input[type=text],input[type=hidden],select,textarea').each(function(){
      var name=$(this).attr('name');
      var value=$(this).val();
      if($(this).hasClass('required_field'))
        needToValidate.push(name);
       if(data[name]!=undefined){
        if(value){
            data[name][loop_count]=value;
        }
        else{
            validation_success=validate_fields(name,value,actionvalue.val(),needToValidate);
          if(!validation_success)
          {
            if($(this).hasClass('select-plugin'))
              $(this).parent().find('.select2-container').addClass('has_error');
            else
            $(this).addClass('has_error');
            error_flag=1;
            $('.alert-danger').show().find('.message').html('Please fill the mandatory fields');
            setTimeout(() => $('.alert-danger').hide(), 10000);
          }
          else
          {
            data[name][loop_count]=value;
          }
        }
    }
    needToValidate = [];
    });
    loop_count++;
});
if(!error_flag){
    if(!['save_forex_process'].includes(data['action'])){
      $('.request_action_buttons').attr('disabled', true);
      var options = customizeConfirmationBoxContent(data['action'], processForex, data);
      openConfirmationBox(options);
    }
    else{
      $('.request_action_buttons').attr('disabled', true);
      processForex(data);
    }
}

});

function validate_fields(name,value, action, needToValidate = null){
  if(needToValidate == null){
    needToValidate = ['currency_code'];
  }
  var exceptionalList = ['remarks','transaction_date', 'mode_code', 'currency_code', 'amount', 'comments'];
  if(needToValidate.length)
    exceptionalList = exceptionalList.filter((e) => !needToValidate.includes(e));
  if(action && action.includes("_reject")){
      if ( !["comments"].includes(name))  return 1;
      else exceptionalList = exceptionalList.filter( (e) => !["comments"].includes(e) );
  }
  if($(`[name = ${name}]`).hasClass('not_required_field'))
    return 1;
  if(!need_to_validate && $(`[name = ${name}]`).hasClass('not_required_fields'))
    return 1;
  if($.inArray(name, exceptionalList)<0){
    switch(name) {
      case 'adult':
        if($('input[name=child]').val()){
          return 0;
          break;

        }       
          return 1;
      break;
  
     case 'practice_unit_code':
      if($('#delivery_unit option').length==1){
        return 1;
      }
      return 0;
      break;
      case 'prefered_accommodation':
        if ($('#preferred_address').css('display') === 'none') {
          return 1;
      }
        return 0;
        break;
      
      case 'currency_code':
          if ($('#currency').css('display') === 'none') {
            return 1;
        }
          return 0;
          break;

 
      default: 
        if(!value)
        return 0;
        break;
    }
  }
 
    return 1;
  }

// To save the details
function saveDetails(data)
{
  handleRefresh(false);
  $.ajax({
    type:'POST',
    data:data,
    url:'/travel_request_actions',          
    dataType:'Json',
    async:false,
    success:function(data,textStatus, jqXHR){ 
      // $('.request_action_buttons').removeAttr('disabled');
      if(data.error){
        $('.alert-danger').html('');
        $(".alert-danger").html(data.message_text);
        $(".alert-danger").show();
        $('.request_action_buttons').removeAttr('disabled');
      }
      else{
        $(".alert-danger").hide();
        localStorage.clear();
        localStorage.setItem('message', data.next_action_details.message_text);
        if(data.next_action_details.mail){
          sendMail(data.next_action_details.mail, data.request_id, data.action);
        }
        setTimeout(function() {
          window.open(data.next_action_details.redirect_url,'_self');
        }, 500);
      }
    },
    error: function(jqXHR, textStatus, errorThrown){
      $('.request_action_buttons').removeAttr('disabled');
      event.preventDefault();
    }
  });

}

// To cutomize the confirmation box
function customizeConfirmationBoxContent(action, callback, ...params)
{
    var approvalRelatedActions = ['bu_head_approve','cp_approve','du_head_approve','du_head_hie_approve','fin_approve','geo_head_approve','project_owner_approve','project_owner_hie_approve'];
    var rejectRelatedActions = ['bu_head_reject','cp_reject','du_head_hie_reject','du_head_reject','fin_reject','geo_head_reject','project_owner_hie_reject','project_owner_reject',"bf_reject"];
    var reviewRelatedActions = ['desk_review_fac','desk_review_fin','desk_review_visa','bf_review'];
    var processRelatedActions = ['ticket_process','visa_process'];
    var forexProcessRelatedActions = ['forex_process','update_forex_process'];

    if(action == 'submit'){
      return {
        content: 'Are you sure want to submit?',
        primaryBtn: 'Submit',
        secondaryBtn: 'Cancel',
        primaryAction: callback,
        primaryActionParams: params,
        secondaryAction: enableActionButtons,
      }
    }
    else if(reviewRelatedActions.includes(action)){
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
    }else if(forexProcessRelatedActions.includes(action)){
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

// To process the forex
function processForex(data)
{
  handleRefresh(false);
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.ajax({
      type:'POST',
      data:{data:data,},
      dataType: 'json',
      url:'/forex_actions',      
      async: true,    
      success:function(result){
        //window.open(result.redirect_url, '_self');
	      window.open('/workbench', '_self');
      },
      error:function(error)
      {
        alert("Error occured");
        console.log(error);
      }
  });

}

// To enable the request action button on cancel action
function enableActionButtons()
{
  $('.request_action_buttons').removeAttr('disabled');
}

// Cancel the travel
$(document).on('click', '#cancelTravelBtn ', function () {
  let data = {
    'edit_id': $('#edit_id').val(),
    'reason_for_cancel': $('#reason_for_cancel').val(),
    'action': $(this).val(),
  }
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.ajax({
    url: '/cancel_travel',
    method: 'POST',
    data: data,
    dataType: 'JSON',
    async: true,
    success: function (response) {
      if(response.error) {
        let alertBox = $('.alert-danger');
        alertBox.show().html(response.error);
        setTimeout( () => alertBox.hide(), 10000 );
      } else {
        if(response.mail) {
          sendMail(response.mail, response.request_id, response.action);
        }
        setTimeout(function() {
          window.open( $('#cancel_travel_btn').attr('redirectURL'), '_self' );
        }, 500);
      }
    },
    error: function () {
      alert("Error occured");
    }
  })
});

$(document).on('input', '#reason_for_cancel', function () {
  if ($(this).val()) {
    $('#cancelTravelBtn').removeAttr('disabled');
  } else {
    $('#cancelTravelBtn').attr('disabled', true);
  }
});

function sendMail(mail, request_id, action)
{
  return;
  $.ajax({
    type:'POST',
    url:'/send_mails',
    dataType:'json',
    data: {mail_name: mail, request_id: request_id, action: action},
    success:function(data1){
      return true;
    },
    error:function(error){
      $('.request_action_buttons').removeAttr('disabled');
      //show_alert_notification('An error has been occured while triggering mails.Please contact help.mis@aspiresys.com for assistance.');
    }
  });
}
