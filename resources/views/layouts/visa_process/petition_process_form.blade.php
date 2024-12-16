    @if(in_array('status_info_petition_denied',$visible_fields))
    <div class="row">
        <div class="fields_div petition_process_section" id="section1" style="display:none">
            <div class="closed">
                <img src="{{asset('/images/closed-icon.svg')}}" class="closed-icon">
                <span>Petition has been denied</span>
            </div>
        </div>
    </div>
    @elseif (in_array('status_info_petition_process',$visible_fields))
    <div class="row">
        <div id="section1" class="fields_div petition_process_section" style="display:none">
            <div class="waiting">
                <img src="{{asset('images/pending-icon.svg')}}" alt="" class="pending-icon">
                <span>Waiting for Immigration team to initiate the petition process</span>
            </div>
        </div>
    </div>
    @elseif (in_array('status_info_completed', $visible_fields))
    <div class="row">
        <div id="section1" class="fields_div petition_process_section" style="display:none">
            <div class="completed">
                <img src="{{asset('/images/completed-icon.svg')}}" alt="" class="completed-icon">
                <span>Petition has been approved</span>
            </div>
        </div>
    </div>  
    @else
    <div class="row">
        <div id="section1" class="card-content fields_div petition_process_section" style="display:none">
            <div class="row">
                @if (in_array('us_manager_id',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('us_manager_id',$editable_fields))
                            <label class="required-field">Reporting manager name</label>
                            <?php echo Form::select('us_manager_id',[''=>'Select']+$us_managers,$us_manager_aceid,['id'=>'us_manager_id','class'=>'form-control myselect','required']); ?>
                        @else
                            <label class="">Reporting manager name</label>
                            <p>{{$us_manager_id}}</p>
                        @endif
                    </div>
                @endif
                @if (in_array('inszoom_id',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('inszoom_id',$editable_fields))
                            <label class="required-field">INSZoom ID</label>
                            <?php echo Form::text('inszoom_id',$inszoom_id,['id'=>'inszoom_id','class'=>'form-control alphaNum','required','style'=>'']); ?>
                        @else
                            <label class="">INSZoom ID</label>
                            <p>{{$inszoom_id}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('entity_id',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('entity_id',$editable_fields))
                            <label class="required-field">Petitioner entity</label>
                            <?php echo Form::select('entity_id',[''=>'Select']+$petitioner_entity,$petitioner_entity_id,['id'=>'entity_id','class'=>'form-control myselect','required']); ?>
                        @else
                            <label class="">Petitioner entity</label>
                            <p>{{$entity_id}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('attorneys_id',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('attorneys_id',$editable_fields))
                            <label class="required-field">Attorneys</label>
                            <?php echo Form::select('attorneys_id',[''=>'Select']+$visa_attorneys_master,$visa_attorneys_id,['id'=>'attorneys_id','class'=>'form-control myselect','required']); ?>
                        @else
                            <label class="">Attorneys</label>
                            <p>{{$attorneys_id}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('petition_file_date',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('petition_file_date',$editable_fields))
                            <label class="required-field">Petition filed date</label>
                            <?php echo Form::text('petition_file_date',$petition_file_date,['id'=>'petition_file_date','class'=>'form-control date','required','onkeydown'=>'return false']); ?>
                        @else
                            <label class="">Petition filed date</label>
                            <p>{{$petition_file_date}}</p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="row">
                @if (in_array('receipt_no',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('receipt_no',$editable_fields))
                            <label class="required-field">Receipt number</label>
                            <?php echo Form::text('receipt_no',$receipt_no,['id'=>'receipt_no','class'=>'form-control alphaNum receipt_no','required','style'=>'']); ?>
                        @else
                            <label class="">Receipt number</label>
                            <p>{{$receipt_no}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('petition_start_date',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('petition_start_date',$editable_fields))
                            <label class="required-field">Petition start date</label> 
                            <?php echo Form::text('petition_start_date',$petition_start_date,['id'=>'petition_start_date','class'=>'form-control date','required','onkeydown'=>'return false']); ?>
                        @else
                            <label class="">Petition start date</label>
                            <p id="petition_start_date_text">{{$petition_start_date}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('petition_end_date',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>                
                        @if(in_array('petition_end_date',$editable_fields))
                            <label class="required-field">Petition end date</label>
                            <?php echo Form::text('petition_end_date',$petition_end_date,['id'=>'petition_end_date','class'=>'form-control date','required','onkeydown'=>'return false']); ?>
                        @else
                            <label class="">Petition end date</label>
                            <p>{{$petition_end_date}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('petition_file', $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(in_array('petition_file', $editable_fields))
                            <label class="required-field">Petition file
                                <img src="{{asset('images/info.svg')}}" alt="info-icon" data-toggle="tooltip" data-placement="top" data-original-title="Allowed formats : PNG, JPG, PDF, DOCX"> 
                            </label>
                            <input type="text" id="petition-file-info" name="petition-file-info" value = "{{$petition_file_name}}" class="form-control file-details dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" readonly>
                            <label for="petition-file" name="petition-file"class="img-label"><img src="{{asset('images/layer.svg')}}" alt=""></label>
                            <input type="file"  id="petition-file" class='form-control custom-file-input' name="petition-file" multiple>
                            @if(isset($petition_file_details))                              
                                <div class="file-uploads-div dropdown-menu" id="petition-file-uploads-div" aria-labelledby="#petition-file-info">
                                    @foreach ($petition_file_details as $file)
                                        <div class="file-content">
                                            <a href="{{$file['filePath']}}" class="file-name" target="_blank" data-toggle='tooltip' data-placement='top' data-original-title="{{ $file['originalName'] }}">{{$file['originalName']}}</a>
                                            <span class="remove-files petition-remove-files">&times;</span>
                                            <span class="file-size">{{$file['fileSize']}}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="file-uploads-div dropdown-menu" id="petition-file-uploads-div" aria-labelledby="#petition-file-info"></div>
                            @endif
                            <input type="hidden" id="petition-file-hidden" name="petition-file-hidden" value="">
                        @else
                            <label>Petition file</label>
                            <p class="dropdown-toggle view" data-toggle="dropdown">{{$petition_file_name}}</p>
                            <div class="file-uploads-div dropdown-menu" id="petition-file-uploads-div" aria-labelledby="#petition-file-info">
                                @if(array_key_exists('petition_file_details',get_defined_vars()))
                                    @foreach ($petition_file_details as $file)
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
            </div>	
        </div>
    </div>	
    @endif
