/*!
 * Currency format changer V1.0
 * Will change the input or html elements value to currency format
 * CurrencyFormat({entity:$selector,allowDecimal:true,decimalPoint:2,prefix:'',
 * currencyType:currency_type});
 * 
 * Created on: 11th May 2024
 */
let conf_decimalPoint=2;
let conf_allowDecimal=true;
let conf_prefix='';
let conf_currencyType='USD';

function CurrencyFormat(parameter){
    var formatted_amount=0;
    var target=parameter.entity;
    var decimalPoint=parameter.decimalPoint?parameter.decimalPoint:conf_decimalPoint;
    var allowDecimal=parameter.allowDecimal?parameter.allowDecimal:conf_allowDecimal;
    var prefix=parameter.prefix?parameter.prefix:conf_prefix;
    var currencyType=parameter.currencyType?parameter.currencyType:conf_currencyType;
    var amount=parameter.amount;
    var isNegative = false;

    if(target){
        var target_type=target[0].nodeName.toLowerCase();
        if($.inArray(target_type,['textarea','input'])>-1){
            var value=target.val();
        }
        else{
            var value=target.html();
        }
        value=$.trim(value);
        value = value.replace(/[^0-9.-]/gi, ''); // Allow negative sign
        if(value){
            value=value.replace(/\,/g, '');
            if(allowDecimal){
                value=parseFloat(value).toFixed(decimalPoint);
            }
            else{
                value=parseInt(value);
            }
            x=value.toString();
        }
        else{
            x=0;
        }
    }
    else{
        x=amount;
        x=parseFloat(x).toFixed(decimalPoint);
    }

    // Check if the value is negative
    if (x < 0) {
        isNegative = true;
        x = Math.abs(x).toString(); // Remove the negative sign for formatting
    }
    if(parseFloat(x)>0){
        if(currencyType=='INR'){
            var afterPoint = '';
            if(x.indexOf('.') > 0)
            afterPoint = x.substring(x.indexOf('.'),x.length);
            x = Math.floor(x);
            x=x.toString();
            var lastThree = x.substring(x.length-3);
            var otherNumbers = x.substring(0,x.length-3);
            if(otherNumbers != '')
                lastThree = ',' + lastThree;
                formatted_amount = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;
        }
        else{
            formatted_amount = x.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Add the negative sign back if the number was negative
        if (isNegative) {
            formatted_amount = '-' + formatted_amount;
        }

        if(target){
            if($.inArray(target_type,['textarea','input'])>-1)
            target.val(formatted_amount);
            else
            target.html(formatted_amount);
        }
        else
        return formatted_amount;
    } 
    else{
        return "0.00";
    }      
}
