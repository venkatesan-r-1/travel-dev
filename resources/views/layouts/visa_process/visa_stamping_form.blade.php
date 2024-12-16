@if(in_array('status_info_visa_denied', $visible_fields))
    <div class="row">
        <div id="section1" class="fields_div visa_stamping_section" style="display:none">
            <div class="closed">
                <img src="{{asset('/images/closed-icon.svg')}}" alt="" class="closed-icon">
                <span>Visa has been denied</span>
            </div>
        </div>
    </div> 
    @elseif(in_array('status_info_visa_stamping',$visible_fields))
    <div class="row">
        <div id="section1" class="fields_div visa_stamping_section" style="display:none">
            <div class="waiting">
                <img src="{{asset('images/pending-icon.svg')}}" class="pending-icon">
                <span>Waiting for Global mobility team to initiate the visa process</span>
            </div>
        </div>
    </div>
    @elseif (in_array('status_info_completed',$visible_fields))
    <div class="row">
        <div id="section1" class="fields_div visa_stamping_section" style="display:none">
            <div class="completed">
                <img src="{{asset('/images/completed-icon.svg')}}" alt="" class="completed-icon">
                <span>Visa has been approved</span>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div id="section1" class="card-content fields_div visa_stamping_section" style="display:none">
            <div class="row">
                @if (in_array('visa_interview_type_id',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('visa_interview_type_id',$editable_fields))
                            <label class="required-field">Visa interview type</label>
                            <?php echo Form::select('visa_interview_type_id',[''=>'Select']+$visa_interview_type_master,$visa_interview_type_id,['id'=>'visa_interview_type_id','class'=>'form-control','required']); ?>
                        @else
                            <label class="">Visa interview type</label>
                            <p>{{$visa_interview_type}}</p>
                        @endif
                    </div>
                @endif
            
                @if (in_array('visa_ofc_date',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('visa_ofc_date',$editable_fields))
                            <label class="required-field">Appointment - OFC date</label>
                            <?php echo Form::text('visa_ofc_date',$visa_ofc_date,['id'=>'visa_ofc_date','class'=>'form-control date','required','onkeydown'=>'return false']); ?>
                        @else
                            <label class="">Appointment - OFC date</label>
                            <p>{{$visa_ofc_date}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('visa_interview_date',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('visa_interview_date',$editable_fields))
                            <label class="required-field">Appointment - Interview date</label>
                            <?php echo Form::text('visa_interview_date',$visa_interview_date,['id'=>'visa_interview_date','class'=>'form-control date','required','onkeydown'=>'return false']); ?>
                        @else
                            <label class="">Appointment - Interview date</label>
                            <p>{{$visa_interview_date}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('visa_status_id',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('visa_status_id',$editable_fields))
                            <label class="required-field">Visa status</label>
                            <?php echo Form::select('visa_status_id',[''=>'Select']+$visa_status_master,$visa_status_id,['id'=>'visa_status_id','class'=>'form-control','required']); ?>
                        @else
                            <label class="">Visa status</label>
                            <p>{{$visa_status}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('travel_date',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('travel_date',$editable_fields))
                            <label class="required-field">Travel date / US start date</label>
                            <?php echo Form::text('travel_date',$travel_date,['id'=>'travel_date','class'=>'form-control date','required','onkeydown'=>'return false']); ?>
                        @else
                            <label class="">Travel date / US start date</label>
                            <p>{{$travel_date}}</p>
                        @endif
                    </div>
                @endif
            </div>
            
            <div class="row">
                @if (in_array('travel_location',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('travel_location',$editable_fields))
                            <label class="required-field">Travel location</label>
                            <?php echo Form::text('travel_location',$travel_location,['id'=>'travel_location','class'=>'form-control name','required','style'=>'']); ?>
                        @else
                            <label class="">Travel location</label>
                            <p>{{$travel_location}}</p>
                        @endif
                    </div>
                @endif
            
                @if (in_array('visa_file', $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        <input type="hidden" id="employee_firstname" value={{$employee_first_name}}>
                        @if(in_array('visa_file', $editable_fields))
                            <label class="required-field">Visa file
                                <img src="{{asset('images/info.svg')}}" alt="info-icon" data-toggle="tooltip" data-placement="top" data-original-title="Allowed formats : PNG, JPG, PDF, DOCX"> 
                            </label>
                            <input type="text" id="visa-file-info" name="visa-file-info" value = "{{$visa_file_name}}" class="form-control file-details dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" readonly>
                            <label for="visa-file" name="visa-file"class="img-label"><img src="{{asset('images/layer.svg')}}" alt=""></label>
                            <input type="file"  id="visa-file" class='form-control custom-file-input' name="visa-file" multiple>
                            @if(isset($visa_file_details))                              
                                <div class="file-uploads-div dropdown-menu" id="visa-file-uploads-div" aria-labelledby="#visa-file-info">
                                    @foreach ($visa_file_details as $file)
                                        <div class="file-content">
                                            <a href="{{$file['filePath']}}" class="file-name" target="_blank" data-toggle='tooltip' data-placement='top' data-original-title="{{ $file['originalName'] }}">{{$file['originalName']}}</a>
                                            <span class="remove-files visa-remove-files">&times;</span>
                                            <span class="file-size">{{$file['fileSize']}}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="file-uploads-div dropdown-menu" id="visa-file-uploads-div" aria-labelledby="#visa-file-info"></div>
                            @endif
                            <input type="hidden" id="visa-file-hidden" name="visa-file-hidden" value="">
                        @else
                            <label>Visa file</label>
                            <p class="dropdown-toggle view" data-toggle="dropdown">{{$visa_file_name}}</p>
                            <div class="file-uploads-div dropdown-menu" id="visa-file-uploads-div" aria-labelledby="#visa-file-info">
                                @if(array_key_exists('visa_file_details',get_defined_vars()))
                                    @foreach ($visa_file_details as $file)
                                        <div class="file-content">
                                            <a href="{{$file['filePath']}}" class="file-name" target="_blank" data-toggle='tooltip' data-placement='top' data-original-title="{{ $file['originalName'] }}">{{$file['originalName']}}</a>
                                            <span class="file-size">{{$file['fileSize']}}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                @if (in_array('traveling_type_id',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('traveling_type_id',$editable_fields))
                            <label class="required-field">Employee traveling status</label>
                            <?php echo Form::select('traveling_type_id',[''=>'Select']+$visa_travel_type_master,$traveling_type_id,['id'=>'traveling_type_id','class'=>'form-control','required']); ?>
                        @else
                            <label class="">Employee traveling status</label>
                            <p>{{$traveling_type}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('dependency_details',$visible_fields))
                    @if(in_array('dependency_details',$editable_fields))
                        @if(is_array($dependency_details) && count($dependency_details))
                            @foreach($dependency_details as $key=>$dd)
                                @php($count = 0)
                                <div class='col-md-2 col-sm-12 col-xs-12 dependent_name hidden_textfield' id="dependent_name_div-1">
                                    <label class="required-field">{{$key}}</label>
                                    <?php echo Form::text('dependent_name_input',$dd,['id'=>'dependent_name-'.($count+1),'class'=>'form-control name','required','style'=>'']); ?>
                                    <span class='dependent_name_remove_btn' style="display:{{count($dependency_details)==1?'none':''}}"><img src="/images/minus-circle.svg" alt="Remove"></span>
                                </div>  
                            @endforeach
                        @else
                            <div class='col-md-2 col-sm-12 col-xs-12 dependent_name hidden_textfield' id="dependent_name_div-1">
                                <label class="required-field">Spouse</label>
                                <?php echo Form::text('dependent_name_input',null,['id'=>'dependent_name-1','class'=>'form-control name','required','style'=>'']); ?>
                                <span class='dependent_name_remove_btn' style='display:none;'><img src="/images/minus-circle.svg" alt="Remove"></span>
                            </div>
                        @endif
                    @else
                        @if(is_array($dependency_details) && count($dependency_details))
                            @php($count = 1)
                            @foreach($dependency_details as $key=>$dd)
                                <div class="col-md-2 col-sm-12 col-xs-12 {{ $count > 2 ? 'dependent_name_save' : ''}}">
                                    <label class="">{{$key}}</label>
                                    <p>{{$dd}}</p>
                                    @php($count++)
                                </div>
                            @endforeach
                        @endif
                    @endif
                    @if(in_array('dependency_details',$editable_fields))
                        <span id='dependent_name_add_btn' class="dependent_name_add_btn"><img src="/images/add.svg" style="/*margin-top: 27px;*/" alt="Add"></span>
                    @endif
                @endif
            </div>
            <div class="visa-stamping-notes">
            <div class="row">
                <div class="col-md-12">
                    <p class="visa-stamping-notes-content"><span class="visa-stamping-notes-header">Note : </span>As per process, We can sponser the visa cost for your spouse and two children. Since you have choose more than two children the travel cost has to be borne by you.</p>
                </div>
            </div>
        </div>
    </div>
        </div>
@endif
@if(!in_array('visa_stamping',$visible_fields))
    @if(in_array('status_info_rfe_progress',$visible_fields))
        <div class="row">
        <div id="section2" class="fields_div visa_stamping_section">
            <div class="waiting">
                <img src="{{asset('images/pending-icon.svg')}}" class="pending-icon"/>
                <span>Waiting for US team to publish the offer letter</span>
            </div>
        </div>
        </div>
    @else
        <div class="row">
            <div id = "section2" class="card-content fields_div visa_stamping_section" style="display:none">
                <div class="row">
                    @if(in_array('us_job_title_id',$visible_fields))
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label class="">Job title</label>
                            <p>{{$us_job_title}}</p>
                        </div>
                    @endif
           
                    @if(in_array('green_card_title',$visible_fields))
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            @if(in_array('green_card_title',$editable_fields))
                                <label>Green card eligibility</label> <br>
                                <label class="switch"> 
                                    <input type="checkbox" id="green_card_title" name="green_card_title" {{$green_card_title==1?'checked':''}} value = "{{$green_card_title}}">
                                    @if(!$is_file_exists)
                                        <span class="slider round"></span>
                                    @else
                                        <p id="green_card_eligibility" style="margin-top: -14px;">{{$green_card_title == 1 ? "Yes" : "No" }}</p>
                                    @endif
                                </label>
                            @else   
                                <label>Green card eligibility</label>
                                <p>{{$green_card_title == 1?"Yes":"No"}}</p>
                            @endif
                        </div>
                    @endif

                    @if(in_array('generate_offer_letter_btn',$visible_fields))
                        @if( !$is_file_exists) 
                        	<div class="col-md-2 col-sm-12 col-xs-12">
		                            <button name="generate_offer_letter" value="generate_offer_letter" id="generate"  class="secondary-button" style="margin-right:14px">Generate offer letter</button>
                	        </div>
			@endif
                    @endif
		        
                        
                    @if(in_array('offer_letter',$visible_fields))
                        <div class="col-md-2 col-sm-12 col-xs-12 offer_letter_column">
                            <label>{{$offer_letter_label}}</label>
                            <a href="{{ $offer_letter_path}}" name="offer_letter_path_link" id="link" target = "blank">{{ $offer_letter_file_name }}</a>
                        </div>
                    @endif

                    @if(in_array('immigration_offer_letter',$visible_fields))
                        <div class="col-md-2 col-sm-12 col-xs-12 offer_letter_column">
                            <label>{{$immigration_offer_letter_label}}</label>
                            <a href="{{$immigration_offer_letter_path}}" name="immigration_offer_letter_path_link" target="blank">{{$immigration_offer_letter_file_name}}</a>
                        </div>
                    @endif

                    @if(in_array('word_document',$visible_fields))
                        <div class="col-md-2 col-sm-12 col-xs-12 offer_letter_column">
                            <label>{{$word_document_label}}</label>
                            <a href="{{$word_document_path}}" name="word_document_path_link" target="blank">{{$word_document_file_name}}</a>
                        </div>
                    @endif

                    @if(in_array('offer_letter_path', $visible_fields))
                        <div class="">
                            <input type="hidden" name="offer_letter_path" id="offer_letter_location" value = "{{ $offer_letter_path}}">
                        </div>
                    @endif

                    @if(in_array('immigration_offer_letter_path',$visible_fields))
                        <div class="">
                            <input type="hidden" name="immigration_offer_letter_path" value="{{ $immigration_offer_letter_path}}">
                            <input type="hidden" name="word_document_path" value="{{ $word_document_path}}">
                        </div>
                    @endif
                </div>
                @if(in_array('password_info',$visible_fields))
                    <div class="visa-password-info">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="visa-password-info-content"><span class="visa-password-info-header">Note : </span>Offer letter protected by an 12 character password and you need to enter it in this format to view. The first three letters of your password are the first 3 letters of your first name, followed by your date of joining in DDMMMYYYY format.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endif
