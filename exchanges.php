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

// Retrieve user details from the database based on session email
$stmt = $conn->prepare("SELECT user_name, phone_number, email, image_path FROM user_details WHERE email = ?");
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
$user_phone = $userDetails['phone_number'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Barter Items</title>
    <style>
        body {
            margin: 0;
            font-family: 'Raleway', sans-serif;
            background-color: white;
            color: black;
            position: relative;
        }

     

        .task-info p {
            color: black;
        }

        header#tasks h1 {
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: white;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            overflow-y: auto;
            position: relative;
            right: 50px;
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
            width: 175px;
            height: 175px;
            background-color: #ccc;
            border-radius: 100%;
            margin-bottom: 10px;
            margin-right: 10px;
        }

        .profile-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
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

        .w3-half {
            color: #000;
        }

        .side-menu-open .w3-main {
            margin-left: 250px;
        }

        .side-menu-open .w3-main {
            margin-left: 0;
        }

        .product {
            background-color: black;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 50px; /* Apply border-radius to the entire product box */
            overflow: hidden; /* Ensure content does not overflow the rounded corners */
            box-shadow: 0 40px 80px rgba(0, 100, 0, 0.2); /* Optional: add shadow for better visual appeal */
        }

        .product img {
            border-radius: 50px; /* Apply border-radius to images inside product */
        }

        .profile-details {
            display: flex;
            align-items: center;
        }

        .profile-details img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-right: 10px;
        }

        /* Modal header background color */
        .w3-modal-content header {
            background-color: #000; /* Set the background color to black */
            color: #fff; /* Ensure text color is white for contrast */
        }

        .w3-modal-content header h2 {
            margin: 0;
        }

.w3-row-padding {
            margin-left: -16px;
            margin-right: -16px;
        }

        .w3-col.m12 {
            margin-bottom: 16px;
        }

        .w3-round {
            border-radius: 8px;
        }

        .w3-white {
            color: white;
        }

        .w3-padding {
            padding: 16px;
            background-color: #000;
            color:white;
            border: 10px solid red;
            
        }

        .w3-margin-right {
            margin-right: 16px;
        }

        .w3-margin-bottom {
            margin-bottom: 16px;
        }

        .w3-button {
            background-color: #444 ;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        .w3-button:hover {
            background-color: #555;
        }

        .w3-clear {
            clear: both;
        }

        hr {
            border: 0;
            border-top: 1px solid red;
        }
    </style>
</head>
<body>
<div class="burger-menu">
<div id="menu-icon" class="menu-icon">
&#9776;
    </div>
    <div class="side-menu" id="sideMenu">
        <div class="profile">
            <div class="profile-image" onclick="showEnlargedImage()">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($image_path); ?>" alt="Profile Image">
            </div>
            <div id="overlay" onclick="hideEnlargedImage()"></div>
            <img id="enlargedImage" src="data:image/jpeg;base64,<?php echo base64_encode($image_path); ?>" alt="Enlarged User Image">
            <div class="profile-name"><?php echo htmlspecialchars($user_name); ?></div>
            <div class="menu-links">
                <button onclick="goToPage('profile.php')">My Profile</button>
                <button onclick="openAddBarterModal()">Add Barter</button>
                <button onclick="viewMyBarters()">View My Barters</button>
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

<h1>Items Available for Barter</h1>
<div class="w3-row-padding">
    <?php
    $sql = "SELECT b.barter_id, b.user_email, b.user_barters, b.barter_description, b.image, u.user_name, u.image_path as user_image, u.phone_number 
            FROM barters b 
            JOIN user_details u ON b.user_email = u.email";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo '<div class="w3-col m12">';
            echo '<div class="w3-card w3-round w3-white">';
            echo '<div class="w3-container w3-padding">';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['user_image']) . '" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px;height:60px;border-radius:50%">';
            echo '<span class="w3-right w3-opacity">1 min</span>'; // Example of timestamp; you can replace it with actual data if available
            echo '<h4>' . htmlspecialchars($row['user_name']) . '</h4>';
            echo '<hr class="w3-clear">';
            echo '<p><strong>Item to be exchanged:</strong> ' . htmlspecialchars($row['user_barters']) . '</p>';
            echo '<p><strong>Barter description:</strong> ' . htmlspecialchars($row['barter_description']) . '</p>';
            echo '<div class="w3-row-padding" style="margin:0 -16px">';
            echo '<div class="w3-half">';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image']) . '" style="width:100%" alt="Item Image" class="w3-margin-bottom">';
            echo '</div>';
            echo '</div>';
            if ($row['user_email'] == $userEmail) {
                echo '<button type="button" class="w3-button w3-theme" onclick="deleteBarter(' . $row['barter_id'] . ')"><i class="fa fa-pencil"></i> Delete</button>';
            }
            echo '</div>';
            echo '</div>';
            echo '<br><br>';
            echo '</div>';
        }
    } else {
        echo '<p>No items available for barter.</p>';
    }

    $conn->close();
    ?>
