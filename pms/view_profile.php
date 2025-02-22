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

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: grey;
            transition: box-shadow 0.3s ease;
        }
        .container:hover {
            box-shadow: brey;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .profile-info {
            margin: 15px 0;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fafafa;
            line-height: 1.6; /* Improved readability */
        }
        .profile-info strong {
            color: #555; /* Darker color for labels */
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: grey;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: black; /* Darker blue on hover */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Profile</h2>
    <div class="profile-info">
        <strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?><br>
        <strong>Phone Number:</strong> <?php echo htmlspecialchars($student['phone_number']); ?><br>
        <strong>Gender:</strong> <?php echo htmlspecialchars($student['gender']); ?><br>
        <strong>Birth Date:</strong> <?php echo htmlspecialchars($student['birth_date']); ?><br>
        <strong>10th Score:</strong> <?php echo htmlspecialchars($student['score_sheet_10th']); ?><br>
        <strong>12th Score:</strong> <?php echo htmlspecialchars($student['score_sheet_12th']); ?><br>
        <strong>Degree Certificate:</strong> <?php echo htmlspecialchars($student['degree_certificate']); ?><br>
        <strong>Masters Certificate:</strong> <?php echo htmlspecialchars($student['masters_certificate']); ?><br>
        <strong>Profile Photo:</strong> <img src="<?php echo htmlspecialchars($student['profile_photo']); ?>" alt="Profile Photo" style="max-width: 100%; height: auto; border-radius: 5px;"><br>
        <strong>CV:</strong> <a href="<?php echo htmlspecialchars($student['cv']); ?>" target="_blank">Download CV</a><br>
        <strong>Created At:</strong> <?php echo htmlspecialchars($student['created_at']); ?><br>
        <strong>Placement Status:</strong> <?php echo htmlspecialchars($student['placement_status']); ?><br>
    </div>
    <a href="update_profile.php">Edit Profile</a>
</div>

</body>
</html>
