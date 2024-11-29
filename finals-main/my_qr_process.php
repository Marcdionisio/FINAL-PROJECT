<?php
include 'db.php'; // Include your database connection file

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = $conn->prepare("SELECT user_id,  student_number, last_name, first_name, college, course, year_level FROM stud_users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    echo json_encode($user_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
} else {
    echo json_encode(["error" => "User not found"]);
}

$query->close();
$conn->close();
?>
