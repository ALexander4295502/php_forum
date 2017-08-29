<?php
require("DBconnect.php");
require("htmlFormatHeader.php");
date_default_timezone_set('America/Chicago');
$terms = explode(" ", urldecode($_GET['Searchbar']));
if($_GET['searchOption'] == 'body') {
	$searchSql = "SELECT id,subject,body,dateposted FROM stories WHERE body LIKE '%" .mysql_real_escape_string($terms[0]) ."%'";
	for($i=1; $i<count($terms); $i++) {
		$searchSql = $searchSql . "AND body LIKE '%".$terms[$i]."%'";
	}
}elseif ($_GET['searchOption'] == 'subject') {
	$searchSql = "SELECT id,subject,body,dateposted FROM stories WHERE subject LIKE '%" .mysql_real_escape_string($terms[0]) ."%'";
	for($i=1; $i<count($terms); $i++) {
		$searchSql = $searchSql . "AND subject LIKE '%".$terms[$i]."%'";
	}
}
$stmt = $mysqli->prepare($searchSql);
if(!$stmt) {
	printf("Query Prep Failed parentSql: %s\n", $mysqli->error);
	exit;
}
$stmt->execute();
$searchResult = $stmt->get_result();
$stmt->close();
$numOfResult = $searchResult->num_rows;
echo "<h1>Search Results</h1>";
echo "<p>Search for ";
foreach($terms as $key) {
    echo "<u>" . htmlentities($key) . "</u> ";
}
echo "</p>";
echo "<p>";
while($searchRow = $searchResult->fetch_assoc()) {
	echo "<h2><a href='viewstory.php?id=" . htmlentities($searchRow['id']) . "'>". $searchRow['subject'] . "</a></h2>";
	echo "Posted on " . htmlentities($searchRow['dateposted']);
	getShortDescription($searchRow['body']);
}
require("htmlFormatFooter.php");

function getShortDescription($des) {
	$final ="";
	$final = (substr($des,0, 200)."...");
	echo "<p>".htmlentities(strip_tags($final))."</p>";
}
?>