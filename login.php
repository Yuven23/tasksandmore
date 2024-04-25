<?php
session_start();
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validate email and password (you should perform more thorough validation and authentication in a real-world application)
    if ($email == "user@example.com" && $password == "password123") {
        // Start a session
        $_SESSION['email'] = $email;
        // Redirect to the home.php file
        header("Location: home.php");
        exit; // Ensure script stops execution after redirection
    } else {
        // Display error message if email or password is incorrect
        $error = "Invalid email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap 4 CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css" />
    <!-- Fontawesome CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <style>
        body {
            background-image: url("https://www.rajalakshmi.org/image/banner-1.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh; /* Ensure full height */
            display: flex;
            align-items: center;
        }

        .wrapper {
            max-width: 600px; /* Adjust as needed */
            margin: 0 auto; /* Center horizontally */
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Login Form Start -->
        <div class="row justify-content-center wrapper">
            <div class="col-lg-10 my-auto myShadow">
                <div class="row">
                    <div class="col-lg-12 bg-white p-4">
                        <h1 class="text-center font-weight-bold text-primary">Sign in to Account</h1>
                        <hr class="my-3" />
                        <form action="#" method="post" class="px-3" id="login-form">
                            <?php if(isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                            <?php endif; ?>
                            <div class="input-group input-group-lg form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text rounded-0"><i class="far fa-envelope fa-lg fa-fw"></i></span>
                                </div>
                                <input type="email" id="email" name="email" class="form-control rounded-0" placeholder="E-Mail" required />
                            </div>
                            <div class="input-group input-group-lg form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text rounded-0"><i class="fas fa-key fa-lg fa-fw"></i></span>
                                </div>
                                <input type="password" id="password" name="password" class="form-control rounded-0" minlength="5" placeholder="Password" required autocomplete="off" />
                            </div>
                            <div class="form-group clearfix">
                                <div class="custom-control custom-checkbox float-left">
                                    <input type="checkbox" class="custom-control-input" id="customCheck" name="rem" />
                                    <label class="custom-control-label" for="customCheck">Remember me</label>
                                </div>
                                <div class="forgot float-right">
                                    <a href="#" id="forgot-link">Forgot Password?</a>
                                </div>
                            </div>
                            <div class="signin">
                                <button type="submit" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Sign In</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Login Form End -->
    </div>
    <!-- jQuery CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
