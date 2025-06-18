<?php
// C:\xampp\htdocs\project\admin\manage-inquiries.php

// 1. Basic Security/Authentication Placeholder (IMPORTANT for admin pages!)
// You will implement proper login system later.
session_start();
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header('Location: ../login.php'); // Redirect to admin login page
//     exit;
// }

// 2. Include Database Connection
require_once '../includes/db-connect.php'; // Adjust path if necessary (it's one level up from admin/)

// Check database connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// 3. Fetch Messages from Database
$sql = "SELECT id, name, email, phone, subject, message, sent_at FROM contact_messages ORDER BY id DESC";
$result = $conn->query($sql);

$messages = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css"> <style>
        /* Basic inline styles for immediate visual feedback */
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 1200px; margin: 0 auto; }
        h1 { color: #2c3e50; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: top; }
        th { background-color: #e9ecef; font-weight: bold; color: #555; }
        tr:nth-child(even) { background-color: #f6f6f6; }
        .no-messages { text-align: center; color: #777; padding: 20px; background-color: #e9e9e9; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Contact Inquiries</h1>
        <p>Here you can view all messages submitted through the contact form.</p>

        <?php if (!empty($messages)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sent At</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Subject</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($msg['id']); ?></td>
                        <td><?php echo htmlspecialchars($msg['sent_at']); ?></td>
                        <td><?php echo htmlspecialchars($msg['name']); ?></td>
                        <td><?php echo htmlspecialchars($msg['email']); ?></td>
                        <td><?php echo htmlspecialchars($msg['phone']); ?></td>
                        <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-messages">No contact inquiries found yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>