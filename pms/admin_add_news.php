<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Initialize variables
$title = $content = "";
$title_err = $content_err = "";
$success_message = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate content
    if (empty(trim($_POST["content"]))) {
        $content_err = "Please enter news content.";
    } else {
        $content = trim($_POST["content"]);
    }

    // Check for errors before inserting into the database
    if (empty($title_err) && empty($content_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO news (title, content) VALUES (?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $title, $content);

            // Execute the statement
            if ($stmt->execute()) {
                // Show a success message
                $success_message = "News added successfully!";
                $title = $content = ""; // Clear the form fields
            } else {
                echo "Error: " . $stmt->error; // Display any SQL error
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error; // Display prepare error
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add News</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0e0e0; /* Light grey background */
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            background-color: #ffffff; /* White background for the form */
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-top: 0;
            color: #555; /* Dark grey for the heading */
        }
        label {
            display: block;
            margin-top: 10px;
            color: #555; /* Dark grey for labels */
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9; /* Light grey for input fields */
        }
        button {
            margin-top: 15px;
            background-color: #777; /* Grey button */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #555; /* Darker grey on hover */
        }
        .error {
            color: red;
        }
        .success {
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add News</h2>
        
        <?php if ($success_message): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        
        <form method="post" action="admin_add_news.php">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>">
            <span class="error"><?php echo $title_err; ?></span><br>

            <label for="content">Content:</label>
            <textarea id="content" name="content"><?php echo htmlspecialchars($content); ?></textarea>
            <span class="error"><?php echo $content_err; ?></span><br>

            <button type="submit">Add News</button>
        </form>
    </div>
</body>
</html>
