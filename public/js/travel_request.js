var hidden_to_city = [];
var proof_details_visible_fields = {};
var proof_details_mandatory_fields = [];
var need_to_validate = true;
const maxRowCountForProofDetails = 9;
var originChanged = false;
var visa_not_required_countries={};
var visa_required=1;

//used to enter only number in the amount field for all the amount fields
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

function toggleProofDetailsFooter(){
    const proofTypeOptionsCount = $('#proof-details-table tbody tr:first .proof-details-fields[name="proof_type"]').children().length-1;
    const rf05RowCount = $('#proof-details-table tbody tr').filter(function() {
        return $(this).find("[name='proof_request_for']").val() === 'RF_05';
    }).length;
    if (proofTypeOptionsCount === rf05RowCount) {
        $('#proof-details-table tfoot').hide();
    } else {
        $('#proof-details-table tfoot').show();
    }
}
$(document).ready(function(){
    if ( $(".file-view-link").length ) {
        const sizeText = $(".file-view-link").closest('.file-wrapper').find('.file-upload-container span.file-size').each(function () {
            $(this).text( getSize( parseInt($(this).text()) ) );
        })
    }
    if(!$('#edit_id').val()){
    if($('.travel_request').val()=="MOD_02"){


        $('.origin_city').css('display','block');
        $('#origin_city').removeClass('not_required_field');
    }else{
        $('.origin_city').css('display','none');
        $('#origin_city').addClass('not_required_field');
        
    

        
    }
    }
    // Showing select box in case user has multiple visa for the coutry
    $('.multiple_visa').each(function () {
        let visa_number_alt_field = $(this); let visa_number_field = $(this).closest('tr').find('[name="visa_number"]');
        visa_number_alt_field.show(); visa_number_field.hide();
    });
    if($('.travel_request').val()=="MOD_01"){
        if($('.travel_request').val()=="MOD_02"){


            $('.origin_city').css('display','block');
            $('#origin_city').removeClass('not_required_field');
        }else{
            $('.origin_city').css('display','none');
            $('#origin_city').addClass('not_required_field');
            
        
    
            
        }

    }

    var row_count = $('#tr_details_row tr').length;
    if (row_count <= 1) {
        $(this).css({ 'opacity': '0.5', 'cursor': 'not-allowed' });
    }
    $(document).ajaxStart(function () {
        $(".ui-wait").show();
      });
      $(document).ajaxStop(function () {
        $(".ui-wait").hide();       
    });
    $('#action_prevent_error').html('');
    //TO show or hide the Billed to client div based on the content inside
    if($("#billed_to_client").find('.col-md-3').length){
        $('#bill_to_client.form-section').show();
    }
    else{
        $('#bill_to_client.form-section').hide();
    }
    
    //to show or hide the comments based on user permissions
    var is_action_required=$('.request-action-btn-grp .primary-button').length;
    if(is_action_required)
        $('#bill_to_client').show();
    else
        $('#bill_to_client').hide();

    if($('#delivery_unit').children().length == 2 || $('#department').children().length == 2)
        $('#delivery_unit, #department').prop('disabled', true);

    handleTicketRequired1();
    handleAccommodation1();
    handleAccommodation2();
    handleForex1();
    handleForex2();

    if($('#edit_id').val()){
        // to hide the proof details action for self request
        var requestFor = getRequestType($('#request_for').val())
        let rowCount = $('#proof-details-table tbody').children().length;
        toggleBehalfOfSection(requestFor);
        if(requestFor == "self" && !isOnsiteUser($('#origin').val()))
        {
            $(document).find('#proof-details-table tfoot').hide();
            $(document).find('.proof-action-btn-grp').hide();
        }
        else if(requestFor = "family" &&  rowCount >= maxRowCountForProofDetails)
        {
            $(document).find('#proof-details-table tfoot').hide();
        }
        else
        {
            var requestFor = getRequestType($('#request_for').val());
            let selectedOptions =  $("#proof-details-table tbody tr .proof-details-fields[name='proof_type']").map(function () { return $(this).val() }).toArray();
            let selectOptions = $("#proof-details-table tbody tr:last .proof-details-fields[name='proof_type']").children().map(function () { return $(this).val()  }).toArray();
            selectedOptions = selectOptions.filter((e) => !selectedOptions.includes(e) && e!="");
            if(selectedOptions.length || requestFor == "family")
                $(document).find('#proof-details-table tfoot').show();
            else
                $(document).find('#proof-details-table tfoot').hide();
            $(document).find('.proof-action-btn-grp').show();
            let otherRequestTypes = ["customer", "new joinee", "on behalf"];
            if(otherRequestTypes.includes(requestFor)){
                need_to_validate = false;
                $('#proof-details-table').find('.proof-details-fields').each(function () { $(this).addClass('not_required_fields') })
            }    
        }
        $('#proof-details-table').find("[name='proof_request_for']").each(function () {
            var requestFor = getRequestType($('#request_for').val())
            let proofRequestFor = getRequestType($(this).val());
            if(proofRequestFor == "self" && requestFor != "on behalf" && !isOnsiteUser($('#origin').val())){
                $(this).closest('tr').find('.proof-action-btn-grp').hide();
                // $(this).closest('td').find('.proof-details-fields').each( function () { $(this).attr('disabled',true) } )
                $(this).closest('tr').find('.proof-details-fields').each( function () { if( !['proof_name'].includes($(this).attr('name')) )  $(this).attr('disabled', true) })
            }
            if(!$(this).prop('disabled'))
            {
                $(this).children().each(function () {
                    let proofRequestFor = getRequestType($(this).val());
                    let requestFor = getRequestType($('#request_for').val());
                    if(requestFor == "on behalf"){
                        if(proofRequestFor == "self")
                            $(this).removeAttr('disabled');
                        else    
                            $(this).attr('disabled',true);
                    } else if(requestFor == "family") {
                        if( getRequestType($(this).parent().val()) == 'self' ) {
                            if( $(this).val() != $(this).parent().val() &&  $(this).val() != $('#request_for').val())
                                $(this).attr('disabled', true);

                        } else if($(this).val() != $('#request_for').val())
                            $(this).attr('disabled',true);
                    } else if($(this).val() != $('#request_for').val())
                        $(this).attr('disabled',true);
                    else
                        $(this).removeAttr('disabled');
                });
            }
        });
        $('#proof-details-table').find(".not_required").each(function () {
            $(this).attr('disabled',true);
        });
        if(['customer', 'new joinee', 'on behalf'].includes(requestFor)){
            need_to_validate = false;
            $('#proof-details-table').find('.proof-details-fields').each(function () { $(this).addClass('not_required_fields') })
        }
        fetchProofDetails($('#request_for').val(), "MANDATORY_FIELDS_ONLY")
        $("[name='proof_type']").each(function () { if($(this).val()) hideProofDetailsFields($(this), false); })
        // if(((".request_for").val()!='RF_05') || ((".request_for").val()!='RF_01')){
        //     $('.country').removeAttr("disabled");

        // }
        
            if($.inArray($('.request_for').find(":selected").val(),['RF_05','RF_01', 'RF_08'])<0){
        $('.country').removeAttr("disabled");
      }else{
        var default_country=$('#default_country').val();
        $('.country').val(default_country).change();
        $('.country').attr("disabled",true);
      }
        $('.to_country select option[value="' + $("#origin").val() + '"]').attr('disabled', true);
        dateFieldChangeBasedONTraveltype();
        travelDates();
        // $('.select-plugin').select2();
        // var hidden_from_date = [];var hidden_to_date = [];
        // $('input[name=hidden_to_city]').each(function(){
        //     hidden_to_city.push($(this).val());
        // }) 
        // $('input[name=hidden_from_date]').each(function(){
        //     hidden_from_date.push($(this).val());
        // }) 
        // $('input[name=hidden_to_date]').each(function(){
        //     hidden_to_date.push($(this).val());
        // }) 
        // $('input[name="from_date"]').each(function(k,v){
        //     $(this).datepicker("setDate",hidden_from_date[k]);
        // })
        // $('input[name="to_date"]').each(function(k,v){
        //     $(this).datepicker("setDate",hidden_to_date[k]);
        // })

       
           // $(document).find('.to_country select').trigger('change');
        // $(document).find('select[name=to_city]').each(function(k,v){
        //     // $("#customer_name option[value='"+key+"']").attr('selected',true);
        //     console.log($(this));
        //     console.log($(this).find("option"));
        //     // $(this).find("option[value='"+hidden_to_city[k]+"']").attr('selected',true);
        //     $(this).val(hidden_to_city[k]).change();
        //     // $(this).trigger('change');
        // })
        // To trigger the on behalf request details
        if($('#on_behalf').val() && ['RF_10','RF_11','RF_12'].includes($('#request_for').val()))
            loadDetailsForBehalfOfRequest($('#on_behalf').val(), false);
        // To display not-allowed effect for upload icon ( in case of already uploaded )
        $('.custom-file-upload').each(function () {
            var fileElement = $(this);
            var wrapper = fileElement.closest('.file-wrapper');
            var container = wrapper.find('.file-upload-container');
            if(container.length > 0){
                wrapper.find('.upload-icon').addClass('uploaded');
            }
        });
        // To upload file (saved request)
        $(document).find('.custom-file-upload').each(function () {
            $(this).removeAttr('disabled');
        });
    }

  //tr  console.log('test1');
    // if($.inArray($('.request_for').find(":selected").val(),['RF_05','RF_01'])){
 
    //     $('.country').removeAttr("disabled");
        
    
    //   }else{
    //     var default_country=$('#default_country').val();
    //     console.log(default_country);
    //     $('.country').val(default_country).change();
    //     $('.country').attr("disabled",true);
    //   }
    if($(document).find('#ticket_required_1').is(':checked')){
        // $(document).find('input[name="family_traveling"]').removeAttr("disabled");
        $('input[name="family_traveling"]').removeAttr("disabled");
        $('input[name="accommodation_required"]').removeAttr("disabled");
        $('input[name="insurance_required"]').removeAttr("disabled");
    }

    if($(document).find('#ticket_required_2').is(':checked')){
        $('#preferred_address').val('');
        $('#preferred_address').prop("disabled", true);
        //$('input[name="family_traveling"]').removeAttr("disabled");
        $('input[name="family_traveling"]').closest('.radio_required').find('.alert_required').hide();
        $('input[name="accommodation_required"]').closest('.radio_required').find('.alert_required').hide();
        $('input[name="insurance_required"]').closest('.radio_required').find('.alert_required').hide();
        $("#accommodation").css("display", "none");
        $('input[name="family_traveling"]').prop("checked",false);
        $('input[name="accommodation_required"]').prop("checked",false);
        $('input[name="insurance_required"]').prop("checked",false);
        $('input[name="accommodation_required"]').attr("disabled",true);
        $('input[name="insurance_required"]').attr("disabled",true);
        $('input[name="family_traveling"]').attr("disabled",true);
        $('#no_child').attr("disabled",true);
        $('#no_child').css("display","none");
        $('#no_adult').attr("disabled",true);
        $('#no_adult').css("display","none");
    }
    if($(document).find('#accommodation_1').is(':checked')){
        //$('input[name="family_traveling"]').removeAttr("disabled");
        $('#preferred_address').prop("disabled", false);
    
        }
        if($(document).find('#accommodation_2').is(':checked')){
          //$('input[name="family_traveling"]').removeAttr("disabled");
          $('#preferred_address').val('');
          $('#preferred_address').prop("disabled", true);
      
          }
          if($(document).find('#forex_required_1').is(':checked')){
            $('#currency').removeAttr("disabled");
            //console.log('t');
           // alert('test');
            
            }
            if($(document).find('#forex_required_2').is(':checked')){
                //$('input[name="family_traveling"]').removeAttr("disabled");
                $('#currency').val('').change();
                //$('#currency').prop("checked",false);
                $('#currency').parent().find('.select2-container').removeClass("has_error");
                $('#currency').attr("disabled",true);
                }
                if($(document).find('#family_travel_1').is(':checked')){
                    $('#no_child').removeAttr("disabled");
                    $('#no_child').css("display","block");
                    $('#no_adult').removeAttr("disabled");
                    $('#no_adult').css("display","block");
                    
                    }
                    if($(document).find('#family_travel_2').is(':checked')){
                        $('#no_child').attr("disabled",true);
                        $('#no_child').css("display","none");
                        $('#no_adult').attr("disabled",true);
                        $('#no_adult').css("display","none");
                        }
    $('body').on('focus',".date", function(){
        $('#dob').datepicker({
            format: 'dd-M-yyyy',
            autoclose: true,
            endDate: new Date(),
        })
        $(".date.proof-details-fields[name='proof_issue_date']").datepicker({
            format:'dd-M-yyyy',
            autoclose: true,
            endDate: new Date(),
        })
        $(".date.proof-details-fields[name='proof_expiry_date']").datepicker({
            format:'dd-M-yyyy',
            autoclose: true,
            startDate: new Date(),
        })
        $(this).datepicker({
         format: 'dd-M-yyyy',
         autoclose: true
       });
       $('#dob').on('changeDate', function () {
            let dob = new Date($(this).val().replace("/-/g","/"));
            if(dob > new Date())
                $(this).val(null);
            $(this).trigger('blur');
       });
       $(".date.proof-details-fields[name='proof_issue_date']").on('changeDate', function () {
            let issuedDate = new Date($(this).val().replace(/-/g,"/"));
            if(issuedDate > new Date())
                $(this).val(null);
            $(this).trigger('blur');
       });
       $(".date.proof-details-fields[name='proof_expiry_date']").on('changeDate', function () {
            let expiryDate = new Date($(this).val().replace(/-/g,"/"));
            let today = new Date();
            today.setHours(0,0,0,0);
            if(expiryDate < today)
                $(this).val(null);
            $(this).trigger('blur');
        });
//         $('input[name="from_date"]').off('changeDate').on('changeDate', function () {
//             let selectedDate = $(this).val();
//             let from_date = new Date(selectedDate);
//             let today=new Date();
//             from_date.setHours(0,0,0,0);
//             today.setHours(0,0,0,0);
//             if(from_date < today){
//             $(this).val(null);
//             }
//             $(this).trigger('blur');
//             if($.inArray($('#travel_type').find(":selected").val(),['TRV_02_01','TRV_01_01', 'TRV_03_01'])<0){
//                 $('input[name="to_date"]').removeAttr('disabled');
//             }
//        });
//        $('input[name="to_date"]').off('changeDate').on('changeDate', function () {
//             let selectedDate = $(this).val();
//             let to_date = new Date(selectedDate);
//             let today=new Date();
//             to_date.setHours(0,0,0,0);
//             today.setHours(0,0,0,0);
//             if(to_date < today){
//             $(this).val(null);
//             }
//             $(this).trigger('blur');
//    });
      })
	$('.select-plugin').select2();
      //To enable/disable the DU field based on the department selection
    department_validation($(document).find('#department').val(),$(document).find('#project_name').val());
         $(".select-plugin").select2().on('select2:open', function(e){
             $('.select2-search__field').attr('placeholder', 'Search');
         });

         $('.travel_request,.request_for,.work_from').select2({
             minimumResultsForSearch: -1
         });
         var defau_req="MOD_02";
         if($('#travel_request_for').val()){
            defau_req=$('#travel_request_for').val();
         }
         if($('#edit_id').val()==""){
            //$('.ui-wait').show();
            $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
           });
           $.ajax({
               type:'POST',
               data:{module:defau_req, visa_request_id: $('#visa_request_id').val()},
               url:'/travel_request', 
               success:function(data){
                   $('.travel_common').html(data);
                   $('.ui-wait').hide();
                   travelDates();
                   $('.select-plugin').select2();
                   $('[data-toggle="tooltip"]').tooltip();
                    checkEntityMapping();
                    disableToCountry( $('#origin').val() );
                    if($('#project_name').val())
                        load_details();
                    
                    //the below code has been used to prevent trigger chage while creating travel request form visa
                    if(!$('#visa_request_id').val()){
                        $('#to_country').trigger('change');
                        
                    }else{
                        $('[name="visa_number"]').attr('disabled','disabled');
                        $('[name="visa_type_code"]').attr('disabled','disabled');
                    }
                    $('#travel_type').trigger('change');
                    if( $('#visa_request_id').val() ){
                        var requestFor = getRequestType($('#request_for').val())
                        let rowCount = $('#proof-details-table tbody').children().length;
                        if(requestFor == "self" && !isOnsiteUser($('#origin').val()))
                        {
                            $(document).find('#proof-details-table tfoot').hide();
                            $(document).find('.proof-action-btn-grp').hide();
                        }
                        else if(requestFor = "family" &&  rowCount >= maxRowCountForProofDetails)
                        {
                            $(document).find('#proof-details-table tfoot').hide();
                        }
                        $('#proof-details-table').find("[name='proof_request_for']").each(function () {
                            var requestFor = getRequestType($('#request_for').val())
                            let proofRequestFor = getRequestType($(this).val());
                            if(proofRequestFor == "self" && requestFor != "on behalf" && !isOnsiteUser($('#origin').val())){
                                $(this).closest('tr').find('.proof-action-btn-grp').hide();
                                // $(this).closest('td').find('.proof-details-fields').each( function () { $(this).attr('disabled',true) } )
                                $(this).closest('tr').find('.proof-details-fields').each( function () { if(!['proof_name'].includes($(this).attr('name'))) $(this).attr('disabled', true) })
                            }
                            if(!$(this).prop('disabled'))
                            {
                                $(this).children().each(function () {
                                    let proofRequestFor = getRequestType($(this).val());
                                    let requestFor = getRequestType($('#request_for').val());
                                    if(requestFor == "on behalf"){
                                        if(proofRequestFor == "self")
                                            $(this).removeAttr('disabled');
                                        else    
                                            $(this).attr('disabled',true);
                                    }else if($(this).val() != $('#request_for').val())
                                        $(this).attr('disabled',true);
                                    else
                                        $(this).removeAttr('disabled');
                                });
                            }
                        });
                        fetchProofDetails($('#request_for').val(), "MANDATORY_FIELDS_ONLY")
                        $("[name='proof_type']").each(function () { if($(this).val()) hideProofDetailsFields($(this), false); })
                    }
                    toggleProofDetailsFooter();
                },
               error:function()
               {
                   $('.ui-wait').hide();
                   alert('Error occured..');
               }
           });

         }
    // if(($(document).find(".request_for").val()!='RF_05') || ($(document).find(".request_for").val()!='RF_01')){
    //   //  console.log('test');
   
    //    // $('.country').removeAttr("disabled");
    
    //   }else{
    //     var default_country=$('#default_country').val();
        
    //     $('.country').val(default_country).change();
    //     $('.country').attr("disabled",true);
    
    //   }
    visa_details($("#tr_details_row").find("tr"),$('#request_for').val())
    hideActionBasedTraveltype();
	$(document).on('change','#department',function(){
        var department=$(this).val();
        var project_name=$('#project_name').val();
        if(department){
            department_validation(department,project_name);
        }
        else{
            $('#delivery_unit').val('');
            $('#delivery_unit').select2().trigger('change');
        }
    });
    if( $('#visa_request_id').val() ){
        // fetchProofDetails( $('#request_for').val() );
        if( $('#request_for').val() == "RF_05" ) {
            $(document).find("#proof-details-table tbody tr .proof-details-fields").each(function () {
                $(this).attr('disabled', true);
            });
        }
    }


    

    edit_id=$('#edit_id').val();

    if(edit_id && $('#travel_request_for').val() == 'MOD_02'){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type:"GET",
            data:{edit_id:edit_id}, 
            url: "/visa_not_required",
            success: function(result){
                visa_not_required_countries=result.visa_not_required_countries;
                if(result.status){
                    visa_error();
                }
          },
          error:function()
               {
                   $('.ui-wait').hide();
                   alert('Error occured..');
               },
        
        });
    }
    

}); 


