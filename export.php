<?php

    // *********************************************************************
    // This file reads the Google Analytic Data and saves it to the database
    // *********************************************************************

    include("./header.php");

    $user = "";
    $posted = "";
    //$startDate = "2017-11-15";
    //$endDate = "2018-01-15";
    $startDate="7daysAgo";
    $endDate="yesterday";
    $filename = "";
    $data = "";    


    if (isset($_SESSION['user']) && strlen($_SESSION['user']) > 0) {
        $user = $_SESSION['user'];
    }
    else {
        header('Location: http://analytics.broadwaymediagroup.com/');
        exit();   
    }
    
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


    if (strlen($user) <= 0)
    {                         
        echo "<p>We could not locate a logged in user to this account.  You can <a href='index.php'>Log In</a> if you know you have an account.</span></p>";
        include('./footer.php');
        exit();
    }

?>

<div class="container">
    <center>
    <section id="embed-api-auth-container"></section>

    <br>
    <b3>* Choose Account *</b3>
    <section id="view-selector"></section> 
  
    <br /><br />
    <script src="/js/excellentexport.js"></script>
    <a download="analytics.csv" href="#" onclick="return ExcellentExport.csv(this, 'dataTable');">Export to CSV</a>
    <br /><br />

    <table id="dataTable" class="table" border="1">
        <thead><tr><th>PropertyID</th><th>PropertyName</th><th>PropertyView</th><th>Date</th><th>Year</th><th>Month</th><th>Day</th><th>Hour</th><th>Minute</th><th>PageViews</th><th>Sessions</th></tr></thread>
        <tbody id="tbody">
        </tbody>
    </table>


<script>

(function(w,d,s,g,js,fjs){
    g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(cb){this.q.push(cb)}};
    js=d.createElement(s);fjs=d.getElementsByTagName(s)[0];
    js.src='https://apis.google.com/js/platform.js';
    fjs.parentNode.insertBefore(js,fjs);js.onload=function(){g.load('analytics')};
}(window,document,'script'));
</script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="/js/view-selector2.js"></script>

<script>

var TABLE_DATA = 'ga:34619258';
//var TABLE_DATA = 'ga:114966573'; // Apple Beer
var PROPERTY = '';
var VIEW = '';

gapi.analytics.ready(function() {
    
    var CLIENT_ID = '471111199052-httikaho3igh6bmou8mte2c8d160o746.apps.googleusercontent.com';
    //var CLIENT_ID = '114955841924377617484';

    gapi.analytics.auth.authorize({
        container: 'embed-api-auth-container',
        clientid: CLIENT_ID,
    });

    var viewSelector = new gapi.analytics.ext.ViewSelector2({
        container: 'view-selector'
    });

    gapi.analytics.auth.on('success', function(response) {
        viewSelector.execute();
    });

    viewSelector.on('viewChange', function(data) {
        TABLE_DATA = data.ids;
        PROPERTY = data.property.name;
        VIEW = data.view.name;
    });

    viewSelector.on('change', function(ids) {
        console.log(ids);
        TABLE_DATA = ids;

        var newIds = {
            query: {
                ids: ids
            }
        }

        gapi.client.analytics.data.ga.get({
            'ids': TABLE_DATA, 
            'dimensions': 'ga:year, ga:month, ga:day, ga:hour, ga:minute',
            'metrics': 'ga:pageviews,ga:sessions',
            'start-date': <?php echo "'" . $startDate . "'"; ?>,
            'end-date': <?php echo "'" . $endDate . "'"; ?>,
            filters: 'ga:medium!=referral:ga:medium!=social:ga:medium!=paid',
            'start-index' : '2',
            'max-results': '10000',
            sort:'ga:year,ga:month,ga:day,ga:hour,ga:minute'
        }).execute(function(results) {
            renderData(results.rows);
        });    
    });  
});


var creative2 = [];

function renderData(data) {

alert("The number of records Google Analytics returned was: " + data.length);

    var s = "";
    var i = 0;

    while(i < (data.length)) {

        s += "<tr>";      
        s += "<td>" + TABLE_DATA + "</td>";
        s += "<td>" + PROPERTY + "</td>";
        s += "<td>" + VIEW + "</td>";
        s += "<td>" + data[i][0] + "-" + data[i][1] + "-" + data[i][2] + "</td>"; 
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
}
</script>


<?php
    //mysqli_close($c);
    include('./footer.php');
?>