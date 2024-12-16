class FileUpload
{
    #config = {
        maxSize: 5242880,
        maxCount: 5,
        allowedFormats: ['png', 'jpg', 'jpeg', 'pdf'],
        multiple: false,
        customMessage: {
            'size': 'Max size exceeds',
            'count': 'Max count reached',
            'extension': 'Not allowed file extension',
        }
    }

    constructor (settings) {
        this.#config = { ...this.#config, ...settings };
    }

    uploadFile(element)
    {
        var details = this.fetchDetails(element);
        var validate = this.validateFile(details);
        if(validate.isValid)
        {
            let formData = new FormData();
            formData.append('file', details.file);
            formData.append('type', details.fileType);
            formData.append('module', $('#travel_request_for').val());
            formData.append('requestFor', details.requestFor);
            this.saveFile(formData, details.element);
        }
        else
        {
            let failedConditions = validate.failedConditions;
            element.closest('.file-wrapper').find('.upload-icon').removeClass('uploaded');
            $('.alert-danger').show().empty().append('<ul></ul><span class="alert_close"><img src="/img/close-red.svg" title="Close alert" /></span>');
            for(let condition of failedConditions)
                $('.alert-danger ul').append(`<li>${this.#config.customMessage[condition]}</li>`);
            setTimeout(()=> $('.alert-danger').hide(), 10000);
        }
    }

    fetchDetails(element)
    {
        var file = element.prop('files')[0];
        var fileName = file.name;
        var requestFor = element.closest('tr').find("[name='proof_request_for']").val();
        var fileType = element.closest('tr').find("[name='proof_type']").val();
        fileType = fileType ? fileType : element.attr('name');
        return {
            element: element,
            file: file,
            fileName: fileName,
            fileSize: file.size,
            fileCount: element.attr('count'),
            fileExenstion: this.getExtension(fileName),
            fileType: fileType ? fileType : null,
            requestFor: requestFor ? requestFor : null,
            edit_id: $('#edit_id').val(),
        };
    }

    validateFile(details)
    {
        console.log(details.fileCount, this.#config.maxCount);
        let failedConditions = [];
        if(details.fileSize > this.#config.maxSize)
            failedConditions.push('size');
        if(details.fileCount > this.#config.maxCount)
            failedConditions.push('size');
        if(!this.#config.allowedFormats.includes(details.fileExenstion))
            failedConditions.push('extension');
        if(failedConditions.length)
            return {isValid: false, failedConditions: failedConditions};
        return {isValid: true};
    }

    saveFile(data, element)
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/save_uploaded_file',
            method: 'post',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: (response) => this.display(response, element),
            error: () => {
                $('.alert-danger').show().find('.message').html('Error occured while uploading the file');
                setTimeout(()=>$('.alert-danger').hide(), 10000);
            }
        });
    }

    deleteFile(data, element)
    {
        console.log(element);
        // return;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/delete_uploaded_file',
            method: 'post',
            data: data,
            dataType: 'json',
            asycn: false,
            success: (response) => {
                let existingFiles = element.closest('.file-wrapper').find("input[type='hidden']").val();
                let updatedFileList = null;
                if(existingFiles)
                {
                    updatedFileList = existingFiles.split(',');
                    updatedFileList.splice(updatedFileList.indexOf(response.file_name),1);
                    element.closest('.file-wrapper').find("input[type='hidden']").val(updatedFileList.join(','));
                }
                let count = element.closest('.file-wrapper').find('.custom-file-upload').attr('count')-1;
                element.closest('.file-wrapper').find('.custom-file-upload').attr('count',count).removeAttr('disabled');
                element.closest('.file-wrapper').find('.file-info .info').text( count ? count+" files selected" : "" );
                if(count == 0)
                    element.closest('.file-upload-container').removeClass('show');
                element.closest('.file-wrapper').find('.upload-icon').removeClass('uploaded');
                element.closest('.row-item').remove();
                return;
            },
            error: () => {
                $('.alert-danger').show().find('.message').html('Error occured while deleting the file');
                setTimeout(()=>$('.alert-danger').hide(), 10000);
            }
        });
    }

    display(response, element)
    {
        if(!this.#config.multiple || ( parseInt(element.attr('count')) + 1 == this.#config.maxCount )) {
            element.closest('.file-wrapper').find('.upload-icon').addClass('uploaded');
            element.attr('disabled', true);
        }
        // element.closest('.file-wrapper').find('.upload-icon').addClass('uploaded');
        if(!element.attr('count')) {
            element.attr('count',0);
            if(element.closest('.file-wrapper').find('.file-info').length == 0) {
                element.closest('.file-wrapper').find('.upload-icon').wrap("<div class='file-info'></div>");
                element.closest('.file-wrapper').find('.upload-icon').closest('.file-info').prepend("<span class='info'></span>");
            }        
        }
            
        let count = parseInt(element.attr('count'))+1;
        element.attr('count',count);
        element.closest('.file-wrapper').find('.file-info .info').text(`${count} files selected`);
        if(!element.closest('.file-wrapper').find('.file-upload-container').length)
            element.closest('.file-wrapper').append("<div class='file-upload-container'></div>");
        if(!element.closest('.file-wrapper').find('.file-upload-container').children().length){
            element.closest('.file-wrapper').find('.file-upload-container').append("<div class='row-item'></div>");
            element.closest('.file-wrapper').find('.file-upload-container .row-item').append(`<div class='file-name'><a href="/${response.file_path}" download="${response.file_name}" target='_blank'>${response.file_name}</div>`);
            element.closest('.file-wrapper').find('.file-upload-container .row-item').append(`<div class='file-action'><span class='file-size'>${this.getSize(response.file_size)}</span><img src='../images/close.svg' class='file-remove'></div>`)
        }
        else{
            let clonedElement = element.closest('.file-wrapper').find('.file-upload-container .row-item:last').clone(true);
            element.closest('.file-wrapper').find('.file-upload-container').append(clonedElement);
        }
        element.closest('.file-wrapper').find('.file-upload-container .row-item:last .file-name a').html(response.file_name);
        element.closest('.file-wrapper').find('.file-upload-container .row-item:last .file-name a').attr('/'+response.file_path);
        element.closest('.file-wrapper').find('.file-upload-container .row-item:last .file-action .file-size').html(this.getSize(response.file_size));
        let existingFiles = element.next().val();
        let updatedFileList = existingFiles ? existingFiles+','+response.name : response.name;
        element.next().val(updatedFileList);
    }

    getSize(fileOrSize)
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

    getExtension = (fileName) => fileName.slice(fileName.lastIndexOf('.')+1);
}

$(document).ready(function () {
    $(document).find('.upload-icon').each( function () {
        if(!$(this).parent().hasClass('file-info'))
            {
                $(this).wrap("<div class='file-info'></div>");
                $(this).closest('.file-info').prepend("<span class='info'></span>");
                let count = $(this).closest('.file-wrapper').find('.file-upload-container').children().length;
                $(this).closest('.custom-file-upload').attr('count',count);
            }
            else
            {
                let count = $(this).closest('.file-wrapper').find('.file-upload-container').children().length;
                $(this).closest('.file-wrapper').find('.custom-file-upload').attr('count',count);
                $('.file-upload-container').find('.file-size').each(function () {
                    let size = parseInt($(this).text());
                    $(this).text( getSize(size) );
                })
            }
    } );
  });

  function uploadFile(element, config=null)
  {
    const file = new FileUpload(config);
    file.uploadFile(element);
    delete file;
  }

  function deleteFile(element, data)
  {
    const file = new FileUpload();
    file.deleteFile(element, data);
    delete file;
  }

  function display(element, data)
  {
    const file = new FileUpload();
    file.display(element, data);
    delete file;
  }

  function getSize(size) {
    const file = new FileUpload();
    const fileSize = file.getSize(size);
    return fileSize;
  }