$(document).on('input','#no_adult', function () {
    var adult_dep='';
if ($('#no_child').val()!== '') {
    adult_dep=9-$('#no_child').val();
   // alert(child_dep);

}else{
    adult_dep=9
}

var value = this.value;
// alert(value);
if (value !== '') {
    value = parseFloat(value);
    
    if (value < 0)
        this.value = 0;
    else if (value > adult_dep)
        this.value = adult_dep;
}

});
$(document).on('input','#no_child', function () {
var child_dep='';
if ($('#no_adult').val()!== '') {
    child_dep=9-$('#no_adult').val();
   // alert(child_dep);

}else{
    child_dep=9;
}

var value = this.value;
// alert(value);
if (value !== '') {
    value = parseFloat(value);
    
    if (value < 0)
        this.value = 0;
    else if (value > child_dep)
        this.value = child_dep;
}

});
$(document).on('input', '#no_adult, #no_child', function () {
let value = $(this).val();
if(!/^[0-9]$/.test(value))
$(this).val( value.slice(0,-1) );
});


    $(document).on('click', '#add_btn', function () {
        var all_fields_filled=true;
        $('#tr_details_row tr:last').find('input[type=text],select').not('[name="visa_number_alt"]').each(function () { 
            if (!$(this).attr('disabled')) {          
            if (!$(this).prop('readonly') && $(this).val() == ''){          
                all_fields_filled = false;
                if($(this).hasClass('select-plugin'))
                    $(this).parent().find('.select2-container').addClass('has_error');
                else
                    $(this).addClass('has_error');
                }
                $(this).on('focus', function () {
                    $(this).removeClass('has_error');
                });  
            }      
        })
        if(all_fields_filled){
        addNewRowTravel();
        }
        else {
            $(".alert-danger").html('Please fill all the fields' + '<span class="alert_close"><img title="Close alert" src="/img/close-red.svg" class=""></span>').show();
            setTimeout(function () {
                $(".alert-danger").hide();
            }, 5000);
        }

    });
    $(document).on('click', '.tr_details_container #delete_btn', function () {
      var row_count=$('#tr_details_row tr').length;
      if(row_count>1){
          if(confirm('Are you sure you want to remove?')){
              $(this).closest('tr').remove();
          }
      }
      else{
        $(this).closest('tr').find('input[type=text],select').val(''); 
        $(this).closest('tr').find('input[name="from_date"]').datepicker('setDate', null);
        $(this).closest('tr').find('input[name="to_date"]').datepicker('setDate', null);
        $(this).closest('tr').find('input[name="from_date"]').datepicker('option', 'maxDate', null); 
        $(this).closest('tr').find('.select-plugin').select2();  
        dateFieldChangeBasedONTraveltype();
    }
      if($('#travel_request_for').val() != 'MOD_01' && $(this).closest('tr').find('.to_country select').val() != '' && $(this).closest('tr').find('input[name="visa_number"]').val() == "") {
        visa_error();
    }

});

