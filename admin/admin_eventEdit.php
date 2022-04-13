<?php
include '/home/techzrla/tmt.php';
session_start();

// User is not logged in, go to login page
if ($_SESSION['username'] == null) {
    header("Location: /login");
    die();
}
else {
    $db = db();
    $perm = "admin";
    $stmt = $db->prepare("select * from account where username = ? and permission = ? ");
    $stmt->bind_param("ss", $_SESSION['username'], $perm);
    $stmt->execute();
    if($stmt->get_result()->num_rows == 0) {
        header("Location: /dashboard");
        die();
    }
}

?>
<html>
<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
        background-color: whitesmoke;
    }
</style>
<head>
    <title>Community Editor; Tech Meets Tech</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>
<form action="index.php" method="post">
    <input type="submit" value='Back' name='Back'>
</form>
<?php
include 'utils.php';
if (isset($_POST["addEvent"])) {
    $toInsert = $_POST["communityHost"];
    $toInsert1 = $_POST["eventToAdd"];
    $toInsert2 = $_POST["descriptionToAdd"];
    $view = 3;
    $table = "event";
    if ($toInsert == null) {
        echo "Field can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("INSERT INTO event(owner_id, name, description) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $toInsert, $toInsert1, $toInsert2);
            $stmt->execute();
            echo "Event statement executed without errors";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

if (isset($_POST["removeEvent"])) {
    $toInsert = $_POST["eventToRemove"];
    $view = 3;
    $table = "event";
    if ($toInsert == null) {
        echo "Field can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("DELETE FROM event WHERE name=?");
            $stmt->bind_param("s", $toInsert);
            $stmt->execute();
            echo "Event removal statement executed successfully";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

echo "Add host + event name + description (optional):"
?>
<form action="admin_eventEdit.php" method="post">
    <input type="text" id="eventToAdd" name="eventToAdd">
    <input type="text" id="descriptionToAdd" name="descriptionToAdd">
    <input type="submit" value="Add User" name="addEvent">
</form>
<?php
echo "Remove event:"
?>
<form action="admin_eventEdit.php" method="post">
    <input type="text" id="eventToRemove" name="eventToRemove">
    <input type="submit" value="Remove User" name="removeEvent">
</form>
<table class="table table-striped">
    <thead class="thead-dark">
        <th>id</th>
        <th>owner_id</th>
        <th>date</th>
        <th>location</th>
        <th>name</th>
        <th>type</th>
        <th>description</th>
    </tr>

<?php
$table = "event";
$result = getAllFromTable($table);
while ($row = $result->fetch_assoc()) {
    echo sprintf('
                    <tr>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                    </tr>
                    ', $row["id"], $row["owner_id"], $row["date"], $row["location"], $row["name"], $row["type"], $row["description"]);
}
echo "<table>";
?>
    </thead>
</table>
