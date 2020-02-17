
<?php
session_start ();

include "../database.php";
function guidv4($data)
{
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

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
 * get_event_sum - Gets the sum of all the events that are currently created (maybe?)
 */
function get_event_sum($db) {
    $query = "SELECT SUM(TABLE_ROWS)
    FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = 'events';";
    if ($statement = $db->prepare($query)) {
        $statement->execute();
        $result = $statement->get_result();
        $row = $result->fetch_assoc();
        var_dump($row);
        return $row[0];
    }
}


/**
 * get_volunteer_amountr - Get's the amount of volunteers volutneering for said event
 * @param db Database Connection
 * @param event Event UUID
 */
function get_volunteer_amount($db, $event) {
    //Select all volunteers from event given by using count
    $query = 'SELECT COUNT(*) FROM volunteers WHERE event_uuid=?';
    if ($statement = $db->prepare($query)) {
        $statement->bind_param("s", $event); //Bind the event (safe way)
        $statement->execute(); //Execute query
        $result = $statement->get_result(); //Get the amount
        $row = $result->fetch_assoc(); //Get the single row of data
        return $row["COUNT(*)"]; //As its count, get the count amount of the volunteers (as theres no need to get ALL of the data)
    }
}

/**
 * Event Listings
 * @author Brendan Fuller 2020
 */

/**
 * create_event - Creates a new event to show on screen
 * @param uuid
 * @param title
 * @param location
 * @param volunteer_needed
 */
function create_event($uuid, $title, $location, $volunteers) {
  $badge = "warning";
  if ($volunteers > 0) {
    $badge = "success";
  } 

  return '<tr itemscope="" itemtype="http://data-vocabulary.org/Event">
  <td>
      <a href="../events/view/?event=' . $uuid . '" itemprop="url"><span itemprop="summary">' . htmlentities($title)  . '</span></a>
      <br>
      <small>' . htmlentities($location) . '</small>
  </td>
  <td class="hidden-xs text-center">
      <span class="badge badge-' .$badge . ' badge-pill">' . abs($volunteers) . '</span>
  </td>
</tr>';
}
/**
 * create_date - Creates a new group for the date of that event(s)
 * @param date - Date of the events (timestamp)
 * @param events - Array of events that are occuring on this date
 */
function create_date($db, $date, $events) {
    echo '<div style="margin-top: 15px;"><p><i>' . date('F d, Y',strtotime($date)) . ' </i></p>
    <table class="event-table">
        <colgroup>
            <col width="auto">
                <col width="10%">
        </colgroup>
        <thead>
            <tr>
                <th>Event</th>
                <th>Volunteers</th>
            </tr>
        </thead>
        <tbody>';
        foreach ($events as $event) {
            $volunteers= get_volunteer_amount($db, $event["uuid"]);
            echo create_event($event["uuid"],$event["title"], $event["location"], $volunteers); 
        } 


  echo '  </tbody>
  </table></div>';
}
/**
 * get_events - Get the list of events based on the page currently on
 */
function get_events($db, $page, $search) {
    $start = ($page * 5) - 5;
    $end = $start + 5;
    if (isset($_GET["past"])) {
        $query = "SELECT * FROM `events` WHERE ((title LIKE '%{$search}%' or location LIKE '%{$search}%') AND date < CURRENT_TIMESTAMP) ORDER BY date DESC LIMIT " . $start . "," . $end;
    } else {
        $query = "SELECT * FROM `events` WHERE ((title LIKE '%{$search}%' or location LIKE '%{$search}%') AND date > CURRENT_TIMESTAMP) ORDER BY date ASC LIMIT " . $start . "," . $end;
    }
    if ($stmt = $db->prepare($query)) {
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0 ) {
            $data = Array();
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        } else {
            return Array();
        }
        $stmt->close();
    }
    return Array();
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
/**
 * add_event - Add the event to the database
 * @param db Database Connection
 * @param creator_uuid Creator of the EVENT (as a USER)
 * @param name Name of the EVENT
 * @param location Location of the EVENT
 * @param time Time of EVENT as a TIMESTAMP
 * @param decsription Descritpion of the Event
 * @param icon The ICON of the EVENT as a base64
 */
function add_event($db, $creator_uuid, $name, $location, $time, $description, $icon) {
    $imgdata="";
    if ($icon["error"] != "4") {
        //Encode the image as a BASE64
        $imgdata = base64_encode(file_get_contents($icon['tmp_name'])); 
    }
    //Generate a uuid for the event to specify when viewing
    $uuid = guidv4(openssl_random_pseudo_bytes(16));
    $date = getDateForDatabase($time); //Get the proper date, this may mess up if the user sends the wrong date format but I don't care, the database should just not add the event
    $query = "INSERT INTO events (date, creator_uuid, title, location, description, uuid, icon) VALUES (?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("sssssss", $date, $creator_uuid, $name, $location, $description, $uuid, $imgdata);
        $stmt->execute();
        $stmt->close(); 
    }
}
//Start the current users session

