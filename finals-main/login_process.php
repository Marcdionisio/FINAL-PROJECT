<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['captcha'])) {
    $usernameOrEmail = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);
    $captcha = trim($_POST['captcha']);

    if ($captcha == $_SESSION['captcha_code']) {
        // Check in the students' table
        $stmt = $conn->prepare("SELECT user_id, password, is_verified FROM stud_users WHERE username = ? OR institutional_email = ?");
        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password, $is_verified);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                if ($is_verified) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['form_expiration'] = time() + 600; // Set form expiration time to 10 minutes
                    header("Location: login.php?alert=success&message=Login successful!&redirect=home_user.php");
                    exit();
                } else {
                    header("Location: login.php?alert=warning&message=Please verify your email address before logging in.");
                    exit();
                }
            } else {
                header("Location: login.php?alert=error&message=Invalid username or password.");
                exit();
            }
        } else {
            // Check in the admins' table if not found in students' table
            $stmt->close(); // Close the previous statement
            $stmt = $conn->prepare("SELECT user_id, password, is_verified FROM admin_users WHERE username = ? OR institutional_email = ?");
            $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($admin_id, $hashed_password, $is_verified);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    if ($is_verified) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['user_id'] = $admin_id;
                        $_SESSION['form_expiration'] = time() + 600; // Set form expiration time to 10 minutes
                        header("Location: login.php?alert=success&message=Login successful!&redirect=dashboard.php");
                        exit();
                    } else {
                        header("Location: login.php?alert=warning&message=Please verify your email address before logging in.");
                        exit();
                    }
                } else {
                    header("Location: login.php?alert=error&message=Invalid username or password.");
                    exit();
                }
            } else {
                header("Location: login.php?alert=error&message=Invalid username or password.");
                exit();
            }
        }

        $stmt->close();
    } else {
        header("Location: login.php?alert=error&message=Invalid CAPTCHA. Please try again.");
        exit();
    }
} else {
    header("Location: login.php?alert=warning&message=Please fill in all fields.");
    exit();
}

$conn->close();
?>
