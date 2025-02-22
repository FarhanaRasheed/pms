<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    $paper_name = $_POST['paper_name'];
    $uploaded_by = $_SESSION['email']; // Admin email
    $target_dir = "uploads/placement_papers/";
    $file_name = basename($_FILES["paper_file"]["name"]);
    $target_file = $target_dir . $file_name;

    // Ensure the upload directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES["paper_file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO placement_papers (paper_name, uploaded_by, file_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $paper_name, $uploaded_by, $target_file);

        if ($stmt->execute()) {
            echo "Placement paper uploaded successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error uploading file.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Placement Paper</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            font-size: 16px;
            color: #333;
            margin-bottom: 8px;
        }
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: grey;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }
        button:hover {
            background-color: black;
        }
    </style>
</head>
<body>
    <h1>Upload Placement Paper</h1>
    <form action="admin_add_paper.php" method="POST" enctype="multipart/form-data">
        <label for="paper_name">Paper Name:</label>
        <input type="text" name="paper_name" id="paper_name" required>
        <label for="paper_file">Select File:</label>
        <input type="file" name="paper_file" id="paper_file" accept=".pdf,.doc,.docx" required>
        <button type="submit">Upload Paper</button>
    </form>
</body>
</html>
