<?php


function check_status($params, $merchant_key, $salt, $env){
    $result = refund_status($params, $merchant_key, $salt, $env);
    return $result;
}


function refund_status($params, $merchant_key, $salt, $env){
    $postedArray = '';
    $URL         = '';

    $argument_validation = _checkArgumentValidation($params, $merchant_key, $salt, $env);
    if(is_array($argument_validation) && $argument_validation['status'] === 0){
        return $argument_validation;
    }


    $params['key'] =  $merchant_key;

    $postedArray = _removeSpaceAndPreparePostArray($params);
    // if(is_array($empty_validation) && $empty_validation['status'] === 0){
    //     return $empty_validation;
    // }

    $empty_validation = _emptyValidation($postedArray, $salt);
    if(is_array($empty_validation) && $empty_validation['status'] === 0){
        return $empty_validation;
    }

    $type_validation = _typeValidation($postedArray, $salt, $env);
    if($type_validation !== true){
        return $type_validation;
    }


    $URL = _getURL($env);

    // process to start refund
    $refund_result = check_refund_status($postedArray, $salt, $URL);

    return $refund_result;        
}


function _checkArgumentValidation($params, $merchant_key, $salt, $env){
    $args = func_get_args();
    $argsc = count($args);
    if($argsc !== 4){
        return array(
            'status' => 0,
            'data' => 'Invalid number of arguments.'
        );
    }
    return 1;
}


function _removeSpaceAndPreparePostArray($params){
    $temp_array = array();
    foreach ($params as $key => $value) {
        $temp_array[$key] = trim(htmlentities($value, ENT_QUOTES));
    }   
    return $temp_array;
}


function _emptyValidation($params, $salt){
    $empty_value = false;
    if(empty($params['key'])) 
        $empty_value = 'Merchant Key';

    if(empty($params['easebuzz_id'])) 
        $empty_value = 'Easebuzz ID';

    if(empty($salt))
        $empty_value = 'Merchant Salt Key';

    if($empty_value !== false){
        return array(
            'status' => 0,
            'data' => 'Mandatory Parameter '.$empty_value.' can not empty'
        );
    }
    return true;
}

function _typeValidation($params, $salt, $env){
    $type_value = false;
    if(!is_string($params['key']))
        $type_value = "Merchant Key should be string";

    if(!is_string($params['easebuzz_id']))
        $type_value =  "Easebuzz ID should be string";
    

    if($type_value !== false){
        return array(
            'status' => 0,
            'data' => $type_value
        );
    }
    return true;
}

function _getURL($env){
    $url_link = '';
    switch($env){
        case 'test' :
            $url_link = "https://testdashboard.easebuzz.in/";
            break;
        case 'prod' :
            $url_link = 'https://dashboard.easebuzz.in/';
            break;
        default :
            $url_link = "https://testdashboard.easebuzz.in/";
    }
    return $url_link;
}

function check_refund_status($params_array, $salt_key, $url){
    $hash_key = '';

    // generate hash key and push into params array.
    $hash_key = _getHashKey($params_array, $salt_key);

    $params_array['hash'] = $hash_key;

    // call curl_call() for initiate pay link
    $curl_result = _curlCall( $url.'refund/v1/retrieve', http_build_query($params_array) );

    return $curl_result;
}

function _getHashKey($posted, $salt_key){
    $hash_sequence = "key|easebuzz_id";

    // make an array or split into array base on pipe sign.
    $hash_sequence_array = explode( '|', $hash_sequence );
    $hash = null;

    // prepare a string based on hash sequence from the $params array.
    foreach($hash_sequence_array as $value ) {
        $hash .= isset($posted[$value]) ? $posted[$value] : '';
        $hash .= '|';
    }

    $hash .= $salt_key;

    // generate hash key using hash function(predefine) and return
    return strtolower( hash('sha512', $hash) );
}


    function _curlCall($url, $params_array){
        // Initializes a new session and return a cURL.
        $cURL = curl_init();

        // Set multiple options for a cURL transfer.
        curl_setopt_array( 
            $cURL, 
            array ( 
                CURLOPT_URL => $url, 
                CURLOPT_POSTFIELDS => $params_array, 
                CURLOPT_POST => true, 
                CURLOPT_RETURNTRANSFER => true, 
                CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', 
                CURLOPT_SSL_VERIFYHOST => 0, 
                CURLOPT_SSL_VERIFYPEER => 0 
            ) 
        );

        // Perform a cURL session
        $result = curl_exec($cURL);

        // check there is any error or not in curl execution.
        if( curl_errno($cURL) ){
            $cURL_error = curl_error($cURL);
            if( empty($cURL_error) )
                $cURL_error = 'Server Error';
            
            return array(
                'curl_status' => 0, 
                'error' => $cURL_error
            );
        }

        $result = trim($result);

        return json_decode($result);
    }
?>