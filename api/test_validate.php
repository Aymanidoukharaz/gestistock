<?php
// Simple test script to validate an exit form

// Set the API URL
$apiUrl = 'http://localhost/SFE/api/public/api/exit-forms/1/validate';

// Set the JWT token (replace with a valid token)
$token = 'YOUR_JWT_TOKEN';

// Initialize cURL session
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
    'Accept: application/json'
]);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if(curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
} else {
    // Print the response
    echo $response;
}

// Close cURL session
curl_close($ch);
