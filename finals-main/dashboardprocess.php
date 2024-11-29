<?php
include 'db.php';

// Fetch total books available
$totalBooksQuery = "SELECT COUNT(id) AS total_books FROM books";
$totalBooksResult = $conn->query($totalBooksQuery);
$totalBooks = $totalBooksResult->fetch_assoc()['total_books'];

// Fetch total borrowed books
$totalBorrowedQuery = "SELECT COUNT(id) AS total_borrowed FROM transactions WHERE stat = 'borrowed'";
$totalBorrowedResult = $conn->query($totalBorrowedQuery);
$totalBorrowed = $totalBorrowedResult->fetch_assoc()['total_borrowed'];

// Fetch total returned books
$totalReturnedQuery = "SELECT COUNT(id) AS total_returned FROM transactions WHERE stat = 'returned'";
$totalReturnedResult = $conn->query($totalReturnedQuery);
$totalReturned = $totalReturnedResult->fetch_assoc()['total_returned'];

// Fetch total returned late books
$totalReturnedLateQuery = "SELECT COUNT(id) AS total_returned_late FROM transactions WHERE stat = 'returned_late'";
$totalReturnedLateResult = $conn->query($totalReturnedLateQuery);
$totalReturnedLate = $totalReturnedLateResult->fetch_assoc()['total_returned_late'];

// Fetch total due books
$totalDueQuery = "SELECT COUNT(id) AS total_due FROM transactions WHERE stat = 'borrowed' AND due_date < NOW()";
$totalDueResult = $conn->query($totalDueQuery);
$totalDue = $totalDueResult->fetch_assoc()['total_due'];

// Fetch historical data for books available by month
$historicalDataQuery = "
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(id) AS total_books
    FROM books
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC
";
$historicalDataResult = $conn->query($historicalDataQuery);

if (!$historicalDataResult) {
    die("Query failed: " . $conn->error);
}

$historicalData = [];
while ($row = $historicalDataResult->fetch_assoc()) {
    $historicalData[] = $row;
}

$data = [
    'total_books' => $totalBooks,
    'total_borrowed' => $totalBorrowed,
    'total_returned' => $totalReturned,
    'total_returned_late' => $totalReturnedLate,
    'total_due' => $totalDue,
    'historical_data' => $historicalData
];

echo json_encode($data);

$conn->close();
?>
