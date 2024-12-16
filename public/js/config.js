class Config
{
    #overallConfig = {
        "travel": {
            
        },
        "visa": {
            "module_id": "MOD_03",
            "visa_type": {
                "VIS_001": "short_term",
                "VIS_002": "long_term",
            },
            "request_for": {
                "RF_08": "self",
                "RF_13": "employee",
                "RF_14": "family",
            },
            "travel_type": {
                1: "alone",
                2: "family",
            },
            "interview_type": {
                1: "drop_box",
                2: "regular",
            },
            "petition_year_diff": 3,
            "visa_interview_date_diff": 1,
            "errorMessage": {
                "ERR01": "Please fill the mandatory fields",
                "ERR02": "Please fill the proof details fields",
                "ERR03": "Please enter the amount other than Zero",
                "ERR04": "Please enter the amount greater than Minimum range",
                "ERR05": "Please enter valid salary range",
                "ERR06": "Salary should be between the salary range mentioned by HR reviewer",
                "ERR07": "Please enter the valid receipt number",
                "ERR08": "Please generate the offer letter",
                "ERR09": "Please select valid visa status",
                "ERR10" : "Please enter the valid secure key",
            },
            "offshoreLocations": ["COU_014"],
            "allowedVisaStatus": ['1', '2'],
        },
    }   
    constructor (moduleName) {
        this.moduleName = moduleName;
    }
    get (...properties) {
        const configObj = this.#overallConfig[this.moduleName] || {};
        return properties.reduce( (p, c) => p?.[c] ?? null, configObj  );
    }
}

function getVisaConfig(...props)
{
    const moduleName = "visa";
    const configObj = new Config(moduleName);
    const result = configObj.get(...props);
    delete configObj;
    return result;
}
function get_visa_status_config(visa_status){
let status=['3','4','5','6'];

if(status.indexOf(visa_status)>-1){
  return true;
}
return false;
}

/**
 * Code added by barath on 11-Sep-2024
 * visa filing field is only visible to travel
 * 
 */
const visa_filing_eligible_country=['COU_001','USA'];
const visa_filing_eligible_type=['VIS_002','Long term'];
