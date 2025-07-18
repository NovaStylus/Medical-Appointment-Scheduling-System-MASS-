<?php
date_default_timezone_set('Africa/Nairobi');

// Your credentials
$consumerKey = 'A0fPA6tbomUEDOQ1qIOe3TpgZBmVZqut9rAJs5UGPn1qoQxJ';
$consumerSecret = 'b5htepwgrZERbGu51XlviX7719AhnTLymAjC2rnbYKRDklqVhSisaNb3dpAJTBOs';
$shortCode = '174379'; // This is the sandbox shortcode
$passkey = 'bfb279f9aa9bdbcf15e97dd71a467cd2c20d5e27b6b7b6c9f2c24c6e12a5f51b';

// Test phone number and amount
$phone = '254708374149';
$amount = 1;
$accountReference = 'TestPay';
$transactionDesc = 'Test payment';

// Step 1: Get access token
$credentials = base64_encode($consumerKey . ':' . $consumerSecret);
$token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '.$credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$token_response = curl_exec($ch);
curl_close($ch);

$accessToken = json_decode($token_response)->access_token;

// Step 2: Prepare STK Push data
$timestamp = date('YmdHis');
$password = base64_encode($shortCode . $passkey . $timestamp);

$stkUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$curl_post_data = [
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $shortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => 'https://yourdomain.com/callback.php',
    'AccountReference' => $accountReference,
    'TransactionDesc' => $transactionDesc
];

$data_string = json_encode($curl_post_data);

// Step 3: Make the request
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $stkUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

$response = curl_exec($curl);
curl_close($curl);

echo $response;
