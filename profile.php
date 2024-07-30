<?php
session_start();
include_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['email'];

// Fetch user details including the image
$stmt = $conn->prepare("SELECT user_name, email, phone_number, image_path, birthday, address, about_me FROM user_details WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$userDetails = $result->fetch_assoc();

if (!$userDetails) {
    echo "User details not found.";
    exit();
}

$userName = $userDetails['user_name'];
$imagePath = $userDetails['image_path'];

 // Fetch total days logged in
 $stmt_logins = $conn->prepare("SELECT COUNT(DISTINCT login_date) AS total_days_logged_in FROM user_logins WHERE user_email = ?");
 $stmt_logins->bind_param("s", $userEmail);
 $stmt_logins->execute();
 $result_logins = $stmt_logins->get_result();
 $totalDaysLoggedIn = $result_logins->fetch_assoc()['total_days_logged_in'];
 $stmt_logins->close();

 // Fetch total tasks completed
 $stmt_tasks = $conn->prepare("SELECT COUNT(t.task_id) AS total_tasks_completed FROM tasks t JOIN accepted_tasks at ON t.task_id = at.task_id WHERE at.accepted_by = ? AND t.completed = 1");
 $stmt_tasks->bind_param("s", $userEmail);
 $stmt_tasks->execute();
 $result_tasks = $stmt_tasks->get_result();
 $totalTasksCompleted = $result_tasks->fetch_assoc()['total_tasks_completed'];
 $stmt_tasks->close();

 // Close the statement
 
 $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?= htmlspecialchars($userDetails['user_name'], ENT_QUOTES, 'UTF-8') ?>'s Profile</title>
 <!-- Bootstrap CSS -->
 <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
 <style>
     body {
         color: #6F8BA4;
         margin-top: 20px;
     }
     .section {
         padding: 100px 0;
         position: relative;
     }
     .gray-bg {
         background-color: #f5f5f5;
     }
     img {
         max-width: 100%;
         vertical-align: middle;
         border-style: none;
     }
     .about-text h3 {
         font-size: 45px;
         font-weight: 700;
         margin: 0 0 6px;
     }
     @media (max-width: 767px) {
         .about-text h3 {
             font-size: 35px;
         }
     }
     .about-text h6 {
         font-weight: 600;
         margin-bottom: 15px;
     }
     @media (max-width: 767px) {
         .about-text h6 {
             font-size: 18px;
         }
     }
     .about-text p {
         font-size: 18px;
         max-width: 450px;
     }
     .about-text p mark {
         font-weight: 600;
         color: #20247b;
     }
     .about-list {
         padding-top: 10px;
     }
     .about-list .media {
         padding: 5px 0;
     }
     .about-list label {
         color: #20247b;
         font-weight: 600;
         width: 88px;
         margin: 0;
         position: relative;
     }
     .about-list label:after {
         content: "";
         position: absolute;
         top: 0;
         bottom: 0;
         right: 11px;
         width: 1px;
         height: 12px;
         background: #20247b;
         transform: rotate(15deg);
         margin: auto;
         opacity: 0.5;
     }
     .about-list p {
         margin: 0;
         font-size: 15px;
     }
     @media (max-width: 991px) {
         .about-avatar {
             margin-top: 30px;
         }
     }
     .about-section .counter {
         padding: 22px 20px;
         background: #ffffff;
         border-radius: 10px;
         box-shadow: 0 0 30px rgba(31, 45, 61, 0.125);
     }
     .about-section .counter .count-data {
         margin-top: 10px;
         margin-bottom: 10px;
     }
     .about-section .counter .count {
         font-weight: 700;
         color: #20247b;
         margin: 0 0 5px;
     }
     .about-section .counter p {
         font-weight: 600;
         margin: 0;
     }
     mark {
         background-image: linear-gradient(rgba(252, 83, 86, 0.6), rgba(252, 83, 86, 0.6));
         background-size: 100% 3px;
         background-repeat: no-repeat;
         background-position: 0 bottom;
         background-color: transparent;
         padding: 0;
         color: currentColor;
     }
     .theme-color {
         color: #fc5356;
     }
     .dark-color {
         color: #20247b;
     }
 </style>
</head>
<body>

<script>
        function toggleEditForm() {
            var form = document.getElementById('editForm');
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>
<body>
<div class="row" id="editForm" style="display:none;">
    <div class="col-lg-12">
        <form action="edit_profile.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Profile Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <div class="form-group">
                <label for="aboutMe">About Me</label>
                <textarea class="form-control" id="aboutMe" name="about_me"><?= htmlspecialchars($userDetails['about_me'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-group">
                <label for="birthday">Birthday</label>
                <input type="date" class="form-control" id="birthday" name="birthday" value="<?= htmlspecialchars($userDetails['birthday'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($userDetails['address'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($userDetails['email'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label for="phoneNumber">Phone Number</label>
                <input type="text" class="form-control" id="phoneNumber" name="phone_number" value="<?= htmlspecialchars($userDetails['phone_number'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <input type="hidden" name="username" value="<?= htmlspecialchars($userDetails['user_name'], ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

    <section class="section about-section gray-bg" id="about">
        <div class="container">
            <div class="row align-items-center flex-row-reverse">
                <!-- Profile Image Section for Small Screens -->
                <div class="col-12 d-block d-lg-none">
                    <div class="about-avatar">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($userDetails['image_path']); ?>" title="" alt="">
                    </div>
                </div>
                <section class="section about-section gray-bg" id="about">
        <div class="container">
            <div class="row align-items-center flex-row-reverse">
                <div class="col-lg-6 col-md-12">
                    <div class="about-text go-to">
                        <h3 class="dark-color"><?= htmlspecialchars($userDetails['user_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($userDetails['about_me'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Birthday:</strong> <?= htmlspecialchars($userDetails['birthday'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Address:</strong> <?= htmlspecialchars($userDetails['address'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($userDetails['email'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Phone Number:</strong> <?= htmlspecialchars($userDetails['phone_number'], ENT_QUOTES, 'UTF-8') ?></p>
                        <button class="btn btn-primary" onclick="toggleEditForm()">Edit Profile</button>
                    </div>
                </div>
                <!-- Profile Image Section for Large Screens -->
                <div class="col-lg-6 col-md-12 d-none d-lg-block">
                    <div class="about-avatar">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($userDetails['image_path']); ?>" title="" alt="">
                    </div>
                </div>
            </div>
            <div class="counter">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="count-data text-center">
                            <h6 class="count h2"><?= $totalDaysLoggedIn ?></h6>
                            <p class="m-0px font-w-600">Total Days Logged In</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="count-data text-center">
                            <h6 class="count h2"><?= $totalTasksCompleted ?></h6>
                            <p class="m-0px font-w-600">Total Tasks Completed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>