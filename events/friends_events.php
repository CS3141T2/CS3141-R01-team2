<?php
include '../sidebar.php';
session_start();
$db = db();

if (!isset($_SESSION['username'])) {
    header("Location: /login");
    die();
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
        <h1>Friends Events</h1>
    </div>
    <div class="d-flex justify-content-center form group" style="max-width: 100%; margin: 1em auto">
        <table class="table table-bordered text-center" id="table">
            <thead>
            <tr>
                <th scope="col">Index</th>
                <th scope="col">Creator</th>
                <th scope="col">Event Name</th>
                <th scope="col">Description</th>
                <th scope="col">Date</th>
                <th scope="col">Time</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $username = $_SESSION["username"];
            $friends = $db->prepare("SELECT user2 FROM friend WHERE user1=? ORDER BY user2 ASC");
            $friends->bind_param("s", $username);
            $friends->execute();
            $result = $friends->get_result();

            if ($result->num_rows > 0) {
                $count = 1;
                while ($list = $result->fetch_assoc()) {

                    $ownEvents = $db->prepare("SELECT id, owner_id, name, description, date FROM indiv_event WHERE owner_id=? AND date > NOW() ORDER BY date ASC");
                    $ownEvents->bind_param("s", $list["user2"]);
                    $ownEvents->execute();
                    $events = $ownEvents->get_result();

                    if ($events->num_rows > 0) {

                        while ($items = $events->fetch_assoc()) {
                            $date = substr($items["date"], 0, 10);
                            $time = substr($items["date"], 11, 5);
                            $index = 1;
                            while ($index < 7) {
                                if ($index == 1) {
                                    echo "<tr><td>" . $count . "</td>";
                                    $count++;
                                }
                                if ($index == 2) {
                                    echo "<td>" . $items["owner_id"] . "</td>";
                                }
                                if ($index == 3) {
                                    echo "<td>" . htmlspecialchars($items["name"]) . "</td>";
                                }
                                if ($index == 4) {
                                    echo "<td>" . htmlspecialchars($items["description"]) . "</td>";
                                }
                                if ($index == 5) {
                                    echo "<td>" . $date . "</td>";
                                }
                                if ($index == 6) {
                                    echo "<td>" . $time . "</td></tr>";
                                }
                                $index++;
                            }
                        }
                    }
                }
            } else {
                echo "No Friends";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
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
