var overall_columns_config={
    0:{'column_no':'0','idname':'visa_process_request_code_filter','filter_required':'yes','filter_type':'input'},
    1:{'column_no':'11','idname':'submitted_from_date','filter_required':'yes','filter_type':'date','filter_column':'submitted'},
    2:{'column_no':'11','idname':'submitted_to_date','filter_required':'yes','filter_type':'date','filter_column':'submitted'},    
    3:{'column_no':'2','idname':'visa_process_employee_filter','filter_required':'yes','filter_type':'input'},
    4:{'column_no':'10','idname':'visa_process_status_filter','filter_required':'yes','filter_type':'multiselect'},
    5:{'column_no':'6','idname':'visa_process_visa_type_filter','filter_required':'yes','filter_type':'multiselect'},
    6:{'column_no':'7','idname':'visa_process_request_type_filter','filter_required':'yes','filter_type':'multiselect'},
    7:{'column_no':'8','idname':'visa_process_client_name_filter','filter_required':'yes','filter_type':'multiselect'},
    8:{'column_no':'9','idname':'visa_process_petiton_entity_filter','filter_required':'yes','filter_type':'multiselect'},
    9:{'column_no':'5','idname':'visa_process_india_manager_filter','filter_required':'yes','filter_type':'input'},
}
var table_config={
    'table_id':'visa_process_history_table',
    'response_url':'/visa_process/get_history_details',
    'request_type':'GET',
    'response_type':'json',
    'parameters':{},
    'table_dom':'Bfrtipl',
    'table_searching':true,
    'overall_filter_required':true,
    'pagination_required':true,
    'pagination_count':10
}
