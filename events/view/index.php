<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Create the Current Session
session_start ();

//Include the Database File
include "../../database.php";

//Verify the username and password of current user
function has_permission($db, $uuid, $permission) {
    $query = "SELECT id FROM permissions WHERE user_uuid=? and type=? LIMIT 1";
    if ($statement = $db->prepare($query)) {
        $statement->bind_param("ss", $uuid, $permission);
        $statement->execute();
        $statement->store_result();
        if($statement->num_rows > 0) {
            $statement->close();
            return true;
        } else {
            $statement->close();
            return false;
        }
    } else {
        //Dump the database error, probably not a good idea but for development its okay
        echo var_dump($db->error);
    }
    return false;
} 

/**
 * get_event - Gets a single event by the UUID of that event
 * @param db - Database Object
 * @param uuid - UUID of event
 */
function get_event($db, $uuid) {
  $query = "SELECT title, location, description, creator_uuid, icon, date FROM events WHERE uuid=? LIMIT 1";
  if ($statement = $db->prepare($query)) {
      $statement->bind_param("s", $uuid);
      $statement->execute();
      $statement->store_result();
      if($statement->num_rows > 0) {
          $statement->bind_result($title, $location, $description, $creator, $icon, $date);
          $statement->fetch();
          $statement->close();
          return Array(
            "title" => $title,
            "location" => $location,
            "description" => $description,
            "creator" => $creator,
            "icon" => $icon,
            "date" => $date
          );
      }
      return true;
  }
} 

/**
 * create_volunteer - Creates HTML for the volunteer (in the table)
 * @param name - Public name from the account created
 * @param notes - Notes for admins for that event
 * @param phone - Phone number for admins to call you for this event
 * @param accepted - Have you been accepted to be a volunteer for this event?
 */
function create_volunteer($db, $admin, $uuid, $notes, $phone, $accepted) {
  
  //Custom icon for the accepted (Checkmark or Times)
  $icon = '<i class="ionicons ion-md-close" style="color: red;"></i>';
  if ($accepted == true) {
    $icon = '<i class="ionicons ion-md-checkmark" style="color: green;"></i>';
  } 

  $name_array = get_user($db, $uuid);
  $name = $name_array["first_name"] . " " . $name_array["last_name"];

  $EXTRA = "";
  $name = htmlentities($name);
  if ($admin == true) {
    $EXTRA = '<td class="hidden-xs text-center">
    ' . htmlentities($phone) . '
  </td>
  <td class="hidden-xs text-center">
    ' . htmlentities($notes) . '
  </td>';
   $name = '<a href="?event=' . $_GET["event"] . '&accepted=' .$uuid . '">' . $name . '</a>';
  }

  return '<tr itemscope="" itemtype="http://data-vocabulary.org/Event">
  <td>
      <a itemprop="url"><span itemprop="summary">'.  $name . '</span></a>
  </td>
  ' . $EXTRA .'
  <td class="hidden-xs text-center">
    ' . $icon . '
  </td>
</tr>';
}
function is_volunteer($db, $uuid, $event) {
  $query = "SELECT id FROM volunteers WHERE user_uuid=? AND event_uuid=? LIMIT 1";
  if ($statement = $db->prepare($query)) {
      $statement->bind_param("ss", $uuid, $event);
      $statement->execute();
      $statement->store_result();
      if($statement->num_rows > 0) {
         return true;
      }
      return false;
  }
  return false;
}
/**
 * add_volunteer Adds a VOLUNTEER (USER) to a EVENT
 * @param db Database Connection
 * @param uuid UUID for the VOLUTNEER (USER)
 * @param event Event UUID to specify the volutneer for that event
 * @param notes Notes for the event coordinator and the admins
 * @param phone The phone number f
 */
 function add_volunteer($db, $isVolunteer, $uuid, $event, $notes, $phone) {
  //Check if the user is not already a volunteer (so we don't have Joe VOLUNTEERING like 9 times)
  if ($isVolunteer == false) {
    //Create thw query to add the USER to the VOLUNTEER 
    $query = "INSERT INTO volunteers (user_uuid, event_uuid, notes, phone, accepted) VALUES (?, ?, ?, ?, 0)";
    //Create the prepare injection
    if ($stmt = $db->prepare($query)) {
        //Bind, execute and close the statement
        $stmt->bind_param("ssss", $uuid, $event, $notes, $phone);
        $stmt->execute();
        $stmt->close(); 
    }
  } 
}


