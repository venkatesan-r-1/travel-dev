$(document).ready(function(){ 
  if($('#salary_range_from').length && $('#salary_range_to').length){
    var from_value = $('#salary_range_from').val();
    var to_value = $('#salary_range_to').val();
  }
  if($('.dataTables_paginate > span > a').length == 0){
    $('.dataTables_length').remove();
    $('.dateTables_paginate').remove();
  }
  
  $('#band_details').parent().hide();

  //To highlight the active tab
  $('#myNavbar > ul.nav > li').each(function () {
    if($(this).hasClass('active'))
      sessionStorage.setItem('tab-title',$(this).attr('title'));
  })
  
  const tabTitle = sessionStorage.getItem('tab-title')
  if(tabTitle)
    $("#myNavbar > ul.nav > li[title ='"+tabTitle+"']").addClass('active');

  //To displaying tooltip tooltip
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

  //Showing alert message
  if(localStorage.getItem('alert-success')){
    showAlertMessage(localStorage.getItem('alert-success'))
  }

  //Status tracker
  // $('.visa_process_detail .container-fluid .tab-content .request-row').append($('.status_bar_btn'));

  //To update the filter badge
  $('#filterApply').on('click',function (event) {
    window.scroll(all.offsetLeft,this.offsetTop)
    var count = 0;
    $('.filter-fields').each(function () {
      var value = $(this).val();
      if(value || (Array.isArray(value) && value.length))
        count++;
    })
    document.querySelector('.filter_badge').textContent = `(${count})`;
  })

  $('#filterReset').on('click',function () {
    $(window).scrollTop(0);
    var count = 0;``
    if($('#submitted_from_date').val()!='')
      count++;
    if($('#submitted_to_date').val()!='')
      count++;
    document.querySelector('.filter_badge').textContent = `(${count})`;
  })

  //To find the last active in progress bar
  $("#progressbar .active:last").addClass('last');

  //Highlight the visa request button
  $('#travel_process_menu').removeClass('active');
  $('#visa_process_menu').addClass('active');

  //Move to last active tab.
  if($('#progressbar').length)
  {
    var last_active_tab = $(this).find('#progressbar .active').last();
    $(this).find('#progressbar .active').removeClass('current');
    last_active_tab.addClass('current');
    $('fields_div').hide();
    $('.'+last_active_tab.attr('id').replace('tab','section')).show();
  }

  $('#doj').on('change',function(){
      autoFillAspireExperience($(this).val());
  })

  //Show/Hide dependent name text box based on employee traveling status.
  //$('#dependent_name_add_btn').hide();
  var index = $('#traveling_type_id').prop('selectedIndex');
  (index==2)?showDependencyDetails():hideDependencyDetails();
  $('#traveling_type_id').on('change',function(){
    var index = $(this).prop('selectedIndex');
    (index==2)?showDependencyDetails():hideDependencyDetails();
  })

  //Move to the current active tab section.
  $('#progressbar .active').click(function (){
    $('.active').each(function(){
      $(this).removeClass('current');
    })
      $(this).addClass('current');
    
    let last_tab = $('.active').last();
    let current_tab = $('.current');
    if(last_tab[0] != current_tab[0])
      $('.request_action_buttons').each(function(){$(this).addClass('button-hidden')})
    else  
      $('.request_action_buttons').each(function(){$(this).removeClass('button-hidden')})
  });
  
  $(".myselect").select2().on('select2:open', function(e){
      $('.select2-search__field').attr('placeholder', 'Search');
  });

  $('#employee_id').on('change',function(){
      get_employee_details($(this).val());
  })

  var education_value='';
  
  //For autoloading the user details
  var request_code = {'request_code':$('input[name="edit_id"]').val()};
  getEmployeeOtherDetails(request_code);

  $('#visa_type_id').on('input',function(){
    fetchMasterDetails($(this).val());
  })

  if($('#education_category').val())
    getEducationDetails($('#education_category').val(),$('#education').val());
    
  $(document).on('select2:select','#education_category',function () {
    getEducationDetails($(this).val(),education_value);
  })

  //For autoloading the user details
  var request_code = {'request_code':$('input[name="edit_id"]').val()};
  getEmployeeOtherDetails(request_code);

  // To show/hide the section based on thee input selection
  $('#progressbar li.active').click(function(){
    var selected_tab=$(this).attr('id');
    var target_section=selected_tab.replace('tab','section');
    $('.fields_div').hide();
    $('.'+target_section).show();
  });

  //To toggle the acceptance checkbox
  $('#acceptance_by_user').on('click',function(){
    if($(this).is(':checked'))
      $('#acceptance_by_user').val(1);
    else
      $('#acceptance_by_user').val(0);
  });

  //To toggle the green card eligibility checkbox
  $('#green_card_title').on('click',function () {
    $(this).is(':checked') ? $(this).val(1) : $(this).val(0);
  })

  /*******Add datepicker */
  
  $('.date').datepicker({  
    'format': 'dd-M-yyyy',
    'autoclose': true,
  });

  $('#dob').datepicker('setEndDate',new Date());

  $('#dob,#doj').addClass('past-date');
  $('#one_time_bonus_payout_date,#next_salary_revision_on').addClass('future-date');

  $('.past-date').on('changeDate', function () {
    if( new Date($(this).val().replaceAll('-','/')) > new Date() )
      $(this).val("");
  });

  $('#doj').datepicker('setEndDate',new Date());

  $('#next_salary_revision_on').datepicker('setStartDate', new Date());

  $('#one_time_bonus_payout_date').datepicker('setStartDate',new Date());

  $('.future-date').on('changeDate', function () {
    if( new Date($(this).val().replaceAll('-','/')).getDate() < new Date().getDate() )
      $(this).val("");
  });

  $('#petition_start_date').on('changeDate',function(){
    $('#petition_end_date').datepicker('setStartDate', $(this).val())
    var fromDate = new Date($(this).val());
    var endDate = [Math.abs(fromDate.getFullYear())+3,fromDate.getMonth()+1,fromDate.getDate()].toString();
    $('#petition_end_date').datepicker('setEndDate',new Date(endDate));
    $('#petition_end_date').val(null);
  })

  $('#petition_end_date').on('changeDate',function(){
  	$('#petition_start_date').datepicker('setEndDate', $(this).val())
    if( new Date($(this).val().replaceAll('-','/')) < new Date($('#petition_start_date').val().replaceAll('-','/')) )
      $(this).val(null);
  })

  var fromDate = $('#petition_start_date').val();
  var toDate = $('#petition_end_date').val();

  if(fromDate){
    $('#petition_end_date').datepicker('setStartDate',fromDate);
    fromDate = new Date(fromDate);
    var endDate = [Math.abs(fromDate.getFullYear())+3,fromDate.getMonth()+1,fromDate.getDate()].toString();
    $('#petition_end_date').datepicker('setEndDate',new Date(endDate))
  }

  if(toDate)
    $('#petition_start_date').datepicker('setEndDate',toDate);

  $('#travel_date').datepicker('setStartDate',new Date($('#petition_start_date_text').text().replaceAll('-','/')));

  $('#travel_date').on('changeDate', function () {
    if( new Date($(this).val().replaceAll('-','/')) < new Date($('#petition_start_date_text').text().replaceAll('-','/')) )
      $(this).val(null);
  });

  if($('#visa_ofc_date').val())
  {
    let ofcDate = new Date($('#visa_ofc_date').val().replaceAll('-','/'));
    let interviewStartDate = new Date(ofcDate);
    interviewStartDate.setDate(ofcDate.getDate()+1);
    $('#visa_interview_date').datepicker('setStartDate',interviewStartDate);
  }

  $('#visa_ofc_date').on('changeDate', function () {
    if($(this).val())
    {
      let ofcDate = new Date($(this).val().replaceAll('-','/'));
      let interviewDate = new Date($('#visa_interview_date').val().replaceAll('-','/'));
      let interviewStartDate = new Date(ofcDate);
      interviewStartDate.setDate(ofcDate.getDate()+1);
      if(ofcDate >= interviewDate)  
        $('#visa_interview_date').val(null);
      $('#visa_interview_date').datepicker('setStartDate',interviewStartDate);
    }
  });

  $('#visa_interview_date').on('changeDate', function () {
    if($(this).val())
    {
      let interviewDate = new Date($(this).val().replaceAll('-','/'));
      let ofcDate = new Date($('#visa_ofc_date').val().replaceAll('-','/'));
      if(ofcDate >= interviewDate)
        $(this).datepicker('setDate','');
    }
  });

  // to add additional dependent name field 
  const dependentNameLabelConfig = {0:"Spouse",1:"First child",2:"Second child",3:"Third child",4:"Fourth child"};
  
  $('.dependent_name:last').append($('#dependent_name_add_btn'));
  $(document).on('click','#dependent_name_add_btn',function(event){
    var last_dependent_field = $('.dependent_name:last').attr('id');
    last_dependent_field = last_dependent_field.split("-");
    var new_count = parseInt(last_dependent_field[1])+1;
    var no_of_dependent_names = $('.dependent_name').length;
    if(no_of_dependent_names <= 4){
      $(".dependent_name:last").clone().insertAfter("div.dependent_name:last");
      $('.dependent_name:last').attr('id',last_dependent_field[0]+'-'+new_count);
      $('.dependent_name').each(function(key,val){
        //$(this).find('label').text('Dependent name '+(key+1));
        $(this).find('label').text(dependentNameLabelConfig[key]);
      });
      $('.dependent_name:last input[type=text]').attr('id','dependent_name-'+new_count);
      $('.dependent_name:last input[type=text]').val('');
    }
    no_of_dependent_names = $('.dependent_name').length;

    if(no_of_dependent_names == 5)
        $('.dependent_name_add_btn').last().hide();

    if(no_of_dependent_names >= 1)
      $('.dependent_name_remove_btn').css('display','table-cell');
    $('.dependent_name:first .dependent_name_remove_btn').hide();
      
    if(no_of_dependent_names > 3)
      $('.visa-stamping-notes').show();
    $(this).remove();
  })
  $(document).on('click','.dependent_name_remove_btn',function(){
    var addBtn = document.querySelector('.dependent_name_add_btn');
    $(this).parent().remove();
    //$('.dependent_name').last().append(addBtn);
    document.querySelector('.dependent_name:last-of-type').append(addBtn);
    $('.dependent_name').each(function(key,val){
      $(this).find('label').text(dependentNameLabelConfig[key]);
    });
    var no_of_dependent_names = $('.dependent_name').length;
    if(no_of_dependent_names < 5)
      $('#dependent_name_add_btn').css('display','table-cell');
    if(no_of_dependent_names == 1)
      $('.dependent_name_remove_btn').hide();
    if(no_of_dependent_names <= 3)
      $('.visa-stamping-notes').hide();
  })

  $('.dependent_name:first .dependent_name_remove_btn').hide();
  if($('.dependent_name').length == 5)
      $('#dependent_name_add_btn').hide();
  if($('.dependent_name').length > 3)
      $('.visa-stamping-notes').show();
  else
      $('.visa-stamping-notes').hide();

  if($('#one_time_bonus').val()){
    $('#one_time_bonus_payout_date').parent().show();
    $('#one_time_bonus').closest('.row').find('.col-md-2:last').addClass('step-down');
  }
  else {
    $('#one_time_bonus_payout_date').val(null);
    $('#one_time_bonus_payout_date').parent().hide();
    $('#one_time_bonus').closest('.row').find('.col-md-2:last').removeClass('step-down');
  }

  if($('#one_time_bonus_payment_date_text').length)
    $('#one_time_bonus_payment_date_text').closest('.row').find('.col-md-2:last').addClass('step-down');

    //To pass the selected values to the server
  $('.request_action_buttons').on('click', function() {
    var action=$(this).val();
    var data={};
    var error_flag=0;
    var current_action = $(this).attr('name');
    data['employee_id']='';data['visa_type_id']='';data['request_type_id']='';data['client_code']='';
    data['petition_id']='';data['remarks']='';data['action']=action;data['edit_id']='';data['first_name']='';
    data['last_name']='';data['gender_id']='';data['dob']='';data['doj']='';data['address']='';data['passport_no']='';
    data['education']='';data['minimum_wage']='';data['work_location']='';data['filing_type_id']='';data['job_titile_id']='';
    data['india_experience_year']='';data['india_experience_month']='';data['overall_experience_year']='';
    data['overall_experience_month']='';data['band_details']='';data['passport-file-hidden']='';data['cv-file-hidden']='';data['degree-file-hidden']='';data['salary_range_from']='';data['salary_range_to']=''; 
    data['us_job_title_id']='';data['acceptance_by_user']='';data['us_salary']='';data['one_time_bonus']='';data['one_time_bonus_payout_date']='';
    data['next_salary_revision_on']='';data['us_manager_id']='';data['inszoom_id']='';data['entity_id']='';data['attorneys_id']='';
    data['petition_file_date']='';data['receipt_no']='';data['petition_start_date']='';data['petition_end_date']='';data['petition-file-hidden']='';data['visa-file-hidden']='';
    data['visa_interview_type_id']='';data['visa_ofc_date']='';data['visa_interview_date']='';data['visa_status_id']='';data['offer_letter_path']='';
    data['travel_date']='';data['travel_location']='';data['traveling_type_id']='';
    data['record_number']='';data['most_recent_doe']='';data['admit_until']='';data['gc_initiated_on']='';data['green_card_title']='';
    data['immigration_offer_letter_path']='';data['word_document_path']='';data['education_category']='';
    $('input[name=dependent_name_input]').each(function(){
      data['dependent_name_input']=[];
    }); 

    //Exception input fields for validations
    let exceptionList = ['passport-file-hidden','cv-file-hidden','degree-file-hidden','petition-file-hidden','visa-file-hidden','one_time_bonus_payout_date'];

    //When one time bonus is provided
    if($('#one_time_bonus').val())
      exceptionList = exceptionList.filter((e) => e!='one_time_bonus_payout_date');

      //When offer has been rejected
    if($('#acceptance_by_user').length && !$('#acceptance_by_user').is(":checked"))
      exceptionList.push('all');
    $('#acceptance_by_user').on('click',function () {
      if(!$('#acceptance_by_user').is(":checked"))
      exceptionList.push("all");
    })

    //When visa has been denied
    if($('#visa_status_id').val() == "2")
      exceptionList.push("all");
    $('#visa_type_id').on('change',function () {
      if($('#visa_status_id').val() == "2") 
        exceptionList.push("all");
    })

    //When visa interview type is "drop box"
    if($('#visa_interview_type_id').val() == "1")
      exceptionList = exceptionList.concat(["visa_ofc_date","visa_interview_date"]);
    
    $('#visa_interview_type_id').on('change',function () {
      if($(this).val() == "1")
        exceptionList = exceptionList.concat(["visa_ofc_date","visa_interview_date"]);
    })
    
    if(current_action == "deny")
      exceptionList.push("all");
    
    else if(current_action == "rfe" || current_action == "save")
      exceptionList = exceptionList.concat(["petition_start_date","petition_end_date","petition-file-info"]);


    $(".fields_div").find('input[type=text],input[type=hidden],input[type=checkbox],select,textarea').each(function(){
      var name=$(this).attr('name');
      var value=$(this).val();
      var need_to_validate = -1;
      if(value)
        need_to_validate = $.inArray(name,['minimum_wage','salary_range_from','salary_range_to','us_salary','dependent_name_input','offer_letter_path','immigration_offer_letter_path','word_document_path','receipt_no']);
      if(value && need_to_validate==-1){  
        if(name == 'dependent_name_input'){
          data[name].push(value);
        }
        else if(data[name]!=undefined){
          data[name]=value;
        }
      }
      else{
        validation_success=validate_fields(name,value,exceptionList,current_action,from_value,to_value);
        $('html, body').animate({scrollTop : 0},500);
        if(!validation_success)
        {
          if($(this).hasClass('myselect'))
            $(this).parent().find('.select2-container').addClass('has_error');
          else
            $(this).addClass('has_error');
          error_flag=1;
          $('.request_action_buttons').removeAttr('disabled');
          setTimeout(function() {
            $('.ui-wait').hide();
            $(".alert-danger").hide();
          }, 10000);
        }
        else if(need_to_validate > -1)
        {
          if(name == "dependent_name_input")
            data[name].push(value);
          else
            data[name] = value;
        }
        else{
          data[name]='';
        } 
      }
    });
    if(!error_flag){
      if(current_action != "save"){
        if($('#acceptance_by_user').length && !$('#acceptance_by_user').is(':checked'))
          showConfirmBox(data,'Confirmation','Employee is not undertaken the offer.<br>Are you sure to continue?','Yes','No')
        else
          showConfirmBox(data)
      }
      else
        save_request_details(data);      
    }
  });

    // Adding datatable for the tables.
    if($('#table_request_home').length)
    {
      var table_request_home = $('#table_request_home').DataTable({
        dom:'Bfrtipl',
        "paging":   true,
        "pageLength":10,
        "pagingType": 'full_numbers',
        // "scrollY": "330px",
        // "scrollCollapse": true,
        order:[],"oLanguage": {"sSearch":"","sSearchPlaceholder": "","sLengthMenu": 'show <select>'+
        '<option value="5">5</option>'+
        '<option value="10">10</option>'+
        '<option value="20">20</option>'+
        '<option value="-1">All</option>'+
        '</select> entries',
        "sEmptyTable":     "No record found","oPaginate":{"sNext":">","sPrevious":"<","sFirst":"<<","sLast":">>",}},
        'initComplete': function () {$('#table_request_home').wrap("<div class='table_request_home_wrapper'></div>");}
          });
        
      $('#search-bar-home-table').on('keyup',function () {
        table_request_home.search( this.value ).draw();
      })

      table_request_home.on('draw',function () {
        // To add tooltip for status icon
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })

        if($('.dataTables_paginate > span > a').length == 0)
        {
          $('.dataTables_length').hide();
          $('.dataTables_paginate').hide();
        }
        else
        {
          $('.dataTables_length').show();
          $('.dataTables_paginate').show();
        }
      })
    }

    if($("#visa_process_filter").length)
    {
      $('#visa_process_filter').on('click',function(){
        $('#visa_process_filter_div').toggle();
      })
    }
    $('#visa_process_financial_year_filter').on('change',function (event) {
      applyFynYearFilter($(this).val());
    });
});

