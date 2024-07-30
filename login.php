<?php
session_start();
include_once 'db_config.php'; // Include your database connection configuration file

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Query to check if the provided email exists and retrieve the hashed password from the database
    $stmt = $conn->prepare("SELECT password FROM user_details WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the email exists in the database
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start a session
            $_SESSION['email'] = $email;
            
            // Query to retrieve additional user information from the database
            $stmt_user = $conn->prepare("SELECT user_name, phone_number FROM user_details WHERE email = ?");
            $stmt_user->bind_param("s", $email);
            $stmt_user->execute();
            $stmt_user->store_result();
            
            if ($stmt_user->num_rows > 0) {
                $stmt_user->bind_result($user_name, $phone_number);
                $stmt_user->fetch();
                
                // Set additional session variables
                $_SESSION['user_name'] = $user_name;
                $_SESSION['phone_number'] = $phone_number;
            }
            
            // Log the login
            $stmt_login = $conn->prepare("INSERT IGNORE INTO user_logins (user_email, login_date) VALUES (?, CURDATE())");
            $stmt_login->bind_param("s", $email);
            $stmt_login->execute();
            
            // Redirect to the homepage
            header("Location: home.php");
            exit; // Ensure script stops execution after redirection
        } else {
            // Password is incorrect
            $error = "Invalid email or password. Please try again.";
        }
    } else {
        // Email does not exist in the database
        $error = "Invalid email or password. Please try again.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('recbgnd3.jpeg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        body:before {
              content: "";
              position: fixed;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              background-color: rgba(0, 0, 0, 0.5); /* Adjust opacity as needed */
              z-index: -1;
          }
        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.5);
        }
        .form-group label {
            text-align: left;
        }
        .btn-custom {
            background-color: red;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container col-md-4">
    <h2 class="mb-4">Login to tasksandmore.com</h2> <!-- Changed title -->
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-custom btn-block">Login</button>
    </form>

    <a href="signup.php" class="btn btn-custom btn-block mt-3">Create Account</a>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
