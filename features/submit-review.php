<?php
session_start();
require_once 'db-connection.php';

function js_alert_and_exit($msg)
{
    echo "<script>alert('$msg'); window.history.back();</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
    $product_id = intval($_POST['product_id'] ?? 0);
    $order_size = $_POST['order_size'] ?? '';
    $order_color = $_POST['order_color'] ?? '';
    $rating = intval($_POST['rating'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $username = mysqli_real_escape_string($conn, $_SESSION['username']);

    // 1. Check user is logged in (already checked above)
    // 2. Get user id
    $user_query = "SELECT user_id FROM users WHERE username = '$username' LIMIT 1";
    $user_result = mysqli_query($conn, $user_query);
    $user = mysqli_fetch_assoc($user_result);
    if (!$user) js_alert_and_exit("User not found.");
    $buyer_id = $user['user_id'];

    // 3. Check product exists
    $product_query = "SELECT product_id FROM products WHERE product_id = $product_id LIMIT 1";
    $product_result = mysqli_query($conn, $product_query);
    if (!mysqli_fetch_assoc($product_result)) js_alert_and_exit("Product does not exist.");

    // 4. Check order item (if used) belongs to user and matches product
    $order_item_id = isset($_POST['order_item_id']) ? intval($_POST['order_item_id']) : null;
    if ($order_item_id) {
        $oi_query = "SELECT oi.order_item_id FROM order_items oi
                     JOIN orders o ON oi.order_id = o.order_id
                     WHERE oi.order_item_id = $order_item_id AND oi.product_id = $product_id AND o.buyer_id = $buyer_id LIMIT 1";
        $oi_result = mysqli_query($conn, $oi_query);
        if (!mysqli_fetch_assoc($oi_result)) js_alert_and_exit("Order item does not belong to you or does not match the product.");
    }

    // 5. Check rating is between 1 and 5
    if ($rating < 1 || $rating > 5) js_alert_and_exit("Rating must be between 1 and 5.");

    // 6. Check review text is not empty
    if (empty($description)) js_alert_and_exit("Review text cannot be empty.");

    // 7. Check for duplicate review for this order item
    if ($order_item_id) {
        $dup_query = "SELECT review_id FROM product_reviews WHERE order_item_id = $order_item_id AND buyer_id = $buyer_id LIMIT 1";
        $dup_result = mysqli_query($conn, $dup_query);
        if (mysqli_fetch_assoc($dup_result)) js_alert_and_exit("You have already reviewed this order.");
    }

    // Insert review
    $stmt = $conn->prepare("INSERT INTO product_reviews (product_id, buyer_id, order_item_id, size, color, rating, review_text) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissis", $product_id, $buyer_id, $order_item_id, $order_size, $order_color, $rating, $description);
    $stmt->execute();

    // Fetch user role for redirect
    $user_query = "SELECT role FROM users WHERE user_id = $buyer_id LIMIT 1";
    $user_result = mysqli_query($conn, $user_query);
    $user = mysqli_fetch_assoc($user_result);
    $role = $user['role'] ?? 'buyer';

    if ($role === 'seller') {
        $redirect = '../../user-handling/sellers/seller_settings.php';
    } else {
        $redirect = '../../user-handling/buyers/buyer_settings.php';
    }
    echo "<script>alert('Your review was submitted successfully!'); window.location.href='$redirect';</script>";
    exit;
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
