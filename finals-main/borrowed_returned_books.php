<?php
include 'session_check.php';
checkSession();
include 'db.php';

// Fetch transactions with join
$query = "
    SELECT 
        su.student_number, su.last_name AS su_last_name, su.first_name AS su_first_name, su.college AS su_college, su.course AS su_course, su.year_level AS su_year_level,
        b.book_name AS b_book_name, b.isbn AS b_isbn, b.author AS b_author, b.year_published AS b_year_published,
        t.borrow_time, t.return_time, t.due_date, t.stat
    FROM 
        transactions t
    JOIN 
        stud_users su ON t.user_id = su.user_id
    JOIN 
        books b ON t.book_id = b.id;
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMUN - Resources Management</title>
    <link rel="icon" type="image/x-icon" href="plmun.ico">
    <link rel="stylesheet" href="borrowed_returned_books.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="alerts.js" defer></script>
    <script src="borrowed_returned_books.js"></script>
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
            <li><a href="student_monitoring.php"><i class="fas fa-user-graduate"></i> Student Monitoring</a></li>
            <li class="active"><a href="borrowed_returned_books.php"><i class="fas fa-book"></i> Borrowed-Returned Books</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Manage Users</a></li>
            <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <div class="scanner-container">
            <h1>Borrowing & Returning of Books</h1>
            <div class="scanner-content">
                <div id="qr-reader"></div>
                <div class="book-info">
                    <form id="transaction-form" action="processTransaction.php" method="POST">
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
                    </div>
                    <div class="book-info">
                        <label for="book-name">Book Name:</label>
                        <input type="text" id="book_name" name="book_name" readonly>
                        <label for="isbn">ISBN:</label>
                        <input type="text" id="isbn" name="isbn" readonly>
                        <label for="author">Author:</label>
                        <input type="text" id="author" name="author" readonly>
                        <label for="year-published">Year Published:</label>
                        <input type="text" id="year_published" name="year_published" readonly>
                        <input type="hidden" name="stat" id="stat" value="borrowed">
                        <button type="submit">Submit</button>
                    </div>
                </form>
            </div>

            <!-- TRANSACTION DATABASE OUTPUT -->
            <div class="transaction-list">
                <h2>Transaction List</h2>
                <div class="table-controls">
                    <input type="text" id="search" placeholder="Search...">
                    <select id="date-type">
                        <option value="borrow_time">Borrowed Date</option>
                        <option value="due_date">Due Date</option>
                        <option value="return_time">Returned Date</option>
                    </select>
                    <input type="date" id="start-date">
                    <input type="date" id="end-date">
                    <button onclick="filterByDate()">Filter</button>
                    <button onclick="exportTableToExcel('transactionTable', 'transactions')">Export to Excel</button>
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
                                <th>Book Name</th>
                                <th>ISBN</th>
                                <th>Author</th>
                                <th>Borrow Time</th>
                                <th>Due Date</th>
                                <th>Return Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <!-- DATABASE CONNECTION-->
                        <tbody>
                            <?php
                            $result = $conn->query($query);
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['student_number']}</td>
                                        <td>{$row['su_last_name']}</td>
                                        <td>{$row['su_first_name']}</td>
                                        <td>{$row['su_college']}</td>
                                        <td>{$row['su_course']}</td>
                                        <td>{$row['su_year_level']}</td>
                                        <td>{$row['b_book_name']}</td>
                                        <td>{$row['b_isbn']}</td>
                                        <td>{$row['b_author']}</td>
                                        <td>{$row['borrow_time']}</td>
                                        <td>{$row['due_date']}</td>
                                        <td>{$row['return_time']}</td>
                                        <td>{$row['stat']}</td>
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
        let currentPage = 1;
        const rowsPerPage = 10;

        function displayRows() {
            const table = document.getElementById('transactionTable');
            const rows = table.getElementsByTagName('tr');
            const totalRows = rows.length - 1; // Exclude the header row
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            
            // Calculate start and end row indexes for the current page
            const startRow = (currentPage - 1) * rowsPerPage + 1; // +1 to skip the header
            const endRow = Math.min(startRow + rowsPerPage - 1, totalRows);
            
            // Hide all rows and display only the ones for the current page
            for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header
                rows[i].style.display = (i >= startRow && i <= endRow) ? '' : 'none';
            }

            // Update pagination controls
            const pageNumbersContainer = document.getElementById('pageNumbers');
            pageNumbersContainer.innerHTML = ''; // Clear existing page numbers

            for (let i = 1; i <= totalPages; i++) {
                const pageNumber = document.createElement('div');
                pageNumber.className = 'page-number' + (i === currentPage ? ' active' : '');
                pageNumber.textContent = i;
                pageNumber.onclick = () => {
                    currentPage = i;
                    displayRows();
                };
                pageNumbersContainer.appendChild(pageNumber);
            }

            document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
        }

        // Initial call to display rows on page load
        displayRows();
    </script>
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
    

</body>
</html>
