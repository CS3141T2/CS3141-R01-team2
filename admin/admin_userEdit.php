<?php
include '../tmt.php';
include 'utils.php';
session_start();

// User is not logged in, go to login page
if (!isset($_SESSION["username"])) {
    header("Location: /login");
    die();
} else {
    $db = db();
    $perm = "admin";
    $stmt = $db->prepare("select * from account where username = ? and permission = ? ");
    $stmt->bind_param("ss", $_SESSION['username'], $perm);
    $stmt->execute();
    if ($stmt->get_result()->num_rows == 0) {
        header("Location: /dashboard");
        die();
    }
}
?>
<html>
<head>
    <?php echo head_goodies(); ?>
    <title>User Editor; Tech Meets Tech</title>
</head>
<body>
<div class="container">
    <div class="col text-center" style="margin: 1em auto;">
        <h2>Manage Users</h2>
        <?php
        if (isset($_POST["addUser"])) {
            $toInsert = $_POST["userToAdd"];
            if ($toInsert == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>User can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("INSERT INTO account(username) VALUES (?)");
                    $stmt->bind_param("s", $toInsert);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Added account</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not add account</div>";
                    }
                } catch (Exception $e) {
                    echo 'Error: caught exception ', $e->getMessage(), '\n';
                }
            }
        }

        // Similar functionality to adding user, but the SQL statement has changed to
        // remove from accounts.
        if (isset($_POST["removeUser"])) {
            $toInsert = $_POST["userToRemove"];
            if ($toInsert == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>User can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("DELETE FROM account WHERE username=?");
                    $stmt->bind_param("s", $toInsert);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Account deleted</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not delete account</div>";
                    }
                } catch (Exception $e) {
                    echo 'Error: caught exception ', $e->getMessage(), '\n';
                }
            }
        }

        ?>

        <form action="index.php" method="post">
            <?php echo mat_but_submit('', 'Back', 'Back', 'keyboard_return', '', '', false); ?>
        </form>
    </div>
    <div class="row text-center" style="margin: 1em auto">
        <div class="col text-center" style="max-width: 30em; margin: auto">
            <h5>Adding Users:</h5>
            <table class='table table-bordered text-center' id='table'>
                <thead>
                <tr>
                    <th>Username</th>
                    <th>Submit</th>
                </tr>
                </thead>
                <tbody>
                <form method="post">
                    <tr>
                        <td style="width: 50%;">
                            <input type="text" id="userToAdd" name="userToAdd">
                        </td>
                        <td>
                            <?php echo mat_but_submit('', 'Add user', 'addUser', 'person_add', '', '', false); ?>
                        </td>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
        <div class="col text-center" style="max-width:30em; margin: auto">
            <h5>Removing Users:</h5>
            <table class='table table-bordered text-center' id='table'>
                <thead>
                <tr>
                    <th>Username</th>
                    <th>Submit</th>
                </tr>
                </thead>
                <tbody>
                <form method="post">
                    <tr>
                        <td style="width: 50%;">
                            <input type="text" id="userToRemove" name="userToRemove">
                        </td>
                        <td>
                            <?php echo mat_but_submit('', 'Remove User', 'removeUser', 'person_remove', '', '', false); ?>
                        </td>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col text-center">
        <table class="table table-striped">
            <thead class="thead-dark">
            <tr>
                <th>username</th>
                <th>phone</th>
                <th>profile_description</th>
                <th>year</th>
                <th>major</th>
                <th>color</th>
                <th>twitter_username</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $table = "account";
            $result = null;
            $result = getAllFromTable($table);
            echo "Formal accounts in TMT";
            while ($row = $result->fetch_assoc()) {
                echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>
                    ', $row["username"], $row["phone"], $row["profile_description"], $row["year"], $row["major"], $row["color"], $row["twitter_username"]);
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
