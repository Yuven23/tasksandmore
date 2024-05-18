<?php
// Include database connection configuration
include_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
    $phone_number = $_POST['phone_number'];
    $name = $_POST['name'];

    // Check if the email is a valid Gmail or Rajalakshmi account
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@(gmail\.com|rajalakshmi\.edu\.in)$/', $email)) {
        echo "Invalid email. Please enter a valid Gmail or Rajalakshmi address.";
        exit;
    }

    // Check if the user already exists in the database
    $stmt_check = $conn->prepare("SELECT * FROM user_details WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // User already registered
        echo "User already registered. Please log in.";
        $stmt_check->close();
        // Redirect to login page
        header("Location: login.php");
        exit;
    }

    $stmt_check->close();

    // Prepare and execute SQL statement to insert user into database
    $stmt_insert = $conn->prepare("INSERT INTO user_details (email, password, phone_number, user_name) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param("ssss", $email, $password, $phone_number, $name);

    if ($stmt_insert->execute()) {
        // User registered successfully
        echo "User registered successfully.";
        // Redirect to home.php
        header("Location: login.php");
        exit;
    } else {
        // Error inserting user
        echo "Error: " . $stmt_insert->error;
    }

    $stmt_insert->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Page</title>
  <link rel="stylesheet" href="style.css">
  <style>
    
body {
  background-image: url("Profile_bgnd.png");
  font-family: sans-serif;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f6f6f6;
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
img {
  width: 100px;
  margin-bottom: 20px;
}

.input-container {
  margin-bottom: 15px;
}

.input-container input {
  width: 100%;
  padding: 15px;
  border: 1px solid #ccc;
  border-radius: 3px;
  font-size: 16px;
}

.button-container {
  margin-bottom: 20px;
}

.button-container button {
  width: 100%;
  padding: 15px;
  background-color: #387eff;
  color: #fff;
  border: none;
  border-radius: 3px;
  font-size: 16px;
  cursor: pointer;
}
.signup-container{
    margin-bottom: 20px; 
}
.signup-container button {
  width: 100%;
  padding: 15px;
  background-color: #387eff;
  color: #fff;
  border: none;
  border-radius: 3px;
  font-size: 16px;
  cursor: pointer;
}

.or-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
}

.or-line {
  width: 45%;
  height: 1px;
  background-color: #ccc;
}

.or-text {
  margin: 0 10px;
  color: #ccc;
}

.facebook-login {
  display: block;
  width: 100%;
  padding:20px;
}
  </style>
</head>
<body>
  <div class="container">
    <form action="#" method="post" class="px-3" id="register-form">
        <div class="input-group input-group-lg form-group">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0"><i class="fas fa-user fa-lg fa-fw"></i></span>
            </div>
            <input type="text" id="name" name="name" class="form-control rounded-0" placeholder="Full Name" required>
        </div>
        <div class="input-group input-group-lg form-group">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0"><i class="far fa-envelope fa-lg fa-fw"></i></span>
            </div>
            <input type="email" id="email" name="email" class="form-control rounded-0" placeholder="E-Mail" required>
        </div>
        <div class="input-group input-group-lg form-group">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0"><i class="fas fa-key fa-lg fa-fw"></i></span>
            </div>
            <input type="password" id="password" name="password" class="form-control rounded-0" minlength="5" placeholder="Password" required autocomplete="off">
        </div>
        <div class="input-group input-group-lg form-group">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0"><i class="fas fa-phone fa-lg fa-fw"></i></span>
            </div>
            <input type="text" id="phone_number" name="phone_number" class="form-control rounded-0" placeholder="Phone Number" required>
        </div>
        <div class="button-container">
            <button type="submit">Create Account</button>
        </div>
        <div class="or-container">
            <div class="or-line"></div>
            <div class="or-text">OR</div>
            <div class="or-line"></div>
        </div>
        <a href="#" class="google-login">Log in with Google</a>
        <br><br>
        <div class="signup-container">
            Already have an account?
            <button  onclick="goToPage('login.php')" class="signup-link">Login</button>
        </div>
    </form>
  </div>
  <script>
    function goToPage(page) {
      // Close the side menu before navigating to the desired page
      var sideMenu = document.getElementById('sideMenu');
      sideMenu.style.left = "-250px";
       window.location.href = page;
    }
  </script>
</body>
</html>