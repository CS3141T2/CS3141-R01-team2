<?php
include '../tmt.php';
session_start();
$db = db();

// Leave the page if the user is not signed in.
if (!isset($_SESSION["username"])) {
	header("Location: /login");
	die();
}

// Load any preexisting data
$stmt = $db->prepare("SELECT * FROM account WHERE username=?");
$stmt->bind_param("s", $_SESSION["username"]);
$stmt->execute();
$result = $stmt->get_result();
$preexisting = $result->fetch_assoc();

// If POST parameters are set, assign data into database
if (
				isset($_POST["major"]) &&
				isset($_POST["year"]) &&
				isset($_POST["bio"]) &&
				isset($_POST["color"]) &&
				isset($_POST["twitter_username"]) &&
				isset($_POST["snapchat_username"]) &&
				isset($_POST["instagram_username"])
) {
	$goodColor = "";
	// Strip off the pound sign if they enter it.
	if (substr($_POST["color"], 0, 1) == "#") {
		$goodColor = substr($_POST["color"], 1, 6);
	} else {
		$goodColor = $_POST["color"];
	}
	$stmt = $db->prepare("UPDATE `account` SET major=?, year=?, profile_description=?, color=?, twitter_username=?, snapchat_username=?, instagram_username=? WHERE username=?");
	$stmt->bind_param("sissssss", $_POST["major"], $_POST["year"], $_POST["bio"], $goodColor, $_POST["twitter_username"],$_POST["snapchat_username"],$_POST["instagram_username"], $_SESSION["username"]);
	$stmt->execute();
	header("Location: /profile/index.php?username=" . $_SESSION["username"]); // Take them to their profile page
	die();
}
?>

<html lang="en-US">
<head>
	<?php echo head_goodies(); ?>
	<style>
		.submit-button {
				max-width: 10em !important;
		}
	</style>
	<title>Edit profile &mdash; Tech Meets Tech</title>
</head>
<body>
<div class="container">
	<h1>Edit your profile</h1>
	<form method="post" style="display: flex; flex-direction: column">
		<div class="form-group">
			<label>
				Major:
				<input type="text" class="form-control" name="major" value="<?php echo $preexisting["major"]; ?>"/>
			</label>
		</div>
		<div class="form-group">
			<label>
				Year:
				<input type="number" class="form-control" name="year" min="1" max="10"
				       value="<?php echo $preexisting["year"]; ?>"/>
			</label>
		</div>
		<div class="form-group">
			<label>
				Short bio:
				<textarea rows="3" cols="40" class="form-control"
				          name="bio"><?php echo $preexisting["profile_description"]; ?></textarea>
			</label>
		</div>
		<div class="form-group">
			<label>
				Color:
				<input class="form-control" name="color" type="text" maxlength="7" value="<?php echo $preexisting["color"]; ?>">
				<small>Enter a HEX color value using a <a href="https://g.co/kgs/8Q8sJc" target="_blank">color
						picker</a>.</small>
			</label>
		</div>
		<hr>
		<h3>Link your social media</h3>
		<div class="form-group">
			<label>
				Twitter:
				<div class="input-group mb-2 mr-sm-2">
					<div class="input-group-prepend">
						<div class="input-group-text">@</div>
					</div>
					<input class="form-control" name="twitter_username" type="text" maxlength="15" value="<?php echo $preexisting["twitter_username"]; ?>" placeholder="Username">
				</div>
			</label>
			<br>
			<label>
				Snapchat:
				<div class="input-group mb-2 mr-sm-2">
					<div class="input-group-prepend">
						<div class="input-group-text">@</div>
					</div>
					<input class="form-control" name="snapchat_username" type="text" maxlength="30" value="<?php echo $preexisting["snapchat_username"]; ?>" placeholder="Username">
				</div>
			</label>
			<br>
			<label>
				Instagram:
				<div class="input-group mb-2 mr-sm-2">
					<div class="input-group-prepend">
						<div class="input-group-text">@</div>
					</div>
					<input class="form-control" name="instagram_username" type="text" maxlength="30" value="<?php echo $preexisting["instagram_username"]; ?>" placeholder="Username">
				</div>
			</label>
		</div>
		<hr>
		<div class="form-group mt-3">
			<?php echo mat_but_submit('', 'Submit', '', 'check', '', '', false); ?>
		</div>
	</form>
</div>
</body>
</html>

