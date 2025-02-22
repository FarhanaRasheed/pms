<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); 
    exit;
}

$admin_email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Full Background and Text Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #e0e0e0;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        /* Dashboard Container */
        .dashboard-container {
            width: 80%;
            max-width: 500px;
            padding: 20px;
            background-color: #f7f7f7;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        /* Header Styles */
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Welcome Message */
        .welcome-msg p {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }

        /* Menu Links / Buttons */
        .dashboard-menu {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        /* Button Styles */
        .dashboard-menu a {
            padding: 10px 15px;
            background-color: #d3d3d3;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
            width: 160px;
            text-align: center;
            font-size: 14px;
        }

        /* Hover and Active Effects */
        .dashboard-menu a:hover {
            background-color: #b0b0b0;
            transform: scale(1.05);
        }

        .dashboard-menu a:active {
            background-color: #a0a0a0;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <h1>Welcome to the Admin Dashboard</h1>

        <div class="welcome-msg">
            <p>Hello, <?php echo htmlspecialchars($admin_email); ?>! Welcome to your dashboard.</p>
        </div>

        <div class="dashboard-menu">
            <a href="admin_approval.php">Authenticate Student</a>
            <a href="manage_students.php">View Students</a>
            <a href="admin_add_paper.php">Add placement papers</a>
            <a href="admin_publish_company.php">Publish Company</a>
            <a href="admin_add_news.php">Add News</a>
            <a href="shortlist_student.php">Shortlist Students</a>
            <a href="view_shortlisted.php">View Shortlisted</a>
            <a href="view_applications.php">View Applications</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

</body>
</html>
