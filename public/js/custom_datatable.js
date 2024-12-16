$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip();
	$(".date").datepicker({
		format: 'dd-M-yyyy',
        autoclose: true,
	})
	var filters = [
		{ columnIndex: 2, filterId: "request_type_filter" ,type:"select"},
		{ columnIndex: 3, filterId: "employee_details_filter",type:"select" },
		{ columnIndex: 4, filterId: "travel" ,type:"date"},
		{ columnIndex: 5, filterId: "travel" ,type:"date"},
		{ columnIndex: 7, filterId: "department_filter",type:"select" },
		{ columnIndex: 8, filterId: "project_filter",type:"select" },
		{ columnIndex: 12, filterId: "customer_filter",type:"select" },
		{ columnIndex: 11, filterId: "delivery_unit_filter",type:"select" },
		{ columnIndex: 10, filterId: "status_filter",type:"select" }
	];

	var request_table=$("#traveler_home_details").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
		"sEmptyTable":     "No records found",
        "oPaginate":{
			"sNext":"<img src='/images/previous-arrow.svg'>",
			"sPrevious":"<img src='/images/next-arrow.svg'>",
			"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
			"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#traveler_home_details_paginate').hide();
		$('#traveler_home_details_length').hide();
		}
		else{
		$('#traveler_home_details_paginate').show();
		$('#traveler_home_details_length').show();
		}
	},
	});
	
	$("#traveler_home_details_filter").on('keyup',function(){
		request_table.search($(this).val()).draw();
	});
    var review_tocheck_table=$("#review_tocheck_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		 pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{
			"sNext":"<img src='/images/previous-arrow.svg'>",
			"sPrevious":"<img src='/images/next-arrow.svg'>",
			"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
			"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#review_tocheck_table_paginate').hide();
		$('#review_tocheck_table_length').hide();
		}
		else{
		$('#review_tocheck_table_paginate').show();
		$('#review_tocheck_table_length').show();
		}
	},
	});
	$("#review_table_filter").on('keyup',function(){
		review_tocheck_table.search($(this).val()).draw();
	});
    var review_checked_table=$("#review_checked_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{
			"sNext":"<img src='/images/previous-arrow.svg'>",
			"sPrevious":"<img src='/images/next-arrow.svg'>",
			"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
			"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#review_checked_table_paginate').hide();
		$('#review_checked_table_length').hide();
		}
		else{
		$('#review_tocheck_table_paginate').show();
		$('#review_tocheck_table_length').show();
		}
	},
	});
	$("#review_table_filter").on('keyup',function(){
		review_checked_table.search($(this).val()).draw();
	});
	var approve_tocheck_table=$("#approve_tocheck_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{
			"sNext":"<img src='/images/previous-arrow.svg'>",
			"sPrevious":"<img src='/images/next-arrow.svg'>",
			"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
			"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#approve_tocheck_table_paginate').hide();
		$('#approve_tocheck_table_length').hide();
		}
		else{
		$('#approve_tocheck_table_paginate').show();
		$('#approve_tocheck_table_length').show();
		}
	},
	});
	$("#approve_table_filter").on('keyup',function(){
		approve_tocheck_table.search($(this).val()).draw();
	});
	
    var approve_checked_table=$("#approve_checked_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{
			"sNext":"<img src='/images/previous-arrow.svg'>",
			"sPrevious":"<img src='/images/next-arrow.svg'>",
			"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
			"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#approve_checked_table_paginate').hide();
		$('#approve_checked_table_length').hide();
		}
		else{
		$('#approve_checked_table_paginate').show();
		$('#approve_checked_table_length').show();
		}
	},
	});
	$("#approve_table_filter").on('keyup',function(){
		approve_checked_table.search($(this).val()).draw();
	});
	var travel_desk_tocheck_table=$("#travel_desk_tocheck_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{"sNext":"<img src='/images/previous-arrow.svg'>",
		"sPrevious":"<img src='/images/next-arrow.svg'>",
		"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
		"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#travel_desk_tocheck_table_paginate').hide();
		$('#travel_desk_tocheck_table_length').hide();
		}
		else{
		$('#travel_desk_tocheck_table_paginate').show();
		$('#travel_desk_tocheck_table_length').show();
		}
	},
	});
	$("#travel_desk_table_filter").on('keyup',function(){
		travel_desk_tocheck_table.search($(this).val()).draw();
	});
    var travel_desk_checked_table=$("#travel_desk_checked_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{"sNext":"<img src='/images/previous-arrow.svg'>",
		"sPrevious":"<img src='/images/next-arrow.svg'>",
		"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
		"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#travel_desk_checked_table_paginate').hide();
		$('#travel_desk_checked_table_length').hide();
		}
		else{
		$('#travel_desk_checked_table_paginate').show();
		$('#travel_desk_checked_table_length').show();
		}
	},
	});
	$("#travel_desk_table_filter").on('keyup',function(){
		travel_desk_checked_table.search($(this).val()).draw();
	});
	var hr_review_tocheck_table=$("#hr_review_tocheck_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{"sNext":"<img src='/images/previous-arrow.svg'>",
		"sPrevious":"<img src='/images/next-arrow.svg'>",
		"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
		"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#hr_review_tocheck_table_paginate').hide();
		$('#hr_review_tocheck_table_length').hide();
		}
		else{
		$('#hr_review_tocheck_table_paginate').show();
		$('#hr_review_tocheck_table_length').show();
		}
	},
	});
	$("#hr_review_table_filter").on('keyup',function(){
		hr_review_tocheck_table.search($(this).val()).draw();
	});
    var hr_review_checked_table=$("#hr_review_checked_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{"sNext":"<img src='/images/previous-arrow.svg'>",
		"sPrevious":"<img src='/images/next-arrow.svg'>",
		"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
		"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#hr_review_checked_table_paginate').hide();
		$('#hr_review_checked_table_length').hide();
		}
		else{
		$('#hr_review_checked_table_paginate').show();
		$('#hr_review_checked_table_length').show();
		}
	},
	});
	$("#hr_review_table_filter").on('keyup',function(){
		hr_review_checked_table.search($(this).val()).draw();
	});
	var gm_review_tocheck_table=$("#gm_review_tocheck_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{"sNext":"<img src='/images/previous-arrow.svg'>",
		"sPrevious":"<img src='/images/next-arrow.svg'>",
		"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
		"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#gm_review_tocheck_table_paginate').hide();
		$('#gm_review_tocheck_table_length').hide();
		}
		else{
		$('#gm_review_tocheck_table_paginate').show();
		$('#gm_review_tocheck_table_length').show();
		}
	},
	});
	$("#gm_review_table_filter").on('keyup',function(){
		gm_review_tocheck_table.search($(this).val()).draw();
	});
    var gm_review_checked_table=$("#gm_review_checked_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{"sNext":"<img src='/images/previous-arrow.svg'>",
		"sPrevious":"<img src='/images/next-arrow.svg'>",
		"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
		"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#gm_review_checked_table_paginate').hide();
		$('#gm_review_checked_table_length').hide();
		}
		else{
		$('#gm_review_checked_table_paginate').show();
		$('#gm_review_checked_table_length').show();
		}
	},
	});
	$("#gm_review_table_filter").on('keyup',function(){
		gm_review_checked_table.search($(this).val()).draw();
	});

	var workbench_tocheck_table=$("#workbench_tocheck_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{
			"sNext":"<img src='/images/previous-arrow.svg'>",
			"sPrevious":"<img src='/images/next-arrow.svg'>",
			"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
			"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#workbench_tocheck_table_paginate').hide();
		$('#workbench_tocheck_table_length').hide();
		}
		else{
		$('#workbench_tocheck_table_paginate').show();
		$('#workbench_tocheck_table_length').show();
		}
	},
	});
	$("#workbench_table_filter").on('keyup',function(){
		workbench_tocheck_table.search($(this).val()).draw();
	});
    var workbench_checked_table=$("#workbench_checked_table").DataTable({
		dom:'Bfrtlip',
		"paging":   true,
		"pageLength":10,
		pagingType: 'full_numbers',
		order:[],"oLanguage": {
        "sSearch":"","sSearchPlaceholder": "Search",
		"sEmptyTable":     "No records found",
        "oPaginate":{
			"sNext":"<img src='/images/previous-arrow.svg'>",
			"sPrevious":"<img src='/images/next-arrow.svg'>",
			"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
			"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
		if((this.api().page.info().pages) <= 0){
		$('#workbench_checked_table_paginate').hide();
		$('#workbench_checked_table_length').hide();
		}
		else{
		$('#workbench_checked_table_paginate').show();
		$('#workbench_checked_table_length').show();
		}
	},
	});
	$("#workbench_table_filter").on('keyup',function(){
		workbench_checked_table.search($(this).val()).draw();
	});

	
	var reportsTable1 = initializeDataTable("#reports_table", [1,2,3,4,5,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33],filters);
	var reportsTable2 = initializeDataTable("#visa_reports_table", [1,2,3,4,5,7,8,9,10,11,12,13,14,15],filters);

	handleSearch("#report_table_filter", reportsTable1);
	handleSearch("#visa_report_table_filter", reportsTable2);


	// var reports_table=$("#reports_table").DataTable({
	// 	dom:'Bfrtlip',
	// 	"paging":   true,
	// 	"pageLength":10,
	// 	pagingType: 'full_numbers',
	// 	order:[],"oLanguage": {
    //     "sSearch":"","sSearchPlaceholder": "Search",
	// 	"sEmptyTable":     "No records found",
    //     "oPaginate":{
	// 		"sNext":"<img src='/images/previous-arrow.svg'>",
	// 		"sPrevious":"<img src='/images/next-arrow.svg'>",
	// 		"sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
	// 		"sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
	// 	},
	// 	drawCallback: function(settings){
	// 		if((this.api().page.info().pages) <= 0){
	// 		$('#reports_table_paginate').hide();
	// 		$('#reports_table_length').hide();
	// 		}
	// 		else{
	// 		$('#reports_table_paginate').show();
	// 		$('#reports_table_length').show();
	// 		}
	// 	},
	// 	dom: 'Br<"#tableContainerDiv"t>l<"dpageClass"p>',
	// 	buttons: [{
	// 		text: 'EXPORT',                       
	// 		extend: 'excelHtml5',
	// 		exportOptions: {
	// 			columns:[1,2,3,4,5,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33],
	// 			modifier: {
	// 				selected: true
	// 			},
	// 			format: {
	// 				header: function (data, columnIdx) {
	// 					return data;
	// 				},
	// 				body: function (data, column, row) {
	// 				   return data.replace(/&amp;/g,'and');
	// 				}
	// 			}
	// 		},"pagingType":'simple_numbers',
	// 		footer: true,
	// 		customize: function (xlsx) {
	// 			var sheet = xlsx.xl.worksheets['sheet1.xml'];
	// 		}
	// 	}],
	// 	initComplete: function () {
	// 		var table=this.api();
	// 		$.each(filters, function (index, filter) {
	// 				table.columns([filter.columnIndex]).every(function () {
	// 				var column = this;
	// 				var select = $("#"+filter.filterId);
	// 				column.data().unique().sort().each(function (d, j) {
	// 					if (d !== "") { 
	// 					select.append('<option value="' + d + '">' + d + '</option>');
	// 					}
	// 				});
	// 			$("#"+filter.filterId).multiselect({placeholder: 'Select',search: true,selectAll: true}); 
	// 		});
	// 	});
	// 	$('#tableContainerDiv').slimscroll({
	// 		height: 'fit-content',
	// 		width: '100%',
	// 		distance: '-0.9px',
	// 		railVisible: true,
	// 		alwaysVisible: false,
	// 		axis: 'both',
	// 		right: '1.1px',
	// 		wheelStep: 750,
	// 		touchScrollStep: 750,
	// 	});
	// }

	// });
	$("#report_table_filter").on('keyup',function(){
		reports_table.search($(this).val()).draw();
	});
	$(document).on('click','#filter_apply',function(){
		applyFilters(reportsTable1, filters);
    		applyFilters(reportsTable2, filters);
	});
	$(document).on('click','#filter_reset',function(){
		$.each(filters,function(i,filter){
			$('#'+filter.filterId).val('');
		});
		$(".date").val('');
		$('.ms-options input[type="checkbox"]').each(function() {
			$(this).prop('checked',false);  
		});

		$(".ms-options ul li").removeClass("selected");
		$('select[multiple]').parent().find('button').text('Select');
		reportsTable1.search('').columns().search('').draw();
		reportsTable2.search('').columns().search('').draw();

		$('.ms-options input[type="text"]').each(function() {
			$(this).val('');
			$(".ms-options ul li").css('display','list-item');
		});
	});
	
});
$(document).on('change','#financial_year_filter',function(){
	$('#loading_icon').show();
	var fin_year=$(this).val();
	var path=$('#fin_path').val();
	window.location.replace('/'+path+'?fin_year=' + fin_year);  
	$('#loading_icon').hide();  
}); 
$(document).on('mouseenter','.route-details',function(){
	var row=$(this);
	var requestId = row.attr('request_id');
	$.ajaxSetup({
        headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    $.ajax({
        type:'post',
		dataType: 'json',
        url:'route_details',
        data:{requestId:requestId},
            success: function(response){
				travelling_details=response.travelling_details;
				var popupContainer = row.find('.details-popup');
				popupContainer.empty();
				var tableHtml = '';
			$.each(travelling_details, function(index, item) {
				var from_place = item.from_city_name ? item.from_city_name : item.from_country_name;
				var to_place = item.from_city_name ? item.to_city_name : item.to_country_name;
				var fromDate = item.from_date;
				var toDate = item.to_date;
				
				tableHtml += '<div class="city-row">' + from_place + '<img src="images/single-way.svg" style="margin: 0 5px;">' + to_place + '</div>';
				tableHtml += '<div class="date-row">' + fromDate + ' - ' + toDate + '</div>';	
				if (index !== travelling_details.length - 1) {
					tableHtml += '<hr>';
				}		
			});  
			popupContainer.append(tableHtml); 
			var pointerImg = $('<img>').attr('src', 'images/pointer-arrow.svg').addClass('popup-pointer');
			popupContainer.prepend(pointerImg);
		}
	})
});

$(document).on('click','#export',function(){
	$('.buttons-excel').click();
});

$(document).on('click','.filter',function(){
	$('.filter-section').toggle();
});
$(document).on('keydown', '#travel_from_date,#travel_to_date',  function () { return false; });
// To close the alert box; added by venkatesan.raj
$(document).on('click', '.alert_close', function () {
	$(this).closest('.alert').hide();
});

function applyFilters(table, filters) {
    $.each(filters, function(index, filter) {
        if (filter.type == 'select') {
            var search = [];
            $.each($('#' + filter.filterId + ' option:selected'), function() {
                var value = $(this).text().replace(/\(/g, "\\(").replace(/\)/g, "\\)").replace(/\?/g, "\\?");
                search.push(value);
            });
            var searchValue = search.join('|');
            table.columns(filter.columnIndex).search(searchValue, true, false).draw();
        } else if (filter.type == 'date') {
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var min = new Date($("#" + filter.filterId + "_from_date").val());
                    var max = new Date($("#" + filter.filterId + "_to_date").val());
                    var date = new Date(moment(data[filter.columnIndex]));
                    if ((isNaN(min) && isNaN(max)) ||
                        (isNaN(min) && date <= max) ||
                        (min <= date && isNaN(max)) ||
                        (min <= date && date <= max)) {
                        return true;
                    }
                    return false;
                });
        }
    });
}

