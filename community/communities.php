<?php
include '../tmt.php';
session_start();
$db = db();

// User not logged in, go to login page
if ($_SESSION['username'] == null) {
	header("Location: /login");
	die();
}

// If button is clicked, view community events
if (isset($_POST['events'])) {
	$value = trim(preg_split('/\s+/', $_POST['events'])[0]);
	header("Location: /community/events.php?comm=$value");
	die;
}

// If button is clicked, add myself to community
if (isset($_POST['join'])) {
	header("Location: /community/");
	$value = $_POST['join'];
	$add = $db->prepare("call addToCommunity(?, ?)");
	$add->bind_param("ss", $_SESSION["username"], $value);
	$add->execute();

	$stmt = $db->prepare("SELECT * FROM member WHERE account_name = ? AND name = ?");
	$stmt->bind_param("ss", $_SESSION["username"], $value);
	$stmt->execute();
	$rows = $stmt->get_result();
	echo "<div class='col text-center' style='max-width: 25%; margin: 1em auto'>";
	if ($rows->num_rows > 0) {
		echo "<div class='alert alert-success'>You have been added to $value community</div>";
	} else {
		echo "<div class='alert alert-danger'>Unable to add you to the community</div>";
	}
	echo "</div>";
	die();
}

// Return to community home page
if (isset($_POST['return'])) {
	header("Location: /community/");
	die();
}

/**
 * Creates a table of all communities.
 *
 * @param mysqli $db connection to database
 * @param string $username username of currently logged-in user
 * @return void
 */
function allCommunities(mysqli $db, string $username): void
{
	// Prepare statement
	$data = $db->prepare("
        WITH memberCount AS (
            SELECT name, COUNT(*) AS count FROM member GROUP BY name
        ),
            communityInfo AS (
            SELECT name, leader, IFNULL(count, 0) AS count FROM community LEFT OUTER JOIN memberCount USING (name)
        ),
            memberJoin AS (
            SELECT * FROM member WHERE account_name=?
        )
        SELECT name, leader, count, IF(IFNULL(t1.entry_no, 'No') = 'No', 'No', 'Yes') AS joined 
            FROM communityInfo 
            LEFT OUTER JOIN memberJoin AS t1 USING (name)");
	$data->bind_param("s", $username);
	$data->execute();
	$member = $data->get_result();

	// Verifies a result
	if ($member->num_rows > 0) {
		echo "<table class='table table-bordered text-center' id='table'>
            <thead>
                <th scope='col'>Community</th>
                <th scope='col'>Leader</th>
                <th scope='col'>Member count</th>
                <th scope='col'>Events</th>
                <th scope='col'>Joined</th>
            </thead>
            <tbody>";
		while ($row = $member->fetch_assoc()) {
			$club = $row["name"];
			echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td>', $club, $row["leader"], $row["count"]);
			echo sprintf('<td><form method="post">%s</form></td>', mat_but_submit('', "$club Events", "event", "calendar_month", "", "$club", false));
			if ($row["joined"] == "Yes") {
				echo sprintf('<td>%s</td>', mat_but_submit('', 'Joined', 'join2', 'check', '', '', true));
			} else {
				echo sprintf('<td><form method="post">%s</form></td>', mat_but_submit("Join $club", "$club", 'join', 'login', '', "join-$club", false));
			}
			echo "</tr>";
		}
		echo "</tbody></table>";
	}
}

?>

<html lang="en-US">
<head>
	<?php echo head_goodies(); ?>
	<title>Communities &mdash; Tech Meets Tech</title>
</head>
<body>
<div class="container">
	<!-- Title & Main menu button -->
	<div class="col text-center" style="margin: 1em">
		<h1>Communities</h1>
		<form method="post">
			<div class="col text-center">
		  <?php echo mat_but_submit('', 'Main menu', 'return', 'keyboard_return', '', '', false); ?>
			</div>
		</form>
		<p>Viewing all communities</p>
	</div>
	<!-- Table for listing all communities -->
	<div class="d-flex justify-content-center form-group" style="max-width: 50%; margin: 1em auto">
	  <?php allCommunities($db, $_SESSION["username"]); ?>
	</div>
</div>
</body>
</html>