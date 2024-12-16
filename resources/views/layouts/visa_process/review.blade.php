@extends('header')

@section('title', 'Travel Request')
@php $page_navigation_no="visa_process_review"; $tab_navigation="us_visa_process";@endphp
<link type="text/css" href="{{asset('/css/travel.css')}}" rel="stylesheet">
<link type="text/css" href="{{asset('/css/visa_process.css')}}" rel="stylesheet">
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/Datatable/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/visa_process.js') }}"></script>
<?php //dd($request_details);?>
<!-- header -->
<div class="container-fluid visa_process_container">
    <div class="tab-content table-tab-content">
        <div class="card requestdetail">
            <div class="row">
                <div class='col-md-2 col-sm-12 col-xs-12 card-header'><h3>Review</h3></div>
                <div class="col-md-2 col-sm-12 col-xs-12 search-bar-div">
                    <input type="text" name="search-bar-home-table" id="search-bar-home-table" placeholder="Search" class="form-control search-bar-home-table">
                </div>
            </div>

<!-- Header end -->
<!-- table start -->
        <div class="row table-row">
            <table class="table table-responsive" id="table_request_home">
                <thead>
                    <tr>
                        <th>Request id</th>
                        <th>Employee details</th>
                        <th>Visa type</th>
                        <th>Request type</th>
                        <th>Client details</th>
                        <th>Created on</th>
                        <th>Status</th>
                    </tr>
                </thead>
            @if(count($request_details))
                @foreach($request_details as $request)
                    @php $edit_id=Crypt::encrypt($request->request_code); @endphp
                <tr>
                    <td><a href="/visa_process/request_id/{{$edit_id}}"><span data-toggle="tooltip" data-placement = "top" data-original-title="{{$request->status}}"><img src='{{ asset("images/$request->status-icon.svg") }}' class='status-icon' style="margin-right: 5px;" ></span>{{$request->request_code}}</a></td>
                    <td>{{$request->employee_name}} - {{$request->employee_aceid}}</td>
                    <td>{{$request->visa_type}}</td>
                    <td>{{$request->request_type}}</td>
                    <td>{{$request->client_name}}</td>
                    <td>{{$request->created_at}}</td>
                    <td>{{$request->status}}</td>
                </tr>
                @endforeach
            @endif
            </table>
        </div>
<!-- Table end -->
    </div>
</div>
</div>

@endsection
