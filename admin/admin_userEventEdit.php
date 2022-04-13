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
    <title>User Editor; Tech Meets Tech</title>
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
if (isset($_POST["addIndivEvent"])) {
    $toInsert = $_POST["userHost"];
    $toInsert1 = $_POST["indivEventToAdd"];
    $toInsert2 = $_POST["indivDescription"];
    $view = 5;
    $table = "indiv_event";
    if ($toInsert == null || $toInsert1 == null) {
        echo "Field can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("INSERT INTO indiv_event(owner_id, name, description) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $toInsert, $toInsert1);
            $stmt->execute();
            echo "Event statement executed without errors";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

if (isset($_POST["removeIndivEvent"])) {
    $toInsert = $_POST["indivEventToRemove"];
    $view = 5;
    $table = "indiv_event";
    if ($toInsert == null) {
        echo "Field can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("DELETE FROM indiv_event WHERE id=?");
            $stmt->bind_param("i", $toInsert);
            $stmt->execute();
            echo "Event removal statement executed successfully";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

// User event management script
echo "Add User host + event name + description (optional):"
?>
<form action="admin_userEventEdit.php" method="post">
    <input type="text" id="indivEventToAdd" name="userHost">
    <input type="text" id="indivEventToAdd" name="indivEventToAdd">
    <input type="text" id="indivDescription" name="indivDescription">
    <input type="submit" value="Add User Event" name="addIndivEvent">
</form>
<?php
echo "Remove User Event (ID):"
?>
<form action="admin_userEventEdit.php" method="post">
    <input type="text" id="indivEventToRemove" name="indivEventToRemove">
    <input type="submit" value="Remove User Event" name="removeIndivEvent">
</form>
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

<?php
$table = "indiv_event";
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
