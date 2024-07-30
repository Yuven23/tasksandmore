<?php
session_start();
include_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['email']; // Retrieve user email from session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and validate form data
    $user_email = filter_var($userEmail, FILTER_VALIDATE_EMAIL);
    $user_barters = htmlspecialchars($_POST['user_barters'] ?? '', ENT_QUOTES, 'UTF-8');
    $barter_description = htmlspecialchars($_POST['barter_description'] ?? '', ENT_QUOTES, 'UTF-8');

    // Ensure required fields are not empty and email is valid
    if ($user_email && !empty($user_barters) && !empty($barter_description) && isset($_FILES['barter_image'])) {
        // Get the image file
        $barter_image = file_get_contents($_FILES['barter_image']['tmp_name']);
        
        // Insert the new barter into the database
        $stmt = $conn->prepare("INSERT INTO barters (user_email, user_barters, barter_description, image) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            echo "Error preparing the statement: " . $conn->error;
        } else {
            $stmt->bind_param("ssss", $user_email, $user_barters, $barter_description, $barter_image);
            $stmt->send_long_data(3, $barter_image);

            if ($stmt->execute()) {
                echo "New barter added successfully.";
            } else {
                echo "Error executing the statement: " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        echo "All fields are required and must be valid.";
    }
}

$conn->close();
?>
