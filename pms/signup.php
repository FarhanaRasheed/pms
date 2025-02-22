<?php
// Include your database connection file
include "db.php"; 

// Initialize variables to store input values and errors
$name = $phone_number = $email = $password = $confirm_password = $course = $gender = $birth_date = "";
$name_err = $phone_err = $email_err = $password_err = $confirm_password_err = $course_err = $gender_err = $birth_date_err = "";

// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate phone number
    if (empty(trim($_POST["phone_number"]))) {
        $phone_err = "Please enter your phone number.";
    } elseif (!preg_match("/^[0-9]{10}$/", trim($_POST["phone_number"]))) {
        $phone_err = "Please enter a valid 10-digit phone number.";
    } else {
        $phone_number = trim($_POST["phone_number"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password !== $confirm_password) {
            $confirm_password_err = "Passwords do not match.";
        }
    }

    // Validate course
    if (empty($_POST["course"])) {
        $course_err = "Please select your course.";
    } else {
        $course = $_POST["course"];
    }

    // Validate gender
    if (empty($_POST["gender"])) {
        $gender_err = "Please select your gender.";
    } else {
        $gender = $_POST["gender"];
    }

    // Validate birth date
    if (empty(trim($_POST["birth_date"]))) {
        $birth_date_err = "Please enter your birth date.";
    } else {
        $birth_date = trim($_POST["birth_date"]);
    }

    // Define an array to hold file paths for uploads
    $file_paths = [];

    // File upload handling
    $upload_dir = 'uploads/'; // Ensure this directory exists and has write permissions

    $files_to_upload = [
        'score_sheet_10th',
        'score_sheet_12th',
        'degree_certificate',
        'masters_certificate',
        'profile_photo',
        'cv'
    ];

    // File upload processing
    foreach ($files_to_upload as $file_key) {
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
            $file_name = basename($_FILES[$file_key]['name']);
            $target_file = $upload_dir . uniqid() . "_" . $file_name; // Unique file name
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate file type (optional)
            $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
            if (!in_array($file_type, $allowed_types)) {
                echo "Sorry, only PDF, JPG, JPEG, and PNG files are allowed for $file_key.";
                exit;
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $target_file)) {
                $file_paths[$file_key] = $target_file; // Store file path for further use
            } else {
                echo "Error uploading file: $file_key";
                exit;
            }
        } else {
            echo "Error with file: $file_key";
            exit;
        }
    }

    // Check for errors before inserting into database
    if (empty($name_err) && empty($phone_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($course_err) && empty($gender_err) && empty($birth_date_err)) {
        // Hash the password before saving
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // SQL query to insert the student details including file paths
        $sql = "INSERT INTO students (name, phone_number, email, password, course, gender, birth_date, 
        score_sheet_10th, score_sheet_12th, degree_certificate, masters_certificate, profile_photo, cv) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param("sssssssssssss", $name, $phone_number, $email, $hashed_password, $course, 
            $gender, $birth_date, 
            $file_paths['score_sheet_10th'], 
            $file_paths['score_sheet_12th'], 
            $file_paths['degree_certificate'], 
            $file_paths['masters_certificate'], 
            $file_paths['profile_photo'], 
            $file_paths['cv']); // 13 variables in total

            if ($stmt->execute()) {
                // Redirect to a success page or login
                header("location: login.php");
                exit; // Prevent further script execution
            } else {
                echo "Something went wrong. Please try again.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px #aaa;
            margin-top: 50px;
        }
        h2 {
            text-align: center;
        }
        form div {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"], input[type="date"],input[type="email"], select, input[type="file"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
        input[type="submit"] {
            background-color: grey;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Registration</h2>
        <form action="signup.php" method="post" enctype="multipart/form-data"> <!-- Add enctype for file uploads -->
            <!-- Name -->
            <div>
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
                <span class="error"><?php echo $name_err; ?></span>
            </div>
            
           <!-- Phone Number -->
<div>
    <label>Phone Number</label>
    <input type="text" name="phone_number" maxlength="10" pattern="^[0-9]{10}$" value="<?php echo htmlspecialchars($phone_number); ?>" required>
    <span class="error"><?php echo $phone_err; ?></span>
</div>

<!-- Email -->
<div>
    <label>Email</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
    <span class="error"><?php echo $email_err; ?></span>
</div>

            
            <!-- Password -->
            <div>
                <label>Password</label>
                <input type="password" name="password">
                <span class="error"><?php echo $password_err; ?></span>
            </div>

            <!-- Confirm Password -->
            <div>
                <label>Confirm Password</label>
                <input type="password" name="confirm_password">
                <span class="error"><?php echo $confirm_password_err; ?></span>
            </div>
            
            <!-- Course (MCA or MBA) -->
            <div>
                <label>Course</label>
                <select name="course">
                    <option value="">Select Course</option>
                    <option value="MCA" <?php echo ($course == 'MCA') ? 'selected' : ''; ?>>MCA</option>
                    <option value="MBA" <?php echo ($course == 'MBA') ? 'selected' : ''; ?>>MBA</option>
                </select>
                <span class="error"><?php echo $course_err; ?></span>
            </div>

            <!-- Gender -->
            <div>
                <label>Gender</label>
                <input type="radio" name="gender" value="Male" <?php echo ($gender == 'Male') ? 'checked' : ''; ?>> Male
                <input type="radio" name="gender" value="Female" <?php echo ($gender == 'Female') ? 'checked' : ''; ?>> Female
                <span class="error"><?php echo $gender_err; ?></span>
            </div>

            <!-- Birth Date -->
            <div>
                <label>Birth Date</label>
                <input type="date" name="birth_date" value="<?php echo htmlspecialchars($birth_date); ?>">
                <span class="error"><?php echo $birth_date_err; ?></span>
            </div>

            <!-- File Uploads -->
            <div>
                <label>10th Score Sheet</label>
                <input type="file" name="score_sheet_10th" required>
            </div>
            <div>
                <label>12th Score Sheet</label>
                <input type="file" name="score_sheet_12th" required>
            </div>
            <div>
                <label>Degree Certificate</label>
                <input type="file" name="degree_certificate" required>
            </div>
            <div>
                <label>Other Certificate</label>
                <input type="file" name="masters_certificate">
            </div>
            <div>
                <label>Profile Photo</label>
                <input type="file" name="profile_photo">
            </div>
            <div>
                <label>CV</label>
                <input type="file" name="cv">
            </div>

            <div>
                <input type="submit" value="Register">
            </div>
        </form>
    </div>
</body>
</html>
