$(document).ready(
		function()
				{
			
		
			//**************initial check for forex and family members validation*******
			if($("#forex option:selected").text()=='No')
			 {
			 	$("#currency").attr("disabled","disabled");
			 	$("#currency").val('');
			 }
		 else 
			 {
			 $('#currency').removeAttr('disabled');
			 }
			if($("#family_traveling option:selected").text()=='No')
			 {
			 	$("#no_mem").attr("disabled","disabled");
			 	$("#no_mem").val('');
			 }
		 else 
			 {
			 $('#no_mem').removeAttr('disabled');
			 }
			//*************************initial check for customer creation**************
			$customer = $("#customer option:selected").val();
		 $department_id=$("#department").val();
			 //console.log($department_id);
			 var approver=$("#customer");
				 if(!$department_id)
				 $("#customer").html("<option value=''>Select</option>");
			 $('option', $("#customer")).not(':eq(0)').remove();
			$.ajaxSetup({
		     headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			   }
	    	});
			$.ajax({
			    type: 'POST',
			    data: {department:$department_id},
			    url: '../getCustomer',
			    dataType:'json',
			    success: function(response) {
			    	
				  type0=response.customer;
				    flag=response.flag;
				    if(flag==0)
				    {
				    	$("#project_name").attr("disabled","disabled");
				    	$("#project_name").val('');
				   	}
				    else
				    {
				    	$('#project_name').removeAttr('disabled');
				   	}
				    $.each(type0, function(key, value) {   
				        $('#customer')
				            .append($("<option></option>")
				                       .attr("value",key)
				                       .text(value)); 
				   });
				    $("#customer option[value="+$customer+"]").attr('selected','selected');
				    }
			 });
			
	//******************initial check for approver selection***********************
			$( function() {
				 $department_id=$("#department").val();
				 $dept_head = $("#approver option:selected").val();
				// alert($dept_head);
							 var approver=$("#approver");
							 $('option', $("#approver")).not(':eq(0)').remove();
							$.ajaxSetup({
						     headers: {
					        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							   }
					    	});
							$.ajax({
							    type: 'POST',
							    data: {department:$department_id},
							    url: '../getDepartment',
							    success: function(response) {
								    type0=response;
								    $.each(type0, function(key, value) {   
								        $('#approver')
								            .append($("<option></option>")
								                       .attr("value",key)
								                       .text(value)); 
								   });
								    $("#approver option[value="+$dept_head+"]").attr('selected','selected');
								    }
							  });
							
		//*************currecy creation*****************
							currency = $("#currency option:selected").val();
							$.ajaxSetup({
							     headers: {
						        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								   }
						    	});
								$.ajax({
								    type: 'POST',
								   // data: {department:$department_id},
								    url: '../getCurrency',
								    dataType:'json',
								    success: function(response) {
								    	$('option', $("#currency")).not(':eq(0)').remove();
									    type0=response.currency;
									    type1=response.name;
									   // console.log(type1);
									    $.each(type0, function(key, value) {   
									        $('#currency')
									            .append($("<option></option>")
									                       .attr("value",key)
									                       .attr("title",type1[key])
									                       .text(value)); 
									   });
									    $("#currency option[value="+currency+"]").attr('selected','selected');
									    }
								  });
							
			//**********mulitiple route table show when validation**********
							if($("#multiple_country_name").val()!='null' &&$("#multiple_country_name").val() != '')
								{
								if($("#type").val()==3)
								{
								var country_name=$("#multiple_country_name").val().split(",");
								var destination=$("#multiple_destination").val().split(",");
								var from=$("#multiple_from").val().split(",");
								var to=$("#multiple_to").val().split(",");
								var table = $('#multi_route_table'); 
								 $("#multi_route_table").find("tr:gt(0)").remove();
								for (i=0;i<country_name.length;i++){
									table.append("<tr><td class='country'>"+country_name[i]+"</td><td>"+destination[i]+"</td><td>"+from[i]+"</td><td>"+to[i]+"</td><td><button type='button' id='removerow'><img src='https://test.aspiresys.com/Travelsystem/img/close.png'></button></td></tr>");
									//$("#multi_route_table tr:last td:eq(1)").attr('title',destination[i]);

								}
								table.show();
								}
								else
									{
									$('#multi_route_table').hide();
									}
								}
		//**************mulitiple route table show***************
							type=$("#type").val();
							frm=$("#from").val();
							if(type==3)
							{
								$('#addbtn').removeAttr('disabled');
								 $('#multi_route_table').show();
								 if($("#from").val()!='')
									{
									 $('#to').removeAttr('disabled');
										frm=$("#from").val();
										  var dt = new Date(frm);
								          //console.log(dt);
								            dt.setDate(dt.getDate() + 1);
										$("#to").datepicker({
											minDate:dt,
											dateFormat: 'dd M yy',
											 onSelect: function (selected) {
										            var fdt = new Date(selected);
										            fdt.setDate(fdt.getDate() - 1);
										            $("#from").datepicker("option", "maxDate",fdt);
										        }
											
										});
									
									}}

							else if($('#type').val()==1)
								{
								$("#to").attr("disabled","disabled");
								$("#to").val('');
								}
							else if($("#type").val()==2)
								{
								if($("#from").val()!='')
								{
								$('#to').removeAttr('disabled');
								frm=$("#from").val();
								var dt = new Date(frm);
					            dt.setDate(dt.getDate() + 1);
								$("#to").datepicker({
									minDate:dt,
									dateFormat: 'dd M yy',
									 onSelect: function (selected) {
								            var fdt = new Date(selected);
								            fdt.setDate(fdt.getDate() - 1);
								            $("#from").datepicker("option", "maxDate",fdt);
								        }
									
								});
								
								}
								}
							$('#addbtn').click(function (){
								var count=0;
								var des=$('#destination').val();
								var $country=$('#country').val();
								var from=$("#from").val();
								var to=$("#to").val();
								if(des==''||$country==''||from==''||to=='')
								{
									$("#multi_route_error").text('All the feilds are required');
									$("#multi_route_error").show();
									setTimeout(function() {
									    $("#multi_route_error").hide('blind', {}, 500)
									    }, 3000);
									$("#to").attr("disabled","disabled");
								}
								else
								{
									var MyRows = $('#multi_route_table').find('tbody').find('tr');
									var from_date=$("#from").val();
									var to_date=$("#to").val();
									var from_date1=[];
									var to_date1=[];
									new_frm=new Date(from_date);
									new_to=new Date(to_date);
									for (var i = 0; i < MyRows.length; i++) 
									{
									from_date1[i]= $(MyRows[i]).find('td:eq(2)').html();
									//console.log(from_date1[i]);
									var d1 = moment(from_date1[i], "DD-MMM-YYYY").format("DD MMM YYYY");
									to_date1[i] = $(MyRows[i]).find('td:eq(3)').html();
									var d2 = moment(to_date1[i], "DD-MMM-YYYY").format("DD MMM YYYY");
									if(new_frm <= new Date(d2) && new_frm>=new Date(d1)||
										new_to <= new Date(d2) && new_to>=new Date(d1)||
										(new_frm <= new Date(d2) && new_frm>=new Date(d1))&&
										(new_to <= new Date(d2) && new_to>=new Date(d1))||
										(new Date(d1)<new_to && new Date(d1)>new_frm)&&
										(new Date(d2)>new_frm && new Date(d2)<new_to))
										{
										count++;
										}
									//console.log(d2);
									}
									
									if(count==0)
									{
								var table = $('#multi_route_table'); 
								var destination=$('#destination').val();
//								if(destination.length>30)
//									{
//									//alert('hai');
//									var short_destination=des.substr(0, 20);
//									var destination=short_destination+'....';
//									}
								var from=$("#from").val();
								var date_from = moment(from, "DD MMM YYYY").format("DD-MMM-YYYY");
								var to=$("#to").val();
								var date_to = moment(to, "DD MMM YYYY").format("DD-MMM-YYYY");
					table.append("<tr><td class='country'>"+$("#country option:selected").text()+"</td><td style='max-width:200px;word-wrap: break-word; overflow:hidden;line-height:1.5em' class='desn'>"+destination+"</td><td>"+date_from+"</td><td>"+date_to+"</td><td><button type='button' id='removerow'><img src='https://test.aspiresys.com/Travelsystem/img/close.png'></button></td></tr>");
					//$("#multi_route_table tr:last td:eq(1)").attr('title',des);
					table.show();
					$("#from").datepicker("option", "minDate",today);
					$("#from").datepicker("option", "maxDate",null);
					$country_name='';	
					$("#destination").val("");
					$('#country option:eq(0)').prop('selected',true);
					$("#from").val("");
					$("#to").val("");
					$("#to").attr("disabled","disabled");
						}
		     		else
					{
		     			alert("Your date is overlapped with previous dates!please Enter correct date");
						$("#from").val("");
						$("#to").val("");
						$("#from").datepicker("option", "minDate",today);
						$("#from").datepicker("option", "maxDate",null);
						$("#to").datepicker("option", "minDate",null);
						$("#to").datepicker("option", "maxDate",null);
						$("#to").attr("disabled","disabled");
					}
				}
					
								
				});
							
							});
			
			 $("#btn_save").click(function(){
				 $.ajax({
					    type: 'POST',
					    data: $("#request_form").serialize(),
					    url: '/edit_update',
					    success: function(data) {
//					    	var success= $.parseJSON(data.responseText);
//					    	alert(success);
					    },
					    error: function(data){
					        // Error...
					        var errors = $.parseJSON(data.responseText);
						        $.each(errors, function(index, value) {
					           $("#errors").append("<br>"+value);
					           $("#errors").show();
					        });
					    }

				 });
			 });
			
//*************Multi Route Table Creation********************
			$("#type").change(
			function(){
				if($('#type').val()==3)
				{ $("#multi_route_table").find("tr:gt(0)").remove();
					$('#addbtn').removeAttr('disabled');
					if($("#from").val()!='')
					{
					$('#to').removeAttr('disabled');
					}
					
		}
				else if($('#type').val()==1)
				{
					var table = $('#multi_route_table'); 
					 $("#multi_route_table").find("tr:gt(0)").remove();
					table.hide();
					
					$("#to").attr("disabled","disabled");
					$('#addbtn').attr("disabled","disabled");
					$("#to").val('');
				}
				else if($('#type').val()==2)
				{
					var table = $('#multi_route_table'); 
					$("#multi_route_table").find("tr:gt(0)").remove();
					table.hide();

					$('#addbtn').attr("disabled","disabled");
					if($("#from").val()!='')
					{
						$('#to').removeAttr('disabled');
						frm=$("#from").val();
						var dt = new Date(frm);
			            dt.setDate(dt.getDate() + 1);
			            $("#to").datepicker("option", "minDate", dt);	
						$("#to").datepicker({
							minDate:dt,
							dateFormat: 'dd M yy',
							 onSelect: function (selected) {
						            var fdt = new Date(selected);
						            fdt.setDate(fdt.getDate() - 1);
						            $("#from").datepicker("option", "maxDate",fdt);
						        }
							
						});
					
					}
					}
				else
				{
					var table = $('#multi_route_table'); 
					table.hide();
					$("#to").attr("disabled","disabled");
					$('#addbtn').attr("disabled","disabled");
				}
				});
					$("#multi_route_table").on('click', '#removerow', function () {
						var MyRows = $('#multi_route_table').find('tbody').find('tr');
						if(MyRows.length==1)
							{
							$(this).closest('tr').remove();
							$('#multi_route_table').hide();
							}
						else
							{$(this).closest('tr').remove();}
				    
					});
	//*****************date picker creation*******************
					$( function() {
						today=new Date(1000*today);
						  // $.datepicker( {dateFormat: 'dd-M-yy',minDate:0 }).attr('readonly','readonly');
								 $("#from").datepicker({
						        numberOfMonths: 1,
						        minDate:today,
						        dateFormat: 'dd M yy',
						        onSelect: function (selected) {
						            var dt = new Date(selected);
						          
						            dt.setDate(dt.getDate() + 1);
						             $("#to").datepicker("option", "minDate", dt);
						             if($('#type').val()!=1||'')
						            	 {
						             $("#to").removeAttr('disabled');     //enable to date after from date entry
						            	 }
						        }
						      //  dateFormat:'dd-M-yy'
						    }).attr('readonly','readonly');
						    $("#to").datepicker({
						        numberOfMonths: 1,
						        minDate:0,
						        dateFormat: 'dd M yy',
						        onSelect: function (selected) {
						            var dt = new Date(selected);
						            dt.setDate(dt.getDate() - 1);
						            $("#from").datepicker("option", "maxDate",dt);
						        }
						    }).attr('readonly','readonly');
						  } );
					
		//*****************Approver Select box Creation*******************
					 
					 $("#department").on('change',function(){
						 $department_id=$("#department").val();
						// alert($department_id);
						 var approver=$("#approver");
						 $('option', $("#approver")).not(':eq(0)').remove();
						$.ajaxSetup({
					     headers: {
				        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						   }
				    	});
						$.ajax({
						    type: 'POST',
						    data: {department:$department_id},
						    url: '../getDepartment',
						    success: function(response) {
							    type0=response;
							
							    $.each(type0, function(key, value) {   
							        $('#approver')
							            .append($("<option></option>")
							                       .attr("value",key)
							                       .attr("title",value)
							                       .text(value)); 
							   });
							    }
						 });
						});
					 
	//***************customer creation***************
					 $("#department").on('change',function(){
						 $department_id=$("#department").val();
					
						 var approver=$("#customer");
						 $('option', $("#customer")).not(':eq(0)').remove();
						// $('option', $("#approver")).not(':eq(0)').remove();
						 
						$.ajaxSetup({
					     headers: {
				        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						   }
				    	});
						$.ajax({
						    type: 'POST',
						    data: {department:$department_id},
						    url: '../getCustomer',
						    dataType:'json',
						    success: function(response) {
							    type0=response.customer;
							    flag=response.flag;
							//    console.log("the retrive vaklue "+response.retrive);
							    if(flag==0)
							    {
							    	$("#project_name").attr("disabled","disabled");
							    	$("#project_name").val('');
							   	}
							    else
							    {
							    	$('#project_name').removeAttr('disabled');
							   	}
							    $.each(type0, function(key, value) {   
							        $('#customer')
							            .append($("<option></option>")
							                       .attr("value",key)
							                       .text(value)); 
							   });
							   //retrive$("#project_name option[value=val]").attr('selected','selected');
							    }
						 });
						});

//**************forx,amount enable and disable***************
					 $("#forex").on('change',function(){
						 if($("#forex option:selected").text()=='No')
							 {
							 	$("#currency").attr("disabled","disabled");
							 	$("#currency").val('');
							 }
						 else 
							 {
							 $('#currency').removeAttr('disabled');
							 }
					 });
				 
					 
//*************family members check condition***********
					 
					 $("#family_traveling").on('change',function(){
						 if($("#family_traveling option:selected").text()=='No')
							 {
							 	$("#no_mem").attr("disabled","disabled");
							 	$("#no_mem").val('');
							 }
						 else 
							 {
							 $('#no_mem').removeAttr('disabled');
							 }
					 });
//					 
//					 //accomodation enable/disable
//					 $("#accomodation").on('change',function(){
//						 if($("#accomodation option:selected").text()=='No')
//							 {
//							 $("#budget").attr("disabled","disabled");
//							 }
//						 else
//							 {
//							 $("#budget").removeAttr('disabled');
//							 }
//					 });
					 
					 
					 //multi Route passing
					 $("#submit").click(function(){
						 $("#errors").hide();
						 	$("#errors").empty();
							$(".container > .alert-danger").hide();
						 	$(".container > .alert-danger").empty();
							var MyRows = $('#multi_route_table').find('tbody').find('tr');
							var country_name=[];
							var multiple_destination=[];
							var from_date=[];
							var to_date=[];
							for (var i = 0; i < MyRows.length; i++) {
							country_name[i]= $(MyRows[i]).find('td:eq(0)').html();
							multiple_destination[i]= $(MyRows[i]).find('td:eq(1)').html();
							from_date[i]= $(MyRows[i]).find('td:eq(2)').html();
							to_date[i] = $(MyRows[i]).find('td:eq(3)').html();
							}
							$('#multiple_country_name').val(country_name);
							$('#multiple_destination').val(multiple_destination);
							$('#multiple_from').val(from_date);
							$('#multiple_to').val(to_date);
							   if($("#type").val() == 3){
									if(country_name.length == 0 || multiple_destination.length == 0 || from_date.length == 0 || to_date.length == 0) {
										$("#multi_route_error").text('Add the fields for multiple route');
										$("#multi_route_error").show();	
										setTimeout(function() {
										    $("#multi_route_error").hide('blind', {}, 500)
										    }, 3000);
										return false;
															
									}
									if($("#from").val() != '' || $("#to").val() != '') {
										$("#multi_route_error").text('Add the fields for multiple route');
										$("#multi_route_error").show();	
										setTimeout(function() {
										    $("#multi_route_error").hide('blind', {}, 500)
										    }, 3000);
									return false;
									}
								
								   }
						
						   });
					 setTimeout(function() {
						    $("#successMessage").hide('blind', {}, 500)
						    }, 5000);
					 $("#save_btn").click(function(){
						 $("#errors").hide();
						 	$("#errors").empty();
						 	$(".container > .alert-danger").hide();
						 	$(".container > .alert-danger").empty();
							var MyRows = $('#multi_route_table').find('tbody').find('tr');
							var country_name=[];
							var multiple_destination=[];
							var from_date=[];
							var to_date=[];
							for (var i = 0; i < MyRows.length; i++) {
							country_name[i]= $(MyRows[i]).find('td:eq(0)').html();
							multiple_destination[i]= $(MyRows[i]).find('td:eq(1)').html();
							from_date[i]= $(MyRows[i]).find('td:eq(2)').html();
							to_date[i] = $(MyRows[i]).find('td:eq(3)').html();
							}
							$('#multiple_country_name').val(country_name);
							$('#multiple_destination').val(multiple_destination);
							$('#multiple_from').val(from_date);
							$('#multiple_to').val(to_date);
							
						 // Check for multiple route
						   if($("#type").val() == 3){
							if(country_name.length == 0 || multiple_destination.length == 0 || from_date.length == 0 || to_date.length == 0) {
								$("#multi_route_error").text('Add the fields for multiple route');
								$("#multi_route_error").show();	
								setTimeout(function() {
								    $("#multi_route_error").hide('blind', {}, 500)
								    }, 3000);
													return false;
													
							}
							if($("#from").val() != '' || $("#to").val() != '') {
								$("#multi_route_error").text('Add the fields for multiple route');
								$("#multi_route_error").show();	
								setTimeout(function() {
								    $("#multi_route_error").hide('blind', {}, 500)
								    }, 3000);
							return false;
							}
							
							
						   }
							
						
					 });

					 
					
		});
