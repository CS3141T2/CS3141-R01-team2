<?php
include '/home/techzrla/tmt.php';
include 'utils.php';
session_start();
$db = db();
$user_info = getUserInfo($db, $_GET["username"]);
?>


<html lang="en-US">
<head>
	<?php echo head_goodies(); ?>
	<title><?php
	  if ($user_info != null) {
		  echo $user_info["name"] . " &mdash; ";
	  }
	  ?>Tech Meets Tech</title>
</head>
<div class="container rounded shadow">
	<div style="padding: 20px" class="row">
	  <?php
	  if ($user_info != null) {
		  echo sprintf('
	  %s
		<div style="margin: auto; width: 500px;" class="text-center row">
			<h1>%s</h1>
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
			  userInterestsList($db, $user_info),
			  userMembershipsList($db, $user_info));
	  } else {
		  echo "<p class=\"text-center\">That user isn't on Tech Meets Tech :(</p>";
	  }
	  ?>
	  <?php
	  // Show an edit button if we are on our own profile page.
	  if ($_GET["username"] == $_SESSION["username"]) {
		  echo '<a class="btn btn-secondary m-auto" href="edit_profile.php" style="max-width: 10em;"><i class="bi bi-pencil-fill"></i> Edit profile</a>';
	  }
	  ?>
	</div>
</div>


</html>