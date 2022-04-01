<html>
<form action="ProjectLogin.php" method="post">
    <?php
    if (!isset($_SESSION["username"])) {
        header("LOCATION:index.php");
    } else {
        echo "Welcome ". $_SESSION["username"];
        echo "Click on the table you would like to edit:"
        ?>
        <input type="submit" value='logout' name='logout'>
        <?php
    }
    ?>
</form>
<?php
    if (isset($_POST["Back"]) || !isset($_SESSION['view'])) {
        $_SESSION['view'] = 0;
    } else {
            if (isset($_POST["Manage Accounts"])) {
                echo "Done1";
            }
            if (isset($_POST["Manage Communities"])) {
                echo "Done2";
            }
            if (isset($_POST["Manage Community Events"])) {
                echo "Done3";
            }
            if (isset($_POST["Manage Community Members"])) {
                echo "Done4";
            }
            if (isset($_POST["Manage User Events"])) {
                echo "Done5";
            }
            if (isset($_POST["Manage User Friends"])) {
                echo "Done6";
            }
        }
?>
<form action="admin_dash.php" method="post">
    <?php
    if ($_SESSION['view'] != 1) {
    ?>
    <input type="submit" value="Manage Accounts" name="Manage Accounts">
    <input type="submit" value="Manage Communities" name="Manage Communities">
    <input type="submit" value="Manage Community Events" name="Manage Community Events">
    <input type="submit" value="Manage Community Members" name="Manage Community Members">
    <input type="submit" value="Manage User Events" name="Manage User Events">
    <input type="submit" value="Manage User Friends" name="Manage User Friends">
    <?php
    } else {
        ?>
    <input type="submit" value='back' name='Back'>
    <?php
    }
    ?>
</form>

</html>
