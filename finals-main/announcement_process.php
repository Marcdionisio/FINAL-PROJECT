<?php
include 'db.php';

// Handle creating or updating an announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and collect form data
    $title = $_POST['announcement_title'];
    $content = $_POST['announcement_content'];

    // Check if we are editing an existing announcement
    if (isset($_POST['edit_announcement_id']) && !empty($_POST['edit_announcement_id'])) {
        $announcement_id = $_POST['edit_announcement_id'];

        // Update the existing announcement
        $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $announcement_id);
        
        if ($stmt->execute()) {
            header("Location: announcement.php?alert=success&message=Announcement updated successfully&redirect=announcement.php");
        } else {
            header("Location: announcement.php?alert=danger&message=Error updating announcement: " . $stmt->error . "&redirect=announcement.php");
        }
    } else {
        // Insert a new announcement
        $stmt = $conn->prepare("INSERT INTO announcements (title, content, date_created, is_active) VALUES (?, ?, NOW(), 1)");
        $stmt->bind_param("ss", $title, $content);
        
        if ($stmt->execute()) {
            header("Location: announcement.php?alert=success&message=Announcement created successfully&redirect=announcement.php");
        } else {
            header("Location: announcement.php?alert=danger&message=Error creating announcement: " . $stmt->error . "&redirect=announcement.php");
        }
    }

    $stmt->close();
    exit();
}

// Handle archiving an announcement
if (isset($_GET['action']) && $_GET['action'] === 'archive' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("UPDATE announcements SET is_active = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: announcement.php?alert=success&message=Announcement archived successfully&redirect=announcement.php");
    exit();
}

// Handle restoring an announcement
if (isset($_GET['action']) && $_GET['action'] === 'restore' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("UPDATE announcements SET is_active = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: announcement.php?alert=success&message=Announcement restored successfully&redirect=announcement.php");
    exit();
}

// Handle deleting an announcement
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: announcement.php?alert=success&message=Announcement deleted successfully&redirect=announcement.php");
    exit();
}

// Redirect for invalid requests
header("Location: announcement.php?alert=danger&message=Invalid request&redirect=announcement.php");
exit();
?>
