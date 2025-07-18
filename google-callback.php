<?php
session_name("user_session");
session_start();
require_once __DIR__ . '/vendor/autoload.php'; // ✅ fixed path
require_once 'database.php'; // ✅ correct


$client = new Google_Client();
$client->setClientId('132496049911-i41i064lfmmkurp6ekd5mtse5t27c8qu.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/app/google-callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token["error"])) {
        $client->setAccessToken($token['access_token']);
        $oauth = new Google_Service_Oauth2($client);
        $userData = $oauth->userinfo->get();

        $email = $userData->email;
        $fullName = $userData->name;

        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            // User does not exist, insert
            $defaultPassword = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            $stmtInsert = $conn->prepare("INSERT INTO users (fullName, email, phone, password) VALUES (?, ?, '', ?)");
            $stmtInsert->bind_param("sss", $fullName, $email, $defaultPassword);
            $stmtInsert->execute();
            $user_id = $stmtInsert->insert_id;
        } else {
            // User exists, get id
            $stmt->bind_result($user_id);
            $stmt->fetch();
        }
        $stmt->close();

        // Start session and set session variables including user_id
        session_start();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $fullName;

        header("Location: index.php");
        exit();
    } else {
        echo "Login failed: " . $token["error"];
    }
} else {
    echo "No code returned from Google.";
}