</div>


<div id="addBarterModal" class="w3-modal">
    <div class="w3-modal-content">
        <header class="w3-container">
            <span onclick="closeAddBarterModal()" class="w3-button w3-display-topright">&times;</span>
            <h2>Add Barter</h2>
        </header>
        <div class="w3-container">
            <form action="add_barter.php" method="post" enctype="multipart/form-data">
                <p><input class="w3-input" type="text" name="user_barters" placeholder="Item to be exchanged" required></p>
                <p><input class="w3-input" type="text" name="barter_description" placeholder="Barter Description" required></p>
                <p><input class="w3-input" type="file" name="barter_image" accept="image/*" required></p>
                <p><input class="w3-button w3-green" type="submit" value="Add Barter"></p>
            </form>
        </div>
    </div>
</div>

<!-- View My Barters Modal -->
<div id="viewMyBartersModal" class="w3-modal">
    <div class="w3-modal-content">
        <header class="w3-container">
            <span onclick="closeViewMyBartersModal()" class="w3-button w3-display-topright">&times;</span>
            <h2>My Barters</h2>
        </header>
        <div class="w3-container">
            <div id="myBartersContent">
                <!-- User's barters will be loaded here by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
   

    function goToPage(page) {
        window.location.href = page;
    }

    function openAddBarterModal() {
        document.getElementById('addBarterModal').style.display = 'block';
    }

    function closeAddBarterModal() {
        document.getElementById('addBarterModal').style.display = 'none';
    }

    function viewMyBarters() {
        document.getElementById('viewMyBartersModal').style.display = 'block';
        loadUserBarters();
    }

    function closeViewMyBartersModal() {
        document.getElementById('viewMyBartersModal').style.display = 'none';
    }

    function loadUserBarters() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'fetch_user_barters.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status === 200) {
                document.getElementById('myBartersContent').innerHTML = this.responseText;
            } else {
                document.getElementById('myBartersContent').innerHTML = 'Failed to load barters.';
            }
        };
        xhr.send();
    }

    function showEnlargedImage() {
        document.getElementById('enlargedImage').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    }

    function hideEnlargedImage() {
        document.getElementById('enlargedImage').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }

    const menuIcon = document.getElementById('menu-icon');
    const sideMenu = document.getElementById('sideMenu');

    function toggleMenu() {
        if (sideMenu.style.left === "0px") {
            sideMenu.style.left = "-250px";
            document.removeEventListener('click', closeMenuOnClickOutside);
        } else {
            sideMenu.style.left = "0px";
            document.addEventListener('click', closeMenuOnClickOutside);
        }
    }

    function closeMenuOnClickOutside(event) {
        if (!sideMenu.contains(event.target) && !menuIcon.contains(event.target)) {
            sideMenu.style.left = "-250px";
            document.removeEventListener('click', closeMenuOnClickOutside);
        }
    }

    function goToPage(page) {
        // Close the side menu before navigating to the desired page
        sideMenu.style.left = "-250px";
        document.removeEventListener('click', closeMenuOnClickOutside);
        window.location.href = page;
    }

    menuIcon.addEventListener('click', function(event) {
        toggleMenu();
        event.stopPropagation();  // Prevent the document click event from firing
    });

    sideMenu.addEventListener('click', function(event) {
        event.stopPropagation();  // Prevent clicks inside the side menu from closing the menu
    });
       // Function to redirect to home page
       function goToHomePage() {
                window.location.href = "home.php";
            }

            function deleteBarter(barterId) {
        if (confirm("Are you sure you want to delete this barter?")) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_barter.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.status === 200) {
                    alert(this.responseText);
                    location.reload(); // Reload the page to reflect the changes
                } else {
                    alert('Failed to delete barter.');
                }
            };
            xhr.send('barter_id=' + barterId);
        }
    }
</script>

</body>
</html>
