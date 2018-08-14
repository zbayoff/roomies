<?php 

session_start();

include '../../../../hidden/config.php';

// define session variables

$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];
$userID = $_SESSION['user_id'];

$chores = [];
$rowsToDelete = [];

date_default_timezone_set('America/New_York');

$currentDate = date('Y\-m\-d', time());

// update choredates if current date is > startdate.
    
// select rows from chores where start date < current date:

$sql = "SELECT users.user_id, chores.chore_id, chore_numUsers, chore_date, chore_date_current, start_date, chore_freq, chore_numDays, user_order, user_dayOfWeek
        FROM user2chores
        JOIN users on users.user_id = user2chores.user_id
        JOIN chores on chores.chore_id = user2chores.chore_id
        WHERE group_id = '$groupID' AND chore_date_current < '$currentDate'";
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

            //echo '<br>';
            //echo 'Current Date: ' . $currentDate;
            //echo '<br>';
          
            $daysToAdd = 0;
            
            while ($currentDate > $currentChoreDate) {
                //echo 'here';
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
            
            //echo '<br>';
            //echo 'Current Chore Date is: ' . $currentChoreDate;
            //echo '<br>';

            //print "<pre>";
            //print_r($row);
            //print "</pre>";
        }
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
            
            /*echo '<br>';
            echo 'Current date is : ' . $currentDate;
            echo '<br>';*/

            for ($i = 0; $i < 7; $i++) {
                $weekAfterCurrentDate[$i] = date('r', strtotime($currentDate . ' + '.$i.'days'));
            }
            
            /*echo '<br>';
            echo 'Week after current date is : ';
            echo '<br>';
            print_r($weekAfterCurrentDate);
            echo '<br>';*/
            

            //converting day of week to date format
            for ($i = 0; $i < 7; $i++) {
                if (date('D', strtotime($weekAfterCurrentDate[$i])) == $userDayofWeek) {
                    $userDayofWeek = date('Y-m-d', strtotime($weekAfterCurrentDate[$i]));
                }
            }
            
            /*echo '<br>';
            echo 'The users new chore day of week is: ' . $userDayofWeek;
            echo '<br>';*/
            
            // UPDATE table user2chores
            $sql = "UPDATE user2chores SET chore_date_current = '$userDayofWeek'
                    WHERE chore_id = '$choreID' AND user_id = '$userIDforChore'";
            $result1 = mysqli_query($link, $sql);
            if(!$result1) {
                echo 'No results';
            }
            
            /*print "<pre>";
            print_r($row);
            print "</pre>";*/
            
            
        }
    }
}

$sql = "SELECT users.user_id, users.fName, chores.chore_id, chores.chore_name, chores.chore_freq, chores.chore_numDays, chore_date_current
        FROM user2chores
        JOIN users on users.user_id = user2chores.user_id
        JOIN chores on chores.chore_id = user2chores.chore_id
        WHERE group_id = '$groupID'
        ORDER BY chore_date_current ASC";
$result = mysqli_query($link, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $choreID = $row['chore_id'];
        $choreDate = date("D\, M d", strtotime($row['chore_date_current']));
        $choreName = $row['chore_name'];
        $choreFreq = $row['chore_freq'];
        $choreNumDays = $row['chore_numDays'];
        $userFname = $row['fName'];
        $userID = $row['user_id'];
        
        $chores['chore' . $choreID]["choreName"] = $choreName;
        $chores['chore' . $choreID]["choreFreq"] = $choreFreq;
        $chores['chore' . $choreID]["choreNumDays"] = $choreNumDays;
        
        if (!isset($chores['chore' . $choreID]["users"])) {
            $chores['chore' . $choreID]["users"] = [];
        }
        
        //$chores['chore' . $choreID]["user" . $userID] = [];
        
        if ("user" . $userID && !isset($chores['chore' . $choreID]["users"]["user" . $userID])) {
            $users = [
                "userName" => $userFname,
                "choreDate" => $choreDate
            ];
        }
        
        $chores['chore' . $choreID]["users"]["user" . $userID] = $users;

    }

    //print "<pre>";
    //print_r($chores);
    //print "</pre>";
    //echo (json_encode(array_values($visitors)));
    
    /*foreach ($rowsToDelete as $key => $value) {
        $sql = "DELETE FROM visitors WHERE visitor_id = ".$rowsToDelete[$key]['visitor_id'].";";
        mysqli_query($link, $sql);
    };*/
    
    $json = [];
    
    // creates array of objects in order to maintain order of $visitors by arrival date.
    foreach($chores as $key => $value) {
        $json[] = [$key => $value];
    }
    
    echo (json_encode($json));
    //echo (json_encode($chores));

}

mysqli_close($link);

?>
