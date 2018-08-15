<?php 

session_start();

include '../../../hidden/config.php';

// define session variables
$groupID = $_SESSION['group_id'];
$userID = $_SESSION['user_id'];

// initialize variables, arrays
$userArray = [];

$sql = "SELECT fName, lName, email, phone, phone_carrier, item_alert_status, chore_alert_status, chore_alert_time, visitor_alert_status, visitor_alert_time
        FROM users
        WHERE user_id = '$userID'
        AND deleted = 0";
$result = mysqli_query($link, $sql);

if($result->num_rows == 1){
    while ($row = $result->fetch_assoc()) {
        $userArray[] = $row;
    }
}

echo json_encode($userArray);

mysqli_close($link);


?>