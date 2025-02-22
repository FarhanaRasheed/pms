<?php
include "db.php"; // Database connection

// Fetch unapproved students
$sql = "SELECT student_id, name, email, phone_number, course, gender, birth_date FROM students WHERE is_approved = 0";
$result = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id']; // Get student ID from form submission

    // Approve the student
    $update_sql = "UPDATE students SET is_approved = 1 WHERE student_id = ?";
    if ($stmt = $conn->prepare($update_sql)) {
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();
        header("location: admin_approval.php"); // Redirect after approval
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Approval</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5; /* Light grey background */
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            margin: 0;
        }
        h2 {
            margin-top: 20px;
            color: #444; /* Dark grey for the heading */
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff; /* White background for the table */
            border-radius: 5px;
            overflow: hidden; /* Rounded corners */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd; /* Light grey line */
        }
        th {
            background-color: #f2f2f2; /* Light grey header */
            color: #555; /* Dark grey for header text */
        }
        tr:hover {
            background-color: #f1f1f1; /* Light grey on row hover */
        }
        input[type="submit"] {
            background-color: grey; /* Green button */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color:black; /* Darker green on hover */
        }
        .no-students {
            font-size: 18px;
            color: #555;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Student Approval List</h2>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Course</th>
                <th>Gender</th>
                <th>Birth Date</th>
                <th>Approve</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                    <td><?php echo htmlspecialchars($row['birth_date']); ?></td>
                    <td>
                        <form action="admin_approval.php" method="post">
                            <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
                            <input type="submit" value="Approve">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="no-students">No students are waiting for approval.</p>
    <?php endif; ?>
</body>
</html>
