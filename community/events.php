<?php
include '../sidebar.php';
include 'utils.php';
$db = db();
session_start();

// User is not logged in, go to login page
if (!isset($_SESSION['username'])) {
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

// Joining event
if (isset($_POST["joinEvent"])) {
    header("LOCATION: /community");
    $stmt = $db->prepare("INSERT INTO communityAttend values(?,?)");
    $stmt->bind_param("ss", $_POST["joinEvent"], $_SESSION["username"]);
    $stmt->execute();
    die();
}

// Leaving event
if (isset($_POST["leaveEvent"])) {
    header("LOCATION: /community");
    $stmt = $db->prepare("DELETE FROM communityAttend where id = ? and username = ?");
    $stmt->bind_param("ss", $_POST["leaveEvent"], $_SESSION["username"]);
    $stmt->execute();
    die();
}

if (isset($_POST['delete'])) {
    header("LOCATION: /community");
    $value = $_POST['delete'];
    $db = db();
    $stmt = $db->prepare("DELETE from event where id = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    die();
}

function communityLeader(mysqli $db, string $comm): bool
{
    $stmt = $db->prepare("SELECT * FROM community WHERE leader = ? AND name = ?");
    $stmt->bind_param("ss", $_SESSION["username"], $comm);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function getAttendees(mysqli $db, string $id, bool $isLeader): void
{
    $stmt = $db->prepare("SELECT username FROM communityAttend WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $attendance = $stmt->get_result();
    if ($attendance->num_rows > 0) {
        echo "<td>
                <div class='dropdown'>
                    <button class='mdc-button mdc-button--raised dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' style='%s; color: #000000; background-color: #ffcd00;'>
                        <div class='mdc-button__ripple'></div>
					    <i class='material-icons mdc-button__icon' aria-hidden='true'>groups</i>
                        Attendees
                    </button>";
        $str = "<div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>";
        while ($row = $attendance->fetch_assoc()) {
            $name = $row["username"];
            $str .= "<a class='dropdown-item' href='/profile/index.php?username=$name'>" . $name . "</a>";
        }
        $str .= "</div>";
        echo $str;
        echo "</div></td>";
    } else {
        echo "<td><p>Nobody attended :(</p></td>";
    }

    $stmt = $db->prepare("SELECT * from communityAttend where id = ? and username = ?");
    $stmt->bind_param("ss", $id, $_SESSION["username"]);
    $stmt->execute();
    echo "<td><form method='post'>";
    if ($stmt->get_result()->num_rows > 0) {
        echo mat_but_submit('Leave', $id, 'leaveEvent', 'logout', '', '', false);
    } else {
        echo mat_but_submit('Join', $id, 'joinEvent', 'groups', '', '', false);
    }
    echo "</form></td>";

    if ($isLeader) {
        echo "<td><form method='post'>";
        echo mat_but_submit('Delete', $id, 'delete', 'delete', '', '', false);
        echo "</form></td>";
    }
}

/**
 * Function for building table for listing community events
 *
 * @param mysqli $db connection to database
 * @param string $community the name of the community
 */
function buildTable(mysqli $db, string $community): void
{
    $isLeader = communityLeader($db, $community);
    $stmt = $db->prepare("
	    SELECT * FROM event where owner_id = ?;
	    ");
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
                <th scope='col'>Attendees</th>
                <th scope='col'>Join event</th>";
        if ($isLeader) {
            echo "<th scope='col'>Delete event</th>";
        }
        echo "</thead>
            <tbody>";
        while ($row = $events->fetch_assoc()) {
            echo sprintf('
            <tr>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>', $row["name"], $row["type"], $row["date"], $row["location"], $row["description"]);
            getAttendees($db, $row["id"], $isLeader);
            echo "</tr>";
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
    <?php
    echo head_goodies();
    echo sideBar($_SESSION['username']);
    ?>
    <title>Community events</title>
</head>
<body>
<!-- Title & Main menu button -->
<div class="container">
    <?php sideBarButton(); ?>
    <div class="col text-center" style="margin: 1em">
        <h1>Community events for <?php echo $comm; ?></h1>
        <form method="post">
            <div class="col text-center">
                <?php echo mat_but_submit('', 'Main menu', 'return', 'keyboard_return', '', '', false); ?>
            </div>
        </form>
    </div>
    <!-- Creating table for community events -->
    <div class="d-flex justify-content-center form-group" style="max-width: 90%; margin: 1em auto">
        <?php buildTable($db, $comm); ?>
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
