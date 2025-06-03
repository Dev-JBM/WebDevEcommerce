<?php
session_start();
require_once '../../features/db-connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../homepage.html");
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
    $target = "../../images/valid-files/" . $fileName;

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

    echo "<script>alert('Application submitted successfully! Your account is now a seller.');window.location.href='../../store.php';</script>";
    exit;
}