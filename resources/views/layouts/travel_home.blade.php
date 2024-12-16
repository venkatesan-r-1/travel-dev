@extends('header')
@section('title', 'Travel system')
@section('content')

<?php $page_navigation_no='request_view';?>
<?php $city_details=DB::table('trd_country_city')->pluck('name','unique_key')->toArray(); ?>
<?php $country_details=DB::table('trd_country_details')->pluck('name','unique_key')->toArray(); ?>
<script src="{{ asset('js/custom_datatable.js') }}"></script>
<script src="{{ asset('js/Datatable/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap_confirmation.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap_tooltip.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/Slimscrollbothaxis.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/dataTables.css') }}">


<div class="table-section" id="home_details">
	<div class="container-fluid" style="margin-bottom: 10px; margin-top: 20px;">
		<div class="row">
			<div class="col-md-7">
				<h3>My requests</h3>
			</div>
			<div class="col-md-5">
				<input id="traveler_home_details_filter" class="dataTables_custom_filter" placeholder="Search" class="form-control" style="margin-left: 15px;">
				<input type='hidden' id="fin_path" value='home'/>
				<?php
				echo Form::select('financial_year_filter',$financial_years,$selected_years,['id'=>'financial_year_filter','class'=>'form-control','style'=>'width:100px;float:right;']);
				?>
				<label id="financial_year_label">Financial year</label>
			</div>
		</div>
	</div>
	<div class="container-fluid table-content">
		<div class="row travel-request-table">
			<div class="col-md-12">
				<table id="traveler_home_details">
					<thead>
						<tr>
                            <th>Request id</th>
							<th>Request type</th>
							<th>Employee details</th>
							<th>Traveling details</th>
							<th>Department</th>
							<th>Project</th>
							<th>Requested on</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						@foreach($requestList as $requests)
							<tr>
								<td>
								<a href="{{ $requests->module == "MOD_03" ? '/visa_request/'.Crypt::encrypt($requests->id) : '/request_full_details/'.Crypt::encrypt($requests->id) }}">
								<img src="/images/status/{{$icons[$requests->status_id]}}" class="status_img " title="{{$requests->status_name}}" data-toggle="tooltip">
								{{$requests->request_id}}
								</a>
								</td>
								<td>{{$requests->module_name}}</td>
								<td>{{$requests->username}}<br>{{$requests->travaler_id}}</td>					
								@if(in_array($requests->travel_type_id, ["TRV_01_03", "TRV_02_03"]))	
								<td class="route-details" request_id="{{$requests->id}}"><span class="multiple-route">Multiple route</span>
								<div class="details-popup">
								<img src="/images/pointer-arrow.svg">
								</div>
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
								<td data-sort="{{$requests->created_at}}">{{ date('d-M-Y', strtotime($requests->created_at))}}</td>
								<td>{{$requests->status_name}}</td>
								<td style="text-align:center">
								@if($requests->status_id=="STAT_01" || ( in_array($requests->status_id, ["STAT_28","STAT_35"] ) && $requests->travaler_id == Auth::User()->aceid ))
								<a href="{{ $requests->module == "MOD_03" ? '/visa_request/'.Crypt::encrypt($requests->id) : '/request_full_details/'.Crypt::encrypt($requests->id) }}"><img src="/images/action.svg" data-toggle="tooltip" class="action_image" title="Edit/Submit"></a>
								@else
								-
								@endif
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
<style>
.popup-pointer{
  position: absolute;
  left: -19px;
  z-index: 10000;
  display: block !important;
  top: 2px;
}
</style>
