<?php
session_start();
require_once '../db-connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$username = $_SESSION['username'];
$data = json_decode(file_get_contents('php://input'), true);

$fields = [
    'username',
    'firstname',
    'middlename',
    'lastname',
    'email',
    'bdate',
    'address',
    'telnum'
];
$updates = [];
$changedFields = [];

if (isset($data['username']) && $data['username'] !== $username) {
    $check = mysqli_query($conn, "SELECT 1 FROM users WHERE username = '" . $conn->real_escape_string($data['username']) . "' AND username != '$username' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'message' => 'Username is already taken.']);
        exit;
    }
    $updates[] = "username = '" . $conn->real_escape_string($data['username']) . "'";
    $changedFields[] = 'username';
}
if (isset($data['email'])) {
    $check = mysqli_query($conn, "SELECT 1 FROM users WHERE email = '" . $conn->real_escape_string($data['email']) . "' AND username != '$username' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email is already taken.']);
        exit;
    }
    $updates[] = "email = '" . $conn->real_escape_string($data['email']) . "'";
    $changedFields[] = 'email';
}
if (isset($data['telnum'])) {
    $check = mysqli_query($conn, "SELECT 1 FROM users WHERE phone_number = '" . $conn->real_escape_string($data['telnum']) . "' AND username != '$username' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'message' => 'Phone number is already taken.']);
        exit;
    }
    $updates[] = "phone_number = '" . $conn->real_escape_string($data['telnum']) . "'";
    $changedFields[] = 'phone_number';
}

foreach ($fields as $field) {
    if (in_array($field, ['username', 'email', 'telnum'])) continue; // Already handled above
    if (isset($data[$field])) {
        $col = $field === 'bdate' ? 'birthdate' : $field;
        $val = $conn->real_escape_string($data[$field]);
        $updates[] = "$col = '$val'";
        $changedFields[] = $col;
    }
}

if (!empty($data['new_password'])) {
    if (empty($data['old_password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is required to change password.']);
        exit;
    }
    $userQ = mysqli_query($conn, "SELECT password_hash FROM users WHERE username = '$username'");
    $user = mysqli_fetch_assoc($userQ);
    if (!$user || !password_verify($data['old_password'], $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
        exit;
    }
    $newHash = password_hash($data['new_password'], PASSWORD_DEFAULT);
    $updates[] = "password_hash = '$newHash'";
    $changedFields[] = 'password_hash';
}

if ($updates) {
    $updates[] = "updated_at = NOW()";
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE username = '$username'";
    if (mysqli_query($conn, $sql)) {
        // Update session username if it was changed
        if (in_array('username', $changedFields)) {
            $_SESSION['username'] = $data['username'];
        }
        echo json_encode(['success' => true, 'updated_fields' => $changedFields, 'updated_at' => date('Y-m-d H:i:s')]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed.']);
    }
}
