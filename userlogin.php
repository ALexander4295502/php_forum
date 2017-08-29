<?php
//We use Mylogin2Vc.php to let user login the file share site with their username
//Here we implement verify code identifying by using the CreateVccode.php
require "DBconnect.php";
require "DBconfig.php";
session_start();
$msg1 = "";
$msg2 = "";
if(isset($_SESSION['SESSION_USERNAME'])){
	header("Location: " . $config_maindir);
	exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if(isset($_POST['Submit'])){
		// Use a prepared statement
		$stmt = $mysqli->prepare("SELECT COUNT(*), username, password, level FROM users WHERE username = ?");

		// Bind the parameter
		$stmt->bind_param('s',$user);
		$user = $_POST['Username'];
		$stmt->execute();

		// Bind the results
		$stmt->bind_result($cnt, $user_name, $pwd_hash, $user_level);
		$stmt->fetch();

		$pwd_guess = $_POST['Password'];

		if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
			// Login succeeded!
			$_SESSION['SESSION_USERNAME'] = $user_name;
			$_SESSION['SESSION_USERLEVEL'] = $user_level;
			if (empty($_SESSION['token'])) {
				$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
			}
			header("Location: " . $config_maindir);
			exit;
		}else{
			// Login Failed.
			$msg1 = "Username and password don't match.";
		}
	}else if(isset($_POST['Register'])){
		$newUsername = stripslashes($_POST["NewUsername"]);
		if($newUsername == ''){
			$msg2 = "Please enter both your username and password";
		}else{

			$stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
			// Bind the parameter
			$stmt->bind_param('s',$newUsername);
			$newUsername = stripslashes($_POST["NewUsername"]);
			$stmt->execute();

			// Bind the results
			$stmt->bind_result($cnt);
			$stmt->fetch();
			$stmt->close();
			
			if($cnt > 0){
				$msg2 = "This username already exists. Please choose a different one";
			} elseif($_POST['NewPassword'] != $_POST['RetypePassword']){
				$msg2 = "The password you entered are not the same!";
			}else{

				$newUsername = stripslashes($_POST["NewUsername"]);
				$newPassword = password_hash($_POST["NewPassword"], PASSWORD_DEFAULT);
				$stmt = $mysqli->prepare("insert into users (username, password, level) values (?, ?, ?)");
				if(!$stmt){
					$msg2 = sprintf("Query Prep Failed: %s\n", $mysqli->error);
				}else{
					$userLevel = 2;
					$stmt->bind_param('ssi', $newUsername, $newPassword, $userLevel);
					$stmt->execute();
					$stmt->close();
					$msg2 = "New user created successfully!";
				}
			}
		}
	}
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Login for File Share</title>
    <link href="http://lib.sinaapp.com/js/bootstrap/latest/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="http://lib.sinaapp.com/js/bootstrap/latest/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="This is a login page for a simple file share website" />
    <meta name="keywords" content="HTML, CSS, PHP, FileShare">
    <style type="text/css">
        table {
            width: 30%
        }
    </style>
</head>

<body style='font-family: "WenQuanYi Micro Hei", "WenQuanYi Zen Hei", "Microsoft YaHei", arial, sans-serif; font-size: 16px;'>
    <section class='box well'>
        <h1>Login</h1>
        <form name="LoginCheck" action="?LoginCheck" method="post" autocomplete="off">
            <table>
                <tr>
                    <td colspan="3">
                        <?php echo htmlentities($msg1); ?>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="Username"><strong>Username:</strong>
                        </label>
                    </th>
                    <!-- Filter input the username can only contain letters and numbers!-><!-->
                    <td colspan="2">
                        <input class="inp_text" placeholder="Enter Your Username" pattern="[a-zA-Z0-9-]+" title="Username can only contain letters and numbers!" name="Username"
                        id="Username" type="text" autocomplete="new-password">
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="Password"><strong>Password:</strong>
                        </label>
                    </th>
                    <!-- Filter input the password can only contain letters and numbers!-><!-->
                    <td colspan="2">
                        <input class="inp_text" placeholder="Enter Your Password" pattern="[a-zA-Z0-9-]+" title="Password can only contain letters and numbers!" name="Password"
                        id="Password" type="password" autocomplete="new-password">
                    </td>
                </tr>

                <tr>

                    <td colspan="3" class="submit-button-right">
                        <input class="send_btn" type="submit" name="Submit" title="Submit" value="Submit">
                        <input class="send_btn" type="reset" name="Reset" title="Reset" value="Reset">
                    </td>
                </tr>
            </table>
        </form>
    </section>

    <section class='box well'>
        <h1>Register</h1>
        <form name="Register" action="?Registered" method="post" autocomplete="off">
            <table>
                <tr>
                    <td colspan="3">
                        <?php echo htmlentities($msg2); ?>
                    </td>
                </tr>

                <tr>
                    <!-- Filter input the username can only contain letters and numbers!-><!-->
                    <th>
                        <label for="NewUsername"><strong>New Username:</strong>
                        </label>
                    </th>
                    <td colspan="2">
                        <input class="inp_text" name="NewUsername" id="NewUsername" placeholder="New Username" pattern="[a-zA-Z0-9-]+" title="Username can only contain letters and numbers!"
                        type="text" autocomplete="new-password">
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="NewPassword"><strong>New Password:</strong>
                        </label>
                    </th>
                    <!-- Filter input the password can only contain letters and numbers!-><!-->
                    <td colspan="2">
                        <input class="inp_text" name="NewPassword" id="NewPassword" placeholder="New Password" pattern="[a-zA-Z0-9-]+" title="Password can only contain letters and numbers!"
                        type="password" autocomplete="new-password">
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="RetypePassword"><strong>Retype Password:</strong>
                        </label>
                    </th>
                    <!-- Filter input the password can only contain letters and numbers!-><!-->
                    <td colspan="2">
                        <input class="inp_text" name="RetypePassword" id="RetypePassword" placeholder="Retype Your Password" pattern="[a-zA-Z0-9-]+" title="Password can only contain letters and numbers!"
                        type="password">
                    </td>
                </tr>

                <tr>
                    <td colspan="3" class="submit-button-right">
                        <input class="send_btn" type="submit" name="Register" title="Register" value="Register">
                    </td>
                </tr>
            </table>
        </form>

    </section>

</body>

</html>