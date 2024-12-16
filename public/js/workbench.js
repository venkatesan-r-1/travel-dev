$(document).ready(function(){
  updateTotalAdvanceAmount();
  updateReloadCount();
  toggleMandatoryChecks($('#load_list tbody tr, #return_list tbody tr'));
    $(document).on('click','#forex_approval_save,#forex_process_btn',function(){
        var data = {};
        var error_flag=0;
        data['action']=$(this).val();
        data['load_date']=[];
        data['load_payment_type']=[];
        data['load_currency']=[];
        data['load_amount']=[];
        data['load_comments']=[];
        data['return_date']=[];
        data['return_payment_type']=[];
        data['return_currency']=[];
        data['return_amount']=[];
        data['return_comments']=[];
        data['forex_comments']='';
        data['forex_total_amount']='';
        var loop_count=0;
    $('.load_list').each(function(){
      $(this).find('input[type=text],select').each(function(){
      var name=$(this).attr('name');
      var value=$(this).val();
       if(data[name]!=undefined){
        if(value){
            data[name][loop_count]=value;
        }
        else{
            validation_success=validate_fields(name,value);
          if(!validation_success)
          {
            if($(this).hasClass('select-plugin'))
              $(this).parent().find('.select2-container').addClass('has_error');
            else
            $(this).addClass('has_error');
            error_flag=1;
          }
        }
    }
    });
    loop_count++;
});
var loop_count=0;
$('.return_list').each(function(){
    $(this).find('input[type=text],select').each(function(){
    var name=$(this).attr('name');
    var value=$(this).val();
     if(data[name]!=undefined){
      if(value){
          data[name][loop_count]=value;
      }
      else{
          validation_success=validate_fields(name,value);
        if(!validation_success)
        {
          if($(this).hasClass('select-plugin'))
            $(this).parent().find('.select2-container').addClass('has_error');
          else
          $(this).addClass('has_error');
          error_flag=1;
        }
      }
  }
  });
  loop_count++;
});
$(".forex_comments").find('textarea').each(function(){
    var name=$(this).attr('name');
    var value=$(this).val();
    if (!$(this).attr('disabled')) {
    if(value){
      if(data[name]!=undefined){
        data[name]=value;
      }
    }
    else{
      validation_success=validate_fields(name,value);
      if(!validation_success)
      {
        if($(this).hasClass('select-plugin'))
          $(this).parent().find('.select2-container').addClass('has_error');
        else
        $(this).addClass('has_error');
        error_flag=1;
      }
    }
    }
  });


    });

    $(document).on('click','#load_add_btn,#return_add_btn',function(){
        if(valideForexFields($(this).closest('table').find('tbody tr')))
        {
          var tbody_id = $(this).closest('tr').parent().siblings('tbody').attr('id');
          var tr_id = $(this).closest('table').find('tbody tr:last').attr('id');
          tr_id = tr_id.replace(/\d+$/, '');
          addNewRowForex(tbody_id,tr_id);
          updateReloadCount();
          $(this).closest('table').find('tbody tr:last td .forex_related_fields').each(function () {
            $(this).removeClass('required_field');
          });
        }
        else
        {
          $('.alert-danger').show().find('.message').html('Please fill the forex details');
          setTimeout(() => $('.alert-danger').hide(), 10000);
        }
    });
    $(document).on('click', '#load_delete,#return_delete', function () {
        var row_count=$(this).closest('tbody').find('tr').length;
        if(row_count>1){
            if(confirm('Are you sure you want to remove?')){
                $(this).closest('tr').remove();
                updateTotalAdvanceAmount();
            }
            updateReloadCount();
        }else{
            alert("Sorry!! Atleast one row should be present to raise a forex request.");
        }
  });

});


$(document).on('click','#ticket_process_btn,#forex_process_btn',function(){
  var comments = $('input[name=common_action_comments]').val();
  if(!comments)
{
  $('input[name=common_action_comments]').addClass('has_error');
  return false;
}
});


