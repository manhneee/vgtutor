<?php
// Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

$sName = "localhost";
$uName = "root"; // Change this to your MySQL username
$pass = "";
$db_name = "vgtutor";
$port = 3306; // or 3306 if that's your MySQL port

$conn = new PDO("mysql:host=$sName;port=$port;dbname=$db_name", $uName, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Temporary output for test
?>
