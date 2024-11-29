<?php
include 'session_check.php';
checkSession();
include 'db.php'; // Move this to the top for better practice

// Query to fetch data
$query = "
    SELECT 
        su.student_number, 
        su.last_name, 
        su.first_name, 
        su.college, 
        su.course, 
        su.year_level, 
        lm.entry_time, 
        lm.exit_time, 
        lm.status 
    FROM library_monitoring lm 
    JOIN stud_users su ON lm.user_id = su.user_id";

$result = $conn->query($query);
if (!$result) {
    die("Query Failed: " . $conn->error); // Basic error handling
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMUN - Student Monitoring</title>
    <link rel="icon" type="image/x-icon" href="plmun.ico">
    <link rel="stylesheet" href="student_monitoring.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="alerts.js" defer></script>
</head>
<body>
<nav class="navbar">
    <div class="logo-container">
        <img src="plmun.png" alt="PLMUN Logo">
        <h2>PLMUN Library Management System</h2>
    </div>
    <ul class="nav-links">
        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        <li class="dropdown">
            <a href="javascript:void(0)" class="dropbtn"><i class="fas fa-list"></i> Books <i class="fas fa-caret-down"></i></a>
            <div class="dropdown-content">
                <a href="list_of_books.php">Book List</a>
                <a href="generate_barcode.php">Generate Barcode</a>
            </div>
        </li>
        <li class="active"><a href="student_monitoring.php"><i class="fas fa-user-graduate"></i> Student Monitoring</a></li>
        <li><a href="borrowed_returned_books.php"><i class="fas fa-book"></i> Borrowed-Returned Books</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Manage Users</a></li>
        <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>
<div class="container">
    <div class="scanner-container">
        <h1>Library Student Monitoring</h1>
        <div class="scanner-content">
            <div id="qr-reader"></div>
            <div class="student-info">
                <form id="scanner-form" action="process_qr.php" method="post">
                    <input type="hidden" id="user_id" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

                    <label for="student-number">Student Number:</label>
                    <input type="text" id="student_number" name="student_number" readonly>
                    
                    <label for="last-name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" readonly>
                    
                    <label for="first-name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" readonly>

                    <label for="college">College:</label>
                    <input type="text" id="college" name="college" readonly>
                    
                    <label for="course">Course:</label>
                    <input type="text" id="course" name="course" readonly>
                    
                    <label for="year-level">Year Level:</label>
                    <input type="text" id="year_level" name="year_level" readonly>
                    
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
        <div class="transaction-list">
            <h2>In and Out List</h2>
            <div class="table-controls">
                <input type="text" id="search" placeholder="Search...">
                <select id="date-type">
                    <option value="entry_time">Entry Time</option>
                    <option value="exit_time">Exit Time</option>
                </select>
                <input type="date" id="start-date">
                <input type="date" id="end-date">
                <button onclick="filterByDate()">Filter</button>
                <button onclick="exportTableToExcel('transactionTable', 'student_monitoring')">Export to Excel</button>
            </div>
            <div class="table-container">
                <table id="transactionTable">
                    <thead>
                        <tr>
                            <th>Student Number</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>College</th>
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Entry Time</th>
                            <th>Exit Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['student_number']) . "</td>
                                    <td>" . htmlspecialchars($row['last_name']) . "</td>
                                    <td>" . htmlspecialchars($row['first_name']) . "</td>
                                    <td>" . htmlspecialchars($row['college']) . "</td>
                                    <td>" . htmlspecialchars($row['course']) . "</td>
                                    <td>" . htmlspecialchars($row['year_level']) . "</td>
                                    <td>" . htmlspecialchars($row['entry_time']) . "</td>
                                    <td>" . htmlspecialchars($row['exit_time']) . "</td>
                                    <td>" . htmlspecialchars($row['status']) . "</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination Controls -->
            <div class="pagination">
                <div class="page-numbers" id="pageNumbers"></div>
                <div id="pageInfo"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Check for alert parameters in the URL
    const urlParams = new URLSearchParams(window.location.search);
    const alertType = urlParams.get('alert');
    const alertMessage = urlParams.get('message');
    const redirectUrl = urlParams.get('redirect');

    // Map alert types to SweetAlert icon types
    let iconType;
    switch (alertType) {
        case 'success':
            iconType = 'success';
            break;
        case 'error':
            iconType = 'error';
            break;
        case 'warning':
            iconType = 'warning';
            break;
        default:
            iconType = 'info'; // Default icon
            break;
    }

    if (alertType && alertMessage) {
        showAlertWithRedirect(alertMessage, '', iconType, redirectUrl);
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script src="student_monitoring.js"></script>
</body>
</html>
