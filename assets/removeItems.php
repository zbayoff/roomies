<?php 

session_start();

include '../../../../hidden/config.php';

$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];
$currentUserfName = $_SESSION['first_name'];
$currentUserlName = $_SESSION['last_name'];
$currentUserEmail = $_SESSION['email'];

// define variables and initialize

$item_errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $itemID = $_POST['itemID'];
    $itemName = $_POST['itemName'];

    // check if error array is empty, else output error
    if (empty(array_filter($item_errors))) {
        //echo 'item id: ' . $itemID . '<br>';
        //echo 'item name: ' . $itemName . '<br>';
        // UPDATE database
        
        $sql = "DELETE FROM items 
                WHERE item_id = '$itemID' AND group_id = '$groupID'";
        $result = mysqli_query($link, $sql);
        
        //echo $result;
        $item_errors[] = array("status" => "success",  "msg" => "Item deleted.");
        echo (json_encode($item_errors));
        
    } else {
        echo (json_encode($item_errors));
    }
    
    mysqli_close($link);

} // if request method == POST


?>
