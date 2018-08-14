<?php 

session_start();

include '../../../../hidden/config.php';

// define variables and initialze

$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];

$visitor_errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
    // Validate Visitor Name
    if (($_POST['visitorName']) == "Select") {
        $visitor_errors[] = array("status" => "error", "field" => "visitorName", "msg" => "Please select a visitor to edit.");
    } else {
        // Prepare statement
        $sql = "SELECT visitor_id FROM visitors WHERE visitor_name = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // bind variables to prepared statements as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_name);
            
            // set parameters
            $param_name = trim($_POST['visitorName']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //store result
                mysqli_stmt_store_result($stmt);
                $visitorName = trim($_POST['visitorName']);
                //echo $visitorName;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo 'Prepare failed.';
        }
        // close stmt
        mysqli_stmt_close($stmt);
        
    } // else visitor name not empty
    
    // Validate that a NEW visitor name has been selected
    if (empty(trim($_POST['newVisitorName']))) {
        $visitor_errors[] = array("status" => "error", "field" => "newVisitorName", "msg" => "Please input a new visitor name.");
    } else {   
        $newVisitorName = trim($_POST['newVisitorName']);
    } // else visitor name validation
    
    // Validate User Name
    if (empty(trim($_POST['userName']))) {
        // do nothing, cannot be empty
    } else {
        // prepare statement
        $sql = "SELECT visitor_id FROM visitors WHERE user_id = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            //bind
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // set paramters
            $param_username = trim($_POST['userName']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //store result
                mysqli_stmt_store_result($stmt);
                
                $userName = trim($_POST['userName']);
                
                $sql = "SELECT user_id from users WHERE user_id  = '$userName'";
                $result = mysqli_query($link, $sql);
                $row = $result->num_rows;
            
                if($row == 1){
                    $a = mysqli_fetch_assoc($result);
                    $userID = $a['user_id'];
                }
    
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            
        }
        
    } // else user name not empty
    
    // Validate Visitor Arrival Date
    if (empty(trim($_POST['arrivalDate']))) {
        $visitor_errors[] = array("status" => "error", "field" => "arrivalDate", "msg" => "Please enter the visitor's arrival date.");
    } else {
        // Prepare statement
        $sql = "SELECT visitor_id FROM visitors WHERE aDate = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // bind variables to prepared statements as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_arrivdate);
            
            // set parameters
            $param_arrivdate = trim($_POST['arrivalDate']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //store result
                mysqli_stmt_store_result($stmt);
                $visitorArrivDate = trim($_POST['arrivalDate']);
    
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo 'Prepare failed.';
        }
        // close stmt
        mysqli_stmt_close($stmt);} // else arrival date not empty
    
    // Validate Visitor Leave Date
    if (empty(trim($_POST['leaveDate']))) {
        $visitor_errors[] = array("status" => "error", "field" => "leaveDate", "msg" => "Please enter the visitor's leave date.");
    } else {
        // Prepare statement
        $sql = "SELECT visitor_id FROM visitors WHERE lDate = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // bind variables to prepared statements as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_leavedate);
            
            // set parameters
            $param_leavedate = trim($_POST['leaveDate']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //store result
                mysqli_stmt_store_result($stmt);
                $visitorLeaveDate = trim($_POST['leaveDate']);
                // Check if arrival date is less than leave date, if not, output error.
                if (!($visitorArrivDate <= $visitorLeaveDate)){
                    $visitor_errors[] = array("status" => "error", "field" => "arrivalDate", "msg" => "Vistor's arrival date is not less than leave date.");
                } 
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo 'Prepare failed.';
        }
        // close stmt
        mysqli_stmt_close($stmt); // else arrival date not empty
    }
    
    if (empty(array_filter($visitor_errors))) {
        
        /*echo 'Visitor Name to edit: ' . $visitorName;
        echo '<br>';
        echo 'Visitor ID to edit: ' . $visitorName;
        echo '<br>';
        echo 'New visitor Name: ' . $newVisitorName;
        echo '<br>';
        echo 'Host name: ' . $userName;
        echo '<br>';
        echo 'Arrival Date: ' . $visitorArrivDate;
        echo '<br>';
        echo 'Leave Date: ' . $visitorLeaveDate;
        echo '<br>';*/
        
        $sql = "UPDATE visitors
                SET visitor_name = '$newVisitorName',
                    user_id = '$userName',
                    aDate = '$visitorArrivDate',
                    lDate = '$visitorLeaveDate'
                WHERE visitor_id = '$visitorName'";
        $result = mysqli_query($link, $sql);
        
        
        
        $visitor_errors[] = array("status" => "success", "msg" => "Visitor info saved.");
        echo (json_encode($visitor_errors));
    } else {
        echo (json_encode($visitor_errors));
    }
    
} // Server REQUEST POST



mysqli_close($link);
    
?>