function initializeDataTable(selector,exportColumns,filters){
	return $(selector).DataTable({
	dom:'Bfrtlip',
    "paging":   true,
    "pageLength":10,
    pagingType: 'full_numbers',
    order:[],"oLanguage": {
    "sSearch":"","sSearchPlaceholder": "Search",
    "sEmptyTable":     "No records found",
    "oPaginate":{
        "sNext":"<img src='/images/previous-arrow.svg'>",
        "sPrevious":"<img src='/images/next-arrow.svg'>",
        "sFirst": "<img src='/images/next-arrow.svg'><img src='/images/next-arrow.svg'>",
        "sLast":"<img src='/images/previous-arrow.svg'><img src='/images/previous-arrow.svg'>"}
    },
	drawCallback: function(settings){
        if((this.api().page.info().pages) <= 0){
        $(selector + '_paginate').hide();
        $(selector + '_length').hide();
        }
        else{
        $(selector + '_paginate').show();
        $(selector + '_length').show();
        }
    },
	buttons: [{
        text: 'EXPORT',                       
        extend: 'excelHtml5',
        exportOptions: {
            columns:exportColumns,
            modifier: {
                selected: true
            },
            format: {
                header: function (data, columnIdx) {
                    return data;
                },
                body: function (data, column, row) {
    				data = data.replace(/<br\s*\/?>/ig, "-"); // Replace <br> or <br/> or <br /> with "-"
					data = data.replace(/&amp;/g, 'and');    // Replace "&amp;" with "and"
    				return data;

                }
            }
        },"pagingType":'simple_numbers',
        footer: true,
        customize: function (xlsx) {
            var sheet = xlsx.xl.worksheets['sheet1.xml'];
        }
    }],
		initComplete: function () {
			var table=this.api();
			$.each(filters, function (index, filter) {
					table.columns([filter.columnIndex]).every(function () {
					var column = this;
					var select = $("#"+filter.filterId);
					column.data().unique().sort().each(function (d, j) {
						if (d !== "") { 
						select.append('<option value="' + d + '">' + d + '</option>');
						}
					});
				$("#"+filter.filterId).multiselect({placeholder: 'Select',search: true,selectAll: true}); 
			});
		});
	}
})
}
function handleSearch(inputSelector, dataTable) {
    $(inputSelector).on('keyup', function () {
        dataTable.search($(this).val()).draw();
    });
}