$(document).on('change','#project_name',function(){ 
    load_details();
});

/*
    To load city based on the country selected
    Added by monisha.thirumalai
*/
$(document).on('change','.to_country select',function(){
    $('.ui-wait').show();
    var country_value = $(this).val();
    var current_row = $(this).closest('tr');
    var request_for = $('#request_for').val();
    $('#action_prevent_error').find('.visa_proof_error').remove();
    if($('#action_prevent_error .system-message.error').length === 0) {
        $('.request_action_buttons').removeAttr('disabled');
    }
    current_row.find('input[name="visa_number"]').val('').prop('readonly', false);
    var visa_type_select = current_row.find('.visa_type select');
    visa_type_select.removeAttr('disabled').find('option').removeAttr('selected');
    visa_type_select.val('').trigger('change');
    load_city_related_details(country_value,current_row,request_for);
    if ($('.to_country select').length <= 1) {
        $('.visa_error').hide();
        //$('.request_action_buttons').prop('disabled', false);
    }

});
$(document).on('change','#origin',function(){
    load_city_related_details($('#origin').val(),null,null,true);
});

function load_city_related_details(country_value,current_row,request_for,origin_city=false){
    $('.ui-wait').show();
    if(country_value){
        $.ajaxSetup({
        headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type:'post',
        url:'/load_city',
        dataType: 'JSON',
        data:{country:country_value},
        success:function(response){
            $('.ui-wait').hide();
if(!origin_city){
            city=response.city;
            visa_details=response.visa_details;
            visa_not_required_countries=response.visa_not_required_countries;
            visa_required=checkVisaRequired(country_value,$('#origin').val(),visa_not_required_countries);            
            if(visa_required){
                current_row.find('input[name="visa_number"]').addClass('not_required_field');
                current_row.find('input[name="visa_number_alt"]').addClass('not_required_field');
            }
            var current_select = current_row.find('.to_city select');
            var current_visa_number=current_row.find('input[name="visa_number"]')
            var current_visa_type=current_row.find('.visa_type select')

            current_select.find('option:not(:first-child)').remove();
            $.each(city, function(key, value) {
                current_select.append("<option value="+key+" >"+value+"</option>");;
            });
            // if (visa_details.length !== 0 && request_for == "RF_05") {
            //         $.each(visa_details, function(key, value) {
            //             current_visa_number.append(value).val(value);
            //             current_visa_type.find('option[value="' + key + '"]').prop('selected', true);
            //         });
            //         current_visa_type.prop('disabled', true);
            //         current_visa_number.prop('readonly', true);
            //         current_visa_type.select2().trigger('change');
            // }
            // else if(visa_details.length == 0 && request_for=="RF_05"){
            //     visa_error();
            // }
          }else{
                city=response.city;
                let originCity = $('#origin_city').val();
                $('#origin_city').find('option:not(:first-child)').remove();
                $.each(city, function(key, value) {
                    
                    $('#origin_city').append("<option value="+key+" >"+value+"</option>");;
                });
                $('#origin_city').val(originCity);
                

            }

            
        },
        error:function(error){
            $('.ui-wait').hide();
            console.log(error);
        }
    });
}
}

$(document).on('change','.travel_request', function(){
    if($('.travel_request').val()=="MOD_02"){
        load_city_related_details($('#origin').val(),null,null,true);
        $('.origin_city').css('display','block');
        $('#origin_city').removeClass('not_required_field');
 
    }else{
        $('.origin_city').css('display','none');
        $('#origin_city').addClass('not_required_field');


        
    }
    // To hide the system error message for international travel
    if(!['MOD_02'].includes( $(this).val() ) || true) {
        const systemMessage = ['.prev_proof_error', '.visa_proof_error', '.prev_blocked_user_error'];
        $(systemMessage.join(', ')).remove();
    }
     $('.ui-wait').show();
    var request_type=$(this).find(":selected").val();
    if(($("#default_travel_flag").val()=="1")&&request_type=='MOD_02'){
        $("#default_travel_flag").val(0);
    }else if(request_type!=""){
        $("#default_travel_flag").val(0);
    $('.ui-wait').show();
    var visa_request_id = $('#visa_request_id').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
        $.ajax({
            type:'POST',
            data:{module:request_type },
            url:'/travel_request',         
            success:function(data){
                
                $('.travel_common').html(data);
                checkEntityMapping();
                disableToCountry( $('#origin').val() );
                //$('#travel_type').val("").change();
                travelDates();
                request_for(request_type);
                load_from_city($("#origin").val());
                $('.select-plugin').select2();
                $('.ui-wait').hide();
                if($('#project_name').val())
                    load_details();
            },
            error:function()
            {
                $('.ui-wait').hide();
                alert('Error occured..');
            }
        });
}
$('.ui-wait').hide();
}) 
$(document).on('change','#travel_type',function(){
    $('input[name="from_date"]').val('');
    $('input[name="to_date"]').val('');
    var currentDate = null;
    var endDate = null;
    $('input[name="from_date"]').datepicker('setDate', currentDate);
    $('input[name="to_date"]').datepicker('setDate', null);
    $('input[name="from_date"]').datepicker('option', 'maxDate', endDate);
    $('input[name="from_date"]').prop('disabled', false);
    $('input[name="to_date"]').prop('disabled', true);
    dateFieldChangeBasedONTraveltype();
    hideActionBasedTraveltype();
});

