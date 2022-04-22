<?php
include '../sidebar.php';
session_start();
$db = db();

if (!isset($_SESSION['username'])) {
	header("Location: /login");
	die();
}

if (isset($_POST["indivEvent"])) {
	header("Location: /events/indiv_event_create.php");
	die();
}

if (isset($_POST['commEvent'])) {
	header("Location: /events/comm_event_create.php");
	die();
}

if (isset($_POST["personalEvents"])) {
    header("Location: /events/personal_events.php");
    die();
}

if (isset($_POST["friendsEvents"])) {
    header("Location: /events/friends_events.php");
    die();
}

if (isset($_POST["joinEvent"])){

    $username = $_SESSION["username"];
    $eventId = $_POST["eventId"];
    $joinEvent = $db->prepare("SELECT * FROM indivAttend WHERE id=? AND account_name=?");
    $joinEvent->bind_param("is", $itemId, $username);
    $joinEvent->execute();

    $result = $joinEvent->get_result();
    $numRows = $result->num_rows;
    if ($numRows == 0){
        $actuallyJoin = $db->prepare("INSERT INTO indivAttend (id, account_name) VALUES (?, ?)");
        $actuallyJoin->bind_param("is", $eventId, $username);
        $actuallyJoin->execute();
    }

}

/**
 * Builds the HTML dropdown displaying each attendee.
 *
 * @param mysqli_result $attendList SQL query result
 * @param $table
 * @return string
 */
function getAttendance(mysqli_result $attendList, $table): string
{
	$string = "<div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>";
	if ($attendList->num_rows > 0) {
		while ($list = $attendList->fetch_assoc()) {
			if (strcmp($table, "indivAttend") == 0) {
                $accName = $list["account_name"];
				$string .= "<a class='dropdown-item' href='/profile/index.php?username=$accName'>" . $list["account_name"] . "</a>";
			} else {
				$string .= "<a class='dropdown-item' href='#'>" . $list["username"] . "</a>";
			}
		}
	}
	$string .= "</div>";
	return $string;
}

function printTable($rows, $table, $db)
{
	$index = 1;
	while ($items = $rows->fetch_assoc()) {
		$count = 0;
        $date = substr($items["date"], 0, 10);
        $time = substr($items["date"], 11, 5);
		while ($count < 10) {
			if ($count == 0) {
				echo "<tr>";
				echo "<th scope='row'>" . $index . "</th>";
				$index++;
			}
			if ($count == 1) {
				echo "<td>" . $items["owner_id"] . "</td>";
			}
			if ($count == 2) {
				echo "<td>" . $date . "</td>";
			}
            if ($count == 3) {
                echo "<td>" . $time . "</td>";
            }
			if ($count == 4) {
				echo "<td>" . htmlspecialchars($items["location"]) . "</td>";
			}
			if ($count == 5) {
				echo "<td>" . htmlspecialchars($items["name"]) . "</td>";
			}
			if ($count == 6) {
				echo "<td>" . htmlspecialchars($items["type"]) . "</td>";
			}
			if ($count == 7) {
				echo "<td>" . htmlspecialchars($items["description"]) . "</td>";
			}
            if ($count == 8){
                if (strcmp($table, "indivAttend") == 0) {
                    $itemId = $items["id"];
                    echo "<form method='post' action='/events/event.php'>
                            <input type='hidden' value=$itemId name='eventId'>
                            <td>". mat_but_submit('', 'Join', 'joinEvent', 'person', '', '', false) ."</td>
                           </form>";
                }
            }
			if ($count == 9) {
				if (strcmp($table, "communityAttend") == 0) {
					$attendListEvents = $db->prepare("SELECT username, id FROM communityAttend WHERE id=" . $items["id"]);
				} else {
					$attendListEvents = $db->prepare("SELECT account_name, id FROM indivAttend WHERE id=" . $items["id"]);
				}
				$attendListEvents->execute();
				$attendList = $attendListEvents->get_result();

                //<button class='btn btn-primary btn-sm dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
				if ($attendList->num_rows > 0) {
					echo "
                        <td>
                            <div class='dropdown'>
                            <button class='mdc-button mdc-button--raised dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' style='%s; color: #000000; background-color: #ffcd00;'>
                        <div class='mdc-button__ripple'></div>
					    <i class='material-icons mdc-button__icon' aria-hidden='true'>groups</i>
                                    Attendees
                                </button>";
					$string = getAttendance($attendList, $table);
					echo $string;
					echo "
                            </div>     
                        </td>
                    </tr>";
				} else {
					echo "<td><p style='white-space: nowrap'>Nobody attended :(</p></td></tr>";
				}
			}
			$count++;
		}
	}
}

