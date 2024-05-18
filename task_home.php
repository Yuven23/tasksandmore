<?php
// Start the session
session_start();

// Include database connection configuration
include_once 'db_config.php';

// Initialize an empty array to store tasks
$all_tasks = []; // Store all tasks
$user_tasks = []; // Store tasks posted by the current user

// Check if session variables are set
if(isset($_SESSION['email'])) {
    // Display user-specific content or perform actions based on session variables
    $email = $_SESSION['email'];
    $user_name = $_SESSION['user_name'];
    $phone_number = $_SESSION['phone_number'];
    
    // Now you can use $email, $user_name, $phone_number variables in your page
} else {
    // Redirect the user to the login page if session variables are not set
    header("Location: login.php");
    exit();
}

// SQL query to retrieve all tasks
$sql_all_tasks = "SELECT tasks.task_description, tasks.timestamp, user_details.user_name, user_details.phone_number, user_details.email 
        FROM tasks 
        INNER JOIN user_details ON tasks.user_email = user_details.email";

// Check if the "View My Tasks" button is pressed
if(isset($_POST['view_my_tasks'])) {
    // SQL query to retrieve tasks posted by the current user
    $sql = "SELECT tasks.task_description, tasks.timestamp, user_details.user_name, user_details.phone_number, user_details.email 
            FROM tasks 
            INNER JOIN user_details ON tasks.user_email = user_details.email
            WHERE tasks.user_email = '$email'";
    
    // Check if the database connection is established
    if ($conn) {
        // Execute the SQL query
        $result = $conn->query($sql);

        // Check if the query was successful
        if ($result) {
            // Fetch tasks from the result set and store them in the $user_tasks array
            while ($row = $result->fetch_assoc()) {
                $user_tasks[] = $row;
            }
        } else {
            // Handle the case where the query failed
            echo "Error: " . $conn->error;
        }
    }
}
// Check if the database connection is established
if ($conn) {
    // Execute the SQL query to fetch all tasks
    $result_all_tasks = $conn->query($sql_all_tasks);

    // Check if the queries were successful
    if ($result_all_tasks) {
        // Fetch all tasks and user tasks from the result sets and store them in the respective arrays
        while ($row_all = $result_all_tasks->fetch_assoc()) {
            $all_tasks[] = $row_all;
        }
    } else {
        // Handle the case where the queries failed
        echo "Error: " . $conn->error;
    }

    // Close the result sets
    $result_all_tasks->close();

    // Close the database connection
    $conn->close();
} else {
    // Handle the case where the database connection could not be established
    echo "Error: Unable to connect to the database.";
}
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
            font-family: "Raleway", Arial, Helvetica, sans-serif;
            background-image: url('Profile_bgnd.png');
            background-size: cover;
            color: white;
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
            color: white; /* Set text color of username, task description, cost, and time to white */
        }
        header#tasks h1 {
            color: white; /* Set text color of the title "Tasks" to white */
        }

        /* Style for modal popup */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 100px; /* Location of the modal */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal content */
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Adjust width as needed */
            max-width: 600px; /* Max width */
            overflow-y: auto; /* Enable vertical scroll */
            position: relative; /* Adjust position */
            right: 50px; /* Move the modal to the right */
        }

        /* Close button */
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
                width: 100%; /* Set task item width to 100% for smaller screens */
                margin-right: 0; /* Remove right margin for smaller screens */
                float: none; /* Remove float for smaller screens */
            }
        }
       /* Hover effect for the links */
    .menu-link:hover {
      background-color: #f0f0f0;
    }
    .burger-menu {
      position: relative;
  }

  .menu-icon {
      width: 40px;
      height: 40px;
      /* position: absolute; */
      top: 20px;
      left: 20px;
      cursor: pointer;
      background-color: whitesmoke;
      color: #333;
      display:flex;
      justify-content: center;
      align-items: center;

  }



  .side-menu {
      position: fixed;
      top: 0;
      left: -250px; /* Start off-screen */
      width: 250px;
      height: 100%;
      background-color: #f4f4f4;
      transition: left 0.3s ease;
  }

  .profile {
      padding: 20px;
  }

  .profile-image img{
      width: 80px;
      height: 80px;
      background-color: #ccc;
      border-radius: 50%;
      margin-bottom: 10px;
  }

  .profile-name {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 10px;
      color : #000;
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

          #overlay {
              display: none;
              position: fixed;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              background: rgba(0, 0, 0, 0.8);
              z-index: 9;
          }
  .menu-links button:hover {
      background-color: #ddd;
  }
        
    </style>
</head>
<body class="w3-light-grey w3-content" style="max-width:1600px">

   <!-- Burger Menu -->
