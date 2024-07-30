<?php
session_start();
include 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $username = $_POST['username'];
    $about_me = $_POST['about_me'];
    $birthday = $_POST['birthday'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $userEmail = $_SESSION['email'];

    // Prepare the SQL statement to update user details
    $stmt = $conn->prepare("UPDATE user_details SET about_me = ?, birthday = ?, address = ?, email = ?, phone_number = ? WHERE user_name = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind the parameters
    $stmt->bind_param("ssssss", $about_me, $birthday, $address, $email, $phone_number, $username);

    // Execute the statement
    if (!$stmt->execute()) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    }

    // Check if image file is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = file_get_contents($_FILES['image']['tmp_name']);

        // Update the user's image in the database
        $stmt_image = $conn->prepare("UPDATE user_details SET image_path = ? WHERE email = ?");
        if ($stmt_image === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
        $stmt_image->bind_param("bs", $image, $userEmail);
        $stmt_image->send_long_data(0, $image);

        if (!$stmt_image->execute()) {
            echo "Error uploading image.";
        }

        $stmt_image->close();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to the profile page
    header("Location: profile.php?username=" . urlencode($username));
    exit();
} else {
    echo "Invalid request method.";
}
?>
