<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start ();
$title = "Cardinal Volunteer - Account Access";
$path = "../../";

include "../../database.php";

//Verify the username and password of current user
function verify_username($db, $username, $password) {
    $query = "SELECT password, uuid, username FROM users WHERE username=? LIMIT 1";
    if ($statement = $db->prepare($query)) {
        $statement->bind_param("s", $username);
        $statement->execute();
        $statement->store_result();
        if($statement->num_rows > 0) {
            $statement->bind_result($hash, $uuid, $username);
            $statement->fetch();
            if (password_verify($password, $hash)) {
               $_SESSION["uuid"] = $uuid;
               $_SESSION["username"] = $username;
               header('Location: ../../home');
               return false;
            } else {
                return true;
            }
            $statement->close();
        }
        return true;
    } else {
        //Dump the database error, probably not a good idea but for development its okay
        echo var_dump($db->error);
    }
} 

$ERROR = false;
if (isset($_REQUEST["submit"])) {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $ERROR = verify_username($db, $_POST["username"], $_POST["password"]);
    }
}
$ACTION = "";
print $ACTION;

?>
    <html>
    <?php include_once  "../../header.php" ?>

        <body>
            <?php include_once "../../navigation.php"; ?>
                <div class="container tiles">
                    <div class="row justify-content-center">
                        <div class="col-sm-8 col-md-8">
                           <?php include_once "_access.php"; ?>
                        </div>
                    </div>
                </div>
                <?php include_once "../../footer.php"; ?>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
                    <script src="../../js/popper.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha256-WqU1JavFxSAMcLP2WIOI+GB2zWmShMI82mTpLDcqFUg=" crossorigin="anonymous"></script>
                    <script src="../../js/argon.min.js"></script>
        </body>

    </html>