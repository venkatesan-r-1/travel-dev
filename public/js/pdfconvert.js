window.jsPDF = window.jspdf.jsPDF;

window.onload  = () => {
    window.img = document.querySelector('.pdf-watermark');
}

$(document).on('click','#generate',function(){
    $('.ui-wait').show();
    // For generating word document
    var formData = new FormData();
    var details = {};
    window.data = formData;
    if($('#green_card_title').is(':checked'))
        $('#green_card_title').val(1);
    else
        $('#green_card_title').val(0);
    $('.word_document').each(function(){
        var property = $(this).attr('id');
        var value = $(this).prop('textContent');
        switch(property)
        {
            case "doc_date_creation" : details['date_creation'] = value; break;
            case "doc_firstname": details['firstname'] = value; break;
            case "doc_lastname": details['lastname'] = value; break;
            case "doc_aceid": details['aceid'] = value; break;
            case "doc_job_title": details['job_title'] = value; break;
            case "doc_start_date": details['start_date'] = value; break;
            case "doc_us_salary": details['us_salary'] = value; break;
            case "doc_entity" : details['entity'] = value; break;
            case "doc_us_hr": details["us_hr"] = value; break;
            case "doc_us_hr_designation": details["us_hr_designation"] = value; break;
            case "doc_us_hr_department" : details["us_hr_department"] = value; break;
            case "doc_us_hr_mail" : details["us_hr_mail"] = value; break;
            case "doc_additional_point":  details["additional_point"] = value; break;
            case "doc_salary_notation": details["currency_notation"] = value; break;
            case "offer_letter_template" : details["offer_letter_template"] = value; break;
        }
        details['green_card'] = $('#green_card_title').val() == 1 ? 1 : 0;
    });
    
    generateWordDocument(details);

    $('.pdf').each(function () {$(this).show(); });
    $('.pdf-header').each(function () { $(this).show(); })
    $('.pdf-footer').each(function () { $(this).show(); })
    $('.pdf-watermark').each(function () { $(this).show(); } )
    $('.actual').each(function () {
        $(this).css('display','inline');
    })
    if($('#green_card_title').val() == 1)
    {
        $('.pdf .optional').css('display','list-item');
    }
    var emloyeee_name = $('#employee_name').val().slice(0,3);
    var date_of_joining = $('#doj').val().replaceAll('-','');
    var password = (emloyeee_name + date_of_joining).toLowerCase();
    var doc = new jsPDF({
        orientation: 'p',
        unit: 'pt',
        format: 'a4',
        encryption: {
            userPassword: password,
            ownerPassword: password,
            userPermissions: ['print','copy'],
        }
    });
    createDocument();
	manageSpace();
	moveFooter();
    var element = getElement();
    $('.pdf').each(function () {$(this).hide(); });
    $('.pdf-header').each(function () { $(this).hide(); })
    $('.pdf-footer').each(function () { $(this).hide(); })
    $('.pdf-watermark').each(function () { $(this).hide(); } )
    element = `<div style='background-color: #F8F8F8;width:595.28px;font-size:11px;font-family: sans-serif; white-spacec:normal !important;'>${element}</div>`;

    doc.html(element,{
    html2canvas:{
        scale: 1,
    },
    callback: function (doc){
            var pageCount = doc.internal.getNumberOfPages();
            if(pageCount == 4)
                doc.deletePage(pageCount);

            for(page=1;page<=pageCount;page++){
                doc.setPage(page);
                var xCord = (doc.internal.pageSize.getWidth()/2) - (316)/2;
                var yCord = (doc.internal.pageSize.getHeight()/2) - (359)/2;    
                doc.addImage(window.img,'PNG',xCord,yCord,316,359);
            }
            var blob = doc.output('blob');
            var id = $("input[name='edit_id']").val();
            var aceid = $('#pdf-aceid').html();
            var filename = `${id}_${aceid}_offer_letter.pdf`;
            window.data.append('request_code',$("input[name = 'edit_id']").val());
            window.data.append('green_card_title',$('#green_card_title').val());
            window.data.append('pdf', blob);
            window.data.append('filename',filename);
            generateOfferLetter(formData);
    }})
})

