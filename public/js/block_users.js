$(document).ready(function () {
    var config = getConfig();
    getDetails(config);
});

function getConfig()
{
    return {
        url : 'get_block_users',
        method: 'post',
        data: {'active_alone': 1},
        dataType: 'json',
        async: false,
    };
}

// To get the user details
function getDetails(config)
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    });
    $.ajax({
        url: config.url,
        method: config.method,
        data: config.data,
        dataType: config.dataType,
        async: config.async || false,
        success: function (response) {
            createTable(response);
        },
        error: function (response) {
            alert("Error occured");
        }
    })
}

// To visualize the gather user details in datatable
function createTable(response)
{
    // create table for blockable users
    if(response.can_block) {
        var config = {
            data: response.can_block.data,
            columns: response.can_block.columns,
        };
        var blockable_table = createDataTable('block_users', config);
        $('#block-user-search').on('input', function () {
            blockable_table.search($(this).val()).draw();
        })
    }
    // Create table for blocked users
    if(response.blocked) {
        var config = {
            data: response.blocked.data,
            columns: response.blocked.columns,
        };
        var blocked_table = createDataTable('blocked_users', config);
        $('#unblock-user-search').on('input', function () {
            blocked_table.search($(this).val()).draw();
        })
    }
}

// To add datatable
function createDataTable(tableID, config)
{
    $('#'+tableID).empty();
    var table = $('#'+tableID).DataTable({
        dom:'Bfrtlip',
        searching: true,
        paging: config.paging ?? true,
        pageLength: config.pageLength ?? 10,
        pagingType: config.pagingType ?? "full_numbers",
        bDestroy: true,
        bLengthChange: false,
        bInfo: false,
        scrollY: "300px",
        scrollCollapse: true,
        oLanguage: {sSearch: "", sSearchPlaceholder: "", sEmptyTable: "No record found",
        "oPaginate":{"sNext":">","sPrevious":"<","sFirst":"<<","sLast":">>"}},
        data: config.data,
        columns: config.columns,
        rowCallback: function (row, data) { // To add action buttons
            var cell = $('td.action',row);
            var value = cell.text();
            if(cell.has('button').length === 0) {
                var btn = tableID === "block_users" ? `<button class="block-user">Block user</button>` : `<button class="unblock-user">Unblock user</button>`
                cell.html(btn);
                cell.find('button').data('aceid', value);
            }
        },
        initComplete: function () { // To add slim scroll
            $('.dataTables_scrollBody').slimscroll({
                    height:'350px',
                    width: '100%',
                    distance: '-0.9px',
                    railVisible: true,
                    alwaysVisible: false,
                    axis: 'both',
                    right: '1.1px',
                    wheelStep: 750,
                    touchScrollStep: 750,
                });
        },
        drawCallback: function () { // To toggle the pagination section based on number of entries
            var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
            pagination.toggle(this.api().page.info().pages > 1);
        }
    });
    return table;
}

// To block a user
$(document).on('click', '.block-user', function () {
    var data = { 'aceid' : $(this).data('aceid') };
    var options = {
        content: 'Are you sure want to add this user to block list?',
        primaryBtn: 'Block',
        secondaryBtn: 'Cancel',
        primaryAction: blockUser,
        primaryActionParams: [data, "block"],
    }
    openConfirmationBox(options);
});

// To unblock a user
$(document).on('click', '.unblock-user', function () {
    var data = { 'aceid' : $(this).data('aceid') };
    var options = {
        content: 'Are you sure want to remove the user from block list?',
        primaryBtn: 'Unblock',
        secondaryBtn: 'Cancel',
        primaryAction: blockUser,
        primaryActionParams: [data, "unblock"],
    }
    openConfirmationBox(options);
});

// To block/unblock on button click
function blockUser(data, type)
{
    var url = type === "block" ? "/block_user"  : "/unblock_user";
    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    });
    $.ajax({
        url: url,
        method: 'post',
        data: data,
        dataType: 'json',
        async: false,
        success: function (response) {
            $('.alert-success').show().html(response.message+`<span class="alert_close"><img title="Close alert" src="/images/close-green.svg" class=""></span>`);
            setTimeout( () => $('.alert-success').hide(), 10000 );
            var config = getConfig();
            getDetails(config);  
        },
        error: function () {
            alert("Error occured");
        }
    })
}

// To show/hide the loading gif on every action
$(document).ajaxStart(function () {
    $('.ui-wait').show();
});

$(document).ajaxStop(function () {
    // setTimeout(() => $('.ui-wait').hide(), 500);
    $('.ui-wait').hide();
});
