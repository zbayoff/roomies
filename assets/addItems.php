<?php 

session_start();

include '../../../hidden/config.php';

$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];
$currentUserfName = $_SESSION['first_name'];
$currentUserlName = $_SESSION['last_name'];
$currentUserEmail = $_SESSION['email'];

// define variables and initialize

$item_errors = [];
$item = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $fName = $_POST['fName'];
        
    // Validate Item Name
    if (empty(trim($_POST['itemName']))) {
        $item_errors[] = array("status" => "error", "field" => "addItemname", "msg" => "Please fill in the missing fields.");
    } else {
        // Prepare statement
        $sql = "SELECT item_id FROM items WHERE item_name = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // bind variables to prepared statements as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_name);
            
            // set parameters
            $param_name = trim($_POST['itemName']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //store result
                mysqli_stmt_store_result($stmt);
                $itemName = trim($_POST['itemName']);

            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo 'Prepare failed.';
        }
        // close stmt
        mysqli_stmt_close($stmt);
    } // else item name not empty
    
    // Validate Item Cost
    if (empty(trim($_POST['itemCost']))) {
        $item_errors[] = array("status" => "error", "field" => "addItemCost", "msg" => "Please fill in the missing fields.");
    } else {
        // Prepare statement
        $sql = "SELECT item_id FROM items WHERE item_cost = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // bind variables to prepared statements as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_cost);
            
            // set parameters
            $param_cost = trim($_POST['itemCost']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //store result
                mysqli_stmt_store_result($stmt);
                $itemCost = trim($_POST['itemCost']);

            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo 'Prepare failed.';
        }
        // close stmt
        mysqli_stmt_close($stmt);
    } // else item cost not empty
    
    // store user ID
    $userID = $_POST['userID'];
    
    date_default_timezone_set('America/New_York');

    $currentDate = date('Y-m');
    //echo $currentDate;
    
    // Validate Item Date, restrict choosing date outside of current month
    if (empty(trim($_POST['timeAdded']))) {
        $item_errors[] = array("status" => "error", "field" => "timeAdded", "msg" => "Please fill in the missing fields.");
    } else if(substr(trim($_POST['timeAdded']), 0,  7) != $currentDate) {
        $item_errors[] = array("status" => "error", "field" => "timeAdded", "msg" => "Please choose a date for the current month.");        
    } else {
        // Prepare statement
        $sql = "SELECT item_id FROM items WHERE time_created = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // bind variables to prepared statements as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_time);
            
            // set parameters
            $param_time = trim($_POST['timeAdded']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //store result
                mysqli_stmt_store_result($stmt);
                $itemTime = trim($_POST['timeAdded']);

            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo 'Prepare failed.';
        }
        // close stmt
        mysqli_stmt_close($stmt);
    } // else item date not empty
    
    
    
    // check if error array is empty, else output error
    if (empty(array_filter($item_errors))) {
        // Insert into database
        
        $sql = "INSERT INTO items (item_name, item_cost, user_id, group_id, time_created) VALUES ('$itemName', '$itemCost', '$userID', '$groupID', '$itemTime')";
        $result = mysqli_query($link, $sql);
        
        $itemID = mysqli_insert_id($link);
        
        $item = ['itemID' => $itemID, 'itemName' => $itemName, 'itemCost' => $itemCost, 'userID' => $userID, 'fName' => $fName, 'itemTime' => $itemTime];
        
        //echo $result;
        $item_errors[] = array("status" => "success",  "msg" => "Item added.", "data" => $item);
        echo (json_encode($item_errors));
        
    } else {
        echo (json_encode($item_errors));
        //echo substr(($_POST['timeAdded']), 0, 7);
    }
    
    mysqli_close($link);

} // if request method == POST

?>
