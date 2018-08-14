<?php 

session_start();

include '../../../../hidden/config.php';

$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];
$currentUserfName = $_SESSION['first_name'];
$currentUserlName = $_SESSION['last_name'];
$currentUserEmail = $_SESSION['email'];

$currentDate = date("Y\-m\-d ");

//define('__ROOT__', dirname(dirname(__FILE__)));

//require_once 'config.php';
//require_once(__ROOT__.'/config.php');

// define variables and initialize
$visitor_name_err = "";
$visitor_errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
    // Validate Visitor Name
    if (empty(trim($_POST['visitorName']))) {
        $visitor_errors[] = array("status" => "error", "field" => "visitorName", "msg" => "Please enter the visitor's name.");
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
    
    
    // Check if current date is greater than leave date
    /*if ($visitorLeaveDate < $currentDate){
        $visitor_errors[] = array("status" => "error", "field" => "leaveDate", "msg" => "The leave date has passed. Choose a later date.");
    }*/
    
    // check if error array is empty, else output error
    if (empty(array_filter($visitor_errors))) {
        // Insert into database
        
        $sql = "INSERT INTO visitors (user_id, group_id, visitor_name, aDate, lDate) VALUES ('$userID', '$groupID', '$visitorName', '$visitorArrivDate', '$visitorLeaveDate')";
        $result = mysqli_query($link, $sql);
        //echo $result;
        $visitor_errors[] = array("status" => "success", "msg" => "Visitor added.");
        echo (json_encode($visitor_errors));
        
    } else {
        echo (json_encode($visitor_errors));
    }
    
    mysqli_close($link);

} // if request method == POST

?>
