<?php 
$visible_fields=$field_details['visible_fields'];
$editable_fields=$field_details['editable_fields'];
$field_attr=$field_details['field_attr'];
$options=$field_details['select_options'];
$ticket_required=$request_details->ticket_required; 
$forex_required=$request_details->forex_required;
$ticket_active = $ticket_required == 1 ? 'active' : '';
$forex_active = $ticket_active != 'active' ? ($forex_required == 1 ? 'active' : '') : '';
// harded for enabling atleast one process
$ticket_active = $forex_active == 'active' ? $ticket_active : 'active';

//dd($forex_details);
//dd(in_array('visa_tab',$visible_fields));
?>
@if($module=='MOD_03' && in_array($request_details->status_id,['STAT_14','STAT_12']))
<div class="container-fluid">
	<div class="col-md-12 is-tab-content">
		<ul class="nav nav-tabs" style="margin-bottom:10px;">
			<li class="active"><a class="ticket_details" data-toggle="tab" href="#visa_process">Visa Process</a></li>
		</ul>
	</div>
</div>
@else
<div class="container-fluid">
	<div class="col-md-12 is-tab-content">
		<ul class="nav nav-tabs processTab" style="margin-bottom:10px;">
			<li role="presentation" class="{{$ticket_active}}"><a class="ticket_details" data-toggle="tab" href="{{ $ticket_required ? '#ticket_process' : '#'; }}">Ticket Process</a></li>
			@if($module==='MOD_02')
				<li role="presentation" class="{{$forex_active}}"><a class="forex_details" data-toggle="tab" href="{{$forex_required ? '#forex_process' : '#'; }}">Forex Process</a></li>
			@endif
		</ul>
	</div>
</div>
@endif

<script type="text/javascript" src="{{ URL::asset('js/workbench.js') }}"></script>

@if($module=='MOD_03' && in_array($request_details->status_id,['STAT_14','STAT_12']))
	
	<div class="form-section">
		<div class="container-fluid form-content">
			<div class="tab-content">
				<div id="Visa_tab" class="tab-pane active">
					@include('layouts.visa_process')
				</div>
			</div>
		</div>
	</div>
@else
	<div class="form-section ticket-process-container">
		<div class="container-fluid form-content">
			<div class="tab-content">
				<div id="ticket_process" class="tab-pane {{$ticket_active}}">
					@include('layouts.ticket_process')
				</div>
				@if($module==='MOD_02')
					<div id="forex_process" class="tab-pane {{$forex_active}}">
						@include('layouts.forex_process')
					</div>
				@endif
			</div>
		</div>
	</div>
@endif


<style>
	.is-tab-content{
		padding-right:0px;
		padding-left:0px
	}
.is-tab-content [href="#"] {
    pointer-events: none;
}
.is-tab-content .nav.nav-tabs{border-bottom: 2px solid #ddd;width: 100% !important;}
.is-tab-content .nav.nav-tabs li{margin-bottom: -3px;}
.is-tab-content .nav.nav-tabs li a {padding: 5px 44px 15px;color: var(--is-dark-grey);border: 0px;font-size: 11px;font-family: mont-semibold;}
.is-tab-content .nav.nav-tabs li a:hover {background: transparent;color: var(--is-black)!important;}
.is-tab-content .nav.nav-tabs li.active a { color: var(--is-purple)!important; border-bottom: 4px solid var(--is-purple)!important;font-size: 12px;font-family: mont-semibold;}
.is-tab-content .nav.nav-tabs li a {
	padding: 15px 35px;
}
.col-md-12{float: none!important;}
.is-tab-content .nav.nav-tabs .active a {background-color: transparent !important;}
.nav > li > a:focus, .nav > li > a:hover {
  background: none!important;
}
</style>
