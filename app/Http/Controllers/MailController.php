<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Http\Controllers\DetailsProvider;
use Log;
use View;
use Mail;
use \Crypt;
use DateTime;
class MailController extends Controller
{
    public function mail_configuration_details()
    {
        $mail_names=[];
        $mail_involved=DB::table('trf_approval_matrix_permissions')->where('active',1)
            ->select('mails_involved','dependent_mails_involved')
            ->get();
        foreach($mail_involved as $mails){
            if($mails->mails_involved){
                $mail_split1=explode('|',$mails->mails_involved);
                foreach($mail_split1 as $mail_split){
                    $mails_array_split=explode(',',$mail_split);
                    $mail_names=array_merge($mail_names,$mails_array_split);
                }
            }
            if($mails->dependent_mails_involved){
                $mail_split1=explode('|',$mails->dependent_mails_involved);
                foreach($mail_split1 as $mail_split){
                    $mails_array_split=explode(',',$mail_split);
                    $mail_names=array_merge($mail_names,$mails_array_split);
                }
            }
        }

        // visa mails involved
        $mail_involved=DB::table('vrf_visa_flow_config')->where('active',1)
            ->select('mails_involved','dependent_mails_involved')
            ->get();
        foreach($mail_involved as $mails){
            if($mails->mails_involved){
                $mail_split1=explode('|',$mails->mails_involved);
                foreach($mail_split1 as $mail_split){
                    $mails_array_split=explode(',',$mail_split);
                    $mail_names=array_merge($mail_names,$mails_array_split);
                }
            }
            if($mails->dependent_mails_involved){
                $mail_split1=explode('|',$mails->dependent_mails_involved);
                foreach($mail_split1 as $mail_split){
                    $mails_array_split=explode(',',$mail_split);
                    $mail_names=array_merge($mail_names,$mails_array_split);
                }
            }
        }
          
        $mail_names=array_filter(array_unique($mail_names));
        // US visa process related mail names...
        $visa_related_mail_names = DB::table('visa_process_mail_recepients')->distinct()->pluck('mail_name')->toArray();
        $mail_names = array_merge( $mail_names, $visa_related_mail_names );
        $mail_names=array_combine($mail_names,$mail_names);
        $roles=DB::table('trd_roles')
            ->where('active',1)->pluck('name','unique_key')->toArray();
        $roles=array_merge($roles,['REQ'=>'Requestor', 'CRE' => "Created by"]);
    
        $mail_configuration=[
            'mail_names'=>$mail_names,
            'roles'=>$roles
        ];
        return view('layouts.mails.mail_configure', $mail_configuration);
    }
    public function save_mail_details(Request $request){
        try{
            $mail_details=$request['mail_details'];
            $mail_data_array=[];
            foreach($mail_details as $key=>$detail){
                if($detail){
                    if(is_array($detail)){
                        $value=implode(",",$detail);
                    }
                    else
                        $value=$detail;
                }
                else
                    $value=null;
                $mail_data_array[$key]=$value;
            }
            $mail_data_array['active']=1;
            $mail_data_array['created_by']=Auth::User()->aceid;
            $mail_data_array['created_at']=date('Y-m-d H:i:s');
            $mail_data_array['updated_at']=date('Y-m-d H:i:s');
            $config_insert=DB::table('trf_mail_configurations')->updateOrInsert(
                ['mail_name'=>$mail_data_array['mail_name'],'active'=>1],
                $mail_data_array
            );
            if($config_insert)
                return json_encode(['SUCC'=>'Mail configuration updated successfully']);
        }   
        catch(\Exception $e){
            Log::error('Err in save_mail_details');
            Log::error($e);
            return json_encode(['ERR'=>'Error occurred ']);
        }       
    }
    public function get_mail_details(Request $request){
        try{
            if(isset($request['mail_name'])){
                $existing_mail_details=DB::table('trf_mail_configurations')
                    ->where('mail_name',$request['mail_name'])
                    ->where('active',1)
                    ->first();
                if($existing_mail_details){;
                    return json_encode(['mail_details'=>$existing_mail_details]);
                }
                else{
                    return json_encode(['NOREC'=>'No records found']);
                }
            }
        }
        catch(\Exception $e){
            dd($e);
            Log::error("Err in get_mail_details");
            Log::error($e);
            return json_encode(['ERR'=>'Error occurred ']);
        }
    }
    public function process_mails(Request $request){
        try{
            Log::info($request->all());
            $mail_name=$request['mail_name'];
            $mails_involved=explode(',',$mail_name);
            $request_id=$request['request_id'];
            $action=$request['action'];
            $mail_flag = $request->input("mail_flag") ?? "travel";
            if($mail_name=='need_assistance'){
                $can_send_mail = $this->need_assistance_log($mail_name, $request_id);
                if(!$can_send_mail) {
                return response()->json(['message' => 'Mail has sent already global mobility team will contact you'], 200);
                }
                $ids=DB::table('vrf_need_assistance_log')->where('request_id',$request_id)->value('id');
                DB::table('vrf_need_assistance_log')->where('id',$ids)->update(['mail_sent_at'=>1]);
            }
            foreach($mails_involved as $mails){
                $mails=$this->fetch_mail_details($mails,$request_id,$action, $mail_flag);
            }
        }
        catch(\Exception $e){
            Log::error("Error in process_mails");;
            Log::error($e);
        }
    }
     public function fetch_mail_details($mail_name,$request_id,$action,$mail_flag="travel"){
     //   public function fetch_mail_details(){
        try{
            // $mail_name='travel_submit';
            // $request_id=3351;
            // $action='submit';
            // $mail_name=$request['mail_name'];
            // $request_id=$request['request_id'];
            // $action=$request['action'];
            $mail_details=DB::table('trf_mail_configurations')
            ->where('mail_name',$mail_name)
            ->where('active',1)
            ->first();
            $to_list=$mail_details->to;
            $cc_list=$mail_details->cc;
            $bcc_list=$mail_details->bcc;
            $custom_to_list=$mail_details->custom_to;
            $custom_cc_list=$mail_details->custom_cc;
            $custom_bcc_list=$mail_details->custom_bcc;
            $to_address=$this->fetch_recipients($to_list,$request_id);
            $cc_address=$this->fetch_recipients($cc_list,$request_id);
            $bcc_address=$this->fetch_recipients($bcc_list,$request_id);
            if($custom_to_list){
                $cust_to_array=explode(',',$custom_to_list);
                $cust_to_array=array_filter($cust_to_array);
                if(count($cust_to_array)){
                    $to_address=array_merge($to_address,$cust_to_array);
                }
            }
            if($custom_cc_list){
                $cust_cc_array=explode(',',$custom_cc_list);
                $cust_cc_array=array_filter($cust_cc_array);
                if(count($cust_cc_array)){
                    $cc_address=array_merge($cc_address,$cust_cc_array);
                }
            }
            if($custom_bcc_list){
                $cust_bcc_array=explode(',',$custom_bcc_list);
                $cust_bcc_array=array_filter($cust_bcc_array);
                if(count($cust_bcc_array)){
                    $bcc_address=array_merge($bcc_address,$cust_bcc_array);
                }
            }
            // Custom recipients...
            $custom_mail_recipients = $this->fetch_custom_mail_recipients($mail_name, $request_id);
            $to_address =  array_unique( array_merge($to_address, $custom_mail_recipients["to_list"]) );
            $cc_address =  array_unique( array_merge($cc_address, $custom_mail_recipients["cc_list"]) );

            $subject=$mail_details->subject;
            $subject=$this->replace_place_holder($subject,$request_id,$action,$mail_name,$mail_flag);
            $body=$mail_details->body;
            $body=$this->replace_place_holder($body,$request_id,$action,$mail_name,$mail_flag);
            $mail_configuration_details=[
                'to_address'=>$to_address,
                'cc_address'=>$cc_address,  
                'bcc_address'=>$bcc_address,
                'subject'=>$subject,
                'body'=>$body
            ];

            $attachment_details = $this->addAttachment($mail_name, $request_id);
            if(array_key_exists('error', $attachment_details))
                throw $attachment_details['error'];

            $attachments = null;
            if( isset($attachment_details['has_attachment']) )
                $attachments = $attachment_details['attachment_details'];
            $send_mail=$this->send_mail($mail_configuration_details, $attachments);
            Log::info('Mail send successfully');
        }
        catch(\Exception $e){
            dd($e);
            Log::error("Err in fetch_mail_details");
            Log::error($e);
        }
        
    }

