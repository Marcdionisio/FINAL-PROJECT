<?php
session_start();

function checkSession() {
    // Check if the user is logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: login.php?alert=warning&message=Please log in to access this page.");
        exit();
    }

    // Set form expiration time to 10 minutes if not already set
    if (!isset($_SESSION['form_expiration'])) {
        $_SESSION['form_expiration'] = time() + 1800; // 1800 seconds = 30 minutes
    }

    // Check if the form has expired
    if (time() > $_SESSION['form_expiration']) {
        header("Location: login.php?alert=warning&message=Login again. Session expired.");
        exit();
    }

    // Reset the expiration time on form usage
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['form_expiration'] = time() + 1800; // Reset expiration on form submission
    }
}
?>
