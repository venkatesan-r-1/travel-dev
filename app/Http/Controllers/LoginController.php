<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Artisaninweb\SoapWrapper\Facades\SoapWrapper;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Session;
use Log;

class LoginController extends Controller
{
    /**
     * Method used to handle login and authenticate user
     * Created by dinakar on 26 Dec 2023
     * 
     * 
     * @param String (email/token)
     * @return Auth
     */
	public function login(Request $request) {
		ini_set('display_errors', 1);
		// Getting guid to validate the login
	    // $user_info_decoded = base64_decode($request->SAMLResponse);
        // $user_info_decoded=str_ireplace(['samlp:', 'saml:'], '', $user_info_decoded);
        // $user_info_decoded1=simplexml_load_string($user_info_decoded);
        // $user_info_arr=json_decode(json_encode($user_info_decoded1),true);
        // $email=$user_info_arr['Assertion']['Subject']['NameID'];
        if(isset($request['email'])){
            $email = $request->input ( 'email' );
            // Hardcoded password
            // checking authentication using email. If passes, allowing the user to view request form
            if (Auth::attempt ( [ 
                    'email' => $email,
                    'password' => 'aspire@123',
                    'active'=>1
            ] )) {
                $user = Auth::user ();
            } else {
                echo "Unauthorised Login";
            }
        }
		if(Auth::User()){
            $url=$request->session()->get('pre-url', '/home');
            // Session::flush('pre-url');
            // storing a key for loading the proof details against the user from service
            session()->put('user_other_details', 'PROOFDETAILS');

			return Redirect::to($url);
		}
        else{
            return view('layouts.login');
        }
	}
	
	/**
     * Method used to logout the authenticated user
     * Created by dinakar on 26 Dec 2023
     * 
     */
	public function logout(){		
		Auth::logout();
        return Redirect::to('/auth/login');
		// return redirect('https://sso-a48e6e89.sso.duosecurity.com/saml2/sp/DI9ARGPTCSF6SGWY2TA9/slo');
		// echo "<script>window.close();</script>";
        echo "Logged out successfully.";

	}

}
