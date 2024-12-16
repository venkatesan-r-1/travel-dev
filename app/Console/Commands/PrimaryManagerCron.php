<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

class PrimaryManagerCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'primary-manager:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provide access to primary managers to raise the visa request on behalf of their reportees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try
        {
            DB::beginTransaction();
            $role='PR_MAN';
            // $pm_list = DB::table('users')->where('active', 1)->distinct()->pluck('ReportingToAceid')->toArray();
            $b4_above_pm_list = DB::table(DB::raw("(
                SELECT aceid FROM users WHERE aceid in 
                (SELECT DISTINCT ReportingToAceid FROM users WHERE active = 1) and LevelName in ('B4','B5','B6','B7','B8')
            ) AS custom "))->pluck('aceid')->toArray();
            DB::table('trf_user_role_mapping')->where('role_code', $role)->update(['active'=>0]);
            $already_having_access = DB::table('trf_user_role_mapping')->where([['role_code', $role],['active', 1]])->distinct()->pluck('aceid')->toArray();
            $need_access = array_filter(array_diff($b4_above_pm_list, $already_having_access));
            foreach($need_access as $aceid) {
                $data = [
                    'active' => 1,
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s'),
                ];
                $condition = [
                    'aceid' => $aceid,
                    'role_code' => $role,
                ];
                if(DB::table('trf_user_role_mapping')->where($condition)->exists())
                    unset($data['created_at']);
                DB::table('trf_user_role_mapping')->updateOrInsert($condition, $data);
            }
            DB::commit();
            $this->info("Primary manager roles added");
        }
        catch (\Exception $e)
        {
            DB::rollback();
            $this->error("Error in running primary-manager:cron");
            Log::error("Error in running primary-manager:cron");
            Log::error($e);
        }
    }
}
