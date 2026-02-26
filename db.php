<?php
/* ===========================
   DATABASE CONFIGURATION
=========================== */

$host = "localhost";
$user = "root";
$pass = "";
$db   = "sdg11_db";

/* ===========================
   CONNECTION
=========================== */
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>