<?php
// Load the Google API PHP Client Library.
//require_once __DIR__ . 'Google/Client.php';
//require_once __DIR__ . 'Google/Service/Analytics.php';

session_start();

require_once __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();

$clientID = '748526673639-v3h4v7t1cm2ml5s0peokrlie4u261tio.apps.googleusercontent.com';
$filename = '/home/rob/client_secrets.json';
$clientSecret = 'IkmUWmRnETtvt_3E1WZ89s3s';

$client->setApplicationName('Graysky Technologies');
//$client->setClientId($clientID);
//$client->setClientSecret($clientSecret);
$client->setAuthConfig($filename);
//$client->setRedirectUri('http://analytics.broadwaymediagroup.com/report8.php');
$client->setScopes(array('http://www.googleapis.com/auth/analytics.readonly'));
$client->setAccessType('offline');


// If the user has already authorized this app then get an access token
// else redirect to ask the user to authorize access to Google Analytics.
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  // Set the access token on the client.
  $client->setAccessToken($_SESSION['access_token']);

  // Create an authorized analytics service object.
  $analytics = new Google_Service_Analytics($client);

  // Get the first view (profile) id for the authorized user.
  $profile = getFirstProfileId($analytics);

  // Get the results from the Core Reporting API and print the results.
  $results = getResults($analytics, $profile);
  printResults($results);
} 
/*
else {
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
*/

    //For loging out.
    if ($_GET['logout'] == "1") {
	unset($_SESSION['token']);
       }
    

    // Step 2: The user accepted your access now you need to exchange it.
    if (isset($_GET['code'])) {
        
    	$client->authenticate($_GET['code']);  
    	$_SESSION['token'] = $client->getAccessToken();
    	$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    	header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    }

    // Step 1:  The user has not authenticated we give them a link to login    
    if (!$client->getAccessToken() && !isset($_SESSION['token'])) {

    	$authUrl = $client->createAuthUrl();

  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));

    	print "Connect Me!";
        }    
    

    // Step 3: We have access we can now create our service
    if (isset($_SESSION['token'])) {
        print "LogOut
";


        print "Access from google: " . $_SESSION['token']."
"; 
        
    	$client->setAccessToken($_SESSION['token']);
    	$service = new Google_Service_Analytics($client);    

        // request user accounts
        $accounts = $service->management_accountSummaries->listManagementAccountSummaries();


        foreach ($accounts->getItems() as $item) {

		echo "Account: ",$item['name'], "  " , $item['id'], "
 \n<br>";
		
		foreach($item->getWebProperties() as $wp) {
			echo '-----WebProperty: ' ,$wp['name'], "  " , $wp['id'], "
 \n<br>";    
			$views = $wp->getProfiles();
			if (!is_null($views)) {
                                // note sometimes a web property does not have a profile / view

				foreach($wp->getProfiles() as $view) {

					echo '----------View: ' ,$view['name'], "  " , $view['id'], "
 \n<br>";    
				}  // closes profile
			}
		} // Closes web property
		
	} // closes account summaries
    }
?>
