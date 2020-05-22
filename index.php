<?php

	session_start();

	$user = "";
	$login = false;

	if (isset($_SESSION['user']) && strlen($_SESSION['user']) > 0) {
	    $user = $_SESSION['user'];
	    include("./header.php");
	}
	else if (isset($_POST['daform']) && $_POST['daform'] === 'LoginForm') {
		$login = true;
	}
	else {
		// redirect the page to the login page
		header('Location: http://analytics.broadwaymediagroup.com/login.php');
		exit();
	}

    // Load the Google API PHP Client Library.
    require_once __DIR__ . '/vendor/autoload.php';

?>

	<center>
	<H1>Broadway Media Radio Analytics Information Portal</H1>
	</center>

<?php


$w = "";
if ($login) {
	$username = "";
	if (strlen(trim($_POST['username'])) > 0 && strlen(trim($_POST['username'])) <= 15) {
		$username = trim($_POST['username']);
	}

	$password = "";
	if (strlen(trim($_POST['password'])) > 0 && strlen(trim($_POST['password'])) <= 15) {
		$password = trim($_POST['password']);
	}

	// don't do the state query if there is only one parameter sent.
	if (strlen($username) > 0 && strlen($password) > 0)
	{
		$username = stripslashes($username);
		$password = stripslashes($password);
	}
	else
	{
		include("./header.php");
		echo("<p>The wrong number of parameters necessary to apply were not provided.  Please try again.</P>");
		include('./footer.php');
		exit();
	}
	$w = "username='" . $username . "' and password='" . $password . "'";

} else if (strlen($user) > 0) {
	$w = "username='" . $user . "'";
}
else {
	// we shouldn't be here....
	$w = "username='' and password=''";
}

////////////////////////////////////////////////////////////////////////////////

	// see if we have a custID and then see if we already have this person
	include('include/connect.php');
	$c = connect('broadway');

	if (!$c) {
		echo "Error: unable to connect to mysql: " . PHP_EOL;
		echo "debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		echo "debugging error: " . mysqli_connect_error() . PHP_EOL;
		mail("rob.gray@grayskytech.com","SQL ERROR!", 'account.php - We had an error! <br> with Error: ' . mysqli_connect_error() . PHP_EOL, "From:Radio Analytics<rob@grayskytech.com>");
		include('./footer.php');
		mysqli_close($c);
		exit();
	}

	$query_sql = "select * from users where " . $w;

	$result = mysqli_query($c, $query_sql);
	if (!$result)
	{
		echo("<p>We had a problem with the database.  Sorry for the problem.  The System administrator has been notified</P>");
		$err = mysqli_error($c);
		echo "the sql error was: " . $err;
		echo "<br>";
		mail("rob.gray@grayskytech.com","SQL ERROR!", 'applicant.php - We had an error! ' . $query_sql . '<br> with Error: ' . $err, "From:Radio Analytics<rob@grayskytech.com>");
		include('./footer.php');
		mysqli_close($c);
		exit();
	}

	$rowcount = mysqli_num_rows($result);

	$userID = 0;
	$firstName = "";
	$lastName = "";
	$address = "";
	$city = "";
	$state = "";
	$zip = "";
	$email = "";
	$phone = "";
	$user = "";
	$firstTime = true;
	$thisTime = true;
//echo "We have " . $rowcount . " users";
	if ($rowcount > 0)
	{
		while ($r = mysqli_fetch_assoc($result))
		{
			$userID = $r['id'];
			$firstName = stripslashes($r['fname']);
			$lastName = stripslashes($r['lname']);
			$address = $r['address'];
			$city = $r['city'];
			$state = $r['state'];
			$zip = $r['zip'];
			$email = $r['email'];
			$phone = $r['phone'];
			$user = $r['username'];

			if ($firstTime) {
				if (!isset($_SESSION['user']) && strlen($user) > 0) {
						$_SESSION['user'] = $user;
						include("./header.php");
				}
				$firstTime = false;
			}
		}
	}
	// Free result set
	mysqli_free_result($result);

	if (strlen($user) <= 0)
	{
		header('Location: http://analytics.broadwaymediagroup.com/login.php?retry=true');
	}


    // ********************************************************  //
    // Get these values from https://console.developers.google.com
    // Be sure to enable the Analytics API
    // ********************************************************    //
    $client_id = '748526673639-v3h4v7t1cm2ml5s0peokrlie4u261tio.apps.googleusercontent.com';
    $client_secret = '1M1R0m__g7ahtfaHwstZnDG-';
    $redirect_uri = 'http://analytics.broadwaymediagroup.com/index.php';


    $client = new Google_Client();
    $client->setApplicationName("Client_Library_Examples");
    $client->setClientId($client_id);
    $client->setClientSecret($client_secret);
    $client->setRedirectUri($redirect_uri);
    $client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));
    $client->setAccessType('offline');   // Gets us our refreshtoken


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
        //print "<a class='login' href='$authUrl'>Connect Me!</a>";
        header('Location: ' . $authUrl);
    }


    // Step 3: We have access we can now create our service
    if (isset($_SESSION['token'])) {
        //print "<a class='logout' href='".$_SERVER['PHP_SELF']."?logout=1'>LogOut</a><br>";

        $client->setAccessToken($_SESSION['token']);
        $service = new Google_Service_Analytics($client);

        // request user accounts
        $accounts = $service->management_accountSummaries->listManagementAccountSummaries();


    echo "<script>";
     	echo "var options = [];";
    	echo "var el = [];";
    echo "</script>";


        foreach ($accounts->getItems() as $item) {
            //echo "<b>Account:</b> ",$item['name'], "  " , $item['id'], "<br /> \n";
            foreach($item->getWebProperties() as $wp) {
                //echo '-----<b>WebProperty:</b> ' ,$wp['name'], "  " , $wp['id'], "<br /> \n";
                $views = $wp->getProfiles();
                if (!is_null($views)) {
                // note sometimes a web property does not have a profile / view
                    foreach($wp->getProfiles() as $view) {
                    	echo "<script>";
	                    	echo "el = {
	                    		account: '" . $item['name'] .  "',
	                    		property: '" . $wp['name'] .  "',
	                    		val: '" . $view['id'] .  "',
	                    		view: '" . $view['name'] . "'
	                    	};";
	                    	echo "options.push(el);";
                    	echo "</script>";
                        //echo '----------<b>View:</b> ' ,$view['name'], "  " , $view['id'], "<br /> \n";
                    }  // closes profile
                }
            } // Closes web property
        } // closes account summaries
    }

    $myDirectory = opendir("./uploads/");
    // get each entry
    while($entryName = readdir($myDirectory)) {
        $dirArray[] = $entryName;
    }
    // close directory
    closedir($myDirectory);

    //  count elements in array
    $indexCount = count($dirArray);
    //Print ("$indexCount files<br>\n");
    // sort 'em
    sort($dirArray);
