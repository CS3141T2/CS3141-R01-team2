<?php
include '../tmt.php';
include 'utils.php';
session_start();
$db = db();

// Redirect if not logged in
if (!isset($_SESSION["username"])) {
	header("Location: /login");
	die();
}

if (isset($_POST["edit"])) {
	header("Location: /profile/edit_profile.php");
	die();
}

// If the username isn't specified, just look at our own page.
$visiting = $_GET["username"] ?? $_SESSION["username"];
$user_info = getUserInfo($db, $visiting);
?>

<html lang="en-US">
<head>
	<?php echo head_goodies(); ?>
	<title><?php if ($user_info != null) {
		  echo $user_info["name"] . " &mdash; ";
	  } ?>Tech Meets Tech</title>
	<style>
      #info-row {
          margin: 1em auto; width: 500px;
      }
	</style>
</head>
<body>
<div class="container rounded shadow">
	<div style="padding: 20px" class="row">
	  <?php
	  if ($user_info != null) {
		  echo sprintf('
	  %s
		<div id="info-row" class="text-center row">
			<h1>%s</h1>
			<p>%s</p>
			<p>%s</p>
			<p>%s</p>
		</div>
		<div class="row">
			<div class="col">
				<h4>Interests:</h4>
				%s
			</div>
			<div class="col">
				<h4>Communities:</h4>
		    %s
			</div>
		</div>',
			  defaultProfilePictureHTML($user_info),
			  $user_info["name"],
			  userTitleHTML($user_info),
			  $user_info["profile_description"],
			  userSocials($user_info),
			  userInterestsList($db, $user_info),
			  userMembershipsList($db, $user_info));
	  } else {
		  echo '<p class="text-center">That user isn\'t on Tech Meets Tech :(</p>';
	  }

	  // Show an edit button if we are on our own profile page.
	  if ($visiting == $_SESSION["username"]) {
		  echo '<form method="post" style="display: flex; justify-content: center">' . mat_but_submit('', 'Edit profile', 'edit', 'edit', '', '', false) . '</form>';
	  }
	  ?>
	</div>
</div>
</body>
</html>