<!-- add_task.php -->

<?php
session_start(); // Start the session (assuming user is logged in)

// Include database connection configuration
include_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task = $_POST['task'];
    $email = $_SESSION['email']; // Retrieve user's email from session

    // Prepare and execute SQL statement to insert task into database
    $stmt = $conn->prepare("INSERT INTO tasks (user_email, task_description) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $task);

    if ($stmt->execute()) {
        echo "Task posted successfully.";
        // Redirect back to the task display page after posting a task
        header("Location: task_home.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
