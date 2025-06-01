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
            header("Location: ../store.php");
            exit;
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Invalid username, email, or phone number.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <script>
        const message = "<?= htmlspecialchars($message) ?>";
        if (message) {
            alert(message);
            setTimeout(() => {
                window.location.href = "../homepage.html";
            }, 100);
        }
    </script>
</head>

<body>
</body>

</html>