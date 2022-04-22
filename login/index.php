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

// Determine what the redirect should be
$preg_match = preg_match("/dev\.techmeetstech\.xyz/", $_SERVER['HTTP_HOST']);
if ($preg_match) {
	$client->setRedirectUri($config["g_redirect_uri_dev"]);
} else {
	$client->setRedirectUri($config["g_redirect_uri_prod"]);
}

$client->addScope("email");
$client->addScope("profile");
$client->setHostedDomain("mtu.edu");

if (isset($_POST["login"])) {
	header("Location: " . $client->createAuthUrl());
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