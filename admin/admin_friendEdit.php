<?php
include '../tmt.php';
include 'utils.php';
session_start();

// User is not logged in, go to login page
if (!isset($_SESSION["username"])) {
    header("Location: /login");
    die();
} // Else verify the use is an admin
else if (isAdmin($_SESSION['username']) == false) {
    header("LOCATION: /dashboard");
    die();
}
?>
<html>
<head>
    <?php echo head_goodies(); ?>
    <title>Friend Editor; Tech Meets Tech</title>
</head>
<body>
<div class="container">
    <div class="col text-center" style="margin: 1em auto;">
        <h2>Manage Friends</h2>
        <?php
        // Add friends
        if (isset($_POST["addFriends"])) {
            $toInsert = $_POST["a_friend1"];
            $toInsert1 = $_POST["a_friend2"];
            if ($toInsert == null || $toInsert1 == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Fields can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("INSERT INTO friend(user1, user2) VALUES (?, ?)");
                    $stmt->bind_param("ss", $toInsert, $toInsert1);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Friends added</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not add friends</div>";
                    }
                } catch (Exception $e) {
                    echo 'Error: caught exception ', $e->getMessage(), '\n';
                }
            }
        }
        // Remove friends
        if (isset($_POST["removeFriends"])) {
            $toRemove = $_POST["r_friend1"];
            $toRemove1 = $_POST["r_friend2"];
            if ($toRemove == null || $toRemove1 == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Fields can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("DELETE FROM friend WHERE user1 = ? and user2 = ?");
                    $stmt->bind_param("ss", $toRemove, $toRemove1);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Friends removed</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not remove friends</div>";
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
    <!-- Add friends table -->
    <div class="col text-center" style="max-width: 50%; margin: auto">
        <h5>Adding Friends:</h5>
        <table class='table table-bordered text-center' id='table'>
            <thead>
            <tr>
                <th>Friend 1</th>
                <th>Friend 2</th>
                <th>Submit</th>
            </tr>
            </thead>
            <form method="post">
                <tr>
                    <td>
                        <input type="text" id="a_friend1" name="a_friend1">
                    </td>
                    <td>
                        <input type="text" id="a_friend2" name="a_friend2">
                    </td>
                    <td>
                        <?php echo mat_but_submit('', 'Add friends', 'addFriends', 'person_add_alt', '', '', false); ?>
                    </td>
                </tr>
            </form>
            </tbody>
        </table>
    </div>
    <!-- Remove friends table -->
    <div class="col text-center" style="max-width: 50%; margin: auto">
        <h5>Removing Friends:</h5>
        <table class='table table-bordered text-center' id='table'>
            <thead>
            <tr>
                <th>Friend 1</th>
                <th>Friend 2</th>
                <th>Submit</th>
            </tr>
            </thead>
            <form method="post">
                <tr>
                    <td>
                        <input type="text" id="r_friend1" name="r_friend1">
                    </td>
                    <td>
                        <input type="text" id="r_friend2" name="r_friend2">
                    </td>
                    <td>
                        <?php echo mat_but_submit('', 'Remove friends', 'removeFriends', 'person_remove', '', '', false); ?>
                    </td>
                </tr>
            </form>
            </tbody>
        </table>
    </div>
    <!-- friend table -->
    <div class="col text-center" style="max-width: 50%; margin: 1em auto">
        <table class="table table-striped">
            <thead class="thead-dark">
            <tr>
                <th>user1</th>
                <th>user2</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $table = "friend";
            $result = getAllFromTable($table);
            while ($row = $result->fetch_assoc()) {
                echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>
                    ', $row["user1"], $row["user2"]);
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
