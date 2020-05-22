<?php
	include("./header.php");
?>


<div class="box" style="background-color: White; height: 400px">

	<center>
	<H1>Radio Analytics Reporting</H1>
	</center>


<?php


	// see if we have a custID and then see if we already have this person
	include('include/connect.php');
	$c = connect('radio');

	$w = "username='" . $username . "' and password='" . $password . "'";

	$query_sql = "select * from account where " . $w;

	$sql = mysqli_query($c, $query_sql);  
	if(!$sql) 
	{
		echo("<p>We had a problem with the database.  Sorry for the problem.  The System administrator has been notified</P>");
		$err = mysqli_error($c);
		echo "the sql error was: " . $err;
		echo "<br>";
		mail("rob.gray@grayskytech.com","SQL ERROR!", 'applicant.php - We had an error! ' . $query_sql . '<br> with Error: ' . $err, "From:SupplyD Wholesale<rob@grayskytech.com>");
		include('./footer.php');
		mysqli_close($c);
		exit();
	}
	
	//$r = mysql_fetch_assoc($c, $sql);
	$s = $query_sql;
	$q = $c->query($s);

	$rowcount = $q->num_rows;

	$firstName = "";
//echo "We have " . $rowcount . " customers";
	if ($rowcount > 0) 
	{
		echo '<table border="1" width="90%" cellpadding="2" cellspacing="0" align=center background="./images/index_07.gif">';
		echo '<tr><th colspan=8 class=normal align=center>We have the following information</th></tr>';
		echo '<tr><th>Name</th><th>Address</th><th>City</th><th>State</th><th>Zip</th><th>Phone</th></tr>';

		$firstName = $r['F_Name'];
		while ($r = $c->fetch($q)) 
		{
			echo "<tr><td class='body10' valign=top align=left>" . stripslashes($r['F_Name']) . " " . stripslashes($r['L_Name']) . "</td><td class='body10'>" . $r['Address'] . "</td><td class='body10'>" . $r['City'] . "</td><td class='body10'>" . $r['State'] . "</td><td class='body10'>" . $r['Zip'] . "</td><td class='body10'>" . $r['Phone'] . "</td></tr>";
		}
		echo '</table>';
	}	
	mysqli_close($c);
?>


<script language="JavaScript">
<!--
function check(f) 
{
	if (f.fname.value.length <= 0) 
	{
		alert('Please enter your FIRST NAME. This field is required.');
		f.fname.focus();
		return(false);
	}
	if (f.lname.value.length <= 0) 
	{
		alert('Please enter your LAST NAME. This field is required.');
		f.lname.focus();
		return(false);
	}
	if (f.address.value.length <= 0) 
	{
		alert('Please enter your ADDRESS. This field is required.');
		f.address.focus();
		return(false);
	}
	if (f.city.value.length <= 0) 
	{
		alert('Please enter the CITY you reside in. This field is required.');
		f.city.focus();
		return(false);
	}
	if (f.state.value == '--') 
	{
		alert('Please enter the STATE you reside in. This field is required.');
		f.state.focus();
		return(false);
	}
	if (f.homePhone1.value.length > 0)
	{
		if (f.homePhone1.value.length < 3 || f.homePhone2.value.length < 3 || f.homePhone3.value.length < 4)
		{

			alert('Please enter a complete telephone number');
			f.homePhone1.focus();
			return(false);
		}
		else if (!isInteger(f.homePhone1.value) || !isInteger(f.homePhone2.value) || !isInteger(f.homePhone3.value))
		{
			alert('Please enter a complete telephone number using only integers');
			f.homePhone1.focus();
			return(false);
		}
	}
	return(true);
}

//-->
</script>




<?php if (strlen($user) > 0 )
{													
	$newApp = 'false';
	echo "Welcome " . $user . " to Broadway Media Analytics";
}
else
{
	$newApp = 'true';
    echo "<p>We could not locate a customer account in our database based on the information you provided.  You can <a href='index.php'>Search Again</a> if you know you have an account.</span>.</p>";
	echo "</div>";

	include('./footer.php');
	exit();
}

?>

</div>

<?php
  include('./footer.php');
?>