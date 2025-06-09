<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/features/db-connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: /homepage.php");
    exit;
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $seller_id = $_SESSION['user_id'] ?? null;

    // Get seller_id from DB if not in session
    if (!$seller_id) {
        $username = $_SESSION['username'];
        $result = $conn->query("SELECT user_id FROM users WHERE username = '$username' LIMIT 1");
        $row = $result->fetch_assoc();
        $seller_id = $row['user_id'];
    }

    // Mark as inactive instead of deleting
    $stmt = $conn->prepare("UPDATE products SET is_active = 0 WHERE product_id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $product_id, $seller_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: seller_settings.php");
exit;
