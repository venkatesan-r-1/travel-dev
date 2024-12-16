$(document).ready(function () {
    const screenSize = window.innerWidth;
    if( screenSize <= 600 ) {
        const proofTable = $('#proof-details-table');
        if(proofTable.length) {
            const lableNames = ['Proof type', 'Request for', 'File upload', 'Number', 'Name as per proof', 'Issued date', 'Expiry date', 'Place of issue'];
            lableNames.forEach( (value, index) => { proofTable.find('tbody tr td').eq(index).prepend(`<label>${value}</label>`) } )
            proofTable.find('tbody').prepend(`<div class="mob-panel expand">Proof details</div>`);
        }
    }
});

$(document).on('click', '.mob-panel', function () {
    $(this).next().slideToggle('slow');
    $(this).toggleClass('expand');
});