$(document).on("change", '.from_city select',function () {
    $(this).closest('tr').find(".to_city select").val('');
    var fromCityLength = $(this).closest('tr').find(".from_city option").not('[value=""]').length;
    var toCityLength = $(this).closest('tr').find(".to_city option").not('[value=""]').length;
    if (fromCityLength == 1 && toCityLength == 1) {
    return;
    }
    var selectedFromCity = $(this).val();
    var current_row = $(this).closest('tr');
    current_row.find(".to_city option").prop("disabled", false);
    current_row.find(".to_city option[value='" + selectedFromCity + "']").prop("disabled", true);
    current_row.find(".to_city select").select2().trigger('change');
});


/**
 * Changes made by venkatesan.raj travel proof details
 */
//To load proof details when request for is changed
$(document).on('change','#request_for', function () {
    $("#tr_details_row").find('input[type=text],select').val('');
    $("#tr_details_row").find('.select-plugin').select2();
    $('input[name="visa_number"]').prop("readonly", false);
    $('input[name="visa_type"]').prop("disabled", false);
    $("#tr_details_row").find("tr:gt(0)").remove();
    $('.visa_error').html('').removeAttr('style').css('background-color', '');
    if($('#action_prevent_error .system-message.error').length === 0)
        $('.request_action_buttons').prop('disabled', false);
    if( $('.behalf_of_section').is(':visible') )
        loadDetailsForBehalfOfRequest();
    toggleBehalfOfSection(getRequestType($(this).val()));
    updateOrgin( $(this).val() );
    fetchProofDetails($(this).val());
    disableToCountry( $('#origin').val() );
    $(".ui-wait").hide();
    
});

function visa_details(current_row,request_for){
    if(request_for=="RF_05"){
        current_row.find('input[name="visa_number"]').attr('readonly',true);
        current_row.find('.visa_type select').attr('disabled',true);
    }else{
        current_row.find('input[name="visa_number"]').attr('readonly',false);
        current_row.find('.visa_type select').attr('disabled',false);
    }

}

//To clone a new proof detail row
$(document).on('click', '#add_proof_btn', function () {
    let flag = true;
    $('#proof-details-table').find('.proof-details-fields').each(function () {
        if(!$(this).hasClass('not_required') && !$(this).val() && !$(this).hasClass('custom-file-upload')){
            flag = false;
            $(this).addClass('has_error');
        }
        $(this).on('focus', () => $(this).removeClass('has_error'));
    })
    if(flag)
        cloneRow();
    else {
        $('.alert-danger').show().find('.message').html('Please fill the proof details');
        setTimeout( () => $('.alert-danger').hide(), 10000 );
    }
    $("#proof-details-table tbody tr:last .proof-details-fields[name='proof_request_for']").children().each(function () {
        let requestFor = getRequestType($('#request_for').val());
        if(requestFor == "on behalf"){
            if(getRequestType($(this).val()) == "self")
                $(this).removeAttr('disabled');
            else
                $(this).attr('disabled', true);
        } else if(isOnsiteUser( $('#origin').val() )) {
            if( !['self', 'family'].includes(getRequestType($(this).val())) ){
                if($(this).val() != $('#request_for').val()) $(this).attr('disabled', true);
            }
        }else if($(this).val() != $('#request_for').val()) {
            $(this).attr('disabled', true);
        }
    });
});
//To delete or update the proof details
$(document).on('click', '.proof-action-btn', function () {
    if($(this).hasClass('delete-btn'))
    {
        $(this).closest('tr').find('.upload-icon').removeClass('uploaded');
        $('#add_proof_btn').closest('tfoot').show();
        $(this).closest('tr').find('.proof-details-fields').each(function () {
            $(this).closest('.file-wrapper').removeClass('has_error');
            $(this).removeClass('has_error');
        });
        let rowCount = 0;
        $('#proof-details-table tbody tr').each(function () {
            let requestFor = getRequestType($('#request_for').val());
            let proofRequestFor = getRequestType($(this).find('.proof-details-fields[name="proof_request_for"]').val());
            if(requestFor == "family" && proofRequestFor == "self")
                rowCount--;
            rowCount++;
        });
        if(rowCount == 1)
            need_to_validate = false;
        if(rowCount > 1)
            $(this).closest('tr').remove();
        else
            $(this).closest('tr').find('.file-info .info').html('');
            $(this).closest('tr').find('.file-upload-container').empty().removeClass('show');
            $(this).closest('tr').find('.proof-details-fields').each(function () {
                $(this).val(null);
                if($(this).hasClass('custom-file-upload')) $(this).attr('file-reference-id', '');
            })
    }
    if($(this).hasClass('.update-btn'))
    {
        //code here
    }
});
//Fetch the proof details from DB
function fetchProofDetails(request_for, additional_params = null)
{
    var data = {
        request_for: request_for,
        module: $('#travel_request_for').val(),
        origin: $('#origin').val(),
    }
    if(additional_params)
        data['additional_params'] = additional_params;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '/list_respective_user_proof_details',
        method: 'post',
        data: data,
        async: false,
        success: (response) => {
            proof_details_visible_fields = response.visible_fields;
            if(response.mandatory_field_list)
                proof_details_mandatory_fields = Object.keys(response.mandatory_field_list ?? {})
            if(!additional_params)
                loadProofDetails(response,request_for);
            if (
                getRequestType(request_for) == "self" &&
                response.hasOwnProperty('mandatory_status') &&
                !response.mandatory_status
            ) {
                var fields = $(".proof-details-fields");
                removeMandatoryCheck({ element: fields });
            } else if (getRequestType(request_for) == "family" && response.hasOwnProperty('mandatory_status') && !response.mandatory_status) {
                var fields = $(".proof-details-fields");
                removeMandatoryCheck({
                    element: fields,
                    // callback: remomveMandatoryCheckForSelfRequestType,
                });
            }
            // To show the blocked user error message
            if(response.is_blocked_user){
                $('#action_prevent_error').append('<div class="system-message error prev_blocked_user_error">'
                +'<img src="/images/error.svg" alt="error">'
                +'<span>You have not submitted the reimbursement claim for your last travel and hence you are not allowed to raise a new international travel. Kindly raise the reimbursement request to proceed further.</span></div>');
                $(document).find('.request_action_buttons').attr('disabled', true);
            } else {
                $('#action_prevent_error').find('.prev_blocked_user_error').remove();
                if($('#action_prevent_error .system-message.error').length === 0)
                    $(document).find('.request_action_buttons').removeAttr('disabled');
            }
        },
        error: () => alert("Error occured")
    });
}





function travelDates() {
    var today = new Date();
    var fromDate = $('input[name="from_date"]');
    if(fromDate.hasClass('extendable_dates'))
    	today = new Date(fromDate.prop('defaultValue'));
    $('input[name="from_date"]').datepicker({
        startDate: today,
        format: 'd-M-yyyy',
        autoclose: true,
    }).on('changeDate', function(e) {
        var from_date = $(this).val();
        $('input[name="to_date"]').datepicker('setStartDate', from_date);
        if($.inArray($('#travel_type').find(":selected").val(),['TRV_02_01','TRV_01_01', 'TRV_03_01'])<0){
            $('input[name="to_date"]').removeAttr('disabled');
            if($('input[name="to_date"]').attr('traveling_type') != undefined){
                if($('input[name="to_date"]').attr('traveling_type') == "one-way")
                    $('input[name="to_date"]').attr('disabled', true);
            }
        }
    });
    $('input[name="from_date"]').on("keydown", function(event) {
        if (event.keyCode === 38 || event.keyCode === 40) {
            event.preventDefault();
        }
    });
  
    $('input[name="to_date"]').datepicker({
        startDate: today,
        format: 'd-M-yyyy',
        autoclose: true,
    }).on('changeDate', function(e) {
        var to_date = $(this).val();

        $('input[name="from_date"]').datepicker('setEndDate', to_date);
    });
}