function get_employee_details(aceid){
    $('#emp_email,#emp_dept,#emp_manager','#emp_practice').html('');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
        type:'POST',
        data:{aceid:aceid},
        url:'/get_employee_details',
        dataType:'json',
        async:false,
        success:function(data){
           user_details=data.user_details;
           $('#emp_email').html(user_details.user_email);
           $('#emp_dept').html(user_details.user_dept);
           $('#emp_manager').html(user_details.user_pm);
           $('#emp_practice').html(user_details.practice ?? "-");
        } 
    });
}
//To validation the dependency validations if any. added by ganesh.veilsamy on 15-11-2022
function validate_fields(name,value,exceptionList = [], current_action, from_value, to_value){
    exceptionList = ['edit_id','remarks','one_time_bonus','next_salary_revision_on'].concat(exceptionList);
    if(exceptionList.includes('all'))
      return 1;
    if($.inArray(name,exceptionList)<0){   
      switch(name) {
        case "minimum_wage":
          if(!value){
            $('.alert-danger').show().html('Please fill all the mandatory fields').append(`<span class="danger-close-btn">&times;</span>`)
            return 0;
          }
          if(value == 0)
          {
            $('.alert-danger').show().html('Please enter the amount other than Zero').append(`<span class="danger-close-btn">&times;</span>`)
            return 0;
          }
          break;

        case "salary_range_from":
          var minimum_wage_text = $('#minimum_wage_text').prop('textContent');
          var minimum_wage = minimum_wage_text.replaceAll('USD ','');
          minimum_wage = minimum_wage.replaceAll(',','');
          if(!value){ 
            $('.alert-danger').show().html('Please fill all the mandatory fields').append(`<span class="danger-close-btn">&times;</span>`)
            return 0;
          }
          if(value == 0)
          {
            $('.alert-danger').show().html('Please enter the amount other than Zero').append(`<span class="danger-close-btn">&times;</span>`)
            return 0;
          }
          if(parseFloat(value) < parseFloat(minimum_wage)){
            $('.alert-danger').show().html('Please enter the amount greater than Minimum range').append(`<span class="danger-close-btn">&times;</span>`)
            return 0;
          }
          if(current_action == "update" && parseFloat(from_value) == parseFloat(value) && parseFloat(to_value) == parseFloat($('#salary_range_to').val())){
            $('.alert-danger').show().html('There is no change in salary range. So, please update').append(`<span class="danger-close-btn">&times;</span>`)
            return 0;
          }
          break;

        case "salary_range_to":
          if(!value){
            $('.alert-danger').show().html('Please fill all the mandatory fields').append(`<span class="danger-close-btn">&times;</span>`)
            return 0;
          }
          if(value == 0)
          {
            $('.alert-danger').show().html('Please enter the amount other than Zero').append(`<span class="danger-close-btn">&times;</span>`)
            return 0;
          }
          else if(parseFloat(value) <= parseFloat($('#salary_range_from').val()))
          {
            $('.alert-danger').show().html('Please enter valid salary range').append(`<span class="danger-close-btn">&times;</span>`)
            return 0;
          }
          else if(current_action == "update" && parseFloat(to_value) == parseFloat(value) && parseFloat(from_value) == parseFloat($('#salary_range_from').val())){
            $('.alert-danger').show().html('There is no change in salary range. So please update').append(`<span class="danger-close-btn">&times;</span>`)
            return 0;
          }
          break;
        
        case "us_salary":
            var range = $('#us_salary_range').prop('textContent');
            range = range.split("to");
            var from = parseFloat(range[0].replace(/[^0-9.]/g,""));
            var to = parseFloat(range[1].replace(/[^0-9.]/g,""));
            if(!value){
              $('.alert-danger').show().html('Please fill all the mandatory fields').append(`<span class="danger-close-btn">&times;</span>`)
              return 0;
            }
            if(value == 0)
            {
              $('.alert-danger').show().html('Please enter the amount other than Zero').append(`<span class="danger-close-btn">&times;</span>`)
              return 0;
            }
            if(parseFloat(value) < from || parseFloat(value) > to)
            {
              $('.alert-danger').show().html('Salary should be between the salary range mentioned by US team').append(`<span class="danger-close-btn">&times;</span>`)
              return 0;
            }
            break;
        // case "petition_start_date":
        // case "petition_end_date":
        //     if(current_action == "rfe" || current_action == "save" || current_action == "deny")
        //       return 1;
        //     else if(!value){
        //       $('.alert-danger').show().html('Please fill all the mandatory fields').append(`<span class="danger-close-btn">&times;</span>`)
        //       return 0;
        //     }
        //     return 0;
          
         case "dependent_name_input":
            let parentDiv = $('#dependent_name-1').parent() || $('#dependent_name').parent();
            if(value || parentDiv.hasClass('hidden_textfield'))
              return 1;
            else if(!value){
              $('.alert-danger').show().html('Please fill all the mandatory fields').append(`<span class="danger-close-btn">&times;</span>`)
              return 0;
            }
          
          case "receipt_no" :
            if(!value){
              $('.alert-danger').show().html('Please fill all the mandatory fields').append(`<span class="danger-close-btn">&times;</span>`)
              return 0;
            }

            if(value.length != 13){
              $('.alert-danger').show().html('Please enter the valid receipt number').append(`<span class="danger-close-btn">&times;</span>`)
              return 0;
            }
            
            break;

          case "offer_letter_path":
          case "immigration_offer_letter_path":
          case "word_document_path":
              if(!value){
                $('.alert-danger').show().html('Please generate the offer letter before publishing the request').append(`<span class="danger-close-btn">&times;</span>`);
                return 0;
              }
              return 1
        
        default: 
          if(!value)
          {
            $('.alert-danger').show().html('Please fill all the mandatory fields').append(`<span class="danger-close-btn">&times;</span>`)
            return 0;
          }
          break;
      }
    }
    return 1;
  }

  $(document).on('focus','.has_error',function(){
    $(this).removeClass('has_error');
    $('.alert-danger').hide();
  });

  $(document).on('click','#back', function(){
    window.location.href = "/visa_process/home";
  })

  /* validations */
  $(document).on('input','.numbers',function(){
    var filteredValue = restrictCharacters($(this).val(),/^$|^[0-9]{1,}$/);
    $(this).val(filteredValue);
  });

  $(document).on('input','.month',function(){
    var filteredValue = restrictCharacters($(this).val(),/^$|^[0-9]$|^0[0-9]$|^1[0-1]$/);
    $(this).val(filteredValue);
  });

  $(document).on('input','.years',function(){
    let filteredValue = restrictCharacters($(this).val(),/^$|^[0-9]$|^[0-5][0-9]$/);
    $(this).val(filteredValue);
  });

  $(document).on('input','.floatingpoint',function(){
    let filteredValue = restrictCharacters
    ($(this).val(),/^$|^[0-9]{1,}$|^[0-9]{1,}\.[0-9]{0,2}$/);
    $(this).val(filteredValue);
    $(this).on('change',function(){
      let isDot = $(this).val().slice(-1) == '.';
      $(this).val(isDot?$(this).val().slice(0,-1):$(this).val());
    });
  });

  $(document).on('input','.name',function(){
    let filteredValue = restrictCharacters($(this).val(),/^$|^[A-Za-z ]{1,}$/);
    $(this).val(filteredValue);
  });

  $(document).on('input','.text',function (){
    let filteredValue = restrictCharacters($(this).val(),/^$|^[A-Za-z0-9 ]{1,}$/);
    $(this).val(filteredValue);
  })

  $(document).on('input','.alphaNum',function(){
    let filteredValue = restrictCharacters
    ($(this).val(),/^$|^[A-Za-z0-9]{1,}$/);
    $(this).val(filteredValue);
  })

  $(document).on('input','.receipt_no',function () {
    let filteredValue = restrictCharacters($(this).val(),/^$|^[A-Za-z0-9]{1,13}$/)
    $(this).val(filteredValue);
  })

  function restrictCharacters(value,pattern)
  {
    return pattern.test(value)?value:restrictCharacters(value.slice(0,-1),pattern);
  }

