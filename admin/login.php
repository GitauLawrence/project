<?php
session_start();
require_once '../includes/db-connect.php'; // Make sure this connects to your DB

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

// Process form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Check credentials without hashing
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    }
}
// session_start();
// require_once '../includes/db-connect.php';
// require_once '../includes/functions.php';

// // Check if already logged in
// if (isset($_SESSION['admin_id'])) {
//     header("Location: dashboard.php");
//     exit();
// }

// $error = '';

// // Process login form
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $username = trim($_POST['username']);
//     $password = $_POST['password'];
    
//     if (empty($username) || empty($password)) {
//         $error = "Please enter both username and password";
//     } else {
//         // Get admin user
//         $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
//         $stmt->bind_param("s", $username);
//         $stmt->execute();
//         $result = $stmt->get_result();
        
//         if ($result->num_rows == 1) {
//             $admin = $result->fetch_assoc();
//             // Verify password
//             if (password_verify($password, $admin['password'])) {
//                 // Set session
//                 $_SESSION['admin_id'] = $admin['id'];
//                 $_SESSION['admin_username'] = $admin['username'];
                
//                 // Redirect to dashboard
//                 header("Location: dashboard.php");
//                 exit();
//             } else {
//                 $error = "Invalid username or password";
//             }
//         } else {
//             $error = "Invalid username or password";
//         }
        
//         $stmt->close();
//     }
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SELLAM Real Estate</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-login-body">
    <div class="admin-login-container">
        <div class="login-header">
            <img src="../assets/images/logo.png.jpg" alt="SELLAM Logo" class="login-logo">
            <h1>Admin Login</h1>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form class="login-form" action="login.php" method="POST">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
        
        <div class="login-footer">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Website</a>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>