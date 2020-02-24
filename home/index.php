<?php
//Start the sessiion
session_start ();

//Set Defaults
$title = "Cardinal Volunteer - Home";
$path = "../";


//TODO: Fix the force set action as HTML forms are garbage
$ACTION = "https://svsu.importprogram.me/volunteer/accounts/access/";
$ERROR = false; //Do we have an error for the Access Page (not used by Home but needed as it will error)
?>
<html>
    <?php include_once  "../header.php" //Include the header ?>
    <body>
        <?php include_once "../navigation.php"; ?>
        <div class="container tiles">
            <?php
                if (isset($_GET["logout"])) {
                    echo ' <div class="alert alert-info" role="alert">
                    <strong>You have been signed out succesfully</strong>
                </div>';
                }
                if (isset($_SESSION["uuid"])) {
                    echo ' <div class="alert alert-primary" role="alert">
                    Signed in as: <strong>' .$_SESSION["username"] . '</strong>
                </div>';
                }
            ?>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-6" id="upcoming-events">
                    <?php include_once "../events/_upcoming.php"; ?>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-1"></div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-5">
                            <?php    
                            //Check if the user is signed in, if so display the custom home page
                            if (isset($_SESSION["uuid"])) { 
                                include_once "_volunteering.php";
                                include_once "_volunteered.php";
                            } else {include_once "../accounts/access/_access.php"; }?>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once "../footer.php"; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
        <script src="../js/popper.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha256-WqU1JavFxSAMcLP2WIOI+GB2zWmShMI82mTpLDcqFUg=" crossorigin="anonymous"></script>
        <script src="../js/argon.min.js"></script>
    </body>
</html>
