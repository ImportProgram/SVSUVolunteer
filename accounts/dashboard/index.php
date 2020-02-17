<?php
session_start();
$title = "Cardinal Volunteer - Dashboard";
$path = "../../";


if (!isset($_SESSION["uuid"])) {
    header("Location: ../../home");
}

include "../../database.php";

//Verify the username and password of current user
function verify_username($db, $username, $password) {
    $query = "SELECT password, uuid FROM users WHERE username=? LIMIT 1";
    if ($statement = $db->prepare($query)) {
        $statement->bind_param("s", $username);
        $statement->execute();
        $statement->store_result();
        if($statement->num_rows > 0) {
            $statement->bind_result($hash, $uuid);
            $statement->fetch();
            if (password_verify($password, $hash)) {
               $_SESSION["uuid"] = $uuid;
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

//Check if there is an error, with saving.
$ERROR = false;
if (isset($_REQUEST["submit"])) {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $ERROR = verify_username($db, $_POST["username"], $_POST["password"]);
    }
}



function get_user($db, $uuid) {
    //Crete the query to find the USER via the uuid given and select the name (first and last) of that USER
    $query = "SELECT first_name, last_name, email, username, password FROM users WHERE uuid=? LIMIT 1";
    //Create the prepare statment
    if ($statement = $db->prepare($query)) {
        //Bind the UUID of the USER to the query (because we don't want to have SQL injection). Execute it as well.
        $statement->bind_param("s", $uuid);
        $statement->execute();
  
        //Now lets store the results
        $statement->store_result();
        //Check if we have a user thats real (because we need to know of this)
        if($statement->num_rows > 0) {
            //Nowe we grab the results and save them to a local variable reference
            $statement->bind_result($first_name, $last_name, $email, $username, $password);
            $statement->fetch(); //Fetch the data 
            $statement->close(); //Close the statment because we are finished
            //Now lets return an array (but really an object) with the first name and last name
            return Array(
              "first_name" => $first_name,
              "last_name" => $last_name,
              "email" => $email,
              "username" => $username,
              "hash" => $password
            );
        }
        return null;
    }
    return null;
  }


function update_user($db, $uuid, $fname, $lname, $email, $hash) {
    $query = "UPDATE users SET first_name=?, last_name=?, email=?, password=? WHERE uuid=?";
    if ($stmt = $db->prepare($query)) {
      //Bind, execute and close the statement
      $stmt->bind_param("sssss", $fname, $lname, $email, $hash, $uuid);
      $stmt->execute();
      $stmt->close(); 
    }
  }
  

$PASSWORD_ERROR = false;
$INVALID_CURRENT_PASSSWORD = false;
$INVALID_NEW_PASSWORD = false;

//Check if we have a user account (which is signed in)
if (isset($_SESSION["uuid"])) {
    $user = get_user($db, $_SESSION["uuid"]);
} else {
    header("Location ../");
    die;
}
if (isset($_POST["submit"]) && isset($_SESSION["uuid"])) {
    //Now we check if they can actually make an account, by default its true
    $pass = true;
    //Check if we have a first name
    if (isset($_POST["first_name"])) {
      //Make sure the first name is over 4 characters
      if (strlen($_POST["first_name"]) >= 4) {
        $FIRST_NAME = preg_replace('/\s/', '',$_POST["first_name"]);
        $user["first_name"] = $FIRST_NAME;
      }
    }
    //Check if we have a last name
    if (isset($_POST["last_name"])) {
        if (strlen($_POST["last_name"]) >= 4) {
            $FIRST_NAME = preg_replace('/\s/', '',$_POST["last_name"]);
            $user["last_name"] = $FIRST_NAME;
        }
    
    }
    //Check if we have a email
    if (isset($_POST["email"])) {
        if (strlen($_POST["email"]) >= 4) {
            $FIRST_NAME = preg_replace('/\s/', '',$_POST["email"]);
            $user["email"] = $FIRST_NAME;
        }
    }
    //Check if we have a password
    if (isset($_POST["current_password"])) {
        if (!empty($_POST["current_password"])) {
            if (password_verify($_POST["current_password"], $user["hash"])) {
                if (isset($_POST["confirm_password"])) {
                    if (strlen($_POST["confirm_password"]) < 4) {
                        $PASSWORD_ERROR = true;
                    }
                    //Also check if the passsword matches the other password
                    if (isset($_POST["password"]) && $PASSWORD_ERROR == false) {
                        if ($_POST["password"] != $_POST["confirm_password"]) {
                            $PASSWORD_ERROR = true;
                            $INVALID_NEW_PASSWORD = true;
                        } else {
                            $PASSWORD = preg_replace('/\s/', '', $_POST["password"]);
                            $user["hash"] = password_hash($PASSWORD, PASSWORD_BCRYPT);
                        }
                    }
                }
            } else {
                $PASSWORD_ERROR = true;
            }
        }
    }
    update_user($db, $_SESSION["uuid"], $user["first_name"], $user["last_name"], $user["email"], $user["hash"]);
}
?>
    <html>
    <?php include_once  "../../header.php" ?>

        <body>
            <?php include_once "../../navigation.php"; ?>
                <div class="container tiles">
                    <div class="row justify-content-center">
                        <div class="col-sm-8 col-md-8">
                           <?php include_once "_dashboard.php"; ?>
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