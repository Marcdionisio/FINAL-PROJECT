<?php
include 'db.php'; // Include your database connection file

if (isset($_GET['token'])) {
    $token = htmlspecialchars(trim($_GET['token']));

    // Prepare a statement to find the user with the given token
    $stmt = $conn->prepare("SELECT user_id FROM admin_users WHERE verification_token = ? AND is_verified = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Token exists, mark email as verified
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE admin_users SET is_verified = 1, verification_token = NULL WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
            <script>
            window.onload = function() {
                Swal.fire({
                    title: 'Success!',
                    html: 'Email verified successfully! You can now log in.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php';
                    }
                });
            };
            </script>";
        } else {
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
            <script>
            window.onload = function() {
                Swal.fire({
                    title: 'Error!',
                    html: 'Error verifying email. Please try again later.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'signup.html';
                    }
                });
            };
            </script>";
        }
    } else {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
        <script>
        window.onload = function() {
            Swal.fire({
                title: 'Invalid or Expired Token!',
                html: 'Invalid or expired token. Please sign up again.',
                icon: 'warning',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'signup.html';
                }
            });
        };
        </script>";
    }

    $stmt->close();
} else {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
    <script>
    window.onload = function() {
        Swal.fire({
            title: 'No Token Provided!',
            html: 'No token provided for verification.',
            icon: 'warning',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'signup.html';
            }
        });
    };
    </script>";
}

$conn->close();
?>