function addNewRowForex(tbody_id,tr_id){
	$('#'+ tbody_id +' .select-plugin').select2("destroy");
    var table_row_id = $('#' + tbody_id + ' tr:last').attr('id');
    var row_count = table_row_id.split('-');
    row_count = parseInt(row_count[1])+1;
    var new_row = $('#' + tbody_id + ' tr:last').clone();
	  new_row.attr('id',tr_id +row_count);
    new_row.attr('forex_row_id', '');
	  new_row.find('input[type=text],select,textarea').each(function() {
		$(this).val('');
		this.id = this.id.replace(/-\d+$/, '-' + row_count);
    });
    $('#'+ tbody_id +' .select-plugin').select2('');
    new_row.find('.select-plugin').select2();
    new_row.find('.select-plugin').select2('enable');

    $('#'+tbody_id).append(new_row);
}

function calculateTotalAmount(tableId) {
  var totalAmounts = {};
  $("#" + tableId + " tbody tr").each(function() {
      var amount = parseFloat($(this).find('input[name="amount"]').val().replace(/\,/g, ''));
      var currency = $(this).find('select[name="currency_code"] option:selected').text();
      if (!isNaN(amount) && currency) {
        if (!totalAmounts[currency]) {
          totalAmounts[currency] = 0;
      }
      totalAmounts[currency] += amount;
      }
  });
  return totalAmounts;
}

function updateTotalAdvanceAmount() {
  var totalLoadAmounts = calculateTotalAmount("load_list");
  var totalReturnAmounts = calculateTotalAmount("return_list");
  var advanceAmounts = {};

  var allCurrencies = Object.assign({}, totalLoadAmounts, totalReturnAmounts);
  for (var currency in allCurrencies) {
    var loadAmount = totalLoadAmounts[currency] || 0;
    var returnAmount = totalReturnAmounts[currency] || 0;
    advanceAmounts[currency] = loadAmount - returnAmount;
}

var advanceAmountString = "";
for (var currency in advanceAmounts) {
    if (advanceAmountString !== "") {
        advanceAmountString += ", ";
    }
    formatted_amount=CurrencyFormat({amount:advanceAmounts[currency],currencyType:currency});
    advanceAmountString += currency + " " + formatted_amount;
}
  $('textarea[name="forex_total_amount"]').val(advanceAmountString);
}

$(document).on('change', 'input[name="amount"], select[name="currency_code"]', function() {
  updateTotalAdvanceAmount();
});
// To add the reload count in the each row of the table
function updateReloadCount()
{
    var table = $('#load_list');
    var rowCount = table.find('tbody tr').length;
    for(var index=0; index<rowCount; index++)
    {
      var currentRow = table.find('tbody tr').eq(index);
      currentRow.find('td:first').html(`Reload ${index+1}`);
    }
}
//Validate fields
function valideForexFields(row)
{
    var result = true;
    row.find('.forex_related_fields').each(function () {
      if(!$(this).val()){
        result = false;
        if($(this).hasClass('select-plugin'))
          $(this).parent().find('.select2-container').addClass('has_error');
        else
          $(this).addClass('has_error');
      }
    });
    return result;
}
//Toggle mandatory checks on input
$(document).on('change', '.forex_related_fields', function () {
  toggleMandatoryChecks($(this).closest('tr'));
});
//Toggle mandatory checks
function toggleMandatoryChecks(row)
{
  if(checkAtleastSingleValueExists(row))
    row.find('.forex_related_fields').each(function () {
      $(this).addClass('required_field');
    });
  else
    $('.forex_related_fields').each(function () {
      $(this).removeClass('required_field');
    });
    
}
//Check if row contains atleast single value
function checkAtleastSingleValueExists(row)
{
  var result = false;
  row.find('.forex_related_fields').each(function () {
    if($(this).val()){
      result = true;
      return;
    }
  });
  return result;
}
//Remove the error class on action
function removeErrorClass()
{
  $('.forex_related_fields').removeClass('has_error');
}
