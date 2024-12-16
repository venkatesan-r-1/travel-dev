<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use mysqli;
use Log;
use Artisan;
use App\Models\Encryption;

class TravelSecureDetails extends Controller
{
        //To redirect to secure key generation page for US visa process
        public function index()
        {
            return view('layouts.travel_secure_details');
        }
    
        //Update the secure key
        public function store(Request $request)
        {
            try {

                $config_name = 'visa_secure_key';
                $new_key = bcrypt( $request->input($config_name));

                $details = [
                    'module' => 'visa',
                    'config_name' => $config_name,
                    'config_value' => $new_key,
                    'created_by' => Auth::User()->aceid,
                    'active' => 1
                ];

                // Inactive the old key
                $encryption_details = Encryption::where([['config_name', $config_name],['active', 1]]);
                $encryption_details->update(['active' => 0]);

                // Add new key
                Encryption::create($details);

                // Re encrypt all fields with new key
                Artisan::call('visa:encrypt');

                $message = $encryption_details->exists() ? "The encryption key has been successfully updated" : "The encryption key has been successfully set";
                return json_encode([
                    'message' => $message
                ]);

            } catch (\Exception $e) {
                Log::error("Error in ".__FUNCTION__);
                Log::error($e);
                return json_encode([
                    'error' => 'An error occurred while creating or updating the key. Please try again. If the issue persists, kindly send a mail to help.is@aspiresys.com'
                ]);
            }
        }
}
