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

// Retrieve accepted tasks for the current user
$stmt = $conn->prepare("SELECT tasks.* FROM tasks INNER JOIN accepted_tasks ON tasks.task_id = accepted_tasks.task_id WHERE accepted_tasks.accepted_by = ? AND accepted_tasks.accepted = 1");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Display accepted tasks
    while ($row = $result->fetch_assoc()) {
        // Output each accepted task
        echo "<div>";
        echo "<h3>" . $row['task_title'] . "</h3>";
        echo "<p>Description: " . $row['task_description'] . "</p>";
        echo "<p>Due Date: " . $row['due_date'] . "</p>";
        // Add more task details as needed
        echo "</div>";
    }
} else {
    echo "You have not accepted any tasks yet.";
}

$stmt->close();
$conn->close();
?>
