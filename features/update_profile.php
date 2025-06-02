<?php
header('Content-Type: application/json');
session_start();
require_once 'db-connection.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

$fields = [
    'firstname',
    'middlename',
    'lastname',
    'email',
    'birthdate',
    'address',
    'phone_number'
];

$updates = [];
foreach ($fields as $field) {
    $new = trim($data[$field] ?? '');
    if ($new !== $user[$field]) {
        $safe = mysqli_real_escape_string($conn, $new);
        $updates[] = "$field = '$safe'";
    }
}

// Password update logic
if (
    !empty($data['password']) ||
    !empty($data['newpassword']) ||
    !empty($data['confirmnewpassword'])
) {
    $oldPass = $data['password'] ?? '';
    $newPass = $data['newpassword'] ?? '';
    $confirmNewPass = $data['confirmnewpassword'] ?? '';

    if (!password_verify($oldPass, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
        exit;
    }
    if ($newPass !== $confirmNewPass) {
        echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
        exit;
    }
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $newPass)) {
        echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.']);
        exit;
    }
    $hashed = password_hash($newPass, PASSWORD_DEFAULT);
    $updates[] = "password_hash = '$hashed'";
}

if ($updates) {
    $updates[] = "updated_at = NOW()";
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE username = '$username'";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile: ' . mysqli_error($conn)]);
        exit;
    }
} else {
    echo json_encode(['success' => true]);
    exit;
}
