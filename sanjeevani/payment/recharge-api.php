<?php
	date_default_timezone_set('Asia/Kolkata');
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('../user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
include('helpers.php');

$request = $_POST;
$userid =$userid_access;
$amount = isset($request['amount']) ? $request['amount'] : null;

if( !$userid ) {
    json_response(['status' => false, 'message' => 'Unauthenticated!']);
} else if( !$amount || !is_numeric($amount) ) {
    json_response(['status' => false, 'message' => 'Please enter valid amount.']);
}

try {
    $postData_arr = [
        'token' => '35a8d3-e59ca1-cea957-d1cab9-ba8f83',
        'bharatpe_id' => 761,
        'txn_amount' => $amount,
    ];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://qrapiweb.aiautomation.co.in/api/create_qrcode',
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
    if( $statusCode != 201 || $statusCode != 200 ) {
        json_response(['status' => false, 'message' => "Error: curl_error". curl_error($curl)]);
    }*/

    // Close cURL resource
    curl_close($curl);

    // if you need to process the response from the API further
    $result = json_decode($response, true);
    
    $mysqltime = date('Y-m-d H:i:s');
    $qrcode = $result['results']['qrcode'];
    $qrdata = $result['results']['qrdata'];
    $qrtime = $result['results']['time'];
    
    parse_str( parse_url( $qrdata, PHP_URL_QUERY), $qrdata_arr );

    $query = mysqli_query($con, "insert into payment(`user_id`,`pay_type`,`amount`,`pay_date`, `date`,`qrcode`,`qrdata`) values('$userid','UPI','$amount','$mysqltime','$mysqltime','$qrcode','$qrdata')");
    $payment_id = mysqli_insert_id($con);

    if( $payment_id == 0 ) {
        json_response(['status' => false, 'message' => 'Something went wrong.']);
    }

    $_SESSION['payment_id'] = $payment_id;

    json_response([
        'status' => true,
        //'message' => 'Success',
        'payment_id' => $payment_id,
        'qrdata' => $qrdata_arr,
        'qrcode' => $qrcode,
        'qrtime' => $qrtime,
    ]);
} catch (Exception $e) {
    json_response(['status' => false, 'message' => $e->getMessage()]);
}

?>
