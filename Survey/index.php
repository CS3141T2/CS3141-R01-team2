<!DOCTYPE html>
<html>
<?php
include '/home/techzrla/tmt.php';
session_start();
?>
<head>
    <title>Interest Survey; Tech Meets Tech</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" />
</head>
<body>
<br>
<div class="container" style="width:600px;">
    <h2 align="center">Select your Interests!</h2>
    <br>
    <form method="post" id="hobby_form">
        <div class="form-group">
            <!--
            <label>What are your favorite hobbies?</label>
            <select id="hobby" name="hobby[]" multiple class="form-control" >
                <option value="Video_Games">Video Games</option>
                <option value="Sports">Sports</option>
                <option value="Music">Music</option>
                <option value="Running">Running</option>
                <option value="Hiking">Hiking</option>
                <option value="Movies">Movies</option>
                <option value="Travel">Travel</option>
                <option value="Cooking">Cooking</option>
            </select>
            <br><br>
            -->
            <?php
            $table = "surveyQuestions";
            $result1 = getAllFromTable($table);
            if ($result1->num_rows > 0) {
                while ($row1 = $result1->fetch_assoc()) {
                    $qNum = $row1["questionID"];
                    echo sprintf('<label>%s</label>
            <select id="hobby%u" name="hobby[]" multiple class="form-control" >', $row1["questionText"], $qNum);
                    $result2 = getAllInterests($qNum);
                    if ($result2->num_rows > 0) {
                        while ($row2 = $result2->fetch_assoc()) {
                            $interest = $row2["interest"];
                            echo sprintf('<option value="%s">%s</option>', $interest, $interest);
                        }
                    }
                    echo "</select>";
                    echo sprintf("<script>
                        $(document).ready(function(){
                            $('#hobby%u').multiselect({
                                nonSelectedText: 'Select Interests',
                                enableFiltering: true,
                                enableCaseInsensitiveFiltering: true,
                                buttonWidth:'400px'
                            });
                        });
                    </script><br><br>", $qNum);
                }
            }

            ?>
        </div>
        <div class="form-group">
            <form action="surveyUpdate.php">
                <input type="submit" class="btn btn-info" name="submit" value="Submit">
            </form>
        </div>
    </form>
    <br>
</div>
</body>
</html>

<!--
<script>
    $(document).ready(function(){
        $('#hobby2').multiselect({
            nonSelectedText: 'Select Hobbies',
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth:'400px'
        });
    });
</script>
-->
<?php
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

/**
 * @param $num - the interests associated with the questionID
 * @return false|mysqli_result
 */
function getAllInterests($num) {
    $db = db();
    $stmt = $db->prepare("SELECT interest FROM surveyInterests WHERE questionAssoc=?");
    $stmt->bind_param("i", $num);
    $stmt->execute();
    return $stmt->get_result();
}
?>
