<?php
// Authentication check - include this at the top of admin pages
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get admin info
include '../includes/db_connect.php';
$adminId = $_SESSION['admin_id'];
$adminQuery = $conn->query("SELECT username, email FROM admin_users WHERE id = $adminId");
$adminInfo = $adminQuery->fetch_assoc();

if (!$adminInfo) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

