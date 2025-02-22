<?php
// Include the database connection
include 'db.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin_login.php"); // Redirect to admin login if not logged in
    exit;
}

// Fetch shortlisted students along with company details
$sql = "
    SELECT s.student_id, s.name, s.email, c.name AS company_name
    FROM applications a
    JOIN students s ON a.student_id = s.student_id
    JOIN companies c ON a.company_id = c.id
    WHERE a.is_shortlisted = 1
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Shortlisted Students</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: grey;
            color: white;
        }
        tr:hover {
            background-color:  dark grey; /* Highlight row on hover */
        }
        .container {
            max-width: 800px;
            margin: auto; /* Center the table */
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Shortlisted Students</h2>

        <?php
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Student ID</th><th>Name</th><th>Email</th><th>Company</th></tr>";

            // Display each shortlisted student and their associated company
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['company_name']) . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No shortlisted students found.</p>";
        }
        ?>
    </div>
</body>
</html>
