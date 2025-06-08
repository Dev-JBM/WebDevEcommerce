<?php
session_start();
require_once 'db-connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../homepage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- BUY NOW LOGIC ---
    if (
        isset($_POST['product_id'], $_POST['size'], $_POST['color'], $_POST['quantity'], $_POST['price'], $_POST['total'], $_POST['payment_method'])
        && !isset($_POST['cart_item_ids'])
    ) {
        $product_id = intval($_POST['product_id']);
        $size = $_POST['size'];
        $color = $_POST['color'];
        $quantity = intval($_POST['quantity']);
        $price = floatval($_POST['price']);
        $total = floatval($_POST['total']);
        $payment_method = $_POST['payment_method'];
        $buyer_id = intval($_POST['buyer_id']);
        $address = $_POST['shipping_address'] ?? '';

        // Get seller_id from product
        $stmt = $conn->prepare("SELECT seller_id FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            $seller_id = $row['seller_id'];
        } else {
            header("Location: ../product.php?error=productnotfound");
            exit;
        }
        $stmt->close();

        $order_stmt = $conn->prepare("INSERT INTO orders (buyer_id, total_amount, shipping_address, payment_method) VALUES (?, ?, ?, ?)");
        $order_stmt->bind_param("idss", $buyer_id, $total, $address, $payment_method);
        $order_stmt->execute();
        $order_id = $order_stmt->insert_id;

        $oi_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, seller_id, quantity, price_at_purchase, size, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $oi_stmt->bind_param(
            "iiiidss",
            $order_id,
            $product_id,
            $seller_id,
            $quantity,
            $price,
            $size,
            $color
        );
        $oi_stmt->execute();

        // Deduct stock
        $stock_stmt = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
        $stock_stmt->bind_param("ii", $quantity, $product_id);
        $stock_stmt->execute();

        header("Location: ../product.php?id=" . $product_id . "&success=1");
        exit;
    }

    // --- CART CHECKOUT LOGIC ---
    if (!empty($_POST['cart_item_ids']) && isset($_POST['payment_method'])) {
        $cart_item_ids = array_map('intval', $_POST['cart_item_ids']);
        $payment_method = $_POST['payment_method'];
        $buyer_id = intval($_POST['buyer_id']);
        $address = $_POST['shipping_address'] ?? '';

        $placeholders = implode(',', array_fill(0, count($cart_item_ids), '?'));
        $types = str_repeat('i', count($cart_item_ids));
        $stmt = $conn->prepare("SELECT ci.*, p.seller_id, p.price, p.name FROM cart_items ci JOIN products p ON ci.product_id = p.product_id WHERE ci.cart_item_id IN ($placeholders)");
        $stmt->bind_param($types, ...$cart_item_ids);
        $stmt->execute();
        $result = $stmt->get_result();

        $total_amount = 0;
        $order_items = [];
        while ($row = $result->fetch_assoc()) {
            $item_total = $row['price'] * $row['quantity'];
            $total_amount += $item_total;
            $order_items[] = $row;
        }

        $order_stmt = $conn->prepare("INSERT INTO orders (buyer_id, total_amount, shipping_address, payment_method) VALUES (?, ?, ?, ?)");
        $order_stmt->bind_param("idss", $buyer_id, $total_amount, $address, $payment_method);
        $order_stmt->execute();
        $order_id = $order_stmt->insert_id;

        foreach ($order_items as $item) {
            $oi_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, seller_id, quantity, price_at_purchase, size, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $oi_stmt->bind_param(
                "iiiidss",
                $order_id,
                $item['product_id'],
                $item['seller_id'],
                $item['quantity'],
                $item['price'],
                $item['size'],
                $item['color']
            );
            $oi_stmt->execute();

            $stock_stmt = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
            $stock_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $stock_stmt->execute();
        }

        // Remove items from cart
        $del_stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_item_id IN ($placeholders)");
        $del_stmt->bind_param($types, ...$cart_item_ids);
        $del_stmt->execute();

        header("Location: ../product.php?id=" . $product_id . "&success=1");
        exit;
    }
}

// Invalid request
header("Location: ../product.php?error=1");
exit;
