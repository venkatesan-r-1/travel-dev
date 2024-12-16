<?php

namespace App\Console\Commands;

use App\Http\Controllers\ConfigController;
use Curl\Curl;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockUserCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'block-user:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Block the international travel request in case reimbursement for the previous travel is not yet claimed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Configuration
            $configured_status = ['STAT_12', 'STAT_13'];
            $configured_module = ['MOD_02'];
            $configured_request_type = ['RF_05'];
            $configured_travel_type = ['TRV_02_02', 'TRV_02_03'];
            $system_name = "REIMBURSEMENT";

            // Helper functions
            // To check whether the user is already blocked
            $is_blocked_user = fn($aceid) => 
                DB::table('trf_blocked_users')
                    ->where('aceid', $aceid)
                    ->where('system_name', $system_name)
                    ->where('active', 1)
                    ->exists();

            $travel_details = DB::table('trf_travel_request as tr')
                                    ->leftJoin('trf_traveling_details as td', function ($join) { $join->on('td.request_id', 'tr.id')->where('td.active', 1); })
                                    ->whereIn('tr.module', $configured_module)
                                    ->whereIn('tr.status_id', $configured_status)
                                    ->whereIn('tr.request_for_code', $configured_request_type)
                                    ->whereIn('td.travel_type_id', $configured_travel_type)
                                    ->whereDate('td.to_date', '<', now())
                                    ->distinct()->pluck('tr.travaler_id', 'tr.request_id')->toArray();

            $ids_has_reimbursement = $this->filter_reimbursement_not_done($travel_details);
            
            $blocked_users = array_filter( $travel_details, fn($e) => $is_blocked_user($e) );
            $users_to_block = Arr::except($travel_details, $ids_has_reimbursement);
            $users_to_unblock = array_filter($blocked_users, fn($e) => !in_array($e, $users_to_block));
            // dd($users_to_block, $users_to_unblock);

            DB::beginTransaction();
            
            $this->toggle_block_users($users_to_block, $system_name);
            $this->toggle_block_users($users_to_unblock, $system_name, 'UNBLOCK');
            $this->sendReminderMail($users_to_block);

            DB::commit();
            Log::info("Block user cron successfully executed");
            $this->info("Block user cron successfully executed");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error occured while executing block-user:cron");
            Log::error($e);
            $this->error("Error occured while executing block-user:cron");
        }
    }

    /**
     * Filter the request ids for which travel reimbursement is not submitted
     * @author venkatesan.raj
     * 
     * @param array $travel_details
     * 
     * @return array
     */
    public function filter_reimbursement_not_done($travel_details)
    {
        try {
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
                'travel_request_ids' => array_keys($travel_details),
                'requestors' => array_values($travel_details),
                'today_completed' => true,
            ]);

            $curl->post($url, $data);

            if($curl->error) {
                new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n");
            } else {
                return $curl->response;
            }

        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * Block / Non block the users
     * @author venkatesan.raj
     * 
     * @param array $user_list
     * @param array $system_name
     * @param string $action optional
     * 
     * @return void
     */
    public function toggle_block_users($user_list, $system_name, $action = "BLOCK")
    {
        try {
            $blocked_users = DB::table('trf_blocked_users');
            $active = $action == "UNBLOCK" ? 0 : 1;
        
            foreach($user_list as $user_aceid) {
                $data = [
                    'active' => $active,
                    'system_name' => $system_name,
                    'created_by' => 'system',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $condition = [
                    'aceid' => $user_aceid,
                ];

                if($blocked_users->where($condition)->exists())
                    $data = Arr::except($data, ['created_at', 'created_by']);
                $blocked_users->updateOrInsert($condition, $data);
            }
        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    /**
     * To send the reminder mail
     * @author venkatesan.raj
     * 
     * @param array $travel_id
     * 
     * @return bool
     */
    public function sendReminderMail($travel_details)
    {
        try {
                   
        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
}
