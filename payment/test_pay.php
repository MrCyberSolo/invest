<?php
    $postData_arr = [
        'token' => '35a8d3-e59ca1-cea957-d1cab9-ba8f83',
        'bharatpe_id' => 761,
        'utr_number' => 412024472287,
    ];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://qrapiweb.aiautomation.co.in/api/utr_verify',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData_arr),
        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
    ));

    // Execute the POST request
    $response = curl_exec($curl);

    /*
    // Get the POST request header status
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // If header status is not Created or not OK, return error message
    if( $statusCode !== 201 || $statusCode !== 200 ) {
        json_response(['status' => false, 'message' => "Error: curl_error". curl_error($curl)]);
    }*/

    // Close cURL resource
    curl_close($curl);

    // if you need to process the response from the API further
    echo $result = json_decode($response, true);

    if( $result['status'] == false ) {
        json_response(['status' => false, 'message' => "Error: ". $result['message']]);
    }
    echo "<br>";
   echo  $result_txn_id = $result['results']['txn_id'];
   echo  $result_utr_number = $result['results']['utr_number'];
  echo  $result_status = $result['results']['status'];
  echo  $result_amount = $result['results']['amount'];
?>