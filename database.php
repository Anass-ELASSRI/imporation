<?php
$servername = "192.168.2.40";
$database = "dolibarrdb";
$username = "igerpdev";
$password = "Hriche@ig97";

// Create connection

$db = mysqli_connect($servername, $username, $password, $database);

// Check connection

if ($db->connect_error) {
die("Connection failed: " . $db->connect_error);
}

echo 'Connected successfully';

// mysqli_close($conn);

?>