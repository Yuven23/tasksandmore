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
$stmt = $conn->prepare("SELECT user_name, image_path FROM user_details WHERE email = ?");
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

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f1f1f1;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 100vw; /* Set width to 100% of viewport width */
            height: 100vh; /* Set height to 100% of viewport height */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* Background image */
            background-image: url('Profile_bgnd.png');
            /* Center and cover the background */
            background-size: cover;
            background-position: center;
            color: white; /* Set text color to white */
        }

        .profile-image img {
            width: 300px;
            height: 300px;
            background-color: #ccc;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .profile-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
        }

        .upload-container {
            margin-top: 20px;
        }

        .custom-file-upload {
            display: inline-block;
            padding: 15px 30px; /* Increased padding */
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border-radius: 8px; /* Increased border radius */
            font-size: 20px; /* Increased font size */
        }

        .custom-file-upload:hover {
            background-color: #45a049;
        }

        .custom-file-upload input[type="file"] {
            display: none;
        }

        .upload-container button {
            margin-top: 50px;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px 30px; /* Increased padding */
            text-align: center;
            display: inline-block;
            font-size: 20px; /* Increased font size */
            border-radius: 8px; /* Increased border radius */
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .upload-container button:hover {
            background-color: #45a049;
        }

        .home-button {
            margin-top: 20px;
        }

        .home-button button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px 30px; /* Increased padding */
            text-align: center;
            display: inline-block;
            font-size: 20px; /* Increased font size */
            border-radius: 8px; /* Increased border radius */
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .home-button button:hover {
            background-color: #45a049;
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
    </style>
</head>
<body>
<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($userName); ?></h1>
    <?php if ($imagePath): ?>
        <div class="profile-image" onclick="showEnlargedImage()">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($imagePath); ?>" alt="Profile Image">
        </div>
    <?php else: ?>
        <p>No profile image uploaded.</p>
    <?php endif; ?>

    <div class="upload-container">
        <!-- Include the form to upload a new image -->
        <form action="image_upload.php" method="post" enctype="multipart/form-data">
            <label class="custom-file-upload">
                <input type="file" name="image" id="image" accept="image/*" required>
                Choose File
            </label>
            <button type="submit">Upload</button>
        </form>
    </div>

    <div class="home-button">
        <form action="home.php" method="get">
            <button type="submit">Home</button>
        </form>
    </div>
</div>
<?php if ($imagePath): ?>
    <div id="overlay" onclick="hideEnlargedImage()"></div>
    <img id="enlargedImage" src="data:image/jpeg;base64,<?php echo base64_encode($imagePath); ?>" alt="Enlarged User Image">
<?php endif; ?>
<script>
    function showEnlargedImage() {
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('enlargedImage').style.display = 'block';
    }

    function hideEnlargedImage() {
        document.getElementById('overlay').style.display = 'none';
        document.getElementById('enlargedImage').style.display = 'none';
    }
</script>
</body>
</html>
