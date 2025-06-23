<?php
// C:\xampp\htdocs\project\admin\manage-inquiries.php

// Set error reporting for debugging (remove or set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session to handle success/error messages
session_start();

// Handle messages from redirects (e.g., after delete/update)
$success_message = '';
$error_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Basic Security/Authentication Placeholder (IMPORTANT for admin pages!)
// Uncomment and implement proper login check when ready
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header('Location: ../login.php'); // Redirect to admin login page
//     exit;
// }

// Include Database Connection
require_once '../includes/db-connect.php'; // Adjust path if necessary

// Check database connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// --- Helper function for bind_param with dynamic arguments (addresses reference issue) ---
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
        // No parameters to bind, just return true
        return true;
    }

    $bind_args[] = $types; // First argument for bind_param is the type string

    // Create references for each parameter
    foreach ($params as $key => $value) {
        $bind_args[] = &$params[$key]; // Pass by reference
    }

    // Use call_user_func_array to call bind_param with the dynamically built array of references
    return call_user_func_array([$stmt, 'bind_param'], $bind_args);
}
// --- End Helper Function ---


// --- Search and Filter Logic ---
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
    $param_types .= 'ssss'; // Four string parameters
}

// Add status filter condition
if (!empty($filter_status) && in_array($filter_status, ['new', 'read', 'resolved'])) {
    $sql_conditions[] = "status = ?";
    $params[] = $filter_status;
    $param_types .= 's'; // One string parameter
}

// Add date range conditions
if (!empty($start_date)) {
    $sql_conditions[] = "sent_at >= ?";
    $params[] = $start_date . ' 00:00:00'; // Start of the day
    $param_types .= 's'; // One string parameter
}
if (!empty($end_date)) {
    $sql_conditions[] = "sent_at <= ?";
    $params[] = $end_date . ' 23:59:59'; // End of the day
    $param_types .= 's'; // One string parameter
}

// Construct the WHERE clause
$where_clause = '';
if (!empty($sql_conditions)) {
    $where_clause = " WHERE " . implode(" AND ", $sql_conditions);
}

// --- Pagination Logic ---
$limit = 10; // Number of inquiries per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of inquiries with filters applied
$total_inquiries_sql = "SELECT COUNT(*) AS total FROM contact_messages" . $where_clause;
$stmt_total = $conn->prepare($total_inquiries_sql);

// Bind parameters for total count query
if (!empty($params)) {
    // Pass $params array by reference to the helper function
    _bind_params_to_stmt($stmt_total, $param_types, $params);
}

$stmt_total->execute();
$total_inquiries_result = $stmt_total->get_result();
$total_inquiries_row = $total_inquiries_result->fetch_assoc();
$total_inquiries = $total_inquiries_row['total'];
$total_pages = ceil($total_inquiries / $limit);
$stmt_total->close();

// --- Fetch Messages from Database with LIMIT, OFFSET, Search, and Filters ---
$sql = "SELECT id, name, email, phone, subject, message, sent_at, status FROM contact_messages" . $where_clause . " ORDER BY sent_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// Create a *new* array of parameters for the main query, including limit and offset
// This is crucial because the previous $params might have been modified by reference.
$main_query_params = $params; // Start with the filter params
$main_query_params[] = $limit;
$main_query_params[] = $offset;

// Add limit and offset parameter types
$main_query_param_types = $param_types . 'ii'; // Two integer parameters for LIMIT and OFFSET

// Bind parameters for the main query
_bind_params_to_stmt($stmt, $main_query_param_types, $main_query_params);

$stmt->execute();
$result = $stmt->get_result();

$messages = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