// To hide all the dependent name textboxes.
function hideDependencyDetails()
{
  $('.dependent_name:first').append($('#dependent_name_add_btn'));
  $('#dependent_name_add_btn').hide();
  $('.dependent_name_remove_btn').hide();
  $('.visa-stamping-notes').hide();
  $('.dependent_name').each(function(){
    if($('.dependent_name:first')[0] == $(this)[0])
    {
      $(this).addClass('hidden_textfield');
      $('#dependent_name-1').val("");
    }
    else
      $(this).remove();
  })
}

//To show depedent name text box.
function showDependencyDetails()
{
  $('#dependent_name_add_btn').css('display','table-cell');
  $('.dependent_name').removeClass('hidden_textfield');
}

//To calculate the aspire experience in terms of years and month based on the DOJ
function autoFillAspireExperience(value)
{
    var date_of_joining = new Date(value);
    var current_date = new Date();
    var month_difference = current_date.getMonth() - date_of_joining.getMonth();
    var year_difference = current_date.getFullYear() - Math.abs(date_of_joining.getFullYear());
    var actual_year_diff = month_difference<0?year_difference-1:year_difference;
    var actual_month_diff = month_difference<0?12+month_difference:month_difference;
    if(!value){
      actual_month_diff = ""; actual_year_diff = "";
    }
    $('#india_experience_year').val(actual_year_diff);
    $('#india_experience_month').val(actual_month_diff);
}

