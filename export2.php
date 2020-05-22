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
        alert("hey");
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
    mysqli_close($c);
    include('./footer.php');
?>