<?php
include 'session_check.php';
checkSession();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMUN - Home</title>
    <link rel="icon" type="image/x-icon" href="plmun.ico">
    <link rel="stylesheet" href="home_user.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
</head>
<body>
    <div class="navbar">
        <div class="logo-container">
            <img src="plmun.png" alt="PLMUN Logo">
            <h2>PLMUN Library Management System</h2>
        </div>
        <ul>
            <li><a href="home_user.php"><i class="fas fa-home"></i> Home</a></li>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn"><i class="fas fa-book"></i> Books <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="list_of_books.php">List of Books</a>
                    <a href="transactions.php">Transactions</a>
                </div>
            </li>
            <li><a href="my_qr.php"><i class="fas fa-qrcode"></i> My QR</a></li>
            <li class="logout"><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="container">
        <h1>Welcome to the User Dashboard</h1>
        <p>Select an option from the navbar to get started.</p>
    </div>

    <script>
        // Dropdown functionality
        document.querySelectorAll('.dropbtn').forEach(button => {
            button.addEventListener('click', () => {
                const dropdownContent = button.nextElementSibling;
                dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
            });
        });
    </script>
</body>
</html>
