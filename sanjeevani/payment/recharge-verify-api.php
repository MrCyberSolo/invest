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
$payment_id = $_SESSION['payment_id'];
$utr_number = isset($request['utr_number']) ? $request['utr_number'] : null;

if( !$userid ) {
    json_response(['status' => false, 'message' => 'Unauthenticated!']);
} else if( !$payment_id ) {
    json_response(['status' => false, 'message' => 'Your Payment ID is not found.']);
} else if( !$utr_number ) {
    json_response(['status' => false, 'message' => 'Please enter valid UTR number.']);
}

$query = mysqli_query($con, "SELECT * FROM payment WHERE `id`='$payment_id' AND `status`='Pending'");
$payment = mysqli_fetch_assoc($query);

if( !$payment ) {
    json_response(['status' => false, 'message' => 'Pending Payment data not found.']);
}

try {
    $postData_arr = [
        'token' => '35a8d3-e59ca1-cea957-d1cab9-ba8f83',
        'bharatpe_id' => 761,
        'utr_number' => $utr_number,
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
    $result = json_decode($response, true);

    if( $result['status'] == false ) {
        json_response(['status' => false, 'message' => "Error: ". $result['message']]);
    }

    $result_txn_id = $result['results']['txn_id'];
    $result_utr_number = $result['results']['utr_number'];
    $result_status = $result['results']['status'];
    $result_amount = $result['results']['amount'];

    $mysql_status = 'Accept'; // 'Reject';
    mysqli_query($con, "UPDATE payment SET `utr`='$result_utr_number', `amount`='$result_amount', `txn_id`='$result_txn_id', `status`='$mysql_status' WHERE `id`='$payment_id'");
    
    $payment_amount = $payment['amount'];
    mysqli_query($con, "UPDATE income SET `fran_bal`= `fran_bal` + $result_amount WHERE `userid`='$userid'");
    $mysqltime = date('Y-m-d H:i:s');
    mysqli_query($con, "INSERT INTO `transaction`(`t_userid`, `t_pay`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES ('$userid','$userid','Account Recharge','$result_amount','Credit','$mysqltime')");
    
    if( isset($_SESSION['payment_id']) ) {
        unlink($_SESSION['payment_id']);
    }
    
    json_response([
        'status' => true,
        'message' => $result_status,
    ]);
} catch (Exception $e) {
    json_response(['status' => false, 'message' => $e->getMessage()]);
}

?>
