<?php
session_start();
// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];

if(isset($_GET['amount'])){
$amount = $_GET['amount'];
$query_user = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `user` WHERE `email`='$userid_access'"));
	$mobile = $query_user['mobile'];
	$name = $query_user['name'];
$orderID = sprintf("%010d", rand(1, 9999999999));
$apiKey = "5525cc-14a21d-939523-8989a5-9e6c54";


$url = 'https://upisafe.online/order/create';
$data = array(
    'token' => $apiKey,
    'order_id' => $orderID,
    'txn_amount' => $amount,
    'txn_note' => 'Pay For ShopKing Infotech',
    'product_name' => 'Redmi Note 12 Pro',
    'customer_name' => 'Priyanshu',
    'customer_mobile' => '9000000000',
    'customer_email' => 'info@oceanfoodco.vip',
    'callback_url' => 'https://oceanfoodco.vip/payment_success.php'
);
$data_string = json_encode($data);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);

$result = curl_exec($ch);
curl_close($ch);

$response = json_decode($result, true);

if ($response['status']) {
    echo "Order Created Successfully\n";
    echo "Transaction ID: " . $response['results']['txn_id'] . "\n";
    echo "Payment URL: " . $response['results']['payment_url'] . "\n";
    echo "UPI Intent:\n";
    echo "BHIM: " . $response['results']['upi_intent']['bhim'] . "\n";
    echo "PhonePe: " . $response['results']['upi_intent']['phonepe'] . "\n";
    echo "Paytm: " . $response['results']['upi_intent']['paytm'] . "\n";
    echo "GPay: " . $response['results']['upi_intent']['gpay'] . "\n";
     mysqli_query($con,"INSERT INTO `payment`(`user_id`, `amount`,`tran`,`pay_link`) VALUES ('$userid_access','$amount','$orderID','$paymentURL')");
      $payment_url = $response['results']['payment_url'];
    header("Location: $payment_url");
} else {
    echo "Error: " . $response['message'] . "\n";
}

}
?>