<?php
$mysql_servername = "localhost";
$mysql_username = "root";
$mysql_password = "";
$mysql_dbname = "smcc_befs";

// Create connection
$conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
$conn2 = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}