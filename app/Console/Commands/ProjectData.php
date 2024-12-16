<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Curl\Curl;
use App\Models\Project;
use App\Models\User;
use DB;
use Log;

class ProjectData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update projects details';

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

            $service_name = "PROJECT";
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
                $project_details = ($response && property_exists($response, 'ActiveProjects')) ? $response->ActiveProjects : null;
                // replace with configuration
                $custom_projects = ["CUST_PROJ_007"];

                DB::beginTransaction();
                if($project_details) Project::whereNotIn("project_code", $custom_projects)->update(['active' => 0]);
                // $projectOwners = array_filter(array_map( fn($e) => property_exists($e, 'ProjectOwner') ? $e->ProjectOwner : null , $project_details ));
                // $projectOwnersAceid = User::whereIn('username',$projectOwners )->where('active', 1)->distinct()->pluck('aceid', 'username')->toArray();
                foreach($project_details as $details) {
                    $status_excluded = ['Closed','Suspended'];
                    $project_code = property_exists($details, 'ProjectCode') ? (string)$details->ProjectCode : null;
                    $project_owner = property_exists($details, 'OwnerAceNumber') ? (string)$details->OwnerAceNumber : null;
                    // $project_owner = array_key_exists($project_owner, $projectOwnersAceid) ? $projectOwnersAceid[$project_owner] : null;
                    $project_name = property_exists($details, 'ProjectName') ? (string)$details->ProjectName : null;
                    $customer_code = property_exists($details, 'CustomerCode') ? (string)$details->CustomerCode : null;
                    $customer_name = property_exists($details, 'CustomerName') ? (string)$details->CustomerName : null;
                    $project_practice = property_exists($details, 'PracticeStringID') ? (string)$details->PracticeStringID : null;
                    $project_department = property_exists($details, 'DepartmentStringID') ? (string)$details->DepartmentStringID : null;
                    $project_unit = property_exists($details, 'ProgramStringID') ? (string)$details->ProgramStringID : null;
                    $start_date = property_exists($details, 'StartDate') ? ( (string)$details->StartDate ? date('Y-m-d', strtotime((string)$details->StartDate)) : null ) : null;
                    $end_date = property_exists($details, 'EndDate') ? ( (string)$details->EndDate ? date('Y-m-d', strtotime((string)$details->EndDate)) : null ) : null;
                    $active = property_exists($details, 'ProjectStatusName') ? ( in_array( (string)$details->ProjectStatusName, $status_excluded ) ? 0 : 1 ) : null;

                    $data = compact(
                        'project_owner', 'project_name', 'customer_code', 'customer_name', 'project_practice', 'project_department', 'project_unit', 'start_date', 'end_date', 'active'
                    );
                    $condition = compact(
                        'project_code'
                    );

                    Project::updateOrCreate($condition, $data);
                }
                DB::commit();
                Log::info("Project details updated successfully");
                $this->info("Project details updated successfully");
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error("Error occured while running the project cron");
            Log::error($e);
            $this->error("Error occured while running the project cron");
        }
    }
}
