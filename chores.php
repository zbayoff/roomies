<?php 

session_start();
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: login.php");
}

// require config file
include '../../hidden/config.php';

// define variables
$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];
$currentUserfName = $_SESSION['first_name'];
$currentUserlName = $_SESSION['last_name'];
$currentUserEmail = $_SESSION['email'];
$userID = $_SESSION['user_id'];

// query to output all users associated with the current group

$sql = "SELECT users.user_id, fName, lName, email, group_name FROM user2group
        JOIN users on users.user_id = user2group.user_id
        JOIN groups on groups.group_id = user2group.group_id 
        WHERE groups.group_id = '$groupID' AND user2group.deleted = '0'";

$result = mysqli_query($link, $sql);

if($result->num_rows > 0){
    while ($row = $result->fetch_assoc()) {

        $usersTableArray[$row['user_id']] = $row['fName'];
        
        $fNames[] = $row['fName'];
        $lNames[] = $row['lName'];
        $emails[] = $row['email'];
        $userIDS[] = $row['user_id'];

    }
}

mysqli_close($link);

?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Roomies-Roommate Management Chores</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-115178608-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-115178608-1');

    </script>
    
</head>

<body>

    <?php require_once("assets/partials/header.php");?>
    <?php require_once("assets/partials/nav.php");?>

    <!-----End Nav----->

    <div class="container-fluid page-container">
        <div class="row justify-content-center">
            <div class="info-block col-xl-6 col-lg-8 col-md-10">
                <div id="chore-header" class="info-header">
                    <div class="info-title">
                        <h2>Chores</h2>
                        <div class="info-title-btn">
                            <button class="add-edit-btn btn" type="button" data-toggle="collapse" data-target="#edit-info-container-chores"><span class="fa fa-edit"></span>
                            </button>
                        </div>
                    </div>
                    <div class="info-paras">
                        <p>Schedule a new chore and assign a day of the week or frequency to each Roomie. </p>
                        <p>Add, edit or remove current chores to accomodate everyone's schedule. </p>
                    </div>
                </div>
                <!-----info block inner----->
                <div class="info-block-inner row">
                    <div id="edit-info-container-chores" class="edit-info-container col-sm-6 order-sm-last collapse ">
                        <div class="edit-info-inner">
                            <form>
                                <div class="form-group">
                                    <label class="add-edit-chore" for="chore_add_edit">Add, Edit, or Remove Chore</label>
                                    <select id="add-edit-select" class="form-control">
                                        <option value="Choose" selected>Choose</option>
                                        <option value="Add">Add</option>
                                        <option value="Edit">Edit</option>
                                        <option value="Remove">Remove</option>
                                    </select>
                                </div>
                            </form>
                            <form method="post" id="edit-chore-form" action="">
                                <div class="edit-chore-container">
                                    <div class="form-group">
                                        <label for="choreName">Chore Name:</label>
                                        <select name="choreName" class="form-control" id="editChoresList">
                                            <option id="edit-chore-default-select-option" value="Select" selected>Select</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="newChoreName">New Chore Name:</label>
                                        <input name="newChoreName" class="form-control" type="text">
                                    </div>
                                    <p>Assign Chore By:</p>
                                    <div class="form-group">
                                        <label for="numDaysRadioEdit">
                                        <input id="numDaysRadioEdit" type="radio" name= "edit-chore-freq" value="numDays" checked><span>Every </span><input name="numDays" type="number" class="" size="4">Days
                                            </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="dayOfWeekRadioEdit">
                                        <input id="dayOfWeekRadioEdit" type="radio" name= "edit-chore-freq" value="dayOfWeek">Day of Week</label>
                                    </div>
                                    <div class="edit-day-of-week-container">
                                        <div class="form-group">
                                            <label for="">Day of Week: </label>
                                            <?php
                                                foreach($usersTableArray as $key=>$value) {
                                                    /* if key value is user_id for the session user, add the attribute 'selected' */
                                                         
                                                    echo "<div class= 'form-group user-name'><p>" . $value . "</p>
                                                    
                                                    <div class='form-check form-check-inline'>
                                                            <label class='form-check-label'>
                                                                <input class='form-check-input edit-checkbox' type= 'checkbox' id='edit-checkbox-dow-$key' value= ''>Exempt from chore?
                                                            </label>
                                                    </div>
                                                            <select id='user$key-dayOfWeek' name='userDayOfWeek[$key]' class='form-control'>
                                                                <option value='Mon'>Mon</option>
                                                                <option value='Tue'>Tue</option>
                                                                <option value='Wed'>Wed</option>
                                                                <option value='Thu'>Thu</option>
                                                                <option value='Fri'>Fri</option>
                                                                <option value='Sat'>Sat</option>
                                                                <option value='Sun'>Sun</option>
                                                            </select>";
                                                    echo "</select>";
                                                    echo "</div>";
                                                }
                                            ?>
                                        </div>
                                    </div>
                                    <!------edit-day-of-week-container------>
                                    <div class="edit-chore-order-container">
                                        <div class="form-group">
                                            <label for="">Chore Rotation Order: (Who goes first)</label>
                                            <?php  
                                            
                                                $numUsers = count($usersTableArray);
                                            
                                                foreach($usersTableArray as $key=>$value) {
                                                    /* if key value is user_id for the session user, add the attribute 'selected' */
                                                    
                                                    echo "<div class= 'form-group user-name'><p>" . $value . "</p>
                                                    
                                                    <div class='form-check form-check-inline'>
                                                            <label class='form-check-label'>
                                                                <input class='form-check-input edit-checkbox' type= 'checkbox' id='edit-checkbox-num-$key' value= ''>Exempt from chore?
                                                            </label>
                                                    </div>
                                                            <select id='user$key-order' name='userOrder[$key]' class='form-control edit-chore-user-order-select'>";
                                                    
                                                    for($i = 1; $i<=$numUsers; $i++) {
                                                        echo "<option value='$i'>$i</option>";
                                                    }
                                                    
                                                    echo "</select>";
                                                    echo "</div>";
                                                }
                                            ?>

                                        </div>
                                    </div>
                                    <!------edit-order-container------>
                                    <div class="form-group start-date-container">
                                        <label for="startDate">Start Date: </label>
                                        <input name="startDate" class="form-control inputDate" type="date" placeholder="mm/dd/yyyy">
                                    </div>
                                    <button id="edit-chore-btn" type="submit" class="btn btn-primary">Save Changes</button>
                                    <button id="edit-chore-btn-cancel" type="button" class="btn btn-secondary">Cancel</button>
                                </div>
                            </form>
                            <form method="post" id="add-chore-form" action="">
                                <div class="add-chore-container">
                                    <div class="form-group">
                                        <label for="choreName">Chore Name:</label>
                                        <input name="choreName" class="form-control" type="text">
                                    </div>
                                    <p>Assign Chore By:</p>
                                    <div class="form-group">
                                        <label for="numDaysRadioAdd">
                                        <input id="numDaysRadioAdd" type="radio" name= "add-chore-freq" value="numDays" checked><span>Every </span><input name="numDays" type="number" class="" size="4" min="1">Days
                                            </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="dayOfWeekRadioAdd">
                                        <input id="dayOfWeekRadioAdd" type="radio" name= "add-chore-freq" value="dayOfWeek">Day of Week</label>
                                    </div>
                                    <div class="add-day-of-week-container">
                                        <div class="form-group">
                                            <label for="">Day of Week: </label>
                                            <?php
                                                foreach($usersTableArray as $key=>$value) {
                                                    /* if key value is user_id for the session user, add the attribute 'selected' */
                                                         
                                                    echo "<div class= 'form-group user-name'><p>" . $value . "</p>
                                                    
                                                    <div class='form-check form-check-inline'>
                                                            <label class='form-check-label'>
                                                                <input class='form-check-input add-checkbox' type= 'checkbox' id='add-checkbox-dow-$key' value= ''>Exempt from chore?
                                                            </label>
                                                    </div>
                                                            <select id='user$key-dayOfWeek' name='userDayOfWeek[$key]' class='form-control'>
                                                                <option value='Mon'>Mon</option>
                                                                <option value='Tue'>Tue</option>
                                                                <option value='Wed'>Wed</option>
                                                                <option value='Thu'>Thu</option>
                                                                <option value='Fri'>Fri</option>
                                                                <option value='Sat'>Sat</option>
                                                                <option value='Sun'>Sun</option>
                                                            </select>";
                                                    echo "</select>";
                                                    echo "</div>";
                                                }
                                            ?>

                                        </div>
                                    </div>
                                    <!------add-day-of-week-container------>
                                    <div class="add-chore-order-container">
                                        <label for="">Chore Rotation Order: (Who goes first)</label>
                                        <?php  
                                            
                                            $numUsers = count($usersTableArray);

                                            foreach($usersTableArray as $key=>$value) {
                                                /* if key value is user_id for the session user, add the attribute 'selected' */

                                                echo "<div class= 'form-group user-name'><p>" . $value . "</p>

                                                <div class='form-check form-check-inline'>
                                                        <label class='form-check-label'>
                                                            <input class='form-check-input add-checkbox' type= 'checkbox' id='add-checkbox-num-$key' value= ''>Exempt from chore?
                                                        </label>
                                                </div>
                                                        <select id='user$key-order' name='userOrder[$key]' class='form-control add-chore-user-order-select'>";

                                                for($i = 1; $i<=$numUsers; $i++) {
                                                    echo "<option value='$i'>$i</option>";
                                                }

                                                echo "</select>";
                                                echo "</div>";
                                            }
                                        ?>
                                    </div>
                                    <!------add-order-container------>
                                    <div class="form-group start-date-container">
                                        <label for="startDate">Start Date: </label>
                                        <input name="startDate" class="form-control inputDate" type="date" placeholder="mm/dd/yyyy">
                                    </div>
                                    <button id="add-chore-btn" type="submit" class="btn btn-primary">Add</button>
                                    <button id="add-chore-btn-cancel" type="button" class="btn btn-secondary">Cancel</button>
                                </div>
                            </form>
                            <form method="post" id="remove-chore-form" action="">
                                <div class="remove-chore-container">
                                    <h4></h4>
                                    <div class="form-group">
                                        <label for="choreName">Chore Name:</label>
                                        <select name="choreName" class="form-control" id="removeChoresList">
                                            <option id="chore-default-select-option" value="Select" selected>Select</option>
                                        </select>
                                    </div>
                                    <button id="remove-chore-btn" type="submit" class="btn btn-primary">Remove Chore</button>
                                    <button id="remove-chore-btn-cancel" type="button" class="btn btn-secondary">Cancel</button>
                                </div>
                                <!----remove-chore-container-->
                            </form>
                        </div>
                        <!-----edit info inner----->
                    </div>
                    <!-----edit info container----->
                    <div class="current-info-container-chores col-sm-6 order-sm-first ">
                        <div class="current-info-header">
                            <h3>Upcoming Chores</h3>
                        </div>
                        <div class="current-info-inner">
                        </div>
                        <!-----current info inner----->
                    </div>
                    <!-----current info container----->

                </div>
                <!-----info block inner----->
            </div>
            <!-----info block----->
        </div>
        <!-----row----->
    </div>
    <!-----Container Fluid----->

    <?php require_once("assets/partials/footer.php");?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="assets/js/chores.js"></script>

</body>

</html>
