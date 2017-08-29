<?php
session_start();
require("DBconfig.php");
require('DBconnect.php');
if($_SESSION['SESSION_USERLEVEL'] < 10) {
	header('Location:' . $config_maindir);
	exit;
} else {
	if (isset($_POST['catfield'])) {
	    if($_POST['categoryParent'] == 'noParCat') {
			//echo " add cat!!\n";
			$sql = "INSERT INTO categories(category, parent) VALUES("
	              . ", '" . mysql_real_escape_string($_POST['catfield']) . "'"
	              . ", " . 0
	              . ")";
			$stmt = $mysqli->prepare($sql);
			if(!$stmt){
				printf("Query Prep Failed_addstory during insert cat without parent: %s\n", $mysqli->error);
				exit;
		}
		$stmt->execute();
		$stmt->close();
		header('Location:' . $config_maindir);
		exit;
		} else {
			//Create category
			$sql = "INSERT INTO categories(category, parent) VALUES("
					. "'" . mysql_real_escape_string($_POST['catfield']) . "'"
					. ", '" . mysql_real_escape_string($_POST['categoryParent'])
					. "')";
			$stmt = $mysqli->prepare($sql);
			if(!$stmt){
				printf("Query Prep Failed_addstory during insert cat with parent: %s\n", $mysqli->error);
				exit;
			}
			$stmt->execute();
			$stmt->close();
			header('Location:' . $config_maindir);
			exit;
		}
	}
	echo <<< HTML
	<!DOCTYPE html>
	<html>
	<form method="post" id="addcat" action="">
	    <p>
	        <label>
	            Category Name:
	            <input type="text" name="catfield" id="textfield" maxlength="50" required>
	        </label>
	    </p>
	
	    Select parent category:
	    <select name="categoryParent">
	        <option value="noParCat">No parent category </option>;
HTML;
	$stmt = $mysqli->prepare("SELECT id, category FROM categories WHERE parent = 0 ORDER BY category");
	if(!$stmt) {
		printf("Query Prep Failed_addstory: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	$numOfResult = $result->num_rows;;
	if($numOfResult > 0){
		while($row = $result->fetch_assoc()) {
			echo "<option value=\"{$row['id']}\">" . htmlentities($row['category']) . "</option>";
		}
	}
	echo <<< HTML
	    </select>
	    <p>
	        <label>
	            <input type="submit" name="button" id="button" value="Submit">
	        </label>
	    </p>
	</form>

	</html>
HTML;
}
?>
