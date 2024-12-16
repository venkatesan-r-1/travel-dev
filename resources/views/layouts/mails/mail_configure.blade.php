@extends('header')

@section('title', 'Travel system')
@php $page_navigation_no='mail_config'; @endphp 
@section('content')
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/texteditor/package/dist/trumbowyg.js"></script>
<link rel="stylesheet" href="js/texteditor/package/dist/ui/trumbowyg.css">
<script type="text/javascript" src="js/select2.min.js"></script>
<link type="text/css" href="/css/select2.min.css" rel="stylesheet">
<div class="container" style="width:90%">
    <div class="row">
        <div class="col-md-12">
<table class="table table-bordered" id="mail_configuration">
    <tbody>
        <tr>
            <td>
                <lable>Mail name</label></td>
                <td>
                {{ Form::select('mail_name', [''=> 'Select'] + $mail_names, null, ['id'=>"mail_name",'class'=>'form-control select-plugin']) }}
            </td>
        </tr>
        <tr>
            <td>
                <lable for="mail_details">Mail Details</label></td>
            <td>
                <textarea name="mail_details" id="mail_details"></textarea>
            </td></tr>
        <tr>
            <td>
                <lable for="mail_subject">Mail subject</label></td>
            <td>
                <input name="subject" type="text" class="form-control" id="mail_subject" />
            </td>
        </tr>
        <tr>
            <td>
                <lable for="mail_to">To</label></td>
            <td>
                {{ Form::select('to',$roles, null, ['id'=>"mail_to",'class'=>'form-control select-plugin','multiple'=>'true']) }}
            </td>
        </tr>
        <tr>
            <td>
                <lable for="mail_cc">CC</label></td>
            <td>
                {{ Form::select('cc', $roles, null, ['id'=>"mail_cc",'class'=>'form-control select-plugin','multiple'=>'true']) }}
            </td>
        </tr>
        <tr>
            <td>
                <lable for="mail_cc">BCC</label></td>
            <td>
                {{ Form::select('bcc',$roles, null, ['id'=>"mail_bcc",'class'=>'form-control select-plugin','multiple'=>'true']) }}
            </td>
        </tr>
        <tr>
            <td>
                <lable for="mail_custom_to">Custom To</label></td>
            <td>
                <input name="custom_to" type="text" class="form-control" id="mail_custom_to" />
            </td>
        </tr>
        <tr>
            <td>
                <lable for="mail_custom_cc">Custom CC</label></td>
            <td>
                <input name="custom_cc" type="text" class="form-control" id="mail_custom_cc" />
            </td>
        </tr>
        <tr>
            <td>
                <lable for="mail_custom_cc">Custom CC</label></td>
            <td>
                <input name="custom_bcc" type="text" class="form-control" id="mail_custom_bcc" />
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <lable for="mail_content">Mail content</label>
                <textarea name="body" id="mail_content">
                </textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2">
            <button id="submit" type="submit" class="primary-button request_action_buttons">Save / Update</button>
            </td>
        </tr>
    </tbody>
</table>
                 
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#mail_content').trumbowyg();
    });
    $('.select-plugin').select2();
    $('#mail_name').on('change',function(){
        var mail_name=$(this ).find('option:selected').text();
        $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
       });
       $.ajax({
        type:'POST',
        data:{mail_name:mail_name},
        url:'/get_mail_details',          
        dataType:'Json',
        async:false,
        success:function(data,textStatus, jqXHR){ 
            if(data.ERR){
                alert('Error occurred');
            }
            else if(data.NOREC){
                alert("There is no mail configured for the selected category. Please fill and save the details.");
                $("#mail_details,#mail_subject,#mail_to,#mail_cc,#mail_bcc,#mail_custom_to,#mail_custom_cc,#mail_custom_bcc,#mail_content").val('');
                $("#mail_to,#mail_cc,#mail_bcc").trigger('change');
                $('.trumbowyg-editor').html('');
            }
            else if(data.mail_details){
                var mail_details=data.mail_details;
                $.each(mail_details,function(name,value){
                    if(name!="mail_name"){
                        var target=$("#mail_configuration").find("[name="+name+"]");
                        if(value){
                            if($.inArray(name,['to','cc','bcc'])>-1)
                            value=value.split(",");
                            if(target.hasClass("select-plugin")){
                                target.val(value);
                                var related_id=target.attr('id');
                                $("#"+related_id).trigger("change");
                            }
                            else if(target.hasClass('trumbowyg-textarea')){
                                target.val(value);
                                $('.trumbowyg-editor').html(value);
                            }
                            else{
                                target.val(value);
                            }
                        }
                    }
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
          alert('Error occurred');
          $("#mail_details,#mail_subject,#mail_to,#mail_cc,#mail_bcc,#mail_custom_to,#mail_custom_cc,#mail_custom_bcc,#mail_content").val('');
                $('.trumbowyg-editor').html('');
        }
      });
    });
    $('#submit').on('click',function(){
        var mail_details={};
        mail_details['mail_name']='';mail_details['mail_details']='';
        mail_details['subject']='';mail_details['to']=[];mail_details['cc']=[];
        mail_details['bcc']=[];mail_details['custom_to']='';
        mail_details['custom_cc']='';mail_details['custom_bcc']='';mail_details['body']=''
        error_count=0;
        $("#mail_configuration").find("input,select,textarea").each(function(){
            var name=$(this).attr('name');
            var value=$(this).val();
            if(($.inArray(name,['mail_name','subject','mail_details','to','cc','body']))>-1){
                if(!$.trim(value)){
                    if($(this).hasClass('select-plugin')){
                        $(this).parents('td').find('.select2-container').addClass('has_error');
                    }
                    else if($(this).hasClass('trumbowyg-textarea')){
                        $(this).parents('td').find('.trumbowyg-box').addClass('has_error');
                    }
                    else
                    $(this).addClass('has_error');
                    error_count=error_count+1;
                }
            }
            if(mail_details[name]!=undefined)
                mail_details[name]=value;
        });
        console.log(mail_details);
        if(!error_count){
            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });
            $.ajax({
                type:'POST',
                url:'/save_mail_details',
                data:{mail_details:mail_details},          
                dataType:'json',
                success:function(data,textStatus, jqXHR){ 
                    if(data.ERR)
                        alert("Error while saving the details.")
                    else if(data.SUCC){
                        alert("Data saved successfully");
                        location.reload();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                
                }
            });
        }
    });
</script>
@endsection