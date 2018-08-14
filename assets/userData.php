<?php 

session_start();

include '../../../../hidden/config.php';


$user = [];
$userArrayData = [];
$chores = [];
$visitors = [];
$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];
$userID = $_GET['userid'];
//$userID = 47;

/*$sql = "SELECT a.*, b.*, c.*, d.*
        FROM users a
        INNER JOIN user2chores b
            ON a.user_id = b.user_id
        INNER JOIN visitors c
            ON b.user_id = c.user_id
        INNER JOIN chores d
        	ON b.chore_id = d.chore_id
        WHERE a.user_id = '$userID'";*/

// SELECT all chore data for specified user and put into chore array.
$sql = "SELECT chores.chore_id, chores.chore_name, chores.chore_freq, chores.chore_numDays, chore_date_current
        FROM user2chores
        JOIN users on users.user_id = user2chores.user_id
        JOIN chores on chores.chore_id = user2chores.chore_id
        WHERE group_id = '$groupID' AND users.user_id = '$userID'
        ORDER BY chore_date_current ASC";


$result = mysqli_query($link, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $choreID = $row['chore_id'];
        $choreDate = date("D\, M d", strtotime($row['chore_date_current']));
        $choreName = $row['chore_name'];
        $choreFreq = $row['chore_freq'];
        $choreNumDays = $row['chore_numDays'];
        
        $chores['chore' . $choreID]["choreName"] = $choreName;
        $chores['chore' . $choreID]["choreFreq"] = $choreFreq;
        $chores['chore' . $choreID]["choreNumDays"] = $choreNumDays;
        $chores['chore' . $choreID]["choreDate"] = $choreDate;

        //$userArrayData['user'] = [$row['fName'], $row['lName']];
    }
    
//        print "<pre>";
//        print_r($chores);
//        print "<pre>";
}

// SELECT all visitor data for specified user and put into visitor array.
$sql = "SELECT visitors.visitor_id, visitors.visitor_name, aDate, lDate
        FROM visitors
        JOIN users on users.user_id = visitors.user_id
        WHERE group_id = '$groupID' AND users.user_id = '$userID'
        ORDER BY aDate ASC";


$result = mysqli_query($link, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $visitorID = $row['visitor_id'];
        $visitorName = $row['visitor_name'];
        
        $arrivalDate = date("D m/d/Y", strtotime($row['aDate']));
        $leaveDate = date("D m/d/Y", strtotime($row['lDate']));

        // Calculate number of nights between two dates
        $dateDiff = strtotime($leaveDate) - strtotime($arrivalDate);
        $numNights = floor($dateDiff / (60 * 60 * 24));

        // Change date format to Day of Week mm/dd
        $arrivalDate = date("D\, M d", strtotime($row['aDate']));
        $leaveDate = date("D\, M d", strtotime($row['lDate']));
        
        
        $visitors['visitor' . $visitorID]["visitorName"] = $visitorName;
        $visitors['visitor' . $visitorID]["arrivalDate"] = $arrivalDate;
        $visitors['visitor' . $visitorID]["leaveDate"] = $leaveDate;
        $visitors['visitor' . $visitorID]["numNights"] = $numNights;

        //$userArrayData['user'] = [$row['fName'], $row['lName']];
    }
//        print "<pre>";
//        print_r($visitors);
//        print "<pre>";
        
}


// SELECT all user data for specified user.
$sql = "SELECT fName
        FROM users
        WHERE user_id = '$userID'";

$result = mysqli_query($link, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $fName = $row['fName'];
        $user['user']["userID"] = $userID;
        $user['user']["fName"] = $fName;

    }
    
//        print "<pre>";
//        print_r($user);
//        print "<pre>";
}


$user['user']["chores"] = $chores;
$user['user']["visitors"] = $visitors;

//print "<pre>";
//print_r($user);
//print "<pre>";




//$array = ['zach', 'tenz'];
//$userArrayData['visitors'] = ['sam', 'jessica'];

echo json_encode($user);
//echo json_encode($array);

mysqli_close($link);

?>