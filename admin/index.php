<html>
<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
        background-color: whitesmoke;
    }
</style>
<?php
include '/home/techzrla/tmt.php';
session_start();
// Validate login before proceeding.
if(!isset($_SESSION["username"])) {
    header("LOCATION: /login");
    die();
}
else {
    // Check that the user is an admin
    $db = db();
    $stmt = $db->prepare("SELECT * FROM account WHERE username = ? AND permission = 'admin'");
    $stmt->bind_param("s", $_SESSION["username"]);
    $stmt->execute();
    if ($stmt->get_result()->num_rows == 0) {
        header("LOCATION: /dashboard");
        die();
    }
    else {
        echo "<div class='alert alert-succes s'>Welcome to the admin dashboard!</div>";
    }
}
print_r($_POST);
// Table variable indicates what table to generate for the table generation function
$table = null;

// Sets view to select a table (0) if $view was not set
if (!isset($view)) {
    $view = 0;
}

// Attempts to add a user to accounts using data input on the "manage accounts" page
// Sets variables to head to the accounts page
if (isset($_POST["addUser"])) {
    $toInsert = $_POST["userToAdd"];
    $view = 1;
    $table = "account";
    if ($toInsert == null) {
        echo "User can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("INSERT INTO account(username) VALUES (?)");
            $stmt->bind_param("s", $toInsert);
            $stmt->execute();
            echo "User added successfully";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

// Similar functionality to adding user, but the SQL statement has changed to
// remove from accounts.
if (isset($_POST["removeUser"])) {
    $toInsert = $_POST["userToRemove"];
    $view = 1;
    $table = "account";
    if ($toInsert == null) {
        echo "User can't be null";
    } else {
        try {
            $db = db();
            $stmt = $db->prepare("DELETE FROM account WHERE username=?");
            $stmt->bind_param("s", $toInsert);
            $stmt->execute();
            echo "User removed successfully";
        } catch (Exception $e) {
            echo 'Error: caught exception ', $e->getMessage(), '\n';
        }
    }
}

// If the back button was pressed, load the navigation page
if (isset($_POST["Back"])) {
    $view = 0;
}

// If "Manage Accounts" was clicked, load the account management scripts.
if (isset($_POST["1"])) {
    $view = 1;
    $table = "account";
    echo "Done1";
}

// If "Manage Communities" was clicked, load the community management scripts.
elseif (isset($_POST["2"])) {
    $view = 2;
    $table = "community";
    echo "Done2";
}

// If "Manage Community Events" was clicked, load event management scripts.
elseif (isset($_POST["3"])) {
    $view = 3;
    $table = "event";
    echo "Done3";
}

// If "Manage Community Members" was clicked, load member management scripts.
elseif (isset($_POST["4"])) {
    $view = 4;
    $table = "member";
    echo "Done4";
}

// If "Manage User Events" was clicked, load user event management scripts.
elseif (isset($_POST["5"])) {
    $view = 5;
    $table = "indiv_event";
    echo "Done5";
}

// If "Manage User Friends" was clicked, load user friend management scripts.
elseif (isset($_POST["6"])) {
    $view = 6;
    $table = "friend";
    echo "Done6";
}

// Not Yet Implemented
elseif (isset($_POST["7"])) {
    $view = 7;
    echo "Done7";
}

// If "Manage Interest List" was clicked, loads users and their interests.
// Will be more directly tied to the survey in the future.
elseif (isset($_POST["8"])) {
    $view = 8;
    $table = "interests";
    echo "Done8";
}

// Loads the table select forms if $view == 0
    if ($view == 0) {
    ?>
<form method="post" action="index.php">
    <input type="submit" value="Manage Accounts" name="1">
    <input type="submit" value="Manage Communities" name="2">
    <input type="submit" value="Manage Community Events" name="3">
    <input type="submit" value="Manage Community Members" name="4">
</form>
<form method="post" action="index.php">
    <input type="submit" value="Manage User Events" name="5">
    <input type="submit" value="Manage User Friends" name="6">
    <input type="submit" value="Manage Survey Questions" name="7">
    <input type="submit" value="Manage Interest List" name="8">
</form>
    <?php
    } else {
        // Loads the back button and figures out which script to run
        ?>
    <form action="index.php" method="post">
    <input type="submit" value='Back' name='Back'>
    </form>
        <?php
        switch ($view) {
            // Account management script.
            case 1:
                echo "Add User:"
                ?>
                <form action="index.php" method="post">
                      <input type="text" id="userToAdd" name="userToAdd">
                      <input type="submit" value="Add User" name="addUser">
                </form>
                <?php
                echo "Remove User:"
                ?>
                <form action="index.php" method="post">
                    <input type="text" id="userToRemove" name="userToRemove">
                    <input type="submit" value="Remove User" name="removeUser">
                </form>
                <table>
                    <tr>
                        <th>username</th>
                        <th>phone</th>
                        <th>profile_description</th>
                        <th>year</th>
                        <th>major</th>
                        <th>color</th>
                        <th>twitter_username</th>
                        <th>permission</th>
                    </tr>

                <?php
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
                        <th>%s</th>
                    </tr>
                    ', $row["username"], $row["phone"], $row["profile_description"], $row["year"], $row["major"], $row["color"], $row["twitter_username"], $row["permission"]);
                }
                echo "<table>";
                break;
            case 2:
                // Community management script
                ?>
                    <table>
                        <tr>
                            <th>name</th>
                            <th>leader</th>
                        </tr>

                <?php
                $result = getAllFromTable($table);
                while ($row = $result->fetch_assoc()) {
                    echo sprintf('
                    <tr>
                        <th>%s</th>
                        <th>%s</th>
                    </tr>
                    ', $row["name"], $row["leader"]);
                }
                echo "<table>";
                break;
            case 3:
                // Community event management script
                ?>
                <table>
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
                break;
                case 4:
                    // Community member management script
                ?>
                <table>
                    <tr>
                        <th>entry_no</th>
                        <th>account_name</th>
                        <th>name</th>
                    </tr>

                <?php
                $result = getAllFromTable($table);
                while ($row = $result->fetch_assoc()) {
                    echo sprintf('
                    <tr>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                    </tr>
                    ', $row["entry_no"], $row["account_name"], $row["name"]);
                }
                echo "<table>";
                break;
                case 5:
                    // User event management script
                ?>
                    <table>
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
                        break;
                        case 6:
                            // User friend management script.
                        ?>
                        <table>
                            <tr>
                                <th>friendID</th>
                                <th>user1</th>
                                <th>user2</th>
                            </tr>

                            <?php
                            $result = getAllFromTable($table);
                            while ($row = $result->fetch_assoc()) {
                                echo sprintf('
                    <tr>
                        <th>%s</th>
                        <th>%s</th>
                        <th>%s</th>
                    </tr>
                    ', $row["friendID"], $row["user1"], $row["user2"]);
                            }
                            echo "<table>";
                            break;
                            case 8:
                                // Interest management script
                            ?>
                            <table>
                                <tr>
                                    <th>user</th>
                                    <th>interest</th>
                                </tr>

                                <?php
                                $result = getAllFromTable($table);
                                while ($row = $result->fetch_assoc()) {
                                    echo sprintf('
                                        <tr>
                                            <th>%s</th>
                                            <th>%s</th>
                                        </tr>
                                    ', $row["user"], $row["interest"]);
                                }
                                echo "<table>";
                                break;

        }



    }


    /**
     * Generates a mysqli result that is used to populate a table in browser
     * @param $table - A string that holds the table name
     * @return false|mysqli_result
     */
function getAllFromTable($table) {
        //connects to database
        //retrieves data and displays
        $db = db();
        $sql = "SELECT * FROM $table";
        $statement = $db->prepare($sql);
        $statement->execute();
        return $statement->get_result();
}

    ?>
</html>