    public function send_mail($mail_configuration_details, $attachments=null){
        try{
            Mail::send('layouts.mails.travel_request', $mail_configuration_details, function($message) use ($mail_configuration_details, $attachments)
            {
                $message->to(['ganesh.veilsamy@aspiresys.com','bala.bashiyam@aspiresys.com','bhuva.seetharaman@aspiresys.com', 'venkatesan.raj@aspiresys.com', 'sasi.kuppuswamy@aspiresys.com', 'monisha.thirumalai@aspiresys.com'])->subject($mail_configuration_details['subject']);
                //$message->to($mail_configuration_details['to_address'])->subject($data['subject']);
                //$message->cc($mail_configuration_details['cc_address']);
                $message->from('admin@gmail.com', 'Admin');
                if( isset($attachments) && is_array($attachments) && count($attachments) ) 
                    $message->attach($attachments['file_path'], $attachments['config']);
            });
        } catch (\Exception $e) {
            Log::error("Error in send_mail");
            Log::error($e);
            throw $e;
            return ["error" => $e];
        }
    }

    public function fetch_recipients($address,$request_id){
        try{
            $address_list=[];
            $details_provider_obj=new DetailsProvider();
            $final_address_list=[];
            if(is_array($address))
                $address_list=$address;
            else
                $address_list=explode(',',$address);
            $roles_with_direct_mapping=['FIN_APP','BF_REV',"HR_PTR"]; // Need to change
            $roles_with_condition_mapping=['AN_COST_FIN','TRV_PROC_TICKET','TRV_PROC_VISA','TRV_PROC_FOREX','DOM_TCK_ADM','AN_COST_FAC', 'AN_COST_VISA','GM_REV','HR_REV'];
            // Visa related roles
            $roles_with_direct_mapping_visa=['US_HR_ADM'];
            $roles_with_direct_mapping = array_merge($roles_with_direct_mapping, $roles_with_direct_mapping_visa);
            $role_based_address=array_intersect($roles_with_direct_mapping,$address_list);
            if(count($role_based_address)){
                $email_address=DB::table('trf_user_role_mapping as urm')
                ->leftJoin('users as u','u.aceid','=','urm.aceid')
                ->whereIn('urm.role_code',$role_based_address)
                ->where('u.active',1)
                ->pluck('email')->toArray();
                $final_address_list=array_merge($final_address_list,$email_address);
            }
            
            $role_with_condition_based_access=array_intersect($roles_with_condition_mapping,$address_list);
            if(count($role_with_condition_based_access)){
                $dp_obj=new DetailsProvider();
                $respective_users=$dp_obj->get_travel_desk_user_details($request_id,$role_with_condition_based_access);  
                $email_address=DB::table('users')->whereIn('aceid',$respective_users)->where('active',1)->pluck('email')->toArray();
                $final_address_list=array_merge($final_address_list,$email_address);
            }

            if(in_array('REQ',$address_list)){
                $request_details=$details_provider_obj->request_full_details($request_id);
                if(array_key_exists('request_details',$request_details)){
                    $travel_id=$request_details['request_details']->travaler_id;
                    $traveler_email=DB::table('users')->where('aceid',$travel_id)
                        ->where('active',1)->value('email');
                    if($traveler_email)
                        $final_address_list[]=$traveler_email;
                }
            }
            if(in_array('CRE', $address_list)) {
                $request_details=$details_provider_obj->request_full_details($request_id);
                if(array_key_exists('request_details',$request_details)){
                    $travel_id=$request_details['request_details']->created_by;
                    $traveler_email=DB::table('users')->where('aceid',$travel_id)
                        ->where('active',1)->value('email');
                    if($traveler_email)
                        $final_address_list[]=$traveler_email;
                }
            }
            $approval_matrix_based_mapping=['PRO_OW','PRO_OW_HIE','DU_H','DU_H_HIE','DEP_H','CLI_PTR','GEO_H'];
            $am_based_address=array_intersect($approval_matrix_based_mapping,$address_list);
            if(count($am_based_address)){
                $email_address=DB::table('trf_approval_matrix_tracker as t')
                    ->leftJoin('users as u','u.aceid','=','respective_role_or_user')
                    ->where('t.request_id',$request_id)->where('t.active',1)->where('u.active',1)
                    ->whereIn('t.flow_code',$am_based_address)
                    ->pluck('u.email')->toArray();
                $final_address_list=array_merge($final_address_list,$email_address);
            }
            // Visa related mail list
            if( in_array('VIS_USR', $address_list) ) {
                $email_address = DB::table('trf_travel_request as tr')
                                ->leftJoin('users as u', 'u.aceid', 'tr.travaler_id')
                                ->where([['tr.id', $request_id],['u.active',1]])->value('u.email');
                array_push($final_address_list, $email_address);
            }
            if( in_array('HR_PRT', $address_list) ) {
                $email_address = DB::table('trf_travel_request as tr')
                                ->leftJoin('vrf_visa_request_details as vr', function ($join) { $join->on('vr.request_id', 'tr.id')->where('vr.active', 1); })
                                ->leftJoin('users as u', 'u.aceid', 'vr.hr_partner')
                                ->where([['tr.id', $request_id],['u.active', 1]])->value('u.email');
                array_push($final_address_list, $email_address);
            }
            return $final_address_list;
        }
        catch(\Exception $e){
            Log::error("err in fetch_recipients");
            Log::error($e);
        }
        
    }
    
