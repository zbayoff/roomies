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
$items = [];

//$_POST['itemID'] = "53";
//$_POST['itemName'] = "gummies";
//$_POST['itemCost'] = "34.23";
//$_POST['itemDate'] = "2018-02-15";
//$_POST['fName'] = "Zach";
//$_POST['userID'] = "47";



if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $itemID = $_POST['itemID'];
    $fName = $_POST['fName'];
        
    // Validate Item Name
    if (empty(trim($_POST['itemName']))) {
        $item_errors[] = array("status" => "error", "field" => "item_name", "msg" => "Please fill in the missing fields.");
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
        $item_errors[] = array("status" => "error", "field" => "item_cost", "msg" => "Please fill in the missing fields.");
    } else {
        // Prepare statement
        $sql = "SELECT item_id FROM items WHERE item_cost = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // bind variables to prepared statements as parameters
            mysqli_stmt_bind_param($stmt, "d", $param_cost);
            
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

    // Validate Item Date
    if (empty(trim($_POST['itemDate']))) {
        $item_errors[] = array("status" => "error", "field" => "time_created", "msg" => "Please fill in the missing fields.");
    } else if(substr(trim($_POST['itemDate']), 0,  7) != $currentDate) {
        $item_errors[] = array("status" => "error", "field" => "time_created", "msg" => "Please choose a date for the current month.");        
    } else {
        // Prepare statement
        $sql = "SELECT item_id FROM items WHERE time_created = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // bind variables to prepared statements as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_time);
            
            // set parameters
            $param_time = trim($_POST['itemDate']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //store result
                mysqli_stmt_store_result($stmt);
                $itemTime = trim($_POST['itemDate']);

            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo 'Prepare failed.';
        }
        // close stmt
        mysqli_stmt_close($stmt);
    } // else item cost not empty
    
    
    
    // check if error array is empty, else output error
    if (empty(array_filter($item_errors))) {
        /*echo 'item id: ' . $itemID . '<br>';
        echo 'item name: ' . $itemName . '<br>';
        echo 'item cost: ' . $itemCost . '<br>';
        echo 'user id: ' . $userID . '<br>';
        echo 'time edited: ' . $itemTime . '<br>';*/
        // UPDATE database
        
        $sql = "UPDATE items SET item_name = '$itemName', item_cost = '$itemCost', user_id = '$userID', time_created = '$itemTime'
                WHERE item_id = '$itemID' AND group_id = '$groupID'";
        $result = mysqli_query($link, $sql);
        
        $item = ['itemID' => $itemID, 'itemName' => $itemName, 'itemCost' => $itemCost, 'userID' => $userID, 'fName' => $fName, 'itemTime' => $itemTime];
        
//        print '<pre>';
//        print_r($items);
//        print '<pre>';
        
        //echo $result;
        $item_errors[] = array("status" => "success",  "msg" => "Item saved.", "data" => $item);
        echo (json_encode($item_errors));
        //echo $item;
        
    } else {
        echo (json_encode($item_errors));
    }
    
    mysqli_close($link);

} // if request method == POST


?>
