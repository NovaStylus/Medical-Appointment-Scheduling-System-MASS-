<?php
$consumerKey = 'A0fPA6tbomUEDOQ1qIOe3TpgZBmVZqut9rAJs5UGPn1qoQxJ';
$consumerSecret = 'b5htepwgrZERbGu51XlviX7719AhnTLymAjC2rnbYKRDklqVhSisaNb3dpAJTBOs';

$credentials = base64_encode($consumerKey . ':' . $consumerSecret);

$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . $credentials
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response);
echo "Access Token: " . $result->access_token;
