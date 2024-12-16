<?php

use App\Http\Controllers\DetailsProvider;
use App\Http\Controllers\testController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TravelRequest;
use App\Http\Middleware\Authenticate;
use App\Http\Controllers\MailController;
use App\Http\Controllers\VisaProcessController;
use App\Http\Controllers\TravelSecureDetails;
use App\Http\Controllers\VisaRequest;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/auth/login');
});
Route::get('/auth/login',[LoginController::class, 'login']);
Route::Post('auth/login', [LoginController::class, 'login']);
Route::get('auth/logout', [LoginController::class, 'logout']);
Route::get('test', [LoginController::class, 'test']);
Route::middleware([CheckSession::class])->group(function(){
    Route::get('/home', [DetailsProvider::class, 'get_home_details']);
    Route::get('request_full_details/{id}',[TravelRequest::class,'request_page']);
    Route::Post('/travel_request',[TravelRequest::class, 'request_redirect']);
    Route::Post('/travel_request_actions',[TravelRequest::class, 'save_or_update']);
    Route::get('request', [TravelRequest::class, 'request_page']);
    Route::get('visa_request', [TravelRequest::class, 'request_page']);
    Route::post('load_project', [DetailsProvider::class, 'load_project']);
    Route::post('load_customer_du', [DetailsProvider::class, 'load_customer_du']);
    Route::post('/load_city', [DetailsProvider::class, 'load_city']);
    Route::post('list_respective_user_proof_details', [DetailsProvider::class, 'list_respective_user_proof_details']);
    Route::post('budget_verification', [DetailsProvider::class, 'budget_verification']);
    Route::post('load_related_select_options', [DetailsProvider::class, 'load_related_select_options']);
    Route::get('/travel_reimbursement', [DetailsProvider::class, 'get_travel_reimbursement']);

Route::Post('/travel_request',[TravelRequest::class, 'request_redirect']);
Route::Post('/travel_request_actions',[TravelRequest::class, 'save_or_update']);
Route::get('request', [TravelRequest::class, 'request_page']);
Route::get('/visa_request', [TravelRequest::class, 'request_page']);
Route::get('/visa_not_required',[DetailsProvider::class, 'visaNotRequiredCountries']);


Route::post('load_project', [DetailsProvider::class, 'load_project']);
Route::post('load_customer_du', [DetailsProvider::class, 'load_customer_du']);
Route::post('/load_city', [DetailsProvider::class, 'load_city']);
Route::post('list_respective_user_proof_details', [DetailsProvider::class, 'list_respective_user_proof_details']);
Route::post('budget_verification', [DetailsProvider::class, 'budget_verification']);

Route::post('load_related_select_options', [DetailsProvider::class, 'load_related_select_options']);
Route::get('/home', [DetailsProvider::class, 'get_home_details']);
Route::get('/travel_reimbursement', [DetailsProvider::class, 'get_travel_reimbursement']);
Route::get('/workbench', [DetailsProvider::class, 'get_workbench_details'])->middleware(['role:TRV_PROC_TICKET|TRV_PROC_VISA|TRV_PROC_FOREX|DOM_TCK_ADM']);
Route::get('/report', [DetailsProvider::class, 'get_report_details'])->middleware(['role:REP_ACC|TRV_EXT']);
Route::get('/traveldesk', [DetailsProvider::class, 'get_travel_desk_details'])->middleware(['role:AN_COST_FAC|AN_COST_FIN|AN_COST_VISA']);
Route::get('/hr_review', [DetailsProvider::class, 'get_hr_review_details'])->middleware(['role:HR_PRT|HR_REV']);
Route::get('/gm_review', [DetailsProvider::class, 'get_gm_review_details'])->middleware(['role:GM_REV']);
Route::get('/review', [DetailsProvider::class, 'get_bfreview_details'])->middleware(['role:BF_REV']);
Route::get('/approval', [DetailsProvider::class, 'get_approver_details'])->middleware(['role:PRO_OW|PRO_OW_HIE|DU_H|DU_H_HIE|DEP_H|FIN_APP|CLI_PTR|GEO_H']);


Route::post('/save_uploaded_file', [TravelRequest::class, 'save_uploaded_file']);
Route::post('/delete_uploaded_file', [TravelRequest::class, 'delete_uploaded_file']);
Route::get('anticipated_cost', [TravelRequest::class, 'anticipated_cost']);
Route::get('request_full_details',[DetailsProvider::class,'request_full_details']);
Route::get('request_full_details/{id}',[TravelRequest::class,'request_page']);
Route::get('test_inputs',[DetailsProvider::class,'fields_visibility_editablity_details']);
Route::get('get_request_for',[DetailsProvider::class,'get_request_for']);
//Route::get('/full_details',[DetailsProvider::class,'']);

Route::post('/forex_actions', [DetailsProvider::class, 'forex_actions']);

Route::post('/load_project_details', [DetailsProvider::class, 'load_project_details']);
Route::post('/load_from_city', [DetailsProvider::class, 'load_from_city']);
Route::post('/fetch_user_details_on_behalf', [DetailsProvider::class, 'fetch_user_details_on_behalf']);

Route::get('/domestic_request',[TravelRequest::class,'domestic_accesss']);
Route::post('route_details', [DetailsProvider::class, 'route_details']);
Route::get('/show_budget_error',[DetailsProvider::class, 'show_budget_error']);

Route::Post('/department_du_validation',[DetailsProvider::class, 'department_du_validation']);

Route::post('/load_user_other_details', [TravelRequest::class, 'load_user_other_details']);
Route::post('/extend_travel_dates', [TravelRequest::class, 'extend_travel_dates']);
Route::Post('/send_mails',[MailController::class, 'process_mails']);

Route::post('/get_block_users',[DetailsProvider::class, 'get_block_users'])->middleware(['role:BLOCK_ACC']);
Route::post('/block_user', [TravelRequest::class, 'block_user'])->middleware(['role:BLOCK_ACC']);
Route::post('/unblock_user', [TravelRequest::class, 'unblock_user'])->middleware(['role:BLOCK_ACC']);

});
Route::get('/unauthorised',function () {
    return view('layouts.unauthorised');
});
Route::get('mail_approval_cron',function(){
    \Artisan::call('mail_approval:cron');
  });
