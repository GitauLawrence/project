<?php
// C:\xampp\htdocs\project\admin\update-inquiry-status.php

session_start();
require_once '../includes/db-connect.php'; // Adjust path as needed for your db-connect file

// Basic admin check (uncomment when login is implemented)
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     $_SESSION['error_message'] = "Access denied.";
//     header('Location: ../login.php'); // Redirect to your admin login page
//     exit;
// }

// Check if an inquiry ID and status are provided and are valid
if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $new_status = $_GET['status'];

    // Validate the new status to ensure it's one of the allowed ENUM values
    $allowed_statuses = ['new', 'read', 'resolved'];
    if (!in_array($new_status, $allowed_statuses)) {
        $_SESSION['error_message'] = "Invalid status provided.";
        header('Location: manage-inquiries.php');
        exit;
    }

    // Check database connection
    if ($conn->connect_error) {
        $_SESSION['error_message'] = "Database connection failed: " . $conn->connect_error;
        header('Location: manage-inquiries.php');
        exit;
    }

    // Prepare an UPDATE statement to prevent SQL injection
    $sql = "UPDATE contact_messages SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // Handle error if statement preparation fails
        $_SESSION['error_message'] = "Failed to prepare statement: " . $conn->error;
        header('Location: manage-inquiries.php');
        exit;
    }

    // Bind the parameters: 's' for string (status), 'i' for integer (id)
    $stmt->bind_param("si", $new_status, $id);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['success_message'] = "Inquiry with ID " . $id . " status updated to " . htmlspecialchars($new_status) . "!";
        } else {
            $_SESSION['error_message'] = "No inquiry found with ID " . $id . " or status was already " . htmlspecialchars($new_status) . ".";
        }
    } else {
        // Handle execution error
        $_SESSION['error_message'] = "Error updating inquiry status: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

} else {
    // If no valid ID or status is provided
    $_SESSION['error_message'] = "Invalid inquiry ID or status provided for update.";
}

// Redirect back to the manage-inquiries page
header('Location: manage-inquiries.php');
exit;
?>