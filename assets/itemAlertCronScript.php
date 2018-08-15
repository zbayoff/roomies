<?php 

chdir(dirname(__FILE__));

include '../../../hidden/config.php';

// query each group and store in groups array.
$sql = "SELECT group_id
        FROM groups";
$result = mysqli_query($link, $sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $groupIDs[] = $row['group_id'];
    }
}
    
// -----Calculate Item Summary Section-------
date_default_timezone_set('America/New_York');
$currentDate = date('Y\-m\-d', time());
$currentTime = date('H:i:s', time());

// query to output all users associated with the current group
$items = [];
$itemDate = date('Y\-m', strtotime($currentDate . '- 1 month'));

$itemMonth = "";
$itemYear = "";

// splice date string from itemDate to extract year in yyyy and month in mm
$itemYear = substr($itemDate, 0, 4);
$itemMonth = substr($itemDate, 5, 6);

foreach ($groupIDs as $key3 => $value3) {
    
        // query users TABLE to select users from the group (that are not deleted).
        $sql = "SELECT users.user_id, users.fName, user2group.group_id, user2group.deleted, groups.group_name, users.phone, users.phone_carrier, users.item_alert_status
                FROM user2group
                JOIN users ON users.user_id = user2group.user_id
                JOIN groups ON groups.group_id = user2group.group_id
                WHERE user2group.group_id = '$value3'";
        $result = mysqli_query($link, $sql);

        $users = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $groupName = $row['group_name'];
                $user_id = $row['user_id'];
                $fName = $row['fName'];
                $deleted = $row['deleted'];
                $phone = $row['phone'];
                $phoneCarrier = $row['phone_carrier'];
                $itemAlertStatus = $row['item_alert_status'];
                $users[] = ['user_id' => $user_id,'fName' => $fName, 'phone' => $phone, 'phone_carrier' => $phoneCarrier, 'item_alert_status' => $itemAlertStatus, 'deleted' => $deleted];
            }
        }

        $groupIDs = [];
    
        $sql = "SELECT items.item_id, item_name, item_cost, items.user_id, users.fName, time_created, groups.group_name
                FROM items
                JOIN users on items.user_id = users.user_id
                JOIN groups on items.group_id = groups.group_id
                WHERE groups.group_id = '$value3' AND YEAR(time_created) = '$itemYear' AND MONTH(time_created) = '$itemMonth'
                ORDER BY time_created ASC";
        $result = mysqli_query($link, $sql);

        $items = [];
        $itemTotals = [];
        $i = 0;
        $row = [];

        $hasItems = 0;
        
        if ($result->num_rows > 0) {
            $hasItems = 1;
            while ($row = $result->fetch_assoc()) {

                $itemID = $row['item_id'];
                $itemName = $row['item_name'];
                $itemCost = $row['item_cost'];
                $userFname = $row['fName'];
                $userID = $row['user_id'];
                $groupName = $row['group_name'];

                $items[$i] = ['user_id' => $userID, 'user_Fname' => $userFname, 'item_cost' => $itemCost];

                $i++;
            }
        } else {
            $hasItems = 0;
        }
    
        if ($hasItems == 1) {

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

            $totalItemCost = array_sum($itemTotals);

            $numUsers = count($itemTotalsUsers);
            // calculate cost to split if there are more than one users
            if ($numUsers > 1) {
                $splitTotalCost = ($totalItemCost / $numUsers);

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

                $transactions = [];

                // Loop through roomiesThatOwe array 
                foreach ($roomiesThatOwe as $key => $value) {
                    // transfer funds by $0.01 by looping through roomiesOwed until either roomieThatOwe has paid
                    // or roomieOwed has been paid. Once either has happened, spit out the log of who paid who
                    // and how much
                    while ($roomiesThatOwe[$key]['amount'] > 0.02) {
                        foreach($roomiesOwed as $key2 => $value2) {
                        // while user who owes still owes greater than 0, send
                            if ($roomiesOwed[$key2]['amount'] > 0) {
                                //$value['amount'] = round(($value['amount'] - 0.01), 2);
                                $amountPaid = 0;

                                //$value['amount'] = $value['amount'] - $value2['amount'];
                                $amountPaid = round(($roomiesThatOwe[$key]['amount'] - $roomiesOwed[$key2]['amount']), 2);

                                if ($amountPaid < 0) {
                                    if ($roomiesThatOwe[$key]['amount'] > 0) {

                                        $transactions[] = ['amountToPay' => $roomiesThatOwe[$key]['amount'], 'userToPay' => $value['fName'], 'userToBePaid' => $value2['fName'], 'userIDToPay' => $value['user_id'], 'userIDToBePaid' => $value2['user_id'] ];
                                        
                                        $roomiesOwed[$key2]['amount'] = abs($amountPaid);
                                        $roomiesThatOwe[$key]['amount'] = 0;

                                    }
                                } else {
                                    
                                        $transactions[] = ['amountToPay' => $roomiesOwed[$key2]['amount'], 'userToPay' => $value['fName'], 'userToBePaid' => $value2['fName'], 'userIDToPay' => $value['user_id'], 'userIDToBePaid' => $value2['user_id'] ];
                                    
                                        $roomiesThatOwe[$key]['amount'] = $amountPaid;
                                        $roomiesOwed[$key2]['amount'] = 0;

                                }
                            }
                        }
                    }
                }

                // call item alert function
                itemAlert ($value3, $transactions, $users, $groupName, $deleted);

            } else {
                $transactions = [];
            }
        
        } else {
            $transactions = [];
            itemAlert ($value3, $transactions, $users, $groupName, $deleted);
        }
        
    } // foreach group ID

