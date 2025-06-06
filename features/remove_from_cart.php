<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db-connection.php';

if (isset($_POST['cart_item_id'])) {
    // Single remove
    $cart_item_id = intval($_POST['cart_item_id']);
    $query = "DELETE FROM cart_items WHERE cart_item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cart_item_id);
    $stmt->execute();
} elseif (isset($_POST['remove_selected']) && !empty($_POST['cart_item_ids'])) {
    // Bulk remove
    $ids = array_map('intval', $_POST['cart_item_ids']);
    $in = implode(',', $ids);
    $query = "DELETE FROM cart_items WHERE cart_item_id IN ($in)";
    $conn->query($query);
}
header("Location: ../cart.php");
exit;
