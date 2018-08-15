<?php 

session_start();

include '../../../hidden/config.php';

// initialize
$groupID = $_SESSION['group_id'];
$userID = $_SESSION['user_id'];

$choreAlertTime = 0;
$choreAlertStatus = 0;

$visitorAlertTime = 0;
$visitorAlertStatus = 0;

$notification_errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validate item checkbox
    if (!isset($_POST['item-alert-check'])) {
        $sql = "UPDATE users SET item_alert_status = 0
                WHERE user_id = '$userID' AND deleted = 0";
        $result = mysqli_query($link, $sql);
        
    } else {
        $sql = "UPDATE users SET item_alert_status = 1
                WHERE user_id = '$userID' AND deleted = 0";
        $result = mysqli_query($link, $sql);

    } // else 
    
    // Validate chores checkbox
    if (!isset($_POST['chore-alert-check'])) {
        $choreAlertStatus = 0;
    } else {
        if (trim($_POST['chore-alert-time']) == "Select"){
            $notification_errors[] = array("status" => "error", "field" => "chore-alert-time", "msg" => "Please choose an alert time.");
        } else {
            // Prepare select statement
            $sql = "SELECT user_id FROM users WHERE chore_alert_time = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_chore_alert_time);

                // Set parameters
                $param_chore_alert_time = trim($_POST['chore-alert-time']);

                // Attempt to execute prepared statement
                if(mysqli_stmt_execute($stmt)) {
                    //Store result
                    mysqli_stmt_store_result($stmt);
                    $choreAlertTime = trim($_POST['chore-alert-time']);
                    $choreAlertStatus = 1;
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    } // else
    
    // Validate visitors checkbox
    if (!isset($_POST['visitor-alert-check'])) {
        $visitorAlertStatus = 0;
    } else {
        if (trim($_POST['visitor-alert-time']) == "Select"){
            $notification_errors[] = array("status" => "error", "field" => "visitor-alert-time", "msg" => "Please choose an alert time.");
        } else {
            // Prepare select statement
            $sql = "SELECT user_id FROM users WHERE visitor_alert_time = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_visitor_alert_time);

                // Set parameters
                $param_visitor_alert_time = trim($_POST['visitor-alert-time']);

                // Attempt to execute prepared statement
                if(mysqli_stmt_execute($stmt)) {
                    //Store result
                    mysqli_stmt_store_result($stmt);
                    $visitorAlertTime = trim($_POST['visitor-alert-time']);
                    $visitorAlertStatus = 1;
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    } // else


    if (empty(array_filter($notification_errors))) {
        
        //echo $choreAlertTime . '<br>';

        $sql = "UPDATE users
                SET chore_alert_status = '$choreAlertStatus', chore_alert_time = '$choreAlertTime', visitor_alert_status = '$visitorAlertStatus', visitor_alert_time = '$visitorAlertTime'
                WHERE user_id = '$userID' AND deleted = 0";
        $result = mysqli_query($link, $sql);
        
        // if phone is not empty, insert into dB, else update it.
        // if phone is not empty, insert carrier into dB, else update it.
        
        $notification_errors[] = array("status" => "success", "msg" => "Notification settings saved.");
        echo (json_encode($notification_errors));
    } else {
        echo (json_encode($notification_errors));
    }
    
    mysqli_close($link);
    
} // if server request post

?>
