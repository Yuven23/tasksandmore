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
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background-image: url('Profile_bgnd.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .container {
            width: 70%; /* Adjust container width for mobile devices */
            max-width: 300px; /* Maximum width for larger screens */
            margin: 0 auto; /* Center the container */
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.5);
        }
        .login-form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            text-align: left; /* Align labels to the left */
        }
        input[type="email"],
        input[type="password"],
        input[type="submit"],
        .create-account-btn {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"],
        .create-account-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }
        input[type="submit"]:hover,
        .create-account-btn:hover {
            background-color: #45a049;
        }
        /* Added style for title */
        h2 {
            color: black;
        }
        /* Media query for smaller screens (e.g., mobile devices) */
        @media screen and (max-width: 0px) {
            body {
                background-size: contain;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Login to Tasks & More</h2> <!-- Changed title -->
    
    <?php if(isset($error)): ?>
        <div><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="login-form">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Login">
        </form>
    </div>

    <a href="signup.php" class="create-account-btn">Create Account</a>
</div>

</body>
</html>
</html>