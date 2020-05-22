<?php
  session_start();

  $user = "";
  if (isset($_SESSION['user']) && $_SESSION['user'])
    $user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Broadway Media Radio Analytics</title>
  <!-- <link href="./css/global.css" media="all" rel="stylesheet" type="text/css"> -->
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

  <link rel="shortcut icon" href="favicon.ico">



<style>

body {
  padding-right: 0px;
  padding-left: 0px;
  background-color: White;
  height: 100%;
}

#main-wrapper {
    padding: 0 0 0px;
    position: relative;
}

footer {
    bottom: 0;
    height: 0px;
    left: 0;
    position: absolute;
    width: 100%;
}

@media print
{
    .no-print, .no-print *
    {
        display: none !important;
    }
}

.pagebreak { page-break-before: always; }

</style>


</head>

  <body id="home">


    <header>

    <div class='no-print'>
      <div class="w3-bar w3-light-blue">
        <div class="p-2 container">Radio Analytics</div>
      </div>
    </div>

    <div class="w3-bar w3-white w3-center my-3">
      <center>
      <a href="./index.php">
        <img src="./images/RadioAnalyticsLogo.png" style="max-width:200px; max-height: 400px align: center;" alt="Broadway Media Radio Analytics">
      </a>
      </center>
    </div>


    <div class="w3-bar w3-light-blue">
      <div class='no-print container' style="width:inherit">
<?php
    if (strlen(trim($user)) > 0) {
        echo "<div class='w3-dropdown-hover w3-light-blue'>";
        echo "<a href='./index.php'><button class='w3-button'>Reports</button></a>";
        if ($user==="broadway") {
            echo "<a href='./people.php'><button class='w3-button'>Admin</button></a>";
            echo "<a href='./creative.php'><button class='w3-button'>Creative</button></a>";
        }
        echo "</div>";
        //echo '<a href="./account.php" class="w3-bar-item w3-button w3-right">View Account</a>';
        echo '<a href="./logout.php" class="w3-bar-item w3-button w3-right">Log Out</a>';
    }
    else {
        echo '<a href="./login.php" class="w3-bar-item w3-button w3-right">Log In</a>';
    }
?>
      </div>
    </div>

    </header>


<div class="content container" style="background-color: White;">
