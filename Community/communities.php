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
    $value = preg_split('/\s+/', $_POST["event2"])[1];
    $add = $db->prepare("call addToCommunity(?, ?)");
    $add->bind_param("ss", $_SESSION["username"], $value);
    $add->execute();

    $stmt = $db->prepare("SELECT * FROM member where account_name = ? and name = ?");
    $stmt->bind_param("ss", $_SESSION["username"], $value);
    $stmt->execute();
    $rows = $stmt->get_result();
    echo "<div class='col text-center' style='max-width: 25%; margin: 1em auto'>";
    if ($rows->num_rows() > 0) {
        echo "<div class='alert alert-success'>You have been added to $value community</div>";
    }
    else {
        echo "<div class='alert alert-danger'>Unable to add you to the community</div>";
    }
    echo "</div>";
    die();
}

// Return to community home page
if (isset($_POST["return"])) {
    header("Location: /community/");
    die();
}

/**
 * Creates a table of all communities
 * @param $db   Database connect
 * @param $username     Current logged in user
 * @return void     Table creation
 */
function allCommunities($db, $username) {

    // Prepare statement
    $data = $db->prepare("
        WITH memberCount as (
            SELECT name, count(*) as count from member group by name
        ),
            communityInfo as (
            SELECT name, leader, ifnull(count, 0) as count from community left outer join memberCount using (name)
        ),
            memberJoin as (
            SELECT * from member where account_name=?
        )
        SELECT name, leader, count, if(ifnull(t1.entry_no, 'No') = 'No', 'No', 'Yes') as joined 
            from communityInfo 
            left outer join memberJoin as t1 using (name)");
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
                echo "<td><form><input class='btn btn-primary' type='submit', name='event' value='Joined' disabled></form></td>";
            }
            else {
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
                        <input class="btn btn-primary" type="submit", name="return" value="Return to main menu">
                    </div>
                </form>
                <p>Viewing all communities</p>
            </div>
            <div class="d-flex justify-content-center form-group" style="max-width: 50%; margin: 1em auto">
                <?php allCommunities($db, $_SESSION["username"]); ?>
            </div>
        </div>
    </body>
</html>