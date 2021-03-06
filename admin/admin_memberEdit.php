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
    <title>Member Editor; Tech Meets Tech</title>
</head>
<body>
<div class="container">
    <div class="col text-center" style="margin: 1em auto;">
        <h2>Manage Community Members</h2>
        <?php
        // Add member to community
        if (isset($_POST["addMember"])) {
            $toInsert = $_POST["memberToAdd"];
            $toInsert1 = $_POST["toCommunity"];
            $view = 4;
            $table = "member";
            if ($toInsert == null || $toInsert1 == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Fields can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("INSERT INTO member(account_name, name) VALUES (?, ?)");
                    $stmt->bind_param("ss", $toInsert, $toInsert1);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Member added</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not add member</div>";
                    }
                } catch (Exception $e) {
                    echo 'Error: caught exception ', $e->getMessage(), '\n';
                }
            }
        }
        // Remove member to community
        if (isset($_POST["removeMember"])) {
            $toInsert = $_POST["memberToRemove"];
            $toInsert1 = $_POST["fromCommunity"];
            $view = 4;
            $table = "member";
            if ($toInsert == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Field can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("DELETE FROM member WHERE account_name=? AND name=?");
                    $stmt->bind_param("ss", $toInsert, $toInsert1);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Member removed</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not remove member</div>";
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
    <!-- Add member table -->
    <div class="col text-center" style="max-width: 50%; margin: auto">
        <h5>Adding Members:</h5>
        <table class='table table-bordered text-center' id='table'>
            <thead>
            <tr>
                <th>User</th>
                <th>Community</th>
                <th>Submit</th>
            </tr>
            </thead>
            <tbody>
            <form method="post">
                <tr>
                    <td>
                        <input type="text" id="memberToAdd" name="memberToAdd">
                    </td>
                    <td>
                        <input type="text" id="toCommunity" name="toCommunity">
                    </td>
                    <td style="width: 33%">
                        <?php echo mat_but_submit('', 'Add member', 'addMember', 'person_add', '', '', false); ?>
                    </td>
                </tr>
            </form>
            </tbody>
        </table>
    </div>
    <!-- Remove member table -->
    <div class="col text-center" style="max-width:50%; margin: auto">
        <h5>Removing Members:</h5>
        <table class='table table-bordered text-center' id='table'>
            <thead>
            <tr>
                <th>User</th>
                <th>Community</th>
                <th>Submit</th>
            </tr>
            </thead>
            <tbody>
            <form method="post">
                <tr>
                    <td>
                        <input type="text" id="memberToRemove" name="memberToRemove">
                    </td>
                    <td>
                        <input type="text" id="fromCommunity" name="fromCommunity">
                    </td>
                    <td style="width: 33%">
                        <?php echo mat_but_submit('', 'Remove member', 'removeMember', 'person_remove', '', '', false); ?>
                    </td>
                </tr>
            </form>
            </tbody>
        </table>
    </div>
    <!-- member table -->
    <div class="col text-center" style="max-width: 50%; margin: 1em auto">
        <table class="table table-striped">
            <thead class="thead-dark">
            <tr>
                <th>account_name</th>
                <th>name</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $table = "member";
            $result = getAllFromTable($table);
            while ($row = $result->fetch_assoc()) {
                echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>
                    ', $row["account_name"], $row["name"]);
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
