<?php

require("DBconnect.php");

echo <<< HTML
<section id="search">
    <h2>Search stories</h2>
    <form id="SearchbarForm" action="search.php" method="get">
        <table>
            <tr>
                <th>
                    <label for="Searchbar"><strong>Search:</strong>
                    </label>
                </th>
                <td colspan="2">
                    <input pattern="[a-zA-Z0-9-]+" title="Content can only contains letters and numbers!" name="Searchbar" id="Searchbar" type="text">
                </td>
            </tr>
            <tr>
                <th>Search in:</th>
                <td>
                    <select name="searchOption">
                        <option value="subject">subject</option>
                        <option value="body">body</option>
                    </select>
				</td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="Submit" title="Submit" value="Submit">
                    <input type="reset" name="Reset" title="Reset" value="Reset">
                </td>
            </tr>
        </table>
    </form>
</section>
HTML;
echo "<section id = 'login' >";
echo "<table width='100%' cellspacing=0 cellpadding=5>";
echo "<h2> Login detail </h2>";
if(isset($_SESSION['SESSION_USERNAME'])) {
	echo "Hello, <strong>" . htmlentities($_SESSION['SESSION_USERNAME'])."</strong> - <a href = 'userlogout.php'>Logout</a>";
	echo "<p>";
	if($_SESSION['SESSION_USERLEVEL'] > 1) {
		echo "<a href = 'addstory.php'>Post a new story</a><br />";
	}
	if($_SESSION['SESSION_USERLEVEL'] == 10) {
        echo "<a href='addcat.php'>Add a new category</a><br />";
    }
    echo "</p>";
}else {
	echo "<a href='userlogin.php'>Login or register here</a>";
}
echo "</td></tr>";
echo "</table>";
echo "</section>";
echo '<h2>Topics</h2>';
$stmt = $mysqli->prepare("select * from categories where parent = 0");
if(!$stmt) {
	printf("Query Prep Failed33333: %s\n", $mysqli->error);
	exit;
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$numOfResult = $result->num_rows;
if($numOfResult == 0) {
	echo "<h1>No Stories</h1>";
	echo "<p>There are no stories in this category. </p>";
}else {
	while($row = $result->fetch_assoc()) {
		// if(isset($_SESSION['SESSION_USERLEVEL']) && $_SESSION['SESSION_USERLEVEL'] == 10){
		// 	echo "<a href = 'deleteCat.php?dp=true&id=".$row['id']."'>[X]</a>";
		// }
		echo "<a href = 'index.php?parentcat=".htmlentities($row['id'])."'>".htmlentities($row['category'])."</a><br>";
		//print('in siteBar session_parent = '.$_GET['parentcat'].'<br>');
		//print("row_id = ".$row['id'].'<br>');
        //print("parent = ".$_SESSION['SESSION_PARENT']);
		if(isset($_SESSION['SESSION_PARENT']) && $row['id'] == $_SESSION['SESSION_PARENT']) {

			$stmt = $mysqli->prepare("select categories.id, categories.category from categories where categories.parent = ".mysql_real_escape_string($_SESSION['SESSION_PARENT']));
			if(!$stmt) {
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$stmt->execute();
			$childResult = $stmt->get_result();
			
			while($childRow = $childResult->fetch_assoc()) {
				if(isset($_SESSION['SESSION_USERLEVEL']) && $_SESSION['SESSION_USERLEVEL'] == 10) {
                    echo "<a href='deleteCat.php?id=" . htmlentities($childRow['id']) . "'>[X]</a> ";
                }
                echo " &bull; <a href='index.php?parentcat=" . htmlentities($row['id']). "&amp;childcat=" . htmlentities($childRow['id']) . "'>" . htmlentities($childRow['category']). "</a><br>";
			}
			$stmt->close();

		}
	}
}
?>