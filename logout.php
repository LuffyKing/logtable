<?php
session_start();
// remove all session variables
session_unset();

// destroy the session
session_destroy();
$host = $_SERVER['HTTP_HOST'];
header("Location: signin.php");
 ?>
