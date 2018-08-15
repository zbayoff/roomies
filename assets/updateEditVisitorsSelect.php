<?php 

session_start();

include '../../../hidden/config.php';

// define variables and initialze
$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];

$visitorID = $_GET['visitorid'];
//$visitorID = 59;

$sql = "SELECT users.user_id, users.fName, visitors.visitor_id, visitors.visitor_name, aDate, lDate
        FROM visitors
        JOIN users on users.user_id = visitors.user_id
        WHERE visitors.visitor_id = '$visitorID'";

$result = mysqli_query($link, $sql);
if(!$result) {
    echo 'no result';
}

$visitorInfo = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $hostID = $row['user_id'];
        $hostName = $row['fName'];
        $visitorName = $row['visitor_name'];
        $arrivalDate = $row['aDate'];
        $leaveDate = $row['lDate'];

        $visitorInfo[] = ['hostID' => $hostID, 'hostName' => $hostName, 'visitorName' => $visitorName, 'arrivalDate' => $arrivalDate, 'leaveDate' => $leaveDate];

        //print '<pre>';
        //print_r($visitorInfo);
        //print '<pre>';

        
    }
}

echo json_encode($visitorInfo);

mysqli_close($link);

?>