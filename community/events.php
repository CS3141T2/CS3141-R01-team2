<?php
include '../tmt.php';
$db = db();
session_start();

// User is not logged in, go to login page
if ($_SESSION['username'] == null) {
	header("Location: /login");
	die();
}

// Return to main menu
if (isset($_POST['return'])) {
	header("Location: /community");
	die();
}

// Verify the given community is not null
$comm = $_GET['comm'];
if (is_null($comm)) {
	header("Location: /community");
	die();
}

// First test the get data is a valid community name
$stmt = $db->prepare("SELECT * FROM community WHERE name = ?");
$stmt->bind_param("s", $comm);
$stmt->execute();
if ($stmt->get_result()->num_rows == 0) {
	header("Location: /community");
	die();
}

/**
 * Function for building table for listing community events
 *
 * @param mysqli $db connection to database
 * @param string $community the name of the community
 */
function buildTable(mysqli $db, string $community): void
{
	$stmt = $db->prepare("SELECT * FROM event WHERE owner_id = ? ORDER BY date DESC");
	$stmt->bind_param("s", $community);
	$stmt->execute();
	$events = $stmt->get_result();

	// Build table if there are community events
	if ($events->num_rows > 0) {
		echo "<table class='table table-bordered text-center' id='table'>
            <thead>
                <th scope='col'>Name</th>
                <th scope='col'>Type</th>
                <th scope='col'>Date</th>
                <th scope='col'>Location</th>
                <th scope='col'>Description</th>
            </thead>
            <tbody>";
		while ($row = $events->fetch_assoc()) {
			echo sprintf('
            <tr>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
            </tr>', $row["name"], $row["type"], $row["date"], $row["location"], $row["description"]);
		}
		echo "</tbody></table>";
	} // Returns an alert if the community has zero events
	else {
		echo "<div class='alert alert-danger text-center' style='min-width: 50%; margin: 1em auto'>There are zero events!</div>";
	}
}

?>

<html lang="en-US">
<head>
	<?php echo head_goodies(); ?>
	<title>Community events</title>
</head>
<body>
<!-- Title & Main menu button -->
<div class="container">
	<div class="col text-center" style="margin: 1em">
		<h1>Community events for <?php echo $comm; ?></h1>
		<form method="post">
			<div class="col text-center">
		  <?php echo mat_but_submit('', 'Main menu', 'return', 'keyboard_return', '', '', false); ?>
			</div>
		</form>
	</div>
	<!-- Creating table for community events -->
	<div class="d-flex justify-content-center form-group" style="max-width: 50%; margin: 1em auto">
	  <?php buildTable($db, $comm); ?>
	</div>
</div>
</body>
</html>
