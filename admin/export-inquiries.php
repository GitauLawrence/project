<?php
// C:\xampp\htdocs\project\admin\export-inquiries.php

// Disable error reporting for live download, enable for debugging if needed
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();
require_once '../includes/db-connect.php'; // Adjust path if necessary

// Basic Security/Authentication Placeholder (IMPORTANT for admin pages!)
// Uncomment and implement proper login check when ready
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header('Location: ../login.php'); // Redirect to admin login page if not logged in
//     exit;
// }

// Check database connection
if ($conn->connect_error) {
    die("Database Connection failed for export: " . $conn->connect_error);
}

// --- Helper function for bind_param (re-using the one from manage-inquiries.php) ---
/**
 * Binds parameters to a prepared statement.
 * This helper function ensures arguments are passed by reference, which bind_param requires.
 *
 * @param mysqli_stmt $stmt The prepared statement object.
 * @param string $types A string containing one or more characters which specify the types for the corresponding bind variables.
 * @param array $params An array of parameters to bind.
 * @return bool True on success or false on failure.
 */
function _bind_params_to_stmt($stmt, $types, &$params) {
    if (empty($params)) {
        return true;
    }
    $bind_args[] = $types;
    foreach ($params as $key => $value) {
        $bind_args[] = &$params[$key];
    }
    return call_user_func_array([$stmt, 'bind_param'], $bind_args);
}
// --- End Helper Function ---

// --- Fetching Logic (similar to manage-inquiries.php but without LIMIT/OFFSET) ---
$search_term = $_GET['search'] ?? '';
$filter_status = $_GET['status_filter'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$sql_conditions = [];
$params = [];
$param_types = '';

// Add search term condition
if (!empty($search_term)) {
    $search_term_like = '%' . $search_term . '%';
    $sql_conditions[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $params[] = $search_term_like;
    $params[] = $search_term_like;
    $params[] = $search_term_like;
    $params[] = $search_term_like;
    $param_types .= 'ssss';
}

// Add status filter condition
if (!empty($filter_status) && in_array($filter_status, ['new', 'read', 'resolved'])) {
    $sql_conditions[] = "status = ?";
    $params[] = $filter_status;
    $param_types .= 's';
}

// Add date range conditions
if (!empty($start_date)) {
    $sql_conditions[] = "sent_at >= ?";
    $params[] = $start_date . ' 00:00:00';
    $param_types .= 's';
}
if (!empty($end_date)) {
    $sql_conditions[] = "sent_at <= ?";
    $params[] = $end_date . ' 23:59:59';
    $param_types .= 's';
}

// Construct the WHERE clause
$where_clause = '';
if (!empty($sql_conditions)) {
    $where_clause = " WHERE " . implode(" AND ", $sql_conditions);
}

// Fetch all messages that match the filters (no LIMIT/OFFSET for export)
$sql = "SELECT id, name, email, phone, subject, message, sent_at, status FROM contact_messages" . $where_clause . " ORDER BY sent_at DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // Handle prepare error
    $_SESSION['error_message'] = "Export failed: SQL prepare error: " . $conn->error;
    header('Location: manage-inquiries.php');
    exit;
}

// Bind parameters using the helper function
if (!empty($params)) {
    _bind_params_to_stmt($stmt, $param_types, $params);
}

$stmt->execute();
$result = $stmt->get_result();

// --- CSV Export Logic ---

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="contact_inquiries_' . date('Y-m-d_His') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Open a file handle to php://output for writing CSV data
$output = fopen('php://output', 'w');

// Output CSV headers (column names)
fputcsv($output, ['ID', 'Sent At', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Status']);

// Output data rows
while ($row = $result->fetch_assoc()) {
    // Ensure data is properly formatted for CSV
    fputcsv($output, [
        $row['id'],
        $row['sent_at'],
        $row['name'],
        $row['email'],
        $row['phone'],
        $row['subject'],
        $row['message'],
        $row['status']
    ]);
}

// Close the file handle
fclose($output);

// Close database connection
$stmt->close();
$conn->close();

exit; // Important: terminate script after file download
?>