    public function replace_place_holder($content,$request_id,$action,$mail_name, $request_type_flag = "travel"){
        try{
            
     
            if($request_type_flag === "visa" && false){

                $visa_request_object =new DetailsProvider();
                $request_types=['MOD_01'=>'domestic travel','MOD_02'=>'travel','MOD_03'=>'visa'];
                $visa_request_details = json_decode(json_encode($visa_request_object->request_full_details($request_id)),true);
                $content_replace_details=[
                    "__VISAREQFULLNAME__"=>DB::table('users')->where([['aceid', $visa_request_details['request_details']['travaler_id']],['active',1]])->value('firstname'),
                    "__REQFULLNAME__"=>DB::table('users')->where([['aceid', $visa_request_details['request_details']['travaler_id']],['active',1]])->value('firstname'),
                    "__VISAREQCODE__"=>isset($visa_request_details) ? $visa_request_details['request_details']["request_code"] : null,
                    "__TRVREQUESTCODE__"=>isset($visa_request_details) ? $visa_request_details['request_details']["request_code"] : null,
                    "__VISADETAILSTABLE__"=>'',
                    "__REQUEST_TYPE__"=>$request_type_flag,
                    "__REQUEST_TYPE_CAP__"=>ucfirst($request_types[$visa_request_details['request_details']['module']]),            
                ];
            } else {
                $detail_object=new DetailsProvider();
                $request_details=$detail_object->request_full_details($request_id);
                $request_types=['MOD_01'=>'domestic travel','MOD_02'=>'travel','MOD_03'=>'visa'];
                $content_replace_details=[
                    "__TRVREQUESTCODE__"=>$request_details['request_details']->request_code,
                    "__REQFULLNAME__"=>$request_details['request_details']->traveler_full_name,
                    "__DETAILSTABLE__"=>'',
                    "__REQUEST_LINK__"=>$request_details['request_details']->module == "MOD_03"
                                    ?   url("visa_request/".Crypt::encrypt($request_id))
                                    :   url("request_full_details/".Crypt::encrypt($request_id)),
                    "__BILLABLE_APPROVE_CONTENT__"=>$this->billable_approve_content($request_details,
                    $mail_name),
                    "__REQUEST_TYPE_CAP__"=>ucfirst($request_types[$request_details['request_details']->module]),
                    "__REQUEST_TYPE__"=>$request_types[$request_details['request_details']->module],
                ];
            }
            $final_content=$content;
            foreach($content_replace_details as $key=>$detail){
                if($key=='__DETAILSTABLE__'){
                    $detail=$this->request_mail_table_creation($request_details,$action);
                    $final_content=str_replace($key,$detail,$final_content);
                } else if( isset($visa_request_details) && $key=='__VISADETAILSTABLE__' ) {
                    $detail=$this->request_visa_mail_table_creation($visa_request_details, $action);
                    $final_content=str_replace($key,$detail,$final_content);
                }
                else
                $final_content=str_replace($key,$detail,$final_content);
            }
            return $final_content;
        }
        catch(\Exception $e){
            Log::error("Err in replace_place_holder");
            Log::error($e);
        }
    }

