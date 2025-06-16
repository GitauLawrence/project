<?php
session_start();
require_once '../includes/db-connect.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if property ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage-properties.php");
    exit();
}

$property_id = $_GET['id'];

// Delete property
if (deleteProperty($conn, $property_id)) {
    header("Location: manage-properties.php?success=delete");
} else {
    header("Location: manage-properties.php?error=delete");
}
exit();
?>