<?php
// Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

$sName = "localhost";
$uName = "root";
$pass = "";
$db_name = "vgtutor1";

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Temporary output for test
    echo "✅ Connected to database <strong>$db_name</strong> successfully!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
    exit;
}
?>
