<?php
session_start();
require("DBconfig.php");
date_default_timezone_set('America/Chicago');

if(is_numeric($_GET['id'])) {
	$validid = $_GET['id'];
} else {
	header("Location: " . $config_maindir);
	exit;
}

require("htmlFormatHeader.php");
$stmt = $mysqli->prepare("SELECT * FROM stories WHERE id = " . mysql_real_escape_string($validid));
if(!$stmt) {
	printf("Query Prep Failed parentSql: %s\n", $mysqli->error);
	exit;
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$row = $result->fetch_assoc();
echo '<h1>Poster detail</h1>';
echo '<h3>'.htmlentities($row["subject"]).'</h3>';
echo '<p>';
echo htmlentities($row['dateposted']) .'<br>';
echo 'written by '.htmlentities($row['username']).'<br>';
echo '</p>';
echo '<p>';
echo nl2br($row['body']).'<br>';
echo '</p>';
echo '<p>';
echo "<a href='".htmlentities($row['link'])."'> "."View the news"." </a> ";
echo '</p>';

if(isset($_SESSION['SESSION_USERLEVEL']) && $_SESSION['SESSION_USERLEVEL'] > 1 && ($_SESSION['SESSION_USERNAME'] ==  $row['username'] || $_SESSION['SESSION_USERLEVEL'] == 10 )) {
	echo "<p><a href = 'viewstory.php?id=".htmlentities($validid)."&editstory=true"."'>Edit the story</a><br /></p>";
	if(isset($_GET['editstory']) && $_GET['editstory'] == 'true') {
//		echo <<< HTML
?>
			<!DOCTYPE html>
			<html lang="en">
			<form  method="post" id="editstory" action="">
				<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
				<p>
				<label>Subject: 
					<textarea rows="1" cols="90" name="editStorySubject" form="editstory" required>

<?php
		echo htmlentities($row['subject']);
?>
					</textarea>
				</label>
			</p>
			<p>
				<label>Body:
					<textarea rows="10" cols="90" name="editStoryBody" form="editstory" required>
<?php
		echo htmlentities($row['body']);
?>

					</textarea>
				</label>
			<p>
				<label> Link:
					<textarea rows="1" cols="90" name="editStoryLink" form="editstory" required>
<?php
		echo htmlentities($row['link']);
?>
					</textarea>
				</label>
			</p>
			<p>
				<label>
					<input type="submit" name="button" id="button" value="Submit">
				</label>
			</p>
		</form>
<?php
		if (isset($_POST['editStoryBody'])) {
			//echo " edit story!!\n";
			if(!hash_equals($_SESSION['token'], $_POST['token'])){
				die("Request forgery detected");
			}
			$stmt = $mysqli->prepare("UPDATE stories set dateposted = NOW(), 
				body = "."'".mysql_real_escape_string($_POST['editStoryBody']). "',"
				."subject = "."'" .mysql_real_escape_string($_POST['editStorySubject'])."',"
				."link = "."'".mysql_real_escape_string($_POST['editStoryLink'])."'"
				. "WHERE id = ". mysql_real_escape_string($row['id']));
			if(!$stmt){
				printf("Query Prep Failed_addstory: %s\n", $mysqli->error);
				exit;
			}
			$stmt->execute();
			$stmt->close();
			header('Location:'."viewstory.php?id=" . $validid);
		}
	}
}

echo '<h2>Comments</h2>';
if(isset($_SESSION['SESSION_USERLEVEL']) && $_SESSION['SESSION_USERLEVEL']>1) {
	echo "<p><a href = 'viewstory.php?id=".htmlentities($validid)."&comment=true"."'>Add a comment</a><br /></p>";
	echo "<br>";
	if(isset($_GET['comment']) && $_GET['comment'] == 'true') {
?>
		<!DOCTYPE html>
		<html>
		<form method="post" id="addcomment" action="">
			<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
			<p>
				<label>
					<textarea rows="10" cols="90" name="body" form="addcomment" required>Enter Comment here </textarea>
				</label>
			</p>
			<p>
				<label>
					<input type="submit" name="button" id="button" value="Submit">
				</label>
			</p>
		</form>
<?php
	}
	if (isset($_POST['body'])) {
		//echo " add comment!!\n";
		if(!hash_equals($_SESSION['token'], $_POST['token'])){
			die("Request forgery detected");
		}
		$sql = "INSERT INTO comments(story_id, username, dateposted, body) VALUES("
	        . $validid
	        . ", '" . mysql_real_escape_string($_SESSION['SESSION_USERNAME']). "'"
	        . ", NOW()"
	        . ", '" . mysql_real_escape_string($_POST['body'])
	        . "')";
		$stmt = $mysqli->prepare($sql);
		if(!$stmt){
			printf("Query Prep Failed_addstory: %s\n", $mysqli->error);
			exit;
		}
		$stmt->execute();
		$stmt->close();
		header('Location:'."viewstory.php?id=" . $validid);
		exit;
	}
}

$stmt = $mysqli->prepare("SELECT * FROM comments WHERE story_id = " . mysql_real_escape_string($validid));
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
while($row = $result->fetch_assoc()) {
	if(isset($_SESSION['SESSION_USERLEVEL']) && $_SESSION['SESSION_USERLEVEL'] > 1 && ($_SESSION['SESSION_USERNAME'] ==  $row['username'] || $_SESSION['SESSION_USERLEVEL'] == 10 )) {
		echo "<a href = 'viewstory.php?id=".htmlentities($validid)."&comment=true &editid=".htmlentities($row['id'])."'>Edit comment</a><br />";
		if(isset($_GET['editid']) && $_GET['editid'] == $row['id']) {
?>

			<!DOCTYPE html>
			<html>
			<form  method="post" id="addcomment">
				<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
				<p>
					<label>
						<textarea rows="10" cols="90" name="commenteditbody" form="addcomment" required>
<?php
			echo htmlentities($row['body']);
?>
						</textarea>
					</label>
				</p>
				<p>
					<label>
						<input type="submit" name="button" id="button" value="Submit">
					</label>
				</p>
			</form>
<?php
			if (isset($_POST['commenteditbody'])) {
				//echo " edit comment body !!\n";
				if(!hash_equals($_SESSION['token'], $_POST['token'])){
					die("Request forgery detected");
				}	
				$stmt = $mysqli->prepare("UPDATE comments set dateposted = NOW(), body = "."'".mysql_real_escape_string($_POST['commenteditbody']). "'WHERE id = ". mysql_real_escape_string($row['id']));
				if(!$stmt) {
					printf("Query Prep Failed_editcomment: %s\n", $mysqli->error);
					exit;
				}
				$stmt->execute();
				$stmt->close();
				header('Location:'."viewstory.php?id=" . $validid);
				exit;
			}
		}
		//deletecomment.php
		echo "<a href='deleteComment.php?id=" . htmlentities($row['id']). "'>[X]</a> ";
	}
    //viewcomment
    echo htmlentities($row['dateposted'])."<br />";
    echo "Author: ".htmlentities($row['username'])."<br />";
    echo "Comment: ".nl2br($row['body'])."<br />";
    echo '<br /><br/>';
}
require("htmlFormatFooter.php")
?>