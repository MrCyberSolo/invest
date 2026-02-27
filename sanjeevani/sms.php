<?php
// API endpoint URL
$url = 'https://login.99smsservice.com/sms/api?action=send-sms';

// API key (replace 'your_api_key' with your actual API key)
$api_key = 'd3JMYkFnYklJempmTHRwdXNmY3A=';

// Sender ID
$from = 'TKINEN';

// Recipient's phone number
$to = '918478884111';

// Message content
$message = 'Dear User, Your OTP is 8529. Valid for 30 minutes. Please do not share this OTP. Regards Ocean Food T.K.INDUSTRIAL';

// Entity ID
$p_entity_id = '1201162643300643505';

// Template ID
$temp_id = '1207169657387094956';

// Build the request parameters
$params = array(
    'api_key' => $api_key,
    'to' => $to,
    'from' => $from,
    'sms' => $message,
    'p_entity_id' => $p_entity_id,
    'temp_id' => $temp_id
);

// Initialize cURL session
$ch = curl_init($url);

// Set the request method to POST
curl_setopt($ch, CURLOPT_POST, 1);

// Set the POST data
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

// Set the response output to a variable
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL session and fetch the response
$response = curl_exec($ch);

// Check for cURL errors
if(curl_errno($ch)){
    echo 'Error: ' . curl_error($ch);
}

// Close the cURL session
curl_close($ch);

// Display the API response
echo $response;
?>
