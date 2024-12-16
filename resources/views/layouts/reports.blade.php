@extends('header')
@section('title', 'Travel system')
@section('content')
<?php  $page_navigation_no='reports'; ?>

<script type="text/javascript" src="{{ asset('js/Datatable/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/Datatable/dataTables.buttons.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/Datatable/jszip.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/Datatable/buttons.html5.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/custom_datatable.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap_confirmation.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap_tooltip.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.multiselect.js') }}"></script>
<link type="text/css" href="{{ asset('css/jquery.multiselect.css')}}" rel="stylesheet">
<script type="text/javascript" src="{{ asset('js/Slimscrollbothaxis.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/moment.js') }}"></script>



<link rel="stylesheet" href="{{ asset('css/dataTables.css') }}">
<?php 
try{
?>
<div class="table-section" id="report_details">
	<div class="container-fluid" style="margin-bottom: 10px;margin-top: 20px;">
		<div class="row">
			<div class="col-md-2">
				<h3>Travel reports</h3>
			</div>
			<div class="col-md-10">
				<button name="export" class="secondary-button" id="export" data-toggle="tooltip" title="" data-original-title="Export as excel" style="float:right">Export</button>
				<div class="filter secondary-button">
          			<span>Filter</span>
          			<img src="/images/filter.svg" data-toggle="tooltip" title="" data-original-title="Filter data">
        		</div>
				<input id="report_table_filter" class="dataTables_custom_filter" placeholder="Search" class="form-control" style="margin-left: 15px;">
				<input type='hidden' id="fin_path" value='report'/>
				<?php
				echo Form::select('financial_year_filter',$financial_years,$selected_years,['id'=>'financial_year_filter','class'=>'form-control','style'=>'float:right;width:100px;']);
				?>
				<label id="financial_year_label">Financial year</label>
			</div>
		</div>
	</div>
	@include('layouts.reports_filter')

	<div class="container-fluid table-content">
		<div class="row travel-request-table">
			<div class="col-md-12">
				<table id="reports_table">
					<thead>
						<tr>	
							<th>Request id</th>
							<th style="display:none">Request id</th>
							<th>Request type</th>
							<th>Employee details</th>
							<th style="display:none">From date</th>
							<th style="display:none">To date</th>
							<th>Traveling details</th>
							<th>Department</th>
							<th>Project</th>
							<th>Requested on</th>
							<th>Status</th>
							<th style="display:none">Delivery unit</th>
							<th style="display:none">Customer name</th>
							<th style="display:none">Purpose of the travel</th>
							<th style="display:none">Request for</th>
							<th style="display:none">Entity</th>
							<th style="display:none">Ticket required</th>
							<th style="display:none">Family traveling</th>
							<th style="display:none">No of members</th>
							<th style="display:none">Forex required</th>
							<th style="display:none">Accomodation</th>
							<th style="display:none">Prefered accommodation</th>
							<th style="display:none">Working from</th>
							<th style="display:none">Laptop required</th>
							<th style="display:none">Insurance required</th>
							<th style="display:none">Traveler address</th>
							<th style="display:none">Phone no</th>
							<th style="display:none">Email</th>
							<th style="display:none">DOB</th>
							<th style="display:none">Nationality</th>
							<th style="display:none">From country</th>
							<th style="display:none">To country</th>
							<th style="display:none">From city</th>
							<th style="display:none">To city</th>
						  </tr>
						</thead>
						<tbody>	
							@foreach($full_list as $requests)
								<tr>
									<td>
									<a href="/request_full_details/{{Crypt::encrypt($requests->travel_request_id)}}">
									{{$requests->request_id}}
									</a>
									</td>
									<td style="display:none">{{$requests->request_id}}</td>
									<td>{{$requests->module_name}}</td>
									<td>{{$requests->traveler_name}}<br>{{$requests->travaler_id}}</td>
									<td style="display:none">
									@if(isset($requests->from_date))
									{{ date('d-M-Y', strtotime($requests->from_date))}}
									@else
									-
									@endif
									</td>
									<td style="display:none">
									@if(isset($requests->to_date))
									{{ date('d-M-Y', strtotime($requests->to_date))}}
									@else
									-
									@endif
									</td>
									@if(in_array($requests->travel_type_id, ["TRV_01_03", "TRV_02_03"]))	
									<td class="route-details" request_id="{{$requests->travel_request_id}}"><span class="multiple-route">Multiple route</span>
										<div class="details-popup"></div>
									</td>
									@else
									@php
										$startLocation = '';
										$endLocation = '';
										if($requests->module_name == "International" || $requests->module == "MOD_03") {
											$startLocation = $requests->from_country_name;
											$endLocation = $requests->to_country_name;
										}elseif ($requests->module_name == "Domestic") {
											$startLocation = $requests->from_city_name;
											$endLocation = $requests->to_city_name;
										}
										$icon = ($requests->travel_type_id == "TRV_01_02" || $requests->travel_type_id == "TRV_02_02") ? 'double-way.svg' : 'single-way.svg';
										$fromDate = !empty($requests->from_date) ? date('d-M-Y', strtotime($requests->from_date)) : '';
        								$toDate = !empty($requests->to_date) ? date('d-M-Y', strtotime($requests->to_date)) : '';
										$dateRange = $fromDate . ($toDate ? " - " . $toDate : '');									
									@endphp
									<td>
									@if($startLocation && $endLocation)
										{{$startLocation}}<img src="/images/{{$icon}}" style="margin: 0 5px;">{{$endLocation}}<br>
										<div class="date-row">{!!$dateRange!!}</div>
									@endif
									</td>
									@endif
									<td>{{$requests->department_name}}</td>
									<td>{{$requests->project_name}}</td>
									<td data-sort="{{$requests->created_at}}">{{ date('d-M-Y', strtotime($requests->created_at))}}</td>
									<td>{{$requests->status_name}}</td>
									<td style="display:none">{{$requests->practice_unit_name}}</td>
									<td style="display:none">{{$requests->customer_name}}</td>
									<td style="display:none">{{$requests->travel_purpose}}</td>
									<td style="display:none">{{$requests->request_for}}</td>
									<td style="display:none">{{$requests->requestor_entity}}</td>
									<td style="display:none">{{$requests->ticket_required==1 ? 'Yes' : 'No'}}</td>
									<td style="display:none">{{$requests->family_traveling==1 ? 'Yes' : 'No'}}</td>
									<td style="display:none">{{$requests->family_traveling==1 ? $requests->no_of_members : '-'}}
									<td style="display:none">{{$requests->module != 'MOD_01' ? ($requests->forex_required == 1 ? 'Yes' : 'No') : '-' }}</td>
									<td style="display:none">{{$requests->accommodation_required==1 ? 'Yes' : 'No'}}
									<td style="display:none">{{$requests->prefered_accommodation}}</td>
									<td style="display:none">{{$requests->work_place}}</td>
									<td style="display:none">{{$requests->laptop_required==1 ? 'Yes' : 'No'}}</td>
									<td style="display:none">{{$requests->insurance_required==1 ? 'Yes':'No'}}</td>
									<td style="display:none">{{$requests->traveller_address}}</td>
									<td style="display:none">{{$requests->phone_no}}</td>
									<td style="display:none">{{$requests->email}}</td>
									<td style="display:none">{{$requests->dob}}</td>
									<td style="display:none">{{$requests->nationality}}</td>
									<td style="display:none">{{$requests->from_country_name}}</td>
									<td style="display:none">{{$requests->to_country_name}}</td>
									<td style="display:none">{{$requests->from_city_name}}</td>
									<td style="display:none">{{$requests->to_city_name}}</td>
								</tr>
							@endforeach	
						</tbody>	
					</table>
				</div>
			</div>			
		</div>
	</div>	
</div>
<?php 
}
catch(Exception $e)
{
	dd($e);
	echo "<b>Something went wrong while fetching data. Please write to help.mis@aspiresys.com for queries.</b>";
}
?>
@endsection
