<?php
include '../sidebar.php';
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

if (isset($_POST["friend"])) {
	header("Location: /profile/findFriend.php");
	die();
}

if (isset($_POST["interests"])) {
	header("Location: /Survey/surveyConfirm.php");
	die();
}

// If the username isn't specified, just look at our own page.
$visiting = $_GET["username"] ?? $_SESSION["username"];
$user_info = getUserInfo($db, $visiting);
?>

<html lang="en-US">
<head>
	<?php
	echo head_goodies();
	echo sideBar($_SESSION["username"]);
	?>
	<title><?php if ($user_info != null) {
		  echo $user_info["name"] . " &mdash; ";
	  } ?>Tech Meets Tech</title>
	<style>
      #info-row {
          margin: 1em auto;
          width: 500px;
      }

      ul {
          text-align: center;
          list-style-position: inside;
      }

      .hatch {
          background-image: linear-gradient(45deg, #ebebeb 2.38%, #e0e0e0 2.38%, #e0e0e0 50%, #ebebeb 50%, #ebebeb 52.38%, #e0e0e0 52.38%, #e0e0e0 100%);
          background-size: 29.70px 29.70px;
		      border-radius: 2em;
      }
	</style>
</head>
<body>
<div class="container rounded shadow">
	<?php sideBarButton(); ?>
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
			<div class="col text-center">
				<h4>Interests:</h4>
				<hr>
				%s
			</div>
			<div class="col text-center">
				<h4>Communities:</h4>
				<hr>
		    %s
			</div>
			<div class="col text-center">
				<h4>Friends:</h4>
				<hr>
		    %s
			</div>
		</div>',
			  defaultProfilePictureHTML($user_info),
			  $user_info["name"],
			  userTitleHTML($user_info),
			  $user_info["profile_description"],
			  userSocials($user_info),
			  userInterestsList($db, $user_info),
			  userMembershipsList($db, $user_info),
			  userFriendsList($db, $user_info)
		  );
	  } else {
		  echo '<p class="text-center">That user isn\'t on Tech Meets Tech :(</p>';
	  }

	  // Show an edit button if we are on our own profile page.
	  if ($visiting == $_SESSION["username"]) {
		  echo '<form method="post"><div class="text-center hatch" style="display: flex; flex-direction: column; padding: 1em; ">';
			echo '<div style="display: flex; justify-content: center; gap: 2em">';
		  echo mat_but_submit('', 'Edit profile', 'edit', 'manage_accounts', 'max-width: 15em;', '', false);
		  echo mat_but_submit('', 'Manage Friends', 'friend', 'group', 'max-width: 15em;', '', false);
		  echo mat_but_submit('', 'Add Interests', 'interests', 'interests', 'max-width: 15em;', '', false);
			echo '</div>';
		  echo '</div></form>';
	  }
	  ?>
	</div>
</div>
</body>
</html>