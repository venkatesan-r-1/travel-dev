<div class="row">
    @if(in_array('status_info_personal_info',$visible_fields))
        <div class="fields_div personal_info_section" id="section1" style="dislay:none">
            <div class="waiting">
                <img src="{{asset('images/pending-icon.svg')}}" alt="" class="pending-icon">
                <span>Waiting for employee to fill the personal details</span>
            </div>
        </div>    
    @else
        <div id="section1" class="card-content fields_div personal_info_section" style="display:none">
            <div class="row">
                @if (in_array('first_name',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('first_name',$editable_fields))
                            <label class="required-field">First name (as per passport)</label>
                            <?php echo Form::text('first_name',$first_name,['id'=>'first_name','class'=>'form-control name','required']); ?>
                        @else
                            <label class="">First name (as per passport)</label>
                            <p>{{$first_name}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('last_name',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('last_name',$editable_fields))
                            <label class="required-field">Last name (as per passport)</label>
                            <?php echo Form::text('last_name',$last_name,['id'=>'last_name','class'=>'form-control name','required']); ?>
                        @else
                            <label class="">Last name (as per passport)</label>
                            <p>{{$last_name}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('gender',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('gender',$editable_fields))
                            <label class="required-field">Gender</label>
                            <?php echo Form::select('gender_id',[''=>'Select']+$gender_master,$gender_id,['id'=>'gender','class'=>'form-control','required']); ?>
                        @else
                            <label class="">Gender</label>
                            <p>{{$gender}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('dob',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('dob',$editable_fields))
                            <label class="required-field">Date of birth</label>
                            <input type="text" class="form-control date" name="dob" id="dob" value="{{$dob}}" style="width:82%;" onkeydown="return false" />
                        @else
                            <label class="">Date of birth</label>
                            <p>{{$dob}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('doj',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('doj',$editable_fields))
                            <label class="required-field">Date of joining (in aspire)</label>
                            <input type="text" class="form-control date" name="doj" id="doj" value="{{$doj}}" style="width:82%;" onkeydown="return false" />
                        @else
                            <label class="">Date of joining (in aspire)</label>
                            <p id="doj_text">{{$doj}}</p>
                        @endif
                    </div>
                @endif
	
            </div>
            <div class="row">
                @if (in_array('address',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('address',$editable_fields))
                            <label class="required-field">Permanent residential address</label>
                            <?php echo Form::textarea('address',$address,['id'=>'address','class'=>'form-control']); ?>
                        @else
                            <label class="">Permanent residential address</label>
                            <p>{{$address}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('passport_no',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('passport_no',$editable_fields))
                            <label class="required-field">Passport number</label>
                            <?php echo Form::text('passport_no',$passport_no,['id'=>'passport_no','class'=>'form-control alphaNum','required']); ?>
                        @else
                            <label class="">Passport number</label>
                            <p>{{$passport_no}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('education',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('education',$editable_fields))
                            <label class="required-field">Education qualification</label>
                            <?php echo Form::select('education_category',[''=>'Select']+$education_category_master,$education_category_id,['id'=>'education_category','class'=>'form-control myselect']);?>
                            <?php echo Form::select('education',[''=>'Select']+$education_master,$education_details_id,['id'=>'education','class'=>'form-control myselect','required','disabled']); ?>
                        @else
                            <label class="">Education qualification</label>
                            <p>{{$education_category ?? ""}} - {{$education}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('india_experience',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('india_experience',$editable_fields))
                            <label class="required-field">Aspire experience</label><br>
                            <?php echo Form::text('india_experience_year',$ind_exp_year_in_num,['id'=>'india_experience_year','class'=>'form-control years','required','style'=>'float:left;width:43%','placeholder'=>'Year','disabled']); ?>
                            <?php echo Form::text('india_experience_month',$ind_exp_month_in_num,['id'=>'india_experience_month','class'=>'form-control month','required','style'=>'float:left;margin-left:7px;width:43%','placeholder'=>'Month','disabled']); ?>
                        @else
                            <label class="">Aspire experience</label>
                            <p>{{$ind_exp_year}}{{$ind_exp_month}}</p>
                        @endif
                    </div>
                @endif
            
                @if (in_array('overall_experience',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('overall_experience',$editable_fields))
                            <label class="required-field">Overall experience</label><br>
                            <?php echo Form::text('overall_experience_year',$overall_exp_year_in_num,['id'=>'overall_experience_year','class'=>'form-control years','required','style'=>'float:left;width:43%','placeholder'=>'Year']); ?>
                            <?php echo Form::text('overall_experience_month',$overall_exp_month_in_num,['id'=>'overall_experience_month','class'=>'form-control month','required','style'=>'float:left;margin-left:7px;width:43%','placeholder'=>'Month']); ?>
                        @else
                            <label class="">Overall experience</label>
                            <p>{{$overall_exp_year}}{{$overall_exp_month}}</p>
                        @endif
                    </div>
                @endif
            </div>

            <div class='row'>
                @if (in_array('band_detail',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        @if(in_array('band_detail',$editable_fields))
                            <label class="required-field">Band details</label>
                            <?php echo Form::hidden('band_details',$band_detail,['id'=>'band_details','class'=>'form-control','disabled']); ?>
                        @else
                            <label class="">Band details</label>
                            <p>{{$band_detail}}</p>
                        @endif
                    </div>
                @endif

                @if (in_array('emp_remarks',$visible_fields))
                    <div class='col-md-2 col-sm-12 col-xs-12'>
                        <label class="">Remarks</label>
                        @if(in_array('emp_remarks',$editable_fields))
                            <?php echo Form::textarea('remarks',$employee_remarks,['id'=>'remarks','class'=>'form-control']); ?>
                        @else
                            <p>{{$employee_remarks}}</p>
                        @endif
                    </div>
                @endif        

                @if (in_array('passport_file', $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(in_array('passport_file', $editable_fields))
                            <label class="required-field">Passport file
                                <img src="{{asset('images/info.svg')}}" alt="info-icon" data-toggle="tooltip" data-placement="top" data-original-title="Allowed formats : PNG, JPG, PDF, DOCX"> 
                            </label>
                            <input type="text" id="passport-file-info" name="passport-file-info" value = "{{$passport_file_name}}" class="form-control file-details dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" readonly>
                            <label for="passport-file" name="passport-file"class="img-label"><img src="{{asset('images/layer.svg')}}" alt=""></label>
                            <input type="file"  id="passport-file" class='form-control custom-file-input' name="passport-file" multiple>
                            @if(isset($passport_file_details))                              
                                <div class="file-uploads-div dropdown-menu" id="passport-file-uploads-div" aria-labelledby="#passport-file-info">
                                    @foreach ($passport_file_details as $file)
                                        <div class="file-content">
                                            <a href="{{$file['filePath']}}" class="file-name" target="_blank" data-toggle='tooltip' data-placement='top' data-original-title="{{ $file['originalName'] }}">{{$file['originalName']}}</a>
                                            <span class="remove-files passport-remove-files">&times;</span>
                                            <span class="file-size">{{$file['fileSize']}}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="file-uploads-div dropdown-menu" id="passport-file-uploads-div" aria-labelledby="#passport-file-info"></div>
                            @endif
                            <input type="hidden" id="passport-file-hidden" name="passport-file-hidden" value="">
                        @else
                            <label>Passport file</label>
                            <p class="dropdown-toggle view" data-toggle="dropdown">{{$passport_file_name}}</p>
                            <div class="file-uploads-div dropdown-menu" id="passport-file-uploads-div" aria-labelledby="#passport-file-info">
                                @if(array_key_exists('passport_file_details',get_defined_vars()))
                                    @foreach ($passport_file_details as $file)
                                        <div class="file-content">
                                            <a href="{{$file['filePath']}}" class="file-name" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="{{ $file['originalName'] }}">{{$file['originalName']}}</a>
                                            <span class="file-size">{{$file['fileSize']}}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                @if (in_array('cv_file', $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(in_array('cv_file', $editable_fields))
                            <label class="required-field">CV file 
                                <img src="{{asset('images/info.svg')}}" alt="info-icon" data-toggle="tooltip" data-placement="top" data-original-title="Allowed formats : DOCX"> 
                            </label>
                            <input type="text" id="cv-file-info" name="cv-file-info" value = "{{$cv_file_name}}" class="form-control file-details dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" readonly>
                            <label for="cv-file" class="img-label"><img src="{{asset('images/layer.svg')}}" alt=""></label>
                            <input type="file"  id="cv-file" name="cv-file" class='form-control custom-file-input' name="cv-file" value="" multiple>
                            @if(isset($cv_file_details))
                                <div class="file-uploads-div dropdown-menu" id="cv-file-uploads-div" aria-labelledby="#cv-file-info">
                                    @foreach ($cv_file_details as $file)
                                        <div class="file-content">
                                            <a href="{{$file['filePath']}}" class="file-name" target="_blank" data-toggle="tooltip" data-original-title="{{ $file['originalName'] }}">{{$file['originalName']}}</a>
                                            <span class="remove-files cv-remove-files">&times;</span>
                                            <span class="file-size">{{$file['fileSize']}}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="file-uploads-div dropdown-menu" id="cv-file-uploads-div" aria-labelledby="#cv-file-info"></div>
                            @endif
                            <input type="hidden" id="cv-file-hidden" name="cv-file-hidden" value="">
                        @else
                            <label>CV file</label>
                            <p class="dropdown-toggle view" data-toggle="dropdown">{{$cv_file_name}}</p>
                            <div class="file-uploads-div dropdown-menu" id="cv-file-uploads-div" aria-labelledby="#cv-file-info">
                                @if(array_key_exists('cv_file_details',get_defined_vars()))
                                    @foreach ($cv_file_details as $file)
                                        <div class="file-content">
                                            <a href="{{$file['filePath']}}" class="file-name" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="{{ $file['originalName'] }}">{{$file['originalName']}}</a>
                                            <span class="file-size">{{$file['fileSize']}}</span>
                                        </div>                                    
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                @if (in_array('degree_file', $visible_fields))
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        @if(in_array('degree_file', $editable_fields))
                            <label class="required-field">Degree certificate
                                <img src="{{asset('images/info.svg')}}" alt="info-icon" data-toggle="tooltip" data-placement="top" data-original-title="Allowed formats : PNG, JPG, PDF, DOCX"> 
                            </label>
                            <input type="text" id="degree-file-info" name="degree-file-info" value = "{{$degree_file_name}}" class="form-control file-details dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" readonly>
                            <label for="degree-file" name="degree-file"class="img-label"><img src="{{asset('images/layer.svg')}}" alt=""></label>
                            <input type="file"  id="degree-file" class='form-control custom-file-input' name="degree-file" multiple>
                            @if(isset($degree_file_details))                              
                                <div class="file-uploads-div dropdown-menu" id="degree-file-uploads-div" aria-labelledby="#degree-file-info">
                                    @foreach ($degree_file_details as $file)
                                        <div class="file-content">
                                            <a href="{{$file['filePath']}}" class="file-name" target="_blank" data-toggle='tooltip' data-placement='top' data-original-title="{{ $file['originalName'] }}">{{$file['originalName']}}</a>
                                            <span class="remove-files degree-remove-files">&times;</span>
                                            <span class="file-size">{{$file['fileSize']}}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="file-uploads-div dropdown-menu" id="degree-file-uploads-div" aria-labelledby="#degree-file-info"></div>
                            @endif
                            <input type="hidden" id="degree-file-hidden" name="degree-file-hidden" value="">
                        @else
                            <label>Degree certificate</label>
                            <p class="dropdown-toggle view" data-toggle="dropdown">{{$degree_file_name}}</p>
                            <div class="file-uploads-div dropdown-menu" id="degree-file-uploads-div" aria-labelledby="#degree-file-info">
                                @if(array_key_exists('degree_file_details',get_defined_vars()))
                                    @foreach ($degree_file_details as $file)
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
    @endif
</div>
