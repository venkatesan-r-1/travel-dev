<?php

namespace App\Console\Commands;

use Curl\Curl;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReimbursementReminderCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rrs-reminder:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $details = $this->get_travel_details();

            $details = $this->filter_reimbursed_requests($details);

            $rrs_done = $details['rrs_done'] ?? [];
            $rrs_not_done = $details['rrs_not_done'] ?? [];

            $users_to_block = array_unique( array_column($rrs_done, 'travaler_id') );
            $users_to_unblock = array_unique( array_column($rrs_not_done, 'travaler_id') );
            $users_to_unblock = array_diff( $users_to_unblock, $users_to_block );

            $this->toggle_block_users($users_to_block);
            $this->toggle_block_users($users_to_unblock, 'UNBLOCK');
            $this->send_rrs_reminder(array_column($rrs_done, 'id'));
        } catch (\Exception $e) {
            Log::error('Error occured while running RRS reminder mails');
            Log::error($e);
            $this->error('Error occured while running RRS reminder mails');
        }
    }
    /**
     * To get the details of the travel ids for which reminder mail need to be send
     * @author venkatesan.raj
     * 
     * @return array
     */
    public function get_travel_details()
    {
        try {
            // Configuration
            $configured_status = ['STAT_12', 'STAT_13'];
            $configured_module = ['MOD_02'];
            $configured_request_type = ['RF_05'];
            $configured_travel_type = ['TRV_02_02', 'TRV_02_03'];
            $start_date=date('Y-m-d', strtotime('2023-05-01'));

            $details = DB::table('trf_travel_request as tr')
                            ->leftJoin('trf_traveling_details as td', function ($join) { $join->on('td.request_id', 'tr.id')->where('td.active', 1); })
                            ->select('tr.id', 'tr.request_id', 'tr.travaler_id', 'td.to_date')
                            ->whereIn('tr.status_id', $configured_status)->whereIn('tr.module', $configured_module)->whereIn('tr.request_for_code', $configured_request_type)->whereIn('td.travel_type_id', $configured_travel_type)
                            ->whereDate('td.to_date', '<=', now())->whereDate('td.to_date', '>=', $start_date)
                            ->get()
                            ->keyBy('request_id')
                            ->toArray();

            return $details;                            
        } catch (\Exception $e) {
            Log::error("Error occured in {function} : {message}", ['function' => __FUNCTION__, 'message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * To filter the requests for which reimbursement request created
     * @author venkatesan.raj
     * 
     * @param array $details
     * 
     * @return array
     */
    public function filter_reimbursed_requests($details)
    {
        try {
            $request_id_list = array_column($details, 'request_id');
            $traveler_list = array_column($details, 'travaler_id');
            
            $controller = new \App\Http\Controllers\Controller();
            $service_config = $controller->service_url_config;
            $service_name = 'TRVREIMFILTER';
            $url = $service_config[$service_name]["url"];
            $apiToken = $service_config[$service_name]["api-token"];

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setDefaultTimeout(120);
            $data = json_encode([
                'api_token' => $apiToken,
                'travel_request_ids' => $request_id_list,
                'requestors' => $traveler_list,
            ]);

            $curl->post($url, $data);

            if($curl->error) {
                new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n");
            } else {
                $response = (array)$curl->response;

                if( !$response || !is_array($response) || count($response) == 0 ) {
                    throw new \Exception("Response is empty");
                }

                return [
                   "rrs_done" => Arr::only($details, $response),
                   "rrs_not_done" => Arr::except($details, $response),
                ];
            }
        } catch (\Exception $e) {
            Log::error("Error occured in {function} : {message}", ['function' => __FUNCTION__, 'message' => $e->getMessage()]);
            throw $e;
        }
    }
    /**
     * To block the given list of users
     * @author venkatesan.raj
     * 
     * @param array $user_list
     * @param string $action
     * 
     * @return void
     */
    public function toggle_block_users($user_list, $action = "BLOCK")
    {
        try {
            foreach ($user_list as $aceid) {
                $block_user = DB::table('trf_blocked_users');
                $active = $action == "UNBLOCK" ? 0 : 1;
                $data = [
                    'active' => $active,
                    'system_name' => 'REIMBURSEMENT',
                    'created_by' => 'system',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                $condition = [
                    'aceid' => $aceid
                ];
                if($block_user->where($condition)->exists())
                    $data = Arr::except($data, ['created_at', 'created_by']);
                $block_user->updateOrInsert($condition, $data);

                if($action == "UNBLOCK") {
                    $this->info("User has been blocked");
                } else {
                    $this->info("User has been unblocked");
                }

            }
        } catch (\Exception $e) {
            Log::error("Error occured in {function} : {message}", ['function' => __FUNCTION__, 'message' => $e->getMessage()]);
            throw $e;
        }
    }
    /**
     * To send the reminder mails
     * @author venkatesan.raj
     * 
     * @param array $request_id_list
     * 
     * @return void
     */
    public function send_rrs_reminder($request_id_list)
    {
        try {
            $today = date('Y-m-d');
            $start_date = date('Y-m-d', strtotime('2023-05-01'));
            $date_after_ten_days = date('Y-m-d', strtotime("+10 days"));
            $should_include_manager = fn($e) => date('Y-m-d', strtotime($e) > $date_after_ten_days);

            $travel_details = DB::table('trf_travel_request as tr')
                                ->leftJoin('trf_traveling_details as td', function ($join) { $join->on('td.request_id', 'tr.id')->where('td.active', 1); })
                                ->leftJoin('users as u', function ($join) { $join->on('u.aceid', 'tr.travaler_id')->where('u.active', 1); })
                                ->select('tr.id', 'tr.request_id', 'tr.travaler_id as requestor_aceid', 'u.firstname as requestor_name', 'u.email as requestor_email', 'td.to_date')
                                ->where('u.active', 1)->whereIn('tr.id', $request_id_list)
                                ->get()
                                ->toArray();
            $mail_recipient_list = [];
            foreach($travel_details as $details) {
                $to_list = [$details->requestor_email];
                if(count(array_filter($to_list)) == 0) continue;
                $cc_list = [];
                if( $should_include_manager($details->to_date) ) {
                    $approver_email = $this->get_approver_details($details->id);
                    if($approver_email) $cc_list[] = $approver_email;
                }
                $subject = "Travel request : {$details->request_id} - Reminder Mail";
                $mail_recipient_list = [...$mail_recipient_list, ...$to_list, ...$cc_list];

                $data = [
                    'to' => $to_list,
                    'cc' => $cc_list,
                    'subject'  => $subject,
                    'request_id' => $details->request_id,
                    'traveler_name' => $details->requestor_name
                ];

                Mail::send('layouts.mails.rrs_reminder', $data, function ($mail) use($data) {
                    $mail->to(['venkatesan.raj@aspiresys.com', 'bala.bashiyam@aspiresys.com', 'bhuva.seetharaman@aspiresys.com']);
                    // $mail->to($data['to']);
                    // if(isset($data['cc'])) $mail->cc($data['cc']);
                    $mail->subject($data['subject']);
                    $mail->from('is@aspiresys.com', 'IS');
                } );
            }

            $mail_recipient_list = array_unique(array_filter($mail_recipient_list));
            sort($mail_recipient_list);
            
            $customLog = Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/remindermail.log')
            ]);

            if( empty($mail_recipient_list) )
                $customLog->info("No Reminder mail sent for today: ".$today);
            else {
                $customLog->info('Reminder Mail send for the date: ',[$today]);
                $customLog->info('Personal reimbursement remainder mail send for the users: '.json_encode($mail_recipient_list, JSON_PRETTY_PRINT));
	            $customLog->info('LastDay:'.$today.' +10Days '.$date_after_ten_days.' StartDate '.$start_date);
            }
            
        } catch (\Exception $e) {
            Log::error("Error occured in {function} : {message}", ['function' => __FUNCTION__, 'message' => $e->getMessage()]);
            throw $e;   
        }
    }
    /**
     * To get approver details
     * @author venkatesan.raj
     * 
     * @param $request_id
     * 
     * @return array
     */
    public function get_approver_details($request_id)
    {
        try 
        {
            $flow_codes = ['PRO_OW', 'PRO_OW_HIE', 'DU_H', 'DU_H_HIE', 'DEP_H', 'CLI_PTR', 'GEO_H'];
            $flow_code_order = "'".implode("', '", $flow_codes)."'";
            return DB::table('trf_approval_matrix_tracker as amt')
                                    ->leftJoin('users as u', function ($join) { $join->on('u.aceid', 'amt.respective_role_or_user')->where('u.active', 1); })
                                    ->where([['amt.request_id', $request_id],['amt.active', 1]])
                                    ->where('amt.respective_role_or_user', '!=', '')->whereNotNull('u.id')
                                    ->whereIn('amt.flow_code', $flow_codes)
                                    ->orderByRaw("FIELD('amt.flow_code', $flow_code_order) ASC")
                                    ->value('u.email');
        } catch (\Exception $e) {
            Log::error("Error occured in {function} : {message}", ['function' => __FUNCTION__, 'message' => $e->getMessage()]);
            throw $e;
        }
    }
}
