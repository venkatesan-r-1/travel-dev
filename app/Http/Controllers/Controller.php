<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\ConfigController;
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function __construct(){
        $config_obj=new ConfigController();
        $this->CONFIG=$config_obj->CONFIG;
        $this->MODULE=$config_obj->module;
        $this->role_based_config=$config_obj->role_based_config;
        $this->location_config_for_costcode=$config_obj->location_config_for_costcode;
        $this->images_for_status=$config_obj->images_for_status;
        $this->FUNC_SALES_DEPT_CODES=$config_obj->FUNC_SALES_DEPT_CODES;
        $this->messages=$config_obj->messages;
        $this->reimburseDuration=$config_obj->reimburseDuration;
        $this->visa_config = $config_obj->visa_config;
        $this->country_based_input=$config_obj->country_based_input;
        $this->country_specific_label_name=$config_obj->country_specific_label_name;
        $this->visa_not_required_countries=$config_obj->visa_not_required_countries;
        $this->service_url_config=$config_obj->service_url_configuration;
    }
}