function accept_volunteer($db, $uuid, $event) {
  $query = "UPDATE volunteers SET accepted = NOT accepted WHERE user_uuid=? AND event_uuid=?";
  if ($stmt = $db->prepare($query)) {
    //Bind, execute and close the statement
    $stmt->bind_param("ss", $uuid, $event);
    $stmt->execute();
    $stmt->close(); 
  }
}
function withdraw_volunteer($db, $uuid, $event) {
  $query = "DELETE FROM volunteers WHERE user_uuid=? AND event_uuid=?";
  if ($stmt = $db->prepare($query)) {
    //Bind, execute and close the statement
    $stmt->bind_param("ss", $uuid, $event);
    $stmt->execute();
    $stmt->close(); 
  }
}

/**
 * get_user - Get's a user first and last name
 * @param db Database Connect
 * @param uuid A UUID of a USER
 */
function get_user($db, $uuid) {
  //Crete the query to find the USER via the uuid given and select the name (first and last) of that USER
  $query = "SELECT first_name, last_name FROM users WHERE uuid=? LIMIT 1";
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
          $statement->bind_result($first_name, $last_name);
          $statement->fetch(); //Fetch the data 
          $statement->close(); //Close the statment because we are finished
          //Now lets return an array (but really an object) with the first name and last name
          return Array(
            "first_name" => $first_name,
            "last_name" => $last_name,
          );
      }
      return null;
  }
  return null;
}

function delete_event($db, $event) {
  $query = "DELETE FROM events WHERE uuid=?";
  //Create the prepare statment
  if ($statement = $db->prepare($query)) {
      //Bind the UUID of the USER to the query (because we don't want to have SQL injection). Execute it as well.
      $statement->bind_param("s", $event);
      $statement->execute();
  }
  $query = "DELETE FROM volunteers WHERE event_uuid=?";
  //Create the prepare statment
  if ($statement = $db->prepare($query)) {
      //Bind the UUID of the USER to the query (because we don't want to have SQL injection). Execute it as well.
      $statement->bind_param("s", $event);
      $statement->execute();
  }
}
/**
 * getDateForDatabase - Gets a date (usually a MM/DD/YY to a timestamp or datetime)
 * @param date
 */
function getDateForDatabase(string $date): string {
  $timestamp = strtotime($date);
  $date_formated = date('Y-m-d H:i:s', $timestamp);
  return $date_formated;
}
function update_event($db, $event, $name, $location, $time, $description, $icon) {
  $imgdata="";
  if ($icon["error"] != "4") {
      //Encode the image as a BASE64
      $imgdata = base64_encode(file_get_contents($icon['tmp_name'])); 
  }
  if (empty($imgdata)) {
    $imgdata = null;
  }
  //Generate a uuid for the event to specify when viewing
  $date = getDateForDatabase($time); //Get the proper date, this may mess up if the user sends the wrong date format but I don't care, the database should just not add the event
  $query = "UPDATE events SET date=COALESCE(?,date), title=COALESCE(?,title), location=COALESCE(?,location), description=COALESCE(?,description), icon=COALESCE(?,icon) WHERE uuid=?";
  if ($stmt = $db->prepare($query)) {
      $stmt->bind_param("ssssss", $date, $name, $location, $description, $imgdata, $event);
      $stmt->execute();
      $stmt->close(); 
  }
}
$CREATE_EVENT_ERROR = null;
if (isset($_POST["create"])) {
  //Do we have a user thats signned in?
  if (isset($_SESSION["uuid"])) {
    $pass = true;
    //Event name?
    if (strlen($_POST["event_name"]) < 5) {
        $pass = false;
        $CREATE_EVENT_ERROR = "Invalid name!";
    }
    //Event location?
    if (strlen($_POST["event_location"]) < 5) {
        $pass = false;
        $CREATE_EVENT_ERROR = "Invalid location!";
    }
    //Event Date?
    if ((strlen($_POST["event_date"]) < 6)) {
        $pass = false;
        $CREATE_EVENT_ERROR = "Invalid date!";
    } else {
        //Check if the date is AFTER today (this is kind of buggy (it doesn't allow for TODAY to be made sadly))
        if (new DateTime() > new DateTime($_POST["event_date"])) {
            $CREATE_EVENT_ERROR = "Can't create event in the past!";                
        }
    }
    //Event icon 
    //TODO: Check for the event data type (png or jpeg)
    if (isset($_FILES["event_icon"])) {
        if ($_FILES["event_icon"]["size"] > 64000) {
            $pass = false;
            $CREATE_EVENT_ERROR = "Invalid Icon, size is over 64k!";
        }  
    }
    if (!isset($_GET["event"])) { 
      $pass = false;
    }
    //Add the event if no error checking occured
    if ($pass == true) {
        update_event($db, $_GET["event"], $_POST["event_name"], $_POST["event_location"], $_POST["event_date"], $_POST["event_description"], $_FILES["event_icon"]);
    }
  }    
}



