<?php 

session_start();

include '../../../../hidden/config.php';

$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];
$currentUserfName = $_SESSION['first_name'];
$currentUserlName = $_SESSION['last_name'];
$currentUserEmail = $_SESSION['email'];

$chore_errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validate that a chore has been selected
    if (($_POST['choreName']) == "Select") {
        $chore_errors[] = array("status" => "error", "field" => "choreName", "msg" => "Please select a chore to remove.");
    }
    
    // check if error array is empty, else output error
    if (empty(array_filter($chore_errors))) {
        $choreID = substr($_POST['choreName'], 5);
        //echo $choreID;
        
        
        
        $sql = "DELETE FROM user2chores WHERE chore_id = '$choreID'";
        $result = mysqli_query($link, $sql);
        
        $sql = "DELETE FROM chores WHERE chore_id = '$choreID'";
        $result = mysqli_query($link, $sql);
        
        $chore_errors[] = array("status" => "success", "msg" => "Chore removed.");
        echo (json_encode($chore_errors));
    } else {
        echo (json_encode($chore_errors));
    }
}

mysqli_close($link);


?>