//Set the defaults for this page
$title = "Cardinal Volunteer - Events";
$path = "../";

//Pageination Code
//Default Page is 1
$PAGE = 1;

//Custom limit of how many events can load per page. Newest events show first.
$EVENT_LIMIT=15;

//Let's check if the user has selected a different page
if (isset($_GET["page"])) {
    //Make sure the number is a valid number
    if (is_numeric($_GET["page"])) {
       $PAGE = $_GET["page"];  
    } 
}
$SEARCH = "";
if (isset($_GET["search"])) {
    $SEARCH = $_GET["search"];
}


$CREATE_EVENT_ERROR = null;

//Create a new event
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
        //Add the event if no error checking occured
        if ($pass == true) {
            add_event($db, $_SESSION["uuid"], $_POST["event_name"], $_POST["event_location"], $_POST["event_date"], $_POST["event_description"], $_FILES["event_icon"]);
        }
    }    
}
?>
    <html>
    <?php include_once  "../header.php" ?>
        <body>
            <?php include_once "../navigation.php"; ?>
                <div class="container tiles">
                    <?php 

                        if ($CREATE_EVENT_ERROR != null) {
                            echo '                <div class="alert alert-warning" role="alert">
                                Event Creation Error: <strong>' . $CREATE_EVENT_ERROR . '
                        </strong></div>';
                        }
                    ?>
                    <div class="row justify-content-center">
                        <div class="col-sm-10 col-md-12" id="upcoming-events">
                            <div class="jumbotron table">

                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h1 class="display-4">Events</h1>
                                    </div>
                                    <div class="d-flex">
                                        <form method="get" action=""  class="d-flex">
                                            <input type="text" class="form-control form-control-alternative" name="search" value="<?php echo $SEARCH; ?>" placeholder="Search">
                                            <div style="margin: 5px;">
                                                <button type="submit" class="btn btn-default btn-circle btn-lg" ><i class="ionicons ion-md-search"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                    <?php  
                                        //Create an arrory for the list of dates shown
                                        $event_dates = Array();
                                        //Now organize the 10 events within the dates given
                                        $data = get_events($db, $PAGE, $SEARCH);
                                        if (count($data) > 0) {
                                           foreach ($data as $value) {
                                            if (!isset($event_dates[$value["date"]])) {
                                                $event_dates[$value["date"]] = Array();
                                            }
                                            $event_dates[$value["date"]][] = $value;
                                            }
                                            //Now lets loop the new array, which is a multi-dimensional array
                                            foreach (($event_dates) as $key => $date) {
                                                create_date($db, $key, $date); //Start the creation of the date
                                            } 
                                        } else {
                                            echo '<div class="text-center">
                                                <h3>No Events Found</h3>
                                            </div>';
                                        }
                                        
                                    ?>
                                
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-center" style="margin-top: 15px;">
                                            <nav aria-label="...">
                                                <ul class="pagination">
                                                    <li class="page-item">
                                                        <a class="page-link" href="?page=<?php echo $PAGE - 1 > 0 ? $PAGE - 1 : 1; if (isset($_GET["past"])) {echo "&past";}?>" tabindex="-1">
                                                            <i class="ionicons ion-md-arrow-back"></i>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                    </li>
                                                    <li class="page-item active">
                                                        <a class="page-link" href="#"><?php echo $PAGE; ?> <span class="sr-only">(current)</span></a>
                                                    </li>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?page=<?php echo $PAGE + 1; if (isset($_GET["past"])) {echo "&past";}?>">
                                                            <i class="ionicons ion-md-arrow-forward"></i>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                    <div class="d-flex" style="margin: 10px;">
                                        <?php  if (isset($_SESSION["uuid"])) {
                                            echo '<button type="button" class="btn btn-info btn-lg" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateEvent">Create Event</button>';
                                    }?>
                                    </div>
                                </div>
                                <?php 

                                    if (isset($_GET["past"])) {
                                        echo ' <p><i>Looking for current events? <a href="../events">Click me!</a></i></p>';
                                    } else {
                                        echo ' <p><i>Looking for past events? <a href="?past">Click me!</a></i></p>';
                                    } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include_once "_modal.php"; ?>
                <?php include_once "../footer.php"; ?>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
                    <script src="../js/popper.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha256-WqU1JavFxSAMcLP2WIOI+GB2zWmShMI82mTpLDcqFUg=" crossorigin="anonymous"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
                    <script src="../js/argon.min.js"></script>
                    

        </body>

    </html>