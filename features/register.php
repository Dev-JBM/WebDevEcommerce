<?php

require_once 'db-connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and get form data
    $username = $conn->real_escape_string($_POST['register_username']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone_number']);
    $password = $_POST['register_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $address = $conn->real_escape_string($_POST['address']);
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $middlename = $conn->real_escape_string($_POST['middlename']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $birthdate = $conn->real_escape_string($_POST['birthdate']);


    $passwordPattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';

    if (!preg_match($passwordPattern, $password)) {
        $message = "Password must be at least 8 characters long and should contain at least one UPPERCASE letter, one LOWERCASE letter, one NUMBER, and one SPECIAL character.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);


        $checkUsernameQuery = "SELECT * FROM users WHERE username = '$username'";
        $checkUsernameResult = mysqli_query($conn, $checkUsernameQuery);

        if (mysqli_num_rows($checkUsernameResult) > 0) {
            $message = "Username is already taken.";
        } else {

            $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
            $checkEmailResult = mysqli_query($conn, $checkEmailQuery);

            if (mysqli_num_rows($checkEmailResult) > 0) {
                $message = "Email is already taken.";
            } else {

                $checkPhoneQuery = "SELECT * FROM users WHERE phone_number = '$phone'";
                $checkPhoneResult = mysqli_query($conn, $checkPhoneQuery);

                if (mysqli_num_rows($checkPhoneResult) > 0) {
                    $message = "Phone number is already taken.";
                } else {

                    $sql = "INSERT INTO users (username, email, phone_number, password_hash, address, firstname, middlename, lastname, birthdate)
                            VALUES ('$username', '$email', '$phone', '$hashed_password', '$address', '$firstname', '$middlename', '$lastname', '$birthdate')";

                    if (mysqli_query($conn, $sql)) {
                        $user_id = mysqli_insert_id($conn);
                        $insert_buyer = "INSERT INTO buyer_profiles (buyer_id, first_name, middle_name, last_name, birthdate, phone_number, address)
                                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($insert_buyer);
                        $stmt->bind_param(
                            "issssss",
                            $user_id,
                            $firstname,
                            $middlename,
                            $lastname,
                            $birthdate,
                            $phone,
                            $address
                        );
                        $stmt->execute();
                        $message = "Registration Successful";
                    } else {
                        $message = "Error: Unable to register. Please try again later.";
                    }
                }
            }
        }
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <script>
        const message = "<?= htmlspecialchars($message) ?>";
        alert(message);
        setTimeout(() => {
            window.location.href = "../homepage.php";
        }, 100);
    </script>
</head>

<body>
</body>

</html>