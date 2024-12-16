<html>
<head>
<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">	
  <meta name="viewport" content="width=device-width, initial-scale=1">

<title>@yield('title', 'Travel systems')</title>
<link rel="icon" href="/img/mobile-logo-voilet.svg" type="image/x-icon">


<link type="text/css" href="{{ asset('css/bootstrap-datepicker.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
<link type="text/css" href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">

<script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap_tooltip.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap_confirmation.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/proof_details.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/currency_format.js') }}"></script>

{{-- <script src="{{ asset('js/bootstrap.min.js') }}"></script> --}}

<style>
.navbar-inverse .navbar-nav > .open > a, .navbar-inverse .navbar-nav > .open > a:hover, .navbar-inverse .navbar-nav > .open > a:focus {background-color: transparent;}
#reports_ui a.active {
border-left: 5px solid var(--is-purple)!important;
border-bottom:none!important
}
</style>
    
	</head>
<nav class="navbar navbar-inverse navbar-fixed-top wow ">
	
		<div class="container-fluid wow animated fadeInDown">
			<div class="row">
		  		<div class="col-md-12 col-sm-12">
					<div class="navbar-header">
			  			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>                        
			  			</button>
			  			<a><img src="/img/logo-voilet.svg" alt="aspire systems" class="img-responsive desktop-logo" style="width: 110px;"><img src="/img/mobile-logo-voilet.svg" alt="aspire systems" class="img-responsive mobile-logo"><p>Travel system</p>
			  			</a>
						@if(Auth::User())
			  			<div class="social-line1 dropdown">
							<p class="dropdown-toggle" data-toggle="dropdown">{{Auth::user()->username}}<span><img src="/img/chevron-down.svg"></span>
							</p>
							<ul class="dropdown-menu">
								<li><a class="dropdown-toggle policy-submenu" data-toggle="dropdown" href="#">Policies<span class="caret" style="float:right;margin-top:6px"></span></a>

								</li>
								<li><a href="/auth/logout">Logout</a></li>
							</ul>
			  			</div>
						
					</div>
		  		</div>
			</div>
	  	</div>
		<!-- US visa process related nav -->
		{{-- @if(Auth::User()->has_any_role_code(['HR_PRT', 'VIS_USR', 'GM_REV', 'US_HR_REV' ]))
			<div class="wow animated fadeInDown menu_link container-fluid">	
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="collapse navbar-collapse" id="myNavbar1">
								<ul class="nav navbar-nav">
									<li class="{{ !isset($tab_navigation) || $tab_navigation != 'us_visa_process' ? 'active' : null }}"><a href="/home" id="travel_process_menu" class="header_nav">Travel request</a></li>
									<li class=" {{ isset($tab_navigation) && $tab_navigation == 'us_visa_process' ? 'active'  : null}}"><a href="/visa_process/home" id="visa_process_menu" class="header_nav">US visa process</a></li>
									<li></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endif --}}
		
		<?php
			// dd($page_navigation_no);
			// if( isset($tab_navigation) ) dd($tab_navigation);
		?>
		<div class="wow animated fadeInDown menu_link container-fluid" style="background-color: #F0F0F0;">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="collapse navbar-collapse" id="myNavbar">
								<ul class="nav navbar-nav">
									<!--<li class="{{ $page_navigation_no=='request_view' ? 'active' : '' }}"><a href="/home">Home</a></li>
									<li><a>Visa request</a></li>
									<li class="{{ $page_navigation_no=='review' ? 'active' : '' }}"><a href="/review">Review</a></li>
									<li class="{{ $page_navigation_no=='travel_request' ? 'active' : '' }}"><a href="/request">Travel request</a></li>
									<li class="{{ $page_navigation_no=='approver_details' ? 'active' : '' }}"><a href="/approver_details">Approval</a></li>
									<li class="{{ $page_navigation_no=='travel_desk' ? 'active' : '' }}"><a href="/traveldesk_details">Traveldesk</a></li>	
									<li class="{{ $page_navigation_no=='workbench' ? 'active' : '' }}"><a href="/workbench_details">Workbench</a></li>
									<li class="{{ $page_navigation_no=='reports' ? 'active' : '' }}"><a  href="/report">Report</a></li>-->
									<li class="{{ $page_navigation_no=='request_view' ? 'active' : '' }}"><a href="/home">Home</a></li>
									<li class="{{ $page_navigation_no=='visa_request' ? 'active' : '' }}"><a href="/visa_request">Visa request</a></li>
									<li class="{{ $page_navigation_no=='travel_request' ? 'active' : '' }}"><a href="/request">Travel request</a></li>
									@if(Auth::user()->has_any_role_code(['BF_REV','AP_REV']))
									<li class="{{ $page_navigation_no=='review' ? 'active' : '' }}"><a href="/review">Review</a></li>
									@endif
									@if(Auth::user()->has_any_role_code(['PRO_OW','PRO_OW_HIE','DU_H','DU_H_HIE','DEP_H','FIN_APP','CLI_PTR','GEO_H']))
									<li class="{{ $page_navigation_no=='approver_details' ? 'active' : '' }}"><a href="/approval">Approval</a></li>
									@endif
									@if(Auth::user()->has_any_role_code(['AN_COST_FAC','AN_COST_FIN']))
									<li class="{{ $page_navigation_no=='travel_desk' ? 'active' : '' }}"><a href="/traveldesk">Traveldesk</a></li>  
									@endif
									@if(Auth::User()->has_any_role_code(['HR_PRT','HR_REV']))
									<li class="{{ $page_navigation_no=='hr_review' ? 'active' : '' }}"><a href="/hr_review">HR review</a></li>  
									@endif
									@if(Auth::User()->has_any_role_code(['GM_REV', 'AN_COST_VISA']))
									<li class="{{ $page_navigation_no=='gm_review' ? 'active' : '' }}"><a href="/gm_review">Immigration review</a></li>  
									@endif
									@if(Auth::user()->has_any_role_code(['TRV_PROC_TICKET','TRV_PROC_VISA','TRV_PROC_FOREX']))
									<li class="{{ $page_navigation_no=='workbench' ? 'active' : '' }}"><a href="/workbench">Workbench</a></li>
									@endif
									@if(Auth::User()->hasReportAccess())
									<li class=""><a href="#" class="dropdown-toggle {{ $page_navigation_no=='reports' ? 'active' : '' }}" data-toggle="dropdown" id="reports_ui">Reports</a>
										<ul class="dropdown-menu" style="right: 0;top:100%;">
											@if(Auth::User()->hasReportAccess("TRAVEL"))
												<li class="{{ $page_navigation_no=='reports' ? 'active' : '' }}">
													<a href="/report">Travel report</a>
												</li>
											@endif
											@if (Auth::User()->hasReportAccess("VISA"))
												<li class="{{ $page_navigation_no=='visa_reports' ? 'active' : '' }}">
													<a href="/visa_reports">Visa report</a>
												</li>
											@endif
										</ul>
									</li>
									@endif
					@if(Auth::user()->has_any_role_code(['BLOCK_ACC', 'VIS_PCS_ADM']))
						<li class="has_sub_menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Administration
								{{-- <span class="caret"></span> --}}
							</a>
							<ul class="dropdown-menu">
								@if(Auth::user()->has_any_role_code(['BLOCK_ACC']))
									<li class="{{ $page_navigation_no=='block_users' ? 'active' : '' }}"><a href="/blocked_users">Block users</a></li>	
								@endif
								@if(Auth::User()->has_any_role_code(['VIS_PCS_ADM']))
									<li class="{{ $page_navigation_no=='block_users' ? 'active' : '' }}"><a href="/secure_key_generation">Secure key generation</a></li>	
								@endif
							</ul>
						</li>
					@endif
									<li class="{{ $page_navigation_no=='travel_reimbursement' ? 'active' : '' }}"><a  href="/travel_reimbursement">Travel reimbursement</a></li>
									
									<br>
									<p class="mob-extra-menu" style="font-size: 14px;color: #8d599f;">Username</p>
									<li class="mob-extra-menu"><a data-toggle="modal" data-target="#myModal">Settings</a></li>
									<li class="mob-extra-menu"><a>Logout</a></li>
								</ul>
							</div>
						</div>
					</div>
				</div> 
		</div>  
		  @endif 
	</nav>
	
	<div class="ui-wait"><img src="{{ asset('img/loading.gif') }}"></div>
    <div class="alert alert-danger">
        <div class="message"></div>
        <div class="close-btn"><img src="{{ asset('images/close-red.svg') }}"></div>
    </div>    
    <div class="alert alert-success">
        <div class="message"></div>
        <div class="close-btn"><img src="{{ asset('images/close-green.svg') }}"></div>
    </div>
	@php
		$session_token = session()->get('user_other_details') ?? null;
		$session_token = $session_token ? Crypt::encryptString($session_token) : null;
	@endphp
	<input type="hidden" id="user_details_token" value="{{$session_token}}">
	@yield('content')
	<footer>
		<center><p>Copyrights © {{ date("Y") }} Aspire Systems</p></center>
	</footer>
	<script>
	$(document).ready(function(){
		var message=localStorage.getItem('message');
		if(message){
			$('.alert-success').html(message+"<span class='alert_close'><img title='Close alert' src='/images/close-green.svg' class=''></span>").show();
			localStorage.clear();
			}
			setTimeout(function() {
			$(".alert-success").hide()
			}, 10000);
		$('.dropdown-submenu a.test').on("click", function(e){
		$(this).next('ul').toggle();
		e.stopPropagation();
		e.preventDefault();
		});
	});
	</script>

	</html>
