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
		header('Location: http://analytics.broadwaymediagroup.com/');
		exit();		
	}

//echo "user is: " . $user . "<br>";
//echo "login is: " . $login . "<br>";

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
				echo '<br><br>';
				echo '<table border="1" width="90%" cellpadding="2" cellspacing="0" align=center>';
				echo '<tr><th colspan=8 class=normal align=center>We have the following user information</th></tr>';
				echo '<tr><th>Name</th><th>Address</th><th>City</th><th>State</th><th>Zip</th><th>Phone</th></tr>';
				$firstTime = false;
			}


			echo "<tr><td class='body10' valign=top align=left>" . $firstName . " " . $lastName . "</td><td class='body10'>" . $address . "</td><td class='body10'>" . $city . "</td><td class='body10'>" . $state . "</td><td class='body10'>" . $zip . "</td><td class='body10'>" . $phone . "</td></tr>";
		}		
		echo '</table>';
	}	
	// Free result set
	mysqli_free_result($result);	

	if (strlen($user) <= 0)
	{
		header('Location: http://analytics.broadwaymediagroup.com/login.php?retry=true');	    
	}


	$query_sql = "select * from account, memberships where memberships.userID=" . $userID . " and memberships.accountID = account.id";

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

	$state = "";
	if ($rowcount > 0) 
	{
		while ($r = mysqli_fetch_assoc($result))
		{
			//$membershipID = $r['id'];
			$name = stripslashes($r['name']);
			$address = $r['address'];
			$city = $r['city'];
			$state = $r['state'];
			$zip = $r['zip'];
			$phone = $r['phone'];
			$accountID = $r['accountID'];
			$userID = $r['userID'];

			if ($thisTime) {
				if (!isset($_SESSION['accountID']) && strlen($accountID) > 0) {
						$_SESSION['accountID'] = $accountID;
				}			
				if (!isset($_SESSION['userID']) && strlen($userID) > 0) {
						$_SESSION['userID'] = $userID;
				}			
				echo '<br><br>';
				echo '<table border="1" width="90%" cellpadding="2" cellspacing="0" align=center>';
				echo '<tr><th colspan=8 class=normal align=center>This user has access to the following accounts</th></tr>';
				echo '<tr><th>Account Name</th><th>Address</th><th>City</th><th>State</th><th>Zip</th><th>Phone</th></tr>';
				$thisTime = false;
			}


			echo "<tr><td class='body10' valign=top align=left>" . $name. "</td><td class='body10'>" . $address . "</td><td class='body10'>" . $city . "</td><td class='body10'>" . $state . "</td><td class='body10'>" . $zip . "</td><td class='body10'>" . $phone . "</td></tr>";
		}		
		echo '</table>';	
	}	
	// Free result set
	mysqli_free_result($result);	


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

/*
//path to directory to scan
$directory = "./uploads/";

//get all text files with a .txt extension.
$texts = glob($directory . "*.csv");

echo "about to display $texts <br>";
//print each file name
foreach($texts as $text)
{
    echo "file: " . $text . "<br>";
}
*/

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
<form action="upload.php" method="post" enctype="multipart/form-data">
    Select csv file with creative data to upload:<br>
    <input type="file" name="fileToUpload" id="fileToUpload">
	<button class="w3-button w3-white w3-border w3-border-gray w3-round-large w3-padding-small" type ="submit">Upload CSV</button>
</form>

<br>

<form action="upload_property.php" method="post" enctype="multipart/form-data">
    Select csv file with property data to upload:<br>
    <input type="file" name="fileToUpload" id="fileToUpload">
	<button class="w3-button w3-white w3-border w3-border-gray w3-round-large w3-padding-small" type ="submit">Upload CSV</button>
</form>

<br>
<br>

<form name="accountForm" id="accountForm" method="post">

<?php
/*
// Removed the start and start date for now.  The creative file will dictate the date range.
<p>
<span class=regular>
  Start Date: <input type="date" name="startDate">
  End Date: <input type="date" name="endDate">
</span>
</p>
*/
?>

<span class=regular>
	Select creative file to apply:&nbsp;&nbsp;</th> 
	<select name="filename" id="filename">
	  <option value="--">- None -</option>
<?php

// loop through the array of files and print them all
for($index=0; $index < $indexCount; $index++) {
    if (substr("$dirArray[$index]", 0, 1) != ".") { 
		echo "<option value='" . $dirArray[$index] . "'>" . $dirArray[$index] . "</option>";
    }
}

?>
	</select>
</span>
<br>
<br>



<span class=regular>
	Select property to analyze:&nbsp;&nbsp;</th> 
	<select name="site" id="site">
	  <option value="--">- None -</option>

<?php
	$query_sql = "select Distinct PropertyID, PropertyName from property where accountID=" . $accountID;

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

	$propertyName = "";
	$propertyID = "";
	if ($rowcount > 0) 
	{
		while ($r = mysqli_fetch_assoc($result))
		{
			$propertyID = $r['PropertyID'];
			$propertyName = $r['PropertyName'];
			echo "<option value=" . $propertyID . ">" . $propertyName . "</option>";
		}		
	}	
	// Free result set
	mysqli_free_result($result);	
?>
	</select>
</span>
<br>
<br>

<button class="w3-button w3-white w3-border w3-border-gray w3-round-large w3-padding-small" onclick="doPreview(0);">Run Database Report</button>
<button class="w3-button w3-white w3-border w3-border-gray w3-round-large w3-padding-small" onclick="doPreview(1);">Run Real Time Report</button>

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
			if (form.site.value == "--") 
			{
				alert("You must select a site to run a report");
				form.site.focus();
				return(false);
			}
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
