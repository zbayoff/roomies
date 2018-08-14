<?php 

session_start();

include '../../../../hidden/config.php';

// define session variables
$groupID = $_SESSION['group_id'];

// initialize variables, arrays

$items = [];
$itemDate = $_GET['itemDate'];
//$itemDate = '2018-02';
$itemMonth = "";
$itemYear = "";

// splice date string from itemDate to extract year in yyyy and month in mm
$itemYear = substr($itemDate, 0, 4);
$itemMonth = substr($itemDate, 5, 6);

// select all items for specified grouID and store in array to be JSON encoded and displayed on the browser.
$sql = "SELECT item_id, item_name, item_cost, items.user_id, users.fName, time_created
        FROM items
        JOIN users on items.user_id = users.user_id
        WHERE group_id = '$groupID' AND YEAR(time_created) = '$itemYear' AND MONTH(time_created) = '$itemMonth'
        ORDER BY time_created ASC";
$result = mysqli_query($link, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
//print "<pre>";
//print_r($items);
//print "<pre>";

echo json_encode($items);

mysqli_close($link);


?>