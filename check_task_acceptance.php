<?php
// Start session if not already started
session_start();

// Include database connection
include_once 'db_config.php';

// Check if task_id is provided in the POST request
if (!isset($_POST['task_id'])) {
    echo json_encode(array('error' => 'Task ID not provided.'));
    exit();
}

// Retrieve task_id from the POST request
$task_id = $_POST['task_id'];

// Prepare statement to check if the task has been accepted
$stmt = $conn->prepare("SELECT accepted FROM accepted_tasks WHERE task_id = ?");
$stmt->bind_param("i", $task_id);

// Execute statement
if ($stmt->execute()) {
    // Bind the result variable
    $stmt->bind_result($accepted);

    // Fetch the result
    $stmt->fetch();

    // Check if the task has been accepted
    if ($accepted == 0) {
        // Task has not been accepted, allow the user to accept it
        echo json_encode(array('accepted' => false));
    } else {
        // Task has already been accepted, print a message
        echo json_encode(array('error' => 'Task has already been accepted by someone.'));
    }
} else {
    // Error occurred
    echo json_encode(array('error' => 'Error checking task acceptance status.'));
}

// Close statement and database connection
$stmt->close();
$conn->close();
?>
