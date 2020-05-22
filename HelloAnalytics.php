<?php

// Load the Google API PHP Client Library.
require_once __DIR__ . '/vendor/autoload.php';
session_start(); 


$client = initializeAnalytics();
$response = getReport($client);
printResults($response);


/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
function initializeAnalytics()
{

    // ********************************************************  //
    // Get these values from https://console.developers.google.com
    // Be sure to enable the Analytics API
    // ********************************************************    //
    $client_id = '748526673639-v3h4v7t1cm2ml5s0peokrlie4u261tio.apps.googleusercontent.com';
    $client_secret = '1M1R0m__g7ahtfaHwstZnDG-';
    $redirect_uri = 'http://analytics.broadwaymediagroup.com/HelloAnalytics.php';


    $client = new Google_Client();
    $client->setApplicationName("Client_Library_Examples");
    $client->setClientId($client_id);
    $client->setClientSecret($client_secret);
    $client->setRedirectUri($redirect_uri);
    $client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));
    $client->setAccessType('offline');   // Gets us our refreshtoken


    //For loging out.
    if (isset($_GET['logout']) && $_GET['logout'] == "1") {
        unset($_SESSION['token']);
    }
    

    // Step 2: The user accepted your access now you need to exchange it.
    if (isset($_GET['code'])) {
        
      $client->authenticate($_GET['code']);  
      $_SESSION['token'] = $client->getAccessToken();
      $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
      header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    }

    // Step 1:  The user has not authenticated we give them a link to login    
    if (!$client->getAccessToken() && !isset($_SESSION['token'])) {

      $authUrl = $client->createAuthUrl();

      print "<a class='login' href='$authUrl'>Connect Me!</a>";
    }    
    

    // Step 3: We have access we can now create our service
    if (isset($_SESSION['token'])) {
        print "<a class='logout' href='".$_SERVER['PHP_SELF']."?logout=1'>LogOut</a><br>";
        $client->setAccessToken($_SESSION['token']);
    }
    return $client;
}


/**
 * Queries the Analytics Reporting API V4.
 *
 * @param service An authorized Analytics Reporting API V4 service object.
 * @return The Analytics Reporting API V4 response.
 */
function getReport($client) {

    // Replace with your view ID, for example XXXX.
    $VIEW_ID = "34618177";

    $service = new Google_Service_AnalyticsReporting($client); 
    // Create the DateRange object.
    $dateRange = new Google_Service_AnalyticsReporting_DateRange();
    $dateRange->setStartDate("2018-01-01");
    $dateRange->setEndDate("2018-06-30");
    //$dateRange->setStartDate("2016-01-01");
    //$dateRange->setEndDate("2017-06-30");
    //$dateRange->setStartDate("2018-08-28");
    //$dateRange->setEndDate("2018-09-03");


    // Create the Metrics object.
    $sessions = new Google_Service_AnalyticsReporting_Metric();
    $sessions->setExpression("ga:sessions");
    $sessions->setAlias("sessions");

    $pageviews = new Google_Service_AnalyticsReporting_Metric();
    $pageviews->setExpression("ga:pageviews");
    $pageviews->setAlias("pageviews");

    //Create the Dimensions object.
    $date = new Google_Service_AnalyticsReporting_Dimension();
    $date->setName("ga:date");
        
    $year = new Google_Service_AnalyticsReporting_Dimension();
    $year->setName("ga:year");
    
    $month = new Google_Service_AnalyticsReporting_Dimension();
    $month->setName("ga:month");

    $day = new Google_Service_AnalyticsReporting_Dimension();
    $day->setName("ga:day");

    $hour = new Google_Service_AnalyticsReporting_Dimension();
    $hour->setName("ga:hour");

    $minute = new Google_Service_AnalyticsReporting_Dimension();
    $minute->setName("ga:minute");
 /*   
    $ordering = new Google_Service_AnalyticsReporting_OrderBy();
    $ordering->setFieldName("ga:date");
    $ordering->setOrderType("VALUE");   
    $ordering->setSortOrder("ASCENDING");
*/
    $ordering1 = new Google_Service_AnalyticsReporting_OrderBy();
    $ordering1->setFieldName("ga:month");
    $ordering1->setOrderType("VALUE");   
    $ordering1->setSortOrder("ASCENDING");

    $ordering2 = new Google_Service_AnalyticsReporting_OrderBy();
    $ordering2->setFieldName("ga:day");
    $ordering2->setOrderType("VALUE");   
    $ordering2->setSortOrder("ASCENDING");

    $ordering3 = new Google_Service_AnalyticsReporting_OrderBy();
    $ordering3->setFieldName("ga:hour");
    $ordering3->setOrderType("VALUE");   
    $ordering3->setSortOrder("ASCENDING");

    $ordering4 = new Google_Service_AnalyticsReporting_OrderBy();
    $ordering4->setFieldName("ga:minute");
    $ordering4->setOrderType("VALUE");   
    $ordering4->setSortOrder("ASCENDING");

    // Create the ReportRequest object.
    $request = new Google_Service_AnalyticsReporting_ReportRequest();
    $request->setViewId($VIEW_ID);
    $request->setPageSize("10000");
    $request->setDateRanges($dateRange);
    $request->setDimensions(array($date,$year,$month,$day,$hour,$minute));
    $request->setMetrics(array($sessions,$pageviews));
    //$request->setOrderBys($ordering); 

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
    $body->setReportRequests( array( $request) );
    $data =  $service->reports->batchGet( $body );

    //printResults($data);
    showData($data->reports[0]);

    //echo "we are about to do some reporting<br>";
    //echo "the next page token is: " . $data->reports[0]->nextPageToken . "<br>";

    $cnt = 0; 
    while ($data->reports[0]->nextPageToken > 0 && $cnt < 10) {
        // There are more rows for this report. we apply the next page token to the page token of the orignal body.
        $body->reportRequests[0]->setPageToken($data->reports[0]->nextPageToken);
        $data = BatchGet($service, $body);
        showData($data->reports[0]);
        $cnt++;
    }

    //return $data;
}


