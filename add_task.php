<?php
session_start(); // Start the session (assuming user is logged in)

// Include database connection configuration
include_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $category = $_POST['category'];
    $user_email = $_SESSION['email']; // Retrieve user's email from session
    
    // Fetch user name from user_details table
    $stmt_user = $conn->prepare("SELECT user_name FROM user_details WHERE email = ?");
    $stmt_user->bind_param("s", $user_email);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user_details = $result_user->fetch_assoc();
    
    // Get user name
    $tasks_posted_by = $user_details['user_name'];

    // Prepare and execute SQL statement to insert task into database
    $stmt = $conn->prepare("INSERT INTO tasks (task_title, task_description, due_date, category, user_email, tasks_posted_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $description, $due_date, $category, $user_email, $tasks_posted_by);

    if ($stmt->execute()) {
        echo "Task posted successfully.";
        // Redirect back to the task display page after posting a task
        header("Location: task_home.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $stmt_user->close();
}
?>
