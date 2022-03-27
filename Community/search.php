<?php
include '/home/techzrla/tmt.php';
session_start();
$db = db();

// User is logged in, go to login page
if ($_SESSION["username"] == null) {
    header("Location: /login");
    die();
}

// Redirects to community home page
if (isset($_POST["return"])) {
    header("Location: /community/");
    die();
}

// Redirects to community home page
if (isset($_POST["find"])) {
    header("Location: /community/commByInterest.php");
    die();
}

/**
 * A function for building the table for search results
 * @param $db   Database connection
 * @return void Table of search results, otherwise posts alert
 */

function buildTable($db) {
    // Only search if a value was entered in the search bar
    if (isset($_POST['search'])) {
        $value = $_POST['community'];
        // Only build table if value isn't ""
        if ($value != "") {
            // Prepare statement
            $stmt = $db->prepare("SELECT name, leader, count(*) as count FROM member natural join community where name = ? group by name");
            $stmt->bind_param("s", $value);
            $stmt->execute();
            $rows = $stmt->get_result();
            // verify results
            if ($rows->num_rows > 0) {
                echo "<table class='table table-bordered text-center' id='table'>
                        <thead>
                            <th scope='col'>Community</th>
                            <th scope='col'>Leader</th>
                            <th scope='col'>Member count</th>
                            <th scope='col'>More info</th>
                        </thead>
                      <tbody>";
                // Iterate through all rows
                while ($row = $rows->fetch_assoc()) {
                    echo sprintf('
                        <tr>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td><a href="" style="text-decoration:none">▶️</a></td>
                        </tr>
                        ', $row["name"], $row["leader"], $row["count"]);
                }
                echo "</tbody></table>";
            } else {
                // Else, no results, return an alert
                echo "<div class='alert alert-danger'>Community \"$value\" does not exist</div>";
            }
        }
        // Else, alert to enter a community name
        else {
            echo "<div class='alert alert-danger'>Please enter a community name</div>";
        }
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
                <div class="row text-center margin-auto mt-3">
                    <form method="post">
                        <div class="col">
                            <input class="btn btn-primary" type="submit", name="return" value="Return to main menu">
                        </div>
                        <div class="col">
                            <input class="btn btn-primary" type="submit" name="find" value="Find similar interests" style="max-width: 14em; margin: 1em auto">
                        </div>
                    </form>
                </div>
                <p>Find your communities</p>
            </div>
            <div class="col text-center" style="max-width: 20em; margin: 1em auto">
                <form method="post" action="search.php">
                    <input class="form-control" name="community" type="text" placeholder="Community" maxlength="30" style="max-width: 14em; margin: 1em auto">
                    <input class="btn btn-primary" type="submit" name="search" value="search" style="max-width: 14em; margin: 1em auto">

                </form>
            </div>
            <div class="col text-center" style="min-width: 25%; max-width: 50%; margin: 1em auto">
                <?php buildTable($db);?>
            </div>
        </div>
    </body>
</html>