<?php
// Start the session
session_start();
include_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['email']; // Retrieve user email from session

// Handle task deletion
if (isset($_POST['delete_task_id'])) {
    $task_id = $_POST['delete_task_id'];

    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Prepare the SQL statement to delete entries in the accepted_tasks table
        $stmt_delete_accepted_tasks = $conn->prepare("DELETE FROM accepted_tasks WHERE task_id = ?");
        $stmt_delete_accepted_tasks->bind_param("i", $task_id);
        $stmt_delete_accepted_tasks->execute();

        // Prepare the SQL statement to delete the task
        $stmt_delete_task = $conn->prepare("DELETE FROM tasks WHERE task_id = ? AND user_email = ?");
        $stmt_delete_task->bind_param("is", $task_id, $userEmail);
        $stmt_delete_task->execute();

        // Commit the transaction
        $conn->commit();

        echo "Task deleted successfully.";
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "Error deleting task: " . $e->getMessage();
    }

    // Redirect to the same page to avoid resubmission on refresh
    header("Location: task_home.php");
    exit();
}

// Handle task completion toggle
if (isset($_POST['toggle_task_id'])) {
    $task_id = $_POST['toggle_task_id'];

    // Fetch the current completion status
    $stmt_fetch_completion = $conn->prepare("SELECT completed FROM tasks WHERE task_id = ? AND user_email = ?");
    $stmt_fetch_completion->bind_param("is", $task_id, $userEmail);
    $stmt_fetch_completion->execute();
    $result = $stmt_fetch_completion->get_result();
    $task = $result->fetch_assoc();

    if ($task) {
        $new_completed_status = $task['completed'] ? 0 : 1;
        $completed_at = $new_completed_status ? date('Y-m-d H:i:s') : null;

        // Update the completion status and completed_at timestamp
        $stmt_toggle_completion = $conn->prepare("UPDATE tasks SET completed = ?, completed_at = ? WHERE task_id = ? AND user_email = ?");
        $stmt_toggle_completion->bind_param("isis", $new_completed_status, $completed_at, $task_id, $userEmail);
        $stmt_toggle_completion->execute();
    }

    // Redirect to the same page to avoid resubmission on refresh
    header("Location: task_home.php");
    exit();
}




// Retrieve user details from the database based on session email
$stmt = $conn->prepare("SELECT user_name, image_path FROM user_details WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$userDetails = $result->fetch_assoc();

if (!$userDetails) {
    // Handle case where user details are not found
    echo "User details not found.";
    exit();
}

$image_path = $userDetails['image_path'];
$user_name = $userDetails['user_name'];

// Initialize an empty array to store tasks
$all_tasks = []; // Store all tasks
$user_tasks = []; // Store tasks posted by the current user

// SQL query to retrieve all tasks with their images from the tasks table
$sql_all_tasks = "
    SELECT 
        tasks.task_id, 
        tasks.task_title, 
        tasks.task_description, 
        tasks.due_date, 
        tasks.category,
        tasks.image AS task_image, 
        tasks.created_at, 
        tasks.completed,
        user_details.image_path AS profile_image, 
        user_details.user_name, 
        user_details.phone_number, 
        user_details.email,
        accepted_tasks.accepted,
        COALESCE(accepted_tasks.accepted_by, '') AS accepted_by,
        accepted_user.user_name AS accepted_user_name
    FROM tasks 
    INNER JOIN user_details ON tasks.user_email = user_details.email
    LEFT JOIN accepted_tasks ON tasks.task_id = accepted_tasks.task_id
    LEFT JOIN user_details AS accepted_user ON accepted_tasks.accepted_by = accepted_user.email";

// Check if the "View My Tasks" button is pressed
if (isset($_POST['view_my_tasks'])) {
    // SQL query to retrieve tasks posted by the current user
    $sql_user_tasks = "
        SELECT 
            tasks.task_id, 
            tasks.task_title, 
            tasks.task_description, 
            tasks.due_date, 
            tasks.category,
            tasks.image AS task_image, 
            tasks.created_at, 
            tasks.completed,
            user_details.image_path AS profile_image, 
            user_details.user_name, 
            user_details.phone_number, 
            user_details.email 
        FROM tasks 
        INNER JOIN user_details ON tasks.user_email = user_details.email
        WHERE tasks.user_email = ?";
    
    $stmt_user_tasks = $conn->prepare($sql_user_tasks);
    $stmt_user_tasks->bind_param("s", $userEmail);
    $stmt_user_tasks->execute();
    $result_user_tasks = $stmt_user_tasks->get_result();

    while ($row = $result_user_tasks->fetch_assoc()) {
        $user_tasks[] = $row;
    }
}

// Execute the SQL query to fetch all tasks
$stmt_all_tasks = $conn->prepare($sql_all_tasks);
$stmt_all_tasks->execute();
$result_all_tasks = $stmt_all_tasks->get_result();

if ($result_all_tasks) {
    while ($row_all = $result_all_tasks->fetch_assoc()) {
        $all_tasks[] = $row_all;
    }
} else {
    echo "Error fetching tasks: " . $conn->error;
}

$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Task Home</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
      margin: 0;
      font-family: 'Raleway', sans-serif;
      background: url("sports.jpg") center/cover fixed no-repeat;
      color:white;
      position: relative;   
  }
  
  body:before {
              content: "";
              position: fixed;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              background-color: rgba(0, 0, 0, 0.5); /* Adjust opacity as needed */
              z-index: -1;
          }

        .task-info p {
            color: white;
        }

        header#tasks h1 {
            color: white;
        }
