<?php
include '../sidebar.php';
session_start();
$db = db();

// User not logged in, go to login page
if ($_SESSION["username"] == null) {
	header("Location: /login");
	die();
}

function recentEvents($db)
{
	$stmt = $db->prepare("SELECT name, type, date, location, id
                          FROM event
                          ORDER BY ts DESC
                          LIMIT 15");
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
			echo '<tr>';
			echo '<td>' . $row["name"] . '</td>';
			echo '<td>' . $row["type"] . '</td>';
			echo '<td>' . substr($row["date"], 0, 10);
			echo '</td>';
			echo '<td>' . $row["location"] . '</td>';
			echo '<td>-sign up button maybe-</td>';
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
<span style="position: absolute;left: 10px;top: 5px" onclick="openNav()">
	<button type="submit" class="mt-2 mdc-button mdc-button--raised tmt-button" value="open sidebar" id="add-btn"
	        style="min-width: 0 !important; text-align: center">
	  <div class="mdc-button__ripple"></div>
	  <i class="material-icons mdc-button__icon" aria-hidden="true" style="margin: 0 !important;">menu</i>
	</button>
</span>
<!-- Add all page content inside this div if you want the side nav to push page content to the right (not used if you only want the sidenav to sit on top of the page -->
<div class="container">
	<div id="recent events" style="text-align: center">
		<h3> &mdash; Latest Events &mdash; </h3>
	  <?php recentEvents($db); ?>
	</div>
</div>
</body>
</html>