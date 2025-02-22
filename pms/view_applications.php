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
    <title>Student Applications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .container {
            max-width: 800px;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: auto;
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
            background-color: #cccccc; /* Grey header */
            color: black; /* Black text for contrast */
        }
        button {
            background-color: #cccccc; /* Grey button */
            color: black; /* Black text for contrast */
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #b3b3b3; /* Darker grey on hover */
        }
        label {
            margin-top: 10px;
            display: block;
            font-weight: bold;
        }
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Applications</h2>
        
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

            // Fetch students who applied for the selected company
            $sql_applications = "
                SELECT s.student_id, s.name, s.phone_number, s.email, s.course, s.gender, s.birth_date
                FROM applications a
                JOIN students s ON a.student_id = s.student_id
                WHERE a.company_id = ?
            ";

            if ($stmt = $conn->prepare($sql_applications)) {
                $stmt->bind_param("i", $company_id);
                $stmt->execute();
                $result_applications = $stmt->get_result();

                if ($result_applications->num_rows > 0) {
                    echo "<h3>Applications for Company ID: " . htmlspecialchars($company_id) . "</h3>";
                    echo "<table>";
                    echo "<tr><th>Student ID</th><th>Name</th><th>Phone Number</th><th>Email</th><th>Course</th><th>Gender</th><th>Birth Date</th></tr>";

                    while ($row = $result_applications->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['course']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['birth_date']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No applications found for this company.</p>";
                }
                $stmt->close();
            } else {
                echo "SQL prepare failed: " . $conn->error;
            }
        }
        ?>
    </div>
</body>
</html>