?>

<center>

<br>
<br>

<form name="accountForm" id="accountForm" method="post">

<span class=regular>
	Select creative file to apply:&nbsp;&nbsp;</th>
	<select size=1 name="filename" id="filename">
	  <option value="--">- None -</option>
<?php

// loop through the array of files and print them all
for($index=0; $index < $indexCount; $index++) {
    if (substr("$dirArray[$index]", 0, 1) != ".") {
		echo "<option value='" . $dirArray[$index] . "'>" . $dirArray[$index] . "</option>";
    }
}

?>
	</select>&nbsp;&nbsp;
    <input type="button" onclick="location.href='./creative.php';" value="Manage Creative CSV Files" />
</span>
<br>
<br>


<span class=regular>

	<center>Select property to analyze</center>
	<br>

	Account: <select size=1 name="account" id="account" onchange="updateProperty();">
	</select>
	<br>
	Property: <select size=1 name="property" id="property" onchange="updateView();">
	</select>
	<br>
	View: <select style="max-width:100%" size=1 name="site" id="site">
	</select>

	<script type='text/javascript'>
		var a = document.getElementById("account");
		var p = document.getElementById("property");
		var v = document.getElementById("site");
		var account = "";
		var property = "";
		var site = "";

		for(var i = 0; i < options.length; i++) {

			if (account != options[i].account) {
				var x = document.createElement("option");
				x.text = options[i].account;
				x.value = options[i].account;
				a.options.add(x);
				account = options[i].account;
			}

			if (property != options[i].property) {
				var y = document.createElement("option");
				y.text = options[i].property;
				y.value = options[i].property;
				p.options.add(y);
				property = options[i].property;
			}

			var z = document.createElement("option");
			//z.text = options[i].account + " - " + options[i].property + " - " + options[i].view;
			z.text =  options[i].view;
			z.value = options[i].val;
			v.options.add(z);
		}

		function updateProperty() {
			var account = document.getElementById("account");
			var property = document.getElementById("property");
			var view = document.getElementById("site");

			var q = document.getElementById("property");
			var l = q.length;
			for (var i = 0; i < l; i++) {
			    q.remove(0);
			}
			q = document.getElementById("site");
			var l = q.length;
			for (var i = 0; i < l; i++) {
			    q.remove(0);
			}
			var lastItem = "";
			for(var i = 0; i < options.length; i++) {

				if (account.value == options[i].account) {
					var x = document.createElement("option");
					x.text = options[i].property;
					x.value = options[i].property;
					if (lastItem != x.value) {
						property.options.add(x);
						lastItem = x.value;
					}
				}
			}
			updateView();
		}

		function updateView() {
			var account = document.getElementById("account");
			var property = document.getElementById("property");
			var view = document.getElementById("site");

			q = document.getElementById("site");
			var l = q.length;
			for (var i = 0; i < l; i++) {
			    q.remove(0);
			}
			var lastItem = "";
			for(var i = 0; i < options.length; i++) {

				if (property.value == options[i].property) {
					var x = document.createElement("option");
					x.text = options[i].view;
					x.value = options[i].val;
					if (lastItem != x.value) {
						view.options.add(x);
						lastItem = x.value;
					}
				}
			}
		}
	</script>

</span>
<br>
<br>

<button class="w3-button w3-white w3-border w3-border-gray w3-round-large w3-padding-small" onclick="doPreview(0);">Run Report</button>

<input type="hidden" name="daform" value="accountForm">


</form>


<script type="text/javascript">

    function doPreview(report)
    {
        form=document.getElementById('accountForm');

		if (form.filename.value == "--")
		{
			alert("You must select a creative to run a report.");
			form.filename.focus();
			return(false);
		}

        if (report == 0) {
/*
			if (form.site.value == "--")
			{
				alert("You must select a site to run a report");
				form.site.focus();
				return(false);
			}
*/
	        form.action='report.php';
        }
	    else {
	    	form.action='report3.php';
	    }

        form.submit();
    }
</script>

</center>

<?php
	mysqli_close($c);
	include('./footer.php');
?>
