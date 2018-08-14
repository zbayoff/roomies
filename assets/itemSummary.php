<?php 

session_start();
// If session variable not set, redirect to login page

if (isset($_SESSION['group_id'])) {
    $groupID = $_SESSION['group_id'];
}

// require config file
include '../../../../hidden/config.php';

$items = [];
$itemDate = $_GET['itemDate'];
$itemMonth = "";
$itemYear = "";

// splice date string from itemDate to extract year in yyyy and month in mm
$itemYear = substr($itemDate, 0, 4);
$itemMonth = substr($itemDate, 5, 6);

// query users TABLE to select users from the group (that are not deleted).
$sql = "SELECT users.user_id, users.fName, group_id, user2group.deleted
        FROM user2group
        JOIN users ON users.user_id = user2group.user_id
        WHERE group_id = '$groupID'";
$result = mysqli_query($link, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['user_id'];
        $fName = $row['fName'];
        $deleted = $row['deleted'];
        $users[] = ['user_id' => $user_id,'fName' => $fName, 'deleted' => $deleted];
    }
}

//print '<pre>';
//print_r($users);
//print '<pre>';


// query to output all users associated with the current group
    
$sql = "SELECT item_id, item_name, item_cost, items.user_id, users.fName, time_created
        FROM items
        JOIN users on items.user_id = users.user_id
        WHERE group_id = $groupID AND YEAR(time_created) = '$itemYear' AND MONTH(time_created) = '$itemMonth'
        ORDER BY time_created ASC";
$result = mysqli_query($link, $sql);

$items = [];
$itemTotals = [];
$i = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $itemID = $row['item_id'];
        $itemName = $row['item_name'];
        $itemCost = $row['item_cost'];
        $userFname = $row['fName'];
        $userID = $row['user_id'];
        
        $items[$i] = ['user_id' => $userID, 'user_Fname' => $userFname, 'item_cost' => $itemCost];

        $i++;
    }
}

//print '<pre>';
//print_r($items);
//print '<pre>';



// Put user IDs, fNames, and total item cost into one array: $itemTotalsUsers
$userIDs = array_column($items, 'user_id');
$itemCosts = array_column($items, 'item_cost');
$fNames = array_column($items, 'user_Fname');

$itemTotals = array_unique($userIDs);
$itemTotals = array_combine($itemTotals, array_fill(0, count($itemTotals), 0));

$fNamesUnique = array_unique($fNames);
$fNamesUnique = array_combine($fNamesUnique, array_fill(0, count($fNamesUnique), 0));

foreach($userIDs as $key => $value) {
    $itemTotals[$value] += $itemCosts[$key];
}

//print '<pre>';
//print_r($fNamesUnique);
//print '<pre>';

$itemTotalsUsers = [];
$userIdsWithItems = [];
foreach ($itemTotals as $key => $value) {
    $itemTotalsUsers[] = ['user_id' => $key, 'totalPaid' => $value]; 
    $userIdsWithItems[] = $key;
}

$i = 0;
foreach ($fNamesUnique as $key => $value) {
    $itemTotalsUsers[$i]['fName'] = $key;
    $i++;
}

//print '<pre>';
//print_r($itemTotalsUsers);
//print '<pre>';

//var_dump($itemTotalsUsers);

// Loop through items and if user_id found that does not have an item, append item array with user_id, fName and item cost of $0. 
$userNoItemArray = [];
foreach ($users as $key => $value) {
    if (in_array($value['user_id'], $userIdsWithItems)) {
        //echo 'user '.$value['user_id'].' has item <br>';
    } else {
        //echo 'user '.$value['user_id'].' has NO item <br>';
        if($value['deleted'] == 0) {
            $userNoItemArray = ['user_id' => (int)$value['user_id'], 'totalPaid' => 0.0000, 'fName' => $value['fName']];
            array_push($itemTotalsUsers, $userNoItemArray);
        }
    }
}



//print '<pre>';
//print_r($itemTotalsUsers);
//print '<pre>';

$totalItemCost = array_sum($itemTotals);

