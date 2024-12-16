@extends('header')

@section('title', 'Travel Request')
@php $page_navigation_no="visa_process_history"; $tab_navigation="us_visa_process";@endphp
<link type="text/css" href="{{asset('/css/travel.css')}}" rel="stylesheet">
<link type="text/css" href="{{asset('/css/visa_process.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('/css/jquery.multiselect.css')}}">
@section('content')
<script src="{{ URL::asset('js/jquery.multiselect.js') }}"></script>
<script src="{{ URL::asset('js/overall_history_table_config.js') }}"></script>
<script src="{{URL::asset('js/datatable_custom_filter.js')}}"></script>
<script type="text/javascript" src="{{ URL::asset('js/Datatable/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/Datatable/dataTables.buttons.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/Datatable/jszip.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/Datatable/buttons.html5.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/visa_process.js') }}"></script>
<div class="container-fluid visa_process_container">
    <div class="tab-content table-tab-content">
        <div class="card requestdetail">
            <div class="row">
                <div class="col-md-2 col-sm-2 col-xs-12 card-header history_header"><h3>History</h3></div>
                <div class="col-md-10 col-sm-10 col-xs-12 export-and-filter-options">
                    <div>
                        <button type="button" name='export' class="secondary-button filter_header" value="export" id="export-to-excel" data-toggle="tooltip" data-placement="left" data-original-title="Export as excel">Export</button>
                        <button type="button" class="secondary-button filter_header" id="visa_process_filter" data-target="#visa_process_filter_div" data-toggle="collapse" aria-expanded="false" aria-controls="visa_process_filter_div">Filter<span class="filter_badge">(0)</span></button>
                        <div class="filter_header">
                            <img class = "info-icon" src="{{asset('images/info.svg')}}" alt="info icon" data-toggle="tooltip" data-placement="left" data-original-title="Financial year filter based on created date">
                            <img class = "mb-info-icon" src="{{asset('images/info.svg')}}" alt="info icon" data-toggle="tooltip" data-placement="top" data-original-title="Financial year filter based on created date">
                            <?php echo Form::select('visa_process_financial_year_filter',$visa_process_financial_year_list,null,['id'=>'visa_process_financial_year_filter','class'=>'form-control']);?>
                        </div>
                    </div>
                    <?php echo Form::text('visa_process_search_bar',null,['id'=>'visa_process_search_bar','class'=>'form-control filter_header datatable_filter_input search-bar-home-table', 'placeholder'=>'Search']);?>
                    
                </div>
            </div>
            <div class="row collapse" id="visa_process_filter_div">
                <div class="card-content">
                    <div class="row">
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>Request code</label>
                            <?php echo Form::text('visa_process_request_code_filter',null,['id'=>'visa_process_request_code_filter','class'=>'form-control filter-fields']); ?>
                        </div>
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>Submitted from</label>
                            <?php echo Form::text('submitted_from_date',null,['id'=>'submitted_from_date','class'=>'date filter-fields filter-fields','onkeydown'=>'return false']);?>
                        </div>
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>Submitted to</label>
                            <?php echo Form::text('submitted_to_date',null,['id'=>'submitted_to_date','class'=>'date filter-fields','onkeydown' => 'return false']);?>
                        </div>
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>Employee</label>
                            <?php echo Form::text('visa_process_employee_filter',null,['id'=>'visa_process_employee_filter','class'=>'form-control filter-fields']);?>
                        </div>
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>Status</label>
                            <select name="visa_process_status_filter" id="visa_process_status_filter" multiple="" class="filter-fields"></select>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>Visa type</label>
                            <select name="visa_process_visa_type_filter" id="visa_process_visa_type_filter" multiple="" class="filter-fields"></select>
                        </div>
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>Request type</label>
                            <select name="visa_process_request_type_filter" id="visa_process_request_type_filter" multiple="" class="filter-fields"></select>
                        </div>
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>Client details</label>
                            <select name="visa_process_client_name_filter" id="visa_process_client_name_filter" multiple="" class="filter-fields"></select>
                        </div>
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>Petition entity</label>
                            <select name="visa_process_petiton_entity_filter" id="visa_process_petiton_entity_filter" multiple="" class="filter-fields"></select>
                        </div>
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>India manager</label>
                            <?php echo Form::text('visa_process_india_manager_filter',null,['id'=>'visa_process_india_manager_filter','class'=>'form-control filter-fields']);?>
                        </div>
                    </div>
                    <div class="row">
                        <button type="button" class="primary-button filter-action-buttons" id="filterApply">Apply</button>
                        <button type="button" class="secondary-button filter-action-buttons" id="filterReset">Cancel</button>
                    </div>
                </div>	
            </div>
            <div id="all" class="row table-row visa_process_history_table-div">
                <table class="table table-responsive" id="visa_process_history_table">
                </table>
            </div>
        </div>
    </div>
</div>	
<script>
    $(document).on('click','#export-to-excel',function () {
        $('#all .buttons-excel').click();
    })
</script>
@endsection