//To send the formData to server
function save_request_details(data)
{
  $('.ui-wait').show();
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.ajax({
    type:'POST',
    url:'/visa_process/save_request_details',
    dataType:'json',
    data:data,
	async: false,
    success:function(data){
      $('.request_action_buttons').removeAttr('disabled');
      if(data['error'])
      {
        $('.alert-danger').html('');
        $('.alert-danger').html(data['error']).show();
        setTimeout(function() {
          $('.ui-wait').hide();
          $(".alert-danger").hide();
        }, 10000); 
      }else{
		if(data.mail_details)
			sendMail(data);
		else{
			setTimeout( function() {
			$(".ui-wait").hide();
			localStorage.setItem('alert-success',data["success"]);
			window.open(data['redirect_url'],"_self");
			$(window).scrollTop(0); 
			}, 1000);
		}
      }
    },
    error:function(error){
        //show_alert_notification('An error has been occured while saving changes.Please contact help.mis@aspiresys.com for assistance.');
        $('.request_action_buttons').removeAttr('disabled');
        $('.ui-wait').hide();
    }
  });
}

function getEmployeeOtherDetails(data){

if(!$('#passport_no').val()){
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url: "/visa_process/getemployeeotherdetails",
    method: "POST",
    dataType: "JSON",
    data: data,
    success: function (response){
        $('#first_name').val(response.first_name);
        $('#last_name').val(response.last_name);
        $('#gender').val(response.gender_id);
        $('#dob').datepicker('setDate', response.dob);
        $('#doj').datepicker('setDate', response.doj);
        $('#address').val(response.address);
        $('#passport_no').val(response.passport_no);
        $('#band_details').val(response.band_details);
        $('#education_category').val(response.education_category_id);
        education_value=response.education_details_id;
        $('#education_category').trigger('change');
        $('#education').val(response.education_details_id);
        autoFillAspireExperience($('#doj').val());
    }
  })
}
};

