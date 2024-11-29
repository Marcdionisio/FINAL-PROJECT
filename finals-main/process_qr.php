<?php
include 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'] ?? '';

    // Validate user_id
    if (empty($user_id)) {
        header("Location: student_monitoring.php?alert=error&message=Incomplete details, please try again.");
        exit();
    }

    // Check if the user exists in stud_users
    $stmt = $conn->prepare("SELECT user_id FROM stud_users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->store_result();

    // If user does not exist, redirect with an error message
    if ($stmt->num_rows === 0) {
        header("Location: student_monitoring.php?alert=error&message=Invalid Student Number.");
        exit();
    }

    // If user exists, continue with the existing logic
    $stmt->close();

    // Check if the student is currently "IN" or "OUT"
    $stmt = $conn->prepare("SELECT lm.id, lm.status FROM library_monitoring lm WHERE lm.user_id = ? ORDER BY lm.created_at DESC LIMIT 1");
    $stmt->bind_param('i', $user_id);
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
                header("Location: student_monitoring.php?alert=success&message=Exit recorded successfully.");
            } else {
                header("Location: student_monitoring.php?alert=error&message=Error updating exit time.");
            }
            exit();
        } else {
            // If the student is "OUT", create a new entry and mark them as "IN"
            $stmt = $conn->prepare("INSERT INTO library_monitoring (user_id, entry_time, status) VALUES (?, NOW(), 'IN')");
            $stmt->bind_param('i', $user_id);
            if ($stmt->execute()) {
                header("Location: student_monitoring.php?alert=success&message=Entry recorded successfully.");
            } else {
                header("Location: student_monitoring.php?alert=error&message=Error recording entry.");
            }
            exit();
        }
    } else {
        // If no prior records, assume student is entering the library for the first time
        $stmt = $conn->prepare("INSERT INTO library_monitoring (user_id, entry_time, status) VALUES (?, NOW(), 'IN')");
        $stmt->bind_param('i', $user_id);
        if ($stmt->execute()) {
            header("Location: student_monitoring.php?alert=success&message=Entry recorded successfully.");
        } else {
            header("Location: student_monitoring.php?alert=error&message=Error recording entry.");
        }
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: student_monitoring.php?alert=error&message=Invalid request.");
    exit();
}
?>
