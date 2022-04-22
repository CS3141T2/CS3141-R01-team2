<?php
include '../sidebar.php';
session_start();
$db = db();

if (!isset($_SESSION['username'])) {
    header("Location: /login");
    die();
}

if (isset($_POST["delete"])) {
    $eventId = $_POST["delete"];
    $delete = $db->prepare("DELETE FROM indiv_event WHERE id=?");
    $delete->bind_param("i", $eventId);
    $delete->execute();

    $deleteAttend = $db->prepare("DELETE FROM indivAttend WHERE id=?");
    $deleteAttend->bind_param("i", $eventId);
    $deleteAttend->execute();
}

?>

<!doctype html>
<html lang="en-US">
<head>
    <?php
    echo head_goodies();
    echo sideBar($_SESSION["username"]);
    ?>
    <title>Personal Events Created</title>
</head>
<body>
<div class="container">
    <?php sideBarButton(); ?>
    <div class="col text-center" style="margin: 1em">
        <h1>Personal Events Created</h1>
    </div>
    <div class="d-flex justify-content-center form group" style="max-width: 100%; margin: 1em auto">
        <table class="table table-bordered text-center" id="table">
            <thead>
            <tr>
                <th scope="col">Index</th>
                <th scope="col">Event Name</th>
                <th scope="col">Date</th>
                <th scope="col">Time</th>
                <th scope="col">Delete?</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $username = $_SESSION["username"];
            $ownEvents = $db->prepare("SELECT id, owner_id, name, date FROM indiv_event WHERE owner_id=? ORDER BY date ASC");
            $ownEvents->bind_param("s", $username);
            $ownEvents->execute();
            $events = $ownEvents->get_result();

            if ($events->num_rows > 0) {
                $count = 1;
                while ($items = $events->fetch_assoc()) {
                    $date = substr($items["date"], 0, 10);
                    $time = substr($items["date"], 11, 5);
                    $index = 1;
                    while ($index < 6) {
                        if ($index == 1) {
                            echo "<tr><td>" . $count . "</td>";
                            $count++;
                        }
                        if ($index == 2) {
                            echo "<td>" . $items["name"] . "</td>";
                        }
                        if ($index == 3) {
                            echo "<td>" . $date . "</td>";
                        }
                        if ($index == 4) {
                            echo "<td>" . $time . "</td>";
                        }
                        if ($index == 5) {
                            $id = $items["id"];
														echo "<td><form method='post'>";
														echo mat_but_submit('Delete', $id, 'delete', 'delete', '', '', false);
                            echo "</form></td>";
                        }
                        $index++;
                    }
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<!--suppress SpellCheckingInspection -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<!--suppress SpellCheckingInspection -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<!--suppress SpellCheckingInspection -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>

</body>
</html>