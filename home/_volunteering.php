

<?php
if (!isset($_SESSION["uuid"])) {
  die;
}

/**
 * Volutneering Events
 * @author Brendan Fuller 2020
 */

/**
 * create_event - Creates a new event to show on screen
 * @param uuid
 * @param title
 * @param location
 * @param volunteer_needed
 */
function create_volunteer_event($uuid, $title, $location) {
    return '<tr itemscope="" itemtype="http://data-vocabulary.org/Event">
    <td>
        <a href="../events/view/?event=' . $uuid . '" title="' . htmlentities($title) . '" itemprop="url"><span itemprop="summary">' . htmlentities($title)  . '</span></a>
        <br>
        <small>' . htmlentities($location) . '</small>
    </td>
  </tr>';
  }
?>

<div class="jumbotron table">
  <h1 class="display-4">Volunteering</h1>
      <?php  
         $query = "SELECT events.title, events.location, events.uuid FROM volunteers INNER JOIN events ON volunteers.event_uuid = events.uuid WHERE volunteers.user_uuid = ? AND events.date > CURRENT_TIMESTAMP";
         if ($statement = $db->prepare($query)) {
             $statement->bind_param("s", $_SESSION["uuid"]);
             $statement->execute();
             $result = $statement->get_result();
             if($result->num_rows > 0 ) {
               echo '<table class="event-table">
               <colgroup>
                 <col width="auto">
               </colgroup>
               <thead>
                 <tr>
                   <th>Event</th>
                 </tr>
               </thead>
               <tbody>';
                 $data = Array();
                 while($row = $result->fetch_assoc()) {
                
                   echo create_volunteer_event($row["uuid"], $row["title"], $row["location"]);
                 }
                 echo '</tbody>';
             } else {
                 echo "You are not in any events";
             }
             $statement->close();
         }                                                          
      ?>
  </table>
</div>