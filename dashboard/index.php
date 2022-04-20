<?php
include '../sidebar.php';
session_start();
$db = db();

// User not logged in, go to login page
if ($_SESSION["username"] == null) {
	header("Location: /login");
	die();
}

// Join event
if (isset($_POST["joinEvent"])) {
    $value = $_POST["joinEvent"];
    $stmt = $db->prepare("INSERT INTO communityAttend values (?, ?)");
    $stmt->bind_param("ss", $value, $_SESSION["username"]);
    $stmt->execute();
}
/**
 * Returns upcoming events
 * @param mysqli $db    Database connection
 * @return void
 */
function recentEvents(mysqli $db)
{
	$stmt = $db->prepare("SELECT name, type, date, location, id
                          FROM event
                          ORDER BY ts DESC
                          LIMIT 10");
	$stmt->execute();
	$data = $stmt->get_result();

	if ($data->num_rows > 0) {
		echo '<table class="table table-bordered text-center">
            <thead>
            <th scope="col">Name</th>
            <th scope="col">Type</th>
            <th scope="col">Date</th>
            <th scope="col">Where</th>
            <th scope="col">Sign Up</th>
            </thead><tbody>';
		while ($row = $data->fetch_assoc()) {
            $id = $row["id"];
			echo '<tr>';
			echo '<td>' . $row["name"] . '</td>';
			echo '<td>' . $row["type"] . '</td>';
			echo '<td>' . substr($row["date"], 0, 10);
			echo '</td>';
			echo '<td>' . $row["location"] . '</td>';
			echo '<td><form method="post">';
            echo mat_but_submit('Join Event', $id, 'joinEvent', 'event_available', '', '', false);
            echo '</form></td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}
}

function upcomingEvents($db)
{
    $stmt2 = $db->prepare("SELECT name, type, date, location, id
                          FROM event
                          where date > NOW()
                          ORDER BY date                          
                          LIMIT 10");
    $stmt2->execute();
    $data = $stmt2->get_result();

    if ($data->num_rows > 0) {
        echo '<table class="table table-bordered text-center">
            <thead>
            <th scope="col">Name</th>
            <th scope="col">Type</th>
            <th scope="col">Date</th>
            <th scope="col">Time</th>
            <th scope="col">Where</th>
            <th scope="col">Sign Up</th>
            </thead><tbody>';
        while ($row = $data->fetch_assoc()) {
            $id = $row["id"];
            echo '<tr>';
            echo '<td>' . $row["name"] . '</td>';
            echo '<td>' . $row["type"] . '</td>';
            echo '<td>' . substr($row["date"], 0, 10);
            echo '</td>';
            echo '<td>' . substr($row["date"], 11,5);
            echo '</td>';
            echo '<td>' . $row["location"] . '</td>';
            echo '<td>';
            echo mat_but_submit('Join event', $id, 'joinEvent', 'event_available', '', '', false);
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
}
?>

<html lang="en-US">
<head>
	<?php
	echo head_goodies();
	echo sideBar($_SESSION["username"]);
	?>
	<title>Dashboard &mdash; Tech Meets Tech</title>
</head>

<body>
<div id="banner" style="background-image: url('/tmt6.jpg');background-size: cover; max-height: 200px">
	<img src="/tmt_logo_white.png"
	     style="max-width: 100%; max-height: 100%; display: block; margin: auto; filter: drop-shadow(5px 5px 5px #222);"
	     alt="Tech Meets Tech logo">
</div>
<!-- Use any element to open the sidenav -->

<!-- Add all page content inside this div if you want the side nav to push page content to the right (not used if you only want the sidenav to sit on top of the page -->
<div class="container">
    <?php sideBarButton(); ?>
	<div id="recent events" style="text-align: center">
		<h3> &mdash; Latest Events &mdash; </h3>
	    <?php recentEvents($db); ?>
        <br>
        <h3> &mdash; Upcoming Events &mdash; </h3>
        <?php upcomingEvents($db); ?>
        <br><br><br><br><br><br><br><br><br>
	</div>
</div>
</body>
</html>