<?php 

// initialize session
session_start();

// If session variable not set, redirect to login page
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: login.php");
}

// require config file
include '../../../hidden/config.php';

// Define variables and initialize
$groupname = $grouppassword = $groupID = "";
$groupname_err = $grouppassword_err = $groupnamejoin_err = $grouppasswordjoin_err = $groupnametojoin = $grouppasswordtojoin = "";
$usergrouptaken_err = "";
$groupnamelaunch_err = "";
$groupnameleave_err = "";

$groupnameleave_succ = "";

$currentgroupnamemsg = "";
$joinedgroupsmsg = "";
$groupArray = [];

$userID = $_SESSION['user_id'];

// check if user is logged in to a group, if yes, display the group
function displayGroups() {
    
    global $currentgroupnamemsg;
    $currentgroupnamemsg = "";

    global $groupArray;
    $groupArray = [];

    global $joinedgroupsmsg;
    $joinedgroupsmsg = "";

    include '../../../hidden/config.php';

    $userID = $_SESSION['user_id'];
    if (isset($_SESSION['group_name'])){
        
        $groupname = $_SESSION['group_name'];
        
        $currentgroupnamemsg = $groupname;

        // Check if user belongs to other groups in user2group, if yes, store all in array variable.
        // query user2group
        $sql = "SELECT group_name FROM user2group
                JOIN users on users.user_id = user2group.user_id
                JOIN groups on groups.group_id = user2group.group_id 
                WHERE users.user_id = '$userID' AND user2group.deleted = '0'";

        if($result = mysqli_query($link, $sql)){
            while ($row = $result->fetch_assoc()) {
                $groupArray[] = $row['group_name'];
            }

            $joinedgroupsmsg = "You have joined the following groups: <br>";

        } else {
            echo 'Something wrong happened.';
        }
        // Output display variable under Launch group section.

    } else {
        
        $sql = "SELECT group_name 
                FROM user2group
                JOIN users on users.user_id = user2group.user_id
                JOIN groups on groups.group_id = user2group.group_id 
                WHERE users.user_id = '$userID' AND user2group.deleted = '0'";

        if($result = mysqli_query($link, $sql)){

            while ($row = $result->fetch_assoc()) {
                $groupArray[] = $row['group_name'];
            }
            if (empty($groupArray)) {
                $joinedgroupsmsg = "You have not joined any groups.";
            } else {
                $joinedgroupsmsg = "You have joined the following groups: <br>";
            }
        } else {
            
        }
        $currentgroupnamemsg = 'You are not currently logged into a group.';
    }
};

displayGroups();

