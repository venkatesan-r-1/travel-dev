@if (in_array('status_info_us_hr_negotiation',$visible_fields) || in_array('status_info_us_hr_partner_negotiation',$visible_fields))
    <div class="row">
        <div id="section1" class="fields_div hr_review_section" style="display:none">
            <div class="waiting">
            <img src="{{asset('/images/pending-icon.svg')}}" alt="" class="pending-icon">
                <span>Waiting for US team to initiate the Salary discussion process</span>
            </div>
        </div>
    </div>
    @elseif (in_array('status_info_declined',$visible_fields))
    <div class="row">
        <div id="section1" class="fields_div hr_review_section" style="display:none">
            <div class="closed">
                <img src="{{asset('/images/closed-icon.svg')}}" alt="" class="closed-icon">
                <span>User declined the offer</span>
            </div>
        </div>
    </div>
    @elseif (in_array('status_info_offshore_hr_negotiation',$visible_fields))
    <div class="row">
        <div id="section1" class="fields_div hr_review_section" style="display:none">
            <div class="waiting">
                <img src="{{asset('images/pending-icon.svg')}}" alt="" class="pending-icon">
                <span>Waiting for HR team to conclude the final US salary</span>
            </div>
        </div>
    </div>
    @elseif(in_array('status_info_completed',$visible_fields) || in_array('status_info_gm_reviewer_completed',$visible_fields))
    <div class="row">
        <div id="section1" class="fields_div hr_review_section" style="display:none">
            <div class="completed">
                <img src="/images/completed-icon.svg" alt="" class="completed-icon">
                <span>Salary discussion process has been completed</span>
            </div>
        </div>
    </div>
