<?php
session_start();
require("DBconnect.php");
date_default_timezone_set('America/Chicago');
// session_register("SESSION_PARENT");
// session_register("SESSION_CHILD");

// Locate the category the user is at
if(isset($_GET['childcat'])) {
	$stmt = $mysqli->prepare("SELECT category FROM categories where id = " . mysql_real_escape_string($_GET['childcat']));
	if(!$stmt) {
		printf("Query Prep Failed 22222: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$catNow = $row['category'];
	$stmt->close();

} elseif (isset($_GET['parentcat'])) {
	$stmt = $mysqli->prepare("SELECT category FROM categories where id = ". mysql_real_escape_string($_GET['parentcat']));
	if(!$stmt){
		printf("Query Prep Failed 22222: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$catNow = $row['category'];
	$stmt->close();
} else {
	$catNow = "Home";
}

if(isset($_GET['parentcat']) && isset($_GET['childcat'])) {
	if(is_numeric($_GET['parentcat'])) {
		$_SESSION['SESSION_PARENT'] = $_GET['parentcat'];
	}
	if(is_numeric($_GET['childcat'])) {
		$currentCat = $_GET['childcat'];
		$_SESSION['SESSION_CHILD'] = $_GET['childcat'];
	}
} else if (isset($_GET['parentcat'])) {
	if (is_numeric($_GET['parentcat'])) {

		$currentCat = $_GET['parentcat'];
		$_SESSION['SESSION_PARENT'] = $_GET['parentcat'];
		$_SESSION['SESSION_CHILD'] = 0;
		//print('in index.php session =  '.$_SESSION['SESSION_PARENT'].'<br>');
	}
} else {
	if(isset($_SESSION['SESSION_PARENT'])){unset($_SESSION['SESSION_PARENT'] );}
	if(isset($_SESSION['SESSION_CHILD'])){unset($_SESSION['SESSION_CHILD'] );}
	$currentCat = 0;
}

require("htmlFormatHeader.php");

if($currentCat == 0) {
	$sql = "SELECT * FROM stories ORDER BY dateposted DESC";
} else {
	$parentSql = "SELECT parent FROM categories WHERE id = ". mysql_real_escape_string($currentCat);
	$stmt = $mysqli->prepare($parentSql);
	if(!$stmt) {
		printf("Query Prep Failed parentSql: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$parentResult = $stmt->get_result();
	$stmt->close();
	$parentRow = $parentResult->fetch_assoc();
	if($parentRow['parent'] == 0) {
		//this is parent category, we also need to list the child category story.
		$sql = sprintf("SELECT stories.* FROM stories INNER JOIN categories ON stories.cat_id = categories.id WHERE categories.parent = %d UNION SELECT stories.* FROM stories WHERE stories.cat_id = %d;" , mysql_real_escape_string($currentCat), mysql_real_escape_string($currentCat));
		//print("currentCat = ".$currentCat);
	} else {
		$sql = "SELECT * FROM stories WHERE cat_id = " . mysql_real_escape_string($currentCat) .";";
	}
}
$stmt = $mysqli->prepare($sql);
if(!$stmt) {
	printf("Query Prep Failed 22222: %s\n", $mysqli->error);
	exit;
}
$stmt->execute();
$result = $stmt->get_result();
$numRows = $result->num_rows;
$stmt->close();

if($numRows == 0) {
	echo '<h1>No stories in '.htmlentities($catNow).' </h1>';
	echo "<p>There are no stories in this category. </p>";
} else {
	echo '<h1>Stories in '.htmlentities($catNow).' </h1>';
	while($row = $result->fetch_assoc()) {
		if(isset($_SESSION['SESSION_USERLEVEL']) && ($_SESSION['SESSION_USERLEVEL'] == 10 || $_SESSION['SESSION_USERNAME'] ==  $row['username'])) {
			//deletestory.php
            echo "<a href='deleteStory.php?id=" . htmlentities($row['id']) . "'>[X]</a> ";
        }
            //viewstory.php
        echo "<strong><a href='viewstory.php?id=" . $row['id']."'>" . htmlentities($row['subject']). "</a></strong><br />";
        echo htmlentities($row['dateposted'])."<br />";
        echo "Author: ".htmlentities($row['username'])."<br />";
        echo '<br />';
    }
}

require("htmlFormatFooter.php");
?>