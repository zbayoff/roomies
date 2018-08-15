<?php 

session_start();

include '../../../hidden/config.php';

// initialize
$groupID = $_SESSION['group_id'];
$userID = $_SESSION['user_id'];

$user_errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validate First Name
    if (empty(trim($_POST['fName-edit']))) {
        $user_errors[] = array("status" => "error", "field" => "fName-edit", "msg" => "Please enter your first name.");
    } else {
        // Prepare select statement
        $sql = "SELECT user_id FROM users WHERE fName = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_firstname);
            
            // Set parameters
            $param_firstname = trim($_POST['fName-edit']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //Store result
                mysqli_stmt_store_result($stmt);
                $firstname = trim($_POST['fName-edit']);
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    } // else (input first name not empty)
    
    // Validate Last Name
    if (empty(trim($_POST['lName-edit']))) {
        $user_errors[] = array("status" => "error", "field" => "lName-edit", "msg" => "Please enter your last name.");
    } else {
        // Prepare select statement
        $sql = "SELECT user_id FROM users WHERE lName = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_lastname);
            
            // Set parameters
            $param_lastname = trim($_POST['lName-edit']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //Store result
                mysqli_stmt_store_result($stmt);
                $lastname = trim($_POST['lName-edit']);
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    } // else (input last name not empty)
    
    // Validate email
    if (empty(trim($_POST['email-edit']))) {
        $user_errors[] = array("status" => "error", "field" => "email-edit", "msg" => "Please enter your email.");
    } else {
        // Prepare select statement
        $sql = "SELECT user_id FROM users WHERE email = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST['email-edit']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //Store result
                mysqli_stmt_store_result($stmt);
                $email = trim($_POST['email-edit']);
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    } // else (input email not empty)
    
    // Validate phone
    
    //echo '<br>';
    //echo strlen((string)trim($_POST['phone-edit']));
    //echo '<br>';
    
    if (empty(trim($_POST['phone-edit']))) {
        $phone = "";
        $carrier = "";
    } else {
        if (strlen((string)trim($_POST['phone-edit'])) != 10) {
            $user_errors[] = array("status" => "error", "field" => "phone-edit", "msg" => "Phone number not valid. Please include 3-digit area code.");
        } else if (!preg_match("/^[1-9][0-9]*$/", trim($_POST['phone-edit']))) {
            $user_errors[] = array("status" => "error", "field" => "phone-edit", "msg" => "Phone number not valid. Phone number must include only numbers and be in proper format.");
        } else {
            // Prepare select statement
            $sql = "SELECT user_id FROM users WHERE phone = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_phone);

                // Set parameters
                $param_phone = trim($_POST['phone-edit']);

                // Attempt to execute prepared statement
                if(mysqli_stmt_execute($stmt)) {
                    //Store result
                    mysqli_stmt_store_result($stmt);
                    $phone = trim($_POST['phone-edit']);
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
            
            // Prepare select statement
            $sql = "SELECT user_id FROM users WHERE phone_carrier = ?";
        
            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_carrier);

                // Set parameters
                $param_carrier = trim($_POST['phone-carrier-edit']);

                // Attempt to execute prepared statement
                if(mysqli_stmt_execute($stmt)) {
                    //Store result
                    mysqli_stmt_store_result($stmt);
                    $carrier = trim($_POST['phone-carrier-edit']);
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        
            // Close statement
            mysqli_stmt_close($stmt);
            
            }   
        } // else (input phone not empty)

    if (empty(array_filter($user_errors))) {
        
//        echo $firstname . '<br>';
//        echo $lastname . '<br>';
//        echo $email . '<br>';
//        echo $phone . '<br>';
//        echo $carrier . '<br>';

        // UPDATE user TABLE
        $sql = "UPDATE users
                SET fName = '$firstname', lName = '$lastname', email = '$email'
                WHERE user_id = '$userID'";
        $result = mysqli_query($link, $sql);

        $sql = "UPDATE users
                SET phone = '$phone', phone_carrier = '$carrier'
                WHERE user_id = '$userID'";
        $result = mysqli_query($link, $sql);
        
        $_SESSION['first_name'] = $firstname;
        $_SESSION['email'] = $email;
        
        
        // if phone is not empty, insert into dB, else update it.
        // if phone is not empty, insert carrier into dB, else update it.
        
        $user_errors[] = array("status" => "success", "msg" => "User info saved.");
        echo (json_encode($user_errors));
    } else {
        echo (json_encode($user_errors));
    }
    
    mysqli_close($link);
    
} // if server request post

?>
