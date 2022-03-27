<?php
include '/home/techzrla/tmt.php';
session_start();
$db = db();

// User is logged in, go to login page
if ($_SESSION["username"] == null) {
    header("Location: /login");
    die();
}

// Redirects to dashboard page
if (isset($_POST['dash'])) {
    header("Location: /dashboard/");
    die();
}

// Logout of user account
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: /login/");
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

/**
 * @param $db   Database connection
 * @param $username Current logged in user
 * @return void Table for communities the user has joined
 */
function produceTable($db, $username) {
    // Prepare statement
    $data = $db->prepare("Select * FROM member NATURAL JOIN community WHERE member.account_name = ?");
    $data->bind_param("s", $username);
    $data->execute();
    $member = $data->get_result();

    // Verifies results
    if ($member->num_rows > 0) {
        echo "<table class='table table-bordered text-center' id='table'>
            <thead>
                <th scope='col'>Community</th>
                <th scope='col'>Leader</th>
                <th >More info</th>
            </thead>
            <tbody>";
        while ($row = $member->fetch_assoc()) {
            echo sprintf('
            <tr>
                <td>%s</td>
                <td>%s</td>
                <td><a href="" style="text-decoration:none">▶️</a></td>
            </tr>
            ', $row["name"], $row["leader"]);
        }
        echo "</tbody></table>";
    }
    // Returns an alert if the user hasn't joined a community
    else {
        echo "<div class='alert alert-danger'>You have not joined communities!</div>";
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
            <div class="col text-center">
                <h1>Communities</h1>
                <div class="row text-center">
                    <form method="post">
                        <div class="row text-center margin-auto mb-3" style="max-width:50%; margin: 1em auto">
                            <div class="col ">
                                <input class="btn btn-primary mb-2" type="submit", name="dash" value="Dashboard">
                            </div>
                            <div class="col">
                                <input class="btn btn-primary mb-2" type="submit", name="logout" value="Logout">
                            </div>
                            <div class="w-100"></div>
                            <div class="col">
                                <input class="btn btn-primary mb-2" type="submit", name="find" value="Find communities">
                            </div>
                            <div class="col">
                                <input class="btn btn-primary mb-2" type="submit", name="view" value="View all communities">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col text-center">
                    <p>Listing communities for <?php echo $_SESSION["username"]?></p>
                </div>
                <div class="d-flex justify-content-center form-group" style="max-width: 50%; margin: 1em auto">
                    <?php produceTable($db, $_SESSION["username"]);?>
                </div>
            </div>
        </div>
    </body>
</html>