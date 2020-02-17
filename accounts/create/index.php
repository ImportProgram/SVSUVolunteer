<?php

//Start the session for the current user
session_start ();

//Require the database file (from root)
include "../../database.php";

//Function to create a uuid that's the version 4
//Thanks to StackOverflow for this :D
function guidv4($data)
{
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


/**
 * check_username
 * @param db Database Connection
 * @param username Username
 */
function check_username($db, $username) {
    //Find if the username is real, if so grab the single row in the database
    $query = "SELECT uuid FROM users WHERE username=? LIMIT 1";
    //Prepare this query for execution
    if ($stmt = $db->prepare($query)) {
        //Add the username to prevent script injection via the bind operation
        $stmt->bind_param("s", $username);
        //Now fully execute
        $stmt->execute();
        //Grab the result of the query
        $result = $stmt->get_result();
        //Close the statment
        $stmt->close();
        //Check of the result is now more than one (meaning someone has that username)
        if (mysqli_num_rows($result) > 0) {
            return false;
        } else {
            return true;
        }  
    } 
}

/**
 * add_username Adds the user to the database (new user)
 * @param db Database Connection
 * @param username Username of the USER
 * @param first_name First name of the USER
 * @param last_name Last name of the USER
 * @param email Email of the USER
 * @param password Password of the USER
 */
function add_username($db, $username, $first_name, $last_name, $email, $password) {
    //First lets create a new UUID for the user to identify them by
    $uuid = guidv4(openssl_random_pseudo_bytes(16));
    //Next we create a query to add the user with the data required to insert into the database
    $query = "INSERT INTO users (username, first_name, last_name, email, password, uuid) VALUES (?, ?, ?, ?, ?, ?)";
    //Now we create a hash
    //TODO: Add a salt to this has as it would probably be a lot safer
    $hashToStoreInDb = password_hash($password, PASSWORD_BCRYPT);
    //Now prerpare for the query to execute
    if ($stmt = $db->prepare($query)) {
        //Bind the data, execute the query and close the statment
        $stmt->bind_param("ssssss", $username, $first_name, $last_name, $email, $hashToStoreInDb, $uuid);
        $stmt->execute();
        $stmt->close(); 
    } 
}


/**
 * Account Creation
 * @author Brendan Fuller 2020
 */

//Start the current users session

//Set the defaults for this page
$title = "Cardinal Volunteer - Home";
$path = "../../";

//Globals for indcations on the creation of a USER
$INVALID_USERNAME = false;
$INVALID_PASSWORD = false;
$INVALID_CONFIRM_PASSWORD = false;

$MISSING_USERNAME = false;
$MISSING_PASSWORD = false;
$MISSING_EMAIL = false;
$MISSING_FIRST_NAME = false;
$MISSING_LAST_NAME = false;
$MISSING_CONFIRM_PASSWORD = false;


//Globals for the indications of what data can be reused (e.g. Password Confirmation check was inccorect, don't need to retype username)
$USERNAME = "";
$EMAIL = "";
$FIRST_NAME= "";
$LAST_NAME="";
$PASSWORD = "";
$CONFIRM_PASSWORD= "";
$pass = false;

//Check if we have a new account wanting to be created
if (isset($_POST["submit"])) {
  //Now we check if they can actually make an account, by default its true
  $pass = true;
  //Now check if they have a username
  if (isset($_POST["username"])) {
    //Make sure the username is over 4 characters
    if (strlen($_POST["username"]) < 4) {
        //Update the Output
        $pass = false;
        $MISSING_USERNAME = true;
    } else {
        //If valid username size, check if the username has not been taken already
        if (!check_username($db, $_POST["username"])) {
            //Update the Oytput
            $pass = false;
            $MISSING_USERNAME = true;
            $INVALID_USERNAME = true;
        } else {
            //If we have a valid username, we will remove characters (this won't effect the outcome if the database doesn't allow for them anyways)
            $USERNAME = preg_replace('/\s/', '',$_POST["username"]);
        }
    }
  }
  //Check if we have a first name
  if (isset($_POST["first_name"])) {
    //Make sure the first name is over 4 characters
    if (strlen($_POST["first_name"]) < 4) {
        //Update the Output
        $pass = false;
        $MISSING_FIRST_NAME = true;
    } else {
        //Replace Spaces with nothing
        $FIRST_NAME = preg_replace('/\s/', '',$_POST["first_name"]);
    }
  }
  //Check if we have a last name
  if (isset($_POST["last_name"])) {
    
    if (strlen($_POST["last_name"]) < 4) {
        $pass = false;
        $MISSING_LAST_NAME = true;
    } else {
        $LAST_NAME = preg_replace('/\s/', '', $_POST["last_name"]);
    }
  
  }
  //Check if we have a email
  if (isset($_POST["email"])) {
    if (strlen($_POST["email"]) < 4) {
        $pass = false;
        $MISSING_EMAIL = true;
    } else {
        $EMAIL = preg_replace('/\s/', '', $_POST["email"]);
    }
  }
  //Check if we have a password
  if (isset($_POST["password"])) {
    if (strlen($_POST["password"]) < 4) {
        $pass = false;
        $MISSING_PASSWORD = true;
        $INVALID_PASSWORD = true;
    }
  }
  //Check if we have a password
  if (isset($_POST["confirm_password"])) {
    if (strlen($_POST["confirm_password"]) < 4) {
        $pass = false;
        $MISSING_CONFIRM_PASSWORD = true;
    }
    //Also check if the passsword matches the other password
    if (isset($_POST["password"])) {
        if ($_POST["password"] != $_POST["confirm_password"]) {
            $pass = false;
            $MISSING_CONFIRM_PASSWORD = true;
            $INVALID_CONFIRM_PASSWORD = true;
        }
    }
  }
  //If we have validated everything (meaning nothing has failed), we can continue
  if ($pass) {
    //Remove the spaces in the password
    $PASSWORD = preg_replace('/\s/', '', $_POST["password"]);
    //Create the new USER
    add_username($db, $USERNAME, $FIRST_NAME, $LAST_NAME, $EMAIL, $PASSWORD);
  }
}

?>
    <html>
    <?php include_once  "../../header.php" ?>
        <body>
            <?php include_once "../../navigation.php"; ?>
                <div class="container tiles">
                    <div class="row justify-content-center">
                        <div class="col-sm-4 col-md-7" id="upcoming-events">
                           <?php
                                if ($pass == false) {
                                    include_once "_create.php";
                                } else {
                                    echo '<div class="alert alert-success" role="alert">
                                    <strong>Account created successfully!</strong> You may now <a href="../access">Sign in!</a>
                                </div>';
                                }
                           ?>
                        </div>
                    </div>
                </div>
                <?php include_once "../../footer.php"; ?>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
                <script src="../../js/popper.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha256-WqU1JavFxSAMcLP2WIOI+GB2zWmShMI82mTpLDcqFUg=" crossorigin="anonymous"></script>
                <script src="../../js/argon.min.js"></script>
                <script>
                    //Checks if any input has pressed space, and if so don't allow it
                    $("input").on({
                        keydown: function(e) {
                                if (e.which === 32)
                                return false;
                        },
                        change: function() {
                                this.value = this.value.replace(/\s/g, "");
                        }
                    });
                 </script>
        </body>

    </html>