function generateOfferLetter(formData)
{
    $('.pdf').each(function () {$(this).show(); });
    $('.pdf-header').each(function () { $(this).show(); })
    $('.pdf-footer').each(function () { $(this).show(); })
    $('.pdf-watermark').each(function () { $(this).show(); } )
    $('.actual').each(function () {
        $(this).hide();
    })
    $('.immigration').each(function () {
        $(this).css('display','inline');
    })
    var emloyeee_name = $('#employee_name').val().slice(0,3);
    var date_of_joining = $('#doj').val().replaceAll('-','');
    var password = (emloyeee_name + date_of_joining).toLowerCase();
    var doc = new jsPDF({
        orientation: 'p',
        unit: 'pt',
        format: 'a4',
        /* encryption: {
            userPassword: password,
            ownerPassword: password,
            userPermissions: ['print','copy'],
        }*/
    });
    var element = getElement();
    $('.pdf').each(function () {$(this).hide(); });
    $('.pdf-header').each(function () { $(this).hide(); })
    $('.pdf-footer').each(function () { $(this).hide(); })
    $('.pdf-watermark').each(function () { $(this).hide(); } )
    element = `<div style='background-color: #F8F8F8;width:595.28px;font-size:11px;font-family: sans-serif;'>${element}</div>`;

    doc.html(element, {
        html2canvas: {
            scale: 1,
        },
        callback: function(doc){
            var pageCount = doc.internal.getNumberOfPages();
            if(pageCount == 4)
                doc.deletePage(pageCount);

            for(page=1;page<=pageCount;page++){
                doc.setPage(page);
                var xCord = (doc.internal.pageSize.getWidth()/2) - (316)/2;
                var yCord = (doc.internal.pageSize.getHeight()/2) - (359)/2;
                doc.addImage(window.img,'PNG',xCord,yCord,316,359);
            }
            var blob = doc.output('blob');
            var id = $("input[name='edit_id']").val();
            var aceid = $('#pdf-aceid').html();
            var filename = `${id}_${aceid}_immigration_offer_letter.pdf`;

            window.data.append('immigration_offer_letter', blob);
            window.data.append('immigration_filename',filename);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url:"/visa_process/uploadofferletter",
                method:"POST",
                dataType:'json',
                data: window.data,
                processData: false,
                contentType: false,
                success:function (response){
                    window.location.reload();
                },
                error: function (){
                    alert("Error occured");
                }
            });
        }
    })
}

window.generateWordDocument = function generateWordDocument(details) {
    loadFile(
        details['offer_letter_template'],
        function (error, content) {
            if (error) {
                throw error;
            }
            var zip = new PizZip(content);
            var doc = new window.docxtemplater(zip, {
                paragraphLoop: true,
                linebreaks: true,
            });

            doc.render({
                date_creation: details['date_creation'],
                firstname: details['firstname'],
                lastname: details['lastname'],
                aceid: details['aceid'],
                job_title: details['job_title'],
                start_date: details['start_date'],
                us_salary: details['us_salary'],
                entity: details['entity'],
                us_hr: details['us_hr'],
                us_hr_designation: details['us_hr_designation'],
                us_hr_department: details['us_hr_department'],
                us_hr_mail: details['us_hr_mail'],
                is_green_card_title_holder: details['green_card'],
                additional_content: details['additional_point'],
                currency_notation: details['currency_notation'],
            });

            var blob = doc.getZip().generate({
                type: "blob",
                mimeType: "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                compression: "DEFLATE",
            });
            var id = $("input[name='edit_id']").val();
            var aceid = $('#pdf-aceid').html();
            var filename = `${id}_${aceid}_offer_letter.docx`;
            window.data.append('word',blob);
            window.data.append('word_filename',filename);
        }
    );
};

function loadFile(url, callback) {
    PizZipUtils.getBinaryContent(url, callback);
}

function createDocument(){
    var pdf = document.querySelectorAll('.pdf');
    for(page of pdf){ addHeader(page); }
}

function addHeader(element){
    element.prepend(document.querySelector('.pdf-header').cloneNode(true));
    element.append(document.querySelector('.pdf-footer').cloneNode(true));
}

function manageSpace(element=null){
    if(!element)
        element = document.querySelector('.pdf');
    var height = element.clientHeight;
    if(!element.classList.contains('pdf'))
        return;
    if(height > 841.89){
        try{
            height = moveLine(element);
        }
        catch(err){
            alert(err.message);
            $('.ui-wait').hide();
        }
    }
    return manageSpace(element.nextElementSibling);
}

function moveLine(element){
   var lastSection = [...document.querySelectorAll('.pdf-section')].at(-1);
   var lastParagraph = [...lastSection.querySelectorAll('.pdf-paragraph')].at(-1);
   var lastLine = lastParagraph.lastElementChild;
   if(!element.nextElementSibling.classList.contains('pdf'))
    addNew(element);
    var nextPage = element.nextElementSibling;
    var firstSection = nextPage.querySelector('.pdf-section');
    var firstParagraph = firstSection.querySelector('.pdf-paragraph');
    if(element.clientHeight <= 841)
        return element.clientHeight;    
    else{
        firstParagraph.append(lastLine);
        return moveLine(element)
    }
}

function addNew(element){
    var newPage = document.createElement('div');
    newPage.setAttribute('class','pdf');
    var newSection = document.createElement('div');
    newSection.setAttribute('class','pdf-section');
    var newParagraph = document.createElement('div');
    newParagraph.setAttribute('class','pdf-paragraph');
    newSection.append(newParagraph);
    newPage.append(newSection);
    addHeader(newPage);
    element.parentElement.insertBefore(newPage,element.nextElementSibling);
}

function getElement(){
    var pdf = document.querySelectorAll('.pdf');
    var html = "";
    for(page of pdf)
        html += page.outerHTML;
    return html.toString();
}

function moveFooter(){
    for(page of document.querySelectorAll('.pdf')){
        if(page.clientHeight < 841.89){
            var top = 841.89 - page.clientHeight;
            page.querySelector('.pdf-footer').style.marginTop = `${top}px`;
        }
    }
}
