<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/features/db-connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: /homepage.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_SESSION['username'];
    $fullname = trim($_POST['fullname'] ?? '');
    $business_name = trim($_POST['business_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $business_address = trim($_POST['business_address'] ?? '');

    // File upload
    $file = $_FILES['valid_id_file'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    $maxSize = 5 * 1024 * 1024;

    if (!in_array($file['type'], $allowedTypes)) {
        die("Unsupported file type.");
    }
    if ($file['size'] > $maxSize) {
        die("File size exceeds 5MB.");
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . "_" . basename($file['name']);
    $target = "/images/valid-files/" . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        die("Failed to upload file.");
    }

    // Insert into seller_applications
    $stmt = $conn->prepare("INSERT INTO seller_applications (username, business_name, business_address, phone_number, valid_id_file) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $business_name, $business_address, $phone_number, $fileName);
    $stmt->execute();
    $stmt->close();

    // Update user role to 'seller'
    $updateRole = $conn->prepare("UPDATE users SET role = 'seller' WHERE username = ?");
    $updateRole->bind_param("s", $username);
    $updateRole->execute();
    $updateRole->close();

    // Get user_id for seller_profiles
    $getUserId = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $getUserId->bind_param("s", $username);
    $getUserId->execute();
    $getUserId->bind_result($user_id);
    $getUserId->fetch();
    $getUserId->close();

    // Check if already in seller_profiles
    $check = $conn->prepare("SELECT seller_id FROM seller_profiles WHERE seller_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        // Insert new seller profile
        $insert = $conn->prepare("INSERT INTO seller_profiles (seller_id, shop_name, business_address, phone_number, valid_id_image_path) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("issss", $user_id, $business_name, $business_address, $phone_number, $fileName);
        $insert->execute();
        $insert->close();
    }
    $check->close();

    echo "<script>alert('Application submitted successfully! Your account is now a seller.');window.location.href='/store.php';</script>";
    exit;
}