// Mail related routes
Route::get('mail_configuration',[MailController::class, 'mail_configuration_details']);
Route::post('save_mail_details',[MailController::class, 'save_mail_details']);
Route::post('get_mail_details',[MailController::class, 'get_mail_details']);
Route::get('test_mail',[MailController::class, 'fetch_mail_details']);
// Block users route
Route::get('/blocked_users', function () {
    return view('layouts.block_users');
})->middleware(['role:BLOCK_ACC']);

// New routes for the visa process related actions
Route::group(['prefix'=>'visa_process'],function(){
	Route::get('/home', [VisaProcessController::class, 'get_request_details'])->middleware(["CheckSession"]);
    Route::get('/myaction', [VisaProcessController::class, 'get_action_details'])->middleware(['CheckSession']);
	// Route::get('/myaction',['middleware'=>'CheckSession','uses'=>'VisaProcessController@get_action_details']);
	// added hr_partner_review page; -> dinakar on 18th Nov 2022
	Route::get('/hr_partner_review',[VisaProcessController::class,'get_hr_partner_review_details'])->middleware(['CheckSession', 'role:US_HR_REV']);
	Route::get('/request', [VisaProcessController::class, 'redirect_to_request'])->middleware(['CheckSession', 'role:HR_PRT']);
	Route::Post('/save_request_details', [VisaProcessController::class, 'save_request_details'])->middleware(['CheckSession', 'role:HR_PRT|VIS_USR|GM_REV|US_HR_REV', 'checkkey']);
	Route::get('/request_id/{id}', [VisaProcessController::class, 'redirect_to_request'])->middleware(['CheckSession', 'role:HR_PRT|VIS_USR|GM_REV|US_HR_REV', 'checkkey']);
	Route::get('/review', [VisaProcessController::class, 'get_review_details'])->middleware(['CheckSession', 'role:GM_REV']);
	Route::post('/uploadofferletter', [VisaRequest::class, 'upload_offer_letter'])->middleware(['CheckSession']);
	Route::get('/history', [VisaProcessController::class, 'get_filter_details'])->middleware(['CheckSession', 'role:HR_PRT|GM_REV|US_HR_REV', 'checkkey']);
	Route::get('/get_history_details',[VisaProcessController::class, 'get_history_details'])->middleware(['CheckSession', 'role:HR_PRT|GM_REV|US_HR_REV', 'checkkey']);
	Route::post('/getemployeeotherdetails', [VisaProcessController::class, 'get_employee_other_details'])->middleware(['CheckSession', 'role:VIS_USR', 'checkkey']);
	Route::post('/update_user_details', [VisaProcessController::class, 'update_user_details']);
});
Route::post('/get_employee_details',[VisaProcessController::class, 'get_employee_details'])->middleware(['CheckSession', 'role:HR_PRT|VIS_USR']);
Route::get('releasenotes',function () { return view('layouts.travel_releasenotes'); });
Route::post('/fetchMasterDetails', [VisaProcessController::class, 'fetch_master_details'])->middleware(['CheckSession', 'role:HR_PRT']);
Route::post('/getEducationDetails',[VisaProcessController::class, 'get_education_details'])->middleware(['CheckSession', 'role:VIS_USR']);
Route::post('/uploadFiles',[VisaProcessController::class, 'upload_files'])->middleware(['CheckSession', 'role:VIS_USR|GM_REV']);
Route::post('/deleteFile',[VisaProcessController::class, 'delete_file'])->middleware(['CheckSession', 'role:VIS_USR|GM_REV']);
Route::get('/key_not_found', function () { return view('layouts.encryption_key_not_found'); });

