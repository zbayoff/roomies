<?php 

session_start();

include '../../../../hidden/config.php';

// define session variables
$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];
$currentUserfName = $_SESSION['first_name'];
$currentUserlName = $_SESSION['last_name'];
$currentUserEmail = $_SESSION['email'];
$userID = $_SESSION['user_id'];

$visitors = [];
$rowsToDelete = [];
$numNights = 0;

$currentDate = date("Y\-m\-d ");

$sql = "SELECT users.fName, visitor_id, visitor_name, aDate, lDate 
        FROM visitors
        JOIN users on users.user_id = visitors.user_id
        WHERE group_id = '$groupID' 
        ORDER BY aDate ASC";
$result = mysqli_query($link, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        
        // Change date format
        $arrivalDate = date("D m/d/Y", strtotime($row['aDate']));
        $leaveDate = date("D m/d/Y", strtotime($row['lDate']));

        // Calculate number of nights between two dates
        $dateDiff = strtotime($leaveDate) - strtotime($arrivalDate);
        $numNights = floor($dateDiff / (60 * 60 * 24));

        // Change date format to Day of Week mm/dd
        $arrivalDate = date("D m/d", strtotime($row['aDate']));
        $leaveDate = date("D m/d", strtotime($row['lDate']));
        
        // if row leave date is less than current date, delete it from table.
        if ($row['lDate'] < $currentDate) {
            array_push($rowsToDelete, $row);
        }
        
        $visitors[$row['visitor_id']] = [$row['visitor_name'], $row['fName'], $arrivalDate, $leaveDate, $numNights];
    }
    //echo (json_encode(array_values($visitors)));
    
    foreach ($rowsToDelete as $key => $value) {
        $sql = "DELETE FROM visitors WHERE visitor_id = ".$rowsToDelete[$key]['visitor_id'].";";
        mysqli_query($link, $sql);
    };
    
    $json = [];
    
    // creates array of objects in order to maintain order of $visitors by arrival date.
    foreach($visitors as $key => $value) {
        $json[] = [$key => $value];
    }
    
    echo (json_encode($json));

}

mysqli_close($link);

?>
