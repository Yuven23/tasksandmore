<?php
// Start session if not already started
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

// Include database connection
include_once 'db_config.php';

// Check if task_id is provided in the POST request
if (isset($_POST['task_id'])) {
    // Retrieve task_id from the POST request
    $task_id = $_POST['task_id'];
    
    // Check if the task is already accepted by someone else
    $stmt_check_other = $conn->prepare("SELECT * FROM accepted_tasks WHERE task_id = ? AND accepted_by <> ?");
    $stmt_check_other->bind_param("is", $task_id, $_SESSION['email']);
    $stmt_check_other->execute();
    $result_check_other = $stmt_check_other->get_result();

    if ($result_check_other->num_rows > 0) {
        // Task is already accepted by someone else, cannot accept
        echo json_encode(array('error' => 'Task is already accepted by someone else.'));
        exit();
    }

    // Check if the task is already accepted by the current user
    $stmt_check_current = $conn->prepare("SELECT accepted FROM accepted_tasks WHERE task_id = ? AND accepted_by = ?");
    $stmt_check_current->bind_param("is", $task_id, $_SESSION['email']);
    $stmt_check_current->execute();
    $result_check_current = $stmt_check_current->get_result();

    if ($result_check_current->num_rows > 0) {
        // Task is already accepted by the current user, toggle acceptance status
        $row = $result_check_current->fetch_assoc();
        $new_status = $row['accepted'] ? 0 : 1;
        
        if ($new_status === 0) {
            // If toggling to 0, delete the row
            $stmt_delete = $conn->prepare("DELETE FROM accepted_tasks WHERE task_id = ? AND accepted_by = ?");
            $stmt_delete->bind_param("is", $task_id, $_SESSION['email']);
            $stmt_delete->execute();
            $stmt_delete->close();
        } else {
            // Otherwise, update the status to 1
            $stmt_toggle = $conn->prepare("UPDATE accepted_tasks SET accepted = ? WHERE task_id = ? AND accepted_by = ?");
            $stmt_toggle->bind_param("iis", $new_status, $task_id, $_SESSION['email']);
            $stmt_toggle->execute();
            $stmt_toggle->close();
        }
    } else {
        // Task is not accepted by the current user, insert it with accepted = 1
        $stmt_insert = $conn->prepare("INSERT INTO accepted_tasks (task_id, accepted_by, accepted) VALUES (?, ?, 1)");
        $stmt_insert->bind_param("is", $task_id, $_SESSION['email']);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    echo json_encode(array('success' => true));
} else {
    // Task ID not provided
    echo json_encode(array('error' => 'Task ID not provided.'));
}

// Close database connection
$conn->close();
?>
