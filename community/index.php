<?php
include '../sidebar.php';
include 'utils.php';
session_start();
$db = db();

// User is not logged in, go to login page
if (!isset($_SESSION['username'])) {
    header("Location: /login");
    die();
}

// Redirect to search for communities page
if (isset($_POST['find'])) {
    header("Location: /community/search.php");
    die();
}

// Redirect to all communities page
if (isset($_POST['view'])) {
    header("Location: /community/communities.php");
    die();
}

// Add community event button clicked, redirect
if (isset($_POST['add'])) {
    header("Location: /events/comm_event_create.php");
    die();
}

// Wanting to view events for community
if (isset($_POST['event'])) {
    $value = trim(preg_split('/\s+/', $_POST['event'])[0]);
    header("Location: /community/events.php?comm=$value");
    die;
}

// Leave community
if (isset($_POST['leave'])) {
    $club = trim(preg_split('/\s+/', $_POST['leave'])[1]);
    $db = db();
    $stmt = $db->prepare("DELETE FROM member where account_name = ? and name = ?");
    $stmt->bind_param("ss", $_SESSION['username'], $club);
    $stmt->execute();
}

/**
 * Builds and echos table for communities the user has joined.
 *
 * @param $username string the currently logged-in user
 * @param mysqli $db connection to database
 * @return void
 */
function produceTable(mysqli $db, string $username): void
{
    // Prepare statement
    $data = $db->prepare("SELECT * FROM member NATURAL JOIN community WHERE member.account_name = ? ORDER BY name");
    $data->bind_param("s", $username);
    $data->execute();
    $member = $data->get_result();

    // Verifies results
    if ($member->num_rows > 0) {
        // Begin table
        echo "<table class='table table-bordered text-center' id='table'>
            <thead>
                <th scope='col'>Community</th>
                <th scope='col'>Leader</th>
                <th scope='col'>Events</th>
                <th scope='col'>Leave</th>
            </thead>
            <tbody>";

        // Build each row
        while ($row = $member->fetch_assoc()) {
            $club = $row["name"];
            echo sprintf('<tr><td>%s</td><td>%s</td><form method="post"><td>%s</td></form>',
                $row["name"], $row["leader"],
                mat_but_submit('', "$club Events", "event", "calendar_month", "", "$club", false)
            );
            isLeader($db, $club);
            echo "</tr>";
        }

        echo "</tbody></table>";
    } else { // Returns an alert if the user hasn't joined a community
        echo "<div class='alert alert-danger'>You have not joined communities!</div>";
    }
}

/**
 * Function to check if a user is a leader of a community. If they are the leader, echos a button to add community events.
 *
 * @param mysqli $db connection to database
 * @param string $user currently logged-in user
 * @return void
 */
function communityLeader(mysqli $db, string $user): void
{
    $stmt = $db->prepare("SELECT * FROM community WHERE leader = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo mat_but_submit('', 'Add community event', 'add', 'add', 'margin-top: 1em;', '', false);
    }
}

?>

<html lang="en-US">
<head>
    <?php
    echo head_goodies();
    echo sideBar($_SESSION['username']);
    ?>
    <title>Communities &mdash; Tech Meets Tech</title>
</head>
<body>
<div class="container">
    <?php sideBarButton(); ?>
    <div class="col text-center" style="margin: 1em">
        <h1>Communities</h1>
        <div class="col text-center">
            <form method="post">
                <br>
                <div class="col">
                    <?php
                    echo mat_but_submit('', 'Find communities', 'find', 'search', '', '', false);
                    echo mat_but_submit('', 'View all communities', 'view', 'list', '', '', false);
                    ?>
                </div>
                <!-- Add button if they are a leader -->
                <?php communityLeader($db, $_SESSION["username"]); ?>
            </form>
        </div>

        <!-- List communities for the currently logged-in user -->
        <div class="col text-center">
            <p>Listing communities for <?php echo $_SESSION["username"] ?></p>
        </div>
        <!-- Table of joined communities -->
        <div class="d-flex justify-content-center form-group" style="max-width: 50%; margin: 1em auto">
            <?php produceTable($db, $_SESSION["username"]); ?>
        </div>
    </div>
</div>
</body>
</html>