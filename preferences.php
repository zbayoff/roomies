<?php 

session_start();
// If session variable not set, redirect to login page
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: login.php");
}

include '../../hidden/config.php';


// define and initialize variables
$groupname = "";
$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];
$currentUserfName = $_SESSION['first_name'];
$currentUserlName = $_SESSION['last_name'];
$currentUserEmail = $_SESSION['email'];
$userID = $_SESSION['user_id'];


?>


<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Roomies-Roommate Management Preferences</title>
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
            <div class="info-block col-lg-8">
                <div class="info-header">
                    <div id="preferences-header" class="info-title">
                        <h2>Preferences</h2>
                    </div>
                    <div class="info-paras">
                        <p>Set up to receive notifications via text to alert you when it is your chore day, a visitor is coming, or the items bill is created.</p>
                    </div>
                </div>
                <!-----info block inner----->
                <div class="info-block-inner">
                    <div class="current-info-container">
                        <div class="current-info-inner row justify-content-center">
                            <div class="col-lg-8 col-md-6 col-sm-8">
                                <div class="card preference-card" id="">
                                    <div class="user-info-section">
                                        <h4 class="card-header" href="#collapseTwo" data-toggle="collapse">Account Info<span class="fa fa-angle-down" style="font-size:24px"></span></h4>
                                        <div class="user-info-inner card-body collapse show" id="collapseTwo">
                                            <form id="user-info-form" method="post" action="">
                                                <div class="form-group row">
                                                    <label class="col-5 col-form-label" for="first-name-edit">First Name</label>
                                                    <div class="col-7">
                                                        <input class="form-control" id="first-name-edit" name="fName-edit" type="text">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-5 col-form-label" for="last-name-edit">Last Name</label>
                                                    <div class="col-7">
                                                        <input class="form-control" id="last-name-edit" name="lName-edit" type="text">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-5 col-form-label" for="email-edit">Email</label>
                                                    <div class="col-7">
                                                        <input class="form-control" id="email-edit" name="email-edit" type="email">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-5 col-form-label" for="phone-edit">Phone</label>
                                                    <div class="col-7">
                                                        <input class="form-control" id="phone-edit" name="phone-edit" type="tel" maxlength="10" placeholder="ex. 5555555555">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-5 col-form-label" for="phone-carrier-edit">Phone Carrier</label>
                                                    <div class="col-7">
                                                        <select class="form-control" id="phone-carrier-edit" name="phone-carrier-edit">
                                                        <option value="atat">AT&amp;T</option>
                                                        <option value="verizon">Verizon</option>
                                                        <option value="sprint">Sprint</option>
                                                        <option value="boostmobile">Boost Mobile</option>
                                                        <option value="virginmobile">Virgin Mobile</option>
                                                        <option value="tmobile">TMobile</option>
                                                    </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-primary" id="" type="submit">Save Changes</button>
                                                </div>
                                            </form>
                                            <!---Form--->
                                        </div>
                                    </div>
                                    <!--user info section--->
                                    <div class="notification-section">
                                        <h4 class="card-header" href="#collapseSix" data-toggle="collapse">Notifications<span class="fa fa-angle-down" style="font-size:24px"></span></h4>
                                        <div class="notification-inner card-body collapse show" id="collapseSix">
                                            <form id="notification-form" method="post" action="">
                                                <div class="item-row row">
                                                    <div class="col-6">
                                                        <h5>Items</h5>
                                                        <p id="item-time-msg">You will recieve a notification via text on the first of each month.</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <label class="" for="item-status">
                                                        <input class="" id="item-status" type="checkbox" name="item-alert-check">Item Monthly Bill Alert
                                                        </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="chore-row row">
                                                    <div class="col-6">
                                                        <h5>Chores</h5>
                                                        <p id="chore-time-msg">You will recieve a notification via text on the days of your chores.</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <label class="" for="chore-status">
                                                        <input id="chore-status" type="checkbox" name="chore-alert-check">Chore Alert
                                                    </label>
                                                        </div>
                                                        <div id="chore-notification-wrapper" class="form-group">
                                                            <label class="" for="chore-alert-time">Alert Time of Day</label>
                                                            <select class="form-control" id="chore-alert-time" name="chore-alert-time">
                                                            <option value="Select" selected>Select</option>
                                                        <?php for($i = 1; $i <= 24; $i++): ?>
                                                            <option value="<?php  echo(date("H:i", strtotime("$i:00"))); ?>"><?php echo(date("h:iA", strtotime("$i:00"))); ?></option>
                                                        <?php endfor; ?>
                                                        </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="visitor-row row">
                                                    <div class="col-6">
                                                        <h5>Visitors</h5>
                                                        <p id="visitor-time-msg">You will recieve a notification via text on the arrival date of your visitors.</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <label class="" for="visitor-status">
                                                        <input id="visitor-status" type="checkbox" name="visitor-alert-check">Visitor Alert
                                                    </label>
                                                        </div>
                                                        <div id="visitor-notification-wrapper" class="form-group">
                                                            <label class="" for="visitor-alert-time">Alert Time of Day</label>
                                                            <select class="form-control" id="visitor-alert-time" name="visitor-alert-time">
                                                            <option value="Select" selected>Select</option>
                                                        <?php for($i = 1; $i <= 24; $i++): ?>
                                                            <option value="<?php  echo(date("H:i", strtotime("$i:00"))); ?>"><?php echo(date("h:iA", strtotime("$i:00"))); ?></option>
                                                        <?php endfor; ?>
                                                        </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="notification-save-container">
                                                    <button class="btn btn-primary" id="" type="submit">Save Changes</button>
                                                </div>
                                            </form>
                                            <!---Form--->
                                        </div>
                                        <!--notification inner--->
                                    </div>
                                    <!--notification section--->
                                </div>
                                <!--card--->
                            </div>
                        </div>
                        <!-----current info inner----->
                    </div>
                    <!-----current info container----->
                </div>
                <!-----info block----->
            </div>
            <!---Info Block --->
        </div>
    </div>
    <!-----container----->

    <?php require_once("assets/partials/footer.php");?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="assets/js/preferences.js"></script>

</body>

</html>
