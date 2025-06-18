<?php
// C:\xampp\htdocs\project\send_contact.php

// Set error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Tell browser to expect JSON response

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from the POST request
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = 'Please fill in all required fields (Name, Email, Message).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address.';
    } else {
        // --- This is where your actual email sending or database saving logic will go ---

        // For now, let's just log the data and pretend it's sent
        $log_message = "New Contact Form Submission:\n"
                     . "Name: $name\n"
                     . "Email: $email\n"
                     . "Phone: $phone\n"
                     . "Subject: $subject\n"
                     . "Message: $message\n"
                     . "-----------------------------------\n";
        file_put_contents('contact_submissions.log', $log_message, FILE_APPEND);

        $response['success'] = true;
        $response['message'] = 'Thank you for your message! We will contact you soon.';

        // If you wanted to save to the database using your db-connect.php
        // require_once 'includes/db-connect.php'; // Ensure connection is established
        // $stmt = $conn->prepare("INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        // $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        // if ($stmt->execute()) {
        //     $response['message'] = 'Message saved to database successfully!';
        // } else {
        //     $response['success'] = false;
        //     $response['message'] = 'Failed to save message to database: ' . $conn->error;
        // }
        // $stmt->close();
        // $conn->close();
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit; // Stop further execution
?>