
<?php

	session_start();
/*
echo "dumping _POST<br>";
var_dump($_POST);
echo "<br>";
echo "<br>dumping _REQUEST<br>";
var_dump($_REQUEST);
echo "<br>dumping _GET<br>";
var_dump($_GET);
echo "<br>dumping _SESSION<br>";
var_dump($_SESSION);
echo "<br>";
*/
	$user = "";
	if (isset($_SESSION['user']) && strlen($_SESSION['user']) > 0) {
	    $user = $_SESSION['user'];
	}
	else {
		// redirect the page to the login page
		header('Location: http://analytics.broadwaymediagroup.com/login.php');
		exit();		
	}

	$type = "";
	$rowID = "";

    if (isset($_GET['rowID']) && strlen($_GET['rowID']) > 0) {
    	$type = 'edit';
        $rowID = $_GET['rowID'];   
    }
    else {
    	$type = 'new';
    }

//echo "The type is: " . $type . "<br>";

    include('./include/connect.php');
    $c = connect('broadway');


	if (!$c) {
		echo "Error: unable to connect to mysql: " . PHP_EOL;
		echo "debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		echo "debugging error: " . mysqli_connect_error() . PHP_EOL;
		mail("rob.gray@grayskytech.com","SQL ERROR!", 'edit.php - We had an error! <br> with Error: ' . mysqli_connect_error() . PHP_EOL, "From:Broadway<rob@grayskytech.com>");
		include('../footer.php');
		mysqli_close($c);
		exit();
	} 

	$username = "";
	$password = "";
	$fname = "";
	$lname = "";
	$phone = "";
	$address = "";
	$city = "";
	$state = "";
	$zip = "";
	$email = "";

	include("header.php");

    if (isset($rowID) && strlen($rowID) > 0) {
		$query_sql = "select * from users where id = " . $rowID;
//echo "The query is: " . $query_sql . "<br>";
		$result = mysqli_query($c, $query_sql);  
		if(!$result) 
		{
			echo("<p>We had a problem with the database.  Sorry for the problem.  The System administrator has been notified</P>");
			$err = mysqli_error($c);
			echo "the sql error was: " . $err;
			echo "<br>";
			mail("rob.gray@grayskytech.com","SQL ERROR!", 'edit.php - We had an error! ' . $query_sql . '<br> with Error: ' . $err, "From:Broadway Analytics<rob@grayskytech.com>");
			include('/footer.php');
			mysqli_close($c);
			exit();
		}

		$rowcount = mysqli_num_rows($result);
		if ($rowcount > 0) 
		{		
			while ($r = mysqli_fetch_assoc($result))
			{
				//$rowID = $r['products.id'];
				$rowID = $r['id'];
				$username = $r['username'];
				$password = $r['password'];
				$fname = stripslashes($r['fname']);
				$lname = stripslashes($r['lname']);
				$phone = $r['phone'];
				$address = $r['address'];
				$city = $r['city'];
				$state = $r['state'];
				$zip = $r['zip'];
				$email = $r['email'];
			}		
		}	
	}
?>

<script language="JavaScript">
<!--

function check(f) {

	if (f.username.value.length <= 0) {
		alert("You must enter a username");
		f.username.focus();
		return(false);
	}
	if (f.password.value.length <= 0) {
		alert("You must enter a password");
		f.partNo.focus();
		return(false);
	}
	if (f.fname.value.length <= 0) {
		alert("You must enter a First and last name");
		f.fname.focus();
		return(false);
	}
	if (f.lname.value.length  <= 0) {
		alert("You must enter a first and last name");
		f.lname.focus();
		return(false);
	}
	if (f.phone.value.length <= 0) {
		alert("You must enter a phone number");
		f.phone.focus();
		return(false);
	}
<?php /*
	if (f.address.value.length <= 0) {
		alert('You must enter an address.');
		f.address.focus();
		return(false);
	}
	if (f.city.value.length <= 0) {
		alert('You must enter a city.');
		f.city.focus();
		return(false);
	}
	if (f.state.value.length <= 0) {
		alert('You must enter a state.');
		f.state.focus();
		return(false);
	}
	if (f.zip.value.length <= 0) {
		alert('You must enter a zip code.');
		f.zip.focus();
		return(false);
	}
*/ ?>	
	if (f.email.value.length <= 0) {
		alert('You must enter an email address.');
		f.email.focus();
		return(false);
	}
	return(true);
}
//-->
</script>