<div class="burger-menu">
    <!-- Menu Icon -->
    <div class="menu-icon" onclick="toggleMenu()">
        &#9776; <!-- Burger icon -->
    </div>
    <!-- Side Menu -->
    <div class="side-menu" id="sideMenu">
        <!-- Profile Section -->
        <div class="profile">
            <!-- Profile Image -->
            <div class="profile-image" onclick="showEnlargedImage()">
                <!-- Display the user's profile image -->
                <img src="data:image/jpeg;base64,<?php echo base64_encode($userDetails['image_path']); ?>" alt="Profile Image">
            </div>
            <!-- Enlarged image and overlay for when the image is clicked -->
            <div id="overlay" onclick="hideEnlargedImage()"></div>
            <img id="enlargedImage" src="data:image/jpeg;base64,<?php echo base64_encode($userDetails['image_path']); ?>" alt="Enlarged User Image">
            <!-- Profile Name -->
            <div class="profile-name"><?php echo $userDetails['user_name']; ?></div>
            <!-- Menu Links/Buttons -->
            <div class="menu-links">
                <button onclick="goToPage('profile.php')">Add Tasks</button>
                <button onclick="goToPage('task_home.php')">View My Tasks</button>
                <button onclick="goToPage('prof')">View Accepted Tasks</button>
                <button onclick="goToPage('settings')">Delete Tasks</button>
                <button onclick="goToPage('other')">Go To Home</button>
            </div>
        </div>
    </div>
</div>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="closeNav()" style="cursor:pointer;background-color:black;color:white"
    title="close side menu" id="myOverlay"></div>

    <div class="w3-panel w3-large">
        <i class="fa fa-facebook-official w3-hover-opacity"></i>
        <i class="fa fa-instagram w3-hover-opacity"></i>
        <i class="fa fa-snapchat w3-hover-opacity"></i>
        <i class="fa fa-pinterest-p w3-hover-opacity"></i>
        <i class="fa fa-twitter w3-hover-opacity"></i>
        <i class="fa fa-linkedin w3-hover-opacity"></i>
    </div>
</nav>

    <!-- Overlay effect when opening sidebar on small screens -->
    <div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer;background-color:black;color:white"
        title="close side menu" id="myOverlay"></div>

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
                <header class="w3-container w3-teal">
                    <span onclick="closeAddTaskModal()" class="w3-button w3-display-topright">&times;</span>
                    <h2>Add Task</h2>
                </header>
                <div class="w3-container">
                    <form method="post" action="add_task.php">
                        <!-- Task Description Text Box -->
                        <div class="w3-row-padding">
                            <div class="w3-half">
                                <br>
                                <label for="taskDescription">Task Description:</label>
                                <input type="text" id="taskDescription" name="task" required>
                                <br><br>
                                <label for="Cost">Cost:</label>
                                $<input type="text" id="cost" name="cost" required>
                                <br><br>
                                <label for="TimeLeft">Time Left:</label>
                                <input type="time" id="TimeLeft" name="Time Left" required>
                                <br><br>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="w3-row-padding">
                            <div class="w3-half">
                                <button type="submit">Add Task</button>
                                <br><br>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tasks from Database -->
        <div class="w3-row-padding">
            <!-- Display tasks here -->
            <?php foreach ($all_tasks as $task): ?>
                <div class="w3-third w3-container w3-margin-bottom task-item">
                    <div class="w3-display-container">
                        <div class="task-info">
                            <p><b><?= $task['user_name'] ?></b></p>
                            <p><?= $task['task_description'] ?></p>
                            <div class="task-details">
                                <p>Cost: x</p>
                                <p>Time Left: x</p>
                            </div>
                            <!-- Button to display user details -->
                            <button onclick="openUserDetailsPopup('<?= $task['email'] ?>', '<?= $task['phone_number'] ?>', '<?= $task['timestamp'] ?>')">Show Details</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($all_tasks)): ?>
                <p>No tasks found.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="w3-center w3-padding-32">
            <div class="w3-bar">
                <a href="#" class="w3-bar-item w3-button w3-hover-black">«</a>
                <a href="#" class="w3-bar-item w3-black w3-button">1</a>
                <a href="#" class="w3-bar-item w3-button w3-hover-black">2</a>
                <a href="#" class="w3-bar-item w3-button w3-hover-black">3</a>
                <a href="#" class="w3-bar-item w3-button w3-hover-black">4</a>
                <a href="#" class="w3-bar-item w3-button w3-hover-black">»</a>
            </div>
        </div>

        <!-- User Details Popup -->
        <div id="userDetailsPopup" class="w3-modal">
            <div class="w3-modal-content">
                <header class="w3-container w3-teal">
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
        <div id="viewTasksModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeViewTasksModal()">&times;</span>
                <h2>All Tasks</h2>
                <!-- Display tasks here -->
                <?php foreach ($all_tasks as $task): ?>
                    <?php if ($task['email'] === $email): ?>
                        <div>
                            <p><b><?= $task['user_name'] ?></b></p>
                            <p><?= $task['task_description'] ?></p>
                            <div>
                                <p>Cost: x</p>
                                <p>Time Left: x</p>
                            </div>
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
            function openUserDetailsPopup(email, phone, timestamp) {
                document.getElementById("userEmail").innerText = email;
                document.getElementById("userPhone").innerText = phone;
                document.getElementById("taskTimestamp").innerText = timestamp;
                document.getElementById("userDetailsPopup").style.display = "block";
            }

            // Function to close user details popup
            function closeUserDetailsPopup() {
                document.getElementById("userDetailsPopup").style.display = "none";
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

        </script>

    </div>
</body>
</html>