<?php 

session_start();

include '../../../../hidden/config.php';

// define variables and initialze
$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];

$choreID = substr($_GET['choreid'], 5);
//$choreID = 89;

$sql = "SELECT users.user_id, chores.chore_id, chore_numUsers, chore_date, chore_date_current, start_date, chore_freq, chore_numDays, user_order, user_dayOfWeek
        FROM user2chores
        JOIN users on users.user_id = user2chores.user_id
        JOIN chores on chores.chore_id = user2chores.chore_id
        WHERE chores.chore_id = '$choreID'";

$result = mysqli_query($link, $sql);
if(!$result) {
    echo 'no result';
}

$choreInfo = [];

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
            
            $choreInfo[] = ['numDays' => $numDays, 'startDate' => $StartDate, 'user' => ["userID" => $userIDforChore, "userOrder" => $orderNumber]];
            
            
            //print '<pre>';
            //print_r($choreInfo);
            //print '<pre>';
            
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
            
            $choreInfo[] = ['startDate' => $StartDate, 'user' => ["userID" => $userIDforChore, "userDayofWeek" => $userDayofWeek]];
            
//            print '<pre>';
//            print_r($choreInfo);
//            print '<pre>';
            
        }
        
    }
}



echo json_encode($choreInfo);
mysqli_close($link);


?>