function multipleRouteDates(prevToDate,container){
    var prevToDateObj = new Date(prevToDate);
    prevToDateObj.setDate(prevToDateObj.getDate()); 
    var from_date='';
    container.find('input[name="from_date"]').datepicker({
      startDate:prevToDateObj,
      format: 'd-M-yyyy',
      "autoclose": true,
      onSelect: function (selected) {
        var dt = new Date(selected.replace(/-/g, ''));
        from_date = $(this).val();
        container.find('input[name="to_date"]').datepicker('setStartDate', dt);
        // setTimeout(function(){
        //   $('input[name="to_date"]').datepicker( "setstartDate", dt );
        // },200);
      }
    }).on('changeDate',function(e){
          var dt = e.date;
          dt.setDate(dt.getDate());
          from_date=$(this).val();
          container.find('input[name="to_date"]').datepicker('setStartDate', from_date);
        //   setTimeout(function(){$('input[name="to_date"]').datepicker( 'setStartDate', from_date);},200);
        if($.inArray($('#travel_type').find(":selected").val(),['TRV_02_01','TRV_01_01', 'TRV_03_01'])<0){
            $('input[name="to_date"]').removeAttr('disabled');
            if($('input[name="to_date"]').attr('traveling_type') != undefined){
                if($('input[name="to_date"]').attr('traveling_type') == "one-way")
                    $('input[name="to_date"]').attr('disabled', true);
            }
         }
    })
    container.find('input[name="to_date"]').datepicker({
      startDate:from_date,
      format: 'd-M-yyyy',
      "autoclose": true,
      onSelect: function (selected) {
        var dt = new Date(selected.replace(/-/g, ''));
        from_date = $(this).val();
        container.find('input[name="from_date"]').datepicker('setstartDate', dt);
        // setTimeout(function(){
        //   $('input[name="from_date"]').datepicker( "setstartDate", dt );
        // },200);
      }
    }).on('changeDate',function(e){
      var dt = e.date
      container.find('input[name="from_date"]').datepicker('setEndDate', dt);
    //   setTimeout(function(){$('input[name="from_date"]').datepicker("setEndDate",dt);},200);
    })
  }
//To load the proof details in table
function loadProofDetails(response,request_for)
{
    let requestFor = getRequestType(request_for);
    var proof_details = response.proof_details;
    var visible_fields = response.visible_fields;
    if(!proof_details)
        return;
    clearProofDetails();
    switch (requestFor)
    {
        case "self":
            if(!$('#dob').val()) $('#dob').val(response['user_details']['dob']);
            $('#traveler_email').val(response['user_details']['email']);
            if(!$('#traveler_address').val()) $('#traveler_address').val(response['user_details']['address']);
            $('#phone_no').val(response['user_details']['phone_no']);
            $('#nationality').val(response['user_details']['nationality']);
            var is_found = response["mandatory_check"];
            var defaultProofDetailsFields = ['proof_number', 'proof_name', 'proof_issue_date', 'proof_expiry_date', 'proof_issued_place'];
            if(response.mandatory_status && Object.keys(is_found).length)
            {
                if($('.prev_proof_error').length<1)
                $('#action_prevent_error').append('<div class="system-message error prev_proof_error">'
                    +'<img src="/images/error.svg" alt="error">'
                    +'<span>You are unable to continue as proof details are not in IDM. Please connect with your HR partner to update the same in IDM</span></div>');
               // $('#proof_error').show().text("Proof details are not found in IDM. Please contact your HR partner for further process");
                $(document).find('.request_action_buttons').each(function () { $(this).attr('disabled',true); });
            }
            else
            {
                $('#action_prevent_error').find('.prev_proof_error').remove();
                if($('#action_prevent_error .system-message.error').length === 0)
                    $(document).find('.request_action_buttons').each(function () { $(this).removeAttr('disabled'); });
            }

            $("#proof-details-table tbody tr:not(:first-child)").remove();
            if (response.mandatory_status) {
            for(index=0; index < proof_details.length; index++)
            {
                if(index)
                    cloneRow();
                $(`#proof_type_${index+1}`).val(proof_details[index].proof_type).attr('disabled',true);
                $(`#proof_request_for_${index+1}`).val(request_for).attr('disabled',true);
                $(`#proof_number_${index+1}`).val(proof_details[index].proof_number).attr('disabled',true);
                $(`#proof_name_${index+1}`).val(proof_details[index].proof_name);//.attr('disabled',true);
                let issued_date = proof_details[index].proof_issue_date ? new Date(proof_details[index].proof_issue_date) : null;
                $(`#proof_issue_date_${index+1}`).datepicker({"format": "dd-M-yyyy"}).datepicker("setDate", issued_date).attr('disabled',true);
                let expiry_date = proof_details[index].proof_expiry_date ? new Date(proof_details[index].proof_expiry_date) : null;
                $(`#proof_expiry_date_${index+1}`).datepicker({"format": "dd-M-yyyy"}).datepicker("setDate",expiry_date).attr('disabled',true);
                $(`#proof_issued_place_${index+1}`).val(proof_details[index].proof_issued_place).attr('disabled',true);
                $(".proof-action-btn-grp").hide();
                $("#add_proof_btn").closest('tfoot').hide();
                // if($('#edit_id').val() == "" && Object.keys(proof_details[index].proof_file_details).length)
                if(Object.keys(proof_details[index].proof_file_details).length)
                    display(proof_details[index].proof_file_details, $(`#proof_file_${index+1}`));
                if(Object.keys(visible_fields).includes(proof_details[index].proof_type))
                {
                    let fieldsNeedToRemove = defaultProofDetailsFields.filter((e) => !visible_fields[proof_details[index].proof_type].includes(e));
                    for(fields of fieldsNeedToRemove)
                        $(`#${fields}_${index+1}`).val(null).addClass('not_required');
                }
            }
	} else {
                $("#proof-details-table tbody tr [name = 'proof_request_for']").children().each(function () {
                    if($(this).val() != $('#request_for').val())
                        $(this).attr('disabled',true);
                    else
                        $(this).removeAttr('disabled');
                })
            }
            break;

        case "family" :
                if($('#travel_request_for').val() == 'MOD_01')
                    relevent_request_for_self = 'RF_01';
                else
                    relevent_request_for_self = 'RF_05';
                if(response.mandatory_status){
                    loadProofDetails(response,relevent_request_for_self);
                    cloneRow(); 
                }
                $("#proof-details-table tbody tr:last [name = 'proof_request_for']").children().each(function () {
                    if(response.mandatory_status){
                        if($(this).val() != $('#request_for').val())
                            $(this).attr('disabled',true);
                        else
                            $(this).removeAttr('disabled');
                    } else {
                        let requestTypes = ["self", "family"];
                        let rowRequestType = getRequestType($(this).val());
                        if(!requestTypes.includes(rowRequestType))
                            $(this).attr('disabled',true);
                        else 
                            $(this).removeAttr('disabled');
                    }
                });
                $('#add_proof_btn').closest('tfoot').show();
                break;
        case "customer":
        case "new joinee":
            need_to_validate = false;
            $('#proof-details-table').find('.proof-details-fields').each(function () { $(this).addClass('not_required_fields') })
            $('#proof_error').text("").hide();
            $("#proof-details-table tbody tr [name = 'proof_request_for']").children().each(function () {
                if($(this).val() != $('#request_for').val())
                    $(this).attr('disabled',true);
                else
                    $(this).removeAttr('disabled');
            })
            $('#add_proof_btn').closest('tfoot').show();
            break;

        case "on behalf":
            need_to_validate = false;
            $('#proof-details-table').find('.proof-details-fields').each(function () { $(this).addClass('not_required_fields') })
            $("#proof-details-table tbody tr [name = 'proof_request_for']").children().each(function () {
                let proofRequestFor = getRequestType($(this).val());
                if(proofRequestFor != "self")
                    $(this).attr('disabled',true);
                else
                    $(this).removeAttr('disabled');
            })
            break;
    }
}
//To clone a proof details row
function cloneRow()
{
    let count = $('#proof-details-table tbody tr').length + 1;
    $('#proof-details-table tbody tr:last').clone().appendTo('#proof-details-table');
    $('#proof-details-table tbody tr:last .proof-details-fields').each(function () {
        if($(this).attr('type') != 'hidden')
        {
            let idAttr = $(this).attr('id').replace(/.$/,count);
            $(this).attr('id',idAttr);
        }
        $(this).val(null);
        $(this).removeAttr('disabled');
        $(this).closest('tr').find('.proof-action-btn-grp').show();
        $(this).removeClass('not_required');
    })
    $('#proof-details-table tbody tr:last .file-info .info').text("");
    $('#proof-details-table tbody tr:last .upload-icon').removeClass('uploaded');
    $('#proof-details-table tbody tr:last .file-wrapper .file-upload-container').remove();
    $('#proof-details-table tbody tr:last .custom-file-upload').attr('count', 0);
    $('#proof-details-table tbody tr:last .custom-file-upload').attr('file-reference-id', '');
    let selectedValues = [];
    $("#proof-details-table tbody tr .proof-details-fields[name='proof_type']").each(function () {
        selectedValues.push($(this).val());
    });
    $("#proof-details-table tbody tr:last .proof-details-fields[name='proof_type']").children().each(function () {
        let requestFor = getRequestType($('#request_for').val());
        if(requestFor == "family")
            return;
        if(selectedValues.includes($(this).val()))
            $(this).attr('disabled',true);
        else
            $(this).removeAttr('disabled');
    });
    let flag = 0;
    $("#proof-details-table tbody tr:last .proof-details-fields[name='proof_type']").children().each(function () {
        if($(this).val() && !$(this).prop('disabled'))
            flag++;
    });
    let rowCount = $('#proof-details-table tbody').children().length;
    if(flag <= 1 || rowCount >= maxRowCountForProofDetails)
        $('#add_proof_btn').closest('tfoot').hide();    
}
/*
    Function used to clone international traveling details
    Added by monisha.thirumalai
*/
function addNewRowTravel(){
	$('#tr_details_row .select-plugin').select2("destroy");
	var table_row_id = $('#tr_details_row tr:last').attr('id');
    var row_count = table_row_id.split('-');
    row_count = parseInt(row_count[1])+1;
	var new_row = $('#tr_details_row tr:last').clone();
	new_row.attr('id', 'travelling_details-' + row_count);
	new_row.attr('tr_row_id', '');
	new_row.find('input[type=text],select').each(function() {
		$(this).val('');
		this.id = this.id.replace(/-\d+$/, '-' + row_count);
    });
	$('#tr_details_row .select-plugin').select2('');
	new_row.find('.select-plugin').select2();
    new_row.find('.select-plugin').select2('enable');
    new_row.find('.to_country select').val('');
    if (new_row.find('.to_country').length > 0) {
        new_row.find('.to_city select option:not(:first-child)').remove();
    }
    var from_date = $('#tr_details_row').find('input[name="from_date"]').last().val();
    var to_date = $('#tr_details_row').find('input[name="to_date"]').last().val();

    from_date=new Date(from_date);
    to_date=new Date(to_date);
      if(from_date <= to_date){
       console.log("No error");
      }else{
        console.log('Error found within the same row for row ');
      }
    $('#tr_details_row').append(new_row);
    new_row.find(".to_city option").prop("disabled", false);
    
 
    
    var prevToDate = $('#travelling_details-' + (row_count - 1)).find('input[name="to_date"]').val();
    if(prevToDate){
    var container = $('#travelling_details-' + row_count);
        multipleRouteDates(prevToDate, container);
        container.find('input[name="to_date"]').prop('disabled', true);
    }
    else
    travelDates();
    //hide visa number select box
    new_row.find('[name="visa_number"]').show().removeAttr('disabled', false);
    new_row.find('[name="visa_number_alt"]').hide();
}


