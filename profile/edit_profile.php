<?php
include '/home/techzrla/tmt.php';
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
if (isset($_POST["major"]) && isset($_POST["year"]) && isset($_POST["bio"]) && isset($_POST["color"])) {
	$goodColor = "";
	// Strip off the pound sign if they enter it.
	if (substr($_POST["color"], 0, 1) == "#") {
		$goodColor = substr($_POST["color"], 1, 6);
	} else {
		$goodColor = $_POST["color"];
	}
	$stmt = $db->prepare("UPDATE `account` SET major=?, year=?, profile_description=?, color=? WHERE username=?");
	$stmt->bind_param("sisss", $_POST["major"], $_POST["year"], $_POST["bio"], $goodColor, $_SESSION["username"]);
	$stmt->execute();
	header("Location: /profile/full.php?username=" . $_SESSION["username"]); // Take them to their profile page
	die();
}
?>

<html>
<head>
	<?php echo head_goodies(); ?>
</head>
<div class="container">
	<h1>Edit your profile</h1>
	<form method="post">
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
		<div class="form-group mt-3">
			<input type="submit" class="btn btn-primary form-control" style="max-width: 7em;">
		</div>
	</form>
</div>
</html>

