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
	$login = false;

	if (isset($_SESSION['user']) && strlen($_SESSION['user']) > 0) {
	    $user = $_SESSION['user'];
	    include("./header.php");
	}
	else {
		// redirect the page to the login page
		header('Location: http://analytics.broadwaymediagroup.com/login.php');
		exit();
	}

?>

	<center>
	<H1>Broadway Media Radio Analytics Information Portal</H1>
	</center>

<?php

	include('include/connect.php');
	$c = connect('broadway');


	$rowID = "";
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

    if (isset($_GET['rowID']) && strlen($_GET['rowID']) > 0 ) {
        $rowID = $_GET['rowID'];
    }

    if (isset($_POST['daform']) && $_POST['daform'] === 'EditForm') {

    	//echo " WE HAVE AN UPDATE TO A RECORD<br>";
        if (isset($_POST['username']) && strlen($_POST['username']) > 0 ) {
            $username = $_POST['username'];
        }
        if (isset($_POST['password']) && strlen($_POST['password']) > 0 ) {
            $password = $_POST['password'];
        }
        if (isset($_POST['fname']) && strlen($_POST['fname']) > 0 ) {
            $fname = $_POST['fname'];
        }
        if (isset($_POST['lname']) && strlen($_POST['lname']) > 0 ) {
            $lname = $_POST['lname'];
        }
        if (isset($_POST['phone']) && strlen($_POST['phone']) > 0 ) {
            $phone = $_POST['phone'];
        }
        if (isset($_POST['address']) && strlen($_POST['address']) > 0 ) {
            $address = $_POST['address'];
        }
        if (isset($_POST['city']) && strlen($_POST['city']) > 0 ) {
            $city = $_POST['city'];
        }
        if (isset($_POST['state']) && strlen($_POST['state']) > 0 ) {
            $state = $_POST['state'];
        }
        if (isset($_POST['zip']) && strlen($_POST['zip']) > 0 ) {
            $zip = $_POST['zip'];
        }
        if (isset($_POST['email']) && strlen($_POST['email']) > 0 ) {
            $email = $_POST['email'];
        }

        if (isset($_POST['rowID']) && strlen($_POST['rowID']) > 0 ) {
            $rowID = $_POST['rowID'];

			$query_sql = "update users set username='" . $username . "'";
			$query_sql .= ", password='" . $password . "'";
			$query_sql .= ", fname='" . addslashes($fname) . "'";
			$query_sql .= ", lname='" . addslashes($lname) . "'";
			$query_sql .= ", phone='" . $phone . "'";
			$query_sql .= ", address='" . $address . "'";
			$query_sql .= ", city='" . $city . "'";
			$query_sql .= ", state='" . $state . "'";
			$query_sql .= ", zip='" . $zip . "'";
			$query_sql .= ", email='" . $email . "'";
			$query_sql .= " where id=" . $rowID;

 //echo "We are going to update an existing record with query: " . $query_sql . "<br>";

			$result = mysqli_query($c, $query_sql);
			if(!$result)
			{
				echo("<p>We had a problem updating the database.  Sorry for the problem.  The System administrator has been notified</P>");
				$err = mysqli_error($c);
				echo "the sql error was: " . $err;
				echo "<br>";
				mail("rob.gray@grayskytech.com","SQL ERROR!", 'admin/index.php - We had an error! ' . $query_sql . '<br> with Error: ' . $err, "From:Broadway Analytics<rob@grayskytech.com>");
				include('./footer.php');
				mysqli_close($c);
				exit();
			}
			mysqli_free_result($result);
		}

        if (isset($_POST['type']) && strlen($_POST['type']) > 0) {
    		$query_sql = "insert into users ";
			$query_sql .= "(username, password, fname, lname, phone, address, city, state, zip, email) values (";
			$query_sql .= "'" . $username . "'";
			$query_sql .= ",'" . $password . "'";
			$query_sql .= ",'" . addslashes($fname) . "'";
			$query_sql .= ",'" . addslashes($lname) . "'";
			$query_sql .= ",'" . $phone . "'";
			$query_sql .= ",'" . $address . "'";
			$query_sql .= ",'" . $city . "'";
			$query_sql .= ",'" . $state . "'";
			$query_sql .= ",'" . $zip . "'";
			$query_sql .= ",'" . $email . "')";

//echo "We are going to insert a new record with query: " . $query_sql . "<br>";

			$result = mysqli_query($c, $query_sql);
			if(!$result)
			{
				echo("<p>We had a problem with the database.  Sorry for the problem.  The System administrator has been notified</P>");
				$err = mysqli_error($c);
				echo "the sql error was: " . $err;
				echo "<br>";
				mail("rob.gray@grayskytech.com","SQL ERROR!", 'admin/index.php - We had an error! ' . $query_sql . '<br> with Error: ' . $err, "From:Broadway Analytics<rob@grayskytech.com>");
				include('./footer.php');
				mysqli_close($c);
				exit();
			}
			mysqli_free_result($result);
		}
    }
    else if (isset($_GET['rowID']) && isset($_GET['delete']) && $_GET['delete'] == 'true') {
    	//$rowID = $_GET['rowID'];
//echo "We are going to delete " . $rowID . "<br>";
    	$query_sql = "delete from users where id = " . $rowID;
		$result = mysqli_query($c, $query_sql);
		if(!$result)
		{
			echo("<p>We had a problem with the database.  Sorry for the problem.  The System administrator has been notified</P>");
			$err = mysqli_error($c);
			echo "the sql error was: " . $err;
			echo "<br>";
			mail("rob.gray@grayskytech.com","SQL ERROR!", 'people.php - We had an error! ' . $query_sql . '<br> with Error: ' . $err, "From:broadway<rob@grayskytech.com>");
			include('./footer.php');
			mysqli_close($c);
			exit();
		}
		echo "<center>The user was removed.</center>";
    }

