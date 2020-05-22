
<?php

    include("./header.php");
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

    $debug = false;
    $user = "";
    $posted = "";
    $startDate = "7daysAgo";
    $endDate = "yesterday";
    $filename = "";
    $data = "";
    $propertyID = "";
    $startYear = "";
    $startMonth = "";
    $startDay = "";
    $endYear = "";
    $endMonth = "";
    $endDay = "";

    echo "<script>";
        echo "var pd = [];";
        echo "var propertyData = [];";
        echo "var isciList = [];";
        echo "var ci = [];";      
    echo "</script>";

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
    
    if (isset($_POST['daform']) && $_POST['daform'] === 'accountForm') {
        if (isset($_POST['startDate']) && strlen($_POST['startDate']) > 0 ) {
            $startDate = $_POST['startDate'];   
            //echo "WE are posting information about the report - start date: " . $startDate . "<br>";
        }
        if (isset($_POST['endDate']) && strlen($_POST['endDate']) > 0 ) {
            $endDate = $_POST['endDate'];   
            //echo "WE are posting information about the report - end date: " . $endDate . "<br>";
        }
        if (isset($_POST['filename']) && strlen($_POST['filename']) > 0 ) {
            $filename = $_POST['filename'];   
        }
        if (isset($_POST['site']) && strlen($_POST['site']) > 0 ) {
            $propertyID = $_POST['site'];   
        }
    }

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

    if (strlen($filename) > 0) {
        $maketemp = "
            CREATE TEMPORARY TABLE temp_table_1 (
              `id` int NOT NULL AUTO_INCREMENT,
              `Station` varchar(20),
              `Advertiser` varchar(100),
              `AirDate` varchar(20),          
              `ISCI` varchar(100),
              `AirHour` varchar(20),
              `AirMinute` varchar(20),
              `Len` varchar(15),
              PRIMARY KEY(id)
            )
          "; 
  
      $result = mysqli_query($c, $maketemp) or die ("very first select - Sql error : ".mysqli_error($c));

      $file = "./uploads/" . $filename;   
      $csv = file_get_contents($file);

      $data = explode("\r",$csv);
      $cnt = count($data);
      // echo "We had this many rows in the CSV file selected: " . $cnt . "<br>";
      for ($i = 0; $i < $cnt; $i++) 
      {
          if ($i === 1) 
          {
              $res = explode(",", $data[$i]);       
              $sd = explode("/", $res[2]);
              $sdm = $sd[0];
              if (strlen($sdm) < 2)
                $sdm = "0" . $sdm;
              $sdd = $sd[1];        
              if (strlen($sdd) < 2)
                $sdd = "0" . $sdd;
              $sdy = $sd[2];
              $startDate = "20" . $sdy . "-" . $sdm . "-" . $sdd;
              $startYear = "20" . $sdy;
              $startMonth = $sdm;
              $startDay = $sdd;       
          }
          else if ($i === ($cnt-1)) {
              $res = explode(",", $data[$i]);       
              $ed = explode("/", $res[2]);
              $edm = $ed[0];
              if (strlen($edm) < 2)
                $edm = "0" . $edm;
              $edd = $ed[1];
              if (strlen($edd) < 2)
                $edd = "0" . $edd;
              $edy = $ed[2];
              $endDate = "20" . $edy . "-" . $edm . "-" . $edd;       
              $endYear = "20" . $edy;
              $endMonth = $edm;
              $endDay = $edd;       
          }

          // don't include the header
          if ($i > 0) {
              $res = explode(",", $data[$i]);

              $dat = explode("/", $res[2]);
              $m = $dat[0];
              if (strlen($m) < 2)
                $sdm = "0" . $m;
              $d = $dat[1];       
              if (strlen($d) < 2)
                $d = "0" . $d;
              $y = $dat[2];

              $z = explode(":", $res[4]);
              $hour = $z[0];
              if (strlen($hour) < 2)
                  $hour = "0" . $hour;
              
              $min = $z[1];       
              if (strlen($min) < 2)
                  $min = "0" . $min;
              $ampm = $z[2];
              if (strlen($ampm) > 2)
                  $ampm = substr($ampm, 2);
              if ($ampm === "PM") {
                  $hour = $hour + 12;
              }

              $inserttemp = "INSERT INTO temp_table_1 (Station, Advertiser, AirDate, ISCI, AirHour, AirMinute, Len) values
                (
                    '" . trim($res[0]) ."',
                    '" . $res[1] ."', 
                    '" . $d    ."', 
                    '" . $res[3] ."', 
                    '" . $hour ."', 
                    '" . $min  ."',
                    '" . $res[5] ."'
                );
              ";
              mysqli_query($c, $inserttemp) or die ("about to insert into  Sql error : ".mysqli_error($c));
          }
      }       

    // ******************************************
    // ******************************************
/*
      echo '<br><br>';
      echo '<table border="1" width="90%" cellpadding="2" cellspacing="0" align=center>';
      echo '<tr><th colspan=8 class=normal align=center>We have the following information</th></tr>';
      echo '<tr><th>Station</th><th>Advertiser</th><th>AirDate</th><th>ISCI</th><th>AirHour</th><th>AirMinute</th><th>Length</th></tr>';
*/
      $select = "SELECT * FROM temp_table_1 order by AirDate, AirHour, AirMinute";
      $result = mysqli_query($c, $select) or die ("get the records inerted Sql error : ".mysqli_error($c));
      $rowcount = 0;
      if ($result) {
          $rowcount = mysqli_num_rows($result);
/*      
          while ($r = mysqli_fetch_assoc($result))
          {
              $s = $r['Station'];
              $a = $r['Advertiser'];
              $ad = $r['AirDate'];
              $i = $r['ISCI'];
              $ah = $r['AirHour'];
              $am = $r['AirMinute'];
              $l = $r['Len'];        
              echo '<tr><td>' . $s . '</td><td>' . $a . '</td><td>' . $ad . '</td><td>' . $i . '</td><td>' . $ah . '</td><td>' . $am . '</td><td>' . $l . '</td></tr>';
          }
          echo "</table>";
*/           
          mysqli_free_result($result);                 
      }

    // ******************************************
    // ******************************************

  }
/*
    $dateRange = " Year >= " . $startYear;
    $dateRange .= " and Year <= " . $endYear;
    $dateRange .= " and Month >= " . $startMonth;
    $dateRange .= " and Month <= " . $endMonth;
    $dateRange .= " and Day >= " . $startDay;
    $dateRange .= " and Day <= " . $endDay;
*/

    $dateRange = " date between '" . $startDate . "' and '" . $endDate . "'";

    //echo "The data range is: " . $dateRange;
    $orderby = " order by Year, Month, Day, Hour, Minute";
    $selectProperty = "SELECT * FROM property where PropertyID='" . $propertyID . "' and " . $dateRange . " " . $orderby;
