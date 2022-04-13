<?php
include '../tmt.php';
include 'utils.php';
$db = db();
session_start();

$bad_code = false;
$expired_code = false;

if ($_SESSION["username"] != null) { // User is logged in, go to dashboard
	header("Location: /dashboard");
	die();
}

if (isset($_POST["code"]) && isset($_SESSION["attempting_username"])) {
	// Verify the given code is valid
	$verifyCodeStmt = $db->prepare("SELECT * FROM Identities WHERE username=? AND login_code=?");
	$verifyCodeStmt->bind_param("si", $_SESSION["attempting_username"], $_POST["code"]);
	$verifyCodeStmt->execute();
	$result = $verifyCodeStmt->get_result();
	$assoc = $result->fetch_assoc();

	if ($result->num_rows == 0) { // Code is invalid
		$bad_code = true;
	} else if (strtotime($assoc["code_expiration"]) < time()) { // Code has expired
		$expired_code = true;
	} else { // Code is good :)
		$_SESSION["username"] = $_SESSION["attempting_username"];
		header("Location: /dashboard");
		die();
	}
}
?>

<html lang="en-US">
<head>
	<?php echo head_goodies(); ?>
	<title>Verify Login &mdash; Tech Meets Tech</title>
	<style>
      .container {
          height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
      }
	</style>
</head>
<body>
<div class="container">
	<div class="col text-center">
		<h1>Check your email.</h1>
	  <?php if ($bad_code) echo "<div class='alert alert-danger'>The proivded code is invalid.</div>"; ?>
	  <?php if ($expired_code) echo "<div class='alert alert-danger'>The proivded code has expired. Return to the <a href='index.php'>login page</a>.</div>"; ?>
		<p>A unique code was sent to your email. Enter the code to login:</p>
		<form method="post">
			<label>
				<input class="form-control" type="text" name="code" placeholder="Login code">
			</label>
			<?php echo mat_but_submit('', 'Log in', 'login', 'login', '', '', false); ?>
		</form>
	</div>
</div>
</body>
</html>