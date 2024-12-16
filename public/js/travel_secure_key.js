$(document).on('click', '#save', function () {
   let data = {}; let action = $(this).val();
   let hasError = false;
   $('.secure-key-generation-container').find('.form-fields').each(function () {
      let name = $(this).attr('name'); let value = $(this).val();
      let validated = validateField(name, value);
      if ( validated.isValid )  {
         data[name] = value;
      } else {
         displayError({messageCode : validated.messageCode});
         $(this).addClass('has_error');
         hasError = true;
      }
   });
   if(!hasError) {
      postData(data);
   }
});

$(document).on('click', '#reset', function () {
   $('.secure-key-generation-container').find('.form-fields').each(function () {
      $(this).val(null);
   });
}); 

$(document).on('click', '.alert_close', function () {  $('.alert-danger').hide(); });

$(document).on('focus click', '.secure-key-generation-container .form-fields',  function () { $(this).removeClass('has_error'); });

function validateField(name, value, action)
{
   let exceptionList = getExceptionList(action);
   if(exceptionList.includes(name)) {
      return { isValid : true };
   }
   if(!value) {
      return {isValid : false, messageCode : 'ERR01'};
   }
   switch (name)
   {
      case "visa_secure_key" : 
         const min = 5; const max = 15;
         if(value.length < min || value.length > max) {
            return {isValid : false, messageCode : "ERR10"};
         } else {
            return {isValid : true};
         }
      
      default :
         if (!value) {
            return { isValid : false, messageCode : "ERR01" };
         } else {
            return { isValid : true };
         }
   }
}

function getExceptionList(action)
{
   switch ( action )
   {
      default: return [];
   }
}

function displayError({messageCode = null, messageTxt = null})
{
   let message = messageTxt || getVisaConfig('errorMessage', messageCode);
   let alertBox = $('.alert-danger');
   let closeIcon = `<span class="alert_close"><img title="Close alert" src="/img/close-red.svg" class=""></span>`;
   alertBox.show().html(message).append(closeIcon);
   setTimeout( () => alertBox.hide(), 10000 );
}

function displaySuccess({messageCode, messageTxt} = {})
{
   let message = messageTxt || getVisaConfig('errorMessage', messageCode);
   let alertBox = $('.alert-success');
   let closeIcon = `<span class="alert_close"><img title="Close alert" src="/img/close-green.svg" class=""></span>`;
   alertBox.show().html(message).append(closeIcon);
   setTimeout( () => alertBox.hide(), 10000 );
}

function postData(data)
{
   $.ajaxSetup({
      headers: {
         'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content'),
      }
   });
   $.ajax({
      url: '/update_secure_key',
      method: 'POST',
      dataType: 'JSON',
      data: data,
      success: function (response) {
         if(response.error) {
            displayError({messageTxt : response.error});
         } else {
            displaySuccess({messageTxt : response.message});
         }
      },
      error: function (response) {
         alert("Error occured");
      }
   });
}