/** To close the alert boxes */
$(document).on('click','.alert .close-btn', function () {
    $(this).parent().hide();
});

// File upload related changes
$(document).on('click', '.upload-icon', function () {
//   if($(this).closest('.file-wrapper').find("[name='proof_file_path'],[name='ticket_file_path']").val())
//       return;
  $(this).addClass('uploaded');
  $(this).closest('.file-wrapper').find('.custom-file-upload').trigger('click');
});
$(document).on('input','.custom-file-upload', function () {
    if(!$(this).attr('count'))
        $(this).attr('count',0);
    if($(this).attr('name')=="ticket_upload")
        uploadFile($(this), {multiple: true});
    else
        uploadFile($(this));
  })
  
  $(document).on('click', '.file-remove', function () {
    var data = {filePath: $(this).closest('.row-item').find('.file-name a').attr('href') };
    if($('#edit_id').val() != '')
        data['edit_id'] = $('#edit_id').val();
    deleteFile(data, $(this));
    need_to_validate = true;
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
    }
  });
  function  request_for(module_name){
    $.ajaxSetup({
		headers: {
	   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
        type:'get',
		url:'/get_request_for',
		dataType: 'JSON',
		data:{module_name},
		success:function(response){
            $('#request_for').find('option').not(':first').remove();
            $.each(response['request_for'],function(key,value){
                if(value!="" && value!=null)
                $('#request_for').append("<option value="+key+" >"+value+"</option>");
                });
                $('#travel_type').find('option').not(':first').remove();
                $.each(response['travel_type'],function(key,value){
                    if(value!="" && value!=null)
                    $('#travel_type').append("<option value="+key+" >"+value+"</option>");
                    });
                $('#travel_purpose').find('option').not(':first').remove();
                $.each(response['travel_purpose'],function(key,value){
                        if(value!="" && value!=null)
                        $('#travel_purpose').append("<option value="+key+" >"+value+"</option>");
                });
		},
		error:function(error){
            $('.ui-wait').hide();
			console.log(error);
		}
    });

  }
$(document).on('change','select[name=master_category]',function(){
    var edit_id = $('#edit_id').val();
    var select_el = $(this);
    var name = $(this).attr('name');
    var dependent_field = $(this).attr('dependent_field');
    var value = $(this).val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type:'POST',
        data:{name:name,value:value,request_id:edit_id},
        url:'/load_related_select_options',          
        success:function(result){
            var selector = select_el.closest('tr').find("select[name="+dependent_field+"]");
            selector.each(function(){
                var current_element = $(this);
                current_element.select2("destroy");
                current_element.find('option:not(:first-child)').remove();
                $.each(result, function(key, value) {
                    current_element.append("<option value=" + key + ">" + value + "</option>");
                });
                current_element.select2();
                
            })
            console.log(result);
        },
        error:function()
        {
            alert('Error occured..');
        }
     });
});

function dateFieldChangeBasedONTraveltype(){
    var travel_type=$("#travel_type option:selected").text();
    if(travel_type=="One-way"){
        $('.add_img').addClass('disabled');
        $('.remove_img').addClass('disabled');
        $('input[name="to_date"]').prop('disabled', true);
        $("#tr_details_row").find("tr:gt(0)").remove(); 
    }else if(travel_type=="Return"){
        $('.add_img').addClass('disabled');
        $('.remove_img').addClass('disabled');
        $("#tr_details_row").find("tr:gt(0)").remove();
    }else if(travel_type=="Multiple Route"){
        $('.add_img').removeClass('disabled');
        $('.remove_img').removeClass('disabled');
    }
}

