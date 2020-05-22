<?php
/*
 * https://console.developers.google.com
 */

//require_once '../google-api-php-client/src/Google_Client.php';
//require_once '../google-api-php-client/src/contrib/Google_PlusService.php';
require_once __DIR__ . '/vendor/autoload.php';

/* Initialize the Google API client */
$client = new Google_Client();
$client->setApplicationName("Google+ PHP QuickStart");
$client->setClientId('748526673639-v3h4v7t1cm2ml5s0peokrlie4u261tio.apps.googleusercontent.com');         //Update with your client ID
$client->setClientSecret('1M1R0m__g7ahtfaHwstZnDG-'); //Updated with your client secret
$client->setRedirectUri('postmessage');         //Required for one-time-code flow

/* Start the Google+ service */
$plus = new Google_PlusService($client);
session_start();

/*
 * Use cached credentials if available; otherwise, read the one-time code
 * from the POST body and exchange for an access token.
 */
if (isset($_SESSION['token'])){
  $client->setAccessToken($_SESSION['token']);
}else{
  /*
   * Get the authorization code from the POST data and authenticate the
   * Google API client.
   */
  try {
    // Receive an OAuth 2.0 authorization code via the POST body.
    $code = file_get_contents('php://input');

    // Exchange the OAuth 2.0 authorization code for user credentials.
    $client->authenticate($code);

    // Remember to store the token and user in a database.

    $token = $client->getAccessToken();
    $_SESSION['token'] = $token;
  } catch (Google_AuthException $e) {
    die("Unable to exchange authorization code for an access token");
  }
}

/*
 * Try making a couple server-side requests to the Google+ APIs to retrieve
 * the authenticated user's profile and a list of people the user has circled.
 * This will validate if your app has server-side access.
 */
try {
  // Retrieve the user's Google+ public profile fields.
  $user = $plus->people->get('me');

  // Retrieve a list of people the user has circled and made visible to the app.
  $people = $plus->people->listPeople('me', 'visible');

  $results = array('profile'=>$user, 'people'=>$people);

  $json = json_encode($results);
  header('Content-type: application/json');
  exit($json);  // Return the JSON response to the browser
} catch (Google_ServiceException $e) {
  die("Could not query the API because of either a configuration problem or an access token problem.");
}
?>
