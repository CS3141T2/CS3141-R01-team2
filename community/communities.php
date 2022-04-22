<?php
include '../sidebar.php';
include 'utils.php';
session_start();
$db = db();

// User not logged in, go to login page
if (!isset($_SESSION['username'])) {
    header("Location: /login");
    die();
}

// If button is clicked, view community events
if (isset($_POST['event'])) {
    $value = trim(preg_split('/\s+/', $_POST['event'])[0]);
    header("Location: /community/events.php?comm=$value");
    die();
}

// Return to community home page
if (isset($_POST['return'])) {
    header("Location: /community/");
    die();
}

// Join/leave community handler
echo communityHandle($db);

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
        SELECT name, leader, count, t1.name AS joined 
            FROM communityInfo 
            LEFT OUTER JOIN memberJoin AS t1 USING (name)
            ORDER BY name");
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
                isLeader($db, $club);
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
    <?php
    echo head_goodies();
    echo sideBar($_SESSION['username']);
    ?>
    <title>Communities &mdash; Tech Meets Tech</title>
</head>
<body>
<div class="container">
    <?php sideBarButton(); ?>
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