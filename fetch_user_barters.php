<?php
session_start();
include_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo "You need to log in.";
    exit();
}

$userEmail = $_SESSION['email']; // Retrieve user email from session

// Retrieve user's barters from the database
$stmt = $conn->prepare("SELECT barter_id, user_barters, barter_description, image FROM barters WHERE user_email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="product">';
        echo '<p><strong>Item to be exchanged:</strong> ' . htmlspecialchars($row['user_barters']) . '</p>';
        echo '<p><strong>Barter description:</strong> ' . htmlspecialchars($row['barter_description']) . '</p>';
        if (!empty($row['image'])) {
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image']) . '" alt="Barter Image" style="width:100%; height:auto;">';
        }
        echo '</div>';
    }
} else {
    echo '<p>You have no barters.</p>';
}

$conn->close();
?>
