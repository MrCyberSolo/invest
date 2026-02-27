<?php 
date_default_timezone_set('Asia/Kolkata');
session_start();
// Check if user is logged in
//if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
//    header("location: login.php");
//    exit;
//}
include('user_menu/database_connect.php');
echo $userid_access = $_SESSION['username'];
error_reporting(E_ERROR | E_PARSE);


if (isset($_GET['client_txn_id'])) {
$order_id= $_GET['client_txn_id'];
$url = 'https://upisafe.online/order/status';
$data = array(
    'token' => '5525cc-14a21d-939523-8989a5-9e6c54',
    'order_id' => $order_id
);

$query_string = http_build_query($data);
$url_with_query = $url . '?' . $query_string;

$ch = curl_init($url_with_query);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);

$response = json_decode($result, true);

if ($response['status']) {
    echo "Transaction Details:\n";
    echo "Transaction ID: " . $response['results']['txn_id'] . "\n";
    echo "Order ID: " . $response['results']['order_id'] . "\n";
    echo "Merchant ID: " . $response['results']['merchant_id'] . "\n";
    echo "Merchant Name: " . $response['results']['merchant_name'] . "\n";
    echo "Merchant VPA: " . $response['results']['merchant_vpa'] . "\n";
    echo "Transaction Date: " . $response['results']['txn_date'] . "\n";
    echo "Transaction Amount: " . $response['results']['txn_amount'] . "\n";
    echo "Transaction Note: " . $response['results']['txn_note'] . "\n";
    echo "Product Name: " . $response['results']['product_name'] . "\n";
    echo "Customer Name: " . $response['results']['customer_name'] . "\n";
    echo "Customer Mobile: " . $response['results']['customer_mobile'] . "\n";
    echo "Customer Email: " . $response['results']['customer_email'] . "\n";
    echo "Customer VPA: " . $response['results']['customer_vpa'] . "\n";
    echo "Bank Order ID: " . $response['results']['bank_orderid'] . "\n";
    echo "UTR Number: " . $response['results']['utr_number'] . "\n";
    echo "Payment Mode: " . $response['results']['payment_mode'] . "\n";
    echo "Status: " . $response['results']['status'] . "\n";
    echo $gateway_txn = $result['results']['order_id'];
    echo $client_txn_id = $result['results']['txn_id'];
    echo $amount = $result['results']['txn_amount'];
    echo $upi_txn_id = $result['results']['utr_number'];
        //mysqli_query($con,"UPDATE `payment` SET `utr`='$upi_txn_id',`amount`='$amount',`status`='SUCCESS',`txn_id`='$client_txn_id' WHERE `tran`='$gateway_txn'");
        //mysqli_query($con,"UPDATE `income` SET `fran_bal`=`fran_bal`+$amount WHERE `userid`='$userid'");
        $mysqltime = date('Y-m-d H:i:s');
        //mysqli_query($con, "INSERT INTO `transaction`(`t_userid`, `t_pay`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES ('$userid','$userid','Account Recharge','$amount','Credit','$mysqltime')");
        
       // echo "<script>window.open('recharge_records.php','_self')</script>";
} else {
    echo "Error: " . $response['message'] . "\n";
    echo "<script>window.open('recharge_records.php','_self')</script>";
}

}
?>