@else
<div class="row">
    <div id="section1" class="card-content fields_div hr_review_section" style="display:none">
        <div class="row">
        @if (in_array('india_experience',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
                <label class="">Aspire experience</label>
                <p>{{$ind_exp_year}}{{$ind_exp_month}}</p>
            </div>
        @endif
        
        @if (in_array('overall_experience',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
                <label class="">Overall experience</label>
                <p>{{$overall_exp_year}}{{$overall_exp_month}}</p>
            </div>
        @endif

        @if (in_array('band_detail',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
                <label class="">Band details</label>
                <p>{{$band_detail}}</p>
            </div>
        @endif
        
        @if (in_array('salary_range_from',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
            @if(in_array('salary_range_from',$editable_fields))
                <label class="required-field">US salary range</label></br>
                <div class="input-group mb-2">
                    <div class="input-group-addon">
                        <span class="input-group-text">{{$currency_notation}}</span>
                    </div>
                    <?php echo Form::text('salary_range_from',$salary_range_from,['id'=>'salary_range_from','class'=>'form-control floatingpoint','required','placeholder'=>'From'])."<span class='salary-range-span'>to</span>"; ?>
            @else
                <label class="">US salary range</label>
                <p id="us_salary_range">{{$currency_notation." ".number_format((float)$salary_range_from,2)}} to {{$currency_notation." ".number_format((float)$salary_range_to,2)}}</p>
            @endif

            @if (in_array('salary_range_to',$visible_fields))
            @if(in_array('salary_range_to',$editable_fields))
                <?php echo Form::text('salary_range_to',$salary_range_to,['id'=>'salary_range_to','class'=>'form-control floatingpoint','required','placeholder'=>'To']); ?>
            </div>
            @endif  
            @endif
            </div>
        @endif

        @if (in_array('us_job_title_id',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
            @if(in_array('us_job_title_id',$editable_fields))
                <label class="required-field">Aspire US job title</label>
                <?php echo Form::select('us_job_title_id',[''=>'Select']+$aspire_job_title_master,$us_job_title_id,['id'=>'us_job_title_id','class'=>'form-control myselect','required']); ?>
            @else
                <label class="">Aspire US job title</label>
                <p>{{$us_job_title}}</p>
            @endif
            </div>
        @endif
        </div>
</div> 
</div>  

<!-- India Hr review for the US salary discussion -->
@if(!in_array('Offshore_hr_salary_negotiation',$visible_fields))
@if(in_array('status_info_offshore_hr_negotiation',$visible_fields) || in_array('status_info_offshore_hr_partner_negotiation',$visible_fields))
    <div class="row">
        <div id="section2" class="fields_div hr_review_section" style="display:none">
            <div class="waiting">
                <img src="{{asset('/images/pending-icon.svg')}}" alt="" class="pending-icon">
                <span>Waiting for HR team to conclude the final US salary</span>
            </div>
        </div>
    </div>
@elseif(in_array('status_info_completed',$visible_fields) || in_array('status_info_gm_reviewer_completed',$visible_fields))
    <div class="row">
        <div id="section2" class="card-content fields_div hr_review_section" style="display:none">
            <div class="row completed">
            <img src="/images/completed-icon.svg" alt="">
                <p>Salary discussion process has been completed</p>
            </div>
        </div>
    </div>

@else
<div class="row">
    <div id="section2" class="card-content fields_div hr_review_section" style="display:none">
        <div class="row">
        @if (in_array('acceptance_by_user',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
            @if(in_array('acceptance_by_user',$editable_fields))
                <!-- <label class="required-field">Accepted the undertaking condition?</label> -->
                <label class="required-field">Undertaken completed</label>
                <label class="switch">
                    <input type="checkbox" id="acceptance_by_user" name="acceptance_by_user" {{$acceptance_by_user==1?'checked':''}} value="{{$acceptance_by_user}}">
                    <span class="slider round"></span> 
                </label>
            @else
                <!-- <label class="">Accepted the undertaking condition?</label> -->
                <label>Undertaken completed</label>
                <p>{{$acceptance_by_user==1?'Yes':'No'}}</p>
            @endif
            </div>
        @endif
        
        @if (in_array('us_salary',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
            @if(in_array('us_salary',$editable_fields))
                <label class="required-field">US salary</label>
                <div class="input-group mb-3">
                    <div class="input-group-addon">
                        <span class="input-group-text">{{$currency_notation}}</span>
                    </div>
                    <?php echo Form::text('us_salary',$us_salary,['id'=>'us_salary','class'=>'form-control floatingpoint','required','style'=>'float:left;width:43%']); ?>
                </div>
            @else
                <label class="">US salary</label>
                <p>{{$currency_notation." ".number_format((float)$us_salary,2)}}</p>
            @endif
            </div>
        @endif

        @if (in_array('one_time_bonus',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
                <label class="">One time bonus</label>
            @if(in_array('one_time_bonus',$editable_fields))
                <div class="input-group mb-3">
                    <div class="input-group-addon">
                        <span class="input-group-text">{{$currency_notation}}</span>
                    </div>
                    <?php echo Form::text('one_time_bonus',$one_time_bonus,['id'=>'one_time_bonus','class'=>'form-control floatingpoint','required','style'=>'float:left;width:43%']); ?>
                </div>
            @else
                <p>{{$currency_notation." ".number_format((float)$one_time_bonus,2)}}</p>   
            @endif
            </div>
        @endif

        @if (in_array('one_time_bonus_payout_date', $visible_fields))
            <div class="col-md-2 col-sm-12 col-xs-12">
                <label>One time bonus payment date</label>
                @if(in_array('one_time_bonus_payout_date', $editable_fields))
                    <?php echo Form::text('one_time_bonus_payout_date', $one_time_bonus_payout_date, ['id'=>'one_time_bonus_payout_date','class'=>'form-control date']); ?>
                @else
                    <p id="one_time_bonus_payment_date_text">{{ $one_time_bonus_payout_date }}</p>
                @endif
            </div>
        @endif
        
        @if (in_array('next_salary_revision_on',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
                <label class="">Next salary revision on</label>
            @if(in_array('next_salary_revision_on',$editable_fields))
                <?php echo Form::text('next_salary_revision_on',$next_salary_revision_on,['id'=>'next_salary_revision_on','class'=>'form-control date', 'onkeydown' => 'return false']); ?>
            @else
                <p>{{$next_salary_revision_on}}</p>
            @endif
            </div>
        @endif

        @if (in_array('hr_review_remarks',$visible_fields))
            <div class='col-md-2 col-sm-12 col-xs-12'>
                <label class="">Remarks</label>
            @if(in_array('hr_review_remarks',$editable_fields))
                <?php echo Form::textarea('remarks',$hr_review_remarks,['id'=>'remarks','class'=>'form-control']); ?>
            @else
                <p>{{$hr_review_remarks}}</p>
            @endif
            </div>
        @endif
        </div>
    </div>
</div>
@endif
@endif
@endif