//Set the defaults for this page

$path = "../../";




//Check if we have an event "?events"
if (isset($_GET["event"])) {
  //Check if the event is in the database
  $event = get_event($db, $_GET["event"]);
  //If the title is bothing, its not a real event
  if ($event["title"] == null) { 
      //Redirect the user to the events page
      header('Location: ../');
      die;
  //if the event is real, lets update the page title with the title of the event
  } else {
    $title = "Cardinal Volunteer - " . $event["title"];
    $creator = get_user($db, $event["creator"]);
  }
} else {
  header('Location: ../');
  //Kill the current process as the data was incorrect and other code below may not break if used.
  die;
}


$IS_CREATOR = false;
if (isset($_SESSION["uuid"])) {
  $isVolunteer = is_volunteer($db, $_SESSION["uuid"], $_GET["event"]);
  if ($_SESSION["uuid"] == $event["creator"]) {
    $IS_CREATOR = true;
  }
}

//Check if someone has applied to this event
if (isset($_POST["apply"])) {
  if (isset($_SESSION["uuid"]) && new DateTime() < new DateTime($event["date"])) {
    add_volunteer($db, $isVolunteer, $_SESSION["uuid"], $_GET["event"], $_POST["notes"], $_POST["phone"]);
    header("Refresh: 0");
  } else {
    echo "Error: You can't add volunteers to event that has passed"; //Message to tell the user, we know that your trying to be sneaky
  }
}


