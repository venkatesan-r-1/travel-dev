<?php

    namespace App\Traits;

    use App\Models\Encryption;
    use Log;
    use DB;

    trait EncryptionTrait
    {
        /**
         * Returns the currently active key to encrypted the fields
         * @author venkatesan.raj
         * 
         * @return mixed
         * 
         * @throws Exception
         */
        public function get_new_key() : mixed
        {
            try {
                return Encryption::firstWhere([['config_name', 'visa_secure_key'],['active', 1]])->config_value ?? ''; 
            } catch (\Exception $e) {
                Log::error("Error in".__FUNCTION__." : ".$e->getMessage());
                throw $e;
            }
        }
        
        /**
         * Returns the previous key to re encrypt the feilds with new key
         * @author venkatesan.raj
         * 
         * @return string
         * 
         * @throws Exception
         */
        public function get_old_key() : string
        {
            try {
                return Encryption::latest()->firstWhere([['config_name', 'visa_secure_key'],['active', 0]])->config_value ?? '';
            } catch (\Exception $e) {
                Log::error("Error in ".__FUNCTION__." : ".$e->getMessage());
                throw $e;
            }
        }
    }