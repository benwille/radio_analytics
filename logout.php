
<?php

session_start();

$_SESSION = array();
session_destroy();

// redirect the page to the login page
header('Location: http://analytics.broadwaymediagroup.com/login.php');
exit();    

?>
