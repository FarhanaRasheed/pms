<?php
require 'config.php'; // Ensure this file exists and is properly configured

// Start session and check if the user is logged in
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

// Get the student's email from the session
$email = $_SESSION['email'];

// Prepare the SQL statement to select the student's current details
$stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
$stmt->bind_param("s", $email); // Bind the email parameter
$stmt->execute();
$result = $stmt->get_result();

// Check if the student was found
if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "No student found.";
    exit;
}

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $score_sheet_10th = $_POST['score_sheet_10th'];
    $score_sheet_12th = $_POST['score_sheet_12th'];
    $placement_status = $_POST['placement_status'];

    // Prepare the SQL statement to update the student's details
    $update_stmt = $conn->prepare("UPDATE students SET name=?, phone_number=?, gender=?, birth_date=?, score_sheet_10th=?, score_sheet_12th=?, placement_status=? WHERE email=?");
    $update_stmt->bind_param("ssssddss", $name, $phone_number, $gender, $birth_date, $score_sheet_10th, $score_sheet_12th, $placement_status, $email);
    
    if ($update_stmt->execute()) {
        echo "Profile updated successfully!";
        // Optionally redirect to view profile page
        header('Location: view_profile.php');
        exit;
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4; /* Light grey background */
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333; /* Dark grey color for the heading */
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #555; /* Medium grey color for labels */
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc; /* Light grey border */
            border-radius: 4px;
            background-color: #f9f9f9; /* Very light grey background for inputs */
        }
        .radio-group {
            margin: 10px 0;
        }
        .radio-group label {
            display: inline-block;
            margin-right: 20px;
            color: #555; /* Medium grey color for radio labels */
        }
        input[type="submit"] {
            background-color: #808080; /* Dark grey button */
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease; /* Smooth transition for hover effect */
        }
        input[type="submit"]:hover {
            background-color: #696969; /* Darker grey on hover */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Update Profile</h2>
    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($student['phone_number']); ?>" required>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male" <?php echo ($student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo ($student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
            <option value="Other" <?php echo ($student['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
        </select>

        <label for="birth_date">Birth Date:</label>
        <input type="date" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($student['birth_date']); ?>" required>

        <!-- <label for="score_sheet_10th">10th Score:</label>
        <input type="number" id="score_sheet_10th" name="score_sheet_10th" step="0.01" value="<?php echo htmlspecialchars($student['score_sheet_10th']); ?>" required>

        <label for="score_sheet_12th">12th Score:</label>
        <input type="number" id="score_sheet_12th" name="score_sheet_12th" step="0.01" value="<?php echo htmlspecialchars($student['score_sheet_12th']); ?>" required> -->

        <label>Placement Status:</label>
        <div class="radio-group">
            <label><input type="radio" name="placement_status" value="Placed" <?php echo ($student['placement_status'] == 'Placed') ? 'checked' : ''; ?>> Placed</label>
            <label><input type="radio" name="placement_status" value="Not Placed" <?php echo ($student['placement_status'] == 'Not Placed') ? 'checked' : ''; ?>> Not Placed</label>
        </div>

        <input type="submit" value="Update Profile">
    </form>
</div>

</body>
</html>
