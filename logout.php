<?php 

// Initalize session
session_start();

// Unset all of the session variables
$_SESSION = array();

session_destroy();

// Redirect to login page
header("location: login.php");
exit;


?>