if (isset($_SESSION["uuid"]) && isset($_POST["withdraw"]) && new DateTime() < new DateTime($event["date"])) {
  withdraw_volunteer($db,$_SESSION["uuid"], $_GET["event"]);
  header("Refresh: 0");
}
//Accept a volunteer by making sure they have a uuid and the date is not past the creation time
if ($IS_CREATOR && isset($_SESSION["uuid"]) && isset($_GET["accepted"]) && new DateTime() < new DateTime($event["date"])) {
  accept_volunteer($db,$_GET["accepted"], $_GET["event"]);
}
//Same as adding a volunteer, but make sure the creator can only delete and it before the event date
if ($IS_CREATOR && isset($_SESSION["uuid"]) && isset($_POST["delete"]) && new DateTime() < new DateTime($event["date"])) {
  delete_event($db, $_GET["event"]);
  header("Location: ../");
}
?>
  <html>
    <?php include_once  "../../header.php" ?>
        <body>
            <?php include_once "../../navigation.php"; ?>
            <div class="container tiles">
            <?php 

              if ($CREATE_EVENT_ERROR != null) {
                  echo '                <div class="alert alert-warning" role="alert">
                      Event Creation Error: <strong>' . $CREATE_EVENT_ERROR . '
              </strong></div>';
              }
              if (!isset($_SESSION["uuid"]) != null) {
                echo '                <div class="alert alert-primary" role="alert">
                    Volunteering for an event reqiures you to be <a href="../../accounts/access/" style="color: black;">signed in.</a>
            </strong></div>';
            }
              ?> 
              <div class="row justify-content-center">
               <div class="col-sm-12 col-md-4 col-lg-4">
                  <div class="jumbotron">
                     <div class="text-center ">
                        <?php  
                            if ($event["icon"] != "") {
                              echo  '<img src="data:image/png;base64,'. $event["icon"] .'">';
                            } else {
                              echo '<div class="event-icon-placholder d-flex justify-content-center" style="margin: 0 auto; padding: 0 auto; padding-top: 15px;">
                                <i style="color: white; font-size: 70px;" class="ion ion-md-information-circle-outline"></i>
                              </div>';
                            }
                        ?>
                        <hr />
                        <div style="margin: 5px; margin-top: 10px;">
                       <h6 style="font-weight: bold">Event Description</h6>
                     </div>
                     </div>
                     <div style="border: 1px solid lightgray; border-radius: 5px; padding: 5px">
                          <p> <?php 
                          
                          echo nl2br(htmlentities($event["description"])); ?></p>
                     </div>
                     <hr />
                     <div class="text-center" style="margin-top: 10px;">
                       <h6 style="font-weight: bold">Created By</h6>
                       <h6><?php echo htmlentities($creator["first_name"] . " " . $creator["last_name"]); ?></h6>
                     </div>               
                  </div>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-8">
                  <div class="jumbotron table">
                    <div class="d-flex justify-content-between">
                        <div>
                          <h1 class="display-4"><?php echo htmlentities($event["title"]); ?> <span style="color: gray; font-size: 16px;"> <?php echo htmlentities($event["location"]); ?></span></h1>
                          
                        </div>
                        <div class="d-flex">
                          <form method="post" action="?event=<?php echo $_GET["event"]; ?>"  class="d-flex">
                            <?php 

                              //Show buttons for creators or volunteers
                              if ($IS_CREATOR && new DateTime() < new DateTime($event["date"])) {
                                echo '<button type="button" class="btn btn-danger btn-lg" class="btn btn-primary" data-toggle="modal" data-target="#modalDelete">Delete</button>';
                              }
                              if ($IS_CREATOR) {
                                echo '<button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#modalCreateEvent">Edit</button>';
                              }
                              if (isset($_SESSION["uuid"]) && new DateTime() < new DateTime($event["date"]) && $isVolunteer == false) {
                                echo '<button type="button" class="btn btn-info btn-lg" class="btn btn-primary" data-toggle="modal" data-target="#modalVolunteer">Volunteer</button>';
                              }
                              if (isset($_SESSION["uuid"]) && new DateTime() < new DateTime($event["date"]) && $isVolunteer == true) {
                                echo '<button type="submit" name="withdraw" class="btn btn-warning btn-lg">Withdraw</a>';
                              }
                            ?>
                          </form>
                        </div>
                        </div>
                        <table class="event-table">
                          <colgroup>
                            <col width="auto">
                            <col width="20%">
                            <col width="20%">
                            <col width="10%">
                          </colgroup>
                          <thead>
                             <tr>
                                <th>Volunteer(s)</th>
                                <?php if ($IS_CREATOR) {
                                  echo ' <th>Phone</th> <th>Notes</th>';
                                }?>
                                <th>Accepted</th>
                              </tr>
                          </thead>
                          <tbody>
                            <?php    
                                    //Create a query to find all volunteers for that event
                                    $query = "SELECT * FROM `volunteers` WHERE event_uuid=?";
                                    //Execute the query safely
                                    if ($statement = $db->prepare($query)) {
                                        $statement->bind_param("s", $_GET["event"]);
                                        $statement->execute();
                                        $result = $statement->get_result();
                                        if($result->num_rows > 0 ) {
                                            //Now lets fetch all of the VOLUTNEERS (as a row) and call the create_volunteer function to
                                            while($row = $result->fetch_assoc()) {
                                              echo create_volunteer($db, $IS_CREATOR, $row["user_uuid"], $row["notes"], $row["phone"], $row["accepted"]);
                                            }
                                          
                                        }
                                        $statement->close();
                                    } 
                                   ?>
                                    
                          </tbody>
                        </table>
                    </div>
                </div>
               <?php if ($IS_CREATOR) {
                 include_once "../_modal.php";
               }?>
            </div>
        </div>
    </div>
    <?php include_once "_volunteer.php"; ?>
    <?php include_once "_delete.php"; ?>
    <?php include_once "../../footer.php"; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha256-WqU1JavFxSAMcLP2WIOI+GB2zWmShMI82mTpLDcqFUg=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
    <script src="../../js/argon.min.js"></script>
  </body>
</html>