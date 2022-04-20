<?php /** @noinspection PhpUndefinedClassInspection */
session_start();
require_once '../vendor/autoload.php';
$config = parse_ini_file("/home/techzrla/creds.ini");

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($config["g_client_id"]);
$client->setClientSecret($config["g_client_secret"]);
$client->setRedirectUri($config["g_redirect_uri"]);
$client->addScope("email");
$client->addScope("profile");
$client->setHostedDomain("mtu.edu");

// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
	$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
	$client->setAccessToken($token['access_token']);

	// get profile info
	$google_oauth = new Google_Service_Oauth2($client);
	$google_account_info = $google_oauth->userinfo->get();
	$email = $google_account_info->email;
	$name = $google_account_info->name;

	// register login
	if (substr($email, -7) == "mtu.edu") {
		$username = explode("@", $email)[0];
		$_SESSION["username"] = $username;
		header("Location: /dashboard");
	} else {
		echo "quit hacking the system!!";
	}
	die();
} else {
	echo "<a href='" . $client->createAuthUrl() . "'>Google Login</a>";
}