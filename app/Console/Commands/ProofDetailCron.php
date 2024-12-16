<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Curl\Curl;
use Auth;
use DB;
use Log;
use Exception;
use App\Models\User;

class ProofDetailCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proof-details:cron {--aceid=ACE1369}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To load the proof details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try
        {
            ini_set('max_execution_time', 7200);

            $user = Auth::User();
            if(!$user){
                $aceid = $this->option('aceid');
                $user = User::firstWhere([['aceid', $aceid],['active',1]]);
            }
            else{
                $aceid = $user->aceid;
            }

            // Configuration
            $controller = new \App\Http\Controllers\Controller();
            $service_config = $controller->service_url_config;

            $service_name = "PROOFDETAILS";
            $url = $service_config[$service_name]["url"];
            $username = $service_config[$service_name]["username"];
            $password = $service_config[$service_name]["password"];
            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setBasicAuthentication($username, $password);
            $curl->setDefaultTimeout(120);
            $data = json_encode(array(
                'aceNumber' => [$aceid],
            ));
            $curl->post($url, $data);
            if ($curl->error)
            {
                echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
                throw new Exception($curl->errorCode . ': ' . $curl->errorMessage);
            }
            else
            {
                $user_details = $curl->response;
                
                $user_details = property_exists($user_details, 'Employees') ? $user_details->Employees : null;

                if($user_details && is_array($user_details) && count($user_details)){
                    foreach($user_details as $detail)
                    {
                        if(isset($detail))
                        {
                            //Extract data from response
                            $AadhaarFile = property_exists($detail, 'AadhaarFile') ? ($detail->AadhaarFile ? $detail->AadhaarFile : null) : null;
                            $Address = property_exists($detail, 'Address') ? $detail->Address : null;
                            $AdhaarCard = property_exists($detail, 'AdhaarCard') ? $detail->AdhaarCard : null;
                            $DOB = property_exists($detail, 'DOB') ? ( $detail->DOB ? date('Y-m-d h:i:s',strtotime($detail->DOB)) : null ) : null;
                            $EmailID = property_exists($detail, 'EmailID') ? $detail->EmailID : null;
                            $EmployeeNumber = property_exists($detail, 'EmployeeNumber') ? ( $detail->EmployeeNumber ? $detail->EmployeeNumber : null ) : null;
                            $Entity = property_exists($detail, 'Entity') ? ( $detail->Entity ? $detail->Entity : null ) : null;
                            $MobileNumber = property_exists($detail, 'MobileNumber') ? ($detail->MobileNumber ? $detail->MobileNumber : null ) : null;
                            $NameInAadhaar = property_exists($detail, 'NameInAadhaar') ? ($detail->NameInAadhaar ? $detail->NameInAadhaar : null) : null;
                            $NameInAadhaar = $NameInAadhaar ? $NameInAadhaar : $user->FirstName;
                            $NameInPancard = property_exists($detail, 'NameInPancard') ? ($detail->NameInPancard ? $detail->NameInPancard : null) : null;
                            $NameInPancard = $NameInPancard ? $NameInPancard : $user->FirstName;
                            $Nationality = property_exists($detail, 'Nationality') ? ($detail->Nationality ? $detail->Nationality : null) : null;
                            $PANNumber = property_exists($detail, 'PANNumber') ? ($detail->PANNumber ? $detail->PANNumber : null) : null;
                            $PancardFile = property_exists($detail, 'PancardFile') ? ($detail->PancardFile ? $detail->PancardFile : null) : null;
                            $PassportFile = property_exists($detail, 'PassportFile') ? ($detail->PassportFile ? $detail->PassportFile : null) : null;
                            $VisaExpiryDate = property_exists($detail, 'VisaExpiryDate') ? ($detail->VisaExpiryDate ? date('Y-m-d h:i:s', strtotime($detail->VisaExpiryDate)) : null) : null;
                            $VisaFile = property_exists($detail, 'VisaFile') ? ($detail->VisaFile ? $detail->VisaFile : null) : null;
                            $VisaIssuedDate = property_exists($detail, 'VisaIssuedDate') ? ($detail->VisaIssuedDate ? date('Y-m-d h:i:s', strtotime($detail->VisaIssuedDate)) : null) : null;
                            $VisaNumber = property_exists($detail, 'VisaNumber') ? ($detail->VisaNumber ? $detail->VisaNumber : null) : null;
                            $VisaType = property_exists($detail, 'VisaType') ? ($detail->VisaType ? $detail->VisaType : null) : null;
                            $VisaCountry = property_exists($detail, 'VisaCountry') ? ($detail->VisaCountry ? $detail->VisaCountry : null) : null;
                            $VisaCount = property_exists($detail, 'VisaCount') ? ($detail->VisaCount ? $detail->VisaCount : null):null;
                            $VisaDetails = property_exists($detail, 'VisaDetails') ? ($detail->VisaDetails ? $detail->VisaDetails : null):null;
                            $PassportCount = property_exists($detail, 'PassportCount') ? ($detail->PassportCount ? $detail->PassportCount : null):null;
                            $PassportDetails = property_exists($detail, 'PassportDetails') ? ($detail->PassportDetails ? $detail->PassportDetails : null):null;
                            $VisaType = $VisaType ? DB::table('trd_visa_type')->where([['name', $VisaType],['active',1]])->value('unique_key') :  null;
                            $VisaCountry = $VisaCountry ? DB::table('trd_country_details')->where([['name', $VisaCountry],['active',1]])->value('unique_key') :  null;
                            $SourceCompanyID = $Entity ? DB::table('trd_entity')->where([['entity_name', $Entity],['active', 1]])->value('unique_key') : null;

                            // Fetch passport details
                            $LatestPassportDetails = [];
                            if(isset($PassportDetails) && (int)$PassportCount > 0){
                                $PassportDetails = json_decode(json_encode($PassportDetails),true);
                                $this->inactivePassports($PassportDetails, $aceid);
                                $PassportDetails = array_filter( $PassportDetails, fn($e) => array_key_exists('IsCurrentlyValid', $e) && isset($e['IsCurrentlyValid']) && !in_array( $e['IsCurrentlyValid'], ['0', 'Inactive'] ) );
                                if(count($PassportDetails)){
                                    $LatestPassportDetails = array_reduce($PassportDetails, function($item, $accumulator) {
                                        if(!isset($accumulator))
                                            $accumulator = $item;
                                        else{
                                            $prevExpiryDate = array_key_exists('ExpiryDate', $accumulator) ? ( $accumulator['ExpiryDate'] ? date('Y-m-d h:i:s', strtotime($accumulator['ExpiryDate'])) : null ) : null;
                                            $curExpiryDate = array_key_exists('ExpiryDate', $item) ? ( $item['ExpiryDate'] ? date('Y-m-d h:i:s', strtotime($item['ExpiryDate'])) : null ) : null;
                                            if(is_null($prevExpiryDate) || ($curExpiryDate > $prevExpiryDate)){
                                                $accumulator = $item;
                                            }
                                        }
                                        return $accumulator;
                                    }, []);
                                }
                            }

                            $PassportExpiryDate = array_key_exists('ExpiryDate', $LatestPassportDetails) ? ($LatestPassportDetails['ExpiryDate'] ? date('Y-m-d h:i:s', strtotime($LatestPassportDetails['ExpiryDate'])) : null) : null;
                            $PassportIssueDate = array_key_exists('IssueDate', $LatestPassportDetails) ? ($LatestPassportDetails['IssueDate'] ? date('Y-m-d h:i:s', strtotime($LatestPassportDetails['IssueDate'])) : null) : null;
                            $PassportNumber = array_key_exists('PassportNumber', $LatestPassportDetails) ? ($LatestPassportDetails['PassportNumber'] ? $LatestPassportDetails['PassportNumber'] : null) : null;
                            $PassportPlaceofissue = array_key_exists('PlaceOfIssue', $LatestPassportDetails) ? ($LatestPassportDetails['PlaceOfIssue'] ? $LatestPassportDetails['PlaceOfIssue'] : null) : null;
                            $NameInPassport = array_key_exists('NameInPassPort', $LatestPassportDetails) ? ($LatestPassportDetails['NameInPassPort'] ? $LatestPassportDetails['NameInPassPort'] : null) : null;
                            $group_id = $this->get_group_id();

                            // Fetch visa related details
                            if(isset($VisaDetails) && $VisaCount > 0){
                                $VisaDetails = json_decode(json_encode($VisaDetails), true);
                            }

                            $file_details = compact('AadhaarFile', 'PancardFile', 'PassportFile', 'VisaFile');
                            $user_details = compact('AdhaarCard', 'NameInAadhaar', 'PANNumber', 'NameInPancard', 'PassportNumber', 'NameInPassport', 'PassportIssueDate', 'PassportExpiryDate', 'PassportPlaceofissue', 'Address', 'DOB', 'EmailID', 'MobileNumber', 'Nationality', 'SourceCompanyID', 'VisaNumber', 'VisaType', 'VisaIssuedDate', 'VisaExpiryDate', 'VisaCountry', 'PassportCount', 'VisaCount');

                            // Insert / Update the date in DB
                            DB::beginTransaction();

                            // Get all the tables needs to be updated
                            $table_list = DB::table('trd_user_attr_mapping_key')->selectRaw("table_affected,JSON_OBJECTAGG(unique_key, api_key_name) as fields")->where('active',1)->whereIn('api_key_name', array_keys($user_details))->groupBy('table_affected')->get()->toArray();
                            $table_list = json_decode(json_encode($table_list),true);
                            $table_list = array_combine(
                                array_column($table_list, 'table_affected'),
                                array_map(fn($e) => (array)json_decode($e['fields']), $table_list)
                            );

                            // Insertion configuration
                            $table_insertion_config = [
                                'trf_user_detail_mapping' => 'row_wise',
                                'users' => 'column_wise',
                                'trd_visa_details' => 'column_wise',
                            ];
                            foreach($table_list as $table_name => $fields)
                            {
                                $insertion_type = array_key_exists($table_name, $table_insertion_config) ? $table_insertion_config[$table_name] : null;
                                $values_to_insert = []; $condition = []; $proof_details = []; $common_fields = [];
                                if($insertion_type == 'row_wise')
                                {
                                    switch($table_name)
                                    {
                                        case 'trf_user_detail_mapping': 
                                            $passport_attributes = ['USRATTR_29', 'USRATTR_30', 'USRATTR_31', 'USRATTR_32', 'USRATTR_33'];
                                            $proof_details = array_map(
                                                fn($attr_name, $attr) => [
                                                    'aceid' => $EmployeeNumber,
                                                    'attribute' => $attr,
                                                    'attribute_name' => $attr_name,
                                                    'mapping_value' => $user_details[$attr_name],
                                                    'group_id' => in_array($attr, $passport_attributes) ? $group_id : null,
                                                    'active' => 1,
                                                    'created_at' => date('Y-m-d h:i:s'),
                                                    'updated_at' => date('Y-m-d h:i:s'),
                                                ],
                                                $fields,
                                                array_keys($fields),
                                            );
                                            $proof_details = array_filter($proof_details, fn($e) => $e['mapping_value']);
                                            break;
                                        default;
                                            continue 2;
                                    }
                                    // Skipping in case of data doesn't exists
                                    if(count($proof_details) == 0)
                                        continue;
                                    foreach($proof_details as $proof_detail)
                                    {
                                        $values_to_insert = array_filter($proof_detail, fn($k) => in_array($k, ['attribute_name', 'mapping_value', 'group_id', 'active', 'created_at', 'updated_at']), ARRAY_FILTER_USE_KEY);
                                        $condition = array_filter($proof_detail, fn($k) => in_array($k, ['aceid', 'attribute']), ARRAY_FILTER_USE_KEY);
                                        if(DB::table($table_name)->where($condition)->exists())
                                            unset($values_to_insert['created_at']);
                                        DB::table($table_name)->updateOrInsert($condition, $values_to_insert);
                                    }
                                }
                                if($insertion_type == 'column_wise')
                                {
                                    switch($table_name)
                                    {
                                        case 'users':
                                            $values_to_insert = [
                                                'DateOfBirth' => $user_details['DOB'],
                                                'SourceCompanyID' => $user_details['SourceCompanyID'],
                                            ];
                                            $values_to_insert = array_filter($values_to_insert);
                                            // Skipping in case of data doesn't exists
                                            if(count($values_to_insert) == 0)
                                                continue 2;
                                            $common_fields = [
                                                'updated_at' => date('Y-m-d h:i:s'),
                                            ];
                                            $condition = [
                                                'aceid' => $EmployeeNumber,
                                            ];
                                            break;
                                        case 'trd_visa_details':
                                            $values_to_insert = [
                                                'visa_number' => $user_details['VisaNumber'],
                                                'visa_issue_date' => $user_details['VisaIssuedDate'],
                                                'visa_expiry_date' => $user_details['VisaExpiryDate'],
                                            ];
                                            $values_to_insert = array_filter($values_to_insert);
                                            // Skipping in case of data doesn't exists
                                            if(count($values_to_insert) == 0 && false)
                                                continue 2;
                                            $common_fields = [
                                                'active' => 1,
                                                'created_by' => 'system',
                                                'created_at' => date('Y-m-d h:i:s'),
                                                'updated_at' => date('Y-m-d h:i:s'),
                                            ];
                                            $condition = [
                                                'aceid' => $EmployeeNumber,
                                                'visa_country_code' => array_key_exists('VisaCountry', $user_details) ? $user_details['VisaCountry'] : null,
                                                'visa_type' => $user_details['VisaType'],
                                            ];
                                            break;
                                        default:
                                            continue 2;
                                    }
                                    $values_to_insert = array_merge($values_to_insert, $common_fields);
                                    if(DB::table($table_name)->where($condition)->exists())
                                        unset($values_to_insert['created_at']);
                                    DB::table($table_name)->updateOrInsert($condition, $values_to_insert);
                                }
                            }
                            DB::commit();
                            Log::info('Details loaded for the user: '.$aceid);
                        }
                    }
                }
            }                
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Err in proof-details:cron');
            Log::error($e);
        }
    }
    public function inactivePassports($passportDetails, $aceid)
    {
        try {
            $passportDetails = array_filter( $passportDetails, fn($e) => array_key_exists('IsCurrentlyValid', $e) && isset($e['IsCurrentlyValid']) && in_array( $e['IsCurrentlyValid'], ['Inactive'] ) );
            foreach($passportDetails as $passportDetail) {
                $passportNumber = $passportDetail['PassportNumber'] ?? null;
                if($passportNumber) {
                    $group_id = DB::table('trf_user_detail_mapping')
                    ->where([
                        ['aceid', $aceid],
                        ['attribute', 'USRATTR_29'],
                        ['mapping_value', $passportNumber]
                    ])->value('group_id');

                    DB::table('trf_user_detail_mapping')->where('group_id', $group_id)->update(['active' => 0]);
                }
            }

        } catch (\Exception $e) {
            Log::error("Error in ".__FUNCTION__." : ".$e->getMessage());
            throw $e;
        }
    }
    public function get_group_id() : int
    {
        $group_id = (int)DB::table('trf_user_detail_mapping')->orderBy('group_id', 'desc')->value('group_id');
        return ++$group_id;
    }
}