//echo "SQL is: " . $selectProperty . "<br/>";
    $propertyResult = mysqli_query($c, $selectProperty) or die ("get property records - Sql error : ".mysqli_error($c));
    $rcount = 0;
    if ($propertyResult) {
        $rcount = mysqli_num_rows($propertyResult);
        if ($rcount > 0) {
            while ($pr = mysqli_fetch_assoc($propertyResult)) {
                $PropertyName = $pr['PropertyName'];
                echo "<script>";
                echo "pd = {
                  Year:'" . $pr['Year'] . "',
                  Month:'" . $pr['Month'] . "',
                  Day: '" . $pr['Day'] . "',
                  Hour: '" . $pr['Hour'] . "',
                  Minute:'" . $pr['Minute'] . "',
                  PageViews:'" . $pr['PageViews'] . "',
                  Sessions:'" . $pr['Sessions'] . "'
                };";                                    
                echo "propertyData.push(pd);";                     
                echo "</script>";                      
            }
            mysqli_free_result($propertyResult);                 
        }
        else {
            echo "We found no records for accountID: " . $accountID . "<Br>";
        }

    }

    echo "<center>";
    echo "<H1>Broadway Media Radio Analytics Reporting</H1>";
    echo "</center>";
    echo "<center><h3>Analytics Report Between " . $startDate . " and " . $endDate . "</h3></center>";
    echo "<center><h3>Reporting with data from: " . $filename . "</h3></center>";
    echo "<center><h3>Reporting for the property: " . $PropertyName . "</h3></center><br>";
    echo "<center>";
?>

<?php
    // ******************************************
    // Report 1
    // ******************************************
?>

    <hr>
    <p style="page-break-before: always">
    <center><h4>Report 1</h4></center>
    <hr>
    
    <div class="w3-row-padding w3-center">
        <div class="w3-col w3-container l4">
            <div class="w3-container w3-border w3-light-blue">
                  <p>Radio Visits</p>
            </div>
            <div class="w3-container w3-border">
                <p id="allVisits">Loading.....</p>
            </div>
        </div>

        <div class="w3-col w3-container l4">
            <div class="w3-container w3-border w3-light-blue">
              <p>Total Ad Spots</p>
            </div>
            <div class="w3-container w3-border">
              <?php 
              echo "<p id='overAllAdCount'>" . $rowcount . "</p>";
              ?>
            </div>
        </div>


        <div class="w3-col w3-container l4">
            <div class="w3-container w3-border w3-light-blue">
              <p>Visitors/Ad</p>
            </div>
            <div class="w3-container w3-border">
              <p id="allAverage">Loading....</p>
            </div>
        </div>          
    </div>
  
    <br><br><br>       

<?php
/*
    <h2>Radio Visits by Creative</h2>     
      <table id="creativeTable" class=table border="1">
            <thead>
              <tr><th>Creative</th><th>Station</th><th>Visits</th><th>Times Aired</th><th>Visits/Airing</th></tr>
            </thread>
            <tbody id="cbody">
*/
             

      $select = "SELECT DISTINCT ISCI, Station FROM temp_table_1 order by ISCI";
      $result = mysqli_query($c, $select) or die ("fetch distinct - Sql error : ".mysqli_error($c));
      $rowcount = mysqli_num_rows($result);

      if ($result && $rowcount > 0) 
      {
          while ($r2 = mysqli_fetch_assoc($result))
          {
              $select2 = "SELECT * FROM temp_table_1 where ISCI='" . $r2['ISCI'] . "' and Station='" . $r2['Station'] . "'";
              $result2 = mysqli_query($c, $select2) or die ("get ISCI list - Sql error : ".mysqli_error($c));
              $rowcount2 = mysqli_num_rows($result2);
              $r2 = mysqli_fetch_assoc($result2);
              {
                  $cr = $r2['ISCI'];
                  $st = $r2['Station'];
                  //echo "<tr>";
                  //  echo "<td>" . $cr . "</td>";
                  //  echo "<td>" . $st . "</td>";
                  //  echo "<th id='" . $cr . "-" . $st . "'></th>";
                  // echo "<th>" . $rowcount2 . "</th>";
                  //  echo "<th id='" . $cr . "-" . $st . "avg'></th>";               
                  //echo "</tr>";

                  if ($i > 0) {
                      echo "<script>";
                        echo "ci = {
                          ISCI:'" . $cr . "', 
                          Station:'" . $st . "',
                          Cnt: '0',
                          Views: '0',
                          Sessions: '0',
                          Morning: '0',
                          Midday: '0',
                          Afternoon: '0',
                          Evening: '0',
                          Overnight: '0',
                          AdCount: '" . $rowcount2 . "'
                        };";                                    
                        echo "isciList.push(ci);";                     
                      echo "</script>";                      
                  }
              }
              mysqli_free_result($result2);                 
          }
          mysqli_free_result($result);                 
      }
      
      //echo "</table>";
      //echo "</tbody>";
    //echo "</table>";
?>
    <div id="chart">
      <h2>Radio Visits by Creative</h2>
      <table class="columns">
          <tr><td><div id="creative_table_div" style="border: 1px solid #ccc"></div></td></tr>
      </table>
    </div>


<?php
    // ******************************************
    // Report 2
    // ******************************************
?>

    <hr>
    <p style="page-break-before: always">
    <center><h4>Report 2</h4></center>
    <hr>

    <div id="chart">
      <h2>Creative Traffic</h2>
      <table class="columns">
          <tr>
              <td><div id="chart_div" style="border: 1px solid #ccc"></div></td>
              <td><div id="chart_table_div" style="border: 1px solid #ccc"></div></td>
          </tr>
      </table>
    </div>

    <br /><br />

<?php
    // ******************************************
    // Report 3
    // ******************************************
