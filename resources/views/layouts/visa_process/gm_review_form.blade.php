@if( in_array('status_info_gm_review',$visible_fields))
<div class="row">
    <div id="section1" class="fields_div gm_review_section" style="display:none;">
        <div class="waiting">
            <img src="{{asset('images/pending-icon.svg')}}" alt="" class="pending-icon">
            <span>Waiting for Immigration team to review the details</span>
        </div>
    </div>
</div>
@elseif (in_array('status_info_completed', $visible_fields))
    <div class="row">
        <div id="section1" class="fields_div gm_review_section" style="display:none;">
            <div class="completed">
                <img src="{{ asset('/images/completed-icon.svg') }}" alt="" class="completed-icon">
                <span>Immigration eligibility review has been completed</span>
            </div>
        </div>
    </div>
@else
<div class="row">
    <div id="section1" class="card-content fields_div gm_review_section" style="display:none">
        <div class="row">
        @if (in_array('minimum_wage',$visible_fields))
        <div class='col-md-2 col-sm-12 col-xs-12'>
            @if(in_array('minimum_wage',$editable_fields))
            <label class="required-field">Minimum annual wage</label>
            <div class="input-group mb-3">
                <div class="input-group-addon">
                    <span class="input-group-text">{{$currency_notation}}</span>
                </div>
                <?php echo Form::text('minimum_wage',$minimum_wage,['id'=>'minimum_wage','class'=>'form-control floatingpoint','required']); ?>
            </div>
                
            @else
                <label class="">Minumum annual wage</label>
                <p id="minimum_wage_text">{{$currency_notation." ".number_format((float)$minimum_wage,2)}}</p>
            @endif
            </div>
        @endif

        @if (in_array('work_location',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
            @if(in_array('work_location',$editable_fields))
                <label class="required-field">Work location</label>
                <?php echo Form::textarea('work_location',$work_location,['id'=>'work_location','class'=>'form-control text']); ?>
            @else
                <label class="">Work location</label>
                <p>{{$work_location}}</p>
            @endif
            </div>
        @endif

        @if (in_array('job_titile_id',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
            @if(in_array('job_titile_id',$editable_fields))
                <label class="required-field">Job title</label>
                <?php echo Form::select('job_titile_id',[''=>'Select']+$job_title_master,$job_title_id,['id'=>'job_title_id','class'=>'form-control myselect','required']); ?>
            @else
                <label class="">Job title</label>
                <p>{{$job_title}}</p>
            @endif
            </div>
        @endif

        @if (in_array('filing_type_id',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
            @if(in_array('filing_type_id',$editable_fields))
                <label class="required-field">Filing type</label>
                <?php echo Form::select('filing_type_id',[''=>'Select']+$visa_filing_master,$filing_type_id,['id'=>'filing_type_id','class'=>'form-control myselect','required']); ?>
            @else
                <label class="">Filing type</label>
                <p>{{$filing_type}}</p>
            @endif
            </div>
        @endif

        @if (in_array('gm_remarks',$visible_fields))
                <div class='col-md-2 col-sm-12 col-xs-12'>
                    <label class="">Remarks</label>
                @if(in_array('gm_remarks',$editable_fields))
                    <?php echo Form::textarea('remarks',$gm_remarks,['id'=>'gm_remarks','class'=>'form-control']); ?>
                @else
                    <p>{{$gm_remarks}}</p>
                @endif
                </div>
            @endif

        </div>
    </div>
</div>
@endif
