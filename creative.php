<?php

	session_start();

/*
echo "<br>";
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
echo "<br>";
*/


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

	if (isset($_POST['filename']) && strlen($_POST['filename']) > 0) {
		$filename = $_POST['filename'];
		if (!unlink("./uploads/" . $filename)) {
  			echo ("<center><div class='text-danger'> Error deleting " . $filename . "</div><br></center>");
  		}
		else {
  			echo ("<center><div class='text-success'> Deleted " . $filename . "<br></div></center>");
  		}
	}

//echo "user is: " . $user . "<br>";
//echo "login is: " . $login . "<br>";

?>

	<center>
	<H1>Broadway Media Radio Analytics Information Portal</H1>
	</center>

<?php


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
<br>

<form name="accountForm" id="accountForm" method="post">


<span class=regular>
	Select creative file to remove:&nbsp;&nbsp;</th>
	<select size=1 name="filename" id="filename">&nbsp;&nbsp;
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
<center"><input type="submit" value="Remove Creative File" tabindex="100"></center>
</span>
</form>

<br>
<br>
</center>

<?php
	include('./footer.php');
?>
