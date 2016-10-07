<?php

include 'getService.php';

function RetrieveItems()
{
    //echo "\r\n**************************************";
    $application = getenv("VCAP_APPLICATION");
    echo "\r\napplication:" . $application;
    $application_json = json_decode($application, true);
    $application_name = $application_json["application_name"];
    //echo "\r\napplication_name:" . $application_name;
    if (substr( $application_name, 0, 3 ) === "ui-") {
        $catalog_name = "catalog-api-" . substr($application_name, 3);
    } else {
        $catalog_name = str_replace("-ui-", "-catalog-api-", $application_name);
    }
    //echo "\r\ncatalog_name:" . $catalog_name;    
    $catalog_url = GetServices($catalog_name);
    //echo "\r\ncatalogRoute:" . $catalogRoute;    
    $url = $catalog_url . "/items";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
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
    return $curlResult;
}

?>

