<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust this path if needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user inputs
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $institutional_email = htmlspecialchars(trim($_POST['institutional_email']));
    $new_username = htmlspecialchars(trim($_POST['username']));
    $student_number = htmlspecialchars(trim($_POST['student_number']));
    $course = htmlspecialchars(trim($_POST['course']));
    $year_level = htmlspecialchars(trim($_POST['year_level']));
    $college = htmlspecialchars(trim($_POST['college']));
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate email and password
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@plmun.edu.ph$/", $institutional_email)) {
        header("Location: signup.html?alert=error&message=Email address must be a valid PLMUN institutional account.&redirect=signup.html");
        exit();
    }
    if (strlen($new_password) < 8 || !preg_match("/[A-Za-z]/", $new_password) || !preg_match("/[0-9]/", $new_password)) {
        header("Location: signup.html?alert=error&message=Password must be at least 8 characters long and contain both letters and numbers.&redirect=signup.html");
        exit();
    }
    if ($new_password !== $confirm_password) {
        header("Location: signup.html?alert=error&message=Passwords do not match.&redirect=signup.html");
        exit();
    }

    // Check for duplicate username and email
    include 'db.php'; // Include your database connection file

    $stmt = $conn->prepare("SELECT username FROM stud_users WHERE username = ?");
    $stmt->bind_param("s", $new_username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header("Location: signup.html?alert=error&message=Username already exists.&redirect=signup.html");
        $stmt->close();
        exit();
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT institutional_email FROM stud_users WHERE institutional_email = ?");
    $stmt->bind_param("s", $institutional_email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header("Location: signup.html?alert=error&message=An account with this email already exists.&redirect=signup.html");
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Encrypt the password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Check for available ID in deleted_ids
    $result = $conn->query("SELECT id FROM deleted_ids ORDER BY id LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $new_id = $row['id'];
        $conn->query("DELETE FROM deleted_ids WHERE id = $new_id");
    } else {
        $new_id = NULL; // Let auto-increment handle it
    }

    // Prepare the insert statement
    $stmt = $conn->prepare("INSERT INTO stud_users (user_id, first_name, last_name, institutional_email, username, student_number, course, year_level, college, password, verification_token, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
    $verification_token = bin2hex(random_bytes(16));
    $stmt->bind_param("issssssssss", $new_id, $first_name, $last_name, $institutional_email, $new_username, $student_number, $course, $year_level, $college, $hashed_password, $verification_token);
    
    if ($stmt->execute()) {
        // Send verification email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'plmunlibrarytesting@gmail.com'; // Your Gmail address
            $mail->Password = 'jthdyqovunqoxlav'; // Your Gmail app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('plmunlibrarytesting@gmail.com', 'PLMUN LIBRARY - TESTING ONLY');
            $mail->addAddress($institutional_email);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email Address';

            $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .content {
            text-align: center;
        }
        .content h2 {
            color: #007bff;
        }
        .content p {
            font-size: 16px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #2a4f1e;
            border-radius: 4px;
            text-decoration: none;
            text-align: center;
        }
        .button:hover {
            background-color:#47942e;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <h2>Email Verification</h2>
            <p>Hello ' . htmlspecialchars($first_name) . ' ' . htmlspecialchars($last_name) . ',</p>
            <p>Thank you for registering with PLMUN Library. Please click the button below to verify your email address.</p>
            <a href="http://localhost/capstone/verify_email.php?token=' . $verification_token . '" class="button">Verify Email</a>
        </div>
        <div class="footer">
            <p>If you did not register for this account, please ignore this email.</p>
        </div>
    </div>
</body>
</html>
';
            $mail->send();
            header("Location: signup.html?alert=success&message=Signup successful! Please check your email for verification.&redirect=login.php");
        } catch (Exception $e) {
            // Handle email sending failure
            header("Location: signup.html?alert=error&message=Account created, but failed to send verification email. Please try again later.&redirect=signup.html");
        }
    } else {
        // Handle database insert failure
        header("Location: signup.html?alert=error&message=Error: " . $stmt->error . "&redirect=signup.html");
    }

    $stmt->close();
}
?>
