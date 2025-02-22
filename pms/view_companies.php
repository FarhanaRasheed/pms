<?php
// Include the database connection
include 'db.php';
session_start();

// Fetch companies from the database
$sql = "SELECT id, name, requirements FROM companies";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Companies</title>
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
        ul {
            list-style-type: none; /* Remove default list style */
            padding: 0; /* Remove padding for the list */
            margin: 0 auto; /* Center the list */
            max-width: 800px; /* Set a maximum width for the list */
        }
        li {
            background-color: #fff;
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        strong {
            color: black; /* Company name color */
        }
        .requirements {
            font-size: 14px;
            color: #666; /* Requirements text color */
        }
        form {
            margin-top: 10px;
        }
        button {
            padding: 10px 15px;
            background-color: grey; /* Button color */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: black; /* Button hover color */
        }
    </style>
</head>
<body>
    <h2>Available Companies</h2>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<li>";
                echo "<strong>" . htmlspecialchars($row["name"]) . "</strong><br>";
                echo "<span class='requirements'>Requirements: " . htmlspecialchars($row["requirements"]) . "</span><br>";
                echo "<form method='post' action='apply.php'>
                        <input type='hidden' name='company_id' value='" . htmlspecialchars($row["id"]) . "'>
                        <button type='submit'>Apply</button>
                      </form>";
                echo "</li>";
            }
        } else {
            echo "<li>No companies available.</li>";
        }
        ?>
    </ul>
</body>
</html>