function showAlertMessage(message){
    $('.requestdetail .row:first').append(`<div class='alert alert-success resultDiv'><span>${message}</span><span class='close-btn'>&times;</close></div>`);
    localStorage.removeItem('alert-success');
    setTimeout( function () {
        $('.alert-success').remove();
    },10000)
    $('.close-btn').on('click',function () {$('.alert-success').remove();} )
}

function showConfirmBox(data,title="Confirmation", body="Are you sure want to submit?", primaryAction="Yes", secondaryAction="No"){
  $('.ui-wait').hide();
  $('.confirm-box-wrap').remove();
  $('body').append("<div class='confirm-box-wrap'></div>");
  $('.confirm-box-wrap').append("<div class='confirm-box'></div>");
  $('.confirm-box').append(`<div class='confirm-box-header'>${title}</div>`);
  $('.confirm-box').append(`<div class='confirm-box-body'>${body}</div>`);
  $('.confirm-box').append("<div class='confirm-box-footer'></div>");
  $('.confirm-box-footer').append(`<button class='primary-button'>${primaryAction}</button><button class='secondary-button'>${secondaryAction}</button>`);
  var flag = false;
  $('.confirm-box-footer .primary-button').on('click', function () {
    hideConfirmBox();
    $('.ui-wait').show();
    save_request_details(data)
  })
  $('.confirm-box-footer .secondary-button').on('click', function () {
    hideConfirmBox();
  })
  return flag;
}

