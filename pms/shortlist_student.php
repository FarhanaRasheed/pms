<?php
// Include the database connection
include 'db.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin_login.php"); // Redirect to admin login if not logged in
    exit;
}

// Fetch companies from the database
$sql_companies = "SELECT id, name FROM companies";
$result_companies = $conn->query($sql_companies);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shortlist Students</title>
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
            background-color: #808080; /* Grey header */
            color: white;
        }
        .shortlist-btn {
            background-color: #696969; /* Dark grey button */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .success-message {
            color: green;
            text-align: center;
            margin-top: 20px;
        }

        /* Additional Styles */
        label {
            font-weight: bold;
        }
        select {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%; /* Make the select box full width */
            max-width: 300px; /* Limit the width */
        }
        button[type="submit"] {
            background-color: #808080; /* Match the header color for consistency */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.3s ease; /* Smooth transition for hover effect */
        }
        button[type="submit"]:hover {
            background-color: #696969; /* Darker shade for hover */
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2; /* Alternate row colors for better readability */
        }
        table tr:hover {
            background-color: #d9d9d9; /* Light grey highlight on row hover */
        }
    </style>
</head>
<body>
    <h2>Shortlist Students</h2>

    <!-- Display success message if available -->
    <?php
    if (isset($_SESSION['success_message'])) {
        echo "<div class='success-message'>" . $_SESSION['success_message'] . "</div>";
        unset($_SESSION['success_message']); // Clear the message after displaying
    }
    ?>

    <form method="post" action="">
        <label for="company">Select Company:</label>
        <select id="company" name="company_id" required>
            <option value="">Select a company</option>
            <?php
            // Populate the company dropdown
            while ($row = $result_companies->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
            }
            ?>
        </select>
        <button type="submit">View Applications</button>
    </form>

    <?php
    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['company_id'])) {
        $company_id = $_POST['company_id'];

        // Fetch students who applied for the selected company but haven't been shortlisted yet
        $sql_applications = "
            SELECT s.student_id, s.name, s.email
            FROM applications a
            JOIN students s ON a.student_id = s.student_id
            WHERE a.company_id = ? AND a.is_shortlisted = 0
        ";

        if ($stmt = $conn->prepare($sql_applications)) {
            $stmt->bind_param("i", $company_id);
            $stmt->execute();
            $result_applications = $stmt->get_result();

            if ($result_applications->num_rows > 0) {
                echo "<h3>Applications for Company ID: " . htmlspecialchars($company_id) . "</h3>";
                echo "<form method='post' action='send_notifications.php'>"; // Action to send notifications
                echo "<table>";
                echo "<tr><th>Select</th><th>Student ID</th><th>Name</th><th>Email</th></tr>";

                while ($row = $result_applications->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='selected_students[]' value='" . htmlspecialchars($row['student_id']) . "'></td>";
                    echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<input type='hidden' name='company_id' value='" . htmlspecialchars($company_id) . "'>";
                echo "<button type='submit' class='shortlist-btn'>Shortlist Selected Students</button>";
                echo "</form>";
            } else {
                echo "<p>No applications found for this company.</p>";
            }
            $stmt->close();
        } else {
            echo "SQL prepare failed: " . $conn->error;
        }
    }
    ?>
</body>
</html>
