<?php
include '../tmt.php';
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
    <?php echo head_goodies(); ?>
    <title>Personal Events Created</title>
</head>
<body>
    <div class="container">
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
                            $ownEvents = $db->prepare("SELECT owner_id, name, date FROM indiv_event WHERE owner_id=?");
                            $ownEvents->bind_param("s", $username);
                            $ownEvents->execute();
                            $events = $ownEvents->get_result();

                            if ($events->num_rows > 0){
                                while ($items = $events->fetch_assoc()){
                                    $date = substr($items["date"], 0, 10);
                                    $time = substr($items["date"], 11, 5);
                                    $index = 1;
                                    while ($index < 6){
                                        if ($index == 1) { echo "<tr><td>". $index ."</td>"; }
                                        if ($index == 2) { echo "<td>". $items["name"] ."</td>"; }
                                        if ($index == 3) { echo "<td>". $date ."</td>"; }
                                        if ($index == 4) { echo "<td>". $time ."</td>"; }
                                        if ($index == 5) { echo "<td>Delete button</td></tr>"; }
                                        $index++;
                                    }
                                }
                            }
                    ?>
                </tbody>
            </table>
	    </div>
    </div>
</body>
</html>