<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$sql = "SELECT paper_name, uploaded_by, file_path, uploaded_at FROM placement_papers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Papers</title>
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
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #f7f7f7;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: grey;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        td a {
            color: grey;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        td a:hover {
            color: black;
        }
        p {
            text-align: center;
            font-size: 16px;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Previous Year Placement Papers</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Paper Name</th>
                    <th>Uploaded By</th>
                    <th>Uploaded At</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['paper_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['uploaded_by']); ?></td>
                        <td><?php echo htmlspecialchars($row['uploaded_at']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($row['file_path']); ?>" download>Download</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No placement papers found.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>
