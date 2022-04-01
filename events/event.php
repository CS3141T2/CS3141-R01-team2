<?php
include '/home/techzrla/tmt.php';
session_start();
$db = db();

if ($_SESSION["username"] == null) {
    header("Location: /login/index.php");
    die();
}

if (isset($_POST["indivEvent"])) {
    header("Location: /events/indiv_event_create.php");
    die();
}

if (isset($_POST['commEvent'])){
    header("Location: /events/comm_event_create.php");
    die();
}

function printTable($rows){
    $index = 1;
    while ($items = $rows->fetch_assoc()) {
        $count = 0;
        while ($count < 8) {
            if ($count == 0) {
                echo "<tr>";
                echo "<th scope='row'>" . $index . "</th>";
                $index++;
            }
            if ($count == 1) { echo "<td>" . $items["owner_id"] . "</td>"; }
            if ($count == 2) { $date = substr($items["date"], 0, 10); echo "<td>" . $date. "</td>"; }
            if ($count == 3) { echo "<td>" . $items["location"] . "</td>"; }
            if ($count == 4) { echo "<td>" . $items["name"] . "</td>"; }
            if ($count == 5) { echo "<td>" . $items["type"] . "</td>"; }
            if ($count == 6) { echo "<td>" . $items["description"] . "</td>"; }
            if ($count == 7) {
                echo "<td>Attendees</td>";
                echo "</tr>";
            }
            $count++;
        }
    }
}
?>

<html lang="en-US">
<head>
    <?php echo bootstrap(); ?>
    <title>Events</title>
</head>
<body>
    <div class="container">
        <div class="col text-center" style="margin: 1em">
            <h1>Events</h1>
            <form method="post" action="event.php">
                <div class="col text-center">
                    <input class="btn btn-primary" type="submit" name="indivEvent" value="Create Individual Event">
                    <input class="btn btn-primary" type="submit" name="commEvent" value="Create Community Event">
                </div>
            </form>
            <h2>Public Individual Events</h2>
        </div>
        <div class="d-flex justify-content-center form group" style="max-width: 100%; margin: 1em auto">
            <table class="table table-bordered text-center" id="table" value="Individual Events">
                <thead>
                    <tr>
                        <th scope="col">Index</th>
                        <th scope="col">Creator</th>
                        <th scope="col">Date</th>
                        <th scope="col">Location</th>
                        <th scope="col">Name</th>
                        <th scope="col">Type</th>
                        <th scope="col">Description</th>
                        <th scope="col">Attendees</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $indivEventsList = $db->prepare("SELECT id, owner_id, date, location, name, type, description FROM indiv_event WHERE private=0");
                        $indivEventsList->execute();
                        $resultEventList = $indivEventsList->get_result();

                        printTable($resultEventList);
                    ?>
                </tbody>
            </table>
        </div>
            <div class="col text-center" style="margin: 1em">
                <h2>Community Events</h2>
            </div>
            <div class="d-flex justify-content-center form group" style="max-width: 100%; margin: 1em auto">
                <table class="table table-bordered text-center" id="table" value="Individual Events">
                    <thead>
                        <tr>
                            <th scope="col">Index</th>
                            <th scope="col">Community</th>
                            <th scope="col">Date</th>
                            <th scope="col">Location</th>
                            <th scope="col">Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Description</th>
                            <th scope="col">Attendees</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $commEventsList = $db->prepare("SELECT owner_id, location, name, type, description FROM event");
                            $commEventsList->execute();
                            $resultCommEventList = $commEventsList->get_result();

                            printTable($resultCommEventList);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>