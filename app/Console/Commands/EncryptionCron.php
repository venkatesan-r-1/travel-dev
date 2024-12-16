<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\EncryptionTrait;
use App\Models\Encryption;
use DB;
use Log;

class EncryptionCron extends Command
{
    use EncryptionTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visa:encrypt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the encryption key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{

            $new_key = $this->get_new_key();
            if(empty($new_key)) {
                throw new \Exception("No active key available");
            }
            $old_key = $this->get_old_key();

            DB::beginTransaction();

            //Fetch data from DB
            $details = DB::table('visa_process_review_details')
                        ->select('process_request_id', 'salary_range_from', 'salary_range_to', 'us_salary', 'one_time_bonus')
                        ->get()
                        ->keyBy('process_request_id')
                        ->map(function ($item) use($old_key, $new_key) {
                            return collect($item)->except(['process_request_id'])->filter(function ($value) {
                                return $value;
                            });
                        })
                        ->filter(function ($item) {
                            return $item->contains(function ($value) { return !empty($value); });
                        })
                        ->toArray();
            
            foreach( $details as $request_id => $detail ) {
                foreach ($detail as $field => $value) {
                    if ( $old_key ) {
                        $value = DB::select("SELECT CAST( AES_DECRYPT( ?, UNHEX( SHA2( ?, 512 ) ) ) AS DECIMAL ) AS value",[$value, $old_key])[0]->value;
                    } 
                    $detail[$field] = DB::select("SELECT AES_ENCRYPT( :value, UNHEX( SHA2( :key, 512 ) ) ) AS value", [':value' => $value,  ':key' => $new_key])[0]->value;
                }
                DB::table('visa_process_review_details')
                    ->where('process_request_id', $request_id)
                    ->update($detail+['updated_at' => 'Y-m-d H:i:s']);
            }

            DB::commit();
            Log::info("All the records has been re encrypted with the new secure key");
            $this->info("All the records has been re encrypted with the new secure key");
        }
        catch (\Exception  $e){
            DB::rollBack();
            $this->error("Error occured while running Encryption cron");
            Log::error("Error occured while running Encryption cron");
            Log::info($e);
        }
    }
}
