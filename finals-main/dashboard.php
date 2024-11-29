<?php
include 'session_check.php';
checkSession();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMUN - Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="icon" type="image/x-icon" href="plmun.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="logo-container">
            <img src="plmun.png" alt="PLMUN Logo">
            <h2>PLMUN Library Management System</h2>
        </div>
        <ul class="nav-links">
            <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn"><i class="fas fa-list"></i> Books <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="list_of_books.php">Book List</a>
                    <a href="generate_barcode.php">Generate Barcode</a>
                </div>
            </li>
            <li><a href="student_monitoring.php"><i class="fas fa-user-graduate"></i> Student Monitoring</a></li>
            <li><a href="borrowed_returned_books.php"><i class="fas fa-book"></i> Borrowed-Returned Books</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Manage Users</a></li>
            <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="datetime">
            <p id="datetime"></p>
        </div>
        <div class="dashboard">
            <div class="card" id="total-books-card">
                <i class="fas fa-book"></i>
                <div class="info">
                    <h3>Total Books Available</h3>
                    <p id="total-books">0</p>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-book-reader"></i>
                <div class="info">
                    <h3>Total Borrowed Books</h3>
                    <p id="total-borrowed">0</p>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-book-open"></i>
                <div class="info">
                    <h3>Total Returned Books</h3>
                    <p id="total-returned">0</p>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-book-dead"></i>
                <div class="info">
                    <h3>Total Returned Late Books</h3>
                    <p id="total-returned-late">0</p>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-book-medical"></i>
                <div class="info">
                    <h3>Total Due Books</h3>
                    <p id="total-due">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- GRAPH -->
    <div id="graphModal" class="modal">
        <div class="modal-content">
            <span class="close">×</span>
            <canvas id="booksChart"></canvas>
            <button id="create-report">Create Report</button>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>
