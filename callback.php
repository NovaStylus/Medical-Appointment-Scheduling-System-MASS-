<?php
// Get the JSON response from M-Pesa
$callbackJSONData = file_get_contents('php://input');

// Save it to a file for now (or log to DB later)
file_put_contents('callback_log.txt', $callbackJSONData);

// Decode and process if needed
$data = json_decode($callbackJSONData, true);

// Example: log payment result
if (isset($data['Body']['stkCallback'])) {
    $status = $data['Body']['stkCallback']['ResultDesc'];
    $amount = $data['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'] ?? null;

    file_put_contents('callback_payment.txt', "Status: $status\nAmount: $amount\n", FILE_APPEND);
}
