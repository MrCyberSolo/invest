<?php

// API URL
$url = "https://webupi.co.in/api/utr_verify";

// JSON data to send in the request
$data = array(
    "token" => "35a8d3-e59ca1-cea957-d1cab9-ba8f83",
    "bharatpe_id" => "713",
    "utr_number" => "330926200834"
);

// Convert data to JSON format
$jsonData = json_encode($data);

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options for the POST request
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
));

// Execute the cURL request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
} else {
    // Decode the response
    $responseData = json_decode($response, true);

    // Check if the response has a "status" key and it's true (indicating success)
    if (isset($responseData["status"]) && $responseData["status"] === true) {
        $results = $responseData["results"];
        echo "Transaction ID: " . $results["txn_id"] . "\n";
        echo "Transaction Date: " . $results["txn_date"] . "\n";
        echo "Store Name: " . $results["store_name"] . "\n";
        // You can access other fields in a similar manner
    } else {
        echo "Error: " . $responseData["message"] . "\n";
    }
}

// Close cURL session
curl_close($ch);
?>
