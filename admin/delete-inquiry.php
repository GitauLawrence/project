<?php
// C:\xampp\htdocs\project\admin\delete-inquiry.php

session_start();
require_once '../includes/db-connect.php'; // Adjust path as needed for your db-connect file

// Basic admin check (uncomment when login is implemented)
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     $_SESSION['error_message'] = "Access denied.";
//     header('Location: ../login.php'); // Redirect to your admin login page
//     exit;
// }

// Check if an inquiry ID is provided and is a valid number
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id']; // Cast to integer to ensure it's a number

    // Check database connection
    if ($conn->connect_error) {
        $_SESSION['error_message'] = "Database connection failed: " . $conn->connect_error;
        header('Location: manage-inquiries.php'); // Redirect back to the inquiries page
        exit;
    }

    // Prepare a DELETE statement to prevent SQL injection
    $sql = "DELETE FROM contact_messages WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // Handle error if statement preparation fails
        $_SESSION['error_message'] = "Failed to prepare statement: " . $conn->error;
        header('Location: manage-inquiries.php');
        exit;
    }

    // Bind the ID parameter
    $stmt->bind_param("i", $id); // 'i' indicates an integer parameter

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['success_message'] = "Inquiry with ID " . $id . " deleted successfully!";
        } else {
            $_SESSION['error_message'] = "No inquiry found with ID " . $id . " or it was already deleted.";
        }
    } else {
        // Handle execution error
        $_SESSION['error_message'] = "Error deleting inquiry: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

} else {
    // If no valid ID is provided
    $_SESSION['error_message'] = "Invalid inquiry ID provided for deletion.";
}

// Redirect back to the manage-inquiries page
header('Location: manage-inquiries.php');
exit;
?>