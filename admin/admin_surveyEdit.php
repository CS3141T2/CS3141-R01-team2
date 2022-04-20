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
    <title>Survey Editor; Tech Meets Tech</title>
</head>
<body>
<div class="container">
    <div class="col text-center" style="margin: 1em auto;">
        <h2>Manage Survey</h2>
        <?php
        // Add question to survey
        if (isset($_POST["addQuestion"])) {
            $toInsert = $_POST["questionToAdd"];
            $view = 7;
            $table = "surveyQuestions";
            if ($toInsert == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Field can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("INSERT INTO surveyQuestions(questionText) VALUES (?)");
                    $stmt->bind_param("s", $toInsert);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Survey question added</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not add survey question</div>";
                    }
                } catch (Exception $e) {
                    echo 'Error: caught exception ', $e->getMessage(), '\n';
                }
            }
        }
        // Remove question to survey
        if (isset($_POST["removeQuestion"])) {
            $toInsert = $_POST["questionToRemove"];
            if ($toInsert == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Field can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("DELETE FROM surveyQuestions WHERE questionID=?");
                    $stmt->bind_param("i", $toInsert);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Survey question deleted</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not delete survey question</div>";
                    }
                } catch (Exception $e) {
                    echo 'Error: caught exception ', $e->getMessage(), '\n';
                }
            }
        }
        // Add interest to question
        if (isset($_POST["addInterest"])) {
            $toInsert = $_POST["interestToAdd"];
            $toInsert1 = $_POST["questionID"];
            if ($toInsert == null || $toInsert1 == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Fields can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("INSERT INTO surveyInterests VALUES (?, ?)");
                    $stmt->bind_param("si", $toInsert, $toInsert1);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Interest added</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not add interest</div>";
                    }
                } catch (Exception $e) {
                    echo 'Error: caught exception ', $e->getMessage(), '\n';
                }
            }
        }
        // Remove interest from question
        if (isset($_POST["removeInterest"])) {
            $toInsert = $_POST["interestToRemove"];
            if ($toInsert == null) {
                echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Fields can't be null</div>";
            } else {
                try {
                    $db = db();
                    $stmt = $db->prepare("DELETE FROM surveyInterests WHERE interest=?");
                    $stmt->bind_param("s", $toInsert);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success' style='max-width: 50%; margin: 1em auto'>Interest removed</div>";
                    } else {
                        echo "<div class='alert alert-danger' style='max-width: 50%; margin: 1em auto'>Did not remove interest</div>";
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

    <div class="row text-center" style="margin: 1em auto">
        <!-- Add question table -->
        <div class="col text-center" style="max-width: 30em; margin: auto">
            <h5>Adding Questions:</h5>
            <table class='table table-bordered text-center' id='table'>
                <thead>
                <tr>
                    <th>Question</th>
                    <th>Submit</th>
                </tr>
                </thead>
                <tbody>
                <form method="post">
                    <tr>
                        <td style="width: 50%;">
                            <input type="text" id="questionToAdd" name="questionToAdd">
                        </td>
                        <td>
                            <?php echo mat_but_submit('', 'Add Question', 'addQuestion', 'edit_note', '', '', false); ?>
                        </td>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
        <!-- Remove question table -->
        <div class="col text-center" style="max-width:30em; margin: auto">
            <h5>Removing Questions:</h5>
            <table class='table table-bordered text-center' id='table'>
                <thead>
                <tr>
                    <th>Question ID</th>
                    <th>Submit</th>
                </tr>
                </thead>
                <tbody>
                <form method="post">
                    <tr>
                        <td style="width: 50%;">
                            <input type="text" id="questionToRemove" name="questionToRemove">
                        </td>
                        <td>
                            <?php echo mat_but_submit('', 'Remove Question', 'removeQuestion', 'edit_off', '', '', false); ?>
                        </td>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row text-center" style="margin: 1em auto">
        <!-- Add interest table -->
        <div class="col text-center" style="max-width: 50em; margin: auto">
            <h5>Adding Interests:</h5>
            <table class='table table-bordered text-center' id='table'>
                <thead>
                <tr>
                    <th>Interest</th>
                    <th>Question ID</th>
                    <th>Submit</th>
                </tr>
                </thead>
                <tbody>
                <form method="post">
                    <tr>
                        <td style="width: 35%;">
                            <input type="text" id="interestToAdd" name="interestToAdd">
                        </td>
                        <td style="width: 35%;">
                            <input type="text" id="leaderToAdd" name="leaderToAdd">
                        </td>
                        <td>
                            <?php echo mat_but_submit('', 'Add Interest', 'addInterest', 'interests', '', '', false); ?>
                        </td>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
        <!-- Remove interest table -->
        <div class="col text-center" style="max-width:30em; margin: auto">
            <h5>Removing Interests:</h5>
            <table class='table table-bordered text-center' id='table'>
                <thead>
                <tr>
                    <th>Interest</th>
                    <th>Submit</th>
                </tr>
                </thead>
                <tbody>
                <form method="post">
                    <tr>
                        <td style="width: 50%;">
                            <input type="text" id="interestToRemove" name="interestToRemove">
                        </td>
                        <td>
                            <?php echo mat_but_submit('', 'Remove Interest', 'removeInterest', 'not_interested', '', '', false); ?>
                        </td>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col text-center" style="max-width: 50%;margin: 1em auto">
        <h5>Questions</h5>
        <!-- surveyQuestions table -->
        <table class="table table-striped">
            <thead class="thead-dark">
            <tr>
                <th scope="col">questionID</th>
                <th scope="col">questionText</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $table = "surveyQuestions";
            $result = getAllFromTable($table);
            while ($row = $result->fetch_assoc()) {
                echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>
                    ', $row["questionID"], $row["questionText"]);
            }
            ?>
            </tbody>
        </table>
        <h5>Interests</h5>
        <!-- surveyInterests table -->
        <table class="table table-striped">
            <thead class="thead-dark">
            <tr>
                <th scope="col">interest</th>
                <th scope="col">questionAssoc</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $table = "surveyInterests";
            $result = getAllFromTable($table);
            while ($row = $result->fetch_assoc()) {
                echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>
                    ', $row["interest"], $row["questionAssoc"]);
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
