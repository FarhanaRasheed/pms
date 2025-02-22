<?php
// Include the database connection
include 'db.php';

// Define admin credentials
$admin_email = "admin@gmail.com"; // Replace with the desired admin email
$admin_password = "admin@1234"; // Replace with the desired admin password

// Hash the password
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// Prepare an SQL statement to insert the admin user
$sql = "INSERT INTO admin (email, password) VALUES (?, ?)";

if ($stmt = $conn->prepare($sql)) {
    // Bind parameters
    $stmt->bind_param("ss", $admin_email, $hashed_password);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Admin user added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    // SQL query preparation failed
    echo "SQL prepare failed: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
