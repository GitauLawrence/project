<?php
// C:\xampp\htdocs\project\send_contact.php

// Set error reporting for debugging (remove or set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Tell browser to expect JSON response

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize data from the POST request
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
        // --- 1. Save to Database ---
        require_once 'includes/db-connect.php'; // Your database connection file

        if ($conn->connect_error) {
            $response['message'] = 'Database connection failed: ' . $conn->connect_error;
            echo json_encode($response);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

        if ($stmt->execute()) {
            $db_success = true;
            // $response['message'] = 'Message saved to database successfully!';
        } else {
            $db_success = false;
            $response['message'] = 'Failed to save message to database: ' . $stmt->error;
            error_log("Database Save Error: " . $stmt->error);
        }
        $stmt->close();
        $conn->close();

        // --- 2. Send Email (Still commented out) ---
        // $to = 'wanjirulawrence78@gmail.com'; // CHANGE THIS to the email address where you want to receive messages
        // $email_subject = "New Contact Form Message: " . $subject;
        // $email_body = "You have received a new message from your website contact form.\n\n"
        //             . "Here are the details:\n\n"
        //             . "Name: " . $name . "\n"
        //             . "Email: " . $email . "\n"
        //             . "Phone: " . (empty($phone) ? "N/A" : $phone) . "\n"
        //             . "Subject: " . (empty($subject) ? "N/A" : $subject) . "\n"
        //             . "Message:\n" . $message . "\n";

        // $headers = "From: " . $email . "\r\n";
        // $headers .= "Reply-To: " . $email . "\r\n";
        // $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // $mail_sent = mail($to, $email_subject, $email_body, $headers);

        // Assume email success for now (since it's commented out)
        $email_success = true;


        // --- Final Response Logic ---
        if ($db_success && $email_success) { // $email_success is always true here
            $response['success'] = true;
            $response['message'] = 'Thank you for your message! It has been saved to the database.';
        } elseif ($db_success && !$email_success) { // This block won't be hit
            $response['success'] = false;
            $response['message'] = 'Message saved to database, but failed to send email. Please check server logs.';
        } elseif (!$db_success && $email_success) { // This block will be hit if DB fails
            $response['success'] = false;
            $response['message'] = 'Failed to save message to database. Email sending is currently disabled.';
        }
        // If both failed (though email is always true), the initial $response['message'] from DB error or default will apply.
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>