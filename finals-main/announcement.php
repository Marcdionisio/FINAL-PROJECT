<?php
include 'session_check.php';
checkSession();
include 'db.php';

// Fetch all active announcements for display
$activeResult = $conn->query("SELECT id, title, content, date_created FROM announcements WHERE is_active = 1");

// Fetch all archived announcements for display
$archivedResult = $conn->query("SELECT id, title, content, date_created FROM announcements WHERE is_active = 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMUN - Announcement Management</title>
    <link rel="icon" type="image/x-icon" href="plmun.ico">
    <link rel="stylesheet" href="announcement.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="alerts.js" defer></script>
    <script>
        function openEditModal(id) {
            // Fetch the current announcement details
            const title = document.getElementById(`title-${id}`).innerText;
            const content = document.getElementById(`content-${id}`).innerText;

            // Populate the modal with existing data
            document.getElementById('modal-announcement-title').value = title;
            document.getElementById('modal-announcement-content').value = content;
            document.getElementById('edit-announcement-id').value = id;

            // Show the modal
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="plmun.png" alt="PLMUN Logo">
            <h2>PLMUN Library Management System</h2>
        </div>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="active"><a href="#"><i class="fas fa-bullhorn"></i> Announcements</a></li>
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
            <li class="logout"><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="container">
        <div class="announcement-container">
            <h1>Announcements</h1>
            <form id="announcement-form" action="announcement_process.php" method="POST">
                <input type="hidden" id="edit-announcement-id" name="edit_announcement_id">
                <div class="form-group">
                    <label for="announcement-title">Title:</label>
                    <input type="text" id="announcement-title" name="announcement_title" required>
                </div>
                <div class="form-group">
                    <label for="announcement-content">Content:</label>
                    <textarea id="announcement-content" name="announcement_content" rows="4" required></textarea>
                </div>
                <button type="submit">Submit Announcement</button>
            </form>

            <!-- ANNOUNCEMENT LIST -->
            <div class="announcement-list">
                <h2>Recent Announcements</h2>
                <div class="table-controls">
                    <input type="text" id="search" placeholder="Search...">
                </div>
                <div class="table-container">
                    <table id="announcementTable">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $activeResult->fetch_assoc()) { ?>
                                <tr>
                                    <td id='title-<?php echo $row['id']; ?>'><?php echo $row['title']; ?></td>
                                    <td id='content-<?php echo $row['id']; ?>'><?php echo substr($row['content'], 0, 20); ?>...</td>
                                    <td><?php echo $row['date_created']; ?></td>
                                    <td>
                                        <button onclick="openEditModal('<?php echo $row['id']; ?>')">Edit</button>
                                        <button onclick="archiveAnnouncement('<?php echo $row['id']; ?>')">Archive</button>
                                        <button onclick="deleteAnnouncement('<?php echo $row['id']; ?>')">Delete</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ARCHIVED ANNOUNCEMENTS LIST -->
            <div class="archived-announcements">
                <h2>Archived Announcements</h2>
                <div class="table-container">
                    <table id="archivedAnnouncementTable">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $archivedResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row['title']; ?></td>
                                    <td><?php echo substr($row['content'], 0, 20); ?>...</td>
                                    <td><?php echo $row['date_created']; ?></td>
                                    <td>
                                        <button onclick="unarchiveAnnouncement('<?php echo $row['id']; ?>')">Unarchive</button>
                                        <button onclick="deleteAnnouncement('<?php echo $row['id']; ?>')">Delete</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Announcement</h2>
            <form action="announcement_process.php" method="POST">
                <input type="hidden" id="edit-announcement-id" name="edit_announcement_id">
                <div class="form-group">
                    <label for="modal-announcement-title">Title:</label>
                    <input type="text" id="modal-announcement-title" name="announcement_title" required>
                </div>
                <div class="form-group">
                    <label for="modal-announcement-content">Content:</label>
                    <textarea id="modal-announcement-content" name="announcement_content" rows="4" required></textarea>
                </div>
                <button type="submit">Update Announcement</button>
            </form>
        </div>
    </div>
<script>
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
                case 'info':
                    iconType = 'info';
                    break;
                default:
                    iconType = 'info'; // Default icon
                    break;
            }

            // Show alert if parameters are present
            if (alertType && alertMessage) {
                showAlertWithRedirect(alertMessage, '', iconType, redirectUrl);
            }
        
</script>

    <script src="announcement.js" defer></script>
</body>
</html>
