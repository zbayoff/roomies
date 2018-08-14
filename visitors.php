<?php 

session_start();
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: login.php");
}

// require config file
include '../../../hidden/config.php';


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
    <title>Roomies-Roommate Management Visitors</title>
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

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="info-block col-xl-6 col-lg-8 col-md-10">
                <div id="visitor-header" class="info-header">
                    <div class="info-title">
                        <h2>Visitors</h2>
                        <div class="info-title-btn">
                            <button class="add-edit-btn btn" type="button" data-toggle="collapse" data-target="#edit-info-container-visitors"><span class="fa fa-edit"></span>
                                </button>
                        </div>
                    </div>
                    <div class="info-paras">
                        <p>Have a guest coming to visit your place for a couple days? </p>
                        <p>Add them to the visitor list! </p>
                        <p>(If it's okay with the other Roomies first).</p>
                    </div>
                </div>
                <!-----info block inner----->
                <div class="info-block-inner row">
                    <div id="edit-info-container-visitors" class="edit-info-container col-md-4 col-sm-4 order-sm-last collapse">
                        <div class="edit-info-inner">
                            <form id="choose-visitor-option-form">
                                <div class="form-group">
                                    <label class="add-edit-visitor" for="add-edit-visitor">Add, Edit, or Remove Visitor</label>
                                    <select id="add-edit-select" class="form-control">
                                        <option value="Choose" selected>Choose</option>
                                        <option value="Add">Add</option>
                                        <option value="Edit">Edit</option>
                                        <option value="Remove">Remove</option>
                                    </select>
                                </div>
                            </form>
                            <form method="post" id="edit-visitor-form" action="">
                                <div class="edit-visitor-container">
                                    <div class="form-group">
                                        <label for="visitorName">Visitor Name:</label>
                                        <select name="visitorName" class="form-control" id="editVisitorsList">
                                            <option id="edit-visitor-default-select-option" value="Select" selected>Select</option> 
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="newVisitorName">New Visitor Name:</label>
                                        <input name="newVisitorName" class="form-control" type="text">
                                    </div>
                                    <div class="form-group">
                                        <label for="userName">Roomie (host) Name:</label>
                                        <select name="userName" class="form-control" id="userListEdit">
                                            <?php  
                                                foreach($usersTableArray as $key=>$value) {
                                                    /* if key value is user_id for the session user, add the attribute 'selected' */
                                                    if ($key == $userID) {
                                                        echo "<option value= '$key' selected>" . $value . "</option>";
                                                    } else {
                                                         echo "<option value= '$key'>" . $value . "</option>";
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="arrivalDate">Arrival Date: </label>
                                        <input name="arrivalDate" class="form-control inputDate" type="date" placeholder="mm/dd/yyyy">
                                    </div>
                                    <div class="form-group">
                                        <label for="leaveDate">Leave Date: </label>
                                        <input name="leaveDate" class="form-control inputDate" type="date" placeholder="mm/dd/yyyy">
                                    </div>
                                    <button id="edit-visitor-btn" type="submit" class="btn btn-primary">Save Changes</button>
                                    <button id="edit-visitor-btn-cancel" type="button" class="btn btn-secondary">Cancel</button>
                                </div>
                            </form>
                            <form method="post" id="add-visitor-form" action="">
                                <div class="add-visitor-container">
                                    <div class="form-group">
                                        <label for="visitorName">Visitor Name:</label>
                                        <input id="visitorName" name="visitorName" class="form-control" type="text">
                                        <?php if(isset($visitor_name_err)) {echo $visitor_name_err;}  ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="userName">Roomie (host) Name:</label>
                                        <select name="userName" class="form-control" id="userListAdd">
                                            <?php  
                                                foreach($usersTableArray as $key=>$value) {
                                                    /* if key value is user_id for the session user, add the attribute 'selected' */
                                                    if ($key == $userID) {
                                                        echo "<option value= '$key' selected>" . $value . "</option>";
                                                    } else {
                                                         echo "<option value= '$key'>" . $value . "</option>";
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="arrivalDate">Arrival Date: </label>
                                        <input name="arrivalDate" class="form-control inputDate" type="date" placeholder="mm/dd/yyyy">
                                    </div>
                                    <div class="form-group">
                                        <label for="leaveDate">Leave Date: </label>
                                        <input name="leaveDate" class="form-control inputDate" type="date" placeholder="mm/dd/yyyy">
                                    </div>
                                    <button id="add-visitor-btn" type="submit" class="btn btn-primary">Add Visitor</button>
                                    <button id="add-visitor-btn-cancel" type="button" class="btn btn-secondary">Cancel</button>
                                </div>
                            </form>
                            <!-----add form----->
                            <form method="post" id="remove-visitor-form" action="">
                                <div class="remove-visitor-container">
                                    <div class="form-group">
                                        <label for="visitorName">Visitor Name:</label>
                                        <select name="visitorName" class="form-control" id="removeVisitorList">
                                            <option id="visitor-default-select-option" value="Select" selected>Select</option>
                                        </select>
                                    </div>
                                    <button id="remove-visitor-btn" type="submit" class="btn btn-primary">Remove Visitor</button>
                                    <button id="remove-visitor-btn-cancel" type="button" class="btn btn-secondary">Cancel</button>
                                </div>
                                <!----remove-chore-container-->
                            </form>
                            <!-----remove form----->
                        </div>
                        <!-----edit info inner----->
                    </div>
                    <!-----edit info container----->
                    <div class="current-info-container-visitors col-md-8 col-sm-8  order-sm-first">
                        <div class="current-info-header">
                            <h3>Upcoming Visitors</h3>
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
    </div>
    <!-----row----->
    <!-----Container----->

    <?php require_once("assets/partials/footer.php");?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="assets/js/visitors.js"></script>

</body>

</html>
