<?php 

// require config file
require_once '../../hidden/config.php';


// Define variables and initialize

$password=  $firstname = $lastname = $email = "";
$firstname_err = $lastname_err = $email_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    
    // Validate First Name
    if (empty(trim($_POST['first-name']))) {
        $firstname_err = "Please enter your first name.";
    } else {
        // Prepare select statement
        $sql = "SELECT user_id FROM users WHERE fName = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_firstname);
            
            // Set parameters
            $param_firstname = trim($_POST['first-name']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //Store result
                mysqli_stmt_store_result($stmt);
                $firstname = trim($_POST['first-name']);
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    } // else (input first name not empty)
    
    // Validate last name
    if (empty(trim($_POST['last-name']))) {
        $lastname_err = "Please enter your last name.";
    } else {
        // Prepare select statement
        $sql = "SELECT user_id FROM users WHERE lName = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_lastname);
            
            // Set parameters
            $param_lastname = trim($_POST['last-name']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //Store result
                mysqli_stmt_store_result($stmt);
                $lastname = trim($_POST['last-name']);
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    } // else (input last name not empty)
    
    // Validate email
    if (empty(trim($_POST['email']))) {
        $email_err = "Please enter your email.";
    } else {

        // Prepare select statement
        $sql = "SELECT user_id FROM users WHERE email = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST['email']);
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                
                //Store result
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already taken.";
                } else{
                    $email = trim($_POST['email']);
                }
  
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    } // else (input email not empty)
    
    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter your password.";
    } else if (strlen(trim($_POST['password'])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    }
    else {
        $password = trim($_POST['password']);
        
    } // else (input password not empty)
    
    // Check input errors before inserting in database
    if(empty($firstname_err) && empty($lastname_err)  && empty($email_err) && empty($password_err)) {
        
        // Prepare an INSERT statement
        $sql = "INSERT INTO users (fName, lName, email, user_password) VALUES
        (?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($link, $sql)) {

            //Bind variables to the prepared statement as parameters
            
            mysqli_stmt_bind_param($stmt, 'ssss', $param_firstname, $param_lastname, $param_email, $param_password);
            
            // Set parameters
            $param_firstname = $firstname;
            $param_lastname = $lastname;
            $param_email = $email;            
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            // Creates password hash
            
            // Attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)) {
                //Store result
                $userID = mysqli_insert_id($link);
        
                session_start();
                $_SESSION['user_id'] = $userID;
                $_SESSION['first_name'] = $firstname;
                $_SESSION['last_name'] = $lastname;
                $_SESSION['email'] = $email;
                header("location: groups.php");
                
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            
        } else {
            echo 'statement did not prepare';
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
    } // Check input errors if statement
    
    // Close connection
        mysqli_close($link);
    
    
} // if $_SERVER REQUEST METHOD ==  POST


?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Roomies-Roommate Management Sign Up</title>
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
    <div class="container-fluid register-container">
        <div class="form-wrapper">
            <form id="register-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <h2>Sign Up</h2>
                <div class="form-group">
                    <label for="first-name">First Name<sup>*</sup></label>
                    <input type="text" class="form-control" name="first-name" value="<?php echo isset($_POST['first-name']) ? $_POST['first-name'] : ''; ?>"><span class="error-msg"><?php echo $firstname_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="last-name">Last Name<sup>*</sup></label>
                    <input type="text" class="form-control" name="last-name" value="<?php echo isset($_POST['last-name']) ? $_POST['last-name'] : ''; ?>"><span class="error-msg"><?php echo $lastname_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="email">Email<sup>*</sup></label>
                    <input type="email" class="form-control" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>"><span class="error-msg"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="password">Password<sup>*</sup></label>
                    <input type="password" class="form-control" name="password"><span class="error-msg"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Create Account">
                </div>
                <div>
                    <p>Already have an account? <a href="login.php">Log in</a></p>
                </div>
            </form>
        </div>
    </div>

    <?php require_once "assets/partials/footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="assets/js/loginRegisterGroups.js"></script>

</body>

</html>
