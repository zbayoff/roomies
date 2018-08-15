<?php 

session_start();
// If session variable not set, redirect to login page
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: login.php");
}

if (empty($_SESSION['group_name']) || !isset($_SESSION['group_name']) || !isset($_SESSION['group_id']) || empty($_SESSION['group_id'])) {
    header("location: groups.php");
}

include '../../hidden/config.php';

// define and initialize variables
$groupname = "";

if (isset($_SESSION['group_id'])) {
    $groupID = $_SESSION['group_id'];
}

if (isset($_SESSION['group_name'])) {
    $groupname = $_SESSION['group_name'];
}

if (isset($_SESSION['user_id'])) {
    $userID = $_SESSION['user_id'];
}

$fNames = [];
$lNames = [];
$emails = [];
$userIDS = [];
$usersTableArray = [];

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
    <title>Roomies-Roomate Management Roomie Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
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
        <div id="groupHeader" class="row justify-content-center">
            <div>
                <h3>Welcome to <span class="ml-1"> <?php echo $_SESSION['group_name']; ?></span> </h3>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="info-block col-lg-6">
                <div class="info-header">
                    <div class="info-title-index">
                        <h2>Roomie Dashboard</h2>
                    </div>
                    <div class="info-paras">
                        <p>The Dashboard shows a list of scheduled events for each Roomie. </p>
                        <p>Choose below to display yours or other Roomies' upcoming events. </p>
                    </div>
                    <div class="roomie-select">
                        <form class="form-inline">
                            <label class="mr-2" for="roomie-dashboard-select">Roomie: </label>
                            <select id="roomie-dashboard-select" class="custom-select">
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
                        </form>
                    </div>
                </div>
                <!-----info block inner----->
                <div class="info-block-inner">
                    <div class="current-info-container">
                        <div class="current-info-inner row justify-content-center">
                            <div class="col-lg-8 col-md-6 col-sm-8">
                                <div class="card roomie-card" id="">
                                    <h3></h3>
                                    <div class="chore-section">
                                        <h4 class="card-header" href="#collapseOne" data-toggle="collapse">Chores<span class="fa fa-angle-down" style="font-size:24px"></span></h4>
                                        <div class="chore-inner collapse show" id="collapseOne">
                                        </div>
                                    </div>
                                    <div class="visitor-section">
                                        <h4 class="card-header" href="#collapseFive" data-toggle="collapse">Visitors<span class="fa fa-angle-down" style="font-size:24px"></span></h4>
                                        <div class="visitor-inner collapse show" id="collapseFive">
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    <!-----Container----->

    <?php require_once("assets/partials/footer.php");?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="assets/js/index.js"></script>

</body>

</html>
