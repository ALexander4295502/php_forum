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
if($_SESSION['SESSION_USERLEVEL'] <= 10) {
	$stmt = $mysqli->prepare("SELECT subject,username from stories where id = ".mysql_real_escape_string($validid));
	if(!$stmt) {
		printf("Query Prep Failed deleteStory22222: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$result = $stmt->get_result();
	$numRows = $result->num_rows;
	$row = $result->fetch_assoc();
	$stmt->close();

	if($numRows>0 && ($row['username'] == $_SESSION['SESSION_USERNAME'] || $_SESSION['SESSION_USERLEVEL'] == 10 ) && isset($_GET['conf']) && $_GET['conf'] == true) {
		$stmt = $mysqli->prepare("DELETE from stories where id = ".mysql_real_escape_string($validid));
		if(!$stmt) {
			printf("Query Prep Failed deleteStory33333: %s\n", $mysqli->error);
			exit;
		}
		$stmt->execute();
		$stmt->close();
		//if(!hash_equals($_SESSION['token'], $_GET['token'])){
		//	die("Request forgery detected");
		//}
		$stmt = $mysqli->prepare("DELETE from comments where story_id = ".mysql_real_escape_string($validid));
		if(!$stmt) {
			printf("Query Prep Failed deleteComments33333: %s\n", $mysqli->error);
			exit;
		}
		$stmt->execute();
		$stmt->close();
		header("Location: " . $config_maindir);
	} else {
		$deleteName = $row['subject'];
		require("htmlFormatHeader.php");
		echo "<h2> Are you sure to delete this story ' ".htmlentities($deleteName)." ' ?</h2>";
		echo "<p>[<a href='deleteStory.php?conf=true&id=". htmlentities($validid)."'>Yes</a>] [<a href='index.php'>No</a>]</p>";
	}
}
?>