?>



<?php

	if (strlen($user) > 0)
	{
		$query_sql = "select * from users";
		//echo "The query is: " . $query_sql . "<br>";

		$result = mysqli_query($c, $query_sql);
		if(!$result)
		{
			echo("<p>We had a problem with the database.  Sorry for the problem.  The System administrator has been notified</P>");
			$err = mysqli_error($c);
			echo "the sql error was: " . $err;
			echo "<br>";
			mail("rob.gray@grayskytech.com","SQL ERROR!", 'people.php - We had an error! ' . $query_sql . '<br> with Error: ' . $err, "From:broadway<rob@grayskytech.com>");
			include('./footer.php');
			mysqli_close($c);
			exit();
		}

		$rowcount = mysqli_num_rows($result);

		echo "<br><br>";
		echo "<center><font color='blue'><a href='edit.php' class='btn btn-primary'>Add a new user</a></font></center>";

		echo '<br><br>';
        echo '<div class="table-responsive">';
		echo '<table class="table table-sm table-striped" border="1" width="90%" cellpadding="2" cellspacing="0" align=center>';
		echo '<tr><th colspan=11 class=normal align=center>Radio Analytics User List</th></tr>';
		echo '<tr>';
		echo '<th>Username</th>';
		echo '<th>First Name</th>';
		echo '<th>Last Name</th>';
		echo '<th>Phone</th>';
		/*
		echo '<th>Address</th>';
		echo '<th>City</th>';
		echo '<th>State</th>';
		echo '<th>Zip</th>';
		*/
		echo '<th>Email</th>';
		echo '<th>&nbsp;</th>';
        echo '<th>&nbsp;</th>';
		echo '</tr>';

		if ($rowcount > 0)
		{
			while ($r = mysqli_fetch_assoc($result))
			{
				$rowID = $r['id'];
				$username = $r['username'];
				$password = $r['password'];
				$fname = $r['fname'];
				$lname = $r['lname'];
				$phone = $r['phone'];
				$city = $r['city'];
				$state = $r['state'];
				$zip = $r['zip'];
				$email = $r['email'];

	 			echo "<tr>";
	 			echo "<td class='body10'>" . $username . "</td>";
	 			//echo "<td class='body10'>" . $password . "</td>";
	 			echo "<td class='body10'>" . stripslashes($fname) . "</td>";
	 			echo "<td class='body10'>" . stripslashes($lname) . "</td>";
	 			echo "<td class='body10'>" . $phone . "</td>";
	 			/*
	 			echo "<td class='body10'>" . $address . "</td>";
	 			echo "<td class='body10'>" . $city . "</td>";
	 			echo "<td class='body10'>" . $state . "</td>";
	 			echo "<td class='body10'>" . $zip . "</td>";
	 			*/
	 			echo "<td class='body10'>" . $email . "</td>";
	 			echo "<td class='body10'><font color='blue'><a href='edit.php?rowID=" . $rowID . "'>Edit</a></font></td>";
	 			echo "<td class='body10'><font color='blue'><a href='people.php?rowID=" . $rowID . "&delete=true'>Delete</a></font></td>";
	 			echo "</tr>";
			}
		}
		echo '</table></div><br>';
		echo "<br><center><font color='blue'><a href='edit.php' class='btn btn-primary'>Add a new user</a></font></center><br>";
		// Free result set
		mysqli_free_result($result);
	}

	mysqli_close($c);
	include('./footer.php');
?>
