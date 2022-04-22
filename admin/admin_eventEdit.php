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
        <h2>Manage Community Events</h2>
        <?php
        // Add community event
        if (isset($_POST["addEvent"])) {
            $toInsert = $_POST["communityHost"];
            $toInsert1 = $_POST["eventToAdd"];
            $toInsert2 = $_POST["descriptionToAdd"];
            $view = 3;
            $table = "event";
            if ($toInsert == null || $toInsert1 == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Fields can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("INSERT INTO event(owner_id, name, description) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $toInsert, $toInsert1, $toInsert2);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Community event added</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not add community event</div>";
                    }
                } catch (Exception $e) {
                    echo 'Error: caught exception ', $e->getMessage(), '\n';
                }
            }
        }
        // Remove community event
        if (isset($_POST["removeEvent"])) {
            $toInsert = $_POST["eventToRemove"];
            $view = 3;
            $table = "event";
            if ($toInsert == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Field can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("DELETE FROM event WHERE name=?");
                    $stmt->bind_param("s", $toInsert);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Community event deleted</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not delete community event</div>";
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
    <!-- Add community event table -->
    <div class="col text-center" style="max-width: 50em; margin: auto">
        <h5>Adding Community Events:</h5>
        <table class='table table-bordered text-center' id='table'>
            <thead>
            <tr>
                <th>Community name</th>
                <th>Event name</th>
                <th>Description (Optional)</th>
                <th>Submit</th>
            </tr>
            </thead>
            <tbody>
            <form method="post">
                <tr>
                    <td style="width: 27%;">
                        <input type="text" id="communityHost" name="communityHost">
                    </td>
                    <td style="width: 27%;">
                        <input type="text" id="eventToAdd" name="eventToAdd">
                    </td>
                    <td style="width: 27%;">
                        <input type="text" id="descriptionToAdd" name="descriptionToAdd">
                    </td>
                    <td>
                        <?php echo mat_but_submit('', 'Add event', 'addEvent', 'event_available', '', '', false); ?>
                    </td>
                </tr>
            </form>
            </tbody>
        </table>
    </div>
    <!-- Remove community event table -->
    <div class="col text-center" style="max-width:30em; margin: auto">
        <h5>Removing Community Events:</h5>
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
                        <input type="text" id="eventToRemove" name="eventToRemove">
                    </td>
                    <td>
                        <?php echo mat_but_submit('', 'Remove User', 'removeEvent', 'event_busy', '', '', false); ?>
                    </td>
                </tr>
            </form>
            </tbody>
        </table>
    </div>
    <!-- Event table -->
    <div class="col text-center" style="margin: 1em auto">
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
            $table = "event";
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
