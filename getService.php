<?php

function GetServiceURL($service_name)
{
	$vcap_services_str = getenv("VCAP_SERVICES");
	$vcap_services_json = json_decode($vcap_services_str, true);
	$disco_url = "";
	$disco_auth_token = "";
	$username = "";
	$password = "";
	if (is_array($vcap_services_json)) {
	  foreach ($vcap_services_json as $key => $services_arr) {
	    error_log("$key", 0);
	    if (preg_match('/^myServiceDiscovery/', $key) === 1) {
	      error_log("Found service_discovery, getting creds", 0);
	      $service_hash = $services_arr[0];
	      $credentials_hash = $service_hash["credentials"];
	      $disco_url = $credentials_hash["url"];
	      $disco_auth_token = $credentials_hash["auth_token"];
	      break;
	    }
	  }
	}
	
	error_log("disco_url = $base_url", 0);
	error_log("disco_auth_token = $auth_token", 0);
	
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $disco_url . "/api/v1/services/instances?service_name=" . $service_name);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$header = array();
	$header[] = 'Content-length: 0';
	$header[] = 'Content-type: application/json';
	$header[] = 'Authorization: Bearer ' . $disco_auth_token;
	curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
    $curlResult = curl_exec($curl);
    $curlError = curl_error($curl);
    $curlErrno = curl_errno($curl);
    curl_close($curl);
    $firstChar = substr($curlResult, 0, 1); /* should check if $curlResult === FALSE if newer PHP */
    if ($firstChar != "{") {
        $errorObject = new stdClass();
        $errorObject->error = $curlError;
        $errorObject->errno = $curlErrno;
        return json_encode($errorObject);
    }

    echo "\r\nccurlResult:" . $curlResult; 
	error_log("curlResult = $curlResult", 0);

    $curlResult_json = json_decode($curlResult, true);
    $service_url = $application_json["application_uris"][0];
    return $curlResult;	
}

?>

