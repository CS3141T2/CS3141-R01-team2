<?php
include '/home/techzrla/tmt.php';
session_start();
$db = db();

/**
 * Generates a random 6-digit string.
 *
 * @return string
 */
function emailCode(): string
{
	$str = "";
	for ($i = 0; $i < 6; $i++) {
		$str .= rand(0, 9);
	}
	return $str;
}

if ($_SESSION["username"] != null) { // User is logged in, go to dashboard
	header("Location: /dashboard");
	die();
}

if (isset($_SESSION["expiration"]) && time() < $_SESSION["expiration"]) { // User has asked for code, take them to code entry page
	// Prevents people from just spamming a bunch of Tech usernames, or at least slows them down
	header("Location: /login/email_sent.php");
	die();
}

if (isset($_POST["username"])) {
	$username = $_POST["username"];

	// Verify the username is valid.
	$checkUsernameStmt = $db->prepare("SELECT * FROM Identities WHERE username=?");
	$checkUsernameStmt->bind_param("s", $username);
	$checkUsernameStmt->execute();
	$result = $checkUsernameStmt->get_result();
	$personID = $result->fetch_assoc();

	if ($result->num_rows == 1) {

		// Determine expiration time
		$expirationTimestamp = date("Y:m:d H:i:s", strtotime("+1 hour"));

		// Store login-code into DB
		$code = emailCode();
		$setCodeStmt = $db->prepare("UPDATE Identities SET login_code=?, code_expiration=? WHERE username=?");
		$setCodeStmt->bind_param("iss", $code, $expirationTimestamp, $username);
		$setCodeStmt->execute();

		// Send email to user
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "From: Tech Meets Tech <noreply@techmeetstech.xyz>" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$name = $personID["name"];
		$msg = "<html><body>Hi $name,<br><br>Please use this code to log into Tech Meets Tech: $code.
For your account security, this code will only be valid for one hour. If you are not trying to login, you may safely 
delete this email.<br><br>&mdash; Tech Meets Tech</body></html>";
		mail($username . "@mtu.edu", "Tech Meets Tech — Login Code", $msg, $headers);

		// Move to `email_sent` page
		header("Location: /login/email_sent.php");
		$_SESSION["expiration"] = time() + 3600;
		$_SESSION["attempting_username"] = $username;
		die();

	} else { // The username isn't a Michigan Tech username
		$_GET["error"] = "\"$username\" isn't a valid Michigan Tech username.";
	}
}

?>

<html lang="en-US">
<head>
	<?php echo bootstrap(); ?>
	<title>Login &mdash; Tech Meets Tech</title>
</head>
<div class="container">
	<div class="col text-center">
		<h1>Welcome to <i>Tech Meets Tech</i>! </h1>
	  <?php
	  if ($_GET["error"]) {
		  echo "<div class='alert alert-danger'>" . $_GET["error"] . "</div>";
	  }
	  ?>
		<p>To login, please enter your Michigan Tech username:</p>
		<form method="post" action="index.php">
			<label>
				Username:
				<input class="form-control" type="text" name="username">
			</label>
			<br>
			<input class="btn btn-primary" type="submit" value="Send login code" style="max-width: 14em; margin: 1em auto">
		</form>
	</div>
</div>


</html>