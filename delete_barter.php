<?php
session_start();
include_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo "You must be logged in to delete a barter.";
    exit();
}

if (isset($_POST['barter_id'])) {
    $barter_id = $_POST['barter_id'];
    $user_email = $_SESSION['email'];

    // Prepare and execute the query to delete the barter
    $stmt = $conn->prepare("DELETE FROM barters WHERE barter_id = ? AND user_email = ?");
    $stmt->bind_param("is", $barter_id, $user_email);
    if ($stmt->execute()) {
        echo "Barter deleted successfully.";
    } else {
        echo "Failed to delete barter.";
    }

    $stmt->close();
} else {
    echo "No barter ID provided.";
}

$conn->close();
?>
