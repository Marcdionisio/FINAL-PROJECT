<?php
include 'db.php'; // Include database connection
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve POST data
    $student_number = $_POST['student_number'] ?? '';
    $isbn = $_POST['isbn'] ?? '';
    $stat = 'borrowed'; // Default to 'borrowed'

    // Get current time for borrow_time
    $current_time = date('Y-m-d H:i:s'); // Current date and time

    // Set due_date to 5 days from now at exactly 4:00 PM
    $due_date_time = new DateTime($current_time);
    $due_date_time->modify('+5 days')->setTime(16, 0); // Add 5 days and set time to 4:00 PM
    $due_date = $due_date_time->format('Y-m-d H:i:s');

    // Validate input
    if (empty($student_number) || empty($isbn)) {
        header("Location: borrowed_returned_books.php?alert=error&message=Incomplete details, please try again.");
        exit();
    }

    // Fetch the user_id and book_id
    $user_query = $conn->prepare("SELECT user_id FROM stud_users WHERE student_number = ?");
    $user_query->bind_param("s", $student_number);
    $user_query->execute();
    $user_query->bind_result($user_id);
    $user_query->fetch();
    $user_query->close();

    $book_query = $conn->prepare("SELECT id FROM books WHERE isbn = ?");
    $book_query->bind_param("s", $isbn);
    $book_query->execute();
    $book_query->bind_result($book_id);
    $book_query->fetch();
    $book_query->close();

    // Validate if user_id and book_id were found
    if (!$user_id) {
        header("Location: borrowed_returned_books.php?alert=error&message=Invalid student number.");
        exit();
    }
    if (!$book_id) {
        header("Location: borrowed_returned_books.php?alert=error&message=Invalid book ISBN.");
        exit();
    }

    // Check if the book is currently borrowed
    $stmt = $conn->prepare("SELECT id, stat, borrow_time, due_date FROM transactions WHERE book_id = ? ORDER BY borrow_time DESC LIMIT 1");
    $stmt->bind_param('i', $book_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch the last status of the book
        $stmt->bind_result($id, $current_status, $borrow_time, $due_date);
        $stmt->fetch();
        // If the last status was "borrowed", the user is returning the book
        if ($current_status == 'borrowed') {
            $return_time = $current_time;
            $stat = (strtotime($return_time) > strtotime($due_date)) ? 'returned late' : 'returned';
            $stmt = $conn->prepare("UPDATE transactions SET return_time = ?, stat = ? WHERE id = ?");
            $stmt->bind_param('ssi', $return_time, $stat, $id);
            if ($stmt->execute()) {
                header("Location: borrowed_returned_books.php?alert=success&message=Book returned successfully.");
                exit();
            } else {
                header("Location: borrowed_returned_books.php?alert=error&message=Error updating return time.");
                exit();
            }
        } else {
            // If the book is already returned, allow it to be borrowed again
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, book_id, borrow_time, due_date, stat) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('iisss', $user_id, $book_id, $current_time, $due_date, $stat);
            if ($stmt->execute()) {
                header("Location: borrowed_returned_books.php?alert=success&message=Book borrowed successfully.");
                exit();
            } else {
                header("Location: borrowed_returned_books.php?alert=error&message=Error recording book borrowing.");
                exit();
            }
        }
    } else {
        // No previous borrow record, meaning this is a new borrow transaction
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, book_id, borrow_time, due_date, stat) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iisss', $user_id, $book_id, $current_time, $due_date, $stat);
        if ($stmt->execute()) {
            header("Location: borrowed_returned_books.php?alert=success&message=Book borrowed successfully.");
            exit();
        } else {
            header("Location: borrowed_returned_books.php?alert=error&message=Error recording book borrowing.");
            exit();
        }
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: borrowed_returned_books.php?alert=error&message=Invalid request.");
    exit();
}
?>
