<?php
include '../tmt.php';
session_start();
$db = db();

// User not logged in, go to login page
if ($_SESSION['username'] == null) {
	header("Location: /login");
	die();
}

// TO DO: if button is clicked, head to events page
if (isset($_POST['event2'])) {
	header("Location: /community/");
	die();
}

// Return to community home page
if (isset($_POST['return'])) {
	header("Location: /community/");
	die();
}

/**
 * Creates a table of communities that have the same interests as the user.
 *
 * @param mysqli $db Database connect
 * @param string $username Current logged in user
 * @return void
 */
function commsByInterest(mysqli $db, string $username): void
{
	// Prepare statement
	$data = $db->prepare("
	WITH count AS (SELECT name, COUNT(*) AS count FROM member GROUP BY name),
	     user AS (SELECT entry_no, name FROM member WHERE account_name = ?)
	SELECT DISTINCT community.name,
	                community.leader,
	                count.count,
	                IF(IFNULL(user.entry_no, 'No') = 'No', 'No', 'Yes') AS joined
	FROM community
	         NATURAL JOIN count
	         LEFT OUTER JOIN user USING (name)
	WHERE name IN (SELECT commInterests.c_name
	               FROM interests
	                        NATURAL JOIN commInterests
	               WHERE user = ?)
	");
	$data->bind_param("ss", $username, $username);
	$data->execute();
	$member = $data->get_result();

	// Verifies a result
	if ($member->num_rows > 0) {
		// Begin table
		echo "<table class='table table-bordered text-center' id='table'>
            <thead>
                <th scope='col'>Community</th>
                <th scope='col'>Leader</th>
                <th scope='col'>Events</th>
            </thead>
            <tbody>";

		// Build each row
		while ($row = $member->fetch_assoc()) {
			$club = $row["name"];
			echo sprintf('<tr><td>%s</td><td>%s</td><td><form method="post">%s</form></td></tr>',
				$row["name"], $row["leader"], mat_but_submit('', "$club Events", "event", "calendar_month", "", "$club", false)
			);
		}
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
	<!-- Title & Button to return to main menu -->
	<div class="col text-center" style="margin: 1em">
		<h1>Communities</h1>
		<form method="post">
			<div class="col text-center">
		  <?php echo mat_but_submit('', 'Main menu', 'return', 'keyboard_return', '', '', false); ?>
			</div>
		</form>
		<p>Viewing communities that share your interests</p>
	</div>

	<!-- Return table for listing communities by interest -->
	<div class="d-flex justify-content-center form-group" style="max-width: 50%; margin: 1em auto">
	  <?php commsByInterest($db, $_SESSION["username"]); ?>
	</div>
</div>
</body>
</html>