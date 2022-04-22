<?php
include '../tmt.php';
session_start();
$db = db();
if (!isset($_SESSION["username"])) {
    header("Location: /login");
    die();
}
$user = $_SESSION["username"];
try {
    $stmt = $db->prepare("SELECT interest FROM interests WHERE user=?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
} catch (Exception $e) {
    echo 'Error: caught exception ', $e->getMessage(), '\n';
}
$result = $stmt->get_result();
$rowCount = $result->num_rows;
if ($rowCount == 0) {
    header("Location: /Survey");
} else {
    ?>

    <html lang="us">
    <head>
        <?php echo head_goodies(); ?>
        <title>Confirmation</title>
    </head>
    <body>
        <div class="row text-center" style="margin: 1em auto">
            <h4>It looks like you already took the survey!</h4>
        </div>
        <div class="row text-center" style="margin: 1em auto">
            <h5>If you take the survey again, your current interests will be replaced. Is this okay?</h5>
        </div>
        <div class="row text-center" style="margin: 1em auto">
            <span style="max-width: 50em ; margin: auto">
                <form method="post" action="userUpdate.php">
                    <?php echo mat_but_submit('', 'Yes', 'Yes', 'done', '', '', false); ?>
                </form>
                <form method="post" action="userUpdate.php">
                    <?php echo mat_but_submit('', 'No', 'No', 'close', '', '', false); ?>
                </form>
            </span>
        </div>
    </body>
    </html>

<?php
}