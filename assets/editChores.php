<?php 

session_start();

include '../../../hidden/config.php';

// define variables and initialze

$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];

$chore_errors = [];
$userDayofWeek = "";
$numDays = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validate that a chore has been selected
    if (($_POST['choreName']) == "Select") {
        $chore_errors[] = array("status" => "error", "field" => "choreName", "msg" => "Please select a chore to edit.");
    } else {
        // Prepare statments
        $sql = "SELECT chore_id FROM chores WHERE chore_name = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // bind paramteters
            mysqli_stmt_bind_param($stmt, "s", $param_chore_name);
            
            // set parameters
            $param_chore_name = trim($_POST['choreName']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)){
                //store result
                mysqli_stmt_store_result($stmt);
                $choreName = trim($_POST['choreName']);
            }
        } else {
            echo 'Prepare failed.';
        }
        
    } // else chore name validation
    
    // Validate that a NEW chore has been selected
    if (empty(trim($_POST['newChoreName']))) {
        $chore_errors[] = array("status" => "error", "field" => "newChoreName", "msg" => "Please input a new chore name.");
    } else {
        $newChoreName = trim($_POST['newChoreName']);
        $choreID = substr($_POST['choreName'], 5);
          // Validate chore name is not already taken.
        $sql = "SELECT chore_name FROM chores WHERE chore_name = '$newChoreName' AND NOT chore_id = '$choreID' AND group_id = '$groupID'";

        $result = mysqli_query($link, $sql);
        $row = $result->num_rows;

        if ($row == 1) {
            $chore_errors[] = array("status" => "error", "field" => "newChoreName", "msg" => "This chore name is already taken. Please choose another.");
        }
        
        
        
    } // else chore name validation
       
    // Validate Start Date
    if (empty(trim($_POST['startDate']))) {
        $chore_errors[] = array("status" => "error", "field" => "startDate", "msg" => "Please enter the chore start date.");
    } else {
        $startDate = $_POST['startDate'];
    } // else start date validation
    
    // Validate chore frequency
    if ($_POST['edit-chore-freq'] == 'numDays'){
        //echo 'You chose number of days';
        if (empty(trim($_POST['numDays']))) {
            $chore_errors[] = array("status" => "error", "field" => "numDaysRadioEdit", "msg" => "Please enter the number of days.");
        } else {
            $numDays = trim($_POST['numDays']);
        }
        
        // Check if user order number has same values, output error
        $userOrder = $_POST['userOrder'];
        $userOrderError = 0;
        $userOrder = array_values($userOrder);

        for ($i = 0; $i < count($userOrder); $i++) {
            for ($j = $i+1; $j < count($userOrder); $j++) {
                if ($userOrder[$i] == $userOrder[$j]) {
                    //echo 'yes';
                    $userOrderError = 1;
                }
            }
        }

        if ($userOrderError == 1) {
            $chore_errors[] = array("status" => "error", "field" => "userOrder", "msg" => "Each user order number must be unique.");
        } else {
            $userOrder = $_POST['userOrder'];
        }
    }
    
    // Validate chore frequency dayOfWeek
    if ($_POST['edit-chore-freq'] == 'dayOfWeek'){
        //echo 'You chose day of week <br>';    
        // Check if user day of weeks are same, output error
        $userDayofWeek = $_POST['userDayOfWeek'];

        $userDayofWeekError = 0;
        $userDayofWeek = array_values($userDayofWeek);

        for ($i = 0; $i < count($userDayofWeek); $i++) {
            for ($j = $i+1; $j < count($userDayofWeek); $j++) {
                if ($userDayofWeek[$i] == $userDayofWeek[$j]) {
                    //echo 'yes';
                    $userDayofWeekError = 1;
                }
            }
        }

        if ($userDayofWeekError == 1) {
            $chore_errors[] = array("status" => "error", "field" => "userDayOfWeek", "msg" => "Each user day of week must be unique.");
        } else {
            $userDayofWeek = $_POST['userDayOfWeek'];
        }
        
    }

    
    // check if error array is empty, else output error
    if (empty(array_filter($chore_errors))) {
        
        $choreID = substr($_POST['choreName'], 5);
        
        if ($_POST['edit-chore-freq'] == 'dayOfWeek') {
            
            // creates array with key = user id and value = day of week
            $chosenUserID = [];

            
            
            // select all rows from chores and user2chores for the specific choreID to be edited. 
            $sql = "SELECT users.user_id, chores.chore_id, chore_numUsers,  chore_date, chore_date_current, start_date, chore_freq, chore_numDays, user_order, user_dayOfWeek
                    FROM user2chores
                    JOIN users on users.user_id = user2chores.user_id
                    JOIN chores on chores.chore_id = user2chores.chore_id
                    WHERE group_id = '$groupID' AND chores.chore_id = '$choreID'";
            
            $result = mysqli_query($link, $sql);
            
            $userIDs = [];
        
            // Grabs all existing userIDs assocaited with current edited chore and stores them in array.
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $userIDs[] = $row['user_id'];
                }
            }

            $userIDsToRemove = $userIDs;

            foreach ($userIDsToRemove as $key => $value) {
                //echo 'User ' . $key . ' date: ' . $value . '<br>';
                $sql = "DELETE FROM user2chores WHERE user_id = '$value' AND chore_id = '$choreID'";
                $result = mysqli_query($link, $sql);
            }
        
            $weekAfterStartDate = array();
        
            $startDateString = (string)$startDate;

            $startDateISO = date('r', strtotime($startDateString));

            for ($i = 0; $i < 7; $i++) {
                $weekAfterStartDate[$i] = date('r', strtotime($startDateString. ' + '.$i.'days'));
            }
            
            $userDateofWeek = [];
            
            $userDateofWeek = $userDayofWeek;

            foreach ($userDateofWeek as $key => $value) {

                //converting day of week to date format
                for ($i = 0; $i < 7; $i++) {
                    if (date('D', strtotime($weekAfterStartDate[$i])) == $value) {
                        $userDateofWeek[$key] = date('Y-m-d', strtotime($weekAfterStartDate[$i]));
                    }
                }
            }
            
            $numUsers = count($userDateofWeek);
            
            // UPDATE chores table
            $sql = "UPDATE chores
                    SET chore_name = '$newChoreName',
                        chore_numUsers = '$numUsers',
                        start_date = '$startDate',
                        chore_freq = 'dayOfWeek',
                        chore_numDays = '0'
                    WHERE chore_id = '$choreID'";
            
            $result = mysqli_query($link, $sql);

            $count = 0;
            $days = [];
            
            foreach ($userDayofWeek as $key => $value) {
                $days[] = $value;
            }
            
            foreach ($userDateofWeek as $key => $value) {
                
                $sql = "INSERT INTO user2chores (user_id, chore_id, chore_date, chore_date_current, user_dayOfWeek, user_order) VALUES ('$key', '$choreID', '$value', '$value', '$days[$count]', '0')";
                
                $result = mysqli_query($link, $sql);
                
                $count++;
            }
            
            
        } // if edit chore is day of week
        
        if ($_POST['edit-chore-freq'] == 'numDays') {
            
            $numDays = trim($_POST['numDays']);
            $userOrder = $_POST['userOrder'];
            // sort user order array based on array value.
            asort($userOrder);
            
            $sql = "SELECT users.user_id, chores.chore_id, chore_numUsers,  chore_date, chore_date_current, start_date, chore_freq, chore_numDays, user_order, user_dayOfWeek
                    FROM user2chores
                    JOIN users on users.user_id = user2chores.user_id
                    JOIN chores on chores.chore_id = user2chores.chore_id
                    WHERE group_id = '$groupID' AND chores.chore_id = '$choreID'";
            
            $result = mysqli_query($link, $sql);
            
            $userIDs = [];
        
            // Grabs all existing userIDs assocaited with current edited chore and stores them in array.
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $userIDs[] = $row['user_id'];
                }
            }
            
            $userIDsToRemove = $userIDs;

            foreach ($userIDsToRemove as $key => $value) {
                //echo 'User ' . $key . ' date: ' . $value . '<br>';
                $sql = "DELETE FROM user2chores WHERE user_id = '$value' AND chore_id = '$choreID'";
                $result = mysqli_query($link, $sql);
            }
            
            $startDateString = (string)$startDate;

            $startDateISO = date('r', strtotime($startDateString));
            
            $userDayofWeek = [];
            $count = 0;
            
            foreach ($userOrder as $key => $value) {
                //echo 'User ' . $key . ' order number is: ' . $value . '<br>';
                $userDateofWeek[$key] = date('Y-m-d', strtotime($startDateString. ' + '.$count.'days'));
                $count = $count + $numDays;
            }
            
            // INSERT into DB
            $numUsers = count($userOrder);
            
            $sql = "UPDATE chores
                    SET chore_name = '$newChoreName',
                        chore_numUsers = '$numUsers',
                        start_date = '$startDate',
                        chore_freq = 'numDays',
                        chore_numDays = '$numDays'
                    WHERE chore_id = '$choreID'";
            
            $result = mysqli_query($link, $sql);
            
            foreach ($userDateofWeek as $key => $value) {
                
                $sql = "INSERT INTO user2chores (user_id, chore_id, chore_date, chore_date_current, user_dayOfWeek) VALUES ('$key', '$choreID', '$value', '$value', '')";

                $result = mysqli_query($link, $sql);
            }
            
            foreach ($userOrder as $key => $value) {
                
                $sql = "UPDATE user2chores
                        SET user_order = '$value',
                            user_dayOfWeek = ''
                        WHERE user_id = '$key' AND chore_id = '$choreID'";

                $result = mysqli_query($link, $sql);
            }
            
        } // if edit chore is num days
        
        $chore_errors[] = array("status" => "success", "msg" => "Chore info saved.");
        echo (json_encode($chore_errors));
    } else {
        echo (json_encode($chore_errors));
    }
    
    mysqli_close($link);

} // if request method == POST
