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

// -----Update Chores Section-------
date_default_timezone_set('America/New_York');
$currentDate = date('Y\-m\-d', time());
$currentTime = date('H:i:s', time());
//echo 'The current date is: ' . $currentDate . '<br><br>';
//echo 'The current time is: ' . $currentTime . '<br><br>';

// update choredates if current date is > startdate.
// select rows from chores where start date < current date:
$sql = "SELECT users.user_id, chores.chore_id, chore_numUsers, chore_date, chore_date_current, start_date, chore_freq, chore_numDays, user_order, user_dayOfWeek
        FROM user2chores
        JOIN users on users.user_id = user2chores.user_id
        JOIN chores on chores.chore_id = user2chores.chore_id
        WHERE chore_date_current < '$currentDate'";
$result = mysqli_query($link, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['chore_freq'] == 'numDays') {

            $StartDate = $row['start_date'];
            $oldChoreDate = $row['chore_date'];
            $currentChoreDate = $row['chore_date_current'];
            $numDays = $row['chore_numDays'];
            $numUsers = $row['chore_numUsers'];
            $orderNumber = $row['user_order'];
            $choreID = $row['chore_id'];
            $userIDforChore = $row['user_id'];
          
            $daysToAdd = 0;
            
            while ($currentDate > $currentChoreDate) {
                //if current chore date <= current date
                //loop through until current chore date is greater than or equal to current date.
                $currentChoreDate = date('Y\-m\-d', strtotime($oldChoreDate. ' + '. $daysToAdd .'days'));
                $daysToAdd = $daysToAdd + ($numDays*$numUsers);
            }
            
            // UPDATE table user2chores
            $sql = "UPDATE user2chores SET chore_date_current = '$currentChoreDate'
                    WHERE chore_id = '$choreID' AND user_id = '$userIDforChore'";
            $result1 = mysqli_query($link, $sql);
            if(!$result1) {
                echo 'No results';
            }
        } // if chore-freq = numDays
        if ($row['chore_freq'] == 'dayOfWeek') {
            
            $StartDate = $row['start_date'];
            $oldChoreDate = $row['chore_date'];
            $currentChoreDate = $row['chore_date_current'];
            $numDays = $row['chore_numDays'];
            $numUsers = $row['chore_numUsers'];
            $orderNumber = $row['user_order'];
            $choreID = $row['chore_id'];
            $userIDforChore = $row['user_id'];
            $userDayofWeek = $row['user_dayOfWeek'];
            
            $weekAfterCurrentDate = array();

            for ($i = 0; $i < 7; $i++) {
                $weekAfterCurrentDate[$i] = date('r', strtotime($currentDate . ' + '.$i.'days'));
            }

            //converting day of week to date format
            for ($i = 0; $i < 7; $i++) {
                if (date('D', strtotime($weekAfterCurrentDate[$i])) == $userDayofWeek) {
                    $userDayofWeek = date('Y-m-d', strtotime($weekAfterCurrentDate[$i]));
                }
            }

            // UPDATE table user2chores
            $sql = "UPDATE user2chores SET chore_date_current = '$userDayofWeek'
                    WHERE chore_id = '$choreID' AND user_id = '$userIDforChore'";
            $result1 = mysqli_query($link, $sql);
            if(!$result1) {
                echo 'No results';
            }
        } // if chore-freq = dayOfWeek
    } // while loop rows
} else {
    //echo 'No Results';
} // if return result

// -----Update Chores Section End-------

// ----------Alert Section--------------

// only query chores with chore alert status of 1
$sql = "SELECT users.user_id, chores.chore_id, chores.chore_name, user2chores.chore_date_current, users.fName, users.phone, users.phone_carrier, users.chore_alert_time, users.deleted, groups.group_id, groups.group_name 
        FROM user2chores
        JOIN users ON users.user_id = user2chores.user_id
        JOIN chores on chores.chore_id = user2chores.chore_id
        JOIN groups ON groups.group_id = chores.group_id
        WHERE users.chore_alert_status = 1 AND users.deleted = 0";

$result = mysqli_query($link, $sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        //print "<pre>";
        //print_r($row);
        //print "<pre>";
        
        // check if user's chore date is today.
        if ($row['chore_date_current'] == $currentDate) {
            //echo 'today is ' . $row['fName'] . 's day to be alerted.<br>';
            
            // check if users chore time is now. (on the hour)
            if (substr($row['chore_alert_time'], 0, 2) == substr($currentTime, 0, 2)){
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
                $subject = $row['group_name'] . ": Chore Notification.";
                
                // set msg field
                $msg = "Today is your day for " . $row['chore_name'] . '.';
                //echo 'The message is: ' . $msg;
                
                // set from field
                $from = "From: Roomies <roomiesmanagement@gmail.com>";
                
                mail($to, $subject, $msg, $from);

                
            } // if chore alert time is current hour
        } // if chore date current is today
    } // while loop through rows
} else {
    echo 'no chores today';
} // if result



mysqli_close($link);




// ----------Alert Section End--------------


?>
