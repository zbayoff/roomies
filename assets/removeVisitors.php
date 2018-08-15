<?php 

session_start();

include '../../../hidden/config.php';

$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];
$currentUserfName = $_SESSION['first_name'];
$currentUserlName = $_SESSION['last_name'];
$currentUserEmail = $_SESSION['email'];

$visitor_errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validate that a visitor name has been selected
    if (($_POST['visitorName']) == "Select") {
        $visitor_errors[] = array("status" => "error", "field" => "visitorName", "msg" => "Please select a visitor to remove.");
    }
    
    // check if error array is empty, else output error
    if (empty(array_filter($visitor_errors))) {
        $visitorName = $_POST['visitorName'];

        $sql = "DELETE FROM visitors WHERE visitor_id = '$visitorName' AND group_id = '$groupID'";
        $result = mysqli_query($link, $sql);
        $visitor_errors[] = array("status" => "success", "msg" => "Visitor removed.");
        echo (json_encode($visitor_errors));
    } else {
        echo (json_encode($visitor_errors));
    }
}

mysqli_close($link);


?>