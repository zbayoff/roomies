<?php 

session_start();

include '../../../../hidden/config.php';

// define session variables
$groupID = $_SESSION['group_id'];

// initialize variables, arrays

$users = [];

// select all items for specified grouID and store in array to be JSON encoded and displayed on the browser.
$sql = "SELECT users.user_id, users.fName
        FROM user2group
        JOIN users ON users.user_id = user2group.user_id
        WHERE group_id = '$groupID'";
$result = mysqli_query($link, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
//print "<pre>";
//print_r($users);
//print "<pre>";

echo json_encode($users);


mysqli_close($link);


?>