<?php
// C:\xampp\htdocs\project\process_inquiry.php

session_start();
require_once 'includes/db-connect.php'; // Path to your db-connect.php

// Set header to indicate JSON response
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';
    $property_id = $_POST['property_id'] ?? null;
    $subject = $_POST['subject'] ?? '';

    if (!empty($property_id)) {
        $subject = "Property Inquiry - ID: " . htmlspecialchars($property_id);
    } elseif (empty($subject)) {
        $subject = "General Contact Inquiry";
    }

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
        exit;
    }

    try {
        if ($property_id !== null) {
            $sql = "INSERT INTO contact_messages (name, email, phone, subject, message, property_id, status) VALUES (?, ?, ?, ?, ?, ?, 'new')";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sssssi", $name, $email, $phone, $subject, $message, $property_id);
            }
        } else {
            $sql = "INSERT INTO contact_messages (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, 'new')";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
            }
        }

        if (!$stmt) {
            throw new Exception("SQL prepare failed: " . $conn->error);
        }

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Your inquiry has been sent successfully!']);
        } else {
            throw new Exception("Failed to insert inquiry: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log("Contact form submission error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred. Please try again later.']);
    } finally {
        $conn->close();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>