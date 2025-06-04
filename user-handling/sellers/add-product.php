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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $seller_id = intval($_POST['seller_id']);
    $name = trim($_POST['productName']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['NumberStock']);
    $description = trim($_POST['description']);
    $gender = $_POST['gender'] ?? '';
    $category = $_POST['category'] ?? '';
    $type = '';
    if ($category === 'Clothes') {
        $type = $_POST['clothesType'] ?? '';
    } elseif ($category === 'Accessories') {
        $type = $_POST['accessoriesType'] ?? '';
    }
    $sizes = isset($_POST['size']) ? implode(',', $_POST['size']) : '';
    $colors = isset($_POST['colors']) ? implode(',', $_POST['colors']) : '';
    // Add more fields as needed (category, sizes, colors, etc.)

    // Handle image upload
    $file = $_FILES['fileInput'];
    $allowedTypes = [
        'image/jpeg',
        'image/jpg',
        'image/pjpeg',
        'image/x-jpg',
        'image/png'
    ];
    $maxSize = 5 * 1024 * 1024;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowedExtensions)) {
        die("Unsupported file extension. Only JPG and PNG are allowed.");
    }

    // Check if file is a real image
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        die("Uploaded file is not a valid image.");
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        die("File size exceeds 5MB.");
    }

    $fileName = uniqid() . "_" . basename($file['name']);
    $target = "../../images/products/" . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        die("Failed to upload product image.");
    }

    // Insert product
    $stmt = $conn->prepare("INSERT INTO products (seller_id, name, description, price, gender, category, type, stock_quantity, sizes_available, colors_available, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdsssisss", $seller_id, $name, $description, $price, $gender, $category, $type, $stock, $sizes, $colors, $fileName);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Product added successfully!');window.location.href='../sellers/seller_settings.php';</script>";
    exit;
}
