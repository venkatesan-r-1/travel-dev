@extends('header')
@section('title', 'Travel system')
@section('content')

@php
    $page_navigation_no = "block_users";
@endphp

<link rel="stylesheet" href="{{ asset('css/dataTables.css') }}">
<link rel="stylesheet" href="{{ asset('css/jquery-confirm.min.css') }}">
<script src="{{ asset('js/custom_datatable.js') }}"></script>
<script src="{{ asset('js/Datatable/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/Slimscrollbothaxis.js') }}"></script>
<script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
<script src="{{ asset('js/confirmation-box.js') }}"></script>
<script src="{{ asset('js/block_users.js') }}"></script>

<div class="container-fluid blocked_users_section" style="margin-top: 20px">
    <div class="row">
        <div class="col-md-6">
            <div class="block-user-header">
                <h3>Users</h3>
                <input type="text" class="form-control table-search" id="block-user-search" placeholder="Search">
            </div>
            <table id="block_users"></table>
        </div>
        <div class="col-md-6">
            <div class="block-user-header">
                <h3>Blocked users</h3>
                <input type="text" class="form-control table-search" id="unblock-user-search" placeholder="Search">
            </div>
            <table id="blocked_users"></table>
        </div>
    </div>
</div>
<style>
    
</style>

@endsection
