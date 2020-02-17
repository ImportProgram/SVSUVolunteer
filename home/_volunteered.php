<?php
/**
 * Volutneered Events
 * @author Brendan Fuller 2020
 */

if (!isset($_SESSION["uuid"])) {
  die;
}


?>
<div class="jumbotron table">
  <h1 class="display-4">Volunteered</h1>
      <?php  
         $query = "SELECT events.title, events.location, events.uuid FROM volunteers INNER JOIN events ON volunteers.event_uuid = events.uuid WHERE volunteers.user_uuid = ? AND events.date < CURRENT_TIMESTAMP";
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