function hideConfirmBox(){
  $('.confirm-box-wrap').remove();
}

$(document).on('click','.danger-close-btn',function () { $(this).parent().hide() })

function applyFynYearFilter(value)
{
  let from_date = value.split('to')[0].replaceAll("-","/");
  let to_date = value.split('to')[1].replaceAll("-","/");
  $('#submitted_from_date').datepicker("setStartDate", new Date(from_date));
  $('#submitted_from_date').datepicker("setEndDate", new Date(to_date));
  $('#submitted_to_date').datepicker("setStartDate", new Date(from_date));
  $('#submitted_to_date').datepicker("setEndDate", new Date(to_date));
}

function fetchMasterDetails(value)
{
  var data = {"visa_type_id":value};

  $.ajaxSetup({
    headers:{
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url: "/fetchMasterDetails",
    method: "POST",
    dataType: "JSON",
    data: data,
    success: function (response){
      $('#petition_id').find("option:not([value=''])").each(function () { $(this).remove(); });
      for( key in response){
        if(response.hasOwnProperty(key))
          $('#petition_id').append(`<option value = ${key}>${response[key]}</option>`);
      }
  
    }  
  })
}

function getEducationDetails(value,education)
{
  if(!value){
    $('#education').attr('disabled','disabled');
    return;
  }

  $.ajaxSetup({
    headers : {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    }
  });

  $.ajax({
    url : "/getEducationDetails",
    method : "POST",
    dataType : "JSON",
    data : {'education_category_id':value},
    success : function (response) {
      $('#education').removeAttr('disabled');
      $('#education').find("option:not([value=''])").each(function () { $(this).remove(); });
      for( element in response ){
        if(response.hasOwnProperty(element))
          $('#education').append(`<option value = ${element}>${response[element]}</option>`);
      }
      $('#education').val(education);
    },
    error : function (response) {
      alert("Error occured...");
    }
  })
}

$(document).on('input', '#one_time_bonus', function () {
  if($(this).val()){
    $('#one_time_bonus_payout_date').parent().show();
    $(this).closest('.row').find('.col-md-2:last').addClass('step-down');
  }
  else {
    $('#one_time_bonus_payout_date').val(null);
    $('#one_time_bonus_payout_date').parent().hide();
    $(this).closest('.row').find('.col-md-2:last').removeClass('step-down');
  }
});

// To send mails
function sendMail(data)
{
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name=csrf_token]').attr('content'),
		}
	});
	$.ajax({
		url: "/send_mails",
		method: "post",
		dataType: "json",
		data: data.mail_details,
		success: function () {
			return true;
		},
		error: function () {
			
		}
	});

	setTimeout( function() {
		$(".ui-wait").hide();
		localStorage.setItem('alert-success',data["success"]);
		window.open(data['redirect_url'],"_self");
		$(window).scrollTop(0); 
	  }, 1000);
}
