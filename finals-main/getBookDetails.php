<?php
include 'db.php';  // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $book_name = $_POST['book_name'] ?? '';
    $author = $_POST['author'] ?? '';
    $isbn = $_POST['isbn'] ?? '';
    $year_published = $_POST['year_published'] ?? '';
    $action = $_POST['action'] ?? '';

    // Check for duplicate ISBN or book name only if adding a new book
    if ($action == 'add') {
        // Check if ISBN already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM books WHERE isbn = ?");
        $stmt->bind_param('s', $isbn);
        $stmt->execute();
        $stmt->bind_result($isbn_count);
        $stmt->fetch();
        $stmt->close();

        // Check if book name already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM books WHERE book_name = ?");
        $stmt->bind_param('s', $book_name);
        $stmt->execute();
        $stmt->bind_result($name_count);
        $stmt->fetch();
        $stmt->close();

        // Display an error message if either ISBN or book name exists
        if ($isbn_count > 0) {
            echo "<script>alert('Error: This ISBN already exists. Please use a different ISBN.'); window.location.href = 'list_of_books.php';</script>";
            exit();
        }

        if ($name_count > 0) {
            echo "<script>alert('Error: This book name already exists. Please use a different name.'); window.location.href = 'list_of_books.php';</script>";
            exit();
        }
    }

    // Handle file upload
    $photo = 'no-pic.jpg';  // Default photo
    if (isset($_FILES['book_photo']) && $_FILES['book_photo']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $photo = basename($_FILES['book_photo']['name']);
        $target_file = $upload_dir . $photo;

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['book_photo']['tmp_name'], $target_file)) {
            echo "<script>alert('Error uploading file.'); window.location.href = 'list_of_books.php';</script>";
            exit();
        }
    }

    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT INTO books (book_name, author, isbn, year_published, photo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssis', $book_name, $author, $isbn, $year_published, $photo);
        $success_message = 'Book added successfully.';
    } elseif ($action == 'edit') {
        $stmt = $conn->prepare("UPDATE books SET book_name = ?, author = ?, isbn = ?, year_published = ?, photo = ? WHERE id = ?");
        $stmt->bind_param('sssisi', $book_name, $author, $isbn, $year_published, $photo, $id);
        $success_message = 'Book updated successfully.';
    } elseif ($action == 'delete') {
        // Fetch the current photo filename before deletion
        $stmt = $conn->prepare("SELECT photo FROM books WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($photo);
        $stmt->fetch();
        $stmt->close();

        // Delete the book record
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            // Delete the photo from the uploads directory
            if ($photo && $photo !== 'no-pic.jpg') {
                $file_path = 'uploads/' . $photo;
                if (file_exists($file_path)) {
                    unlink($file_path); // Remove the file
                }
            }
            echo "<script>alert('Book deleted successfully.'); window.location.href = 'list_of_books.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error deleting book.'); window.location.href = 'list_of_books.php';</script>";
            exit();
        }
    }

    if ($stmt->execute()) {
        echo "<script>alert('$success_message'); window.location.href = 'list_of_books.php';</script>";
    } else {
        echo "<script>alert('Error processing request.'); window.location.href = 'list_of_books.php';</script>";
    }

    $stmt->close();
    $conn->close();

} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Fetch book details by ISBN for scanning functionality
    $isbn = $_GET['isbn'] ?? '';

    // Check if ISBN is provided
    if (!empty($isbn)) {
        // Prepare the SQL statement to fetch book details by ISBN
        $stmt = $conn->prepare("SELECT book_name, author, isbn, year_published FROM books WHERE isbn = ?");
        $stmt->bind_param('s', $isbn);
        $stmt->execute();
        $stmt->bind_result($book_name, $author, $isbn, $year_published);
        $stmt->fetch();
        $stmt->close();

        // Check if a book was found
        if (!empty($book_name)) {
            // Return the book details as a JSON response
            echo json_encode([
                'success' => true,
                'book_name' => $book_name,
                'author' => $author,
                'isbn' => $isbn,
                'year_published' => $year_published
            ]);
        } else {
            // No book found with the given ISBN
            echo json_encode(['success' => false, 'message' => 'Book not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No ISBN provided']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
