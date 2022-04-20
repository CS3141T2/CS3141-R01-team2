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
    <title>Community Editor; Tech Meets Tech</title>
</head>
<body>
<div class="container">
    <div class="col text-center" style="margin: 1em auto;">
        <h2>Manage Communities</h2>
        <?php
        // Add community
        if (isset($_POST["addCommunity"])) {
            $toInsert = $_POST["communityToAdd"];
            $toInsert1 = $_POST["leaderToAdd"];
            $view = 2;
            $table = "community";
            if ($toInsert == null || $toInsert1 == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Fields can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("INSERT INTO community VALUES (?, ?)");
                    $stmt->bind_param("ss", $toInsert, $toInsert1);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Community added</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not add community</div>";
                    }
                } catch (Exception $e) {
                    echo 'Error: caught exception ', $e->getMessage(), '\n';
                }
            }
        }
        // Remove community
        if (isset($_POST["removeCommunity"])) {
            $toInsert = $_POST["communityToRemove"];
            $view = 2;
            $table = "community";
            if ($toInsert == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Field can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("DELETE FROM community WHERE name=?");
                    $stmt->bind_param("s", $toInsert);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Community deleted</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not delete community</div>";
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
        <!-- Add community table -->
        <div class="col text-center" style="max-width: 50em; margin: auto">
            <h5>Adding Community:</h5>
            <table class='table table-bordered text-center' id='table'>
                <thead>
                <tr>
                    <th>Community name</th>
                    <th>Community leader</th>
                    <th>Submit</th>
                </tr>
                </thead>
                <tbody>
                <form method="post">
                    <tr>
                        <td style="width: 35%;">
                            <input type="text" id="communityToAdd" name="communityToAdd">
                        </td>
                        <td style="width: 35%;">
                            <input type="text" id="leaderToAdd" name="leaderToAdd">
                        </td>
                        <td>
                            <?php echo mat_but_submit('', 'Add Community', 'addCommunity', 'groups', '', '', false); ?>
                        </td>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
        <!-- Remove community table -->
        <div class="col text-center" style="max-width:30em; margin: auto">
            <h5>Removing Communities:</h5>
            <table class='table table-bordered text-center' id='table'>
                <thead>
                <tr>
                    <th>Community Name</th>
                    <th>Submit</th>
                </tr>
                </thead>
                <tbody>
                <form method="post">
                    <tr>
                        <td style="width: 50%;">
                            <input type="text" id="communityToRemove" name="communityToRemove">
                        </td>
                        <td>
                            <?php echo mat_but_submit('', 'Remove Community', 'removeCommunity', 'waving_hand', '', '', false); ?>
                        </td>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Community table -->
    <div class="col text-center" style="max-width: 50%; margin: 1em auto">
        <table class="table table-striped">
            <thead class="thead-dark">
            <tr>
                <th>name</th>
                <th>leader</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $table = "community";
            $result = getAllFromTable($table);
            while ($row = $result->fetch_assoc()) {
                echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>
                    ', $row["name"], $row["leader"]);
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
