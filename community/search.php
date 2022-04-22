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

// Redirects to community home page
if (isset($_POST['return'])) {
    header("Location: /community/");
    die();
}

// Redirects to community home page
if (isset($_POST['find'])) {
    header("Location: /community/commByInterest.php");
    die();
}

// Wanting to view events for community
if (isset($_POST['event'])) {
    $value = trim(preg_split('/\s+/', $_POST['event'])[0]);
    header("Location: /community/events.php?comm=$value");
    die;
}

// Join/leave community handler
echo communityHandle($db);

/**
 * A function for building the table for search results. Echos table of search results, otherwise posts alert.
 *
 * @param mysqli $db connection to database
 * @return void
 */

function buildTable(mysqli $db): void
{
    // Only search if a value was entered in the search bar
    if (isset($_POST['search'])) {
        $value = $_POST['community'];
        // Only build table if value isn't ""
        if ($value != "") {
            // Prepare statement
            $stmt = $db->prepare("
			    WITH memberCount AS (
                    SELECT name, COUNT(*) AS count FROM member where name = ?
                ),
                communityInfo AS (
                    SELECT name, leader, IFNULL(count, 0) AS count FROM community NATURAL JOIN memberCount
                ),
                memberJoin AS (
                    SELECT * FROM member WHERE account_name= ?
                )
                SELECT name, leader, count, t1.name AS joined
                    FROM communityInfo
                    LEFT OUTER JOIN memberJoin AS t1 USING (name)
                    ORDER BY name;");
            $stmt->bind_param("ss", $value, $_SESSION["username"]);
            $stmt->execute();
            $rows = $stmt->get_result();
            // verify results
            if ($rows->num_rows > 0) {
                echo "<table class='table table-bordered text-center' id='table'>
                        <thead>
                            <th scope='col'>Community</th>
                            <th scope='col'>Leader</th>
                            <th scope='col'>Member count</th>
                            <th scope='col'>Events</th>
                            <th scope='col'>Joined</th>
                        </thead>
                      <tbody>";
                // Iterate through all rows
                while ($row = $rows->fetch_assoc()) {
                    $club = $row["name"];
                    echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td>', $club, $row["leader"], $row["count"]);
                    echo sprintf("<td><form method='post'>%s</form></td>",
                        mat_but_submit('', "$club Events", "event", "calendar_month", "", "$club", false)
                    );
                    if ($row["joined"]) {
                        isLeader($db, $club);
                    } else {
                        echo sprintf('<td><form method="post">%s</form></td>', mat_but_submit("Join $club", "$club", 'join', 'login', '', "join-$club", false));
                    }
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                // Else, no results, return an alert
                echo "<div class='alert alert-danger'>Community \"$value\" does not exist</div>";
            }
        } // Else, alert to enter a community name
        else {
            echo "<div class='alert alert-danger'>Please enter a community name</div>";
        }
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
    <?php sideBarButton();?>
    <div class="col text-center" style="margin: 1em">
        <h1>Communities</h1>
        <!-- Buttons for main menu & interests -->
        <div class="col text-center margin-auto mt-3">
            <form method="post">
                <?php echo mat_but_submit('', 'Main menu', 'return', 'keyboard_return', '', '', false); ?>
                <?php echo mat_but_submit('', 'Find similar interests', 'find', 'interests', '', '', false); ?>
            </form>
        </div>
        <p>Find your communities</p>
    </div>
    <!-- Search button -->
    <div class="col text-center" style="max-width: 20em; margin: 1em auto">
        <form method="post">
            <label>
                <input class="form-control" name="community" type="text" placeholder="Community" maxlength="30"
                       style="max-width: 14em; margin: 1em auto">
            </label>
            <?php echo mat_but_submit('', 'Search', 'search', 'search', '', '', false); ?>
        </form>
    </div>
    <!-- Display search results -->
    <div class="col text-center" style="min-width: 25%; max-width: 50%; margin: 1em auto">
        <?php buildTable($db); ?>
    </div>
</div>
</body>
</html>