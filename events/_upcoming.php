

<?php
include "../database.php";
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
   * create_date - Creates a new group for the date of that event(s)
   * @param db Database Connection
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
  function get_events($db, $page) {
      $start = ($page * 10) - 10;
      $end = $start + 10;
      $query = "SELECT * FROM `events` WHERE date > CURRENT_TIMESTAMP ORDER BY date ASC LIMIT " . $start . "," . $end;
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
?>



<div class="jumbotron table">
    <h1 class="display-4">Upcoming Events</h1>
    <?php  
                                        //Create an arrory for the list of dates shown
                                        $event_dates = Array();
                                        //Now organize the 10 events within the dates given
                                        $data =get_events($db, 1);
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

</div>