<?php
include 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_number = $_POST['student_number'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $course = $_POST['course'] ?? '';
    $year_level = $_POST['year_level'] ?? '';
    $college = $_POST['college'] ?? '';

    if (empty($student_number) || empty($last_name) || empty($first_name) || empty($course) ||
        empty($year_level) || empty($college)) {
        header("Location: QR_SCAN_ONLY.php?alert=error&message=Incomplete details, please try again.");
        exit();
    }

    // Check if the student is currently "IN" or "OUT"
    $stmt = $conn->prepare("SELECT id, status FROM library_monitoring WHERE student_number = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param('s', $student_number);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch the last status of the student
        $stmt->bind_result($id, $status);
        $stmt->fetch();

        // If the student is "IN", mark them as "OUT" and update the exit_time
        if ($status == 'IN') {
            $stmt = $conn->prepare("UPDATE library_monitoring SET exit_time = NOW(), status = 'OUT' WHERE id = ?");
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                header("Location: QR_SCAN_ONLY.php?alert=success&message=Exit recorded successfully.");
            } else {
                header("Location: QR_SCAN_ONLY.php?alert=error&message=Error updating exit time.");
            }
            exit();
        } else {
            // If the student is "OUT", create a new entry and mark them as "IN"
            $stmt = $conn->prepare("INSERT INTO library_monitoring (student_number, last_name, first_name, college, course, year_level, entry_time, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'IN')");
            $stmt->bind_param('ssssss', $student_number, $last_name, $first_name, $college, $course, $year_level);
            if ($stmt->execute()) {
                header("Location: QR_SCAN_ONLY.php?alert=success&message=Entry recorded successfully.");
            } else {
                header("Location: QR_SCAN_ONLY.php?alert=error&message=Error recording entry.");
            }
            exit();
        }
    } else {
        // If no prior records, assume student is entering the library for the first time
        $stmt = $conn->prepare("INSERT INTO library_monitoring (student_number, last_name, first_name, course, year_level, college, entry_time, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'IN')");
        $stmt->bind_param('ssssss', $student_number, $last_name, $first_name, $course, $year_level, $college);
        if ($stmt->execute()) {
            header("Location: QR_SCAN_ONLY.php?alert=success&message=Entry recorded successfully.");
        } else {
            header("Location: QR_SCAN_ONLY.php?alert=error&message=Error recording entry.");
        }
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: QR_SCAN_ONLY.php?alert=error&message=Invalid request.");
    exit();
}
?>
