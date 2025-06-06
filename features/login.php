<?php
session_start();
require_once 'db-connection.php'; // Use your actual connection file name

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userInput = $conn->real_escape_string($_POST['login_username']);
    $password = $_POST['login_password'] ?? '';

    // Query to find user by username, email, or phone
    $query = "SELECT * FROM users 
              WHERE username = '$userInput' 
              OR email = '$userInput' 
              OR phone_number = '$userInput' 
              LIMIT 1";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['username'] = $user['username'];
            // Role-based redirection
            if (isset($user['role'])) {
                if ($user['role'] === 'admin') {
                    header("Location: ../user-handling/admin/admin.php");
                    exit;
                } elseif ($user['role'] === 'buyer' || $user['role'] === 'seller') {
                    header("Location: ../store.php");
                    exit;
                } else {
                    $message = "Unknown user role.";
                }
            } else {
                $message = "User role not set.";
            }
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Invalid username, email, or phone number.";
    }

    if (!empty($message)) {
        $_SESSION['login_error'] = $message;
        header("Location: ../homepage.php");
        exit;
    }


    $conn->close();
}
?>