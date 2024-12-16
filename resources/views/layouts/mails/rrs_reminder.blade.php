<p>
    Hello {{ $traveler_name }},<br /><br />

    A gentle reminder to submit the bills for the travel request id:<strong>{{ $request_id }}</strong> in <strong>Reimbursement System</strong>. Once you submit the bills in Reimbursement System, please submit the original bills to Finance team to process your claim. <br /><br />

    Regards, <br />
    Team Finance. <br /><br />

    Note: This is an auto-generated E-mail. Please do not reply. If you have any queries, please write to <a href="mailto:help.mis@aspiresys.com">help.mis@aspiresys.com</a>
</p>
<p>
    <strong>To list : </strong> {{ isset($to) ? implode(', ', $to) : "NA" }} <br />
    <strong>CC list : </strong> {{ isset($cc) ? implode(', ', $cc) : "NA" }} <br />
</p>