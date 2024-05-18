<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'C:\c\Community_Application\phpmailer\vendor\autoload.php';

// Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF; // Disable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'yuvensenthil@gmail.com';                 // SMTP username
    $mail->Password   = 'teyjoyuburcwrvai';                        // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
    $mail->Port       = 465;                                    // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    // Recipients
    $mail->setFrom($_POST['Email'], $_POST['Name']);
    $mail->addAddress('yuvensenthil@gmail.com');                 // Add a recipient

    // Content
    $mail->isHTML(true);                                       // Set email format to HTML
    $mail->Subject = $_POST['Subject'];
    $mail->Body    = $_POST['Message'];

    // Send mail
    $mail->send();
    
    // Custom success message
    $success_message = "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Success</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                background-image: url(bgnd18.jpeg);
                background-size: cover;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                color: white;
                font-family: Arial, sans-serif;
                text-align: center;
            }
        </style>
        <meta http-equiv='refresh' content='5;url=home.php'> <!-- Redirect to home.php after 5 seconds -->
    </head>
    <body>
        <div>
            <h1>Your message has been successfully sent!</h1>
            <p>We'll get back to you as soon as possible.</p>
            <h4>Redirecting back to home page</h4>
        </div>
    </body>
    </html>";
    echo $success_message;
} catch (Exception $e) {
    // Custom error message
    $error_message = "Oops! Something went wrong while sending your message. Please try again later.";
    echo $error_message;
}
?>
