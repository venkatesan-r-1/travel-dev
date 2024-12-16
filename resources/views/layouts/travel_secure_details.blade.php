@extends('header')

@section('title', 'Travel Request')
@php 
    $page_navigation_no="secure_key_generation";
    $visa_secure_key = $visa_secure_key ?? null;
@endphp
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/config.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/travel_secure_key.js') }}"></script>

@php($visa_secure_key = $visa_secure_key ?? null)
<div class="form-section secure-key-generation-container">
    <div class="container-fluid form-content">
        <div class="row">
            <div class="col-md-2 col-sm-12 col-xs-12">
                <label for="visa_secure_key" class="form-label'">Secure key generation</label>
            </div>
            <div class="col-md-10 col-sm-12 col-xs-12">
                {{ Form::text('visa_secure_key', $visa_secure_key, ['id' => 'visa_secure_key', 'class'=>'form-control form-fields']) }}
                <small class="form-text text-muted">The length should be minimum of 5 and maximum of 15</small>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-10">
              <button class="secondary-button" style="margin-right: 10px;" id="reset">Reset</button>
              <button class="primary-button" id="save" value="save">Submit</button>

            </div>
        </div>
    </div>
</div>
<style>
    .secure-key-generation-container > .form-content {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .secure-key-generation-container .form-fields {
        max-width: 300px;
    }
</style>
@endsection