?>
    <hr>
    <p style="page-break-before: always">
    <center><h4>Report 3</h4></center>
    <hr>
    <h2>Radio Schedule</h2>

    <div class="w3-row-padding w3-center">
        <div class="w3-col w3-container l4">
            <div class="w3-container w3-border w3-purple">
                  <p>Top Daypart</p>
            </div>
            <div class="w3-container w3-border">
                <p id="topDaypart">0</p>
            </div>
        </div>

        <div class="w3-col w3-container l4">
            <div class="w3-container w3-border w3-purple">
              <p>Total radio Visits</p>
            </div>
            <div class="w3-container w3-border">
              <p id="totalRadioVisits">0</p>
            </div>
        </div>


        <div class="w3-col w3-container l4">
            <div class="w3-container w3-border w3-purple">
              <p>Total Aired Spots</p>
            </div>
            <div class="w3-container w3-border">
              <p id="totalAiredSpots">0</p>
            </div>
        </div>
            
    </div>
    <br /><br />
    <div id="chart">
      <h2>Total Website Visits by day</h2>
      <table class="columns">
          <tr>
              <td><div id="report3_area_chart_div" style="border: 1px solid #ccc"></div></td>
          </tr>
          <tr>
              <td><div id="report3_line_chart_div" style="border: 1px solid #ccc"></div></td>
          </tr>
      </table>
    </div>

    <br /><br />
    
<?php
    // ******************************************
    // Report 4
    // ******************************************
?>
    <hr>
    <p style="page-break-before: always">
    <center><h4>Report 4</h4></center>
    <hr>

    <div id="chart">
      <h2>Daypart Traffic Percent</h2>
      <table class="columns">
          <tr><td><div id="daypart_pie_div" style="border: 1px solid #ccc"></div></td></tr>
          <tr><td><div id="daypart_table_div" style="border: 1px solid #ccc"></div></td></tr>
          <tr><td><div id="daypart_station_table_div" style="border: 1px solid #ccc"></div></td></tr>
      </table>
    </div>

    <br /><br />


<?php
    // ******************************************
    // Report 5
    // ******************************************
?>
    <hr>
    <p style="page-break-before: always">
    <center><h4>Report 5</h4></center>
    <hr>

    <h2>Web Analytics</h2>

    <div class="w3-row-padding w3-center">
        <div class="w3-col w3-container l4">
            <div class="w3-container w3-border w3-amber">
                  <p>Traffic From Ads</p>
            </div>
            <div class="w3-container w3-border">
                <p id="adTraffic">0</p>
            </div>
        </div>

        <div class="w3-col w3-container l4">
            <div class="w3-container w3-border w3-amber">
              <p>Total radio Visits</p>
            </div>
            <div class="w3-container w3-border">
              <p id="radioVisits">0</p>
            </div>
        </div>


        <div class="w3-col w3-container l4">
            <div class="w3-container w3-border w3-amber">
              <p>Total Website Visits</p>
            </div>
            <div class="w3-container w3-border">
              <p id="overallVisits">0</p>
            </div>
        </div>
<!--            
        <div class="w3-col w3-container l3">
            <div class="w3-container w3-border w3-amber">
              <p>New user Percent</p>
            </div>
            <div class="w3-container w3-border">
              <p id="newUsers">0</p>
            </div>
        </div>
-->        
    </div>

    <br /><br />

    <div id="chart">
      <h2>Total Website Visits by day</h2>
      <table class="columns">
          <tr>
              <td><div id="area_chart_div" style="border: 1px solid #ccc"></div></td>
          </tr>
          <tr>
              <td><div id="line_chart_div" style="border: 1px solid #ccc"></div></td>
          </tr>
      </table>
    </div>

    <br /><br />

    <div id="chart">
      <h2>Traffic lift from Radio</h2>
      <table class="columns">
          <tr>
              <td><div id="column_chart_div" style="border: 1px solid #ccc"></div></td>
          </tr>
      </table>
    </div>
    <br /><br />

<?php
    // ******************************************
    // ***** END OF REPORTS *********************
    // ******************************************

if ($debug === true) {
?>

    <hr>
    <center><h4>Reporting Data (this will be removed)</h4></center>
    <hr>
    <br><br><br>        
        <table id="dataTable" class="table" border="1">
            <thead><tr><th>Station</th><th>Advertiser</th><th>Air Day</th><th>ISCI</th><th>Air Hour</th><th>Air Minute</th><th>Year</th><th>Month</th><th>Day</th><th>Hour</th><th>Minute</th><th>Pageviews</th><th>Sessions</th></tr></thread>
            <tbody id="tbody">
            </tbody>
        </table>
    </center>

<?php

} // end debug
// ***** END OF REPORTING DATA *********************

?>

</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


