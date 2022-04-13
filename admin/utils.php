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

