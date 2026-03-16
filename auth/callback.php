<?php
session_start();

require '../vendor/autoload.php';
require '../config/config.php';
require '../config/database.php';

$client = new Google_Client();

$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

if (!isset($_GET['code'])) {
    die('Error: No code found. Please login first via Google.');
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (isset($token['error'])) {
    die('Error fetching access token: ' . $token['error']);
}

$client->setAccessToken($token);

$oauth = new Google_Service_Oauth2($client);
$user = $oauth->userinfo->get();

$google_id = $user->id;
$name = $user->name;
$email = $user->email;
$picture = $user->picture;

$stmt = $pdo->prepare("INSERT IGNORE INTO users (google_id,name,email,picture) VALUES (?, ?, ?, ?)");
$stmt->execute([$google_id, $name, $email, $picture]);

$_SESSION['user'] = $google_id;

header('Location: ../show_youtube_channel.html'); // your app's main page
exit;