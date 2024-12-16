
@if (in_array ('status_info_visa_tracking',$visible_fields))
    <div class="row">
        <div id="section1" class="fields_div completed_section">
            <div class="waiting">
                <img src="{{asset('/images/pending-icon.svg')}}" alt="" class="pending-icon">
                <span>Waiting for Global mobility team to fill the travel details</span>
            </div>
        </div>
    <div>
@else
    <div class="row">
        <div id="section1" class="card-content fields_div completed_section" style="display:none">
            <div class="row">
                @if (in_array('us_final_salary',$visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        <label>US salary</label>
                        <p>{{$currency_notation." ".number_format((float)$us_salary,2)}}</p>
                    </div>
                @endif
                @if (in_array('record_number',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('record_number',$editable_fields))
                            <label class="required-field">(I-94) Record number</label>
                            <?php echo Form::text('record_number',$i_94_record_number,['id'=>'record_number','class'=>'form-control alphaNum','required','style'=>'']); ?>
                        @else
                            <label class="">(I-94) Record number</label>
                            <p>{{$i_94_record_number}}</p>
                        @endif
                    </div>
                @endif
            
                @if (in_array('most_recent_doe',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('most_recent_doe',$editable_fields))
                            <label class="required-field">Most recent date of entry</label>
                            <?php echo Form::text('most_recent_doe',$most_recent_date_of_entry,['id'=>'most_recent_doe','class'=>'form-control date','required','onkeydown'=>'return false']); ?>
                        @else
                            <label class="">Most recent date of entry</label>
                            <p>{{$most_recent_date_of_entry}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('admit_until',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('admit_until',$editable_fields))
                            <label class="required-field">I-94 End date</label>
                            <?php echo Form::text('admit_until',$admit_until_date,['id'=>'admit_until','class'=>'form-control date','required','onkeydown'=>'return false']); ?>
                        @else
                            <label class="">I-94 End date</label>
                            <p>{{$admit_until_date}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('gc_initiated_on',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('gc_initiated_on',$editable_fields))
                            <label class="required-field">GC to be initiated on</label>
                            <?php echo Form::text('gc_initiated_on',$gc_to_be_initiated_on,['id'=>'gc_initiated_on','class'=>'form-control date','required','onkeydown'=>'return false']); ?>
                        @else
                            <label class="">GC to be initiated on</label>
                            <p>{{$gc_to_be_initiated_on}}</p>
                        @endif
                    </div>
                @endif
            </div>
        @endif
        </div>
    </div>

