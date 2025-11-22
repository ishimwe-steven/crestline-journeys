<?php
$host = "localhost";
$user = "cresijjl_crestline"; // change if needed
$pass = "Crestline@2025";     // change if you set a MySQL password
$dbname = "cresijjl_crestline"; // your database name

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>