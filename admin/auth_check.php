<?php
// Authentication check - include this at the top of admin pages
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get account info
include '../includes/db_connect.php';
$adminId = (int) $_SESSION['admin_id'];
$adminQuery = $conn->query("SELECT username, email FROM admin_users WHERE id = $adminId");
$adminInfo = $adminQuery->fetch_assoc();

if (!$adminInfo) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Lightweight role handling without changing the database:
// treat username "admin" as the main admin, everyone else as a regular gallery user.
// This lets you have a separate user account area while reusing the same login table.
$adminInfo['role'] = (strtolower($adminInfo['username']) === 'admin') ? 'admin' : 'user';

// Also keep role in session for convenience if other pages want it
$_SESSION['user_role'] = $adminInfo['role'];
?>

