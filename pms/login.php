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
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    }

    // Validate password
    if (empty($password)) {
        $password_err = "Please enter your password.";
    }

    // Check if there are no validation errors
    if (empty($email_err) && empty($password_err)) {
        // Prepare a SQL query to fetch the user's record by email from the student table
        $sql = "SELECT student_id, email, password FROM students WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind the email to the prepared statement
            $stmt->bind_param("s", $email);

            // Execute the statement
            if ($stmt->execute()) {
                // Store the result
                $stmt->store_result();

                // Check if the user exists in the student table
                if ($stmt->num_rows == 1) {
                    // Bind the result variables
                    $stmt->bind_result($id, $db_email, $db_password);
                    
                    if ($stmt->fetch()) {
                        // Verify the password
                        if (password_verify($password, $db_password)) {
                            // Start a new session and store user data in the session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $db_email;

                            // Redirect the student to their dashboard
                            header("location: student_dashboard.php");
                            exit();
                        } else {
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else {
                    // User not found in the student table, check the admin table
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
                                        // Start a new session and store admin data in the session
                                        $_SESSION["loggedin"] = true;
                                        $_SESSION["id"] = $id;
                                        $_SESSION["email"] = $db_email;

                                        // Redirect the admin to their dashboard
                                        header("location: admin_dashboard.php");
                                        exit();
                                    } else {
                                        $login_err = "Invalid email or password.";
                                    }
                                }
                            } else {
                                $login_err = "Invalid email or password.";
                            }
                        } else {
                            echo "Oops! Something went wrong. Please try again later. Error: " . $stmt->error;
                        }
                    } else {
                        // SQL query preparation failed
                        echo "SQL prepare failed: " . $conn->error;
                    }
                }
            } else {
                echo "Oops! Something went wrong. Please try again later. Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            // SQL query preparation failed
            echo "SQL prepare failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - MES AIMAT</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your main stylesheet -->
    <link rel="stylesheet" href="login.css"> <!-- Link to the new CSS for login -->
    <style>
        /* General page styling */
        body {
            margin: 0;
            background-image: url('lab.jpg'); /* Background image */
            background-size: cover; /* Cover the entire background */
            background-repeat: no-repeat; /* Do not repeat the background */
            color: white; /* Default text color */
        }

        /* Header styling */
        header {
            /* padding: 30px; */
            text-align: center;
            background: rgba(0, 0, 0, 0.7); /* Slightly transparent background */
        }

        h1 {
            font-size: 36px;
            margin: 0;
            /* margin-top: 10px;  */
            /* No margin to eliminate spacing */
        }

        h3 {
            font-size: 24px;
            margin: 10px 0;
            /* margin-bottom: 5px; */
             /* Add margin for spacing below h1 */
        }

        nav {
            background: rgba(0, 0, 0, 0.7); /* Nav background */
            padding: 10px;
            /* margin-top: 10px; */
            text-align: center; /* Centered nav items */
        }

        nav ul {
            list-style-type: none;
            padding: 0;
        }

        nav ul li {
            display: inline-block;
            margin-right: 15px; /* Space between navigation items */
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

        /* Main content */
        main {
            padding: 40px 20px;
            text-align: center;
        }

        .login-form {
            max-width: 400px; /* Limit the form width */
            margin: auto; /* Center the form */
            background: rgba(0, 0, 0, 0.7); /* Form background */
            padding: 20px;
            border-radius: 10px; /* Rounded corners */
        }

        .login-form h1 {
            font-size: 32px;
            color: #fff;
        }

        .login-form label {
            font-size: 18px;
            display: block;
            margin: 10px 0 5px;
        }

        .login-form input[type="email"],
        .login-form input[type="password"] {
            width: 100%; /* Full-width input fields */
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ccc; /* Border styling */
            border-radius: 5px; /* Rounded corners */
            background: rgba(255, 255, 255, 0.1); /* Input background */
            color: white; /* Input text color */
        }

        .login-form input[type="submit"] {
    width: 50%;
    padding: 8px; /* Reduced padding for a smaller button */
    background: grey;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px; /* Reduced font size */
}

.login-form input[type="submit"]:hover {
    background: grey;
}


        /* Footer styling */
        footer {
            color: white;
            padding: 15px 0;
            text-align: center;
            margin-top: 30px;
            background-color: rgba(0, 0, 0, 0.7);
        }

        footer a {
            color: white;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <br><br>
            <h1><b>MES AIMAT, MARAMPALLY</b></h1>
            <h3><b>PLACEMENT MANAGEMENT CELL</b></h3><br>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="Index.html">Home</a></li>
            <li><a href="About.html">About Us</a></li>
            <li><a href="admin.php">Admin</a></li>
        </ul>
    </nav>

    <main>
        <div class="container">
            <div class="login-form">
                <h1>Student Login</h1>
                
                <?php 
                // Display login error message if any
                if (!empty($login_err)) {
                    echo '<div class="alert alert-danger">' . $login_err . '</div>';
                }
                ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                    
                    <label for="password">Password:</label>
                    <input type="password" name="password" required>
<br><br>
                    <input type="submit" value="Login">
                </form>
                <p>Don't have an account? <a href="signup.php">Sign up now</a>.</p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 MES AIMAT. All rights reserved.</p>
    </footer>
</body>
</html>
