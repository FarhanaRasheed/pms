<?php
include 'db.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$name = $requirements = "";
$name_err = $requirements_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $requirements = trim($_POST["requirements"]);

    if (empty($name)) {
        $name_err = "Please enter a company name.";
    }
    if (empty($requirements)) {
        $requirements_err = "Please enter job requirements.";
    }

    if (empty($name_err) && empty($requirements_err)) {
        $sql = "INSERT INTO companies (name, requirements) VALUES (?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $name, $requirements);
            if ($stmt->execute()) {
                header("location: admin_dashboard.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "SQL prepare failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Publish Company</title>
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
        form {
            background-color: #fff; /* White background for the form */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px; /* Fixed width for the form */
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            margin-top: 15px;
            background-color: grey; /* Green button */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%; /* Full width for button */
        }
        button:hover {
            background-color: black; /* Darker green on hover */
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h2>Publish Company Details</h2>
    <form method="post" action="admin_publish_company.php">
        <label for="name">Company Name:</label>
        <input type="text" id="name" name="name" required>
        <span class="error"><?php echo $name_err; ?></span><br>

        <label for="requirements">Requirements:</label>
        <textarea id="requirements" name="requirements" required></textarea>
        <span class="error"><?php echo $requirements_err; ?></span><br>

        <button type="submit">Publish</button>
    </form>
</body>
</html>
