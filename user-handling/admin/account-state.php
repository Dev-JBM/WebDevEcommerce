<?php
// filepath: /home/jb_magtoto/Projects/WebDevEcommerce/user-handling/admin/suspend-account.php
session_start();
require_once '../../features/db-connection.php';

if (!isset($_GET['id']) || !isset($_GET['role']) || !isset($_GET['action'])) {
    header('Location: buyers.php');
    exit;
}

$user_id = intval($_GET['id']);
$role = $_GET['role'];
$action = $_GET['action']; // 'suspend' or 'reactivate'

// Only allow buyer or seller
if (!in_array($role, ['buyer', 'seller'])) {
    header('Location: buyers.php');
    exit;
}

$is_active = ($action === 'reactivate') ? 1 : 0;

$query = "UPDATE users SET is_active = $is_active WHERE user_id = $user_id AND role = '$role'";
mysqli_query($conn, $query);

// Redirect back to the appropriate page
if ($role === 'buyer') {
    header('Location: buyers.php');
} else {
    header('Location: sellers.php');
}
exit;
?>