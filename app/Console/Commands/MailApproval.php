<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Http\Controllers\TravelRequest;
use App\Http\Controllers\MailController;

class MailApproval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail_approval:cron';

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
        $mail_box='ganesh.veilsamy@aspiresys.com';
        $access_token=$this->get_access_token();
        $access_token=json_decode($access_token,true);
        if(array_key_exists('access_token',$access_token)&&$access_token['access_token']){
            $emails=$this->read_mails($access_token['access_token'],$mail_box);
        }
        else{
            print_r('error occured while getting access token');
        }
        if($emails) {
            foreach($emails as $email_number) {
                if(array_key_exists('from',$email_number)){
                    $mailid = $email_number['from']['emailAddress']['address'];
                }
                else{
                    continue;
                }
                $from_address = $mailid;$bill_to_client=null;
                $subject_splitup = explode("-",$email_number['subject']);
                $request_id = isset($subject_splitup[2])?preg_match("/^[0-9]{4}[T][R][V][0-9]{4}$/",trim($subject_splitup[2])):false;
                $status = isset($subject_splitup[3])?preg_match("/^(approved|rejected)/", strtolower(trim($subject_splitup[3]))):false;
                $billable =  isset($subject_splitup[4])?strtolower(trim($subject_splitup[4])):null;
                if($request_id){
                    $request_id=trim($subject_splitup[2]);
                    $status=strtolower(trim($subject_splitup[3]));
                    $travel_request_details=DB::table('trf_travel_request')->where('request_id',$request_id)->first();
                    // The below line should be removed when moving to production
                    $from_address=str_replace('aspiresys','aspiresys123',$from_address);
                    $from_user_details=DB::table('users')->where('email',$from_address)->where('active',1)->first();
                    if($travel_request_details&&$from_user_details){
                        $status_id=$travel_request_details->status_id;
                        $status_action_mapping=[
                            'STAT_04'=>'PRO_OW',
                            'STAT_06'=>'PRO_OW_HIE',
                            'STAT_08'=>'DU_H',
                            'STAT_09'=>'DU_H_HIE',
                            'STAT_10'=>'DEP_H',
                            'STAT_11'=>'FIN_APP',
                            'STAT_24'=>'CLI_PTR',
                            'STAT_25'=>'GEO_H'
                        ];
                        if(array_key_exists($status_id,$status_action_mapping)){
                            $respective_user=$status_action_mapping[$status_id];
                            $respective_approver_detail=DB::table('trf_approval_matrix_tracker')
                                ->where('request_id',$travel_request_details->id)
                                ->where('flow_code',$respective_user)->where('active',1)
                                ->where('is_completed',0)
                                ->first();
                            if(!$respective_approver_detail)
                                continue;
                            $respective_user_detail=DB::table('users')->where('aceid',$respective_approver_detail->respective_role_or_user)
                                ->first();
                            if(!$respective_user_detail)
                                continue;
                            
                            if(strtolower($respective_user_detail->email)==strtolower($from_address)||in_array($from_address,['ganesh.veilsamy@aspiresys123.com','bala.bashiyam@aspiresys123.com'])){
                                $approval_actions_array=[
                                    'PRO_OW'=>['approved'=>'project_owner_approve','rejected'=>'project_owner_reject'],
                                    'PRO_OW_HIE'=>['approved'=>'project_owner_hie_approve','rejected'=>'project_owner_hie_reject'],
                                    'DU_H'=>['approved'=>'du_head_approve','rejected'=>'du_head_reject'],
                                    'DU_H_HIE'=>['approved'=>'du_head_hie_approve','rejected'=>'du_head_hie_reject'],
                                    'DEP_H'=>['approved'=>'bu_head_approve','rejected'=>'bu_head_reject'],
                                    'FIN_APP'=>['approved'=>'fin_approve','rejected'=>'fin_reject'],
                                    'CLI_PTR'=>['approved'=>'cp_approve','rejected'=>'cp_reject'],
                                    'GEO_H'=>['approved'=>'geo_head_approve','rejected'=>'geo_head_reject'],
                                ];
                                if(array_key_exists($respective_user,$approval_actions_array)){
                                    if(array_key_exists($status,$approval_actions_array[$respective_user])){
                                        if(is_null($travel_request_details->billed_to_client)){
                                            $bill_to_client=$billable=='yes'?1:0;
                                        }
                                        $this->approve_reject_request($travel_request_details->id,$approval_actions_array[$respective_user][$status],$bill_to_client,$respective_user_detail->aceid);
                                    }
                                }
                            }
                            else{
                                continue;
                            }
                        }
                        else{
                            continue;
                        }
                    }
                }
            }
            dd('scusss');
        }
    }
    public function get_access_token(){
        try{
            $TENANT="406f6fb2-e087-4d29-9642-817873fddc4c";
			$CLIENT_ID="ddc3f6a5-f857-437d-94a4-64ad4b526681";
			$CLIENT_SECRET="Eaq8Q~pi.M.lVkCTK2TrnbOtGeOtiCKtwZJhycir";
			$SCOPE="https://graph.microsoft.com/.default";
			$STATE='mis';
			$url= "https://login.microsoftonline.com/$TENANT/oauth2/v2.0/token";
            $param_post_curl = [ 
				'client_id'=>$CLIENT_ID,
				'scope'=>$SCOPE,
				'client_secret'=>$CLIENT_SECRET,
				'grant_type'=>'client_credentials' 
            ];
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($param_post_curl));
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
            $token_string=curl_exec($ch);
            $token_json=json_decode($token_string,true);
            if(array_key_exists('access_token',$token_json)){
                return json_encode(['access_token'=>$token_json['access_token']]);
            }
            else if(array_key_exists('error',$token_json)){
                return json_encode(['error'=>$token_json['error']]);
                print_r($token_json['error']);
                print_r($token_json['error_description']);
            }
            else{
                return json_encode(['access_token'=>'']);
                print_r("Error in reaching mail server");
            }
        }
        catch(\Exception $e){
            Log::error("err in get_access_token");
            Log::error($e);
        }
    }
    public function read_mails($access_token=null,$mail_box=null){
		if(!$mail_box)
		{
			echo "Please provide mailbox details";exit();
		}
		if($access_token){
			$url = 'https://graph.microsoft.com/v1.0/users/'.$mail_box.'/MailFolders/Inbox/messages?$search="received>=2023-01-20T00:00:00Z"';
			
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Accept: application/json',
				'Authorization: Bearer '.$access_token
			));
			
			$r = curl_exec($ch);
			
			curl_close($ch);
			
			$result_json = json_decode( $r,true);
			if(array_key_exists('value',$result_json)){
				return $result_json['value'];
			}
			else{
				print_r('Error in reading mails');
			}
		}
		
	}

    public function approve_reject_request($request_id,$action,$bill_to_client,$action_user){
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->request->add(['edit_id'=>$request_id,'action'=>$action,'billed_to_client'=>$bill_to_client,'common_action_comments'=>'','from_mail_approval'=>1,'action_user'=>$action_user]);
        $trf_object=new TravelRequest();
        $trf_data_update=$trf_object->save_or_update($request);
        $result_data=json_decode($trf_data_update);
        $request1 = new \Illuminate\Http\Request();
        $request1->setMethod('POST');
        $request1->request->add(['mail_name'=>$result_data->next_action_details->mail,'action'=>$result_data->action,'request_id'=>$result_data->request_id]);
        $mail_object=new MailController();
        $mail_send=$mail_object->process_mails($request1);
        echo "Approved and mail triggered";
    }
}
