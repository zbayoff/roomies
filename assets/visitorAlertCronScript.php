<?php 

chdir(dirname(__FILE__));

include '../../../../hidden/config.php';

// Phone carrier gateway suffixes
$AllTel = "@text.wireless.alltel.com";
$ATAT = "@txt.att.net";
$BoostMobile= "@myboostmobile.com";
$Sprint = "@messaging.sprintpcs.com";
$TMobile = "@tmomail.net";
$Verizon = "@vtext.com";
$VirginMobile = "@vmobl.com";

// -----Update Visitors Section-------
date_default_timezone_set('America/New_York');
$currentDate = date('Y\-m\-d', time());
$currentTime = date('H:i:s', time());
//echo 'The current date is: ' . $currentDate . '<br><br>';
//echo 'The current time is: ' . $currentTime . '<br><br>';

$visitors = [];
$rowsToDelete = [];
$numNights = 0;

$sql = "SELECT users.fName, visitor_id, visitor_name, aDate, lDate 
        FROM visitors
        JOIN users on users.user_id = visitors.user_id
        ORDER BY aDate ASC";
$result = mysqli_query($link, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        
        // Change date format
        $arrivalDate = date("D m/d/Y", strtotime($row['aDate']));
        $leaveDate = date("D m/d/Y", strtotime($row['lDate']));

        // Calculate number of nights between two dates
        $dateDiff = strtotime($leaveDate) - strtotime($arrivalDate);
        $numNights = floor($dateDiff / (60 * 60 * 24));

        // Change date format to Day of Week mm/dd
        $arrivalDate = date("D m/d", strtotime($row['aDate']));
        $leaveDate = date("D m/d", strtotime($row['lDate']));
        
        // if row leave date is less than current date, delete it from table.
        if ($row['lDate'] < $currentDate) {
            array_push($rowsToDelete, $row);
        }
        $visitors[$row['visitor_id']] = [$row['visitor_name'], $row['fName'], $arrivalDate, $leaveDate, $numNights];
    }    
    foreach ($rowsToDelete as $key => $value) {
        $sql = "DELETE FROM visitors WHERE visitor_id = ".$rowsToDelete[$key]['visitor_id'].";";
        mysqli_query($link, $sql);
    };
}


// -----Update Visitors Section End-------

// ----------Alert Section--------------

// only query chores with chore alert status of 1
$sql = "SELECT users.user_id, visitor_id, visitor_name, aDate, lDate, users.fName, users.phone, users.phone_carrier, users.visitor_alert_time, users.deleted, groups.group_id, groups.group_name 
        FROM visitors
        JOIN users ON users.user_id = visitors.user_id
        JOIN groups ON groups.group_id = visitors.group_id
        WHERE users.visitor_alert_status = 1 AND users.deleted = 0";

$result = mysqli_query($link, $sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        //print "<pre>";
        //print_r($row);
        //print "<pre>";
        
        // check if user's visitor arrival date is today.
        if ($row['aDate'] == $currentDate) {
            //echo 'today is ' . $row['fName'] . 's day to be alerted.<br>';
            
            // check if users chore time is now. (on the hour)
            if (substr($row['visitor_alert_time'], 0, 2) == substr($currentTime, 0, 2)){
                //echo 'now is ' . $row['fName'] . 's hour to be alerted.<br>';
                
                // user must now be alerted by mail/sms
                
                // set "to" field
                switch($row['phone_carrier']) {
                    case "atat":
                        $to = $row['phone'] . $ATAT;
                        break;
                    case "verizon":
                        $to = $row['phone'] . $Verizon;
                        break;
                    case "sprint":
                        $to = $row['phone'] . $Sprint;
                        break;
                    case "tmobile":
                        $to = $row['phone'] . $TMobile;
                        break;
                    case "boostmobile":
                        $to = $row['phone'] . $BoostMobile;
                        break;
                    case "virginmobile":
                        $to = $row['phone'] . $VirginMobile;
                        break;
                    default:
                        $to = "";
                }
                
                // set subject field
                $subject = $row['group_name'] . ": Visitor Notification.";
                
                // set msg field
                $msg = "You have " . $row['visitor_name'] . ' coming today.';
                //echo 'The message is: ' . $msg;
                
                // set from field
                $from = "From: Roomies <roomiesmanagement@gmail.com>";
                
                mail($to, $subject, $msg, $from);

                
            } // if chore alert time is current hour
        } // if chore date current is today
    } // while loop through rows
} else {
    //echo 'no visitors today';
} // if result

mysqli_close($link);


// ----------Alert Section End--------------


?>