    public function request_mail_table_creation($request_details,$action){
        return View::make('layouts.mails.request_detail_table')->with(['request_details'=>$request_details,'action'=>$action])->render();
    }

    public function request_visa_mail_table_creation($request_details,$action)
    {
        $data = compact('request_details', 'action');
        
        return View::make('layouts.mails.visa_process_mail')->with(['request_details'=>$data['request_details'],'action'=>$data['action']])->render();
    }

    public function billable_approve_content($request_details,$mail_name){
        $content='';
        //un comment this line during production move
        //$mail_id="trs.approval@aspiresys.com";
        $mail_id="ganesh.veilsamy@aspiresys.com";
        $request_code=$request_details['request_details']->request_code;
        $requestor_full_name=$request_details['request_details']->traveler_full_name;
        $request_id=$request_details['request_details']->travel_request_id;
        if(in_array($mail_name,['client_partner_approval','dept_head_approval','du_h_approval','final_approval','geo_head_approval','po_approval','po_h_approval'])){
            if(is_null($request_details['request_details']->billed_to_client)){
                $content='<p>You can approve/reject this request by forwarding to trs.approval@aspiresys.com with either of the below format as subject or please <a href="'.url("request_full_details/".Crypt::encrypt($request_id)).'" target="_blank">click here</a> to approve/reject the request.<br></p>
                <p><a href="'.url("mailto:".$mail_id."?subject=TravelRequested-".$requestor_full_name." -".$request_code."-Approved-Yes").'">TravelRequested-'.$requestor_full_name.'- '.$request_code.'-Approved-Yes</a></br><a href="'.url("mailto:".$mail_id."?subject=TravelRequested-".$requestor_full_name." -".$request_code."-Approved-No").'">TravelRequested-'.$requestor_full_name.'- '.$request_code.'-Approved-No</a></br>
                <a href="'.url("mailto:".$mail_id."?subject=TravelRequested-".$requestor_full_name." -".$request_code."-Rejected").'">TravelRequested-'.$requestor_full_name.'- '.$request_code.'-Rejected</a></p>
                    <p><strong>* Yes / No - indicates "Billable to client".</strong></p>';
            }
            else{
                $content='<p>You can approve/reject this request by forwarding to trs.approval@aspiresys.com with either of the below format as subject or please <a href="'.url("request_full_details/".Crypt::encrypt($request_id)).'" target="_blank">click here</a> to approve/reject the request.<br></p>
                    <p><a href="'.url("mailto:".$mail_id."?subject=TravelRequested-".$requestor_full_name." -".$request_code."-Approved").'">TravelRequested-'.$requestor_full_name.'- '.$request_code.'-Approved</a></br>
                    <a href="'.url("mailto:".$mail_id."?subject=TravelRequested-".$requestor_full_name." -".$request_code."-Rejected").'">TravelRequested-'.$requestor_full_name.'- '.$request_code.'-Rejected</a></p>';
            }
        }
        return $content;
    }

