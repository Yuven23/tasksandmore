<?php
session_start();
include_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Check if image file is uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $userEmail = $_SESSION['email'];
    $image = file_get_contents($_FILES['image']['tmp_name']);

    // Update the user's image in the database
    $stmt = $conn->prepare("UPDATE user_details SET image_path = ? WHERE email = ?");
    $stmt->bind_param("bs", $image, $userEmail);
    $stmt->send_long_data(0, $image);

    if ($stmt->execute()) {
        echo "Image uploaded successfully.";
        header("Location: profile2.php");
        exit();
    } else {
        echo "Error uploading image.";
    }
} else {
    echo "No image uploaded or there was an error with the upload.";
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Image</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body, h1 {
            font-family: "Raleway", Arial, Helvetica, sans-serif;
        }

        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f1f1f1;
        }

        .upload-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .upload-container h1 {
            margin-bottom: 20px;
        }

        .upload-container input[type="file"] {
            margin-bottom: 20px;
        }

        .upload-container button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .upload-container button:hover {
            background-color: #45a049;
        }
    </style>
</body>
</html>