function load_details(){
    $('.ui-wait').show();
    var project=$('#project_name option:selected').val();
    var employee = $('#on_behalf').val() ?? null;

    $('#department').removeAttr('disabled');
    if(project){
        $("#department option:not(:first-child)").remove();
        $("#delivery_unit option:not(:first-child)").remove();
        $("#customer_name option:not(:first-child)").remove();    }
    $.ajaxSetup({
		headers: {
	   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		type:'post',
		url:'/load_project_details',
		dataType: 'JSON',
		data:{project:project, employee: employee},
		success:function(response){
            $('.ui-wait').hide();
            department=response.department;
            customers=response.customer;
            delivery=response.delivery_unit;
            selected_department= $('#department_hidden').val() || response.selected_department;
            
			$.each(department, function(key, value) {   
				$('#department').append($("<option></option>").attr("value",key).text(value));
                $("#department option[value='"+selected_department+"']").attr('selected',true);
                if(Object.keys(department).length==1){
                    $("#department option[value='"+key+"']").attr('selected',true);
                    $('#department').attr('disabled','disabled');
                    $('#department').parent().find('.select2-container').removeClass('has_error');
                }
		    });
            

            $.each(customers, function(key, value) {   
                $('#customer_name').append($("<option></option>").attr("value",key).text(value));
                if(Object.keys(customers).length==1){
                    $("#customer_name option[value='"+key+"']").attr('selected',true);
                    $('#customer_name').attr('disabled','disabled');
                    $('#customer_name').parent().find('.select2-container').removeClass('has_error');	
                } 
            }); 
            
            $.each(delivery, function (key, value) {
                $('#delivery_unit').append($("<option></option>").attr("value", key).text(value));                
            });
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
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });

    
}
function handleTicketRequired1() {
    if ($('#ticket_required_1').is(':checked')) {
        $("#family_travel").css("display", "block");
        $("#accommodation").css("display", "block");
        $("#insurance").css("display", "block");
        
        if ($('#family_travel_1').is(':checked')) {
            $('#no_child').removeAttr("disabled").css("display", "block");
            $('#no_adult').removeAttr("disabled").css("display", "block");
        }
        if ($('#family_travel_2').is(':checked')) {
            $('#no_child').attr("disabled", true).css("display", "none");
            $('#no_adult').attr("disabled", true).css("display", "none");
        }
    }
}
function handleAccommodation1() {
    if ($('#accommodation_1').is(':checked')) {
    $('#preferred_address_div').css("display", "block");
    }
}

function handleAccommodation2() {
    if ($('#accommodation_2').is(':checked')) {
    $('#preferred_address_div').css("display", "none");
    $('#preferred_address').val('');
    }
}

function handleForex1() {
    if ($('#forex_required_1').is(':checked')) {
        $('#currency_div').css("display", "block");
    }
}

function handleForex2() {
    if ($('#forex_required_2').is(':checked')) {
        $('#currency_div').css("display", "none");
        $('#currency').val('');
    }
}

$(document).on('change', '#ticket_required_1', function() {
    handleTicketRequired1();
});

$(document).on('change', '#family_travel_1', function() {
    handleTicketRequired1(); 
});

$(document).on('change', '#family_travel_2', function() {
    handleTicketRequired1();
});

$(document).on('change', '#ticket_required_2', function() {
    if ($('#ticket_required_2').is(':checked')) {
        $("#family_travel").css("display", "none");
        $("#accommodation").css("display", "none");
        $("#insurance").css("display", "none");
        $('#preferred_address_div').css("display", "none");
        $('#no_child').attr("disabled", true).css("display", "none");
        $('#no_adult').attr("disabled", true).css("display", "none");
    }
});

$(document).on('change', '#accommodation_1', function() {
    handleAccommodation1();
});

$(document).on('change', '#accommodation_2', function() {
    handleAccommodation2();
});

$(document).on('change', '#forex_required_1', function() {
    handleForex1();
});

$(document).on('change', '#forex_required_2', function() {
    handleForex2();
    
});

function visa_error(showError = false){
    var anyEmpty = false;
    $('#tr_details_row tr').each(function() {
        var visa_number = $(this).find('input[name="visa_number"]').val();
        var visa_number_alt = $(this).find('select[name="visa_number_alt"]').children().length;
        var visa_type = $(this).find('.visa_type select').val();
        let to_country= $(this).find("[name=to_country]").val();
        visa_required=checkVisaRequired(to_country,$('#origin').val(),visa_not_required_countries);
        if(visa_required){
            $(this).find('input[name="visa_number"]').removeClass('not_required_field');
            $(this).find('.visa_type select').removeClass('not_required_field');
            $(this).find('select[name="visa_number_alt"]').removeClass('not_required_field');
        }else{
            $(this).find('input[name="visa_number"]').val(null).trigger('change');
            $(this).find('input[name="visa_number"]').attr('disabled','disabled');
            $(this).find('input[name="visa_number"]').addClass('not_required_field');
            $(this).find('select[name="visa_number_alt"]').val(null).trigger('change');
            $(this).find('select[name="visa_number_alt"]').attr('disabled','disabled');
            $(this).find('select[name="visa_number_alt"]').addClass('not_required_field');
            $(this).find('.visa_type select').val(null).trigger('change');
            $(this).find('.visa_type select').attr('disabled','disabled');
            $(this).find('.visa_type select').addClass('not_required_field');
            showError=false;
        }
        if(visa_required){
            if (!(visa_number || visa_number_alt ) || !visa_type ) {
                anyEmpty = true;
                return false;
            }
        }
        
    });
    if (anyEmpty || showError) {
        if($('#action_prevent_error .visa_proof_error').length == 0)
        $('#action_prevent_error').append('<div class="system-message error visa_proof_error">'
                    +'<img src="/images/error.svg" alt="error">'
                    +'<span>Visa details not found for the respective country. Please go to visa tab or <a href="/visa_request">click here</a> and make a new request.</span></div>');
        // $('.visa_error').show().html("Visa details not found for the respective country. Please go to visa tab or <a href='/visa_request'>click here</a> and make a new request.");
        $('.request_action_buttons').prop('disabled', true);
    } else {
        $('#action_prevent_error').find('.visa_proof_error').remove();
        if($('#action_prevent_error .system-message.error').length === 0)
            $('.request_action_buttons').prop('disabled', false);
    }
}

//to close the alert boxes
$(document).on('click', '.alert_close', function () { $(this).closest('.alert').hide() });

// To prevent date enter manually
$(document).on('keydown', '.date',  function () { return false; });

// To restrict characters based on the input type
$(document).on('input', '#phone_no', function () {
    let value = $(this).val();
    let filteredValue = value.replace(/[^0-9-()+ ]+/g, '').substring(0, 16);
    $(this).val(filteredValue);
});
$(document).on('select2:select', '#origin', function (){
    $('.to_country select').val('');
    $('.to_country select option').attr('disabled', false);
    if($('#travel_request_for').val()=='MOD_01')
    load_from_city($(this).val());
    else if($('#travel_request_for').val()!='MOD_01'){
    if ($(this).val()) {
        $('.to_country select option[value="' + $(this).val() + '"]').attr('disabled', true);
    }
    $('.to_country select').select2();
    // fetchProofDetails($('#request_for').val());
}

});


function hideActionBasedTraveltype(){
    var travelType=$("#travel_type option:selected").text();
        if (travelType === 'Multiple Route') {
            $('#action-th').show();
            $('.action-cell').show();
            
        } else {
            $('#action-th').hide();
            $('.action-cell').hide();
        }
}

function load_from_city(origin_value){
    $('.ui-wait').show();
    setTimeout(function(){
    if(origin_value && $('#travel_request_for').val()=='MOD_01' && $('#travel_request_for').val()!=''){
        $.ajaxSetup({
            headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url:'/load_from_city',
            dataType: 'JSON',
            data:{origin_value},
            success:function(response){
                from_city=response.from_city;
                $('.from_city select').find('option:not(:first-child)').remove();
                $('.to_city select').find('option:not(:first-child)').remove();
            $.each(from_city, function(key, value) {
                $('.from_city select').append("<option value="+key+" >"+value+"</option>");;
                $('.to_city select').append("<option value="+key+" >"+value+"</option>");;
            });
            originChanged = true;
            },
            error:function(error){
                $('.ui-wait').hide();
                console.log(error);
            }
        });
    }
},500);
$('.ui-wait').hide();
}
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

$(document).on('change', '.proof-details-fields', function () {
    var row = $(this).closest("tr");
    need_to_validate = hasAnyValue(row);
});

$(document).on('change','input[name="billed_to_client"]',function(){
    $(".ui-wait").show();
    var billed_value = $(this).val();
    setTimeout(function() {
        show_budget_error(billed_value);
        $(".ui-wait").hide();
    }, 100);
})
$(document).ready(function(){
    if ($('#bill_to_client_2').is(':checked')) {
        var value = $('#bill_to_client_2').val();  
        show_budget_error(value);
    }
});

$(document).on('change', 'input[name="approver_anticipated_amount"],select[name="approver_currency_code"],select[name="currency_code"]', function () {
    currency_type=$(this).find('option:selected').text();
    currency_format_changes_for_same_row(currency_type,'approver_anticipated_amount');
  });

function show_budget_error(billed_value){
    $('#anticipated_cost_body tr').each(function(index) {
        var errorImage = $(this).find('.cost-row-error');
        var errorMessage = errorImage.attr('data-original-title');
        if (billed_value == 0 && errorMessage) {
            $(this).closest('tr').addClass('row-error');
            errorImage.show();
           $('.budget_error').show();
        } else if(billed_value == 1){
            $(this).closest('tr').removeClass('row-error');
            errorImage.hide();
            $('.budget_error').hide();
    }
});
}
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
// To find the request types
function getRequestType(value)
{
    let selfRequestTypes = ['RF_01', 'RF_05', 'RF_08'];
    let familyRequestTypes = ['RF_02', 'RF_06'];
    let customerRequestTypes = ['RF_03','RF_07'];
    let newJoineeRequestTypes = ['RF_04', 'RF_09'];
    let onBehalfOfRequestTypes = ['RF_10', 'RF_11', 'RF_12'];
    if(selfRequestTypes.includes(value))
        return "self";
    else if(familyRequestTypes.includes(value))
        return "family";
    else if(customerRequestTypes.includes(value))
        return "customer";
    else if(newJoineeRequestTypes.includes(value))
        return "new joinee";
    else if(onBehalfOfRequestTypes.includes(value))
        return "on behalf";
    else
        return false;
}

// To toggle the visibility of on behalf of section
function  toggleBehalfOfSection(requestFor)
{
    if(requestFor == "on behalf"){
        $('.behalf_of_section').show();
        $('#on_behalf').addClass('not_required_field');
    }else{
        $('.behalf_of_section').hide();
        $('#on_behalf').addClass('not_required_field');
        $('#on_behalf').val(null).trigger('change');
    }
}
// To clear the proof details table
function clearProofDetails()
{
    $('#traveler_address').val(null);
    $('#traveler_email').val(null);
    $('#phone_no').val(null);
    $('#dob').val(null);
    $('#proof-details-table tbody tr:not(:first)').remove();
    $('.proof-details-fields').val(null).removeAttr('disabled');
    $('.proof-details-fields').removeClass("not_required not_required_field not_required_fields");
    $('#proof-details-table tbody tr .file-wrapper .file-info .info').empty();
    $('#proof-details-table tbody tr:first .file-wrapper .file-upload-container').empty().removeClass('show');
    $('.proof-action-btn-grp').show();
    $('#add_proof_btn').closest('tfoot').show();
    $("#proof-details-table tbody tr:first .proof-details-fields[name='proof_type']").children().each(function () {$(this).removeAttr('disabled')});
    $("#proof-details-table tbody tr:first .proof-details-fields[name='proof_request_for']").children().each(function () { $(this).removeAttr('disabled')});
    $('#action_prevent_error').find('.prev_proof_error').remove();
    $('.upload-icon').removeClass('uploaded');
    $(".proof-details-fields.custom-file-upload").attr("count",0);
}

// To load for behalf of user
function loadDetailsForBehalfOfRequest(aceid = null,isChanged = true)
{
    $.ajaxSetup({
        'headers' : {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '/fetch_user_details_on_behalf',
        method: 'post',
        dataType: 'json',
        data: {'aceid' : aceid},
        async: false,
        success: function (response) {
            let prevProjectValue = $('#project_name').val();
            let prevUnitValue = $('#delivery_unit').val();
            if(response.origin){
                $('#default_country').val(response.origin);
                $('#origin').val(response.origin).attr('disabled', true).trigger('change');
            }
            if(response.project_list && Object.keys(response.project_list).length){
                var projectDropdown = $('#project_name');
                projectDropdown.find('option:not(:first)').remove();
                var projectList = response.project_list;
                for(projectValue in projectList){
                    var projectName = projectList[projectValue];
                    projectDropdown.append(`<option value='${projectValue}'>${projectName}</option>`);
                }
            }
            if(isChanged)
                $('#project_name').val(response.default_project).trigger('change');
            else {
                $('#project_name').val(prevProjectValue).trigger('change');
                $('#delivery_unit').val(prevUnitValue).trigger('change');
            }
            if( isChanged && !response.default_project ) {
                $('#department').val(null);
                $('#department,#customer_name,#delivery_unit').children().each(function () {
                    if($(this).val())
                        $(this).remove();
                })
            }
            $('#requestor_entity').val(response.entity);
        },
        error: function () {
            console.log("Error occured");
        }
    })
}

$(document).on('select2:select', '#on_behalf', function () {
    loadDetailsForBehalfOfRequest($(this).val());
});

// To remove the proof related fields from mandatory fields
function removeMandatoryCheck(options) {
    var element = options.element;
    var parentElement = options.parentElement ?? null;
    var callback = options.callback ?? null;
    need_to_validate = false;

    if (!element) return false;
    if (callback) {
        callback(element);
    } else if (parentElement) {
        parentElement.find(element).each(function () {
            $(this).addClass("not_required_fields");
        });
    } else {
        element.each(function () {
            $(this).addClass("not_required_fields");
        });
    }
    return true;
}

function remomveMandatoryCheckForSelfRequestType(element) {
    var table = element.closest("table");
    table.find("tbody tr").each(function () {
        var currentRequestType = $(this)
            .find('.proof-details-fields[name="proof_request_for"]')
            .val();
        currentRequestType = getRequestType(currentRequestType);
        var currentRow = $(this);
        if (currentRequestType == "self") {
            removeMandatoryCheck({
                parentElement: currentRow,
                element: element,
            });
        }
    });
}

/* To check whether the user is onsite or offshore*/
function isOnsiteUser(origin) {
    var offShoreLocations = ['COU_014'];
    return !offShoreLocations.includes(origin) ? true : false;
}

// To hide the proof details fields based on request type and origin
function hideProofDetailsFields(element, clearValues=true)
{
    fetchProofDetails($("#request_for").val(), "VISIBLE_FIELDS_ONLY");
    let visible_fields = proof_details_visible_fields[element.val()].concat([ "proof_type", "proof_request_for", "proof_file_upload", "proof_file_path"] );
    if(clearValues) {
        element.closest("tr").find(".file-info .info").html("");
        element.closest("tr").find(".file-upload-container").empty().removeClass("show");
    }
    element.closest("tr").find(".proof-details-fields").each(function () {
        if (!visible_fields.includes($(this).attr("name"))) {
            $(this).addClass("not_required");
            $(this).attr("disabled", true);
        } else {
            $(this).removeClass("not_required").removeClass("has_error");
            if(clearValues)
                $(this).removeAttr("disabled");
        }
        if (clearValues && element[0] != $(this)[0]) $(this).val(null);
    });

}

// To chech whether any one of the proof details fields has value
function hasAnyValue(row)
{
    var has_value = false;
    row.find(".proof-details-fields").each(function () {
        if ($(this).val()) {
            has_value = true;
            return;
        }
    });
    return has_value;
}


setTimeout(function(){
    $(document).on('input change select2:select', 'input, select, textarea', function (e) {
        handleRefresh();
    });

},500);

// To check the entity is mapped for the user or not
function checkEntityMapping()
{
    if($(document).find("#requestor_entity").length>0){
        if(!$("#requestor_entity").val()){
            if(!$('.entity_empty_error').length)
            $('#action_prevent_error').append('<div class="system-message error entity_empty_error">'
            +'<img src="/images/error.svg" alt="error">'
            +'<span>Entity details is not mapped for you in IDM. Please connect with your HR partner to update the same in IDM to proceed further.</span></div>');
            $(document).find('.request_action_buttons').each(function () { $(this).attr('disabled',true); });
        }
        else{
            $('#action_prevent_error').find('.entity_empty_error').remove();
            if( $('#action_prevent_error .system-message.error').length === 0 )
                $(document).find('.request_action_buttons').each(function () { $(this).removeAttr('disabled'); });
        }
    }
}

// To disable coutries in to_country drop down
function disableToCountry(...countries)
{
    $('.travelling_details:first select[name="to_country"]').children().each(function () {
        if( countries.includes( $(this).val() ) )
            $(this).attr('disabled', true);
        else
            $(this).removeAttr('disabled');
    })
}
function currency_format_changes_for_same_row(currency_type,input_class){
    $('.'+input_class+'.currency_format').each(function(){
      console.log($(this));
      CurrencyFormat({
        entity:$(this),
        allowDecimal:true,
        decimalPoint:2,
        prefix:'',
        currencyType:currency_type
      });
    });
  }

  $(document).on('keyup', '.date', function (e) {
	if(e.keyCode == '8')
  	$(this).val('').datepicker('update');
})
$(document).on('change', '[name="visa_number_alt"]', function () {
    $(this).closest('tr').find('[name="visa_number"]').val( $(this).val() );
});

$(document).on('select2:select', '[name="to_country"]', function () {
    loadVisaDetails( $(this).val(), $('#travel_purpose').val(), $(this).closest('tr') );
});

$(document).on('select2:select', '#travel_purpose', function () {
    let countries = $('[name="to_country"]').map( function () { return $(this).val(); } ).toArray();
    loadVisaDetails( countries, $('#travel_purpose').val() );
});

// Load visa details
function loadVisaDetails(country, travel_purpose, row=null)
{
    let requestFor = $('#request_for').val();
    if(country == "") return;
    $.ajaxSetup({
        'headers': {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    });
    $.ajax({
        url: '/load_visa_details',
        data: {'countries': country, 'travel_purpose': travel_purpose, 'request_for': requestFor},
        method: 'post',
        dataType: 'json',
        success: function (response) {
            if(response.hasOwnProperty('error')) {
                $('.alert-danger').show().html(response.error);
                setTimeout( () => $('.alert-danger').hide(), 10000 );
            } else {
                let rows = $('.travelling_details');
                if(row)
                    rows = $('.travelling_details').filter( function () { return $(this)[0] == row[0]; } );
                rows.each( function () {
                    row = $(this);
                    let visa_number_field = row.find('[name="visa_number"]');
                    let visa_number_alt_field = row.find('[name="visa_number_alt"]');
                    let visa_type_field = row.find('[name="visa_type_code"]');
                    let to_country = row.find('[name="to_country"]').val();
                    visa_not_required_countries=response.visa_not_required_countries;
                    visa_required=checkVisaRequired(to_country,$('#origin').val(),visa_not_required_countries);
                    console.log($('#request_for').val() == "RF_05" && response.visa_number.hasOwnProperty(to_country) && response.visa_number[to_country].length != 0);
                    if(($('#request_for').val() == "RF_05" || $('#request_for').val() == "RF_06") && response.visa_number.hasOwnProperty(to_country) && response.visa_number[to_country].length != 0) {
                        let visa_numbers = response.visa_number[to_country];
                        if(visa_numbers.length > 1) {
                            visa_number_field.hide();
                            visa_number_alt_field.show();
                            visa_number_alt_field.children().each(function () {
                                if( $(this).val() ) $(this).remove();
                            });
                            visa_numbers.forEach( (e) => {
                                visa_number_alt_field.append(`<option value=${e}>${e}</option>`);
                            } );
                        } else {
                            visa_number_field.show();
                            visa_number_alt_field.hide()
                            visa_number_field.val(visa_numbers[0]).attr('disabled', true);
                        }
                        visa_type_field.val(response.visa_type).attr('disabled', true).trigger('change');
                        if( $('#request_for').val() == "RF_05" )
                            visa_error();
                    } else if($('#request_for').val() == "RF_05" || $('#request_for').val() == "RF_06") {
                        visa_number_alt_field.val(null).removeAttr('disabled').hide().children().each(function () {
                            if($(this).val()) $(this).remove();
                        });
                        visa_number_field.val(null).removeAttr('disabled').show();
                        visa_type_field.val(null).removeAttr('disabled').trigger('change');
                        if( $('#request_for').val() == "RF_05" )
                            visa_error(true);
                    }
                } );
            }
        },
        error: function (response) {
            alert("Error occured");
        }
        
    });
}

// Update user orgin based on request for
function updateOrgin(request_for)
{
    if($.inArray(request_for,['','RF_05','RF_01','RF_08'])<0){
        $('.country').removeAttr("disabled");
      }else{
        var default_country=$('#default_country').val();
        $('.country').val(default_country).change();
        $('.country').attr("disabled",true);
        disableToCountry( $('#origin').val() );
      }
}
function checkVisaRequired(to_country,origin,visa_not_required_countries){
    if(Object.hasOwn(visa_not_required_countries, to_country) && visa_not_required_countries[to_country]==origin){
        
        return 0;
        
    }else{
        return 1;
    }
    


}
