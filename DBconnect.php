<?php
require 'DBconfig.php';
$mysqli = new mysqli($dbhost, $dbuser, $dbpassword, $dbdatabaseName);
if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>