?>

<!doctype html>
<html lang="en-US">
<head>
    <?php
    echo head_goodies();
    echo sideBar($_SESSION["username"]);
    ?>
	<title>Events</title>
</head>
<body>
<div class="container">
    <?php sideBarButton(); ?>
	<div class="col text-center" style="margin: 1em">
		<h1>Events</h1>
		<form method="post" action="event.php">
			<div class="col text-center">
		  <?php echo mat_but_submit('', 'Create Individual Event', 'indivEvent', 'person', '', '', false); ?>
		  <?php echo mat_but_submit('', 'Create Community Event', 'commEvent', 'people', '', '', false); ?>
			<br><br>
          <?php echo mat_but_submit('', 'View Personal Events', 'personalEvents', 'people', '', '', false); ?>
          <?php echo mat_but_submit('', 'View Friends Events', 'friendsEvents', 'people', '', '', false); ?>
            </div>
		</form>
		<br>
		<h2>Public Individual Events</h2>
	</div>
	<div class="d-flex justify-content-center form group" style="max-width: 100%; margin: 1em auto">
		<table class="table table-bordered text-center" id="table">
			<thead>
			<tr>
				<th scope="col">Index</th>
				<th scope="col">Creator</th>
				<th scope="col">Date</th>
                <th scope="col">Time</th>
				<th scope="col">Location</th>
				<th scope="col">Name</th>
				<th scope="col">Type</th>
				<th scope="col">Description</th>
                <th scope="col">Attend Event</th>
				<th scope="col">Attendees</th>
			</tr>
			</thead>
			<tbody>
	  <?php
	  $indivEventsList = $db->prepare("SELECT id, owner_id, date, location, name, type, description FROM indiv_event 
                                                                WHERE private=0 AND date > NOW() ORDER by date ASC");
	  $indivEventsList->execute();
	  $resultEventList = $indivEventsList->get_result();

	  $indivTable = "indivAttend";
	  if ($resultEventList->num_rows > 0) {
		  printTable($resultEventList, $indivTable, $db);
	  }
	  ?>
			</tbody>
		</table>
	</div>
	<div class="col text-center" style="margin: 1em">
		<h2>Community Events</h2>
	</div>
	<div class="d-flex justify-content-center form group" style="max-width: 100%; margin: 1em auto">
		<table class="table table-bordered text-center" id="table">
			<thead>
			<tr>
				<th scope="col">Index</th>
				<th scope="col">Community</th>
				<th scope="col">Date</th>
                <th scope="col">Time</th>
				<th scope="col">Location</th>
				<th scope="col">Name</th>
				<th scope="col">Type</th>
				<th scope="col">Description</th>
				<th scope="col">Attendees</th>
			</tr>
			</thead>
			<tbody>
	  <?php
	  $commEventsList = $db->prepare("SELECT id, owner_id, date, location, name, type, description FROM event WHERE date > NOW() ORDER by date ASC");
	  $commEventsList->execute();
	  $resultCommEventList = $commEventsList->get_result();

	  $commTable = "communityAttend";
	  if ($resultCommEventList->num_rows > 0) {
		  printTable($resultCommEventList, $commTable, $db);
	  }
	  ?>
			</tbody>
		</table>
	</div>
</div>
<!--suppress SpellCheckingInspection -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<!--suppress SpellCheckingInspection -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<!--suppress SpellCheckingInspection -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>