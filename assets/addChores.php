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
    
    // Validate Chore Name
    if (empty(trim($_POST['choreName']))) {
        $chore_errors[] = array("status" => "error", "field" => "choreName", "msg" => "Please enter the chore name.");
    } else {
        // Prepare statment
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
                
                // Validate chore name is not already taken.
                $sql = "SELECT chore_name FROM chores WHERE chore_name = '$choreName' AND group_id = '$groupID'";

                $result = mysqli_query($link, $sql);
                $row = $result->num_rows;

                if ($row == 1) {
                    $chore_errors[] = array("status" => "error", "field" => "choreName", "msg" => "This chore name is already taken. Please choose another.");
                }
                
            }
        } else {
            echo 'Prepare failed.';
        }
        
    } // else chore name validation
    
    // Validate Start Date
    if (empty(trim($_POST['startDate']))) {
        $chore_errors[] = array("status" => "error", "field" => "startDate", "msg" => "Please enter the chore start date.");
    } else {
        $startDate = $_POST['startDate'];
    } // else start date validation
    
    // Validate chore frequency numDays
    if ($_POST['add-chore-freq'] == 'numDays'){
        //echo 'You chose number of days';
        if (empty(trim($_POST['numDays']))) {
            $chore_errors[] = array("status" => "error", "field" => "numDaysRadioAdd", "msg" => "Please enter the number of days.");
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
    if ($_POST['add-chore-freq'] == 'dayOfWeek'){
        //echo 'You chose day of week <br>';    
        // Check if user day of weeks are same, output error
        $userDayofWeek = $_POST['userDayOfWeek'];
        foreach ($userDayofWeek as $key => $value) {
            //echo 'User ' . $key . ' chore day of week is: ' . $value . '<br>';
        }
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
        
        if ($_POST['add-chore-freq'] == 'dayOfWeek') {
            // creates array with key = user id and value = day of week
            
            foreach ($userDayofWeek as $key => $value) {
            //echo 'User ' . $key . ' chore day of week is: ' . $value . '<br>';
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
                //echo 'User ' . $key . ' chore day of week is: ' . $value . '<br>';

                //converting day of week to date format
                for ($i = 0; $i < 7; $i++) {
                    if (date('D', strtotime($weekAfterStartDate[$i])) == $value) {
                        $userDateofWeek[$key] = date('Y-m-d', strtotime($weekAfterStartDate[$i]));
                    }
                }
            }
            
            $numUsers = count($userDateofWeek);
            
            // INSERT into DB
            $sql = "INSERT INTO chores (chore_name, group_id, chore_numUsers, start_date, chore_freq) VALUES ('$choreName', '$groupID', '$numUsers', '$startDate', 'dayOfWeek')";
            $result = mysqli_query($link, $sql);

            $choreID = mysqli_insert_id($link);

            $count = 0;
            $days = [];
            
            foreach ($userDayofWeek as $key => $value) {
                $days[] = $value;
            }
            
            foreach ($userDateofWeek as $key => $value) {
                
                $sql = "INSERT INTO user2chores (user_id, chore_id, chore_date, chore_date_current, user_dayOfWeek) VALUES ('$key', '$choreID', '$value', '$value', '$days[$count]')";
                $result = mysqli_query($link, $sql);
                
                $count++;
            }
            
            
        } // if add chore is day of week
        
        if ($_POST['add-chore-freq'] == 'numDays') {
//            $numDays = trim($_POST['numDays']);
//            $userOrder = $_POST['userOrder'];
            // sort user order array based on array value.
            asort($userOrder);
            foreach ($userOrder as $key => $value) {
              //  echo 'User ' . $key . ' order number is: ' . $value . '<br>';
            }
            //echo "The num days is: " . $numDays;
            //echo '<br>';
    
            $startDateString = (string)$startDate;

            $startDateISO = date('r', strtotime($startDateString));
                  
            //echo $startDateISO;
            
            $userDayofWeek = [];
            $count = 0;
            
            foreach ($userOrder as $key => $value) {
                //echo 'User ' . $key . ' order number is: ' . $value . '<br>';
                $userDateofWeek[$key] = date('Y-m-d', strtotime($startDateString. ' + '.$count.'days'));
                $count = $count + $numDays;
            }
            
            // INSERT into DB
            
            $numUsers = count($userOrder);
            
        
            $sql = "INSERT INTO chores (chore_name, group_id, chore_numUsers, start_date, chore_freq, chore_numDays) VALUES ('$choreName', '$groupID', '$numUsers', '$startDate', 'numDays', '$numDays')";
            $result = mysqli_query($link, $sql);

            $choreID = mysqli_insert_id($link);
            $choreIDchores = $choreID;
            
            /*echo '<br>';
            echo 'User order is: ';
            print_r($userOrder);
            echo '<br>';
            echo '<br>';
            echo 'User dates are: ';
            print_r($userDateofWeek);
            echo '<br>';*/
            
            
            foreach ($userDateofWeek as $key => $value) {
                //echo 'User ' . $key . ' date: ' . $value . '<br>';
                $sql = "INSERT INTO user2chores (user_id, chore_id, chore_date, chore_date_current) VALUES ('$key', '$choreID', '$value', '$value')";
                $result = mysqli_query($link, $sql);
            }
            
            
            //echo "chore id to enter into: " . $choreIDchores;
            
            foreach ($userOrder as $key => $value) {
                //echo 'User ' . $key . ' order: ' . $value . '<br>';
                $sql = "UPDATE user2chores SET user_order = '$value' WHERE chore_id = '$choreIDchores' AND user_id = '$key'";
                //$sql = "INSERT INTO user2chores (user_order) VALUES ('$value') WHERE chore_id = '$choreIDchores' AND user_id = '$key'";
                $result = mysqli_query($link, $sql);
            }

            //echo '<br>';
            //print_r($userDayofWeek);
        } // if add chore is num days
        
        $chore_errors[] = array("status" => "success", "msg" => "Chore added.");
        echo (json_encode($chore_errors));
    } else {
        echo (json_encode($chore_errors));
    }
    
    mysqli_close($link);

} // if request method == POST

?>