function itemAlert ($groupID, $transactions, $roomiesArray, $groupName, $deleted) {
    
     //Phone carrier gateway suffixes
    $AllTel = "@text.wireless.alltel.com";
    $ATAT = "@txt.att.net";
    $BoostMobile= "@myboostmobile.com";
    $Sprint = "@messaging.sprintpcs.com";
    $TMobile = "@tmomail.net";
    $Verizon = "@vtext.com";
    $VirginMobile = "@vmobl.com";
    
    // Loop through roomiesArray and see if the user_id matches either the userIDtoPay or userIDToBePaid, if so, send text.
    foreach ($roomiesArray as $key => $value) {
        $alertArray = [];
        $roomieID = $value['user_id'];
        $roomiefName = $value['fName'];
        $roomiePhone = $value['phone'];
        $roomiePhoneCarrier = $value['phone_carrier'];
        $roomieItemAlertStatus = $value['item_alert_status'];
        $roomieDeleted = $value['deleted'];
        
        //echo 'Roomie ID to be alerted: ' . $roomieID . '('.$roomiefName.')' . '<br><br>';
        
        foreach ($transactions as $key2 => $value2) {
            
            $roomieAmountToPay = round($value2['amountToPay'], 2);
            $roomieUserToPay = $value2['userToPay'];
            $roomieUserToBePaid = $value2['userToBePaid'];
            $roomieUserIDToPay = $value2['userIDToPay'];            
            $roomieUserIDToBePaid = $value2['userIDToBePaid'];
            
            if ($roomieID == $roomieUserIDToPay) {
                $alertArray[] = "You owe " . $roomieUserToBePaid . " $" . $roomieAmountToPay ."\r\n";
            }
            
            if ($roomieID == $roomieUserIDToBePaid) {
                $alertArray[] = $roomieUserToPay . " owes you $" . $roomieAmountToPay . "\r\n";
            }            
        }
        
        if ($roomieDeleted == 0) {
            
            if ($roomieItemAlertStatus == 1) {
                if (empty($alertArray)) {
                    $msg = "All item bills are caught up!";
                    //echo 'msg is : ' . $msg . '<br>';
                } else {

                    $msg = implode(" ", $alertArray) . "\r\n";
                }

                //  set "to" field
                switch($roomiePhoneCarrier) {
                    case "atat":
                        $to = $roomiePhone . $ATAT;
                        break;
                    case "verizon":
                        $to = $roomiePhone . $Verizon;
                        break;
                    case "sprint":
                        $to = $roomiePhone . $Sprint;
                        break;
                    case "tmobile":
                        $to = $roomiePhone . $TMobile;
                        break;
                    case "boostmobile":
                        $to = $roomiePhone . $BoostMobile;
                        break;
                    case "virginmobile":
                        $to = $roomiePhone . $VirginMobile;
                        break;
                    default:
                        $to = "";
                }

                // set subject field
                $subject = " " . $groupName . ": \r\n Item Monthly Bill Notification.";

                // set from field
                $from = "From: Roomies <roomiesmanagement@gmail.com>";
                mail($to, $subject, $msg, $from);
                
            }
        }
    }
} // itemAlert function

mysqli_close($link);

?>
