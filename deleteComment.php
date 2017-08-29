<?php
session_start();
require("DBconfig.php");
require("DBconnect.php");
if($_SESSION['SESSION_USERLEVEL'] <= 1) {
    header("Location: " . $config_maindir);
	exit;
}

if(is_numeric($_GET['id'])) {
	$validid = $_GET['id'];
}else{
	header("Location: " . $config_maindir);
	exit;
}
if($_SESSION['SESSION_USERLEVEL'] <= 10){
	$stmt = $mysqli->prepare("SELECT username from comments where id =".mysql_real_escape_string($validid));
	if(!$stmt) {
		printf("Query Prep Failed deleteComment2222: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$result = $stmt->get_result();
	$numRows = $result->num_rows;
	$row = $result->fetch_assoc();
	$stmt->close();

	if($numRows>0 && ($row['username'] == $_SESSION['SESSION_USERNAME'] || $_SESSION['SESSION_USERLEVEL'] == 10)  && isset($_GET['conf']) && $_GET['conf'] == true){
		$stmt = $mysqli->prepare("DELETE from comments where id =".mysql_real_escape_string($validid));
		if(!$stmt) {
			printf("Query Prep Failed deleteComment33333: %s\n", $mysqli->error);
			exit;
		}
		$stmt->execute();
		$stmt->close();
		header("Location: " . "viewstory.php?id=" . $validid);
		exit;
	} else {
		require("htmlFormatHeader.php");
		echo "<h2> Are you sure to delete this comment? </h2>";
		echo "<p>[<a href='deleteComment.php?conf=true&id=". htmlentities($validid)."'>Yes</a>] [<a href='index.php'>No</a>]</p>";
	}
}
?>