<?php
include '../tmt.php';
session_start();
$db = db();

if ($_SESSION["username"] == null) {
	header("Location: /login/index.php");
	die();
}

if ((($_POST["commName"]) != "") && (($_POST["nameEvent"]) != "") && (($_POST["typeSelect"]) != "") && (($_POST["description"]) != "") && (($_POST["date"]) != "") && (($_POST["location"]) != "")) {
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
		$_SESSION["errorOnCreate"] = "<div class='alert alert-warning' role='alert'>Must have an <a href='https://dev.techmeetstech.xyz/dashboard/' class='alert-link'>account</a> to create an individual event.</div>";
		header("Location: /events/error.php");
		die();
	}

	$commName = $_POST["commName"];
	$getCommLeader = $db->prepare("SELECT leader FROM community WHERE name=?");
	$getCommLeader->bind_param("s", $commName);
	$getCommLeader->execute();
	$commResult = $getCommLeader->get_result();
	if ($commResult->num_rows < 1) {
		$_SESSION["errorOnCreate"] = "<div class='alert alert-warning' role='alert'>Community does not exist. <a href='https://dev.techmeetstech.xyz/events/event.php' class='alert-link'>Back to event page.</a></div>";
		header("Location: /events/error.php");
		die();
	}

	$commLeader = $commResult->fetch_assoc();
	if (strcmp($username, $commLeader["leader"]) != 0) {
		$_SESSION["errorOnCreate"] = "<div class='alert alert-warning' role='alert'>Must be a community leader. <a href='https://dev.techmeetstech.xyz/events/event.php' class='alert-link'>Back to event page.</a></div>";
		header("Location: /events/error.php");
		die();
	}

	$addEvent = $db->prepare("call addCommunityEvent(?, ?, ?, ?, ?, ?)");
	$addEvent->bind_param("ssssss", $_POST["commName"], $_POST["date"], $_POST["location"], $_POST["nameEvent"], $_POST["typeSelect"], $_POST["description"]);
	$addEvent->execute();
	header("Location: /events/event.php");
	die();
}
?>

<html lang="en-US">
<head>
	<?php echo head_goodies(); ?>
	<title>Events</title>
</head>
<div class="col text-center">
	<h1>Create a Community Event</h1>
	<form method="post" action="comm_event_create.php">
		<label>
			Community Name:
			<input class="form-control" type="text" name="commName">
		</label>
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
			<input class="form-control" type="date" name="date">
		</label>
		<label>
			Location:
			<input class="form-control" type="text" name="location">
		</label>
		<br><br>
	  <?php echo mat_but_submit('', 'Submit', '', 'check', '', '', false); ?>
	</form>
</div>
</html>