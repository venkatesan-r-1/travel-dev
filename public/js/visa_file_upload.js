//Configuration for file uploads - US visa process
const maxFileCount = 5;
const maxFileSize = 2000000;
const allowedFormats = {
    "passport-file" : ["pdf","png","jpg","jpeg","docx"],
    "cv-file" : ["docx"],
    "degree-file" : ["pdf","png","jpg","jpeg","docx"],
    "petition-file" : ["pdf","png","jpg","jpeg","docx"],
    "visa-file" : ["pdf","png","jpg","jpeg","docx"]
}

jQuery(function () {
    if($('.file-uploads-div').children().length == 0)
        $('.file-uploads-div').empty();

    $('.file-uploads-div').find('.file-size').each(function () {
        let sizeInBytes = $(this).html();
        $(this).html(getSize(parseInt(sizeInBytes)));
    });

    $('.file-uploads-div').on('click',function (event) {
        event.stopPropagation();
    });
    
    //Upload the file to server
    $('.custom-file-input').each(function () {
        $(this).on('input',function (event) {
            var isValid = validateFile(event.target.files[0],event.target.id);
            // isValid.result = true;
            if(isValid.result)
                uploadFiles($("input[name='edit_id']").val(),event.target.files[0],event.target.id);
            else{
                $('.alert-danger').show().html(isValid.message).append(`<span class="danger-close-btn">&times;</span>`);
                $('.danger-close-btn').on('click', function () { $(this).parent().hide(); });
                setTimeout(()=>$('.alert-danger').hide(),10000,);
            }
        })
    })
    //To delete the files...
    $('.remove-files').bind('click',deleteFile);
});

function validateFile(file,type)
{
    if(!allowedFormats[type].includes(file.name.split('.').pop()))
        return {"result" : false, message : "Please upload the valid file formats as mentioned in info"};
    if(file.size > maxFileSize)
       return {"result" : false, message : "Please upload the file within 2MB"};
    if($('#'+type+'-uploads-div').children().length >= maxFileCount)
        return {"result" : false, message : "Maximum file upload count reached"};
    return {"result" : true};
}

function uploadFiles(edit_id,file,type)
{
    var formData = new FormData();
    formData.append('edit_id',edit_id);
    formData.append('file',file);

    $.ajaxSetup({
        headers : {
            'X-CSRF-TOKEN' : $("meta[name='csrf-token']").attr('content'),
        }
    });

    $.ajax({
        url : "/uploadFiles",
        method : "POST",
        dataType : "JSON",
        data : formData,
        processData : false,
        contentType : false,
        success : function (response){
            displayFileDetails(response,type);
        },
        error : function (response) {
            alert("Error occured in file upload");
        }
    })
}

function displayFileDetails(response,type)
{
    if(!response)
        return;

    let hiddenValue = $('#'+type+'-hidden').val() == "" ? [] : $('#'+type+'-hidden').val().split(',');
    hiddenValue.push(response.tempName);
    $('#'+type+'-hidden').val(hiddenValue.toString());
    let fileCount = $('#'+type+'-uploads-div').children().length + 1;
    
    //if($('#'+type+'-uploads-div').children().length > 0)
        //$('#'+type+'-uploads-div .file-content:last').clone(true).insertAfter("#"+type+"-uploads-div .file-content:last");
    //else{
    $('#'+type+'-uploads-div').append(`<div class="file-content"><a id="${type}-name-${fileCount}" class="file-name" target="_blank"></a><span class="remove-files ${type.split("-").join("-remove-")}s">&times;</span><span class="file-size"></span></div>`);
    $('.remove-files').bind('click',deleteFile);
    //}
        
    $('#'+type+"-info").val(fileCount == 1 ? response.fileName : `${fileCount} files uploaded`);
    $('#'+type+"-uploads-div").find('.file-content .file-name:last').prop("textContent",response.fileName);
    $('#'+type+"-uploads-div").find('.file-content .file-name:last').attr("id",type+"-name-"+fileCount);
    $('#'+type+"-uploads-div").find('.file-content .file-name:last').attr("href",response.filePath);
    //$('#'+type+"-uploads-div").find('.file-content .file-name:last').attr("data-original-title",response.fileName);
    $('#'+type+"-uploads-div").find('.file-content .file-size:last').prop("textContent",getSize(response.fileSize));
    $('#'+type+"-uploads-div").find('.file-content .file-name:last').attr("title",response.fileName);
    $('#'+type+"-uploads-div").find('.file-content .file-name:last').prop("textContent",response.fileName);

    $('.file-name').tooltip();
}

function getSize(fileOrSize)
{
    let size = (Number.isInteger(fileOrSize)) ? fileOrSize : fileOrSize.size;
    if(!size)
        return "0 B";
    const units = ["B","KB","MB","GB","TB","PB","EB","ZB","YB","RB","QB"];
    const exponent = Math.min(
        Math.floor(Math.log(size)/Math.log(1000)),
        units.length-1
    );
    const approx = size/1000 ** exponent;
    return (exponent === 0)? `${size} B` : `${approx.toFixed(1)} ${units[exponent]}`;
}

function deleteFile(event)
{
    if($(this).hasClass('passport-remove-files'))
        type="passport-file";
    if($(this).hasClass('cv-remove-files'))
        type="cv-file";
    if($(this).hasClass('degree-remove-files'))
        type="degree-file"; 
    if($(this).hasClass('petition-remove-files'))
        type="petition-file";
    if($(this).hasClass('visa-remove-files'))
        type="visa-file";

    let index = $('#'+type+'-uploads-div .remove-files').index($(this)[0]);
    if(index == -1)
        return;
    let filePath = $('#'+type+'-uploads-div .file-name').eq(index).attr('href');
    let fileName = filePath.split("/").reverse()[0];
    
    $.ajaxSetup({
        headers : {
            "X-CSRF-TOKEN" : $("meta[name='csrf-token']").attr("content"),
        },
    });

    $.ajax({
        url : "/deleteFile",
        method : "POST",
        dataType : "JSON",
        data : {"filePath":filePath,"edit_id":$("input[name='edit_id']").val(),'type':type},
        success : function (response) {
            
        },
        error : function (response) {

        },
    });

    let hiddenValue = $('#'+type+'-hidden').val() == "" ? [] : $('#'+type+'-hidden').val().split(',');
    hiddenValue = hiddenValue.filter((e) => e!=fileName).toString();
    $('#'+type+'-hidden').val(hiddenValue);
    $('#'+type+'-uploads-div').find('.file-content').eq(index).remove();
    if($('#'+type+'-uploads-div').children().length == 0)
        $('#'+type+'-uploads-div').empty();
    let fileCount = $('#'+type+'-uploads-div').children().length;
    if(fileCount === 0)
        $('#'+type+'-uploads-div').parent().removeClass('open');
    $('#'+type+'-info').val(fileCount != 0 ? (fileCount == 1 ? $('#'+type+'-uploads-div .file-name:first').prop('textContent') : `${fileCount} files uploaded`) : null);
}