/* Styles for View Tasks Modal */
.viewTasksModal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.viewTasksModal .modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

/* Styles for Accepted Tasks Modal */
.acceptedTasksModal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.acceptedTasksModal .modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}


        .w3-modal-content .w3-header {
            background-color: black;
            color: white; /* Set text color to white */
        }
        .w3-header-black {
            background-color: black;
            color: white;
        }


        .add-task-button {
            background-color: black; /* Set the background color to black */
            color: white; /* Set the text color to white */
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        .add-task-button:hover {
            background-color: #333; /* Optional: Darken the button on hover */
        }
        

        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        @media screen and (max-width: 768px) {
            .task-item {
                width: 100%;
                margin-right: 0;
                float: none;
            }

            .w3-main {
                margin-left: 0;
            }
        }

        .menu-link:hover {
            background-color: #f0f0f0;
        }

        .burger-menu {
            position: relative;
            z-index: 2;
        }

        .menu-icon {
            width: 40px;
            height: 40px;
            cursor: pointer;
            background-color: whitesmoke;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .side-menu {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100%;
            background-color: #f4f4f4;
            transition: left 0.3s ease;
            z-index: 3;
        }

        .profile {
            padding: 20px;
        }

        .profile-image img {
            width: 200px;
            height: 200px;
            background-color: #ccc;
            border-radius: 100%;
            margin-bottom: 10px;
        }
        .profile-container {
        display: flex;
        align-items: center;
    }
        .profile-photo img {
            width: 110px;
            height: 110px;
            background-color: #ccc;
            border-radius: 100%;
            margin-bottom: 10px;
            margin-right: 10px;
        }

        .profile-name {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
    color: black;
    font-family: 'Helvetica', sans-serif;

}

        .menu-links button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            background-color: transparent;
            cursor: pointer;
        }

        .menu-links button:hover {
            background-color: #ddd;
        }

        #enlargedImage {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            max-width: 90%;
            max-height: 90%;
        }

       
        

        .w3-main {
            transition: margin-left 0.3s ease;
        }

        .side-menu-open .w3-main {
            margin-left: 250px;
        }


            .side-menu-open .w3-main {
                margin-left: 0;
            }
            


        .task-info {
            padding: 10px;
            border: 10px solid red;
            border-radius: 50px;
            position: relative;
            background-color: black;
        }

        .completed-task .task-info {
            background-color: black; /* Light red background for completed tasks */
        }

        .completed-text {
            font-size: 24px;
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
        .task-filter {
            margin-bottom: 20px;
        }

        .task-filter button {
            background-color: white;
            color: black;
            border: none;
            padding: 10px 20px;
            text-align: center;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 10px;
        }

        .task-filter button:hover {
            background-color: #45a049;
        }
        
       
        
    </style>
</head>
<body class="w3-light-grey w3-content" style="max-width:1600px">

<div class="burger-menu">
    <div class="menu-icon" onclick="toggleMenu()">
        &#9776;
    </div>
    <div class="side-menu" id="sideMenu">
    <div class="profile-image" onclick="showEnlargedImage()">
                <!-- Display the user's profile image -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userDetails['image_path']); ?>" alt="Profile Image">
            </div>
           <!-- Enlarged image and overlay for when the image is clicked -->
<div id="overlay" onclick="hideEnlargedImage()"></div>         
<img id="enlargedImage" src="data:image/jpeg;base64,<?php echo base64_encode($userDetails['image_path']); ?>" alt="Enlarged User Image">     
<!-- Display the user's name -->
            <div class="profile-name"><?php echo $userDetails['user_name']; ?></div>
            <!-- Menu links/buttons -->
            <div class="menu-links">
                <button onclick="goToPage('profile.php')">my profile</button>
                <button onclick="openAddTaskModal()">Add Tasks</button>
                <button onclick="viewMyTasks()">View My Tasks</button>
                <button onclick="openAcceptedTasksModal()">View Accepted Tasks</button>
                <button onclick="goToHomePage()">Go To Home</button>
            </div>
            <div class="w3-panel w3-large" style="color:#000;">
        <i class="fa fa-facebook-official w3-hover-opacity"></i>
        <i class="fa fa-instagram w3-hover-opacity"></i>
        <i class="fa fa-snapchat w3-hover-opacity"></i>
        <i class="fa fa-pinterest-p w3-hover-opacity"></i>
        <i class="fa fa-twitter w3-hover-opacity"></i>
        <i class="fa fa-linkedin w3-hover-opacity"></i>
    </div>
        </div>
    </div>
</div>

    <!-- PAGE CONTENT -->
    <div class="w3-main" style="margin-left:300px">

        <!-- Header -->
        <header id="tasks">
            <div class="w3-container">
                <h1><b>Tasks</b></h1>
                <div class="w3-section w3-bottombar w3-padding-16">
                    <!-- Remove filter options -->
                </div>
            </div>
        </header>
<!-- Add Task Modal -->
<div id="addTaskModal" class="w3-modal">
            <div class="w3-modal-content">
                <header class="w3-container w3-header">
                    <span onclick="closeAddTaskModal()" class="w3-button w3-display-topright">&times;</span>
                    <h2>Add Task</h2>
                </header>
                <div class="w3-container">
                    <form method="post" action="add_task.php">
                        <!-- Display your profile image -->
                        <div class="profile-container">
                            <div class="profile-photo">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($image_path); ?>" alt="Profile Image">
                            </div>
                            <div class="profile-name">
                                <?php echo $user_name; ?>
                            </div>
                        </div>
                        <!-- Task fields -->
                        <div class="w3-section">
                            <label for="title">Title</label>
                            <input class="w3-input w3-border" type="text" id="title" name="title" required>
                        </div>
                        <div class="w3-section">
                            <label for="description">Description</label>
                            <textarea class="w3-input w3-border" id="description" name="description" required></textarea>
                        </div>
                        <div class="w3-section">
                            <label for="due_date">Due Date</label>
                            <input class="w3-input w3-border" type="date" id="due_date" name="due_date" required>
                        </div>
                        <div class="w3-section">
                            <label for="category">Category</label>
                            <input class="w3-input w3-border" type="text" id="category" name="category" required>
                        </div>
                        <p><button class="add-task-button" type="submit">Add Task</button></p>
                    </form>
                </div>
            </div>
        </div>
       

       
        <div class="task-filter">
    <button onclick="filterTasks('newest')">Newest First</button>
    <button onclick="filterTasks('oldest')">Oldest First</button>
  
</div>

<div id="task-container">
    <!-- Display tasks here -->
    <?php foreach ($all_tasks as $task): ?>
        <div id="task-<?= $task['task_id'] ?>" class="w3-third w3-container w3-margin-bottom task-item <?= $task['completed'] ? 'completed-task' : '' ?>" style="<?= $task['completed'] ? : '' ?>">
            <div class="w3-display-container">
                <div class="task-info">
                    <div class="profile-container">
                        <div class="profile-photo">
                            <!-- Display the user's profile image -->
                            <a href="profile2.php?username=<?= urlencode($task['user_name']) ?>">
    <img src="data:image/jpeg;base64,<?= base64_encode($task['profile_image']) ?>" alt="Profile Image">
</a>

                        </div>
                        <p><b><?= htmlspecialchars($task['user_name'], ENT_QUOTES, 'UTF-8') ?></b></p>
                    </div>
                    <p><?= htmlspecialchars($task['task_title'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><?= htmlspecialchars($task['task_description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="task-details">
                        <p>Due Date: <?= htmlspecialchars($task['due_date'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <?php if (!$task['completed']): ?>
                        <?php if ($task['email'] != $userEmail): ?>
                            <?php if ($task['accepted'] == 1 && $task['accepted_user_name'] != $user_name): ?>
                                <!-- Task accepted by someone else -->
                                <button disabled style="background-color:red;color:black;">Accepted by <?= htmlspecialchars($task['accepted_user_name'], ENT_QUOTES, 'UTF-8') ?></button>
                            <?php elseif ($task['accepted'] == 1 && $task['accepted_user_name'] == $user_name): ?>
                                <!-- Button to deselect task -->
                                <button style="background-color: yellow; color: black;" onclick="toggleTaskAcceptance(<?= $task['task_id'] ?>)">Deselect Task</button>
                            <?php else: ?>
                                <!-- Button to accept task -->
                                <button style="background-color: green; color: white;" onclick="toggleTaskAcceptance(<?= $task['task_id'] ?>)">Accept Task</button>
                            <?php endif; ?>
                        <?php endif; ?>
                        <!-- Button to display user details -->
                        <button onclick="openUserDetailsPopup('<?= htmlspecialchars($task['email'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($task['phone_number'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($task['created_at'], ENT_QUOTES, 'UTF-8') ?>')">Show Details</button>
                        <?php if ($task['email'] == $userEmail): ?>
                            <!-- Button to mark task as completed or incomplete -->
                            <form id="completeTaskForm-<?= $task['task_id'] ?>" method="post" style="display: inline;">
                                <input type="hidden" name="toggle_task_id" value="<?= $task['task_id'] ?>">
                                <button type="button" style="background-color: blue; color: white;" onclick="completeTask(<?= $task['task_id'] ?>)">
                                    <?= $task['completed'] ? 'Mark Incomplete' : 'Complete' ?>
                                </button>
                            </form>
                            <!-- Button to delete task -->
                            <form id="deleteTaskForm-<?= $task['task_id'] ?>" method="post" style="display: inline;">
                                <input type="hidden" name="delete_task_id" value="<?= $task['task_id'] ?>">
                                <button type="button" style="background-color: red; color: white;" onclick="deleteTask(<?= $task['task_id'] ?>)">Delete</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                    <!-- Show Completed text for completed tasks -->
                    <?php if ($task['completed']): ?>
                        <div class="completed-text">Completed</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


         <!-- Modal Popup for Viewing Accepted Tasks -->
<div id="acceptedTasksModal" class="acceptedTasksModal">
    <div class="modal-content">
        <span class="close" onclick="closeAcceptedTasksModal()">&times;</span>
        <h2>Accepted Tasks</h2>
        <!-- Container to display accepted tasks -->
        <div id="acceptedTasksContainer"></div>
    </div>
</div>
            <!-- User Details Popup -->
    <div id="userDetailsPopup" class="w3-modal">
        <div class="w3-modal-content">
            <header class="w3-container w3-header-black">
                <span onclick="closeUserDetailsPopup()" class="w3-button w3-display-topright">&times;</span>
                <h2>User Details</h2>
            </header>
            <div class="w3-container">
                <p><strong>Email:</strong> <span id="userEmail"></span></p>
                <p><strong>Phone:</strong> <span id="userPhone"></span></p>
                <p><strong>Posted:</strong> <span id="taskTimestamp"></span></p>
            </div>
        </div>
    </div>

       <!-- Modal Popup for Viewing Tasks -->
       <div id="viewTasksModal" class="viewTasksModal">
            <div class="modal-content">
                <span class="close" onclick="closeViewTasksModal()">&times;</span>
                <h2>All Tasks</h2>
                <!-- Display tasks here -->
                <?php foreach ($all_tasks as $task): ?>
                    <?php if ($task['email'] === $userEmail): ?>
                        <div>
                            <p><b><?= $task['user_name'] ?></b></p>
                            <p><?= $task['task_title'] ?></p>
                            <p><?= $task['task_description'] ?></p>
                            <p><?= $task['due_date'] ?></p
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (empty($user_tasks)): ?>
                    <p>No tasks found.</p>
                <?php endif; ?>
            </div>
        </div>
        
    



        <script>
            // Function to redirect to home page
            function goToHomePage() {
                window.location.href = "home.php";
            }


            function goToPage(page) {
      // Close the side menu before navigating to the desired page
      var sideMenu = document.getElementById('sideMenu');
      sideMenu.style.left = "-250px";
      document.removeEventListener('click', closeMenuOnClickOutside);
      window.location.href = page;
            }



            function toggleMenu() {
      var sideMenu = document.getElementById('sideMenu');
      if (sideMenu.style.left === "-250px" || sideMenu.style.left === "") {
          sideMenu.style.left = "0";
          // Add event listener to close menu on outside click
          document.addEventListener('click', closeMenuOnClickOutside);
      } else {
          sideMenu.style.left = "-250px";
          // Remove event listener when menu is closed
          document.removeEventListener('click', closeMenuOnClickOutside);
      }
  }



 // Function to open the accepted tasks modal
function openAcceptedTasksModal() {
    // Call AJAX to fetch and display accepted tasks
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Update the acceptedTasksContainer with the fetched tasks
            document.getElementById("acceptedTasksContainer").innerHTML = this.responseText;
            // Show the modal
            document.getElementById("acceptedTasksModal").style.display = "block";
        }
    };
    xhttp.open("GET", "fetch_accepted_tasks.php", true);
    xhttp.send();
}

// Function to close the accepted tasks modal
function closeAcceptedTasksModal() {
    document.getElementById("acceptedTasksModal").style.display = "none";
}

function completeTask(taskId) {
    if (confirm("Are you sure you want to toggle the completion status of this task?")) {
        // Submit the form to update the task status
        document.getElementById('completeTaskForm-' + taskId).submit();

   }
}
function filterTasks(criteria) {
        let tasks = Array.from(document.getElementsByClassName('task-item'));

        if (criteria === 'newest') {
            tasks.sort((a, b) => new Date(b.querySelector('.task-details p').innerText.split(': ')[1]) - new Date(a.querySelector('.task-details p').innerText.split(': ')[1]));
        } else if (criteria === 'oldest') {
            tasks.sort((a, b) => new Date(a.querySelector('.task-details p').innerText.split(': ')[1]) - new Date(b.querySelector('.task-details p').innerText.split(': ')[1]));
        } 
        let taskContainer = document.getElementById('task-container');
        taskContainer.innerHTML = '';
        tasks.forEach(task => taskContainer.appendChild(task));
    }



  // Function to close the side menu when clicking outside
  function closeMenuOnClickOutside(event) {
      var sideMenu = document.getElementById('sideMenu');
      var menuIcon = document.querySelector('.menu-icon');
      if (!sideMenu.contains(event.target) && event.target !== menuIcon) {
          sideMenu.style.left = "-250px";
          document.removeEventListener('click', closeMenuOnClickOutside);
      }
  }



            // Function to open user details popup
            function openUserDetailsPopup(email, phoneNumber, createdAt) {
            document.getElementById('userEmail').innerText = email;
            document.getElementById('userPhone').innerText = phoneNumber;
            document.getElementById('taskTimestamp').innerText = createdAt;
            document.getElementById('userDetailsPopup').style.display = 'block';
        }

        function closeUserDetailsPopup() {
            document.getElementById('userDetailsPopup').style.display = 'none';
        }
            

            // Function to open and close add task modal
            function openAddTaskModal() {
                document.getElementById("addTaskModal").style.display = "block";
            }

            function closeAddTaskModal() {
                document.getElementById("addTaskModal").style.display = "none";
            }




            // Function to open modal for viewing all tasks
            function viewMyTasks() {
                document.getElementById("viewTasksModal").style.display = "block";
            }

            // Function to close modal for viewing all tasks
            function closeViewTasksModal() {
                document.getElementById("viewTasksModal").style.display = "none";
            }



            

function deleteTask(taskId) {
            if (confirm("Are you sure you want to delete this task?")) {
                document.getElementById('deleteTaskForm-' + taskId).submit();
            }
        }


// Function to accept task and toggle acceptance status
function toggleTaskAcceptance(taskId) {
        // Create a FormData object to send data via AJAX
        var formData = new FormData();
        formData.append('task_id', taskId);

        // Create a new XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Define the function to handle the AJAX response
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Task acceptance toggled successfully, reload the page to reflect changes
                    location.reload();
                } else {
                    // Error occurred, display error message
                    alert('Error toggling task acceptance status. Please try again.');
                }
            }
        };

        // Open a POST request to the toggle_acceptance.php script
        xhr.open('POST', 'toggle_acceptance.php', true);

        // Send the FormData object containing the task ID
        xhr.send(formData);

    xhrCheck.open("POST", "check_task_owner.php", true);
    xhrCheck.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhrCheck.send("task_id=" + taskId);
}


function toggleAcceptance(taskId) {
    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();
    
    // Define the function to handle the AJAX response
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Parse the response JSON
                var response = JSON.parse(xhr.responseText);
                if (response.accepted === 1) {
                    // If the task is already accepted, display an error message
                    alert('This task has already been accepted by someone.');
                } else {
                    // If the task is not accepted, proceed to toggle acceptance
                    toggleTaskAcceptance(taskId);
                }
            } else {
                // Error occurred, display error message
                alert('Error checking task acceptance status. Please try again.');
            }
        }
    };
    
    // Open a POST request to the check_task_acceptance.php script
    xhr.open('POST', 'check_task_acceptance.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    // Send the task ID in the request body
    xhr.send('task_id=' + taskId);
}



        </script>

    </div>
</body>
</html>