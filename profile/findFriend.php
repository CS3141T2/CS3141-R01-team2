<?php

include '../sidebar.php';
session_start();
$db = db();

// Leave the page if the user is not signed in.
if (!isset($_SESSION["username"])) {
    header("Location: /login");
    die();
}
/**
 * Function for building search result table
 * @param mysqli $db Connection to database
 * @return void
 */
function buildTable(mysqli $db): void
{
    // Only build table if search results are valid
    if (isset($_POST["search"])) {
        $value = $_POST['friend'];
        // Cannot add yourself
        if ($value == $_SESSION["username"]) {
            echo "<div class='alert alert-danger'>Cannot be friends with yourself!</div>";
        } // Ensure a valid input
        else if ($value != "") {
            $stmt = $db->prepare("SELECT username, IF(? in (SELECT user2 FROM friend where user1 = ?), 'Yes', 'No') as friends
                                    from account where username = ?");
            $stmt->bind_param("sss", $value, $_SESSION["username"], $value);
            $stmt->execute();
            $rows = $stmt->get_result();
            if ($rows->num_rows > 0) {
                echo "<table class='table table-bordered text-center' id='table'>
                        <thead>
                            <th scope='col'>Username</th>
                            <th scope='col'>Profile</th>
                            <th scope='col'>Friend</th>
                        </thead>
                      <tbody>";
                while ($row = $rows->fetch_assoc()) {
                    $user = $row["username"];
                    echo sprintf("<tr><td>%s</td><td>%s</td>", $user, "<a href='index.php?username=$user'>$user</a>", "Profile");
                    if ($row["friends"] == "Yes") {
                        echo sprintf('<td><form method="post">%s</form></td>', mat_but_submit("Remove friend", $user, 'remove', 'person_remove', '', "Add-$user", false));
                    } else {
                        echo sprintf('<td><form method="post">%s</form></td>', mat_but_submit("Add friend", $user, 'add', 'person_add', '', "Remove-$user", false));
                    }
                    echo "</tr>";
                }
            } else {
                // Else, no results, return an alert
                echo "<div class='alert alert-danger'>Friend \"$value\" does not exist</div>";
            }
        } // Alert to have a friend username
        else {
            echo "<div class='alert alert-danger'>Please enter a friend username</div>";
        }
    }
}

?>


<!-- HTML file + page -->
<html>
<head>
    <?php
    echo head_goodies();
    echo sideBar($_SESSION["username"]);
    ?>
    <title> Find Friends </title>
</head>
<body>
<div class="container" style="max-width: 20em; margin: 1em auto">
    <?php sideBarButton(); ?>
    <div class="col text-center">
        <h3>Find friends</h3>

        <?php
        // Add friend (display success/error)
        if (isset($_POST["add"])) {
            $stmt = $db->prepare("INSERT INTO friend(user1, user2) values (?,?)");
            $stmt->bind_param("ss", $_SESSION["username"], $_POST["add"]);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Added friend</div>";
            } else {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Could not add friend</div>";
            }
        }
        // Remove friend (display success/error)
        if (isset($_POST["remove"])) {
            $stmt = $db->prepare("DELETE FROM friend where user1 = ? AND user2 = ?");
            $stmt->bind_param("ss", $_SESSION["username"], $_POST["remove"]);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Removed friend</div>";
            } else {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Could not remove friend</div>";
            }
        }

        ?>
        <div class="col">
            <form method="post">
                <label>
                    <input class="form-control" name="friend" type="text" placeholder="Username" maxlength="30"
                           style="max-width: 14em; margin: 1em auto">
                </label>
                <br>
                <?php echo mat_but_submit('', 'Search', 'search', 'search', '', '', false); ?>
            </form>
        </div>
        <div class="col">
            <?php buildTable($db); ?>
        </div>
    </div>
</div>
</body>
</html>
