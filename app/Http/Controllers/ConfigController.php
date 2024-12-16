<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function __construct(){
        $this->CONFIG=[
            'CUSTOM_PROJECT'=>['CUST_PROJ_007'],
            'DEFAULT_PROJECT'=>[
                'FUNCTIONAL' => 'CUST_PROJ_007',
            ],
            'REQUEST_FOR_SELF'=>['RF_01','RF_05','RF_08'],
            'REQUEST_FOR_FAMILY'=>['RF_02','RF_06'],
            'ON_BEHALF_LIST'=>['RF_10', 'RF_11', 'RF_12'],
            'REQUEST_FOR_SELF_DOM'=>'RF_01',
            'REQUEST_FOR_FAMILY_DOM'=>'RF_02',
            'REQUEST_FOR_SELF_IN'=>'RF_05',
            'REQUEST_FOR_FAMILY_IN'=>'RF_06',
            'REQUEST_FOR_SELF_VIS'=>'RF_08',
            'REQUEST_FOR_FAMILY_VIS'=>'RF_14',
            'BILLABLE_CHOOSE_ACCESS'=>['PRO_OW','PRO_OW_HIE','DU_H','DU_H_HIE','DEP_H','FIN_APP','GEO_H','CLI_PTR','BF_REV'],
            'RELEVENT_PROOF_TYPE'=>[
                "AADHAR" => ["PR_TY_01_01"],
                "PANCARD" => ["PR_TY_01_02", "PR_TY_02_02", "PR_TY_03_02"],
                "PASSPORT" => ["PR_TY_01_04", "PR_TY_02_01", "PR_TY_03_01"]
            ],
            'COMMENTS_ENABLED_STATUS'=>['STAT_01','STAT_02','STAT_03','STAT_04','STAT_05','STAT_06','STAT_07',
                                        'STAT_08','STAT_09','STAT_10','STAT_11','STAT_24','STAT_25','STAT_28',
                                        'STAT_29','STAT_30','STAT_31','STAT_33','STAT_35','STAT_38'],
            'BILLABLE_ENABLED_STATUS'=>['STAT_01','STAT_03','STAT_04','STAT_05','STAT_06','STAT_07',
                                        'STAT_08','STAT_09','STAT_10','STAT_11','STAT_24','STAT_25'],
            'COMMENTS_ENABLE_FOR_ROLES'=>[
                'STAT_02'=>['AN_COST_FIN','AN_COST_FAC','AN_COST_VISA'],
                'STAT_04'=>['PRO_OW'],
                'STAT_05'=>['BF_REV'],
                'STAT_06'=>['PRO_OW_HIE'],
                'STAT_08'=>['DU_H'],
                'STAT_09'=>['DU_H_HIE'],
                'STAT_10'=>['DEP_H'],
                'STAT_11'=>['FIN_APP'],
                'STAT_24'=>['CLI_PTR'],
                'STAT_25'=>['GEO_H'],
                'STAT_28'=>['VIS_USR'],
                'STAT_29'=>['HR_REV'],
                'STAT_30'=>['HR_PRT'],
                'STAT_31'=>['GM_REV'],
                'STAT_33'=>['GM_REV'],
                'STAT_35'=>['VIS_USR'],
                'STAT_38'=>['GM_REV'],
            ],                                        
            'FILENAME' => [
                'PR_TY_01_01' => 'Aadhaar',
                'PR_TY_01_02' => 'Pancard',
                'PR_TY_01_03' => 'Driving_license',
                'PR_TY_01_04' => 'Passport',
                'PR_TY_02_01' => 'Passport',
                'PR_TY_02_02' => 'Pancard',
                'PR_TY_03_01' => 'Passport',
                'PR_TY_03_02' => 'Pancard',
            ],
            "USRATTR" => [
                'ADDRESS' => 'USRATTR_37',
                'PHONE_NO' => 'USRATTR_38',
                'NATIONALITY' => 'USRATTR_40',
            ],
            "REMARKS" => [
                'requestor_remarks' => ['0-STAT_01','STAT_01-STAT_01'],
            ],
            "STATUS_TRACKER" => [
                "STAT_01" => [
                    0 => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_01" => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                ],
                "STAT_28" => [
                    0 => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_01" => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                ],
                "STAT_02" => [
                    0 => ["ACTION" => "Submitted on", "ROLE" => "Requestor"],
                    "STAT_01" => ["ACTION" => "Submitted on", "ROLE" => "Requestor"],
                    "STAT_28" => ["ACTION" => "Submitted on", "ROLE" => "Employee"],
                    "STAT_02" => [
                        "desk_review_fac" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Facilities)"],
                        "desk_review_fin" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Finance)"],
                        "desk_review_visa" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Visa)"],
                    ],
                ],
                "STAT_04" => [
                    0 => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_01" => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_02" => [
                        "desk_review_fac" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Facilities)"],
                        "desk_review_fin" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Finance)"],
                        "desk_review_visa" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Visa)"],
                    ],
                ],
                "STAT_05" => [
                    0 => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_01" => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_02" => [
                        "desk_review_fac" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Facilities)"],
                        "desk_review_fin" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Finance)"],
                        "desk_review_visa" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Visa)"],
                    ],
                    "STAT_04" => [ "ACTION" => "Approved on", "ROLE" => "Project owner" ],
                    "STAT_10" => ["ACTION" => "Approved on", "ROLE" => "Department head"],
                    "STAT_08" => ["ACTION" => "Approved on", "ROLE" => "IDO"],
                    "STAT_24" => ["ACTION" => "Approved by", "ROLE" => "Client partner"],
                    "STAT_25" => ["ACTION" => "Approved on", "ROLE" => "Geo head"],
                ],
                "STAT_06" => [
                    0 => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_01" => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_05" => ["ACTION" => "Reviewed on", "ROLE" => "BF reviewer"],
                    "STAT_06" => ["ACTION" => "Approved on", "ROLE" => "Project owner's hierarchy manager"],
                ],
                "STAT_08" => [
                    0 => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_01" => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_02" => [
                        "desk_review_fac" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Facilities)"],
                        "desk_review_fin" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Finance)"],
                        "desk_review_visa" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Visa)"],
                    ],
                    "STAT_05" => ["ACTION" => "Reviewed on", "ROLE" => "BF reviewer" ],
                    "STAT_06" => ["ACTION" => "Approved on", "ROLE" => "Project owner's hierarchy manager"],
                ],
                "STAT_09" => [
                    0 => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_01" => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_09" => ["ACTION" => "Approved on", "ROLE" => "IDO hierarchy manager"],
                    "STAT_08" => ["ACTION" => "Approved on", "ROLE" => "IDO"],
                    "STAT_05" => ["ACTION" => "Reviewed on", "ROLE" => "BF reviewer" ],
                    "STAT_02" => [
                        "desk_review_fac" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Facilities)"],
                        "desk_review_fin" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Finance)"],
                        "desk_review_visa" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Visa)"],
                    ],
                ],
                "STAT_10" => [
                    0 => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_01" => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_02" => [
                        "desk_review_fac" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Facilities)"],
                        "desk_review_fin" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Finance)"],
                        "desk_review_visa" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Visa)"],
                    ],
                    "STAT_05" => ["ACTION" => "Reviewed on", "ROLE" => "BF reviewer"],
                    "STAT_06" => ["ACTION" => "Approved on", "ROLE" => "Project owner's hierarchy manager"],
                    "STAT_08" => ["ACTION" => "Approved on", "ROLE" => "IDO"],
                    "STAT_09" => ["ACTION" => "Approved on", "ROLE" => "IDO hierarchy manager"],
                ],
                "STAT_11" => [
                    "STAT_05" => ["ACTION" => "Approved on", "ROLE" => "BF reviewer"],
                    "STAT_08" => ["ACTION" => "Approved on", "ROLE" => "IDO"],
                    "STAT_09" => ["ACTION" => "Approved on", "ROLE" => "IDO hierarchy manager"],
                    "STAT_10" => ["ACTION" => "Approved on", "ROLE" => "Department head"],
                    "STAT_25" => ["ACTION" => "Approved on", "ROLE" => "Geo head"],
                ],
                "STAT_12" => [
                    "STAT_05" => ["ACTION" => "Approved on", "ROLE" => "BF reviewer"],
                    "STAT_08" => ["ACTION" => "Approved on", "ROLE" => "IDO"],
                    "STAT_09" => ["ACTION" => "Approved on", "ROLE" => "IDO hierarchy manager"],
                    "STAT_10" => ["ACTION" => "Approved on", "ROLE" => "Department head"],
                    "STAT_11" => ["ACTION" => "Approved on", "ROLE" => "Final approver"],
                    "STAT_25" => ["ACTION" => "Approved on", "ROLE" => "Geo head"],
                    "STAT_12" => [
                        "ticket_process" => ["ACTION" => "Processed on", "ROLE" => "Ticket admin"],
                        "forex_process" => ["ACTION" => "Processed on", "ROLE" => "Forex admin"],
                        "save_visa_process" => ["ACTION" => "Processed on", "ROLE" => "Visa admin"],
                    ],
                    "STAT_35" => ["ACTION" => "Submitted by", "ROLE" => "Employee"],
                    "STAT_37" => ["ACTION" => "Published by", "ROLE" => "HR reviewer"],
                ],
                "STAT_13" => [
                    "STAT_12" => [
                        "ticket_process" => ["ACTION" => "Processed on", "ROLE" => "Ticket admin"],
                        "forex_process" => ["ACTION" => "Processed on", "ROLE" => "Forex admin"],
                    ],
                ],
                "STAT_14" => [
                    "STAT_12" => [
                        "visa_process" => ["ACTION" => "Processed on", "ROLE" => "Visa admin"],
                    ]
                ],
                "STAT_15" => [
                    "STAT_05" => ["ACTION" => "Rejected on", "ROLE" => "BF reviewer"],
                ],
                "STAT_17" => [
                    "STAT_04" => ["ACTION" => "Rejected on", "ROLE" => "Project owner"],
                ],
                "STAT_18" => [
                    "STAT_06" => ["ACTION" => "Rejected on", "ROLE" => "Project owner's hierarchy manager"],
                ],
                "STAT_19" => [
                    "STAT_08" => ["ACTION" => "Rejected on", "ROLE" => "IDO"],
                ],
                "STAT_20" => [
                    "STAT_09" => ["ACTION" => "Rejected on", "ROLE" => "IDO hierarchy manager"],
                ],
                "STAT_21" => [
                    "STAT_10" => ["ACTION" => "Rejected on", "ROLE" => "Department head"],
                ],
                "STAT_22" => [
                    "STAT_11" => ["ACTION" => "Rejected on", "ROLE" => "Final approver"],
                ],
                "STAT_24" => [
                    0 => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_01" => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_02" => [
                        "desk_review_fac" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Facilities)"],
                        "desk_review_fin" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Finance)"],
                        "desk_review_visa" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Visa)"],
                    ],
                ],
                "STAT_25" => [
                    0 => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_01" => [ "ACTION" => "Submitted on", "ROLE" => "Requestor" ],
                    "STAT_24" => ["ACTION" => "Approved by", "ROLE" => "Client partner"],
                    "STAT_02" => [
                        "desk_review_fac" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Facilities)"],
                        "desk_review_fin" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Finance)"],
                        "desk_review_visa" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Visa)"],
                    ],
                    "STAT_05" => ["ACTION" => "Approved on", "ROLE" => "BF reviewer"],
                ],
                "STAT_29" => [
                    "STAT_02" => [
                        "desk_review_visa" => ["ACTION" => "Reviewed on", "ROLE" => "Reviewer (Visa)"],
                    ],
                    "STAT_04" => ["ACTION" => "Approved on", "ROLE" => "Project owner"],
                    "STAT_05" => ["ACTION" => "Approved on", "ROLE" => "BF reviewer"],
                    "STAT_06" => ["ACTION" => "Approved on", "ROLE" => "Project owner's hierarchy manager"],
                    "STAT_08" => ["ACTION" => "Approved on", "ROLE" => "IDO"],
                    "STAT_09" => ["ACTION" => "Approved on", "ROLE" => "IDO hierarchy manager"],
                    "STAT_10" => ["ACTION" => "Approved on", "ROLE" => "Department head"],
                    "STAT_11" => ["ACTION" => "Approved on", "ROLE" => "Final approver"],
                    "STAT_24" => ["ACTION" => "Approved by", "ROLE" => "Client partner"],
                    "STAT_25" => ["ACTION" => "Approved on", "ROLE" => "Geo head"],
                ],
                'STAT_30' => [
                    "STAT_29" => ["ACTION" => "Reviewed on", "ROLE" => "HR reviewer"],
                ],
                'STAT_31' => [
                    "STAT_30" => ["ACTION" => "Reviewed on", "ROLE" => "HR partner"],
                ],
                "STAT_32" => [
                    "STAT_30" => ["ACTION" => "Reviewed by", "ROLE" => "HR partner"],
                ],
                "STAT_33" => [
                    "STAT_31" => ["ACTION" => "Approved by", "ROLE" => "Immigration reviewer"],
                    "STAT_38" => ["ACTION" => "Approved by", "ROLE" => "Immigration reviewer"],
                    "STAT_04" => ["ACTION" => "Approved by", "ROLE" => "Project owner"],
                    "STAT_05" => ["ACTION" => "Reviewed by", "ROLE" => "BF reviewer"],
                    "STAT_06" => ["ACTION" => "Approved by", "ROLE" => "Project owner's hierarchy manager"],
                    "STAT_08" => ["ACTION" => "Approved by", "ROLE" => "IDO"],
                    "STAT_09" => ["ACTION" => "Approved by", "ROLE" => "IDO hierarchy manager"],
                    "STAT_10" => ["ACTION" => "Approved by", "ROLE" => "Department head"],
                    "STAT_11" => ["ACTION" => "Approved by", "ROLE" => "Final approver"],
                    "STAT_24" => ["ACTION" => "Approved by", "ROLE" => "Client partner"],
                    "STAT_25" => ["ACTION" => "Approved by", "ROLE" => "Geo head"],
                ],
                "STAT_38" => [
                    "STAT_31" => ["ACTION" => "RFE in progress", "ROLE" => "Immigration reviewer"],
                ],
                "STAT_34" => [
                    "STAT_31" => ["ACTION" => "Rejected by", "ROLE" => "Immigration reviewer"],
                ],
                "STAT_35" => [
                    "STAT_33" => ["ACTION" => "Reviewed by", "ROLE" => "Immigration reviewer"],
                ],
                "STAT_36" => [
                    "STAT_34" => ["ACTION" => "Rejected by", "ROLE" => "Immigration reviewer"],
                ],
                "STAT_37" => [
                    "STAT_35" => ["ACTION" => "Submitted", "ROLE" => "Employee"],
                ],
                "STAT_23" => [
                    "STAT_02" => ["ACTION" => "Cancelled on", "ROLE" => "Cancelled by"],
                    "STAT_04" => ["ACTION" => "Cancelled on", "ROLE" => "Cancelled by"],
                    "STAT_05" => ["ACTION" => "Cancelled on", "ROLE" => "Cancelled by"],
                    "STAT_06" => ["ACTION" => "Cancelled on", "ROLE" => "Cancelled by"],
                    "STAT_08" => ["ACTION" => "Cancelled on", "ROLE" => "Cancelled by"],
                    "STAT_09" => ["ACTION" => "Cancelled on", "ROLE" => "Cancelled by"],
                    "STAT_10" => ["ACTION" => "Cancelled on", "ROLE" => "Cancelled by"],
                    "STAT_11" => ["ACTION" => "Cancelled on", "ROLE" => "Cancelled by"],
                    "STAT_12" => ["ACTION" => "Cancelled on", "ROLE" => "Cancelled by"],
                    "STAT_13" => ["ACTION" => "Cancelled on", "ROLE" => "Cancelled by"]
                ],
                "default" => ["ACTION" => "Action on", "ROLE" => "Action by"],
            ],

            "AUTO_APPROVAL_DEPTS" => ["DEP001"],
            "DEFAULT_APPROVERS_FLOW_CODES" => ["PRO_OW", "PRO_OW_HIE", "DU_H", "DU_H_HIE", "DEP_H", "CLI_PTR", "GEO_H"],
            "DEFAULT_APPROVERS" => [
                "ACE0002" => "ACE0089",
            ],
            "MODULE" => ['MOD_01' => 'domestic', 'MOD_02' => 'international', 'MOD_03' => 'visa'],

            "status_flow_code_mapping" => [
                'STAT_08' => ['DU_H' => 'IDO'], 
                'STAT_10' => ['DEP_H' => 'Department head'],               
                'STAT_04' => ['PRO_OW' => 'Project owner'],           
                'STAT_06' => ['PRO_OW_HIE' => 'Project owner hierarchy manager'],  
                'STAT_09' => ['DU_H_HIE' => 'IDO hierarchy manager'],  
                'STAT_11' => ['FIN_APP' => 'Final approver'], 
                'STAT_24' => ['CLI_PTR' => 'Client partner'],
                'STAT_25' => ['GEO_H' => 'Geo head'],
            ],
            "report_configure" => ['AN_COST_FIN','AN_COST_FAC','TRV_PROC_TICKET','TRV_PROC_FOREX'],
        ];
        $this->module=[
            'MOD_01'=>'TRV',
            'MOD_02'=>'TRV',
            'MOD_03'=>'VISA'
        ];
        $this->country_based_input=[
            'COU_001'=>['INP_109']
        ];
        $this->country_specific_label_name=[
            'COU_001'=>[
                    'INP_131' => '(I – 94) Record number',
                    'INP_132' => 'Most recent date of entry',
                    'INP_133' => 'I-94 End date'
                ]
        ];
        $this->role_based_config=[
            'AN_COST_FIN'=>[
                'to_check'=>['STAT_02'],
                'checked'=>['STAT_02','STAT_03','STAT_04','STAT_05',
                'STAT_06','STAT_07','STAT_08','STAT_09','STAT_10',
                'STAT_11','STAT_12','STAT_13','STAT_14','STAT_15',
                'STAT_16','STAT_17','STAT_18','STAT_19','STAT_20',
                'STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27']
            ],
            'AN_COST_FAC'=>[
                'to_check'=>['STAT_02'],
                'checked'=>['STAT_02','STAT_03','STAT_04','STAT_05',
                'STAT_06','STAT_07','STAT_08','STAT_09','STAT_10',
                'STAT_11','STAT_12','STAT_13','STAT_14','STAT_15',
                'STAT_16','STAT_17','STAT_18','STAT_19','STAT_20',
                'STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27']
            ],
            'AN_COST_VISA'=>[
                'to_check'=>['STAT_02'],
                'checked'=>['STAT_03','STAT_04','STAT_05',
                'STAT_06','STAT_07','STAT_08','STAT_09','STAT_10',
                'STAT_11','STAT_12','STAT_13','STAT_14','STAT_15',
                'STAT_16','STAT_17','STAT_18','STAT_19','STAT_20',
                'STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27','STAT_29','STAT_30',
                'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38'],
            ],
            'GM_REV'=>[
                'to_check'=>['STAT_31', 'STAT_33', 'STAT_38'],
                'checked'=>[
                    'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_12', 'STAT_14'
                ],
            ],
            'PRO_OW'=>[
                'to_check'=>['STAT_04'],
                'checked'=>['STAT_05',
                'STAT_06','STAT_07','STAT_08','STAT_09','STAT_10',
                'STAT_11','STAT_12','STAT_13','STAT_14','STAT_15',
                'STAT_16','STAT_17','STAT_18','STAT_19','STAT_20',
                'STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27',
                'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38']
            ],
            'PRO_OW_HIE'=>[
                'to_check'=>['STAT_06'],
                'checked'=>['STAT_06','STAT_07','STAT_08','STAT_09','STAT_10',
                'STAT_11','STAT_12','STAT_13','STAT_14','STAT_15',
                'STAT_16','STAT_17','STAT_18','STAT_19','STAT_20',
                'STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27',
                'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38']
            ],
            'DU_H'=>[
                'to_check'=>['STAT_08'],
                'checked'=>['STAT_05','STAT_09','STAT_10',
                'STAT_11','STAT_12','STAT_13','STAT_14','STAT_15',
                'STAT_16','STAT_17','STAT_18','STAT_19','STAT_20',
                'STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27',
                'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38']
            ],
            'DU_H_HIE'=>[
                'to_check'=>['STAT_09'],
                'checked'=>['STAT_10',
                'STAT_11','STAT_12','STAT_13','STAT_14','STAT_15',
                'STAT_16','STAT_17','STAT_18','STAT_19','STAT_20',
                'STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27',
                'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38']
            ],
            'DEP_H'=>[
                'to_check'=>['STAT_10'],
                'checked'=>['STAT_05','STAT_11','STAT_12','STAT_13','STAT_14','STAT_15',
                'STAT_16','STAT_17','STAT_18','STAT_19','STAT_20',
                'STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27',
                'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38']
            ],
            'FIN_APP'=>[
                'to_check'=>['STAT_11'],
                'checked'=>['STAT_12','STAT_13','STAT_14','STAT_15',
                'STAT_16','STAT_17','STAT_18','STAT_19','STAT_20',
                'STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27',
                'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38']
            ],
            'CLI_PTR'=>[
                'to_check'=>['STAT_24'],
                'checked'=>['STAT_25','STAT_26','STAT_27','STAT_05','STAT_12', 'STAT_14',
                'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38']
            ],
            'GEO_H'=>[
                'to_check'=>['STAT_25'],
                'checked'=>['STAT_11','STAT_12','STAT_14','STAT_27','STAT_05',
                'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38']
            ],
            'REP_ACC'=>['STAT_01','STAT_02','STAT_03','STAT_04','STAT_05','STAT_06','STAT_07','STAT_08','STAT_09','STAT_10','STAT_11','STAT_12','STAT_13','STAT_14','STAT_15','STAT_16','STAT_17','STAT_18','STAT_19','STAT_20','STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27'],
            'TRV_PROC_TICKET'=>['STAT_12','STAT_13', 'STAT_23'],
            'TRV_PROC_VISA'=>['STAT_12','STAT_14'],
            'TRV_PROC_FOREX'=>['STAT_12','STAT_13', 'STAT_23'],
            'DOM_TCK_ADM'=>['STAT_12','STAT_13'],
            'BF_REV'=>[
                'to_check'=>['STAT_05'],
                'checked'=>['STAT_06','STAT_07','STAT_08','STAT_09','STAT_10','STAT_11','STAT_12','STAT_13','STAT_14','STAT_15','STAT_16','STAT_17','STAT_18','STAT_19','STAT_20','STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27','STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38']
            ],
            'AP_REV'=>['STAT_07','STAT_08','STAT_09','STAT_10','STAT_11','STAT_12','STAT_13','STAT_14','STAT_15','STAT_16','STAT_17','STAT_18','STAT_19','STAT_20','STAT_21','STAT_22','STAT_23','STAT_24','STAT_25','STAT_26','STAT_27'],
            'HR_REV'=>[
                'to_check' => ['STAT_29', 'STAT_37'],
                'checked' => ['STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_12', 'STAT_14'],
            ],
            'HR_PRT'=>[
                'to_check' => ['STAT_30'],
                'checked' => ['STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_12', 'STAT_14'],
            ],
        ];

        $this->location_config_for_costcode = [
            'US/IL'=>'US',
            'US/CA'=>'US',
            'US/TX'=>'US',
            'US/AZ'=>'US',
            'ME/DU'=>'Middle East',
            'ME/RY'=>'Middle East',
            'MX/PU'=>'Mexico',
            'UK/LN'=>'UK',
            'UK/SL'=>'UK',
            'NL/UT'=>'Netherlands',
            'PL/GD'=>'Poland',
            'IL/DB'=>'Ireland',
            'BL/GH'=>'Belgium',
            'IN/SIR'=>'Siruseri',
            'IN/NVL'=>'Navalur',
            'IN/HYD'=>'Hyderabad',
            'IN/KOC'=>'Kochi',
            'IN/BLR'=>'Bangalore',
            'GBL/GBL'=>'Global',
        ];
        //Images respective to the status.
        $this->images_for_status = [
            'STAT_01' => 'saved.svg',
            'STAT_02' => 'waiting.svg',
            'STAT_03' => 'waiting.svg',
            'STAT_04' => 'waiting.svg',
            'STAT_05' => 'waiting.svg',
            'STAT_06' => 'waiting.svg',
            'STAT_07' => 'waiting.svg',
            'STAT_08' => 'waiting.svg',
            'STAT_09' => 'waiting.svg',
            'STAT_10' => 'waiting.svg',
            'STAT_11' => 'waiting.svg',
            'STAT_12' => 'process.svg',
            'STAT_13' => 'closed.svg',
            'STAT_14'=>'closed.svg',
            'STAT_15' => 'rejected.svg',
            'STAT_16' => 'rejected.svg',
            'STAT_17' => 'rejected.svg',
            'STAT_18' => 'rejected.svg',
            'STAT_19' => 'rejected.svg',
            'STAT_20' => 'rejected.svg',
            'STAT_21' => 'rejected.svg',
            'STAT_22' => 'rejected.svg',
            'STAT_23' => 'rejected.svg',
            'STAT_24' => 'waiting.svg',
            'STAT_25' => 'waiting.svg',
            'STAT_26' => 'rejected.svg',
            'STAT_27' => 'rejected.svg',
            'STAT_28' => 'waiting.svg',
            'STAT_29' => 'waiting.svg',
            'STAT_30' => 'waiting.svg',
            'STAT_31' => 'waiting.svg',
            'STAT_32' => 'rejected.svg',
            'STAT_33' => 'waiting.svg',
            'STAT_34' => 'rejected.svg',
            'STAT_35' => 'waiting.svg',
            'STAT_36' => 'rejected.svg',
            'STAT_37' => 'waiting.svg',
            'STAT_38' => 'waiting.svg',
        ];
        //Functional and sales department codes 
        $this->FUNC_SALES_DEPT_CODES=[
            'DEP005','DEP008','DEP034','DEP025','DEP097','DEP037','DEP096','DEP142',
            'DEP044','DEP029','DEP015','DEP016','DEP006','DEP011','DEP047','DEP030',
            'DEP035','DEP041','DEP046','DEP063','DEP038','DEP040','DEP007','DEP013',
            'DEP042','DEP004','DEP021','DEP031','DEP014','DEP045','DEP027','DEP028',
            'DEP043','DEP095','DEP001','DEP012'
        ];
        $this->messages=[
            'en'=>[
                'SUBM_SUCC'=>'Request has been submitted successfully',
                'SAVE_SUCC'=>'Request has been saved successfully',
                'REV_SUCC'=>'Request has been reviewed successfully',
                'APPR_SUCC'=>'Request has been approved successfully',
                'REJ_SUCC'=>'Request has been rejected',
                'PROC_SUCC'=>'Request has been processed successfully',
                'ERR'=>'Error has been occurred. Please try again and write to help.is@aspiresys.com if issue persists.',
                'VISA_SAVE_SUCC'=>'Changes have been saved successfully',
                'TICKET_SAVE_SUCC'=>'Changes have been saved successfully',
                'FOREX_SAVE_SUCC'=>'Changes have been saved successfully',
                'RFE_PROG'=>'Petition has been moved to RFE status',
                'PUB_SUCC'=>'Offer letter has been published',
                'INVALID_ACTION'=>"Sorry! You don't have permission to perform this action."
            ],
        ];
        $this->reimburseDuration = [
            // 'DAY' => 0,
            'MONTH' => 5,
            // 'YEAR' => 0
        ];
        // visa request config
        $this->visa_config = [
            'visa_flow' => [
                "VIS_RUL_001" => "default",
                "VIS_RUL_002" => "short_term",
                "VIS_RUL_003" => "long_term",
                "VIS_RUL_004" => "long_term_h1b_cap",
            ],
            'visible_sections' => [
                'initiation' => [0, 'STAT_01', 'STAT_28', 'STAT_02', 'STAT_04', 'STAT_05', 'STAT_06', 'STAT_08', 'STAT_09', 'STAT_10','STAT_11', 'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38', 'STAT_12', 'STAT_14', 'STAT_15', 'STAT_16', 'STAT_17', 'STAT_18', 'STAT_19', 'STAT_20', 'STAT_21', 'STAT_22', 'STAT_26', 'STAT_27', 'STAT_24', 'STAT_25'],
                'personal_info' => [0, 'STAT_01', 'STAT_28', 'STAT_02', 'STAT_04', 'STAT_05', 'STAT_06', 'STAT_08', 'STAT_09', 'STAT_10','STAT_11', 'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38', 'STAT_12', 'STAT_14', 'STAT_24','STAT_25', 'STAT_26', 'STAT_27', 'STAT_15', 'STAT_16', 'STAT_17', 'STAT_18', 'STAT_19', 'STAT_20', 'STAT_21', 'STAT_22', 'STAT_26', 'STAT_27'],
                'gm_review' => ['STAT_02', 'STAT_04', 'STAT_05', 'STAT_06', 'STAT_08', 'STAT_09', 'STAT_10', 'STAT_11', 'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38', 'STAT_12', 'STAT_14', 'STAT_24','STAT_25', 'STAT_26', 'STAT_27','STAT_15', 'STAT_16', 'STAT_17', 'STAT_18', 'STAT_19', 'STAT_20', 'STAT_21', 'STAT_22', 'STAT_26', 'STAT_27'],
                'immigration_review' => ['STAT_02', 'STAT_04', 'STAT_05', 'STAT_06', 'STAT_08', 'STAT_09', 'STAT_10', 'STAT_11', 'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38', 'STAT_12', 'STAT_14', 'STAT_24','STAT_25', 'STAT_26', 'STAT_27','STAT_15', 'STAT_16', 'STAT_17', 'STAT_18', 'STAT_19', 'STAT_20', 'STAT_21', 'STAT_22', 'STAT_26', 'STAT_27'],
                'approval' => ['STAT_04', 'STAT_05', 'STAT_06', 'STAT_08', 'STAT_09', 'STAT_10', 'STAT_11', 'STAT_24','STAT_25', 'STAT_26', 'STAT_27', 'STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38', 'STAT_12', 'STAT_14','STAT_15', 'STAT_16', 'STAT_17', 'STAT_18', 'STAT_19', 'STAT_20', 'STAT_21', 'STAT_22', 'STAT_26', 'STAT_27'],
                'onsite_salary_negotiation' => ['STAT_29', 'STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38', 'STAT_12', 'STAT_14'],
                'offshore_salary_negotiation' => ['STAT_30', 'STAT_31', 'STAT_32', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38', 'STAT_12', 'STAT_14'],
                'petition_process' => ['STAT_31', 'STAT_33', 'STAT_34', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_38', 'STAT_12', 'STAT_14'],
                'visa_approval' => ['STAT_33', 'STAT_35', 'STAT_36', 'STAT_37', 'STAT_12', 'STAT_14'],
                'visa_entry' => ['STAT_35', 'STAT_37', 'STAT_12', 'STAT_14' ],
                'visa_stamping' => ['STAT_37', 'STAT_12', 'STAT_14'],
                'visa_process' => ['STAT_12', 'STAT_14']
            ],
            'waiting_messages' => [
                'STAT_28' => 'Waiting for employee to fill the personal details',
                'STAT_02' => 'Waiting for Visa desk review team to review the details',
                'STAT_29' => 'Waiting for Onsite HR to initiate the Salary discussion process',
                'STAT_30' => 'Waiting for Offshore HR team to conclude the final US salary',
                'STAT_31' => 'Waiting for Immigration team to initiate the petition process',
                'STAT_33' => 'Waiting for Immigration team to initiate the visa process',
                'STAT_35' => 'Waiting for employee to fill the visa details',
                'STAT_37' => 'Waiting for Onsite HR team to publish the offer letter',
                'STAT_04' => 'Waiting for Project owner approval',
                'STAT_06' => "Waiting for Project owner's hierarchy manager approval",
                'STAT_08' => 'Waiting for IDO approval',
                'STAT_09' => "Waiting for IDO hierarchy manager approval",
                'STAT_10' => 'Waiting for BU head approval',
                'STAT_11' => 'Waiting for Final approval',
                'STAT_24' => 'Waiting for client partner approval',
                'STAT_25' => 'Waiting for Geo head approval',
                'STAT_05' => 'Waiting for BF reviewer approval',
                'STAT_12' => 'Waiting for Visa process admin to process the visa',
                'STAT_38' => 'Petition process has been moved to RFE',
            ],
            'completed_messages' => [
                'immigration_review' => 'Immigration eligibility review has been completed',
                'onsite_salary_negotiation' => 'Salary discussion process has been completed',
                'petition_process' => 'Petition has been approved',
                'visa_approval' => 'Visa has been approved',
            ],
            'rejected_messages' => [
                'STAT_32' => 'User declined the offer',
                'STAT_34' => 'Petition has been denied',
                'STAT_36' => 'Visa has been denied',
            ],
            'upload_path' => [
                'cv_file_path' => 'visa_uploaded_documents',
                'degree_file_path' => 'visa_uploaded_documents',
                'petition_file_path' => 'visa_uploaded_documents',
                'visa_file_path' => 'visa_uploaded_documents',
                'pdf' => 'offer_letter',
                'word' => 'offer_letter',
                'immigration_offer_letter' => 'offer_letter',
                'default' => 'visa_uploaded_documents',
            ],
            'file_name_format' => [
                'cv_file_path' => 'cv',
                'degree_file_path' => 'degree',
                'petition_file_path' => 'petition',
                'visa_file_path' => 'visa',
                'pdf' => 'offer_letter',
                'word' => 'offer_letter',
                'immigration_offer_letter' => 'immigration_offer_letter',
                'default' => '',
            ],
            'salary_range_edit_status' => ['STAT_30'],
            'salary_range_edit_role'  => ['VIS_SAL_EDT'],
            'salary_range_visible_fields' => ['INP_100', 'INP_101', 'edit_salary_range'],
            'salary_range_editable_fields' => ['INP_100', 'INP_101'],
	        'default_hr_partner' => 'ACE0089',
            'default_reporting_manager' => ['ACE6058'],
        ];

        //added for configured country
        $this->visa_not_required_countries=[
            'COU_036'=>'COU_014',
            'COU_033'=>'COU_014',
            'COU_002'=>'COU_014',
            'COU_035' =>  'COU_029'
        ];

        // Configuration for service url and its credentials
        $this->service_url_configuration = [
            "DEPARTMENT" => [
                "url" => "https://amsapi.aspiresys.com/RestServiceImpl.svc/GetAllDepartment",
                "username" => "RRS",
                "password" => "30391411-7BEB-4418-AFD2-0B5EDD5446BE"
            ],
            "PRACTICE" => [
                "url" => "https://amsapi.aspiresys.com/RestServiceImpl.svc/GetAllPractice",
                "username" => "RRS",
                "password" => "30391411-7BEB-4418-AFD2-0B5EDD5446BE"
            ],
            "DELIVERYUNIT" => [
                "url" => "https://amsapi.aspiresys.com/RestServiceImpl.svc/GetAllDeliveryUnits",
                "username" => "RRS",
                "password" => "30391411-7BEB-4418-AFD2-0B5EDD5446BE"
            ],
            "PROJECT" => [
                "url" => "https://amsapi.aspiresys.com/RestServiceImpl.svc/GetAllActiveProjects",
                "username" => "RRS",
                "password" => "30391411-7BEB-4418-AFD2-0B5EDD5446BE"
            ],
            "PROOFDETAILS" => [
                "url" => "https://amsapi.aspiresys.com/RestServiceImpl.svc/GetEmployeeRequiredDetailsUsingAceNumbers",
                "username" => "PRISM",
                "password" => "PRISM@123"
            ],
            "USERLIST" => [
                "url" => "https://amsapi.aspiresys.com/RestServiceImpl.svc/GetEmployeeList",
                "username" => "RRS",
                "password" => "30391411-7BEB-4418-AFD2-0B5EDD5446BE"
            ],
            "INACTIVEUSER" => [
                "url" => "https://amsapi.aspiresys.com/RestServiceImpl.svc/GetAllInActiveEmployeeList",
                "username" => "RRS",
                "password" => "30391411-7BEB-4418-AFD2-0B5EDD5446BE"
            ],
            "USERDETAILS" => [
                "url" => "https://amsapi.aspiresys.com/RestServiceImpl.svc/GetEmployeeDetail",
                "username" => "RRS",
                "password" => "30391411-7BEB-4418-AFD2-0B5EDD5446BE"
            ],
            "TRVREIMFILTER" => [
                "url" => "http://localhost:8001/api/rrs_request_details",
                "api-token" => "abcdefghijklmno"
            ]
        ];
    }

}
