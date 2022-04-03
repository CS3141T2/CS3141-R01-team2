<?php
include '/home/techzrla/tmt.php';
session_start();
$db = db();

// User not logged in, go to login page
if ($_SESSION["username"] == null) {
	header("Location: /login");
	die();
}

// TO DO: if button is clicked, head to events page
if (isset($_POST["event2"])) {
	header("Location: /community/");
	die();
}

// Return to community home page
if (isset($_POST["return"])) {
	header("Location: /community/");
	die();
}

/**
 * Creates a table of communities that have the same interests as the user
 * @param $db   mysqli Database connect
 * @param $username     string Current logged in user
 * @return void     Table creation
 */
function commsByInterest($db, $username)
{

	// Prepare statement
	$data = $db->prepare("
    WITH count as (
            SELECT name, count(*) as count 
            from member 
            group by name ),
        user as (
            SELECT entry_no, name 
            from member 
            where account_name=?)
        SELECT distinct community.name, community.leader, count.count, if(ifnull(user.entry_no, 'No') = 'No', 'No', 'Yes') as joined
        from community  natural join count
                        left outer join user using(name)
        where name in (select commInterests.c_name
                         from interests natural join commInterests
                         where user=?) ");
	$data->bind_param("ss", $username, $username);
	$data->execute();
	$member = $data->get_result();

	// Verifies a result
	if ($member->num_rows > 0) {
		echo "<table class='table table-bordered text-center' id='table'>
            <thead>
                <th scope='col'>Community</th>
                <th scope='col'>Leader</th>
                <th scope='col'>Member count</th>
                <th >Joined</th>
            </thead>
            <tbody>";
		while ($row = $member->fetch_assoc()) {
			$club = $row["name"];
			echo sprintf('
            <tr>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
            ', $club, $row["leader"], $row["count"]);
			if ($row["joined"] == "Yes") {
				echo "<td><form><input class='btn btn-primary' type='submit' , name='event' value='Joined' disabled></form></td>";
			} else {
				echo "<td><form method='post'><input class='btn btn-primary' type='submit', name='event2' value= 'Join $club'></form></td>";
			}
			echo "</tr>";
		}
		echo "</tbody></table>";
	}
}

?>

<html lang="en-US">
<head>
	<?php echo bootstrap(); ?>
	<title>Communities &mdash; Tech Meets Tech</title>
</head>
<body>
<div class="container">
	<div class="col text-center" style="margin: 1em">
		<h1>Communities</h1>
		<form method="post">
			<div class="col text-center">
				<input class="btn btn-primary" type="submit" , name="return" value="Return to main menu">
			</div>
		</form>
		<p>Viewing communities that share your interests</p>
	</div>
	<div class="d-flex justify-content-center form-group" style="max-width: 50%; margin: 1em auto">
	  <?php commsByInterest($db, $_SESSION["username"]); ?>
	</div>
</div>
</body>
</html>