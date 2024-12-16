<table border="1">
    <tbody>
        <tr>
            <td colspan=2>Request ID</td>
            <td colspan=2>{{$request_details['request_details']->request_code}}</td>
        </tr>
        <tr>
            <td colspan=2>Traveler aceid</td>
            <td colspan=2>{{$request_details['request_details']->travaler_id}}</td>
        </tr>
        <tr>
            <td colspan=2>Traveler username</td>
            <td colspan=2>{{$request_details['request_details']->traveler_name}}</td>
        </tr>
        <tr>
            <td colspan=2>Name of the traveler</td>
            <td colspan=2>{{$request_details['request_details']->traveler_full_name}}</td>
        </tr>
        <tr>
            <td colspan=2>Source company</td>
            <td colspan=2>{{$request_details['request_details']->requestor_entity}}</td>
        </tr>
        @if($request_details['request_details']->module == "MOD_03") 
            <tr>
                <td colspan=2>Visa type</td>
                <td colspan=2>{{$request_details['visa_details']->visa_type}}</td>
            </tr>
            <tr>
                <td colspan=2>Visa category</td>
                <td colspan=2>{{$request_details['visa_details']->visa_category}}</td>
            </tr>
        @else
            <tr>
                <td colspan=2>Purpose</td>
                <td colspan=2>{{$request_details['request_details']->travel_purpose}}</td>
            </tr>
        @endif
        <tr>
            <td colspan=2>Department</td>
            <td colspan=2>{{$request_details['request_details']->department_name}}</td>
        </tr>
        <tr>
            <td colspan=2>Requestor location</td>
            <td colspan=2>{{$request_details['request_details']->traveler_location}}</td>
        </tr>
        <tr>
            <td colspan=2>Customer name</td>
            <td colspan=2>{{$request_details['request_details']->customer_name}}</td>
        </tr>
        <tr>
            <td colspan=2>Project</td>
            <td colspan=2>{{$request_details['request_details']->project_name}}</td>
        </tr>
        <tr>
            <th>{{$request_details['request_details']->module=='MOD_01'?"From":"Country"}}</th>
            <th>{{$request_details['request_details']->module=='MOD_01'?"To":"City"}}</th>
            <th>From date</th>
            <th>To date</th>
        </tr>
        @foreach ($request_details['travelling_details'] as $traveling_detail)
        <tr>
            <td>{{$request_details['request_details']->module=='MOD_01'? $traveling_detail->from_city_name:$traveling_detail->to_country_name}}</td>
            <td>{{$request_details['request_details']->module=='MOD_01'? $traveling_detail->to_city_name:$traveling_detail->to_city_name}}</td>
            <td>{{$traveling_detail->from_date}}</td>
            <td>{{$traveling_detail->to_date?$traveling_detail->to_date:"NA"}}</td>
        </tr>
        @endforeach
        @if (in_array($request_details['request_details']->module,['MOD_01','MOD_02']))
            <tr>
                <td colspan=2>Ticket required</td>
                <td colspan=2>{{$request_details['request_details']->ticket_required?"Yes":"No"}}</td>
            </tr>    
            @if (in_array($request_details['request_details']->module,['MOD_02']))
                <tr>
                    <td colspan=2>Forex required</td>
                    <td colspan=2>{{$request_details['request_details']->forex_required?"Yes":"No"}}</td>
                </tr>
            @endif
        @endif
        @if(isset($request_details['status_details']))
        <tr>
                <td colspan=2>Traveler Remarks</td>
                <td colspan=2>{{((array_key_exists('requestor_remarks',$request_details['status_details']['remarks_details']))&&$request_details['status_details']['remarks_details']['requestor_remarks'])?$request_details['status_details']['remarks_details']['requestor_remarks']:"NA"}}</td>
            </tr>  
        @endif
        @if(!is_null($request_details['request_details']->billed_to_client))
            <tr>
                <td colspan=2>Billable to client</td>
                <td colspan=2>{{$request_details['request_details']->billed_to_client?"Yes":"No"}}</td>
            </tr>  
        @endif

        @foreach ($request_details['status_details'] as $status_detail)
            @if(isset($status_detail['action'])&&$status_detail['action']==$action&& $action!='submit')
            <tr>
                <td colspan=2>{{$status_detail['action_by_label']}}</td>
                <td colspan=2>{{$status_detail['action_by']}}</td>
            </tr>
            <tr>
                <td colspan=2>{{$status_detail['action_on_label']}}</td>
                <td colspan=2>{{$status_detail['action_on']}}</td>
            </tr>
            <tr>
                <td colspan=2>Comments</td>
                <td colspan=2>{{$status_detail['comments']?$status_detail['comments']:"NA"}}</td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>