$numUsers = count($itemTotalsUsers);
// calculate cost to split if there are more than one users
if ($numUsers > 1) {
    $splitTotalCost = ($totalItemCost / $numUsers);

//    print '<pre>';
//    print_r($splitTotalCost);
//    print '<pre>';


    $amountToPay = 0;

    // Calculate how much everyone owes and put into array

    // payStatus 0 => owes, 1 => is owed
    foreach ($itemTotalsUsers as $key => $value) {
        $amountToPay = $splitTotalCost - $value['totalPaid'];
        if ($amountToPay > 0) {
            $amountToPay = round(abs($amountToPay), 3);
            $payStatus = 0;
            $roomiesThatOwe[] = ['user_id' => $value['user_id'], 'fName' => $value['fName'], 'payStatus' => $payStatus, 'amount' => $amountToPay];
        } else {
            $amountToPay = round(abs($amountToPay), 3);
            $payStatus = 1;
            $roomiesOwed[] = ['user_id' => $value['user_id'], 'fName' => $value['fName'], 'payStatus' => $payStatus, 'amount' => $amountToPay];
        }
    }

//    print '<pre>';
//    print_r($roomiesThatOwe);
//    print '<pre>';

//    print '<pre>';
//    print_r($roomiesOwed);
//    print '<pre>';

    $transactions = [];

    // Loop through roomiesThatOwe array 
    foreach ($roomiesThatOwe as $key => $value) {
        // transfer funds by $0.01 by looping through roomiesOwed until either roomieThatOwe has paid
        // or roomieOwed has been paid. Once either has happened, spit out the log of who paid who
        // and how much
        //echo 'user who owes: ' . $value['fName']. ' and owes ' .$value['amount']. '<br><br>';
        while ($roomiesThatOwe[$key]['amount'] > 0.02) {
            foreach($roomiesOwed as $key2 => $value2) {
            //echo 'user owed: ' . $value2['fName']. ' and owed ' .$value2['amount']. '<br><br>';
            // while user who owes still owes greater than 0, send
                if ($roomiesOwed[$key2]['amount'] > 0) {
                    //echo $value['amount'] . '<br>';
                    //$value['amount'] = round(($value['amount'] - 0.01), 2);
                    $amountPaid = 0;

                    //$value['amount'] = $value['amount'] - $value2['amount'];
                    $amountPaid = round(($roomiesThatOwe[$key]['amount'] - $roomiesOwed[$key2]['amount']), 2);

                    if ($amountPaid < 0) {
                        if ($roomiesThatOwe[$key]['amount'] > 0) {

                            //echo $value['fName'] . ' paid ' . $value2['fName'] . ' $ ' . $roomiesThatOwe[$key]['amount'] . '<br>';

                            $transactions[] = ['amountToPay' => $roomiesThatOwe[$key]['amount'], 'userToPay' => $value['fName'], 'userToBePaid' => $value2['fName'], 'userIDToPay' => $value['user_id'], 'userIDToBePaid' => $value2['user_id'] ];
//  
                            $roomiesOwed[$key2]['amount'] = abs($amountPaid);
                            $roomiesThatOwe[$key]['amount'] = 0;


                        }
                    } else {
                        
                            //echo $value['fName'] . ' paid ' . $value2['fName'] . ' $ ' . $roomiesOwed[$key2]['amount'] . '<br>';
                        
                            
                            $transactions[] = ['amountToPay' => $roomiesOwed[$key2]['amount'], 'userToPay' => $value['fName'], 'userToBePaid' => $value2['fName'], 'userIDToPay' => $value['user_id'], 'userIDToBePaid' => $value2['user_id'] ];
//
                            $roomiesThatOwe[$key]['amount'] = $amountPaid;
                            $roomiesOwed[$key2]['amount'] = 0;

                    }
                }

            }
        }
    }

//    print '<pre>';
//    print_r($transactions);
//    print '<pre>';

} else {
//    print '<pre>';
//    print_r('There are no costs to split.');
//    print '<pre>';
    $transactions = [];
}


echo json_encode(array($itemTotalsUsers, $transactions));

mysqli_close($link);


?>
