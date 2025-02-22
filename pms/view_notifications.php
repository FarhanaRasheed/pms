<?php
// Include the database connection
include 'db.php';
session_start();

// Check if the student is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: student_login.php");
    exit;
}

// Get the logged-in student's ID from the session
$student_id = $_SESSION["id"];

// Fetch notifications for the student
$sql = "
    SELECT c.name AS company_name, n.message, n.test_date, n.interview_level, n.job_details, n.created_at
    FROM notifications n
    JOIN companies c ON n.company_id = c.id
    WHERE n.student_id = ?
    ORDER BY n.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .notification {
            background-color: #e0f7fa;
            border: 1px solid #4dd0e1;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .date {
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <h2>Your Notifications</h2>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='notification'>";
            echo "<p><strong>Company:</strong> " . htmlspecialchars($row['company_name']) . "</p>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($row['message']) . "</p>";
            if (!empty($row['test_date'])) {
                echo "<p><strong>Test Date:</strong> " . htmlspecialchars($row['test_date']) . "</p>";
            }
            if (!empty($row['interview_level'])) {
                echo "<p><strong>Interview Level:</strong> " . htmlspecialchars($row['interview_level']) . "</p>";
            }
            if (!empty($row['job_details'])) {
                echo "<p><strong>Job Details:</strong> " . htmlspecialchars($row['job_details']) . "</p>";
            }
            echo "<p class='date'>Received on: " . htmlspecialchars($row['created_at']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No new notifications.</p>";
    }

    $stmt->close();
    ?>
</body>
</html>
