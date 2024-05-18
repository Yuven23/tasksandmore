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

  // Assuming you have a database connection established and $conn represents the connection object
  // Retrieve user details from the database based on session email
  // Replace 'your_db_table' with the actual table name where user details are stored
  $userEmail = $_SESSION['email']; // Retrieve user email from session

  // Example database query to fetch user details including image_path
$stmt = $conn->prepare("SELECT user_name, image_path FROM user_details WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$userDetails = $result->fetch_assoc();

// Check if user details including image_path are found
if (!$userDetails) {
    // Handle case where user details are not found
    echo "User details not found.";
    exit();
}

// Retrieve image path from the fetched user details
$image_path = $userDetails['image_path'];
$user_name = $userDetails['user_name'];


  // Check if user details are found
  // Example:
  if (!$userDetails) {
      // Handle case where user details are not found
      // You can redirect the user to an error page or display a message
      echo "User details not found.";
      exit();
  }
  ?>
  <!DOCTYPE html>
  <html>
  <head>
  <title>W3.CSS Template</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
  body,h1,h2,h3,h4,h5,h6 {font-family: "Raleway", Arial, Helvetica, sans-serif}

  body, html {
    height: 100%;
    line-height: 1.8;
  }

  body {
      margin: 0;
      font-family: 'Raleway', sans-serif;
      background: url("Profile_bgnd.png") center/cover fixed no-repeat;
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

  .video-container {
      margin: 50px auto;
      position: relative;
      padding-bottom: 56.25%; /* 16:9 aspect ratio */
      width: 80%; 
  }
    
  .video-container iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
  }

  .w3-bar .w3-button {
    padding: 16px;
  }

  /* Contact button style */
  .contact-button {
    padding: 10px 20px; /* Adjust padding as needed */
    margin-top: 10px; /* Add margin for spacing */
    font-size: 16px; /* Adjust font size as needed */
    transition: background-color 0.3s, color 0.3s; /* Smooth transition for hover effect */
    cursor: pointer; /* Change cursor to pointer on hover */
  }

  /* Hover effect */
  .contact-button:hover {
    background-color: #f1f1f1; /* Change background color on hover */
    color: #333; /* Change text color on hover */
  }

  /* Hide the contact details initially */
  .contact-details {
    display: none;
  }

  /* User account icon style */
  .user-account {
    position: relative;
    cursor: pointer;
    margin-left: auto;
  }

  .user-account img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
  }

  .user-dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    z-index: 1;
    right: 0;
  }

  .user-dropdown-content a {
    display: block;
    padding: 12px 16px;
    text-decoration: none;
    color: black;
  }

  .user-dropdown-content a:hover {
    background-color: #f1f1f1;
  }

  .user-account:hover .user-dropdown-content {
    display: block;
  }

    .w3-bar .w3-button.icon {
      float: right;
      display: block;
    
    }
    .w3-bar-item {
      padding: 15px 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      transition: background-color 0.3s;
    }
    /* Style for the toggled menu items */
    .w3-bar-item.active {
      background-color: #f0f0f0;
    }
    .menu-item {
      display: inline-block;
    }
    /* Style for the links */
    .menu-link {
      padding: 15px 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      transition: background-color 0.3s;
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
  <body>

  <div class="burger-menu">
    <div class="menu-icon" onclick="toggleMenu()">
        &#9776;
    </div>
    <div class="side-menu" id="sideMenu">
        <div class="profile">
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
                <button onclick="goToPage('profile.php')"> My Profile</button>
                <button onclick="goToPage('task_home.php')">Tasks</button>
                <button onclick="goToPage('prof')">Exchanges</button>
                <button onclick="goToPage('settings')">LeaderBoard</button>
                <button onclick="goToPage('other')">Other</button>
            </div>
        </div>
    </div>
</div>




  <!-- About Section -->
  <div class="w3-container overlay" style="padding:128px 16px" id="about">
    <h3 class="w3-center">ABOUT THE WEBSITE</h3>
    <p class="w3-center w3-large">Key features of our website</p>
    <div class="w3-row-padding w3-center" style="margin-top:64px">
      <div class="w3-quarter">
        <i class="fa fa-desktop w3-margin-bottom w3-jumbo w3-center"></i>
        <p class="w3-large">Responsive</p>
        <p>Being responsive means more than just adapting to different screen sizes; it's about being flexible and adaptable in all aspects of our interactions and communications. By actively listening to others, empathizing with their perspectives, and remaining open to feedback, we can cultivate responsiveness in our relationships.</p>
      </div>
      <div class="w3-quarter">
        <i class="fa fa-heart w3-margin-bottom w3-jumbo"></i>
        <p class="w3-large">Passion</p>
        <p>Humans are driven by an inherent passion that fuels their pursuits, ambitions, and creative endeavors. This passion manifests in various forms, from the relentless pursuit of knowledge to the relentless quest for self-improvement. It's the driving force behind innovation, discovery, and the pursuit of excellence in all aspects of life.</p>
      </div>
      <div class="w3-quarter">
        <i class="fa fa-diamond w3-margin-bottom w3-jumbo"></i>
        <p class="w3-large">Design</p>
        <p>Our impeccable design is a testament to our unwavering commitment to excellence in every aspect of our work. It reflects our dedication to crafting experiences that delight and inspire,with meticulous attention to detail and a deep understanding of user needs, we create designs that not only look stunning but also function seamlessly across all platforms and devices..</p>
      </div>
      <div class="w3-quarter">
        <i class="fa fa-cog w3-margin-bottom w3-jumbo"></i>
        <p class="w3-large">Support</p>
        <p>Our support team is comprised of highly skilled professionals who are passionate about helping our clients succeed. Whether it's troubleshooting technical issues, providing product demonstrations, or offering expert advice, our team is always ready to lend a helping hand with patience, empathy, and expertise..</p>
      </div>
    </div>
  </div>

  <!-- Team Section -->
  <div class="w3-container overlay" style="padding:128px 16px;background-image: url('team_background.jpg');">
    <h3 class="w3-center">THE TEAM</h3>
    <p class="w3-center w3-large">The ones who run this website</p>
    <div class="w3-row-padding w3-grayscale" style="margin-top:64px">
      <div class="w3-col l3 m6 w3-margin-bottom">
        <div class="w3-card">
          <div class="w3-container">
            <h3>Shiiv R.S</h3>
            <p class="w3-opacity">Creator</p>
            <p>The Biggest risk is to not take any risk.</p>
            <button class="w3-button w3-light-grey w3-block contact-button" onclick="toggleContact('contact-shiiv')"><i class="fa fa-envelope"></i> Contact</button>
            <div class="contact-details" id="contact-shiiv">
              <p><i class="fa fa-phone fa-fw w3-xxlarge w3-margin-right"></i> Phone: +91 93846 37022</p>
              <p><i class="fa fa-envelope fa-fw w3-xxlarge w3-margin-right"> </i> Email: 220701331@rajalakshmi.edu.in</p>
            </div>
          </div>
        </div>
      </div>
      <div class="w3-col l3 m6 w3-margin-bottom">
        <div class="w3-card">
          <div class="w3-container">
            <h3>Yuven Senthilkumar</h3>
            <p class="w3-opacity">Creator</p>
            <p>More gold has been mined from the brains of men than has ever been from the world.</p>
            <button class="w3-button w3-light-grey w3-block contact-button" onclick="toggleContact('contact-yuven')"><i class="fa fa-envelope"></i> Contact</button>
            <div class="contact-details" id="contact-yuven">
              <p><i class="fa fa-phone fa-fw w3-xxlarge w3-margin-right"></i> Phone: +91 99400 21719</p>
              <p><i class="fa fa-envelope fa-fw w3-xxlarge w3-margin-right"> </i> Email:220701330@rajalakshmi.edu.in</p>
            </div>
          </div>
        </div>
      </div>
      <div class="w3-col l3 m6 w3-margin-bottom">
        <div class="w3-card">
          <div class="w3-container">
            <h3>Udhaya Shankar J</h3>
            <p class="w3-opacity"> Creator</p>
            <p>We learn from mistakes, convey our learnings by success</p>
            <button class="w3-button w3-light-grey w3-block contact-button" onclick="toggleContact('contact-udhaya')"><i class="fa fa-envelope"></i> Contact</button>
            <div class="contact-details" id="contact-udhaya">
              <p><i class="fa fa-phone fa-fw w3-xxlarge w3-margin-right"></i> Phone: +91 6374 829 686</p>
              <p><i class="fa fa-envelope fa-fw w3-xxlarge w3-margin-right"> </i> Email:220701306@rajalakshmi.edu.in</p>
            </div>
          </div>
        </div>
      </div>
      <div class="w3-col l3 m6 w3-margin-bottom">
        <div class="w3-card">
          <div class="w3-container">
            <h3>Sharukeshwar P</h3>
            <p class="w3-opacity">Creator</p>
            <p>"Creation is the essence of mankind, and I'm here to do my part. With a good amount of expertise in designing, I bridge the gap between my visions and reality.”</p>
            <button class="w3-button w3-light-grey w3-block contact-button" onclick="toggleContact('contact-sharukeshwar')"><i class="fa fa-envelope"></i> Contact</button>
            <div class="contact-details" id="contact-sharukeshwar">
              <p><i class="fa fa-phone fa-fw w3-xxlarge w3-margin-right"></i> Phone: +91 93610 70552</p>
              <p><i class="fa fa-envelope fa-fw w3-xxlarge w3-margin-right"> </i> Email: 220701265@rajalakshmi.edu.in</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Video Section -->
  <div class="content">
      <p style="font-size: 24px;justify-content:center;align-items:center">Watch this video to learn about the barter system:</p> <!-- Added line -->
      <div class="video-container">
          <iframe width="960" height="540" src="https://www.youtube.com/embed/WCr5UVf-vKM" allowfullscreen></iframe>
      </div>
  </div>

  <!-- Contact Section -->
  <div class="w3-container" style="padding:128px 16px" id="contact">
    <h3 class="w3-center">CONTACT</h3>
    <p class="w3-center w3-large">Lets get in touch. Send us a message:</p>
    <div style="margin-top:48px">
      <form action="send_msg.php" method="post" target="_blank">
        <p><input class="w3-input w3-border" type="text" placeholder="Name" required name="Name"></p>
        <p><input class="w3-input w3-border" type="text" placeholder="Email" required name="Email"></p>
        <p><input class="w3-input w3-border" type="text" placeholder="Subject" required name="Subject"></p>
        <p><input class="w3-input w3-border" type="text" placeholder="Message" required name="Message"></p>
        <p>
          <button class="w3-button w3-black" type="submit">
          <i class="fa fa-paper-plane"></i> SEND MESSAGE
          </button>
        </p>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <footer class="w3-center w3-black w3-padding-64">
    <a href="home.php" class="w3-button w3-light-grey"><i class="fa fa-arrow-up w3-margin-right"></i>To the top</a>
    <div class="w3-xlarge w3-section">
      <i class="fa fa-facebook-official w3-hover-opacity"></i>
      <i class="fa fa-instagram w3-hover-opacity"></i>
      <i class="fa fa-snapchat w3-hover-opacity"></i>
      <i class="fa fa-pinterest-p w3-hover-opacity"></i>
      <i class="fa fa-linkedin w3-hover-opacity"></i>
    </div>
    <p>Powered by <a href="https://www.w3schools.com/w3css/default.asp" title="W3.CSS" target="_blank" class="w3-hover-text-green">w3.css</a></p>
  </footer>

  <script>
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

  function goToPage(page) {
      // Close the side menu before navigating to the desired page
      var sideMenu = document.getElementById('sideMenu');
      sideMenu.style.left = "-250px";
      document.removeEventListener('click', closeMenuOnClickOutside);

      // Here you can implement the logic to navigate to the desired page
      switch (page) {
          case 'task_home.php':
              // Navigate to the home page
              window.location.href = "task_home.php";
              break;
          case 'profile.php':
              // Navigate to the profile page
              window.location.href = "profile.php";
              break;
          case 'settings':
              // Navigate to the settings page
              window.location.href = "settings.html";
              break;
          default:
              console.log("Unknown page: " + page);
      }
      console.log("Navigating to " + page + " page");
  }
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
