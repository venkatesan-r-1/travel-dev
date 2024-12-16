<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use \Curl\Curl;
use DB;


class UserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command. The below function fetched users details from the IDM server and add/update the details in user table
     */
    public function handle()
    {
        try {
            ini_set('max_execution_time',7200);

            // Configuration
            $controller = new \App\Http\Controllers\Controller();
            $service_config = $controller->service_url_config;
            
            $service_name = "USERLIST";
            $url = $service_config[$service_name]["url"];
            $username = $service_config[$service_name]["username"];
            $password = $service_config[$service_name]["password"];
            $curl=new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setBasicAuthentication($username, $password);
            $curl->setDefaultTimeout(120);
            $data = json_encode(array (
                    'Date' => " ",
                    'ACENumber' => 'ACE002',
                    'RelationId' => - 1
            ));
            $curl->post($url, $data);
            if ($curl->error) {
                echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            } 
            else {
                $usersDetailsList=$curl->response;
                $user_array = []; $user_relation_array = [];
                $user_relations = DB::table('trd_user_attr_mapping_key')->select('api_key_name')->where('active',1)->where('table_affected','users')->pluck('api_key_name')->toArray();
                echo "Total records : " . count($usersDetailsList->employees). "\n";
                // Inactive all users
                // DB::table('users')->update(['active' => 0]);
                foreach ( $usersDetailsList->employees as $index=>$userDetails ) {
                    $aceid = ( string ) $userDetails->ACEID;
                    $user_name = (string)$userDetails->UserName;
                    $email = str_replace('@aspiresys.com','@aspiresys123.com',( string ) $userDetails->Email);
                    $innerArray = [
                        'aceid' => $aceid,
                        'username'=> $user_name,
                        'email'=> trim($email),
                        'password' => Hash::make ( 'aspire@123' ), // Hardcoded password
                        'active' => 1
                    ];
                    foreach($user_relations as $key => $val){
                        $innerArray[$val] = ( string ) $userDetails->{$val};
                    }
                    $user_array[] = $innerArray;
                    // User::updateOrInsert(
                    //     [
                    //         'aceid' => $aceid
                    //     ],
                    //     [
                    //         'username'=>$user_name,
                    //         'email'=>trim($email),
                    //         'password' => Hash::make ( 'aspire@123' ), // Hardcoded password
                    //         'active' => 1
                    //     ]
                    // );
                    
                    // foreach($user_relations as $key => $val){
                        // $user_relation_array[] = [
                        //     'unique_key' => $aceid.'-'.$key,
                        //     'aceid' => $aceid,
                        //     'attribute' => $key,
                        //     'attribute_name' => $val,
                        //     'mapping_value' => ( string ) $userDetails->{$val},
                        //     'active' => 1   
                        // ];
                        // DB::table('trf_user_detail_mapping')->updateOrInsert(
                        //     [
                        //         'unique_key' => $aceid.'-'.$key,
                        //         'aceid' => $aceid,
                        //         'attribute' => $key
                        //     ],
                        //     [
                        //         // 'attribute_name' => strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $val)),
                        //         'attribute_name' => $val,
                        //         'mapping_value' => ( string ) $userDetails->{$val},
                        //         'active' => 1   
                        //     ]
                        // );
                    // }
                    echo ($index+1)."\n";
                    if((($index+1) % 500) == 0){
                        User::upsert($user_array,['aceid'],array_merge(['username','email','password','active'],$user_relations));
                        $user_array = [];
                        echo "Inserted ".($index+1)."\n";
                    }
                    elseif(($index+1) == count($usersDetailsList->employees)){
                        echo "Inserted ".($index+1)."\n";
                    }
                }
                // dd(array_merge(['username','email','password','active'],$user_relations));
                User::upsert($user_array,['aceid'],array_merge(['username','email','password','active'],$user_relations));
                // DB::table('trf_user_detail_mapping')->upsert($user_relation_array,['unique_key'],['aceid','attribute','attribute_name','mapping_value','active']);
            }

            //Inactive users updation
            $service_name = "INACTIVEUSER";
            $url = $service_config[$service_name]["url"];
            $username = $service_config[$service_name]["username"];
            $password = $service_config[$service_name]["password"];
            $curl=new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setBasicAuthentication($username, $password);
            $curl->setDefaultTimeout(120);

            $curl->get($url);

            if ($curl->error) {
                echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            } else {
                $usersInactiveDetailsList=$curl->response;
                $inactive_users=[];
                foreach ( $usersInactiveDetailsList->employees as $userInactiveDetails ) {
                    array_push($inactive_users,(string) $userInactiveDetails->ACEID);
                }
                $userObj = new User();
                $data = $userObj->whereIn ( 'aceid',$inactive_users)->count ();
                if($data){
                    $userObj->whereIn ( 'aceid',$inactive_users)->update ( [
                        'active' => 0
                    ] );
                }
            }
        }catch ( \Illuminate\Database\QueryException $ex ) {
            var_dump($ex->getMessage ()) ;

        }
    }
}
