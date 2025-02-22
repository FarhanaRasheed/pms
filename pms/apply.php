<?php
// Include the database connection
include 'db.php';
session_start();

// Check if the student is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: student_login.php");
    exit;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_SESSION["id"]; // Use `id` from session as student_id
    $company_id = $_POST["company_id"];

    // Check if company_id exists in companies table
    $check_sql = "SELECT id FROM companies WHERE id = ?";
    if ($check_stmt = $conn->prepare($check_sql)) {
        $check_stmt->bind_param("i", $company_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows === 0) {
            echo "Error: Company ID does not exist.";
        } else {
            // Prepare an insert statement
            $sql = "INSERT INTO applications (student_id, company_id) VALUES (?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ii", $student_id, $company_id);

                // Execute the statement
                if ($stmt->execute()) {
                    echo "Application submitted successfully.";
                } else {
                    echo "Something went wrong. Please try again later.";
                }

                // Close the statement
                $stmt->close();
            } else {
                echo "SQL prepare failed: " . $conn->error;
            }
        }
        $check_stmt->close();
    } else {
        echo "SQL prepare failed: " . $conn->error;
    }
}
?>
