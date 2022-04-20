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
    <title>User Editor; Tech Meets Tech</title>
</head>
<body>
<div class="container">
    <div class="col text-center" style="margin: 1em auto;">
        <h2>Manage Individual Events</h2>
        <?php
        // Add individual event
        if (isset($_POST["addIndivEvent"])) {
            $toInsert = $_POST["userHost"];
            $toInsert1 = $_POST["indivEventToAdd"];
            $toInsert2 = $_POST["indivDescription"];
            $view = 5;
            $table = "indiv_event";
            if ($toInsert == null || $toInsert1 == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Fields can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("INSERT INTO indiv_event(owner_id, name, description) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $toInsert, $toInsert1);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>User event added</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not add user event</div>";
                    }
                } catch (Exception $e) {
                    echo 'Error: caught exception ', $e->getMessage(), '\n';
                }
            }
        }
        // Remove individual event
        if (isset($_POST["removeIndivEvent"])) {
            $toInsert = $_POST["indivEventToRemove"];
            $view = 5;
            $table = "indiv_event";
            if ($toInsert == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Field can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("DELETE FROM indiv_event WHERE id=?");
                    $stmt->bind_param("i", $toInsert);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>User event deleted</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not delete user event</div>";
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
    <!-- Add individual event table -->
    <div class="col text-center" style="max-width: 50em; margin: auto">
        <h5>Adding User Event:</h5>
        <table class='table table-bordered text-center' id='table'>
            <thead>
            <tr>
                <th>User</th>
                <th>Event name</th>
                <th>Description (Optional)</th>
                <th>Submit</th>
            </tr>
            </thead>
            <tbody>
            <form method="post">
                <tr>
                    <td style="width: 25%;">
                        <input type="text" id="indivEventToAdd" name="userHost">
                    </td>
                    <td style="width: 25%;">
                        <input type="text" id="indivEventToAdd" name="indivEventToAdd">
                    </td>
                    <td style="width: 25%;">
                        <input type="text" id="indivDescription" name="indivDescription">
                    </td>
                    <td>
                        <?php echo mat_but_submit('', 'Add Event', 'addIndivEvent', 'event_available', '', '', false); ?>
                    </td>
                </tr>
            </form>
            </tbody>
        </table>
    </div>
    <!-- Remove individual event table -->
    <div class="col text-center" style="max-width:30em; margin: auto">
        <h5>Removing User Event:</h5>
        <table class='table table-bordered text-center' id='table'>
            <thead>
            <tr>
                <th>Event name</th>
                <th>Submit</th>
            </tr>
            </thead>
            <tbody>
            <form method="post">
                <tr>
                    <td style="width: 50%;">
                        <input type="text" id="indivEventToRemove" name="indivEventToRemove">
                    </td>
                    <td>
                        <?php echo mat_but_submit('', 'Remove Event', 'removeIndivEvent', 'event_busy', '', '', false); ?>
                    </td>
                </tr>
            </form>
            </tbody>
        </table>
    </div>
    <!-- indiv_event table -->
    <div class="col text-center" style="max-width: 50em; margin: 1em auto">
        <table class="table table-striped">
            <thead class="thead-dark">
            <tr>
                <th>id</th>
                <th>owner_id</th>
                <th>date</th>
                <th>location</th>
                <th>name</th>
                <th>type</th>
                <th>description</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $table = "indiv_event";
            $result = getAllFromTable($table);
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
                    ', $row["id"], $row["owner_id"], $row["date"], $row["location"], $row["name"], $row["type"], $row["description"]);
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
