<?php /** @noinspection PhpUndefinedClassInspection */
include '../tmt.php';
include 'utils.php';
require_once '../vendor/autoload.php';
$config = parse_ini_file("/home/techzrla/creds.ini");
session_start();
$db = db();

if ($_SESSION["username"] != null) { // User is logged in, go to dashboard
	header("Location: /dashboard");
	die();
}

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($config["g_client_id"]);
$client->setClientSecret($config["g_client_secret"]);
$client->setRedirectUri($config["g_redirect_uri"]);
$client->addScope("email");
$client->addScope("profile");
$client->setHostedDomain("mtu.edu");

if (isset($_POST["login"])) {
	header("Location: " . $client->createAuthUrl());
}

// User has asked for code, take them to code entry page
if (isset($_SESSION["expiration"]) && time() < $_SESSION["expiration"]) {
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
		$msg = "<html lang='en-US'><body>Hi $name,<br><br>Please use this code to log into Tech Meets Tech: $code. For your account security, this code will only be valid for one hour. If you are not trying to log in, you may safely delete this email.<br><br>&mdash; Tech Meets Tech</body></html>";
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
	<?php echo head_goodies(); ?>
	<title>Login &mdash; Tech Meets Tech</title>
	<style>
      .container {
          height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
      }
	</style>
	<link href="/styles.css" rel="stylesheet" />
</head>
<div class="container">
	<div class="row" id="content-row">
		<div class="col text-center">
			<h1>Welcome to <i>Tech Meets Tech</i>!</h1>
		<?php if ($_GET["error"]) {
			echo "<div class='alert alert-danger'>" . $_GET["error"] . "</div>";
		} ?>
			<p>Sign in with your Michigan Tech credentials.</p>
			<form method="post" action="index.php">
		    <?php echo mat_but_submit('', 'Michigan Tech Login', 'login', 'login', '', '', false); ?>
			</form>
			<p style="font-size: small; font-style: italic;">
				If you are currently signed into your <code>@mtu.edu</code> via Google, you may be automatically signed in.
			</p>
		</div>
	</div>
</div>


</html>