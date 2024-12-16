<?php
    $us_salary = is_numeric($us_salary)?number_format($us_salary):0.0;
?>
<head>
    <link rel="stylesheet" href="{{asset('css/pdfconvert.css')}}">
</head>
    <div class="pdf">
        <div class="pdf-section">
            <p class="pdf-paragraph">
                <span class="pdf-paragraph-line"><?php echo date("F d, Y");?></span><br>
                <b class="pdf-paragraph-line">{{$first_name}} {{$last_name}}</span><br>
                <span class="pdf-paragraph-line" id="pdf-aceid">{{ $travaler_id}}</span></b><br>
                Dear <b class="">{{$first_name}},</b><br>
            </p>
        </div>
        <div class="pdf-section">
            <p class="pdf-paragraph">
                <span class="pdf-paragraph-line"><center>Sub: Appointment as <b class="actual">"{{$us_job_title}}"</b><b class="immigration">"{{$job_title}}"</b></center></span>
            </p>
        </div>
        <div class="pdf-section">
            <p class="pdf-paragraph">
                <span class="pdf-paragraph-line">We take great pleasure in offering you the position of <b class="actual">"{{$us_job_title}}"</b><b class="immigration">"{{$job_title}}"</b>.</span>
                <span class="pdf-paragraph-line">Your title is indicative of Aspire's expectations from you and we are sure you understand the criticality of your role.</span>
                <span class="pdf-paragraph-line">We hope you will enjoy your role and make a significant contribution to the success of our customers and business.</span>
            </p>
            <p class="pdf-paragraph">
                <span class="pdf-paragraph-line">We would like you to begin employment with us from <b class="">{{date("F d, Y",strtotime($travel_date))}}.</b></span>
                <span class="pdf-paragraph-line">Your compensation will be <b class="">{{$visa_currency_notation}} {{$us_salary}} per annum</b> which will be paid in accordance with Aspire's pay schedule, subject to any legally permissible deductions.</span>
                <span class="pdf-paragraph-line">These numbers have been derived based on your skill sets, experience level and the city index.</span>
                <span class="pdf-paragraph-line">As a regular, full-time employee of Aspire, you are eligible for all the benefits extended by Aspire.</span>
                <span class="pdf-paragraph-line">However, such benefits maybe changed or removed at any time at the discretion of Aspire with due notice.</span>
                <span class="pdf-paragraph-line">You will also be eligible to participate in our health insurance plan from the date of joining.</span>
                <span class="pdf-paragraph-line">You will accrue 10 days of annual leave per year and eligible for sick leave in accordance with the company policy and State guidelines.</span>
            </p>
            <span class="pdf-paragraph-line">On your first day at Aspire, please be prepared to provide employment eligibility verification.</span>
            <p class="pdf-paragraph">
            </p>
            <p class="pdf-paragraph">
                <span class="pdf-paragraph-line">Your commencement of employment with us is contingent upon your acceptance of this offer also subject to the employment agreement being signed.</span>
            </p>
            <p class="pdf-paragraph">
                <span class="pdf-paragraph-line">We look forward to welcoming you aboard, {{$first_name}}!</span>
            </p>
        </div>
        <div class="pdf-section">
            <div class="pdf-paragraph">
                <span class="pdf-paragraph-line"><span class="pdf-text-bold">{{ $petitioner_entity }}</span></span>
                <div class="pdf-digi-sign">
                    <img src="{{$signature_location}}" alt="" width="200px" height="50px" />
                </div>
                <span class="pdf-paragraph-line"><span class="pdf-text-bold">{{ $hr_admin_name ? ucwords(str_replace('.',' ', $hr_admin_name)) : null }}</span><br>{{ $hr_admin_designation }} - {{ $hr_admin_department }}<br><a href="">{{ $hr_admin_mail }}</a></span>
            </div>
        </div>
        <div class="pdf-section">
            <div class="pdf-paragraph">
                <span class="pdf-paragraph-line"><span class="pdf-text-bold">Enclosures:</span><br>Annexure 1: Terms and conditions of Employment</span>
            </div>
        </div>
    </div>
    <div class="pdf">
        <div class="pdf-section">
            <div class="pdf-paragraph">
                <span class="pdf-paragraph-line"><span class="pdf-text-bold">Annexure 1: Terms and Conditions of Employment</span></span><br><br>
                <span class="pdf-paragraph-line">Your services at Aspire will be governed by the terms and conditions detailed below, as well as your Employment Agreement with Aspire and policies issued from time to time by Aspire.</span>
                <div class="pdf-list-items">
                    <span class="pdf-paragraph-line">Aspire Systems is an equal opportunity employer.</span>
                    <span class="pdf-paragraph-line">We demonstrate respect and provide equal employment opportunities for all employees and applicants for positions regardless of race, color, national origin , religion,  disability, gender, age, genetic information, record of offence or on any other grounds upheld by the applicable laws but which do not affect the quality and efficiency of the work to be performed by such employee .</span>
                    <span class="pdf-paragraph-line">All our decisions will be based on job performance, merit, experience, and qualifications.</span>
                </div>
                <div class="pdf-list-items">
                    <span class="pdf-paragraph-line">During your tenure with us, we would expect you to appropriately intimate us if you plan to indulge in any activity or profession, which would prove detrimental to our operations or which would adversely impact the quality of your services to us.</span>
                    <span class="pdf-paragraph-line">All software products, systems developed by you during your period of service with the company will be the sole property of the company as more fully detailed in your Employment Agreement.</span>
                </div>
                <div class="pdf-list-items">
                    <span class="pdf-paragraph-line">Any information provided by you prior to your employment with us will be subject to a background verification, on a need basis. .</span>
                    <span class="pdf-paragraph-line">Such verification will be carried out by an external agency with your prior consent and authorization based on the documents you furnish as required.</span>
                    <span class="pdf-paragraph-line">At any point of time, during your services at Aspire, should we find this information inconsistent, your offer and employment with us may be revoked without any prior notice.</span>
                </div>
                <div class="pdf-list-items">
                    <span class="pdf-paragraph-line">You may from time to time be deputed to work for any of our offices/customers within US or abroad on behalf of Aspire.</span>
                    <span class="pdf-paragraph-line">In such circumstances, any advance given to you by the company need to be reconciled within one week of returning from the assignment.</span>
                    <span class="pdf-paragraph-line">During the deputation, you will also be required to comply with the local laws, ordinances, regulations and codes that govern such countries.</span>
                    <span class="pdf-paragraph-line">In case you fail to comply with the laws, ordinances, regulations and codes in such country, you will indemnify the company, to the extent permissible by law, against any loss or damage that may be sustained due to failure on your part.</span>
                </div>
            </div>
        </div>
    </div>
    <div class="pdf">
        <div class="pdf-section">
            <div class="pdf-paragraph">
                <div class="pdf-list-items">
                    <span class="pdf-paragraph-line">During your employment, you may become aware of information relating to the business of the Company, trade secrets, client names/details and pricing structures.</span>
                    <span class="pdf-paragraph-line">Confidential information remains the sole property of Aspire Systems.</span>
                    <span class="pdf-paragraph-line">You shall not, other than as restricted or required by law, either during or after your employment, in any form of communication medium including but not limited to electronic press, social networking media like facebook, internet without the prior approval or consent of the Company, directly or indirectly divulge to any person or use the confidential information for your own or another's benefit.</span>
                    <span class="pdf-paragraph-line">We expect you would not under any circumstance try to start or help any other person start the activities carried on by this company without prior written consent from Aspire's legal team.</span>
                </div>
                <div class="pdf-list-items">
                    <span class="pdf-paragraph-line">This is an at will employment and maybe terminated at any time by either of us.</span>
                    <span class="pdf-paragraph-line">We prefer that you provide us with a four weeks' prior notice when you desire to terminate the employment relationship.</span>
                    <span class="pdf-paragraph-line">Please note that we would like you to continue your services until the end of such notice period.</span>
                    <span class="pdf-paragraph-line">During your employment, if your performance is not up to our expectations or are not satisfactory, or for any other reasons as per the HR Policies, we would usually prefer providing you with a four weeks' prior notice to terminate your services.</span>
                </div>
                <div class="pdf-list-items">
                    <span class="pdf-paragraph-line">The rules and regulations of service of the Company that are in force may be framed, amended, altered or extended from time to time and shall be appropriately notified to the employees by the HR Team.</span>
                    <span class="pdf-paragraph-line">They will govern you in the same form as and when altered or amended.</span>
                </div>
                <div class="pdf-list-items">
                    <span class="pdf-paragraph-line">Medical Insurance Coverage: You will be enrolled in the general medical insurance coverage of the company.</span>
                    <span class="pdf-paragraph-line">The contribution towards your medical insurance will be equally borne by you and the organization in the ratio of 46% from employee and 54% from Aspire.</span>
                    <span class="pdf-paragraph-line">Dental and Vision insurance are optional and should be paid in full by the employee.</span>
                </div>
                <div class="pdf-list-items">
                    <span class="pdf-paragraph-line">The Designation at Aspire is <span class="pdf-text-bold actual">"{{$us_job_title}}"</span><span class="pdf-text-bold immigration">"{{$job_title}}"</span>.</span>
                    <span class="pdf-paragraph-line">This designation is classified as a full-time exempt professional.</span>
                </div>
                <div class="pdf-list-items">
                    <span class="pdf-paragraph-line">Performance appraisal: Your salary in the US has been established based on your current experience levels and skill sets.</span>
                    <span class="pdf-paragraph-line">We usually conduct periodical reviews of the employees and have annual appraisals.</span>
                    <span class="pdf-paragraph-line">You will be included in such reviews and appraisals as per your performance and eligibility which shall be after completion of minimum one year of service with us.</span>
                    <span class="pdf-paragraph-line">We are not under any obligation to revise your salary every year and revisions if any shall be paid out in the next applicable appraisal cycle.</span>
                </div>
                <div class="pdf-list-items optional">
                    <span class="pdf-paragraph-line">Green Card Process: You have to complete minimum 36 months with Aspire US in order to be eligible for Green Card (GC) and upon completion of 36 months GC process will be initiated based on the Business needs and management direction.</span>
                </div>
          </div>
        </div>
    </div> 
    <div class="pdf-header">
        <img src="{{asset('images/logo.png')}}" alt="aspire-logo" class="pdf-logo">
    </div>
    <div class="pdf-footer">
        <div class="pdf-footer-table">
            <div class="pdf-footer-content">
                <div>US Headquarders</div>
                <div><span class="pdf-text-bold">Chicago</span></div>
                <div>{{$petitioner_entity}}</div>
                <div>1200 Harger Road</div>
                <div>Suite 722, Oak Brook, IL - 60523, USA</div>
                <div>Fax: +1 408 904 4591</div>
                <div>Tel: +1 630 368 0970, 1 630 368 0973</div>
            </div>
            <div class="pdf-footer-vr"></div>
            <div class="pdf-footer-content">
                <div><span class="pdf-text-bold">San Jose</span></div>
                <div>{{ $petitioner_entity }}</div>
                <div>1735 Technology Drive </div>
                <div>Suite 260, San Jose, CA - 95110, USA</div>
                <div>Fax: +1 408 854 7946 </div>
                <div>Tel: +1 408 260 2076, +1 408 260 2090</div>
            </div>
            <div class="pdf-footer-vr"></div>
            <div class="pdf-footer-content">
                <div><span class="pdf-text-bold">Dallas</span></div>
                <div>{{ $petitioner_entity }}</div>
                <div>210 E John Carpenter Fwy</div>
                <div>Suite 600, Irving, TX- 75062, USA</div>
                <div>Fax: +1 469 249 1852</div>
                <div>Tel: + 1 972 808 7830</div>
            </div>
        </div>
        <div class="pdf-footer-website">www.aspiresys.com</div>
    </div>
    <!-- Data required for offer letter generation in docx format -->
    <div class="doc" style="display:none;">
        <div class="word_document" id="doc_date_creation">{{date("F d, Y")}}</div>
        <div class="word_document" id="doc_firstname">{{$first_name}}</div>
        <div class="word_document" id="doc_lastname">{{$last_name}}</div>
        <div class="word_document" id="doc_aceid">{{$travaler_id}}</div>
        <div class="word_document" id="doc_job_title">{{$us_job_title}}</div>
        <div class="word_document" id="doc_start_date">{{$travel_date}}</div>
        <div class="word_document" id="doc_salary_notation">{{$visa_currency_notation}}</div>
        <div class="word_document" id="doc_us_salary">{{$us_salary}}</div>
        <div class="word_document" id="doc_entity">{{$petitioner_entity}}</div>
        <div class="word_document" id="doc_us_hr">{{$hr_admin_name ? ucwords(str_replace('.',' ', $hr_admin_name)) : null}}</div>
        <div class="word_document" id="doc_us_hr_designation">{{ $hr_admin_designation }}</div>
        <div class="word_document" id="doc_us_hr_department">{{ $hr_admin_department }}</div>
        <div class="word_document" id="doc_us_hr_mail">{{ $hr_admin_mail }}</div>
        <div class="word_document" id="offer_letter_template">{{ $offer_letter_template }}</div>
        <div class="word_document" id="doc_additional_point">Green Card Process: You have to complete minimum 36 months with Aspire US in order to be eligible for Green Card (GC) and upon completion of 36 months GC process will be initiated based on the Business needs and management direction.</div>
    </div>
    <img src="{{asset('images/watermark-sample.png')}}" alt="" class="pdf-watermark">
