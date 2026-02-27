<?php
// Define API endpoint URL
$apiUrl = 'https://zozowallet.com/api/payout/transfer';

// Set API parameters
$apiToken = 'IKRRK961puFVNcVcsETXJOC8fO48d2Et4TgGApnTowQSrMFuu0C68oNgdddm';
$beneficiaryName = 'John Doe';
$accountNumber = '401610110003681';
$ifsc = 'BKID0004016';
$mobileNumber = '9876543210';
$amount = '216';
$clientID = '1478523696';

// Build query string
$queryString = http_build_query([
    'api_token' => $apiToken,
    'beneficiary_name' => $beneficiaryName,
    'account_number' => $accountNumber,
    'ifsc' => $ifsc,
    'mobile_number' => $mobileNumber,
    'amount' => $amount,
    'client_id' => $clientID,
]);

// Combine API URL with query string
$requestUrl = $apiUrl . '?' . $queryString;

// Send GET request to API
$response = file_get_contents($requestUrl);

// Check if the response is received
if ($response !== false) {
    // Process the API response (e.g., print or manipulate data)
    echo "API Response: " . $response;
} else {
    // Handle errors if the API request fails
    echo "Error fetching API response.";
}
?>
