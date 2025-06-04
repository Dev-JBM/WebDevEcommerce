<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../features/db-connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../homepage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: edit-products.php");
    exit;
}

// Get seller info
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$seller_id = $user['user_id'];

// Get product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['product_id']) ? intval($_POST['product_id']) : 0);
if (!$product_id) {
    echo "No product selected.";
    exit;
}

// Get form data
$name = $_POST['productName'] ?? '';
$description = $_POST['description'] ?? '';
$stock = $_POST['NumberStock'] ?? '';
$price = $_POST['price'] ?? '';
$gender = $_POST['gender'] ?? '';
$category = $_POST['category'] ?? '';
$type = $_POST['clothesType'] ?? ($_POST['accessoriesType'] ?? '');
$sizes = isset($_POST['size']) ? implode(',', $_POST['size']) : '';
$colors = $_POST['colors'] ?? '';

// Check if a new image was uploaded
$imageUpdated = false;
if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
    $imgName = uniqid('prod_') . '_' . basename($_FILES['fileInput']['name']);
    $targetDir = '../../images/products/';
    $targetFile = $targetDir . $imgName;
    if (move_uploaded_file($_FILES['fileInput']['tmp_name'], $targetFile)) {
        $imageUpdated = true;
    }
}

// Build SQL
if ($imageUpdated) {
    $sql = "UPDATE products SET name=?, description=?, stock_quantity=?, price=?, gender=?, category=?, type=?, sizes_available=?, colors_available=?, image_path=? WHERE product_id=? AND seller_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssii", $name, $description, $stock, $price, $gender, $category, $type, $sizes, $colors, $imgName, $product_id, $seller_id);
} else {
    $sql = "UPDATE products SET name=?, description=?, stock_quantity=?, price=?, gender=?, category=?, type=?, sizes_available=?, colors_available=? WHERE product_id=? AND seller_id=?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssssssssssi", $name, $description, $stock, $price, $gender, $category, $type, $sizes, $colors, $product_id, $seller_id);
}

if ($stmt->execute()) {
    header("Location: edit-products.php?id=$product_id&success=1");
    exit;
} else {
    echo "Error updating product: " . $stmt->error;
}
$stmt->close();
$conn->close();
