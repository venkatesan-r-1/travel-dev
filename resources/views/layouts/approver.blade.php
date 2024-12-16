@extends('header')
@section('title', 'Travel system')
@section('content')


<?php $page_navigation_no='approver_details'; ?>

<script src="{{ asset('js/custom_datatable.js') }}"></script>
<script src="{{ asset('js/Datatable/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap_confirmation.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap_tooltip.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/Slimscrollbothaxis.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/dataTables.css') }}">
<?php 
try{
?>
    <div class="table-section" id="approver_details">
		<div class="container-fluid" style="margin-bottom: 10px;margin-top: 20px;">
			<div class="row">
				<div class="col-md-7">
					<h3>My approval</h3>
				</div>
				<div class="col-md-5">
					<input type='hidden' id="fin_path" value='approval'/>
					<?php
					echo Form::select('financial_year_filter',$financial_years,$selected_years,['id'=>'financial_year_filter','class'=>'form-control','style'=>'float:right;width:100px;margin-top: 15px;']);
					?>
					<label style="float: right;margin-top: 25px;margin-right: 10px;">Financial year</label>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 is-tab-content">
					<ul class="nav nav-tabs am-menu" style="margin-bottom:10px;">
						<li class="active nav-item">
							<a class="nav-link" data-toggle="tab" href="#approve_tocheck">To check</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#approve_checked">Checked</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="row col-md-2" style="float:right">
				<input id="approve_table_filter"  class="dataTables_custom_filter" placeholder="Search" class="form-control">
			</div>
		</div>
		<div class="container-fluid table-content">
			<div class="row tab-content travel-request-table">
				<div id="approve_tocheck" class="col-md-12 tab-pane active">
					<table id="approve_tocheck_table">
						<thead>
							<tr>	
								<th>Request id</th>
								<th>Request type</th>
								<th>Employee details</th>
								<th>Traveling deatils</th>
								<th>Department</th>
								<th>Project</th>
								<th>Requested on</th>
								<th>Action</th>
						  	</tr>
						</thead>
						<tbody>	
							@foreach($approver_to_check as $requests)
							<tr>
								<td>
								<a href="{{ $requests->module == "MOD_03" ? '/visa_request/'.Crypt::encrypt($requests->id) : '/request_full_details/'.Crypt::encrypt($requests->id) }}">
								<img src="/images/status/{{$icons[$requests->status_id]}}" class="status_img" title="{{$requests->status_name}}" data-toggle="tooltip">
								{{$requests->request_id}}
								</a>								</td>
								<td>{{$requests->module_name}}</td>
								<td>{{$requests->username}}<br>{{$requests->travaler_id}}</td>
								@if(in_array($requests->travel_type_id, ["TRV_01_03", "TRV_02_03"]))	
								<td class="route-details" request_id="{{$requests->id}}"><span class="multiple-route">Multiple route</span>
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
										$dateRange = $fromDate . ($toDate ? " - " . $toDate : '');									@endphp
									<td>
									@if($startLocation && $endLocation)
										{{$startLocation}}<img src="/images/{{$icon}}" style="margin: 0 5px;">{{$endLocation}}<br>
										<div class="date-row">{!!$dateRange!!}</div>
									@endif
									</td>
								@endif
								<td>{{$requests->dep_name}}</td>
								<td>{{$requests->project_name}}</td>
								<td>{{ date('d-M-Y', strtotime($requests->created_at))}}</td>
								<td>
								<a href="{{ $requests->module == "MOD_03" ? '/visa_request/'.Crypt::encrypt($requests->id) : '/request_full_details/'.Crypt::encrypt($requests->id) }}"><img src="/images/action.svg" data-toggle="tooltip" class="action_image" title="Approve/Reject"></a>
								</td>
							</tr>
							@endforeach	
						</tbody>	
					</table>
				</div>
				<div id="approve_checked" class="col-md-12 tab-pane fade">
					<table id="approve_checked_table">
						<thead>
							<tr>	
								<th class="request_id_th">Request id</th>
								<th class="request_type_th">Request type</th>
								<th>Employee details</th>
								<th>Traveling deatils</th>
								<th>Department</th>
								<th>Project</th>
								<th class="request_on_th">Requested on</th>
								<th class="status_th">Status</th>
						  	</tr>
						</thead>
						<tbody>	
							@foreach($approver_checked as $requests)
							<tr>
								<td>
								<a href="{{ $requests->module == "MOD_03" ? '/visa_request/'.Crypt::encrypt($requests->id) : '/request_full_details/'.Crypt::encrypt($requests->id) }}">
								<img src="/images/status/{{$icons[$requests->status_id]}}" class="status_img" title="{{$requests->status_name}}" data-toggle="tooltip">
								{{$requests->request_id}}
								</a>								
								</td>
								<td>{{$requests->module_name}}</td>
								<td>{{$requests->username}}<br>{{$requests->travaler_id}}</td>
								@if(in_array($requests->travel_type_id, ["TRV_01_03", "TRV_02_03"]))	
								<td class="route-details" request_id="{{$requests->id}}"><span class="multiple-route">Multiple route</span>
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
										$dateRange = $fromDate . ($toDate ? " - " . $toDate : '');									@endphp
									<td>
									@if($startLocation && $endLocation)
										{{$startLocation}}<img src="/images/{{$icon}}" style="margin: 0 5px;">{{$endLocation}}<br>
										<div class="date-row">{!!$dateRange!!}</div>
									@endif
									</td>
								@endif
								<td>{{$requests->dep_name}}</td>
								<td>{{$requests->project_name}}</td>
								<td>{{ date('d-M-Y', strtotime($requests->created_at))}}</td>
								<td>{{$requests->status_name}}</td>
							</tr>
							@endforeach		
							</tbody>	
					</table>
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
