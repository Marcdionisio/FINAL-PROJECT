<?php
include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $book_name = $_POST['book_name'] ?? '';
    $author = $_POST['author'] ?? '';
    $isbn = $_POST['isbn'] ?? '';
    $year_published = $_POST['year_published'] ?? '';
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT INTO books (book_name, author, isbn, year_published) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $book_name, $author, $isbn, $year_published);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Book added successfully.'); window.location.href = 'list_of_books.php';</script>";
    } elseif ($action == 'edit') {
        $stmt = $conn->prepare("UPDATE books SET book_name = ?, author = ?, isbn = ?, year_published = ? WHERE id = ?");
        $stmt->bind_param('sssii', $book_name, $author, $isbn, $year_published, $id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Book updated successfully.'); window.location.href = 'list_of_books.php';</script>";
    } elseif ($action == 'delete') {
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Book deleted successfully.'); window.location.href = 'list_of_books.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'list_of_books.php';</script>";
}

$conn->close();
?>
