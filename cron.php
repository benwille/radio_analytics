<?php

    // *********************************************************************
    // This file reads the Google Analytic Data and saves it to the database
    // *********************************************************************

    //include("./header.php");

    $user = "";
    $posted = "";
    //$startDate = "2017-11-15";
    //$endDate = "2018-01-15";
    $startDate="7daysAgo";
    $endDate="yesterday";
    $filename = "";
    $data = "";    

/*
    if (isset($_SESSION['user']) && strlen($_SESSION['user']) > 0) {
        $user = $_SESSION['user'];
    }
    else {
        header('Location: http://analytics.broadwaymediagroup.com/');
        exit();   
    }
*/
    if (isset($_POST['daform']) && $_POST['daform'] === 'accountForm') {
        if (isset($_POST['startDate']) && strlen($_POST['startDate']) > 0 ) {
            $startDate = $_POST['startDate'];   
            echo "WE are posting information about the report - start date: " . $startDate . "<br>";
        }
        if (isset($_POST['endDate']) && strlen($_POST['endDate']) > 0 ) {
            $endDate = $_POST['endDate'];   
            echo "WE are posting information about the report - end date: " . $endDate . "<br>";
        }
        if (isset($_POST['filename']) && strlen($_POST['filename']) > 0 ) {
            $filename = $_POST['filename'];   
        }
    }

    if (isset($_GET['startDate']) && strlen($_GET['startDate']) > 0 ) {
        $startDate = $_GET['startDate'];   
        echo "WE are posting information about the report - start date: " . $startDate . "<br>";
    }

    if (isset($_GET['endDate']) && strlen($_GET['endDate']) > 0 ) {
        $endDate = $_GET['endDate'];   
        echo "WE are posting information about the report - end date: " . $endDate . "<br>";
    }

    $table_data = 'ga:34619258';
    $property = 'X96';
    $view = 'KXRK';
/*
    $table_data = 'ga:114966573';
    $property = 'Apple Beer';
    $view = 'All Web Site Data';
*/
    if (isset($_GET['table_data']) && strlen($_GET['table_data']) > 0 ) {
        $table_data = $_GET['table_data'];   
        echo "WE are posting information about the report - table_data: " . $table_data . "<br>";
    }
    if (isset($_GET['property']) && strlen($_GET['property']) > 0 ) {
        $property = $_GET['property'];   
        echo "WE are posting information about the report - property: " . $property . "<br>";
    }
    if (isset($_GET['view']) && strlen($_GET['view']) > 0 ) {
        $view = $_GET['view'];   
        echo "WE are posting information about the report - view: " . $view . "<br>";
    }

    $filename = 'analytics.csv';
    if (isset($_GET['filename']) && strlen($_GET['filename']) > 0 ) {
        $filename = $_GET['filename'];   
        echo "WE are posting information about the report - filename: " . $filename . "<br>";
    }


?>

<div class="container">
    
    <center>  
    <br /><br />
    <script src="/js/excellentexport.js"></script>
    <a download= <?php echo "'" . $filename . "'" ?> href="#" id='clicker' onclick="return ExcellentExport.csv(this, 'dataTable');">Export to CSV</a>
    <br /><br />

    <table id="dataTable" class="table" border="1">
        <thead><tr><th>PropertyID</th><th>PropertyName</th><th>PropertyView</th><th>Year</th><th>Month</th><th>Day</th><th>Hour</th><th>Minute</th><th>PageViews</th><th>Sessions</th></tr></thread>
        <tbody id="tbody">
        </tbody>
    </table>
    </center>


<script>

    // Replace with your client ID from the developer console.
    var CLIENT_ID = '471111199052-httikaho3igh6bmou8mte2c8d160o746.apps.googleusercontent.com';
    //var CLIENT_ID = '385361870783-3mtb3vkmr5dr6p2u0q1l6k16illc9aul.apps.googleusercontent.com';
    var SCOPES = ['https://www.googleapis.com/auth/analytics.readonly'];

    TABLE_DATA = <?php echo "'" . $table_data . "'" ?>;
    PROPERTY =   <?php echo "'" . $property   . "'" ?>;
    VIEW =       <?php echo "'" . $view       . "'" ?>;


  function authorize(event) {
    var useImmdiate = event ? false : true;
    var authData = {
      client_id: CLIENT_ID,
      scope: SCOPES,
      immediate: useImmdiate
    };

    gapi.auth.authorize(authData, function(response) {
        queryAccounts();    
    });
  }


function queryAccounts() {
  // Load the Google Analytics client library.
  gapi.client.load('analytics', 'v3').then(function() {

    // Get a list of all Google Analytics accounts for this user
    gapi.client.analytics.management.accounts.list().then(handleAccounts);
  });
}