    /**
     * To add attachment
     * @author venkatesan.raj
     * 
     * @param string $mail_name
     * @param string $request_id
     * 
     * @return array
     */
    public function addAttachment($mail_name, $request_code)
    {
        try
        {
            $has_attachment = false; $attachment_details = [];
            if( $mail_name === "VisaStamping" ) {
                $pdf_path = DB::table('visa_process_request_details as pr')
                                    ->leftJoin('visa_process_tracking_details as pt', 'pt.process_request_id', 'pr.id')
                                    ->where([['pr.request_code', $request_code],['pr.active',1]])->value('pt.offer_letter_path');
                [$offer_letter_path, $immigration_offer_letter_path] = explode(',', $pdf_path);
                $offer_letter_path = public_path(substr(explode(",",$offer_letter_path)[0],6));
                $file_name = 'Offer letter.pdf';
                $mime_type = 'application/pdf';
                $has_attachment = true;
                $attachment_details = [ "file_path" => $offer_letter_path, "config" => ['as' => $file_name, 'mime' => $mime_type] ];
            }

            return compact('has_attachment', 'attachment_details');
        }
        catch (\Exception $e)
        {
            Log::error('Error in addAttachment');
            Log::error($e);
            return ['has_attachment' => false, 'error' => $e ];
        }
    }
    public function need_assistance_log($mail_name,$request_id){
        $ids=DB::table('vrf_need_assistance_log')->where('request_id',$request_id)->value('id');
	    $log_count = DB::table('vrf_need_assistance_log')->where('request_id', $request_id)->count();
        if($log_count<=1){
            DB::table('vrf_need_assistance_log')->where('id',$ids)->update(['mail_sent_at'=>1]);
        }
        $created_at=DB::table('vrf_need_assistance_log')->where('request_id',$request_id)->where('mail_sent_at',1)->orderBy('id', 'desc')->first();
        if($created_at && $log_count > 1){
            $already_created_time = new DateTime($created_at->created_at);
            $current_time = new DateTime(date('Y-m-d H:i:s'));
            $interval = $already_created_time->diff($current_time);
            if ($interval->days >= 1 || ($interval->h >= 24 && $interval->days == 0)) {
                return true;
            } else {
                return false;
            }
         }else{
            return true;
        }
    }
     /**
     * Returns the aceid of recipients added on some special cases
     * @author venkatesan.raj
     * 
     * @param string $mail_name
     * @param string $request_id
     * 
     * @return array
     */
    public function fetch_custom_mail_recipients($mail_name, $request_id)
    {
        try {
            $provider = new DetailsProvider();
            $travel_details = DB::table('trf_travel_request')->where('id', $request_id);
            $traveling_details = DB::table('trf_traveling_details')->where([['request_id', $request_id],['active', 1]]);
            $visa_details = DB::table('vrf_visa_request_details')->where([['request_id', $request_id],['active', 1]]);
            switch($mail_name) {
                case "Initiation":
                    $params = [
                        "module" => $travel_details->value('module'),
                        "to_country" => $traveling_details->value('to_country'),
                        "visa_type" => $visa_details->value('visa_type'),
                        "status_id" => $travel_details->value('status_id')
                    ];
                    $to_list = $provider->get_values_against_rule($params+["config_name"=>"mail_to"], [":REQ_ID" => $request_id]);
                    $to_list = $to_list && is_array($to_list) && count($to_list) ? array_column($to_list, 'email') : [];
                    $cc_list = $provider->get_values_against_rule($params+["config_name"=>"mail_cc"], [":REQ_ID" => $request_id]);
                    $cc_list = $cc_list && is_array($cc_list) && count($cc_list) ? array_column($cc_list, 'email') : [];
                    break;
                default:
                    $to_list = []; $cc_list = [];
            }
            return compact("to_list", "cc_list");
        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__);
            Log::error($e);
            throw $e;
        }
    }
}
