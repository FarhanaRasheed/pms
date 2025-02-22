<?php
include 'db.php';
session_start();

// Check if student is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Fetch news from the database
$sql = "SELECT title, content, created_at FROM news ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>College News</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        .news-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .news-item {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .news-title {
            font-size: 1.5em;
            color: #333;
            margin: 0 0 10px;
        }
        .news-content {
            font-size: 1em;
            color: #555;
            margin-bottom: 10px;
        }
        .news-date {
            font-size: 0.9em;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="news-container">
        <h2>College News</h2>
        
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="news-item">';
                echo '<h3 class="news-title">' . htmlspecialchars($row['title']) . '</h3>';
                echo '<p class="news-content">' . nl2br(htmlspecialchars($row['content'])) . '</p>';
                echo '<p class="news-date">Published on: ' . date('F j, Y, g:i a', strtotime($row['created_at'])) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No news available at the moment.</p>';
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
