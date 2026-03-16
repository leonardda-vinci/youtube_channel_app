<?php
require '../vendor/autoload.php';
require '../config/config.php';
session_start();

$client = new Google_Client();

$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

$client->setScopes([
    "email",
    "profile",
    "https://www.googleapis.com/auth/youtube.readonly"
]);
$client->setAccessType('offline');
$client->setPrompt('consent');

// If user is already logged in, redirect to main page
// if (isset($_SESSION['user'])) {
//   header('Location: ../public/show_youtube_channel.php');
//   exit;
// }

// Redirect user to Google login page
$authUrl = $client->createAuthUrl();
header('Location: ' . $authUrl);
exit;

// return $client;