<script>
    
    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages':['corechart']});
    google.charts.load('current', {'packages':['table']});


    google.charts.setOnLoadCallback(
        function() { // Anonymous function that calls drawChart1 and drawChart2
            renderData(propertyData);
        }
    );

    // Report 1 
    function drawReport1Creatives(data) {
        // Instantiate and draw our chart, passing in some options.
        var table = new google.visualization.Table(document.getElementById('creative_table_div'));
        table.draw(data, {showRowNumber: true, width: 900, height: 400});
    }


    // Report 2
    function drawChart(data) {
        // Set chart options
        var options = {'title':'Creative/Station Visits Percent',
               'width':700,
               'height':400};
        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
    function drawTable(data) {
        // Instantiate and draw our chart, passing in some options.
        var table = new google.visualization.Table(document.getElementById('chart_table_div'));
        table.draw(data, {showRowNumber: true, width: 400, height: 400});
    }

    // report 3
    function drawReport3Chart1(data) {
        var options = {
            width: 800,
            height: 400,
            title: 'Radio Visits by Hour per Day',
            hAxis: {title: 'Hour',  titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 0}
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.AreaChart(document.getElementById("report3_area_chart_div"));
        if (chart != null)
            chart.draw(data, options);
    }
    function drawReport3Chart2(data) {
        var options = {
            width: 800,
            height: 100,
            title: 'Radio Ads by Hour per Day',
            hAxis: {title: 'Hour',  titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 0}
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById("report3_line_chart_div"));
        if (chart != null)
            chart.draw(data, options);
    }

    // Report 4
    function drawReport4Chart(data) {
        // Set chart options
        var options = {'title':'Daypart Traffic Percent',
               'width':900,
               'height':400};
        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('daypart_pie_div'));
        chart.draw(data, options);
    }
    function drawReport4Dayparts(data) {
        // Instantiate and draw our chart, passing in some options.
        var table = new google.visualization.Table(document.getElementById('daypart_table_div'));
        table.draw(data, {showRowNumber: true, width: 900, height: 300});
    }
    function drawReport4Stations(data) {
        // Instantiate and draw our chart, passing in some options.
        var table = new google.visualization.Table(document.getElementById('daypart_station_table_div'));
        table.draw(data, {showRowNumber: true, width: 900, height: 400});
    }

    // Report 5
    function drawAreaChart(data) {
        var options = {
            width: 800,
            height: 400,
            title: 'Web Overall Performance',
            hAxis: {title: 'Hour',  titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 0}
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.AreaChart(document.getElementById("area_chart_div"));
        if (chart != null)
            chart.draw(data, options);
    }
    function drawLineChart(data) {
        var options = {
            width: 800,
            height: 100,
            title: 'Radio Performance',
            hAxis: {title: 'Hour',  titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 0}
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById("line_chart_div"));
        if (chart != null)
            chart.draw(data, options);
    }
    function drawColumn(data) {
        var options = {
            width: 700,
            height: 400,
            legend: { position: 'top', maxLines: 3 },
            bar: { groupWidth: '75%' },
            isStacked: true,
        };      

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.ColumnChart(document.getElementById("column_chart_div"));
        if (chart != null)
            chart.draw(data, options);
    }

    // *****************
    // done with reports
    // *****************

var creative2 = [];
var pageViews = 0;
var sessions = 0;

var overallViews = 0;
var overallSessions = 0;

function renderData(data) {

  pageViews = 0;
  sessions = 0;
  creative2 = [];
  var webHourlyViews =   [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
  var radioHourlyViews = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
  var hourlyAds =        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];

  var tbody = document.getElementById("tbody");
  var cbody = document.getElementById("cbod");
  var topBody = document.getElementById("topbody");
  
  var top = "";
  var center = "";
  var s = "";

  var i = 0;
  var x = 0;
  var airDate;
  var airHour;
  var airMinute;


  var ad = "";
  var station = "";
  var stationVisits = 0;
  var adCount = 0;
  
  var lastMinute;

  var stat = [];
  var includeThisOne = false;

  for( var t = 0, len = isciList.length; t < len; t++ ) {
      isciList[t].Views = 0;
      isciList[t].Sessions = 0;
      isciList[t].Morning = 0;
      isciList[t].Midday = 0;
      isciList[t].Afternoon = 0;
      isciList[t].Evening = 0;
      isciList[t].Overnight = 0;
  }

<?php
    $select3 = "SELECT * FROM temp_table_1 order by AirDate, AirHour, AirMinute";
    $result3 = mysqli_query($c, $select3) or die ("Sql error : ".mysqli_error($c));
    $rowcount3 = mysqli_num_rows($result3);

    if ($result3) {
        while ($r = mysqli_fetch_assoc($result3))
        {
            $s = $r['Station'];
            $a = $r['Advertiser'];
            $ad = $r['AirDate'];
            $isci = $r['ISCI'];
            $ah = $r['AirHour'];
            $am = $r['AirMinute'];
            $l = $r['Len'];        

            echo "stat = {
              Station:'"   . $s   ."', 
              Advertiser:'". $a   ."', 
              AirDate:'"   . $ad  ."', 
              ISCI:'"      . $isci."', 
              AirHour:'"   . $ah  ."', 
              AirMinute:'" . $am  ."', 
              Len:'"       . $l   ."'
            };";              
            echo "creative2.push(stat);";
            $indx = $ah - 1;
            echo "hourlyAds[" . $indx . "] += 1;";
        }
        mysqli_free_result($result3);                 
    }
?>
  lastMinute = null;
  lastHour = null;
  lastDay = null;

  expiresMinutes = 8;
  expires = null;

  while(i < data.length) {
      s += "<tr>";      
      if (creative2[x] != null) {
        airDate = creative2[x].AirDate;
        airHour = creative2[x].AirHour;
        airMinute = creative2[x].AirMinute;
      }
      // data array
      // 0 = year
      // 1 = month
      // 2 = Day
      // 3 = Hour
      // 4 = Minute
      // 5 = page views
      // 6 = session
      googleYear = parseInt(data[i].Year);
      googleMonth  = parseInt(data[i].Month);
      googleDay = parseInt(data[i].Day);
      googleHour = parseInt(data[i].Hour);
      googleMinute = parseInt(data[i].Minute);

      airDate = parseInt(airDate);
      airHour = parseInt(airHour);
      airMinute = parseInt(airMinute);

      if (creative2[x] != null && (googleDay >= airDate)) {
          if (googleHour >= airHour) {
              if (googleMinute >= airMinute) {
                  s += "<td style='color:red'>" + creative2[x].Station    + "</td>";
                  s += "<td style='color:red'>" + creative2[x].Advertiser + "</td>";
                  s += "<td style='color:red'>" + creative2[x].AirDate    + "</td>";
                  s += "<td style='color:red'>" + creative2[x].ISCI       + "</td>";
                  s += "<td style='color:red'>" + creative2[x].AirHour    + "</td>";
                  s += "<td style='color:red'>" + creative2[x].AirMinute  + "</td>";
                  includeThisOne = true;              
                  lastMinute = parseInt(creative2[x].AirMinute);
                  lastHour = parseInt(creative2[x].AirHour);
                  lastDay = parseInt(creative2[x].AirDate);
                  expires = parseInt(lastMinute) + parseInt(expiresMinutes);
                  if (expires > 59)
                    expires = expires - 60;
                  
                  var index = 0;
                  for( var t = 0, len = isciList.length; t < len; t++ ) {
                      if( isciList[t].ISCI === creative2[x].ISCI ) {
                          index = t;
                          break;
                      }
                  }
                  var newOne = parseInt(isciList[index].Cnt) + 1;
                  isciList[index].Cnt = newOne;                    
              }    
              else if (lastMinute != null && expires == null && airMinute >= 52 && googleHour > airHour) {                                        
                  s += "<td style='color:blue'>" + creative2[x].Station    + "</td>";
                  s += "<td style='color:blue'>" + creative2[x].Advertiser + "</td>";
                  s += "<td style='color:blue'>" + creative2[x].AirDate    + "</td>";
                  s += "<td style='color:blue'>" + creative2[x].ISCI       + "</td>";
                  s += "<td style='color:blue'>" + creative2[x].AirHour    + "</td>";
                  s += "<td style='color:blue'>" + creative2[x].AirMinute  + "</td>";
                  includeThisOne = true;              
                  lastMinute = parseInt(creative2[x].AirMinute);
                  lastHour = parseInt(creative2[x].AirHour);
                  lastDay = parseInt(creative2[x].AirDate);
                  expires = parseInt(lastMinute) + parseInt(expiresMinutes);
                  if (expires > 59)
                    expires = expires - 60;              
                  
                  var index = 0;
                  for( var t = 0, len = isciList.length; t < len; t++ ) {
                      if( isciList[t].ISCI === creative2[x].ISCI ) {
                          index = t;
                          break;
                      }
                  }
                  var newOne = parseInt(isciList[index].Cnt) + 1;
                  isciList[index].Cnt = newOne;
              }
              else if (lastMinute != null && expires == null && airMinute >= 52 && googleDay > lastDay) {
                  s += "<td style='color:green'>" + creative2[x].Station    + "</td>";
                  s += "<td style='color:green'>" + creative2[x].Advertiser + "</td>";
                  s += "<td style='color:green'>" + creative2[x].AirDate    + "</td>";
                  s += "<td style='color:green'>" + creative2[x].ISCI       + "</td>";
                  s += "<td style='color:green'>" + creative2[x].AirHour    + "</td>";
                  s += "<td style='color:green'>" + creative2[x].AirMinute  + "</td>";
                  includeThisOne = true;              
                  lastMinute = parseInt(creative2[x].AirMinute);
                  lastHour = parseInt(creative2[x].AirHour);
                  lastDay = parseInt(creative2[x].AirDate);
                  expires = parseInt(lastMinute) + parseInt(expiresMinutes);
                  if (expires > 59)
                    expires = expires - 60;              
                  var index = 0;
                  for( var t = 0, len = isciList.length; t < len; t++ ) {
                      if( isciList[t].ISCI === creative2[x].ISCI ) {
                          index = t;
                          break;
                      }
                  }
                  var newOne = parseInt(isciList[index].Cnt) + 1;
                  isciList[index].Cnt = newOne;
              }
              else if ((expires != null && googleMinute <= expires) || (expires != null && lastHour !=null && googleHour > lastHour && googleMinute <= expires)) {
                  s += "<td style='color:red'> googleMinute: " + googleMinute + " </td>";
                  s += "<td style='color:red'> expires: " + expires + " </td>";
                  s += "<td style='color:red'> - </td>";
                  s += "<td style='color:red'>  1 - include this one  </td>";
                  s += "<td style='color:red'> - </td>";
                  s += "<td style='color:red'> - </td>";    
                  includeThisOne = true;
              }
              else {
                  s += "<td> - </td>";
                  s += "<td> - </td>";
                  s += "<td> - </td>";
                  s += "<td> - </td>";
                  s += "<td> - </td>";
                  s += "<td> - </td>";
                  expires = null;
              }
          }        
          else if (expires != null && googleMinute <= expires && lastHour !=null && lastHour == googleHour) {
              s += "<td style='color:red'> - </td>";
              s += "<td style='color:red'> - </td>";
              s += "<td style='color:red'> - </td>";
              s += "<td style='color:red'>  2 - include this one  </td>";
              s += "<td style='color:red'> - </td>";
              s += "<td style='color:red'> - </td>";                  
              includeThisOne = true;              
          }  
          else {
              if (expires != null && expires < 8 && googleMinute <= expires ) {
                  s += "<td style='color:red'> - </td>";
                  s += "<td style='color:red'> - </td>";
                  s += "<td style='color:red'> - </td>";
                  s += "<td style='color:red'>3 - include this one  </td>";
                  s += "<td style='color:red'> - </td>";
                  s += "<td style='color:red'> - </td>";                  
                  includeThisOne = true;              
                } else {
                    s += "<td> - </td>";
                    s += "<td> - </td>";
                    s += "<td> - </td>";
                    s += "<td> - </td>";
                    s += "<td> - </td>";
                    s += "<td> - </td>";
                    expires = null;
                }
            }
        }        
        else if (expires != null && googleMinute <= expires) {
            s += "<td style='color:red'> - </td>";
            s += "<td style='color:red'> - </td>";
            s += "<td style='color:red'> - </td>";
            s += "<td style='color:red'> 4 - include this one </td>";
            s += "<td style='color:red'> - </td>";
            s += "<td style='color:red'> - </td>";                  
            includeThisOne = true;              
        }
        else {
            s += "<td> - </td>";
            s += "<td> - </td>";
            s += "<td> - </td>";
            s += "<td> - </td>";
            s += "<td> - </td>";
            s += "<td> - </td>";
            expires = null;
        }

        if (includeThisOne) {
            s += "<td style='color:red'>" + data[i].Year + "</td>";
            s += "<td style='color:red'>" + data[i].Month + "</td>";
            s += "<td style='color:red'>" + data[i].Day + "</td>";
            s += "<td style='color:red'>" + data[i].Hour + "</td>";
            s += "<td style='color:red'>" + data[i].Minute + "</td>";
            s += "<td style='color:red'>" + data[i].PageViews + "</td>";
            s += "<td style='color:red'>" + data[i].Sessions + "</td></tr>";

            // data array
            // 0 = year
            // 1 = month
            // 2 = Day
            // 3 = Hour
            // 4 = Minute
            // 5 = page views
            // 6 = session

            pageViews += parseInt(data[i].PageViews);
            sessions  += parseInt(data[i].Sessions);
            overallViews += parseInt(data[i].PageViews);
            overallSessions += parseInt(data[i].Sessions);
            radioHourlyViews[ parseInt(data[i].Hour) ] += parseInt(data[i].PageViews);
            webHourlyViews[ parseInt(data[i].Hour) ] += parseInt(data[i].PageViews);

            if (creative2[x] != null && typeof creative2[x] != 'undefined' && typeof creative2[x].ISCI != 'undefined') {
                var index = 0;
                for( var t = 0, len = isciList.length; t < len; t++ ) {
                    if( isciList[t].ISCI === creative2[x].ISCI && isciList[t].Station === creative2[x].Station) {
                        index = t;
                        break;
                    }
                }
                var newOne = parseInt(isciList[index].Views) + parseInt(data[i].PageViews);
                isciList[index].Views = newOne;
                var newTwo = parseInt(isciList[index].Sessions) + parseInt(data[i].Sessions);
                isciList[index].Sessions = newTwo;

                var index = 0;
                for( var t = 0, len = isciList.length; t < len; t++ ) {
                    if( isciList[t].ISCI === creative2[x].ISCI && isciList[t].Station === creative2[x].Station) {
                        index = t;
                        break;
                    }
                }                    
            } 

            /*
             * morning: 6am -10am
             * midday: 10am - 3pm (15)
             * afternoon: 3pm-7pm (15 - 19)
             * evening: 7pm - midnight (19 - 23)
             * overnight: midnight - 6am    
            */
            if (lastHour >= 6 && lastHour < 10) { // morning
                isciList[index].Morning += parseInt(data[i].PageViews);
            }
            else if (lastHour >= 10 && lastHour < 15) { // midday
                isciList[index].Midday += parseInt(data[i].PageViews);
            }
            else if (lastHour >= 15 && lastHour < 19) { // afternoon
                isciList[index].Afternoon += parseInt(data[i].PageViews);
            }
            else if (lastHour >= 19 && lastHour <= 23) { // evening
                isciList[index].Evening += parseInt(data[i].PageViews);
            }
            else {  // overnight
                isciList[index].Overnight += parseInt(data[i].PageViews);
            }

        }      
        else {
            s += "<td>" + data[i].Year + "</td>";
            s += "<td>" + data[i].Month + "</td>";
            s += "<td>" + data[i].Day + "</td>";
            s += "<td>" + data[i].Hour + "</td>";
            s += "<td>" + data[i].Minute + "</td>";
            s += "<td>" + data[i].PageViews + "</td>";
            s += "<td>" + data[i].Sessions + "</td></tr>";
            overallViews += parseInt(data[i].PageViews);
            overallSessions += parseInt(data[i].Sessions);
            webHourlyViews[ parseInt(data[i].Hour) ] += parseInt(data[i].PageViews);            
        }

        if (typeof creative2[x] != 'undefinded' && (googleDay == airDate)) {
            if (googleHour == airHour) {
                if (googleMinute == airMinute) {                                        
                    i++;
                    x++;
                }
                else if (googleMinute > airMinute) {
                    i++;
                    x++;
                }
                else {
                    i++;
                }
            }
            else if (googleHour > airHour) {
                i++;
                x++;
            }
            else {
                i++;
            }
        }
        else if (typeof creative2[x] != 'undefinded' && (googleDay < airDate)) {
            i++;
        }
        else {
            i++;
            x++;
        }

        includeThisOne = false;      
    }    

    // ********************
    // Report 1
    // ********************
    for( var t = 0, len = isciList.length; t < len; t++ ) {
        var lStr = isciList[t].ISCI + "-" + isciList[t].Station;
        var vis = document.getElementById(lStr);
        if (vis != null) {
            var myViews = parseInt(isciList[t].Views);
            vis.innerHTML = myViews;
        }

        var aStr = isciList[t].ISCI + "-" + isciList[t].Station + "avg";
        var avg = document.getElementById(aStr);
        if (avg != null) {
            var thisCnt = parseInt(isciList[t].Cnt);
            var thisViews = parseInt(isciList[t].Views);
            var adCount = parseInt(isciList[t].AdCount);
            if (thisViews <= 0 || adCount <= 0)
                avg.innerHTML = 0;
            else
            {
                var value = thisViews/adCount;            
                value = Math.ceil(value * 100) / 100;        
                avg.innerHTML = value; //thisViews/adCount;

            }
        }
    }
    
    var cr = document.getElementById('allVisits');
    if (cr != null)
      cr.innerHTML = pageViews;
    var oac = document.getElementById("overAllAdCount").innerHTML;
    var aa = document.getElementById('allAverage');
    if (aa != null)
    {
        var value = pageViews / parseInt(oac);
        value = Math.ceil(value * 100) / 100;
        aa.innerHTML = value;
    }

<?php    
if ($debug === true) { 
?>
    tbody.innerHTML = s;
<?php
}
?>
    // Create the data table.    
    dataReport1 = new google.visualization.DataTable();
    dataReport1.addColumn('string', 'Creative');
    dataReport1.addColumn('string', 'Radio Station');
    dataReport1.addColumn('number', 'Visits');
    dataReport1.addColumn('number', 'Times Aired');
    dataReport1.addColumn('number', 'Visits/Airing');
    
    for( var t = 0, len = isciList.length; t < len; t++ ) {

        var thisCnt = parseInt(isciList[t].Cnt);
        var thisViews = parseInt(isciList[t].Views);
        var adCount = parseInt(isciList[t].AdCount);

        var value = thisViews/adCount;            
        value = Math.ceil(value * 100) / 100;        

        dataReport1.addRow([isciList[t].ISCI, isciList[t].Station, thisViews, adCount, value]);
    }    
    drawReport1Creatives(dataReport1);


    // ********************
    // **  Report 2  ******
    // ********************
    var data2 = new google.visualization.DataTable();
    data2.addColumn('string', 'Creative');
    data2.addColumn('number', 'Visits');

    for( var t = 0, len = isciList.length; t < len; t++ ) {

        var thisCnt = parseInt(isciList[t].Cnt);
        var thisViews = parseInt(isciList[t].Views);
        var adCount = parseInt(isciList[t].AdCount);

        var value = thisViews/adCount;            
        value = Math.ceil(value * 100) / 100;        

        data2.addRow([isciList[t].ISCI + "-" + isciList[t].Station, thisViews]);

    }
    drawChart(data2)

    // Create the data table.    
    var data3 = new google.visualization.DataTable();
    data3.addColumn('string', 'Creative');
    data3.addColumn('string', 'Station');
    data3.addColumn('number', 'Visits');
    
    for( var t = 0, len = isciList.length; t < len; t++ ) {

        var thisCnt = parseInt(isciList[t].Cnt);
        var thisViews = parseInt(isciList[t].Views);
        var adCount = parseInt(isciList[t].AdCount);

        var value = thisViews/adCount;            
        value = Math.ceil(value * 100) / 100;        

        data3.addRow([isciList[t].ISCI, isciList[t].Station, thisViews]);
    }    
    drawTable(data3);

    // ********************
    // **  Report 4  ******
    // ********************
    /*
     * morning: 6am -10am  (4 hours)
     * midday: 10am - 3pm (15) (5 hours)
     * afternoon: 3pm-7pm (15 - 19)  (4 hours)
     * evening: 7pm - midnight (19 - 23) (5 hours)
     * overnight: midnight - 6am   (6 hours)
    */    
    var dataReport4 = new google.visualization.DataTable();
    dataReport4.addColumn('string', 'Daypart');
    dataReport4.addColumn('number', 'Visits');

    var morning 
        = radioHourlyViews[6]   // '6 AM'
        + radioHourlyViews[7]   // '7 AM'
        + radioHourlyViews[8]   // '8 AM'
        + radioHourlyViews[9];  // '9 AM'
    var midday
        = radioHourlyViews[10]  // '10 AM'
        + radioHourlyViews[11]  // '11 PM'
        + radioHourlyViews[12]  // '12 PM'
        + radioHourlyViews[13]  // '1 PM'
        + radioHourlyViews[14]; // '2 PM'
    var afternoon 
        = radioHourlyViews[15]  // '3 PM'
        + radioHourlyViews[16]  // '4 PM'
        + radioHourlyViews[17]  // '5 PM'
        + radioHourlyViews[18]; // '6 PM'
    var evening 
        = radioHourlyViews[19]  // '7 PM'
        + radioHourlyViews[20]  // '8 PM'
        + radioHourlyViews[21]  // '9 PM'
        + radioHourlyViews[22]  // '10 PM'
        + radioHourlyViews[23];  // '11 PM'
    var overnight 
        = radioHourlyViews[0]  // 'Midnight 12 AM'
        + radioHourlyViews[1]  // '1 AM'
        + radioHourlyViews[2]  // '2 AM'
        + radioHourlyViews[3]  // '3 AM'
        + radioHourlyViews[4]  // '4 AM'
        + radioHourlyViews[5]; // '5 AM'

    dataReport4.addRow(["Morning", morning]);
    dataReport4.addRow(["Midday", midday]);
    dataReport4.addRow(["Afternoon", afternoon]);
    dataReport4.addRow(["Evening", evening]);
    dataReport4.addRow(["Overnight", overnight]);
    drawReport4Chart(dataReport4);

    var adsMorning 
        = hourlyAds[6]   // '6 AM'
        + hourlyAds[7]   // '7 AM'
        + hourlyAds[8]   // '8 AM'
        + hourlyAds[9];  // '9 AM'
    var adsMidday
        = hourlyAds[10]   // '10 AM'
        + hourlyAds[11]  // '11 AM'
        + hourlyAds[12]  // '12 PM'
        + hourlyAds[13]  // '1 PM'
        + hourlyAds[14]; // '2 PM'
    var adsAfternoon 
        = hourlyAds[15]  // '3 PM'
        + hourlyAds[16]  // '4 PM'
        + hourlyAds[17]  // '5 PM'
        + hourlyAds[18]; // '6 PM'
    var adsEvening 
        = hourlyAds[19]  // '7 PM'
        + hourlyAds[20]  // '8 PM'
        + hourlyAds[21]  // '9 PM'
        + hourlyAds[22]  // '10 PM'
        + hourlyAds[23]  // '11 PM'
    var adsOvernight 
        = hourlyAds[0]  // '12 AM'
        + hourlyAds[1]  // '1 AM'
        + hourlyAds[2]  // '2 AM'
        + hourlyAds[3]  // '3 AM'
        + hourlyAds[4]; // '4 AM'
        + hourlyAds[5]; // '5 AM'

    // Create the data table.    
    dataReport4 = new google.visualization.DataTable();
    dataReport4.addColumn('string', 'Dayparts');
    dataReport4.addColumn('number', 'Ads');
    dataReport4.addColumn('number', 'Visits');
    dataReport4.addColumn('number', 'Visits/Airing');
    
    vaMorning = 0
    if (morning > 0 || adsMorning > 0)
        vaMorning = morning/adsMorning;
    
    vaMidday = 0
    if (midday > 0 || adsMidday > 0)
        vaMidday = midday/adsMidday;

    vaAfternoon = 0;
    if (afternoon > 0 || adsAfternoon > 0)
        vaAfternoon = afternoon/adsAfternoon;

    vaEvening = 0;
    if (evening > 0 || adsEvening > 0)
        vaEvening = evening/adsEvening;

    vaOvernight = 0;
    if (overnight > 0 || adsOvernight > 0)
        vaOvernight = overnight/adsOvernight;

    dataReport4.addRow(["Morning", adsMorning, morning, vaMorning]);
    dataReport4.addRow(["Midday", adsMidday, midday, vaMidday]);
    dataReport4.addRow(["Afternoon", adsAfternoon, afternoon, vaAfternoon]);
    dataReport4.addRow(["Evening", adsEvening, evening, vaEvening]);
    dataReport4.addRow(["Overnight", adsOvernight, overnight, vaOvernight]);
    drawReport4Dayparts(dataReport4);

    // Create the data table.    
    dataReport4 = new google.visualization.DataTable();
    dataReport4.addColumn('string', 'Radio Station');
    dataReport4.addColumn('string', 'Creative');

    dataReport4.addColumn('number', 'Morning');
    dataReport4.addColumn('number', 'Midday');
    dataReport4.addColumn('number', 'Afternoon');
    dataReport4.addColumn('number', 'Evening');
    dataReport4.addColumn('number', 'Overnight');

    dataReport4.addColumn('number', 'Ads');
    dataReport4.addColumn('number', 'Visits');
    dataReport4.addColumn('number', 'Visits/Airing');
    
    for( var t = 0, len = isciList.length; t < len; t++ ) {

        var thisCnt = parseInt(isciList[t].Cnt);
        var thisViews = parseInt(isciList[t].Views);
        var adCount = parseInt(isciList[t].AdCount);

        var value = thisViews/adCount;            
        value = Math.ceil(value * 100) / 100;        

        dataReport4.addRow([isciList[t].Station, isciList[t].ISCI, 
              parseInt(isciList[t].Morning), parseInt(isciList[t].Midday), 
              parseInt(isciList[t].Afternoon), parseInt(isciList[t].Evening), 
              parseInt(isciList[t].Overnight), 
              adCount, thisViews, value]);
    }    
    drawReport4Stations(dataReport4);

    // ********************
    // ***  Report 5  *****
    // ********************
    var data4 = google.visualization.arrayToDataTable([
        ['Visits', 'Web'],
        ['1 AM',  webHourlyViews[0]],
        ['2 AM',  webHourlyViews[1]],
        ['3 AM',  webHourlyViews[2]],
        ['4 AM',  webHourlyViews[3]],
        ['5 AM',  webHourlyViews[4]],
        ['6 AM',  webHourlyViews[5]],
        ['7 AM',  webHourlyViews[6]],
        ['8 AM',  webHourlyViews[7]],
        ['9 AM',  webHourlyViews[8]],
        ['10 AM', webHourlyViews[9]],
        ['11 AM', webHourlyViews[10]],
        ['12 PM', webHourlyViews[11]],
        ['1 PM',  webHourlyViews[12]],
        ['2 PM',  webHourlyViews[13]],
        ['3 PM',  webHourlyViews[14]],
        ['4 PM',  webHourlyViews[15]],
        ['5 PM',  webHourlyViews[16]],
        ['6 PM',  webHourlyViews[17]],
        ['7 PM',  webHourlyViews[18]],
        ['8 PM',  webHourlyViews[19]],
        ['9 PM',  webHourlyViews[20]],
        ['10 PM', webHourlyViews[21]],
        ['11 PM', webHourlyViews[22]],
        ['12 AM', webHourlyViews[23]]
    ]);
    drawAreaChart(data4);

    var data5 = google.visualization.arrayToDataTable([
      ['Hour', 'Radio'],
      ['1 AM',  radioHourlyViews[0]],
      ['2 AM',  radioHourlyViews[1]],
      ['3 AM',  radioHourlyViews[2]],
      ['4 AM',  radioHourlyViews[3]],
      ['5 AM',  radioHourlyViews[4]],
      ['6 AM',  radioHourlyViews[5]],
      ['7 AM',  radioHourlyViews[6]],
      ['8 AM',  radioHourlyViews[7]],
      ['9 AM',  radioHourlyViews[8]],
      ['10 AM', radioHourlyViews[9]],
      ['11 AM', radioHourlyViews[10]],
      ['12 PM', radioHourlyViews[11]],
      ['1 PM',  radioHourlyViews[12]],
      ['2 PM',  radioHourlyViews[13]],
      ['3 PM',  radioHourlyViews[14]],
      ['4 PM',  radioHourlyViews[15]],
      ['5 PM',  radioHourlyViews[16]],
      ['6 PM',  radioHourlyViews[17]],
      ['7 PM',  radioHourlyViews[18]],
      ['8 PM',  radioHourlyViews[19]],
      ['9 PM',  radioHourlyViews[20]],
      ['10 PM', radioHourlyViews[21]],
      ['11 PM', radioHourlyViews[22]],
      ['12 AM', radioHourlyViews[23]]      
    ]);
    drawLineChart(data5);

    var data6 = google.visualization.arrayToDataTable([
    ['Genre', 'Web', 'Radio', 'Mystery/Crime',
            'Western', { role: 'annotation' } ],
        ['Visits', overallViews - pageViews, pageViews, 29, 30, '']
    ]);

    var view = new google.visualization.DataView(data6);
    view.setColumns([0, 1,
                     { calc: "stringify",
                       sourceColumn: 1,
                       type: "string",
                       role: "annotation" },
                     2]);

    drawColumn(view);

    var at = document.getElementById('adTraffic');
    if (at != null)
        var value = pageViews/overallViews;
        value = value * 100;
        value = Math.ceil(value * 100) / 100;
        at.innerHTML = value + "%";

    var rv = document.getElementById('radioVisits');
    if (rv != null)
        rv.innerHTML = pageViews;

    var ov = document.getElementById("overallVisits");
    if (ov != null)
      ov.innerHTML = overallViews;

    var nu = document.getElementById('newUsers');
    if (nu != null)
    {
        var value = pageViews / parseInt(oac);
        value = Math.ceil(value * 100) / 100;
        //nu.innerHTML = value;
        nu.innerHTML = "???";
    }

    // ******************
    // ***  Report 3  ***
    /*
     * morning: 6am -10am
     * midday: 10am - 3pm
     * afternoon: 3pm-7pm
     * evening: 7pm - midnight
     * overnight: midnight - 6am    
    */
    // ******************
    var data7 = google.visualization.arrayToDataTable([
        ['Visits', 'Radio'],
        ['12 AM', radioHourlyViews[0]],
        ['1 AM',  radioHourlyViews[1]],
        ['2 AM',  radioHourlyViews[2]],
        ['3 AM',  radioHourlyViews[3]],
        ['4 AM',  radioHourlyViews[4]],
        ['5 AM',  radioHourlyViews[5]],
        ['6 AM',  radioHourlyViews[6]],
        ['7 AM',  radioHourlyViews[7]],
        ['8 AM',  radioHourlyViews[8]],
        ['9 AM',  radioHourlyViews[9]],
        ['10 AM', radioHourlyViews[10]],
        ['11 PM', radioHourlyViews[11]],
        ['12 PM', radioHourlyViews[12]],
        ['1 PM',  radioHourlyViews[13]],
        ['2 PM',  radioHourlyViews[14]],
        ['3 PM',  radioHourlyViews[15]],
        ['4 PM',  radioHourlyViews[16]],
        ['5 PM',  radioHourlyViews[17]],
        ['6 PM',  radioHourlyViews[18]],
        ['7 PM',  radioHourlyViews[19]],
        ['8 PM',  radioHourlyViews[20]],
        ['9 PM', radioHourlyViews[21]],
        ['10 PM', radioHourlyViews[22]],
        ['11 AM', radioHourlyViews[23]]      
    ]);
    drawReport3Chart1(data7);


    var data8 = google.visualization.arrayToDataTable([
        ['Hour', 'Ads'],
        ['12 AM', hourlyAds[0]],
        ['1 AM',  hourlyAds[1]],
        ['2 AM',  hourlyAds[2]],
        ['3 AM',  hourlyAds[3]],
        ['4 AM',  hourlyAds[4]],
        ['5 AM',  hourlyAds[5]],
        ['6 AM',  hourlyAds[6]],
        ['7 AM',  hourlyAds[7]],
        ['8 AM',  hourlyAds[8]],
        ['9 AM',  hourlyAds[9]],
        ['10 AM', hourlyAds[10]],
        ['11 PM', hourlyAds[11]],
        ['12 PM', hourlyAds[12]],
        ['1 PM',  hourlyAds[13]],
        ['2 PM',  hourlyAds[14]],
        ['3 PM',  hourlyAds[15]],
        ['4 PM',  hourlyAds[16]],
        ['5 PM',  hourlyAds[17]],
        ['6 PM',  hourlyAds[18]],
        ['7 PM',  hourlyAds[19]],
        ['8 PM',  hourlyAds[20]],
        ['9 PM',  hourlyAds[21]],
        ['10 PM', hourlyAds[22]],
        ['11 AM', hourlyAds[23]]      
    ]);
    drawReport3Chart2(data8);

    var topDay = morning;
    var daypartTotal = morning + midday + afternoon + evening + overnight;
    var topDayName = "Morning";

    if (morning < midday) {
        topDay = midday;
        topDayName = "Midday";
    }
    if (topDay < afternoon) {
        topDay = afternoon;
        topDayName = "Afternoon";
    }
    if (topDay < evening) {
        topDay = evening;
        topDayName = "Evening";
    }
    if (topDay < overnight) {
        topDay = overnight;
        topDayName = "Overnight";
    }

    var value = topDay / daypartTotal * 100;
    value = Math.ceil(value * 100) / 100;

    var at = document.getElementById('topDaypart');
    if (at != null) {
        at.innerHTML = topDay + " (" + value + "%) " + topDayName; 
    }

    var rv = document.getElementById('totalRadioVisits');
    if (rv != null)
        rv.innerHTML = pageViews;

    var ov = document.getElementById("totalAiredSpots");
    if (ov != null)
      ov.innerHTML = oac;    
}
        
</script>

<?php
    mysqli_close($c);
    include('./footer.php');
?>