@php( $request_details = json_decode(json_encode($request_details),true); )
<table border='1'>
    <tr>
        <td>Visa request id</td>
        <td>{{ $request_details['request_details']['request_code'] }}</td>
    </tr>
    <tr>
        <td>Employee</td>
        <td>{{ $request_details['request_details']['traveler_name'] }}</td>
    </tr>
    <tr>
        <td>Ace ID</td>
        <td>{{ $request_details['request_details']['travaler_id']}}</td>
    </tr>
    <tr>
        <td>Department</td>
        <td>{{ $request_details['request_details']['department_name'] }}</td>
    </tr>
    <tr>
        <td>Primary manager</td>
        <td>{{ $request_details['request_details']['primary_manager'] ?? 'NA' }}</td>
    </tr>
    <tr>
        <td>Visa type</td>
        <td>{{ $request_details['visa_details']['visa_type'] }}</td>
    </tr>
    <tr>
        <td>Visa category</td>
        <td>{{ $request_details['visa_details']['visa_category'] }}</td>
    </tr>
</table>
