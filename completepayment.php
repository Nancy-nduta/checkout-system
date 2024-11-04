<?php

// Include the necessary libraries
require 'vendor/autoload.php';

use Kabangi\Mpesa\Init as Mpesa;

// Configuration
$consumerKey = 'jAUghrNniPvG6zHACGaO8QeN6b2zQ1pcGu0v35N1u7jdbt8L'; // Replace with your Consumer Key
$consumerSecret = '1G7Eyp1o8WunEm60Vca6KrjCTwxoVFbfGs8z3SfxMy0w4SQj2FHfGIsCtjbxE1YL'; // Replace with your Consumer Secret
$phoneNumber = '254742478248'; // Replace with your phone number
$amount = 100; // Amount to be paid
$transactionDesc = 'Payment for services'; // Description of the transaction
$callbackUrl = 'https://b272-41-80-113-234.ngrok-free.app/checkout%20system/callback.php'; // Callback URL for payment confirmation // Callback URL for payment confirmation
$accountReference = 'Payment123'; // Unique account reference for the transaction

// Initialize M-Pesa
$mpesa = new Mpesa([
    'consumerKey' => $consumerKey,
    'consumerSecret' => $consumerSecret,
]);

// Function to initiate payment
function initiatePayment($mpesa, $amount, $phoneNumber, $transactionDesc, $callbackUrl, $accountReference) {
    try {
        $response = $mpesa->STKPush([
            'amount' => $amount,
            'transactionDesc' => $transactionDesc,
            'phoneNumber' => $phoneNumber,
            'callbackUrl' => $callbackUrl,
            'accountReference' => $accountReference, // Ensure this is included
        ]);
        
        return json_encode($response);
    } catch (\Exception $e) {
        return json_encode(['error' => $e->getMessage()]);
    }
}

// Call the function to initiate payment
$response = initiatePayment($mpesa, $amount, $phoneNumber, $transactionDesc, $callbackUrl, $accountReference);

// Output the response
header('Content-Type: application/json');
echo $response;

?>