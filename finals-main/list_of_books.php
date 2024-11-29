<?php
include 'session_check.php';
checkSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMUN - List of Books</title>
    <link rel="icon" type="image/x-icon" href="plmun.ico">
    <link rel="stylesheet" href="main.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="navbar">
        <div class="logo-container">
            <img src="plmun.png" alt="PLMUN Logo">
            <h2>PLMUN Library Management System</h2>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
            <div class="dropdown">
                <a href="javascript:void(0)" class="dropbtn"><i class="fas fa-list"></i> Books <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="list_of_books.php">Book List</a>
                    <a href="generate_barcode.php">Generate Barcode</a>
                </div>
            </div>
            <li><a href="student_monitoring.php"><i class="fas fa-user-graduate"></i> Student Monitoring</a></li>
            <li><a href="borrowed_returned_books.php"><i class="fas fa-book"></i> Borrowed-Returned Books</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Manage Users</a></li>
            <li><a class="logout" href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="container">
        <div class="left-section">
            <h2>Add/Edit Book</h2>
            <form action="getBookDetails.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">
                <div class="input-container">
                    <label for="book_name">Book Name:</label>
                    <input type="text" name="book_name" id="book_name" required>
                </div>
                <div class="input-container">
                    <label for="author">Author:</label>
                    <input type="text" name="author" id="author" required>
                </div>
                <div class="input-container">
                    <label for="isbn">ISBN:</label>
                    <input type="text" name="isbn" id="isbn" required>
                </div>
                <div class="input-container">
                    <label for="year_published">Year Published:</label>
                    <input type="number" name="year_published" id="year_published" required>
                </div>
                <div class="input-container">
                    <label for="book_photo">Book Photo:</label>
                    <input type="file" name="book_photo" id="book_photo" accept="image/*">
                </div>
                <button type="submit" name="action" value="add">Add Book</button>
                <button type="submit" name="action" value="edit">Edit Book</button>
            </form>
        </div>
        <div class="right-section">
            <h2>Book List</h2>

            <!-- Search Bar -->
            <div class="search-bar-container">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search..." class="search-bar">
            </div>

            <div class="table-container">
                <table id="bookTable">
                    <thead>
                        <tr>
                            <th>Book Name</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Year Published</th>
                            <th>Photo</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include 'db.php';
                        $result = $conn->query("SELECT * FROM books");
                        while ($row = $result->fetch_assoc()) {
                            $photo = $row['photo'] ? $row['photo'] : 'no-pic.jpg';
                            echo "<tr>
                                    <td>{$row['book_name']}</td>
                                    <td>{$row['author']}</td>
                                    <td>{$row['isbn']}</td>
                                    <td>{$row['year_published']}</td>
                                    <td><img src='uploads/{$photo}' alt='Book Photo' width='50'></td>
                                    <td>
                                        <button type='button' onclick=\"editBook({$row['id']}, '{$row['book_name']}', '{$row['author']}', '{$row['isbn']}', {$row['year_published']}, '{$photo}')\">Edit</button>
                                        <button type='button' onclick=\"confirmDelete({$row['id']})\">Delete</button>
                                        <form id='deleteForm{$row['id']}' action='getBookDetails.php' method='post' style='display:none;'>
                                            <input type='hidden' name='id' value='{$row['id']}'>
                                            <input type='hidden' name='action' value='delete'>
                                        </form>
                                    </td>
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
    
    <script>
        let currentPage = 1;
        const rowsPerPage = 10;

        function displayRows() {
            const table = document.getElementById('bookTable');
            const rows = table.getElementsByTagName('tr');
            const totalRows = rows.length - 1; // Exclude the header row
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            
            // Calculate start and end row indexes for the current page
            const startRow = (currentPage - 1) * rowsPerPage + 1; // +1 to skip the header
            const endRow = Math.min(startRow + rowsPerPage - 1, totalRows);
            
            // Hide all rows and display only the ones for the current page
            for (let i = 1; i < rows.length; i++) {
                rows[i].style.display = (i >= startRow && i <= endRow) ? '' : 'none';
            }
            
            // Update pagination information
            const pageInfo = document.getElementById('pageNumbers');
            pageInfo.innerHTML = ``;
            
            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement('button');
                pageButton.className = 'page-number' + (i === currentPage ? ' active' : '');
                pageButton.innerHTML = i;
                pageButton.onclick = () => {
                    currentPage = i;
                    displayRows();
                };
                pageNumbers.appendChild(pageButton);
            }
            document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
        }

        // Call displayRows on page load to show the first set of data
        displayRows();

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var rows = document.querySelectorAll('#bookTable tbody tr');
            rows.forEach(function(row) {
                var cells = row.querySelectorAll('td');
                var match = false;
                cells.forEach(function(cell) {
                    if (cell.textContent.toLowerCase().includes(searchValue)) {
                        match = true;
                    }
                });
                row.style.display = match ? '' : 'none';
            });
        });

        // Confirm Delete
        function confirmDelete(bookId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the corresponding form to delete the book
                    document.getElementById('deleteForm' + bookId).submit();
                }
            });
        }

        // Edit book (this function would populate the form for editing a book)
        function editBook(id, bookName, author, isbn, yearPublished, photo) {
            document.getElementById('id').value = id;
            document.getElementById('book_name').value = bookName;
            document.getElementById('author').value = author;
            document.getElementById('isbn').value = isbn;
            document.getElementById('year_published').value = yearPublished;
        }
    </script>
</body>
</html>
