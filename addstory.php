<?php
session_start();
require("DBconfig.php");
require('DBconnect.php');
if($_SESSION['SESSION_USERLEVEL'] < 2) {
	header('Location:' . $config_maindir);
	exit;
} else {
	if (isset($_POST['body'])) {
		
		if(!hash_equals($_SESSION['token'], $_POST['token'])){
			die("Request forgery detected");
		}
		
		//echo " add story!!\n";
		$sql = "INSERT INTO stories(cat_id, username, dateposted, subject, body,link) VALUES("
		        . $_POST['category']
		        . ", '" . $_SESSION['SESSION_USERNAME']. "'"
		        . ", NOW()"
		        . ", '" . mysql_real_escape_string($_POST['subjectfield']) . "'"
		        . ", '" . mysql_real_escape_string($_POST['body']) . "'"
		        . ", '" . mysql_real_escape_string($_POST['newslink'])
		        . "')";
		$stmt = $mysqli->prepare($sql);
		if(!$stmt) {
			printf("Query Prep Failed_addstory: %s\n", $mysqli->error);
			exit;
		}
		$stmt->execute();
		$stmt->close();
		header('Location:' . $config_maindir);
		exit;
	}
?>
	<!DOCTYPE html>
	<html lang="en">
	<form id="addstory" method="post">
		<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
	    <p>
	        <label>
	            Subject:
	            <input type="text" name="subjectfield" id="textfield" maxlength="50" required/>
	        </label>
	    </p>
	    <p>
	        <label>
	            <textarea rows="50" cols="90" name="body" form="addstory" required>Enter body here </textarea>
	        </label>
	    </p>
	    Category:
	    <select name="category">
<?php
	$stmt = $mysqli->prepare("SELECT id, category FROM categories ORDER BY category");
	if(!$stmt){
		printf("Query Prep Failed_addstory: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	$numOfResult = $result->num_rows;;
	if($numOfResult > 0) {
		while($row = $result->fetch_assoc()){
			echo "<option value=\"{$row['id']}\">" . htmlentities($row['category']) . "</option>";
		}
	}
?>
	    </select>
	    <p>
	        <label>
	            Link:
	            <input type="text" name="newslink" id="newslink">
	        </label>
	    </p>
	    <p>
	        <label>
	            <input type="submit" name="button" id="button" value="Submit">
	        </label>
	    </p>
	</form>

	</html>
<?php
}
?>