// Processing form data when form Create Group is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST"){
    
    // switch statements to execute based on which form was submitted.
    switch ($_POST['group-forms']){
        case 'create-group':
            // Validate and execute statements
            
            // Validate Group Name
            if (empty(trim($_POST['group-name']))) {
                $groupname_err = "Please enter your group name.";
            } 
            else {
                // Prepare select statement
                $sql = "SELECT group_id FROM groups WHERE group_name = ?";

                if ($stmt = mysqli_prepare($link, $sql)) {
                    // Bind variables to prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "s", $param_groupname);

                    // Set parameters
                    $param_groupname = trim($_POST['group-name']);

                    // Attempt to execute prepared statement
                    if(mysqli_stmt_execute($stmt)) {
                        //Store result
                        mysqli_stmt_store_result($stmt);
                        $groupname = trim($_POST['group-name']);
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                } else {
                    echo 'Prepared statement failed.';
                }

                // Close statement
                mysqli_stmt_close($stmt);
            } // else (input group name not empty)

            // Validate grouppassword
            if (empty(trim($_POST['group-password']))) {
                $grouppassword_err = "Please enter your group password.";
            } 
            else if (strlen(trim($_POST['group-password'])) < 6) {
                $grouppassword_err = "Password must have at least 6 characters.";
            }
            else {
                $grouppassword = trim($_POST['group-password']);
            } // else (input group password not empty)
            
            // Check input errors before inserting in database
            if(empty($groupname_err) && empty($grouppassword_err)) {
                
                // check if groupname already exists, and if so, display error.
                $sql = "SELECT group_name FROM groups WHERE group_name = '$groupname'";
                $result = mysqli_query($link, $sql);
                $row = $result->num_rows;
                
                if ($row == 1) {
                    $groupname_err = "This group<br> name is taken. Please choose another.";
                } else {
                    
                    // Prepare an INSERT statement
                    $sql = "INSERT INTO groups (group_name, group_password) VALUES
                    (?, ?)";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        //Bind variables to the prepared statement as parameters

                        mysqli_stmt_bind_param($stmt, 'ss', $param_groupname, $param_grouppassword);

                        // Set parameters
                        $param_groupname = $groupname;
                        $param_grouppassword = password_hash($grouppassword, PASSWORD_DEFAULT);
                        // Creates password hash

                        // Attempt to execute prepared statement
                        if(mysqli_stmt_execute($stmt)) {
                            $groupID = mysqli_insert_id($link);
                            $_SESSION['group_name'] = $groupname;
                            $_SESSION['group_id'] = $groupID;
                            header("location: index.php");
                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);

                    // Create variable from SESSION for logged in usersID
                    $userID = $_SESSION['user_id'];

                    // Create query to insert user_id from users and group_id from groups into user2group
                    $sql = "INSERT INTO user2group (user_id, group_id) VALUES ('$userID', '$groupID')";
                    $result = mysqli_query($link, $sql);

                }// check if group name already taken
            } // Check input errors if statement

            // Close connection
                mysqli_close($link);

            break;
            
        case 'join-group':
            // Validate and execute statements
            
             // Check if group-name-join is empty
            if (empty(trim($_POST['group-name-join']))) {
                $groupnamejoin_err = "Please enter the group name you want to join.";
            } else {
                // Prepare select statement
                $groupnametojoin = trim($_POST['group-name-join']);
            } // else (input group name to join not empty)
                
            // check if group-join-password is empty
            if (empty(trim($_POST['group-password-join']))) {
                $grouppasswordjoin_err = "Please enter the group password.";
            } else {
                $grouppasswordtojoin = trim($_POST['group-password-join']);
            }
            
            // Validate credentials
            // query select statement from user2group table to check whether the user belongs to their inputted group.
            // if they belong, error message they are already joined to the group
            // if they don't belong, insert their user_id to their inputted group in user2group

            if (empty($groupnamejoin_err) && empty($grouppasswordjoin_err)) {
                
                // Query groups TABLE to grab group_id from inputted group_name.
                $sql = "SELECT group_name, group_password FROM groups WHERE group_name = ?";

                // Prepared statement
                if ($stmt = mysqli_prepare($link, $sql)){

                    //Bind variables to prepared statement
                    mysqli_stmt_bind_param($stmt, "s", $param_groupnametojoin);
                    
                    // set parameters
                    $param_groupnametojoin = $groupnametojoin;
                    
                    // attempt to execute prepared statement
                    if (mysqli_stmt_execute($stmt)) {
                        // store result
                        mysqli_stmt_store_result($stmt);
                        
                        // check if groupname exists,if yes, then verify password
                        if (mysqli_stmt_num_rows($stmt) == 1) {
                            
                            // bind results
                            mysqli_stmt_bind_result($stmt, $groupnametojoin, $hashed_grouppassword);
                            
                            if(mysqli_stmt_fetch($stmt)) {
                                if(password_verify($grouppasswordtojoin, $hashed_grouppassword)) {
                                    // password is correct, now check if user has already joined the group
                                     // query user2group to see if user has joined user2group
                                    
                                    $userID = $_SESSION['user_id'];
                                    
                                    $sql = "SELECT group_id FROM groups WHERE group_name = '$groupnametojoin'";
                                    $result = mysqli_query($link, $sql);
                                    $row = $result->num_rows;

                                    if ($row == 1) {
                                        $a = mysqli_fetch_assoc($result);
                                        $groupID = $a["group_id"];
                                    }
                                    
                                    $sql = "SELECT user_id, group_id FROM user2group WHERE user_id = '$userID' AND group_id = '$groupID' AND user2group.deleted = '0'";
                                    $result = mysqli_query($link, $sql);
                                    $row = $result->num_rows;
                    
                                    // if row is found in user2group with both users id and group id
                                    if ($row == 1) {
                                        $usergrouptaken_err = "User has already joined this group";
                                    } else {
                                        
                                        // check if user has left group by deleted column
                                        $sql = "SELECT user_id, group_id FROM user2group WHERE user_id = '$userID' AND group_id = '$groupID' AND user2group.deleted = '1'";
                                        $result = mysqli_query($link, $sql);
                                        $row = $result->num_rows;
                                        if ($row == 1) {
                                            $_SESSION['group_name'] = $groupnametojoin;
                                            $sql = "UPDATE user2group SET deleted = '0' 
                                                    WHERE user_id = '$userID' AND group_id = '$groupID'";
                                            $result = mysqli_query($link, $sql);

                                            if ($result) {
                                                $_SESSION['user_id'] = $userID;
                                                $_SESSION['group_id'] = $groupID;
                                            }

                                            mysqli_close($link);

                                            header("location: index.php");
                                            
                                        } else {
                                        
                                            // Add user and group to user2groups
                                            $_SESSION['group_name'] = $groupnametojoin;


                                            $sql = "SELECT group_id FROM groups WHERE group_name = '$groupnametojoin'";
                                            $result = mysqli_query($link, $sql);
                                            $row = $result->num_rows;

                                            if ($row == 1) {
                                                $a = mysqli_fetch_assoc($result);
                                                $groupID= $a["group_id"];
                                            }

                                            // Create query to insert user_id from users and group_id from groups into user2group
                                            $sql = "INSERT INTO user2group (user_id, group_id) VALUES ('$userID', '$groupID')";
                                            $result = mysqli_query($link, $sql);

                                            if ($result) {
                                                $_SESSION['user_id'] = $userID;
                                                $_SESSION['group_id'] = $groupID;
                                            }

                                            mysqli_close($link);

                                            header("location: index.php");
                                            }
                                    }
                                } else {
                                    $grouppasswordjoin_err = 'The group password you entered was not valid.';
                                }
                            }
                            
                        } else {
                            $groupnamejoin_err = 'No account found with that group name.';
                        }
                        
                    } else {
                        echo "Oops! Something went wrong. Please try again.";
                    } // execute statement

                } // prepare if statement
                
                // close stmt
                mysqli_stmt_close($stmt);
                            
            } //check if error variables are empty
            
            break;
            
        case 'launch-group':
            // Validate and execute statements
            
            if (empty(trim($_POST['group-name-launch']))) {
                $groupnamelaunch_err = "Please enter a group name.";
            } else {
                $groupname = trim($_POST['group-name-launch']);
                
                //query DB to check if group_name is first: in the groups table (if no output error) and second: if the current user's ID and group ID cobination is in the user2group tabe (if not, output error that user has not joined).
                
                // check if group name exists
                if (empty($groupnamelaunch_err)) {
                    $sql = "SELECT group_name FROM groups WHERE group_name = '$groupname'";
                    $result = mysqli_query($link, $sql);
                    $row = $result->num_rows;

                    if (!$row == 1) {
                        $groupnamelaunch_err = "This group name does not exist.";
                    } else {
                        // check if group_id and user_id is in user2group (same row)
                        
                        $sql = "SELECT *
                                FROM user2group
                                JOIN users on users.user_id = user2group.user_id
                                JOIN groups on groups.group_id = user2group.group_id 
                                WHERE users.user_id = '$userID'
                                AND groups.group_name = '$groupname'
                                AND user2group.deleted = '0'";
                        
                        //$sql = "SELECT group_name FROM groups WHERE group_name = '$groupname'";
                        if($result = mysqli_query($link, $sql)){
                            $row = $result->num_rows;

                            if (!$row == 1) {
                                $groupnamelaunch_err = 'You have not joined this group.';
                            } else {
                                $sql = "SELECT group_id FROM groups WHERE group_name = '$groupname'";
                                $result = mysqli_query($link, $sql);
                                $row = $result->num_rows;

                                if ($row == 1) {
                                    $a = mysqli_fetch_assoc($result);
                                    $groupID = $a["group_id"];
                                }
                                
                                $_SESSION['group_id'] = $groupID;
                                $_SESSION['group_name'] = $groupname;
                                
                                header("location: index.php");
                            }
                        } else {
                            echo 'somthing wrong happened.';
                        }
                    }
                }
                
            }
            
            break;
            
        case 'leave-group':
            // Validate and execute statements
            
            if (empty(trim($_POST['group-name-leave']))) {
                $groupnameleave_err = "Please enter a group name to leave.";
            } else {
                $groupname = trim($_POST['group-name-leave']);
                // check if group name exists
                if (empty($groupnameleave_err)) {
                    $sql = "SELECT group_name FROM groups WHERE group_name = '$groupname'";
                    $result = mysqli_query($link, $sql);
                    $row = $result->num_rows;

                    if (!$row == 1) {
                        $groupnameleave_err = "This group name does not exist.";
                    } else {
                        // check if group_id and user_id is in user2group (same row)
                        
                        $sql = "SELECT *
                                FROM user2group
                                JOIN users on users.user_id = user2group.user_id
                                JOIN groups on groups.group_id = user2group.group_id 
                                WHERE users.user_id = '$userID'
                                AND groups.group_name = '$groupname'";
                        
                        //$sql = "SELECT group_name FROM groups WHERE group_name = '$groupname'";
                        if($result = mysqli_query($link, $sql)){
                            $row = $result->num_rows;

                            if (!$row == 1) {
                                $groupnameleave_err = 'You have not joined this group.';
                            } else {
                                $sql = "SELECT group_id FROM groups WHERE group_name = '$groupname'";
                                $result = mysqli_query($link, $sql);
                                $row = $result->num_rows;

                                if ($row == 1) {
                                    $a = mysqli_fetch_assoc($result);
                                    $groupID = $a["group_id"];
                                }
                           
                                if (isset($_SESSION['group_id']) && isset($_SESSION['group_name'] )) {
                                    
                                    unset($_SESSION['group_id']);
                                    unset($_SESSION['group_name']);
                                }
                                
                                
                                //$sql = "DELETE FROM user2group WHERE user_id = '$userID' AND group_id = '$groupID' ";
                                $sql = "UPDATE user2group SET deleted = '1' WHERE user_id = '$userID' AND group_id = '$groupID'";
                                $result = mysqli_query($link, $sql);
                                
                                $sql = "DELETE FROM user2chores WHERE user_id = '$userID'";
                                $result = mysqli_query($link, $sql);
                                
                                $sql = "DELETE FROM visitors WHERE user_id = '$userID'";
                                $result = mysqli_query($link, $sql);
                                
                                $groupnameleave_succ = "You have left the group.";
                                
                                displayGroups();
                            }
                        } else {
                            echo 'somthing wrong happened.';
                        }
                    }
                }
            }
            
            break;
            
// Processing form data when form Join Group is submitted
// When user enters the group name and password they want to join, they are logging in to that group.
    
        } // switch statement for different group forms
}  // if $_SERVER REQUEST METHOD ==  POST for Group form submittal

