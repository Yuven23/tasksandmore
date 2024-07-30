<?php
session_start();
include_once 'db_config.php';

// Check if task ID is provided
if (isset($_POST['task_id'])) {
    // Get the task ID from POST data
    $taskId = $_POST['task_id'];
    
    // Get the user's email from session
    $userEmail = $_SESSION['email'];

    // Check if the task is posted by the current user
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE task_id = ? AND user_email = ?");
    $stmt->bind_param("is", $taskId, $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Task is posted by the current user
        echo "posted_by_current_user";
    } else {
        // Task is not posted by the current user
        echo "not_posted_by_current_user";
    }

    $stmt->close();
    $conn->close();
} else {
    // Task ID not provided
    echo "Task ID not provided.";
}
?>