/**
 * Parses and prints the Analytics Reporting API V4 response.
 *
 * @param An Analytics Reporting API V4 response.
 */
function printResults($reports) {
  for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
    $report = $reports[ $reportIndex ];
    $header = $report->getColumnHeader();
    $dimensionHeaders = $header->getDimensions();
    $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
    $rows = $report->getData()->getRows();

    for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
      $row = $rows[ $rowIndex ];
      $dimensions = $row->getDimensions();
      $metrics = $row->getMetrics();
      for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
        print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
      }

      for ($j = 0; $j < count($metrics); $j++) {
        $values = $metrics[$j]->getValues();
        for ($k = 0; $k < count($values); $k++) {
          $entry = $metricHeaders[$k];
          print($entry->getName() . ": " . $values[$k] . "\n");
        }
        print("<br>");
      }
    }
  }
}

function showData($data)  {
    ?> <pre><table><?php
    ?><tr><?php // Header start row
    for($i = 0; $i < sizeof($data->columnHeader->dimensions);$i++)  {
        ?> <td> <?php print_r($data->columnHeader->dimensions[$i]); ?> </td> <?php
    }
    for($i = 0; $i < sizeof($data->columnHeader->metricHeader->metricHeaderEntries);$i++)   {
        ?> <td> <?php print_r($data->columnHeader->metricHeader->metricHeaderEntries[$i]->name); ?> </td> <?php
    }
    ?><tr><?php  // Header row end
    
    // Display data
    for($i = 0; $i < sizeof($data->data->rows);$i++)    {
        ?><tr><?php // Data row start
        // Dimensions
        for($d = 0; $d < sizeof($data->columnHeader->dimensions);$d++)  {
            ?> <td> <?php print_r($data->data->rows[$i]->dimensions[$d]); ?> </td> <?php
        }
        // Metrics
        for($m = 0; $m < sizeof($data->columnHeader->metricHeader->metricHeaderEntries);$m++)   {
            ?> <td> <?php print_r($data->data->rows[$i]->metrics[0]->values[$m]); ?> </td> <?php
        }
        ?><tr><?php  // Header row end
    }
    ?></table></pre><?php
}

function showText($data)
{
 ?> <pre> <?php print_r($data); ?> </pre> <?php
}

/**
* Returns the Analytics data. 
* Documentation https://developers.google.com/analyticsreporting/v4/reference/reports/batchGet
* Generation Note: This does not always build corectly.  Google needs to standardise things I need to figuer out which ones are wrong.
* @service Authenticated Analyticsreporting service.</param>  
* @body A valid Analyticsreporting v4 body.</param>
* @return GetReportsResponseResponse</returns>
*/
function BatchGet($service, $body)
{
  try
  {
    // Initial validation.
    if ($service == null)
      throw new Exception("service");
    if ($body == null)
      throw new Exception("body");

    // Make the request.
    return $service->reports->batchGet($body);
  }
  catch (Exception $ex)
  {
    throw new Exception("Request Reports.BatchGet failed.", $ex->getMessage());
  }
}
