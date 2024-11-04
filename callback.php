<?php
// Set the content type to JSON
header("Content-Type: application/json");

// Read the input from M-Pesa
$content = file_get_contents('php://input');
$data = json_decode($content, true);

// Log the incoming data for debugging purposes
file_put_contents('mpesa_callback_log.txt', print_r($data, true), FILE_APPEND);

// Check if the request is for validation or confirmation
if (isset($data['TransactionType'])) {
    // Validation logic
    if ($data['TransactionType'] === 'C2B' && $data['Status'] === 'Pending') {
        // Validate the transaction (e.g., check amount, phone number)
        // Here you can check if the transaction amount matches your expected amount
        if ($data['Amount'] > 0) {
            // Respond with success to M-Pesa
            echo json_encode(['ResponseCode' => '00', 'ResponseDescription' => 'Accepted']);
        } else {
            // Respond with failure to M-Pesa
            echo json_encode(['ResponseCode' => '01', 'ResponseDescription' => 'Declined']);
        }
    } elseif ($data['TransactionType'] === 'C2B' && $data['Status'] === 'Completed') {
        // Confirmation logic
        // Process the transaction (e.g., update your database, send notification, etc.)
        // Log the successful transaction
        file_put_contents('successful_transactions_log.txt', print_r($data, true), FILE_APPEND);
        
        // Respond with success to M-Pesa
        echo json_encode(['ResponseCode' => '00', 'ResponseDescription' => 'Transaction processed successfully']);
    } else {
        // Handle any other transaction types or statuses as needed
        echo json_encode(['ResponseCode' => '02', 'ResponseDescription' => 'Unknown transaction type']);
    }
} else {
    // Handle the case where data is not as expected
    echo json_encode(['ResponseCode' => '03', 'ResponseDescription' => 'Invalid request']);
}
?>