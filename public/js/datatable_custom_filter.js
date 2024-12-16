
/**
 * This file is used to create datatables and its relavant filters
 * @author: Ganesh.veilsamy
 * addedon: oct132022
 */
  // var datatable_ref='';
$(document).ready(function(){
  var datatable_ref;
    initiate_ajax(table_config);
    $('#visa_process_financial_year_filter').on('change',function () {
      table_config.parameters = {"visa_process_financial_year":$(this).val()};
      initiate_ajax(table_config);
    })

    //adding filter action
    $('#filterApply').on('click',function(){
        $.each(overall_columns_config,function(i,val){
          var filter_array=[];
          if(val.filter_required=='yes'){
            if(val.filter_type=='input'){
              filter_value=$('#'+val.idname).val();
              datatable_ref.columns(val.column_no).search(filter_value,true,false).draw();
            }
            else if(val.filter_type=='multiselect'){
              $.each($('#'+val.idname+' option:selected'), function(){
                var value = $(this).text().replace(/\(/g, "\\(").replace(/\)/g, "\\)").replace(/\?/g, "\\?");
                    filter_array.push(value);
               });
               filter_text = filter_array.join('|');
               datatable_ref.columns(val.column_no).search(filter_text,true,false).draw();
            }
            else if(val.filter_type=='date'){
              $.fn.dataTable.ext.search.push(
                function( settings, data, dataIndex ) {
                  var min =new Date($("#"+val.filter_column+"_from_date").val());
                  var max =new Date($("#"+val.filter_column+"_to_date").val());
                  var date = new Date(moment(data[val.column_no])) // use data for the age column
                  //For handling the negative year issue in mozilla firefox
                  if(min.getFullYear()<0 || max.getFullYear()<0 || date.getFullYear()<0)
                  {
                    min =new Date($("#"+val.filter_column+"_from_date").val().replaceAll('-','/'));
                    max =new Date($("#"+val.filter_column+"_to_date").val().replaceAll('-','/'));
                    date = new Date(moment(data[val.column_no].replaceAll('-','/')));
                  }
                  if ( ( isNaN( min ) && isNaN( max ) ) ||
                       ( isNaN( min ) && date <= max ) ||
                       ( min <= date   && isNaN( max ) ) ||
                       ( min <= date   && date <= max ) ) {
                  return true;
                  }
                  return false;
                  });
            }
          }
        });
      });
      
      //adding overall filter fot the datatable
      $(".datatable_filter_input").on('keyup',function(){
        datatable_ref.search($(this).val()).draw();
      });	

      //For removing pagination and show entries select box in empty table...
      datatable_ref.on('draw',function () {
        if($('.dataTables_paginate > span > a').length == 0)
        {
          $('.dataTables_length').hide();
          $('.dataTables_paginate').hide();
        }
        else
        {
          $('.dataTables_length').show();
          $('.dataTables_paginate').show();
        }
      });

      //Reseting the filter values
      $('#filterReset').on('click',function(){
        $.each(overall_columns_config,function(i,val){
            $('#'+val.idname).val('');
        });
        $('.ms-options input[type="checkbox"]').each(function() {
          $(this).prop('checked',false);  
      });
      $(".ms-options ul li").removeClass("selected");
      $('select[multiple]').parent().find('button').text('Search');
        datatable_ref.search('').columns().search('').draw();
      });
      

    //Function used to get the data from backend to create a datatable
    function initiate_ajax(table_config){
      $.ajax({
          url: table_config.response_url,
          type: table_config.request_type,
          dataType:table_config.response_type,
          data:table_config.parameters,
          async:false,
         success: function(data) {
              datatable_ref=create_datatable(table_config,data);
              return datatable_ref;
          },
          error:function(error){
              alert("Error occured..");
          }
      
        });
  }

  //To add a datepicker for the dateinputs
  $('.date1z').datepicker({  
    'format': 'dd-M-yyyy',
    'autoclose': true,
    'startDate':   new Date('2022-04-01'),
    'endDate':new Date('2023-03-31')
  }); 

  //To show the view page for the particular request id
  $(document).on('click','td.request_redirect',function(){
    var request_id=$(this).html();
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
      });
      $.ajax({
        type:'POST',
        data:{request_id:request_id},
        url:'/payment_id_encrypt',
        //dataType:'json',
        async:false,
        success:function(data){
          window.open(data);
        },
        error:function(error){
          alert('Error occured. Please write to help.mis@aspiresys.com for assistance');
        }
      });
  });
});

function create_datatable(table_config,data){
    var d=data.data;var columns=data.columns;
    $("#"+table_config.table_id).empty(); 
    table=$("#"+table_config.table_id).DataTable({
        dom:table_config.table_dom,
        "Searching":true,
        "paging":  table_config.pagination_required ,
        "pageLength":table_config.pagination_count,
        "pagingType":"full_numbers",
        "bDestroy": true,
        // "scrollX": true,
        // "scrollY": "330px",
        // "scrollCollapse": true,
         order:[],
        "oLanguage": {"sSearch":"","sSearchPlaceholder": "","sEmptyTable":     "No records found","sLengthMenu": 'Show <select>'+
        '<option value="5">5</option>'+
        '<option value="10">10</option>'+
        '<option value="20">20</option>'+
        '<option value="-1">All</option>'+
        '</select> entries',
                      "oPaginate":{"sNext":">","sPrevious":"<","sFirst":"<<","sLast":">>"}},
        buttons:[{
            extend:'excelHtml5',
		    exportOptions: {modifier: {selected: true},
                format: {header: function (data, columnIdx) {return data;},
		            body: function (data, column,row){return data.toString().replace('/&/g', "and");}
                }
		    },
			customize: function (xlsx) {
		          var sheet = xlsx.xl.worksheets['sheet1.xml'];
		          var col = $('col', sheet);
		          $(col[2]).attr('width', 20);
		        }
        }],          
        data:d,columns:columns,
        initComplete: function () {
          datatable_api=this.api();
          $.each(overall_columns_config,function(i,val){
            if(val.filter_required&&val.filter_required=='yes'){
              if(val.filter_type=='multiselect'){
                datatable_api.columns([val.column_no]).every( function () {
                  var column = this;
                  var select = $("#"+val.idname); 
                  column.data().unique().sort().each( function ( d, j ) {
                    if(d)
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                  });
                  });
                  $("#"+val.idname).multiselect({placeholder: 'Select',search: true,selectAll: true}); 
              }
            }
          });
          $('.ui-wait').hide();
          $('#'+table_config.table_id).wrap(`<div class='${table_config.table_id}_wrapper'></div>`);
        }
    });
    return table;
}

