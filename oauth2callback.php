<?php

require_once __DIR__ . '/vendor/autoload.php';

session_start();


// Create the client object and set the authorization configuration
// from the client_secrets.json you downloaded from the Developers Console.
$client = new Google_Client();
$client->setAuthConfig('/home/rob/client_secrets.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');
$client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

/*
$client = new Google_Client();


$clientID = '748526673639-v3h4v7t1cm2ml5s0peokrlie4u261tio.apps.googleusercontent.com';
$filename = '/home/rob/client_secrets.json';
$clientSecret = 'IkmUWmRnETtvt_3E1WZ89s3s';

$client->setApplicationName('Graysky Technologies');
$client->setClientSecret($clientSecret);
$client->setAuthConfig($filename);
$client->setRedirectUri('http://analytics.broadwaymediagroup.com/test2.php');
$client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));
$client->setAccessType('offline');
$client->setClientId($clientID);
*/

// Handle authorization flow from the server.
if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/HelloAnalytics.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

