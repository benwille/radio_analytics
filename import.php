<?php

    // *********************************************************************
    // This file reads the Google Analytic Data and saves it to the database
    // *********************************************************************

    include("./header.php");

    sleep(10);
    echo "we are importing";

    $user = "";
    $filename = "";
    $data = "";    

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
    
    // make sure there is a user logged in
    if (isset($_SESSION['user']) && strlen($_SESSION['user']) > 0) {
        $user = $_SESSION['user'];
    }
    else {
        header('Location: http://analytics.broadwaymediagroup.com/');
        exit();   
    }

    if (strlen($user) <= 0)
    {                         
        echo "<p>We could not locate a logged in user to this account.  You can <a href='index.php'>Log In</a> if you know you have an account.</span></p>";
        include('./footer.php');
        exit();
    }
/*
    echo "dumping _POST<br>";
    var_dump($_POST);
    echo "<br>dumping _REQUEST<br>";
    var_dump($_REQUEST);
    echo "<br>dumping _GET<br>";
    var_dump($_GET);
    echo "<br>dumping _SESSION<br>";
    var_dump($_SESSION);
*/
    // see if we are goign to upload a csv file or import a CSV file into the databaswe
    if (isset($_GET['filename']) && strlen($_GET['filename']) > 0 ) {
        $filename = $_GET['filename'];   
        echo "<br /><br />The filename to ingest into the databse is: " . $filename . "<br /><br />";
    }
    else {
?>
        <form action="upload_property.php" method="post" enctype="multipart/form-data">
            Select csv file with property data to upload:<br>
            <input type="file" name="fileToUpload" id="fileToUpload">
            <button class="w3-button w3-white w3-border w3-border-gray w3-round-large w3-padding-small" type ="submit">Upload CSV</button>
        </form>
<?php       
        mysqli_close($c);
        include('./footer.php');
        exit();
    }
?>

<center>

<?php
/*
    if (strlen($filename) > 0) {
        $maketemp = "
            CREATE TABLE property (
              `id` int NOT NULL AUTO_INCREMENT,
              `PropertyID` varchar(40),
              `PropertyName` varchar(40),
              `PropertyView` varchar(40),          
              `Year` varchar(5),
              `Month` varchar(5),
              `Day` varchar(5),
              `Hour` varchar(5),
              `Minute` varchar(5),
              `PageViews` varchar(5),
              `Sessions` varchar(5),
              PRIMARY KEY(id)
            )";     
        $result = mysqli_query($c, $maketemp) or die ("create table - Sql error : ".mysqli_error($c));
          
        $alter = "
            ALTER TABLE property ADD UNIQUE (
              `PropertyID`,
              `PropertyName`,
              `PropertyView`,          
              `Year`,
              `Month`,
              `Day`,
              `Hour`,
              `Minute`
            )";     
        $result = mysqli_query($c, $alter) or die ("alert table - Sql error : ".mysqli_error($c));
      }    

      ALTER TABLE property ADD UNIQUE (`PropertyID`,`PropertyName`,`PropertyView`,`Year`,`Month`,`Day`,`Hour`,`Minute`);
  
*/

      $file = "./property/" . $filename;   
      $csv = file_get_contents($file);

//echo "<br />The file is: " . $file . "<br />";
//echo "<br />csv is: " . $csv . "<br />";


      $data = explode("\r",$csv);

      $cnt = count($data);

      for ($i = 0; $i < $cnt; $i++) 
      {

          $res = explode(",", $data[$i]);

          // don't include the header
          if ($i > 0) {

              // ID,Property,View,Year,Month,Day,Hour,Minute,PageViews,Sessions
              $inserttemp = "INSERT INTO property (PropertyID,PropertyName,PropertyView,Date,Year,Month,Day,Hour,Minute,PageViews,Sessions) values
                (
                    '" . trim($res[0]) ."',
                    '" . trim($res[1]) ."', 
                    '" . $res[2] ."', 
                    '" . $res[3] ."', 
                    '" . $res[4] ."', 
                    '" . $res[5] ."',
                    '" . $res[6] ."',
                    '" . $res[7] ."',
                    '" . $res[8] ."',
                    '" . $res[9] ."',
                    '" . $res[10] ."'
                );
              ";
              mysqli_query($c, $inserttemp);
          }
      }       

    echo "Import Complete<br /></center>";

    mysqli_close($c);
    include('./footer.php');
?>