// Close database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">

    <style>
        /* Define a color palette for easier management */
        :root {
            --primary-color: #4CAF50; /* A pleasant green, often used for success/action */
            --secondary-color: #2196F3; /* A vibrant blue, for accents */
            --dark-heading: #3F51B5; /* Deeper blue for headings */
            --text-color: #34495e; /* Darker grey for primary text */
            --light-text-color: #7f8c8d; /* Lighter grey for secondary text */
            --bg-light: #ecf0f1; /* Very light grey for main background */
            --bg-card: #ffffff; /* White for cards/containers */
            --border-light: #e0e0e0; /* Light border color */
            --table-header-bg: #f5f5f5; /* Light grey for table headers */
            --table-row-even-bg: #fefefe; /* Almost white for even rows */
            --table-row-hover-bg: #e8f5e9; /* Very light green on hover */
            --error-color: #e74c3c; /* Red for errors/no messages */
            --success-color: #28a745; /* Green for success messages */
            --info-color: #17a2b8; /* Blue for info/read status */


            /* Colors for table headers */
            --header-color1: #007bff; /* Blue */
            --header-color2: #28a745; /* Green */
            --header-color3: #ffc107; /* Yellow/Orange */
            --header-color4: #dc3545; /* Red */
            --header-color5: #6f42c1; /* Purple */
            --header-color6: #fd7e14; /* Orange */
            --header-color7: #20c997; /* Teal */
            --header-color8: #00bcd4; /* Light Blue for Status column */
            --header-color9: #343a40; /* Darker grey for Actions column */


            /* Colors for pagination buttons */
            --pagination-prev-bg: #dc3545; /* Red for Previous */
            --pagination-next-bg: #28a745; /* Green for Next */
            --pagination-text-color: #ffffff; /* White text for colored buttons */
            --pagination-disabled-bg: #f0f0f0;
            --pagination-disabled-color: #ccc;

            /* New button colors for actions */
            --btn-delete-bg: #dc3545; /* Red */
            --btn-delete-hover: #c82333; /* Darker red */
            --btn-read-bg: #17a2b8; /* Info blue */
            --btn-read-hover: #138496; /* Darker info blue */
            --btn-resolve-bg: #28a745; /* Success green */
            --btn-resolve-hover: #218838; /* Darker success green */
            --btn-view-bg: #6c757d; /* Grey */
            --btn-view-hover: #5a6268; /* Darker grey */

            /* New styles for search/filter form */
            --form-bg: #f8f9fa; /* Light grey for form background */
            --input-border: #ced4da; /* Light border for inputs */
            --button-primary-bg: #007bff; /* Blue for primary buttons */
            --button-primary-hover: #0056b3; /* Darker blue on hover */
        }

        /* General Body and Container Styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 25px;
            background-color: var(--bg-light);
            color: var(--text-color);
            line-height: 1.7;
        }

        .container {
            background-color: var(--bg-card);
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            max-width: 1400px;
            margin: 0 auto;
            text-align: center; /* THIS CENTERS THE H1 and P within the container */
        }

        /* Message display for success/error */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }


        /* Heading Styles */
        h1 {
            color: var(--dark-heading);
            margin-bottom: 20px;
            font-size: 2.5em;
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 15px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        p {
            text-align: center;
            margin-bottom: 40px;
            color: var(--light-text-color);
            font-size: 1.15em;
        }

        /* Search and Filter Form */
        .filter-form {
            background-color: var(--form-bg);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            display: flex;
            flex-wrap: wrap; /* Allows items to wrap on smaller screens */
            gap: 15px; /* Space between form elements */
            justify-content: center; /* Center items horizontally */
            align-items: flex-end; /* Align items to the bottom */
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Align labels/inputs to the left */
        }

        .filter-form label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-color);
            font-size: 0.95em;
        }

        .filter-form input[type="text"],
        .filter-form input[type="date"],
        .filter-form select {
            padding: 10px 12px;
            border: 1px solid var(--input-border);
            border-radius: 6px;
            font-size: 1em;
            color: var(--text-color);
            width: 200px; /* Fixed width for consistency */
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .filter-form input[type="text"]:focus,
        .filter-form input[type="date"]:focus,
        .filter-form select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2); /* Soft glow */
            outline: none;
        }

        .filter-form button {
            padding: 10px 20px;
            background-color: var(--button-primary-bg);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 15px; /* Add some top margin to align with inputs */
        }

        .filter-form button:hover {
            background-color: var(--button-primary-hover);
            transform: translateY(-1px);
        }

        .filter-form button:active {
            transform: translateY(0);
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-radius: 10px;
            overflow: hidden;
            margin-left: auto; /* Centers the table within the container */
            margin-right: auto; /* Centers the table within the container */
            text-align: left; /* Reset text alignment for table content */
        }

        th, td {
            border: 1px solid var(--border-light);
            padding: 14px 18px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: var(--table-header-bg);
            font-weight: 700;
            color: var(--dark-heading);
            text-transform: uppercase;
            font-size: 0.95em;
            letter-spacing: 0.8px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Specific styling for the top-left and top-right corners of the header */
        th:first-child {
            border-top-left-radius: 10px;
        }
        th:last-child {
            border-top-right-radius: 10px;
        }

        /* Different colors for subheadings (<th>) */
        th:nth-child(1) { color: var(--header-color1); } /* # */
        th:nth-child(2) { color: var(--header-color2); } /* Sent At */
        th:nth-child(3) { color: var(--header-color3); } /* Name */
        th:nth-child(4) { color: var(--header-color4); } /* Email */
        th:nth-child(5) { color: var(--header-color5); } /* Phone */
        th:nth-child(6) { color: var(--header-color6); } /* Subject */
        th:nth-child(7) { color: var(--header-color7); } /* Message */
        th:nth-child(8) { color: var(--header-color8); } /* Status */
        th:nth-child(9) { color: var(--header-color9); } /* Actions */


        /* Table Body Rows */
        tbody tr {
            transition: background-color 0.3s ease-in-out;
        }

        tbody tr:nth-child(even) {
            background-color: var(--table-row-even-bg);
        }

        /* Status-based row styling */
        tbody tr.status-new {
            font-weight: bold;
            background-color: #ffffff; /* Changed to white */
        }
        tbody tr.status-read {
            background-color: #f7f7f7; /* Slightly greyed out */
            color: #777;
        }
        tbody tr.status-resolved {
            background-color: #e6ffe6; /* Light green for resolved messages */
            color: #5a5;
        }

        tbody tr:hover {
            background-color: var(--table-row-hover-bg);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        /* Message column specific styling */
        td.message-cell { /* Added class for targeting in JS */
            max-width: 250px; /* Reduced width to make space for buttons */
            word-wrap: break-word;
            white-space: pre-wrap; /* Preserve line breaks */
            font-size: 0.95em;
            color: #555;
        }

        /* Action Buttons Styling */
        .action-buttons {
            display: flex;
            flex-wrap: wrap; /* Allow buttons to wrap to next line if space is tight */
            gap: 5px; /* Space between buttons */
            justify-content: center; /* Center buttons within their cell */
            align-items: center;
        }
        .action-buttons a, .action-buttons button {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 0.85em;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            white-space: nowrap; /* Prevent button text from wrapping */
            text-align: center;
        }

        .btn-view { background-color: var(--btn-view-bg); }
        .btn-view:hover { background-color: var(--btn-view-hover); transform: translateY(-1px); }

        .btn-read { background-color: var(--btn-read-bg); }
        .btn-read:hover { background-color: var(--btn-read-hover); transform: translateY(-1px); }

        .btn-resolve { background-color: var(--btn-resolve-bg); }
        .btn-resolve:hover { background-color: var(--btn-resolve-hover); transform: translateY(-1px); }

        .btn-delete { background-color: var(--btn-delete-bg); }
        .btn-delete:hover { background-color: var(--btn-delete-hover); transform: translateY(-1px); }


        /* "No messages" style */
        .no-messages {
            text-align: center;
            color: var(--error-color);
            padding: 40px;
            background-color: #ffe0e0;
            border-radius: 10px;
            margin-top: 30px;
            font-size: 1.3em;
            font-style: normal;
            font-weight: 500;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.1);
            border: 1px solid var(--error-color);
        }

        /* Pagination Styles */
        .pagination {
            text-align: center;
            margin-top: 30px;
            padding: 10px 0;
        }

        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            background-color: var(--bg-card); /* Default background for page numbers */
            color: var(--dark-heading);
            border: 1px solid var(--border-light);
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        /* Styling for Previous button */
        .pagination a.prev-btn { /* Specific class for Previous */
            background-color: var(--pagination-prev-bg);
            color: var(--pagination-text-color);
            border-color: var(--pagination-prev-bg);
        }
        .pagination a.prev-btn:hover {
            background-color: #c82333; /* Darker red on hover */
            border-color: #c82333;
        }

        /* Styling for Next button */
        .pagination a.next-btn { /* Specific class for Next */
            background-color: var(--pagination-next-bg);
            color: var(--pagination-text-color);
            border-color: var(--pagination-next-bg);
        }
        .pagination a.next-btn:hover {
            background-color: #218838; /* Darker green on hover */
            border-color: #218838;
        }


        .pagination a:not(.prev-btn):not(.next-btn):hover { /* General hover for page numbers */
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .pagination span.current-page {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            cursor: default;
        }
        .pagination span.disabled {
            background-color: var(--pagination-disabled-bg);
            color: var(--pagination-disabled-color);
            cursor: not-allowed;
            border-color: var(--pagination-disabled-bg);
        }

        /* Modal Styles (for viewing full messages) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.6); /* Black w/ opacity */
            backdrop-filter: blur(5px); /* Blurred background */
            -webkit-backdrop-filter: blur(5px); /* For Safari */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 5% from the top and centered */
            padding: 30px;
            border: 1px solid #888;
            width: 80%; /* Could be more responsive */
            max-width: 700px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative; /* For close button positioning */
            animation-name: animatetop;
            animation-duration: 0.4s;
        }

        /* Add Animation */
        @keyframes animatetop {
            from {top: -300px; opacity: 0}
            to {top: 0; opacity: 1}
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
        }

        .close-button:hover,
        .close-button:focus {
            color: #333;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-header {
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-light);
            color: var(--dark-heading);
            font-size: 1.8em;
            font-weight: bold;
            text-align: left; /* Adjust for modal */
        }
        .modal-body p {
            text-align: left; /* Adjust for modal */
            margin-bottom: 10px;
            color: var(--text-color);
            font-size: 1em;
            line-height: 1.6;
        }
        .modal-body p strong {
            color: var(--dark-heading);
            min-width: 100px; /* Align labels */
            display: inline-block;
        }
        .modal-body .message-content {
            background-color: var(--bg-light);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            white-space: pre-wrap; /* For line breaks */
            word-wrap: break-word; /* For long words */
            font-style: italic;
            color: #444;
            border: 1px solid var(--border-light);
        }

        /* Responsive Table on smaller screens (Updated for Actions column) */
        @media (max-width: 1024px) {
            .container {
                padding: 15px;
            }
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-form input[type="text"],
            .filter-form input[type="date"],
            .filter-form select {
                width: 100%; /* Full width on small screens */
            }
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin-bottom: 20px;
                border: 1px solid var(--border-light);
                border-radius: 8px;
                overflow: hidden;
            }

            td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
                text-align: right;
            }

            td:last-child { /* Last td (Actions) may not need bottom border if it's the very last */
                border-bottom: 0;
            }

            td:before {
                position: absolute;
                top: 0;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: bold;
                color: var(--dark-heading);
            }
            /* Adjust padding for action buttons cell on mobile */
            td[data-label="Actions"] {
                text-align: center; /* Center buttons on mobile */
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Contact Inquiries</h1>
        <p>Here you can view all messages submitted through the contact form.</p>

        <?php if ($success_message || $error_message): ?>
            <div id="message-alert" class="message <?php echo $success_message ? 'success' : 'error'; ?>">
                <?php echo $success_message ? $success_message : $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="GET" action="manage-inquiries.php" class="filter-form">
            <div class="filter-group">
                <label for="search">Search Keywords:</label>
                <input type="text" id="search" name="search" placeholder="Name, Email, Subject, Message" value="<?php echo htmlspecialchars($search_term); ?>">
            </div>

            <div class="filter-group">
                <label for="status_filter">Status:</label>
                <select id="status_filter" name="status_filter">
                    <option value="">All Statuses</option>
                    <option value="new" <?php echo ($filter_status == 'new') ? 'selected' : ''; ?>>New</option>
                    <option value="read" <?php echo ($filter_status == 'read') ? 'selected' : ''; ?>>Read</option>
                    <option value="resolved" <?php echo ($filter_status == 'resolved') ? 'selected' : ''; ?>>Resolved</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            </div>

            <div class="filter-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            </div>

            <button type="submit">Apply Filters</button>
            <?php if (!empty($search_term) || !empty($filter_status) || !empty($start_date) || !empty($end_date)): ?>
                <button type="button" onclick="window.location.href='manage-inquiries.php'">Clear Filters</button>
            <?php endif; ?>
            <button type="button" onclick="window.location.href='export-inquiries.php?<?php echo http_build_query($_GET); ?>'">Export to CSV</button>
        </form>

        <?php if (!empty($messages)): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sent At</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th> <th>Actions</th> </tr>
                </thead>
                <tbody>
                    <?php
                    // Continuous numbering across pages
                    $display_number_start = $offset + 1;
                    foreach ($messages as $index => $msg):
                    ?>
                    <tr class="status-<?php echo htmlspecialchars($msg['status']); ?>">
                        <td data-label="#"><?php echo $display_number_start + $index; ?></td>
                        <td data-label="Sent At"><?php echo htmlspecialchars($msg['sent_at']); ?></td>
                        <td data-label="Name"><?php echo htmlspecialchars($msg['name']); ?></td>
                        <td data-label="Email"><?php echo htmlspecialchars($msg['email']); ?></td>
                        <td data-label="Phone"><?php echo htmlspecialchars($msg['phone']); ?></td>
                        <td data-label="Subject"><?php echo htmlspecialchars($msg['subject']); ?></td>
                        <td data-label="Message" class="message-cell"
                            data-full-message="<?php echo htmlspecialchars($msg['message']); ?>"
                            >
                            <?php
                            $truncated_message = htmlspecialchars($msg['message']);
                            if (strlen($truncated_message) > 100) {
                                $truncated_message = substr($truncated_message, 0, 100) . '...';
                            }
                            echo nl2br($truncated_message); // nl2br converts newlines to <br> for display
                            ?>
                        </td>
                        <td data-label="Status">
                            <?php
                            echo htmlspecialchars(ucfirst($msg['status']));
                            // Temporary Debug: Check if status is truly empty here
                            if (empty($msg['status'])) {
                                echo '  Resolved ';
                            }
                            ?>
                        </td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <button class="btn-view" data-message-id="<?php echo htmlspecialchars($msg['id']); ?>">View</button>

                                <?php if ($msg['status'] == 'new'): ?>
                                    <a href="update-inquiry-status.php?id=<?php echo $msg['id']; ?>&status=read" class="btn-read">Mark as Read</a>
                                <?php elseif ($msg['status'] == 'read'): ?>
                                    <a href="update-inquiry-status.php?id=<?php echo $msg['id']; ?>&status=resolved" class="btn-resolve">Mark as Resolved</a>
                                <?php endif; ?>

                                <a href="delete-inquiry.php?id=<?php echo $msg['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this inquiry? This action cannot be undone.');">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php
                $pagination_base_url = 'manage-inquiries.php?';
                $current_query_params = [];
                if (!empty($search_term)) $current_query_params['search'] = urlencode($search_term);
                if (!empty($filter_status)) $current_query_params['status_filter'] = urlencode($filter_status);
                if (!empty($start_date)) $current_query_params['start_date'] = urlencode($start_date);
                if (!empty($end_date)) $current_query_params['end_date'] = urlencode($end_date);

                $query_string_prefix = http_build_query($current_query_params);
                if (!empty($query_string_prefix)) {
                    $pagination_base_url .= $query_string_prefix . '&';
                }
                ?>

                <?php if ($page > 1): ?>
                    <a href="<?php echo $pagination_base_url . 'page=' . ($page - 1); ?>" class="prev-btn">Previous</a>
                <?php else: ?>
                    <span class="disabled">Previous</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current-page"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo $pagination_base_url . 'page=' . $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="<?php echo $pagination_base_url . 'page=' . ($page + 1); ?>" class="next-btn">Next</a>
                <?php else: ?>
                    <span class="disabled">Next</span>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <p class="no-messages">No contact inquiries found matching your criteria.</p>
        <?php endif; ?>
    </div>

    <div id="inquiryModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <div class="modal-header">Full Inquiry Details</div>
            <div class="modal-body">
                <p><strong>ID:</strong> <span id="modal-id"></span></p>
                <p><strong>Sent At:</strong> <span id="modal-sentat"></span></p>
                <p><strong>Name:</strong> <span id="modal-name"></span></p>
                <p><strong>Email:</strong> <span id="modal-email"></span></p>
                <p><strong>Phone:</strong> <span id="modal-phone"></span></p>
                <p><strong>Subject:</strong> <span id="modal-subject"></span></p>
                <p><strong>Message:</strong></p>
                <div id="modal-message" class="message-content"></div>
            </div>
        </div>
    </div>

    <script>
        // Get the modal elements
        var modal = document.getElementById("inquiryModal");
        var closeButton = document.getElementsByClassName("close-button")[0];
        var viewButtons = document.querySelectorAll(".btn-view");

        // Function to open the modal
        function openModal(messageData) {
            document.getElementById("modal-id").textContent = messageData.id;
            document.getElementById("modal-sentat").textContent = messageData.sentAt;
            document.getElementById("modal-name").textContent = messageData.name;
            document.getElementById("modal-email").textContent = messageData.email;
            document.getElementById("modal-phone").textContent = messageData.phone;
            document.getElementById("modal-subject").textContent = messageData.subject;
            document.getElementById("modal-message").innerHTML = messageData.fullMessage.replace(/\n/g, '<br>');
            modal.style.display = "block";
        }

        // When the user clicks on a "View" button, open the modal
        viewButtons.forEach(function(button) {
            button.addEventListener("click", function() {
                var row = this.closest('tr');
                var messageCell = row.querySelector('.message-cell');

                var messageData = {
                    id: row.querySelector('td:first-child').textContent,
                    sentAt: row.querySelector('td[data-label="Sent At"]').textContent,
                    name: row.querySelector('td[data-label="Name"]').textContent,
                    email: row.querySelector('td[data-label="Email"]').textContent,
                    phone: row.querySelector('td[data-label="Phone"]').textContent,
                    subject: row.querySelector('td[data-label="Subject"]').textContent,
                    fullMessage: messageCell.dataset.fullMessage
                };
                openModal(messageData);
            });
        });

        // When the user clicks on (x), close the modal
        closeButton.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Auto-hide success/error messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            var messageAlert = document.getElementById('message-alert');
            if (messageAlert) {
                setTimeout(function() {
                    messageAlert.style.display = 'none';
                }, 5000); // Hide after 5 seconds (5000 milliseconds)
            }
        });
    </script>
</body>
</html>