function handleAccounts(response) {
  // Handles the response from the accounts list method.
  if (response.result.items && response.result.items.length) {
    // Get the first Google Analytics account.
    var firstAccountId = response.result.items[0].id;

    // Query for properties.
    queryProperties(firstAccountId);
  } else {
    console.log('No accounts found for this user.');
  }
}


function queryProperties(accountId) {
  // Get a list of all the properties for the account.
  gapi.client.analytics.management.webproperties.list(
      {'accountId': accountId})
    .then(handleProperties)
    .then(null, function(err) {
      // Log any errors.
      console.log(err);
  });
}


function handleProperties(response) {
  // Handles the response from the webproperties list method.
  if (response.result.items && response.result.items.length) {

    // Get the first Google Analytics account
    var firstAccountId = response.result.items[0].accountId;

    // Get the first property ID
    var firstPropertyId = response.result.items[0].id;

    // Query for Views (Profiles).
    queryProfiles(firstAccountId, firstPropertyId);
  } else {
    console.log('No properties found for this user.');
  }
}


function queryProfiles(accountId, propertyId) {
  // Get a list of all Views (Profiles) for the first property
  // of the first Account.
  gapi.client.analytics.management.profiles.list({
      'accountId': accountId,
      'webPropertyId': propertyId
  })
  .then(handleProfiles)
  .then(null, function(err) {
      // Log any errors.
      console.log(err);
  });
}

function handleProfiles(response) {
  // Handles the response from the profiles list method.
  if (response.result.items && response.result.items.length) {
    // Get the first View (Profile) ID.
    var firstProfileId = response.result.items[0].id;

    // Query the Core Reporting API.
    //queryCoreReportingApi(firstProfileId);
    queryCoreReportingApi(TABLE_DATA);
  } else {
    console.log('No views (profiles) found for this user.');
  }
}


function queryCoreReportingApi(profileId) {
  // Query the Core Reporting API for the number sessions for
  // the past seven days.
  gapi.client.analytics.data.ga.get({
    'ids': profileId,
    'dimensions': 'ga:year,ga:month,ga:day,ga:hour,ga:minute',
    'metrics': 'ga:pageviews,ga:sessions',
    'start-date': '7daysAgo',
    'end-date': 'today',
    'filters': 'ga:medium!=referral:ga:medium!=social:ga:medium!=paid',
    'max-results': '40000',
    'sort': 'ga:year, ga:month, ga:day, ga:hour, ga:minute'
  })
  .then(function(response) {
    renderData(response.result.rows);
  })
  .then(null, function(err) {
      console.log(err);
  });
}


var creative2 = [];

function renderData(data) {

    if (data.length > 9999)
        alert("The number of records Google Analytics returned was: " + data.length);

    var s = "";
    var i = 0;

    while(i < (data.length)) {

        s += "<tr>";      
        s += "<td>" + TABLE_DATA + "</td>";
        s += "<td>" + PROPERTY + "</td>";
        s += "<td>" + VIEW + "</td>";
        s += "<td>" + data[i][0] + "</td>";
        s += "<td>" + data[i][1] + "</td>";
        s += "<td>" + data[i][2] + "</td>";
        s += "<td>" + data[i][3] + "</td>";
        s += "<td>" + data[i][4] + "</td>";
        s += "<td>" + data[i][5] + "</td>";
        s += "<td>" + data[i][6] + "</td>";
        s += "</tr>";
        i++;
    }    
        
    tbody.innerHTML = s;

    document.getElementById("clicker").click();
}
</script>

<script src="https://apis.google.com/js/client.js?onload=authorize"></script>



<?php

    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************




    include('./import.php');    
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
    
    // see if we are goign to upload a csv file or import a CSV file into the databaswe
    if (isset($_GET['filename']) && strlen($_GET['filename']) > 0 ) {
        $filename = $_GET['filename'];   
        echo "<br /><br />The filename to ingest into the databse is: " . $filename . "<br /><br />";
    }


    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************
    // *********************************************************************



$target_dir = "property/";
$filename = basename($_FILES["fileToUpload"]["name"]);
$target_file = $target_dir . $filename;
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        include("./header.php");
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        include("./header.php");
        echo "File is not an image.";
        $uploadOk = 0;
    }
}

echo "we are in the upload<br>";

// Check if file already exists
//if (file_exists($target_file)) {
//    echo "Sorry, file already exists.";
//    $uploadOk = 0;
//}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 1000000) {
    include("./header.php");
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "csv") {
    echo "Sorry, only CSV files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    include("./header.php");
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        //echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        header('Location: http://analytics.broadwaymediagroup.com/import.php?filename=' . $filename);
        exit();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

include("./import.php");

window.setTimeout(uploadFile, 10000);

    function uploadFile() {

        alert ('hey bro');
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
    }


    echo "Import Complete<br /></center>";

    mysqli_close($c);

?>