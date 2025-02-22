<?php
// Include the session file to check if the user is logged in
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Include your database connection file
include "db.php"; // Ensure you have your database connection details here

// Fetch all students from the database
$sql = "SELECT student_id, name, email, phone_number, course, gender, birth_date, placement_status FROM students";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS for styling -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0; /* Reset default margin */
            padding: 0; /* Reset default padding */
        }
        .container {
            width: 80%;
            margin: 20px auto; /* Center the container and add top margin */
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: grey; /* Changed heading background color to grey */
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .back-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: grey;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: black;
        }
        h1 {
            color: grey; /* Changed heading color to grey */
        }
    </style>
</head>
<body>

<div class="container">
    <h1>View Students</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Course</th>
            <th>Gender</th>
            <th>Birth Date</th>
            <th>Placement Status</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                echo "<td>" . htmlspecialchars($row['course']) . "</td>";
                echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                echo "<td>" . htmlspecialchars($row['birth_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['placement_status'] ? 'Placed' : 'Not Placed') . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No students found.</td></tr>";
        }
        ?>
    </table>

    <a class="back-button" href="admin_dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