Route::get('/user_details', [VisaProcessController::class, 'get_employee_other_details']);

Route::middleware([CheckSession::class])->group(function(){
    Route::get('/visa_request', [VisaRequest::class, 'redirect_to_request'])->middleware(['checkkey']);
    Route::get('/visa_request/{id}', [VisaRequest::class, 'redirect_to_request'])->middleware(['checkkey']);;
    Route::post('/save_request_details', [VisaRequest::class, 'save_request_details']);

    Route::post("/load_employee_list", [DetailsProvider::class, 'load_employee_list']);
    Route::post('/load_visa_category', [DetailsProvider::class, 'load_visa_category']);
    Route::post('/load_visa_user_details', [DetailsProvider::class, 'load_visa_user_details']);
    Route::post('/load_education_qualification', [DetailsProvider::class, 'load_education_details']);
    Route::post('/update_salary_range', [VisaRequest::class, 'update_salary_range']);

    Route::get('/travel_link/{id}', [VisaRequest::class, 'link_to_travel']);
    Route::post('/load_visa_details', [DetailsProvider::class, 'load_visa_numbers']);
    Route::post('/cancel_travel', [TravelRequest::class, 'cancel_travel']);
});
Route::get('/visa_reports', [DetailsProvider::class, 'get_visa_report_details'])->middleware(['CheckSession', 'role:VIS_REP_ACC', 'checkkey']);

Route::post('/save_need_assistance_details',[VisaRequest::class, 'save_need_assistance_details'])->middleware(['CheckSession']);

Route::get('/secure_key_generation', [TravelSecureDetails::class, 'index']);
Route::post('/update_secure_key', [TravelSecureDetails::class, 'store']);

// test routes
Route::get('/get_hr_partner/{aceid}', [DetailsProvider::class, 'get_hr_partner']);
Route::get('/get_hr_config/{id}', [DetailsProvider::class, 'get_hr_admin_details']);
Route::get('/get_visa_details', [DetailsProvider::class, 'get_visa_details']);
Route::get('/get_cc_list', [DetailsProvider::class, 'get_values_against_rule']);
Route::get('/get_users_can_extend_dates', [TravelRequest::class, 'get_users_can_extend_dates']);
