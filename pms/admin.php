<?php
// Include the database connection
include 'db.php';

// Start the session
session_start();

// Initialize variables for error messages
$email_err = $password_err = $login_err = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get email and password from POST
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Validate email
    if (empty($email)) {
        $email_err = "Please enter your email.";
    }

    // Validate password
    if (empty($password)) {
        $password_err = "Please enter your password.";
    }

    // Check if there are no validation errors
    if (empty($email_err) && empty($password_err)) {
        // Prepare a SQL query to fetch the admin's record by email
        $sql = "SELECT id, email, password FROM admin WHERE email = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind the email to the prepared statement
            $stmt->bind_param("s", $email);

            // Execute the statement
            if ($stmt->execute()) {
                // Store the result
                $stmt->store_result();

                // Check if the admin exists
                if ($stmt->num_rows == 1) {
                    // Bind the result variables
                    $stmt->bind_result($id, $db_email, $db_password);

                    if ($stmt->fetch()) {
                        // Verify the password
                        if (password_verify($password, $db_password)) {
                            // Password is correct, start a new session and store admin data in the session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $db_email;

                            // Redirect the admin to their dashboard
                            header("location: admin_dashboard.php");
                            exit();
                        } else {
                            // Invalid password
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else {
                    // Admin not found
                    $login_err = "Invalid email or password.";
                }
            } else {
                // Something went wrong
                $login_err = "Oops! Something went wrong. Please try again later.";
            }

            // Close the statement
            $stmt->close();
        } else {
            // SQL query preparation failed
            $login_err = "SQL prepare failed: " . htmlspecialchars($conn->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - MES AIMAT</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url('MES.jpeg'); /* Set lab.jpg as background */
            background-size: cover; /* Cover the whole page */
            background-repeat: no-repeat; /* Do not repeat the background image */
            color: white; /* Set text color to white */
        }

        /* Header styling with navigation inside */
        header {
            background: rgba(0, 0, 0, 0.8); /* Semi-transparent black background */
            padding: 20px; /* Add some padding */
            text-align: center; /* Center the text */
            position: relative; /* Positioning context */
            z-index: 1; /* Ensure it stays on top of other elements */
        }

        h1 {
            font-size: 36px; /* Font size */
            color: white; /* Text color */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); /* Shadow for better readability */
            margin-bottom: 10px; /* Space below the heading */
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0; /* Remove default margin */
        }

        nav ul li {
            display: inline-block;
            margin-right: 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 5px 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        nav ul li a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        main {
            text-align: center;
            padding: 20px;
        }

        .container {
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent background for the container */
            padding: 20px;
            border-radius: 8px; /* Rounded corners */
            display: inline-block; /* Center the container */
            margin-top: 20px; /* Space above the container */
        }

        .alert {
            color: red; /* Error message color */
            margin-bottom: 10px; /* Space below alert */
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent background for footer */
            position: relative;
            bottom: 0;
            width: 100%;
        }
     
        input[type="email"],
        input[type="password"],
        button {
            padding: 10px;
            width: 100%; /* Full width */
            margin-bottom: 10px; /* Space between inputs */
            border: none; /* Remove border */
            border-radius: 5px; /* Rounded corners */
        }
        button {
    width: 50%; /* Reduced width */
    padding: 8px; /* Adjust padding as needed */
    background-color: grey; /* Green background */
    color: white; /* White text */
    cursor: pointer; /* Pointer cursor on hover */
    border-radius: 5px; /* Rounded corners */
    border: none; /* Remove border */
}

button:hover {
    background-color: light black; /* Darker green on hover */
}

       
    </style>
</head>

<body>
    <header>
        <h1>Admin Panel - MES AIMAT</h1>
        <br><br>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="About.html">About Us</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <h2>Login to Admin Panel</h2>

            <?php if (!empty($login_err)): ?>
                <div class="alert">
                    <?php echo $login_err; ?>
                </div>
            <?php endif; ?>

            <form id="adminLoginForm" method="post" action="admin.php">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                <span class="error"><?php echo $email_err; ?></span>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <span class="error"><?php echo $password_err; ?></span>
                <br><br>

                <button type="submit">Login</button>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
        <p>Contact Us: <a href="mailto:mesaimat@gmail.com">mesaimat@gmail.com</a> | Phone: 8976905857
        </div>
    </footer>
</body>

</html>