?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Roomies - Roommate Management Groups</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
    <?php require_once "assets/partials/header.php"; ?>
    <?php require_once("assets/partials/nav.php");?>

    <div class="container">
        <div class="info-title-group">
            <h2>Groups</h2>
        </div>
        <div class="your-groups row">
            <div class="col-sm-10 col-md-8 col-lg-6 your-groups-section">
                <div class="your-groups-section-inner">
                    <div>
                        <h2>Current Group:</h2>
                        <?php echo "<p>".$currentgroupnamemsg."</p>"; ?>
                    </div>
                    <h2>Your Groups: </h2>
                    <p>
                        <?php if (!empty($groupArray)) {
                            foreach ($groupArray as $value) {
                            echo $value . "<br>";
                            }
                        } else {
                            echo $joinedgroupsmsg;
                        }
                    ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="group-wrapper row">
            <div class="col-sm-10 col-md-8 col-lg-6 form-container">
                <div class="form-container-inner">
                    <div class="group-menu">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link" id="launch-group-link" href="#">Group Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="create-group-link" href="#">Create Group</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="join-group-link" href="#">Join Group</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="leave-group-link" href="#">Leave Group</a>
                            </li>
                        </ul>
                    </div>
                    <p class="group-succ-msg mt-3">
                        <?php echo $groupnameleave_succ; ?>
                    </p>
                    <div id="create-group-wrapper" class="form-wrapper">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <h2>Create Group</h2>
                            <p>Create a new group and invite your Roomies by giving them the Group Name and password.</p>
                            <input type="hidden" name="group-forms" value="create-group">
                            <div class="form-group">
                                <label for="group-name">Group Name<sup>*</sup></label>
                                <input type="text" class="form-control" name="group-name" value="<?php echo isset($_POST['group-name']) ? $_POST['group-name'] : ''; ?>"><span class="error-msg"><?php echo $groupname_err; ?></span>
                            </div>
                            <div class="form-group">
                                <label for="group-password">Group Password<sup>*</sup></label>
                                <input type="password" class="form-control" name="group-password"><span class="error-msg"><?php echo $grouppassword_err; ?></span>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Create Group">
                            </div>
                        </form>
                    </div>
                    <div id="join-group-wrapper" class="form-wrapper">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <h2>Join Group</h2>
                            <p>Has a Roomie already created a group? Join it with the Group Name and password.</p>
                            <input type="hidden" name="group-forms" value="join-group">
                            <div class="form-group">
                                <label for="group-name">Group Name<sup>*</sup></label>
                                <input type="text" class="form-control" name="group-name-join" value="<?php echo isset($_POST['group-name-join']) ? $_POST['group-name-join'] : ''; ?>"><span class="error-msg"><?php echo $groupnamejoin_err; ?></span>
                            </div>
                            <div class="form-group">
                                <label for="group-password-join">Group Password<sup>*</sup></label>
                                <input type="password" class="form-control" name="group-password-join"><span class="error-msg"><?php echo $grouppasswordjoin_err; echo $usergrouptaken_err; ?></span>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Join Group">
                            </div>
                        </form>
                    </div>
                    <div id="launch-group-wrapper" class="form-wrapper">
                        <form method="post" action="">
                            <h2>Group Login</h2>
                            <p>Once you've created or joined a group, you can log in with just the Group Name.</p>
                            <input type="hidden" name="group-forms" value="launch-group">
                            <div class="form-group">
                                <label for="group-name-lauch">Group Name<sup>*</sup></label>
                                <input type="text" class="form-control" name="group-name-launch" value="<?php echo isset($_POST['group-name-launch']) ? $_POST['group-name-launch'] : ''; ?>"><span class="error-msg"><?php echo $groupnamelaunch_err; ?></span>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Login to Group">
                            </div>
                        </form>
                    </div>
                    <div id="leave-group-wrapper" class="form-wrapper">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <h2>Leave Group</h2>
                            <p>You may also leave a Group at any time.</p>
                            <input type="hidden" name="group-forms" value="leave-group">
                            <div class="form-group">
                                <label for="group-name-leave">Group Name<sup>*</sup></label>
                                <input type="text" class="form-control" name="group-name-leave" value="<?php echo isset($_POST['group-name-leave']) ? $_POST['group-name-leave'] : ''; ?>"><span class="error-msg"><?php echo $groupnameleave_err; ?></span>
                            </div>
                            <div class="form-group">
                                <input id="leave-group-input" type="submit" class="btn btn-primary" value="Leave Group">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once("assets/partials/footer.php");?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="assets/js/loginRegisterGroups.js"></script>

</body>

</html>
