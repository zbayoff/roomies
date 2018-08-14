<?php 

session_start();
// If session variable not set, redirect to login page
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: login.php");
}

// require config file
require_once '../../../hidden/config.php';

// define and initialize variables
$groupname = "";
$groupID = $_SESSION['group_id'];
$groupname = $_SESSION['group_name'];
$currentUserfName = $_SESSION['first_name'];
$currentUserlName = $_SESSION['last_name'];
$currentUserEmail = $_SESSION['email'];
$userID = $_SESSION['user_id'];

$users = [];
$items = [];

// query to output all users associated with the current group
    
$sql = "SELECT item_id, item_name, item_cost, items.user_id, users.fName, items.group_id, time_created
        FROM items
        JOIN users on users.user_id = items.user_id
        JOIN groups on groups.group_id = items.group_id 
        WHERE groups.group_id = '$groupID'";

$result = mysqli_query($link, $sql);

if($result->num_rows > 0){
    while ($row = $result->fetch_assoc()) {

        $items[] = $row;
    }
}

$sql = "SELECT users.user_id, users.fName
        FROM user2group
        JOIN users on users.user_id = user2group.user_id
        JOIN groups on groups.group_id = user2group.group_id
        WHERE groups.group_id = '$groupID' AND user2group.deleted = '0'";

$result = mysqli_query($link, $sql);

if($result->num_rows > 0){
    while ($row = $result->fetch_assoc()) {

        $users[] = $row;
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
    <title>Roomies-Roommate Management Items</title>
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
            <div class="info-block col-xl-8 col-lg-8 col-md-10">
                <div class="info-header">
                    <div class="info-title-index">
                        <h2>Items</h2>
                    </div>
                    <div class="info-paras">
                        <p>This page allows Roomies to add/edit, and delete items that they've purchased for the household.</p>
                        <p>The items purchased will total up at the end of each month and calculate the bill for each user.</p>
                    </div>
                </div>
                <!-----info block inner----->
                <div class="info-block-inner">
                    <div class="current-info-container">
                        <div class="current-info-inner row justify-content-center">
                            <div id="select-month-container" class="col-12 mb-3">
                                <p class="d-inline">View Month:</p>
                                <select id="item-month-select" class="d-inline">
                                </select>
                            </div>
                            <div class="table-container col-12" id="">
                                <table id="items-table" class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Cost</th>
                                            <th>Paid By</th>
                                            <th>Date</th>
                                            <th id="action-table-header">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="add-item-row">
                                            <td><input class="form-control" type="text" name="addItemname" placeholder="Item"></td>
                                            <td><input class="cost-input form-control" type="number" step="any" name="addItemCost" min="0" placeholder="$"></td>
                                            <td>
                                                <select class="form-control user-select" name="addUserID">
                                                    <?php foreach ($users as $user): ?>
                                                    <option value="<?php echo $user['user_id']; ?>"><?php echo $user['fName']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input class="form-control" type="date" onfocus="(this.type='date')" name="timeAdded"></td>
                                            <td><button id="addBtn" class="btn action-column" type="button" value="Add"><span class="fa fa-plus"></span></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="item-summary-container col-lg-6 col-md-8 col-sm-6">
                                <div class="item-summary-btn-wrapper">
                                    <button id="item-summary-btn" class="btn btn-primary" type="button">Calculate Item Summary</button>
                                </div>
                                <div class="item-summary-inner table-container-summary">
                                    <h4></h4>
                                    <table id="item-totals-table" class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Roomie</th>
                                                <th>Total Spent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                    <table id="user-owes-table" class="table table-sm">
                                    </table>
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
    <script src="assets/js/items.js"></script>

</body>

</html>
