<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Curl\Curl;
use App\Models\User;
use App\Models\Department;
use App\Models\Practice;
use DB;
use Log;

class DepartmentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'department:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update department and practice details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try
        {
            // Configuration
            $controller = new \App\Http\Controllers\Controller();
            $service_config = $controller->service_url_config;

            // Department details
            $service_name = "DEPARTMENT";
            $url = $service_config[$service_name]["url"];
            $username = $service_config[$service_name]["username"];
            $password = $service_config[$service_name]["password"];
            $curl=new Curl();
            $curl->setBasicAuthentication($username, $password);
            $curl->get($url);
            if($curl->error) {
                throw new \Exception($curl->errorCode." : ".$curl->errorMessage);
            } else {
                $response = $curl->response;
                $department_details = ($response && property_exists($response, 'Department')) ? $response->Department : null;
                DB::beginTransaction();
                if($department_details) Department::query()->update(['active' => 0]);
                foreach($department_details as $details)
                {
                    //$excluded_codes = ['DEP049'];
                    $code = property_exists($details, 'DepartmentStringId') ? $details->DepartmentStringId : null;
                    //if(in_array($code, $excluded_codes)) continue;
                    $name = property_exists($details, 'DepartmentName') ? $details->DepartmentName : null;
                    $head = property_exists($details, 'DepartmentHeadIdentifier') ? $details->DepartmentHeadIdentifier : null;
                    $active = 1;
                    $data = compact(
                        'name', 'head', 'active'
                    );
                    $condition = compact(
                        'code'
                    );
                    Department::updateOrCreate($condition, $data);
                }
                DB::commit();
                Log::error("Department details updated successfully");
                $this->info("Department details updated successfully");
            }

            // Practice details
            $service_name = "PRACTICE";
            $url = $service_config[$service_name]["url"];
            $username = $service_config[$service_name]["username"];
            $password = $service_config[$service_name]["password"];
            $curl=new Curl();
            $curl->setBasicAuthentication($username, $password);
            $curl->get($url);
            if($curl->error) {
                throw new \Exception($curl->errorCode." : ".$curl->errorMessage);
            } else {
                $response = $curl->response;
                $practice_details = $response && property_exists($response, 'Practices') ? $response->Practices : null;
                DB::beginTransaction();
                if($practice_details) Practice::where('type', 'practice')->update(['active' => 0]);
                foreach($practice_details as $details)
                {
                    $code = property_exists($details, 'PracticeStringID') ? $details->PracticeStringID : null;
                    $name = property_exists($details, 'Practice') ? $details->Practice : null;
                    $head = property_exists($details, 'PracticeHeadACENumber') ? $details->PracticeHeadACENumber : null;
                    $super_head = property_exists($details, 'PracticeSuperHeadACENumber') ? $details->PracticeSuperHeadACENumber : null;
                    $type = "practice";
                    $active = 1;
                    $data = compact(
                        'name', 'head', 'super_head', 'type', 'active'
                    );
                    $condition = compact(
                        'code'
                    );
                    Practice::updateOrCreate($condition, $data);
                }
                DB::commit();
                Log::info("Practice details updated succussfully");
            }

            $service_name = "DELIVERYUNIT";
            $url = $service_config[$service_name]["url"];
            $username = $service_config[$service_name]["username"];
            $password = $service_config[$service_name]["password"];
            $curl=new Curl();
            $curl->setBasicAuthentication($username, $password);
            $curl->get($url);
            if ($curl->error) {
                throw new \Exception($curl->errorCode." : ".$curl->errorMessage);
            } else {
                $response = $curl->response;
                $unit_details = ($response && property_exists($response, 'DeliveryUnit')) ? (array)$response->DeliveryUnit : $response;
                DB::beginTransaction();
                if($unit_details) Practice::where("type", "delivery")->update(['active' => 0]);
                foreach($unit_details as $details) {
                    $code = property_exists($details, 'DUStringID') ? $details->DUStringID : null;
                    $name = property_exists($details, 'DUName') ? $details->DUName : null;
                    $head = property_exists($details, 'DUHeadACENumber') ? $details->DUHeadACENumber : null;
                    $type = "delivery";
                    $active = 1;
                    $data = compact(
                        'name', 'head', 'type', 'active'
                    );
                    $condition = compact(
                        'code'
                    );
                    Practice::updateOrCreate($condition, $data);
                }
                DB::commit();
                Log::info("Unit details updated successfully");
                $this->info("Practice/Unit details updated successfully");
            }
        }
        catch (\Exception $e)
        {
            DB::rollback();
            Log::error("Error occured while running department cron");
            Log::error($e);
            $this->error("Error occured while running department cron");
        }
    }
}
