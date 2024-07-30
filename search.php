<?php
// Include database connection and other necessary files
include_once 'db_config.php';

// Retrieve the search query from the request
$query = $_GET['query'];

// Prepare the SQL statement to search for tasks based on title, category, and due date
$sql = "SELECT * FROM tasks WHERE task_title LIKE ? OR category LIKE ? OR due_date LIKE ?";
$stmt = $conn->prepare($sql);

// Bind parameters and execute the statement
$searchQuery = "%$query%"; // Add wildcards to search for partial matches
$stmt->bind_param("sss", $searchQuery, $searchQuery, $searchQuery);
$stmt->execute();

// Get the result set
$result = $stmt->get_result();

// Display the search results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Output each task as desired (you can customize this)
        echo "<div>";
        echo "<h3>" . $row['task_title'] . "</h3>";
        echo "<p>" . $row['task_description'] . "</p>";
        echo "</div>";
    }
} else {
    echo "No results found.";
}

// Close the database connection and statement
$stmt->close();
$conn->close();
?>