<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "food_ordering";

$connect = mysqli_connect($servername, $username, $password, $dbname);

if (!$connect) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
