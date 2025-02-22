<?php
// Include the database connection
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_students'], $_POST['company_id'])) {
    $selected_students = $_POST['selected_students'];
    $company_id = $_POST['company_id'];

    foreach ($selected_students as $student_id) {
        // Update the applications table
        $sql = "UPDATE applications SET is_shortlisted = 1 WHERE student_id = ? AND company_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $student_id, $company_id);
            $stmt->execute();
            $stmt->close();
        }

        // Insert notification into the notifications table
        $message = "You have been shortlisted for Company ID: $company_id.";
        $notification_sql = "INSERT INTO notifications (student_id, company_id, message) VALUES (?, ?, ?)";
        if ($notification_stmt = $conn->prepare($notification_sql)) {
            $notification_stmt->bind_param("iis", $student_id, $company_id, $message);
            $notification_stmt->execute();
            $notification_stmt->close();
        }
    }

    // Set a session variable to display success message
    $_SESSION['success_message'] = "Students have been successfully shortlisted.";

    // Redirect back to shortlist_student.php
    header("location: shortlist_student.php");
    exit;
}
?>
