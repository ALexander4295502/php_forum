<?php
session_start();
require("DBconfig.php");
require("DBconnect.php");
if($_SESSION['SESSION_USERLEVEL'] < 10) {
    header("Location: " . $config_maindir);
	exit;
}

if(is_numeric($_GET['id'])){
	$validid = $_GET['id'];
} else {
	header("Location: " . $config_maindir);
	exit;
}

$stmt = $mysqli->prepare("SELECT category from categories where id =".mysql_real_escape_string($validid));
if(!$stmt){
	printf("Query Prep Failed delete22222: %s\n", $mysqli->error);
	exit;
}
$stmt->execute();
$result = $stmt->get_result();
$numRows = $result->num_rows;
$row = $result->fetch_assoc();
$stmt->close();

if($numRows>0 && isset($_GET['conf']) && $_GET['conf'] == true){

	$stmt = $mysqli->prepare("DELETE from categories where id =".mysql_real_escape_string($validid));
	if(!$stmt){
		printf("Query Prep Failed deleteStory33333: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$stmt->close();
	
	$stmt = $mysqli->prepare("DELETE stories, comments FROM stories JOIN comments ON (comments.story_id=stories.id) WHERE stories.cat_id =".mysql_real_escape_string($validid));
	if(!$stmt){
		printf("Query Prep Failed deleteStory33333: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$stmt->close();

	header("Location: " . $config_maindir);
} else {
	$deleteName = $row['category'];
	require("htmlFormatHeader.php");
	echo "<h2> Are you sure to delete this cat ' " . htmlentities($deleteName) . " ' ?</h2>";
	echo "<p>[<a href='deleteCat.php?conf=true&id=". htmlentities($validid)."'>Yes</a>] [<a href='index.php'>No</a>]</p>";
}
?>