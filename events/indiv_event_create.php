<?php
include '../sidebar.php';
session_start();
$db = db();

if ($_SESSION["username"] == null) {
	header("Location: /login/index.php");
	die();
}

if ((($_POST["nameEvent"]) != "") && (($_POST["typeSelect"]) != "") && (($_POST["description"]) != "") && (($_POST["date"]) != "") && (($_POST["location"]) != "")) {
	$eventDate = strtotime(date('Y-m-d', strtotime($_POST["date"])));
	$currDate = strtotime(date('Y-m-d'));
	if ($eventDate < $currDate) {
		$_SESSION["errorOnCreate"] = "<div class='alert alert-warning' role='alert'>Date invalid!</div>";
		header("Location: /events/error.php");
		die();
	}

	$username = $_SESSION["username"];
	$getAcctName = $db->prepare("SELECT username FROM account WHERE username=?");
	$getAcctName->bind_param("s", $username);
	$getAcctName->execute();
	$result = $getAcctName->get_result();
	if ($result->num_rows < 1) {
		$_SESSION["errorOnCreate"] = "<div class='alert alert-warning' role='alert'>Must have an account to create an individual event.</div>";
		header("Location: /events/error.php");
		die();
	}

	$private = 0;
	$privateVal = $_POST["checkBox"];
	if (strcmp($privateVal, "on") == 0) {
		$private = 1;
	}

	$addEvent = $db->prepare("call addUserEvent(?, ?, ?, ?, ?, ?, ?)");
	$addEvent->bind_param("ssssssi", $username, $_POST["date"], $_POST["location"], $_POST["nameEvent"], $_POST["typeSelect"], $_POST["description"], $private);
	$addEvent->execute();

	header("Location: /events/event.php");
	die();
}
?>

<html lang="en-US">
<head>
	<?php
    echo head_goodies();
    echo sideBar($_SESSION["username"]);
    ?>
	<title>Events</title>
</head>
<?php sideBarButton(); ?>
<div class="col text-center">
	<h1>Create an Individual Event</h1>
	<form method="post" action="indiv_event_create.php">
		<label>
			Name of Event:
			<input class="form-control" type="text" name="nameEvent">
		</label>
		<label>
			Type of Event:
			<input class="form-control" type="text" name="typeSelect">
		</label>
		<label>
			Description of Event:
			<input class="form-control" type="text" name="description">
		</label>
		<label>
			Event Date:
			<input class="form-control" type="datetime-local" name="date">
		</label>
		<label>
			Location:
			<input class="form-control" type="text" name="location">
		</label>
		<label>
			<div class="form-check form-switch">
				<input class="form-check-input" type="checkbox" id="privateCheck" name="checkBox">
				<label class="form-check-label" for="privateCheck">Make this Event Private</label>
			</div>
		</label>
		<br><br>
	  <?php echo mat_but_submit('', 'Submit', '', 'check', '', '', false); ?>
	</form>
</div>
</html>