<center>
<H1>Add/Edit Person Record</H1>
</center>

<form name="EditForm" class="edit" method="post" action="people.php" autocomplete="off" onSubmit="return check(this)">
<p>

</p>
<center>
<table border="1" width="90%" cellpadding="2" cellspacing="0" align=center>

<tr><th valign=top align=right>Username:</th>
	<td valign=top>
		<input type="text" id="username" value=<?php echo "\"" . $username . "\"" ?> name="username" size=60 maxlength=60 tabindex="10">
	</td>
</tr>

<tr><th valign=top align=right>Password:</th>
	<td valign=top>
		<input type="password" id="password" value=<?php echo "\"" . $password . "\"" ?> name="password" size=60 maxlength=60 tabindex="20">
	</td>
</tr>

<tr><th valign=top align=right>First Name:</th>
	<td valign=top>
		<input type="text" id="fname" value=<?php echo "'" . $fname . "'" ?> name="fname" size=60 maxlength=60 tabindex="30">&nbsp;
	</td>
</tr>

<tr><th valign=top align=right>Last Name:</th>
	<td valign=top>
		<input type="text" id="lname" name="lname" value=<?php echo "\"" . $lname . "\"" ?> size=60 maxlength=60 tabindex="40">
	</td>
</tr>

<tr><th valign=top align=right>Phone:</th>
	<td valign=top>
		<input type="text" id="phone" name="phone" value=<?php echo "\"" . $phone . "\"" ?> size=60 maxlength=60 tabindex="50">
	</td>
</tr>
<?php /*
<tr><th valign=top align=right>Address:</th>
	<td valign=top>
		<input type="text" id="address" name="address"value=<?php echo "\"" . $address . "\"" ?>  size=60 maxlength=60 tabindex="60">
	</td></tr>
<tr><th valign=top align=right>City:</th>
	<td valign=top>
		<input type="text" id="city" name="city" value=<?php echo "\"" . $city . "\"" ?> size=60 maxlength=60 tabindex="70">
	</td>
</tr>
<tr><th valign=top align=right>State:</th>
	<td valign=top>
		<input type="text" id="state" name="state" value=<?php echo "\"" . $state . "\"" ?> size=5 maxlength=2 tabindex="80">
	</td>
</tr>

<tr><th valign=top align=right>Zip Code:</th>
	<td valign=top>
		<input type="text" id="zip" name="zip"  value=<?php echo "\"" . $zip . "\"" ?> size=60 maxlength=60 tabindex="90">
	</td>
</tr>
*/ ?>
<tr><th valign=top align=right>email Address:</th>
	<td valign=top>
		<input type="text" id="email" name="email"  value=<?php echo "\"" . $email . "\"" ?> size=60 maxlength=60 tabindex="95">
	</td>
</tr>

</table>

</br>
<center"><input type="submit" value="Save" tabindex="100"></center>

<input type="hidden" name="daform" value="EditForm">
<input type="hidden" name="subject" value="Edit Form">
<input type="hidden" name="rowID" value=<?php echo "\"" . $rowID . "\"" ?>>
<?php
if ($type === 'new') {
	echo "<input type='hidden' name='type' value='new'>";
}
?>
</form>
</center>



<table border="0" width="90%" cellpadding="2" cellspacing="0" align=center>
<tr class='separator_info'><td>&nbsp;</td></tr>
<tr><td colspan=2 class="body13" align=left>

</td></tr>
</table>

<br>

<script language="JavaScript">
<!--

	document.getElementById("category").focus()

//-->
</script>

<?php
	include('footer.php');
?>
