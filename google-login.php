<?php
session_name("user_session");
session_start();
require_once __DIR__ . '/vendor/autoload.php'; // ✅ fixed path
require_once 'database.php'; // ✅ correct


$client = new Google_Client();
$client->setClientId('132496049911-i41i064lfmmkurp6ekd5mtse5t27c8qu.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/app/google-callback.php');
$client->addScope('email');
$client->addScope('profile');

$loginUrl = $client->createAuthUrl();
header('Location: ' . $loginUrl);
exit();
