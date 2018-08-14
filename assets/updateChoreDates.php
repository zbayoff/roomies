<?php 

// Get contents from existing JSON file and store in array $d.
$j = file_get_contents('chores.json');
$d = json_decode($j, true);

// Set timezone default to Eastern Standard Time
date_default_timezone_set('America/New_York');

// Sets the current actual date to full date ISO format
$currentDate = date('r');

// Initialize array for finding the week after the chosen start date. 
$weekAfterStartDate = array();

// Checks if the array $d is empty, which, if true, would skip the if statement
// If $d has data, then loop through array $d, updating start date and user day of week values.
if (!empty($d)){
    
    foreach($d as $key => $value) {
        $startDateISO = $value['startDate'];
        if(strtotime($currentDate) >= strtotime($startDateISO)){
            /*
                1. Set startDate to the Current Date. 
                2. Update php $d array startDate value
                3. Find week after new start Date and store in array
            */
            $startDateISO = $currentDate;
            $value['startDate'] = $startDateISO;
            $d[$key]['startDate'] = $value['startDate'];
            
            for ($i = 0; $i < 7; $i++) {
                $weekAfterStartDate[$i] = date('r', strtotime($startDateISO. ' + '.$i.'days'));
            }
            
            for ($i = 0; $i < 7; $i++) {
                if (date('D', strtotime($weekAfterStartDate[$i])) == date('D', strtotime($d[$key]['user1']['DayOfWeek']))) {
                    $d[$key]['user1']['DayOfWeek'] = $weekAfterStartDate[$i];
                }
            }
            
            for ($i = 0; $i < 7; $i++) {
                if (date('D', strtotime($weekAfterStartDate[$i])) == date('D', strtotime($d[$key]['user2']['DayOfWeek']))) {
                    $d[$key]['user2']['DayOfWeek'] = $weekAfterStartDate[$i];
                }
            }
            
            for ($i = 0; $i < 7; $i++) {
                if (date('D', strtotime($weekAfterStartDate[$i])) == date('D', strtotime($d[$key]['user3']['DayOfWeek']))) {
                    $d[$key]['user3']['DayOfWeek'] = $weekAfterStartDate[$i];
                }
            }
            
            for ($i = 0; $i < 7; $i++) {
                if (date('D', strtotime($weekAfterStartDate[$i])) == date('D', strtotime($d[$key]['user4']['DayOfWeek']))) {
                    $d[$key]['user4']['DayOfWeek'] = $weekAfterStartDate[$i];
                }
            }
        } // End of if statement
    } // End of foreach loop
    
    // Create new array, encode and put back to JSON file
    $dJSON = json_encode($d);
    file_put_contents('chores.json', $dJSON);

} // If statement

?>
