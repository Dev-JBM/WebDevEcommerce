<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../features/db-connection.php';

if (isset($_SESSION['user_id'])) {
    $adminId = $_SESSION['user_id'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$adminId' AND role = 'admin' LIMIT 1");
    if ($row = mysqli_fetch_assoc($result)) {
        $user = $row;
        $adminUsername = htmlspecialchars($row['username']);
        $adminImage = !empty($row['image'])
            ? '../../images/profiles/' . htmlspecialchars($row['image'])
            : '../../images/profile-circle-svgrepo-com.png';
    }
}

if (isset($_SESSION['user_id']) && isset($_FILES['fileImg']['name']) && $_FILES['fileImg']['name'] !== '') {
    $adminId = $_SESSION['user_id'];
    $src = $_FILES["fileImg"]["tmp_name"];
    $imageName = uniqid() . "_" . basename($_FILES["fileImg"]["name"]);
    $target = "../../images/profiles/" . $imageName;

    if (is_uploaded_file($src)) {
        if (move_uploaded_file($src, $target)) {
            $query = "UPDATE users SET image = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "si", $imageName, $adminId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            // Refresh to show new image
            header("Location: admin-profile.php");
            exit;
        }
    }
}

if (isset($_SESSION['user_id'])) {
    $adminId = $_SESSION['user_id'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$adminId' AND role = 'admin'");
    if (!$result) {
        die('Query error: ' . mysqli_error($conn));
    }
    if ($row = mysqli_fetch_assoc($result)) {
        $user = $row;
        $adminUsername = htmlspecialchars($row['username']);
    }
}

$sellerResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'seller'");
$sellerRow = mysqli_fetch_assoc($sellerResult);
$totalSellers = $sellerRow['total'];

$buyerResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'buyer'");
$buyerRow = mysqli_fetch_assoc($buyerResult);
$totalBuyers = $buyerRow['total'];

// Fetch all orders with buyer info
$ordersResult = mysqli_query($conn, "
    SELECT 
        o.order_id, o.buyer_id, o.order_date, o.total_amount, o.shipping_address, o.payment_status, o.payment_method,
        u.username AS buyer_username, u.email AS buyer_email
    FROM orders o
    JOIN users u ON o.buyer_id = u.user_id
    ORDER BY o.order_date DESC
");
$orders = [];
if ($ordersResult) {
    while ($row = mysqli_fetch_assoc($ordersResult)) {
        $orders[$row['order_id']] = $row;
        $orders[$row['order_id']]['items'] = [];
    }
}

// Fetch all order items and attach to orders
$orderIds = array_keys($orders);
if (!empty($orderIds)) {
    $orderIdsStr = implode(',', array_map('intval', $orderIds));
    $itemsResult = mysqli_query($conn, "
        SELECT 
            oi.*, 
            p.name AS product_name, p.description, p.stock_quantity, p.gender, p.category, p.type, 
            p.sizes_available, p.colors_available, p.image_path, p.created_at, p.updated_at,
            u.username AS seller_username, u.email AS seller_email
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        JOIN users u ON oi.seller_id = u.user_id
        WHERE oi.order_id IN ($orderIdsStr)
    ");
    if ($itemsResult) {
        while ($item = mysqli_fetch_assoc($itemsResult)) {
            $orders[$item['order_id']]['items'][] = $item;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wear Dyans</title>
    <link rel="stylesheet" href="/style/admin.css">
    <link rel="stylesheet" href="/style/view-data.css">
    <link rel="stylesheet" href="/style/settings.css">
    <link rel="stylesheet" href="/style/logout.css">
    <link href="https://fonts.googleapis.com/css2?family=Mynerve&family=Mandali&family=Aoboshi+One&family=Inter:ital,wght@0,100..900;1,100..900&family=MuseoModerno:ital,wght@0,100..900;1,100..900&family=Podkova:wght@400..800&display=swap" rel="stylesheet">
    <style>
        .my-account {
            display: flex;
        }
    </style>
</head>

<body>
    <nav class="admin-sidebar">
        <div class="admin-profile">
            <img src="<?= htmlspecialchars($adminImage) ?>" alt="Admin Profile" />
            <h2>Hello, <?= $adminUsername ?></h2>
            <p>Admin</p>
        </div>
        <ul class="admin-nav-list">
            <li><a href="admin.php">Dashboard</a></li>
            <hr style="width: 80%;">
            <li><a href="buyers.php">Buyers</a></li>
            <hr style="width: 80%;">
            <li><a href="sellers.php">Sellers</a></li>
            <hr style="width: 80%;">
            <li><a href="products-admin.php">Products</a></li>
            <hr style="width: 80%;">
            <li><a href="orders.php">Orders</a></li>
            <hr style="width: 80%;">
            <li><a href="admin-profile.php">Profile Settings</a></li>
        </ul>
    </nav>

    <main>
        <header class="header">
            <div class="left-header">
                <a href="/homepage.php">Wear Dyans</a>
            </div>
            <div class="sub-menu-link" id="logout" style="color: white;">
                <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                    <path
                        d="M160 256a16 16 0 0116-16h144V136c0-32-33.79-56-64-56H104a56.06 56.06 0 00-56 56v240a56.06 56.06 0 0056 56h160a56.06 56.06 0 0056-56V272H176a16 16 0 01-16-16zM459.31 244.69l-80-80a16 16 0 00-22.62 22.62L409.37 240H320v32h89.37l-52.68 52.69a16 16 0 1022.62 22.62l80-80a16 16 0 000-22.62z" />
                </svg>
                <p class="sub-menu-text">Log Out</p>
                <span>></span>
            </div>
        </header>
        <div class="profile-menu-wrapper" style="display: flex; flex-direction: column; align-items: center;">
            <!-- Profile Image Column -->
            <div class="profile-container">
                <div class="userImg">
                    <form action="" enctype="multipart/form-data" method="post">
                        <div class="userImg-pic">
                            <img src="<?= htmlspecialchars($adminImage); ?>" id="image">
                            <div class="leftRound" id="cancelPicBtn" style="display: none;">
                                <span class="btn-icon" aria-label="Cancel" title="Cancel">&#10006;</span>
                            </div>
                            <div class="rightRound" id="uploadBtn">
                                <input type="file" name="fileImg" id="fileImg" accept=".jpg, .jpeg, .png">
                                <span class="btn-icon" aria-label="Choose" title="Choose">&#128247;</span>
                            </div>
                            <div class="rightRound" id="confirmPicBtn" style="display: none; background: #00C851;">
                                <input type="submit" id="check" value="">
                                <span class="btn-icon" aria-label="Confirm" title="Confirm">&#10004;</span>
                            </div>
                        </div>
                        <p>Edit Profile Picture - Click Camera Icon<br>Allowed format: .jpg, .jpeg, .png</p>
                    </form>
                </div>
            </div>
            <!-- Profile Info Column -->
            <div class="my-account" style="display:flex;">
                <div class="title">
                    <p>Profile Settings</p>
                </div>
                <div class="profile-content">
                    <form action="" enctype="multipart/form-data" method="post">
                        <div class="inputs-container">
                            <div class="input-box">
                                <label class="add-product-label" for="username">Username:</label>
                                <input type="text" id="username" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                            </div>
                            <div class="input-box">
                                <label class="add-product-label" for="firstname">First Name:</label>
                                <input type="text" id="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" readonly>
                            </div>
                            <div class="input-box">
                                <label class="add-product-label" for="middlename">Middle Name:</label>
                                <input type="text" id="middlename" value="<?= htmlspecialchars($user['middlename']) ?>" readonly>
                            </div>
                            <div class="input-box">
                                <label class="add-product-label" for="lastname">Last Name:</label>
                                <input type="text" id="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" readonly>
                            </div>
                            <div class="input-box">
                                <label class="add-product-label" for="email">Email:</label>
                                <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                            </div>
                            <div class="input-box">
                                <label class="add-product-label" for="birthdate">Birthdate:</label>
                                <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($user['birthdate']) ?>" readonly>
                            </div>
                            <div class="input-box">
                                <label class="add-product-label" for="address">Address:</label>
                                <input type="text" id="address" value="<?= htmlspecialchars($user['address']) ?>" readonly>
                            </div>
                            <div class="input-box">
                                <label class="add-product-label" for="phone_number">Phone Number:</label>
                                <input type="tel" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" readonly pattern="[0-9]{10,15}">
                            </div>
                            <div class="update-password-container">
                                <hr>
                                <p class="update-password">Update Password</p>
                                <hr>
                            </div>
                            <div class="input-box">
                                <label class="add-product-label" for="password">Enter Password:</label>
                                <input class="password-input" type="password" id="password" name="password" readonly>
                                <span class="toggle-password">
                                    <img class="eye-close" src="/images/eye-close-svgrepo-com.svg" style="display:inline;">
                                    <img class="eye-open" src="/images/eye-2-svgrepo-com.svg" style="display:none;">
                                </span>
                            </div>
                            <div class="input-box">
                                <label class="add-product-label" for="newpassword">New Password:</label>
                                <input class="password-input" type="password" id="newpassword" name="newpassword" readonly>
                                <span class="toggle-password">
                                    <img class="eye-close" src="/images/eye-close-svgrepo-com.svg" style="display:inline;">
                                    <img class="eye-open" src="/images/eye-2-svgrepo-com.svg" style="display:none;">
                                </span>
                            </div>
                            <div class="input-box">
                                <label class="add-product-label" for="confirmnewpassword" style="font-size: 22px;">Confirm New Password:</label>
                                <input class="password-input" type="password" id="confirmnewpassword" name="confirmnewpassword" readonly>
                                <span class="toggle-password">
                                    <img class="eye-close" src="/images/eye-close-svgrepo-com.svg" style="display:inline;">
                                    <img class="eye-open" src="/images/eye-2-svgrepo-com.svg" style="display:none;">
                                </span>
                            </div>
                        </div>
                        <div class="edit-btn-container">
                            <button id="editProfileBtn" class="profile-edit-btn" type="button">Edit</button>
                            <button id="updateProfileBtn" class="profile-edit-btn" type="button" style="display:none;">Update</button>
                            <button id="cancelEditBtn" class="profile-edit-btn" type="button" style="display:none;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <div id="orderModal" class="buyer-modal" style="display:none;">
        <div class="buyer-modal-content" style="max-width:600px;">
            <span class="buyer-modal-close" id="orderModalClose">&times;</span>
            <h2>Order Details</h2>
            <table class="buyer-modal-table">
                <tr>
                    <th>Order ID</th>
                    <td id="orderModalId"></td>
                </tr>
                <tr>
                    <th>Order Date</th>
                    <td id="orderModalDate"></td>
                </tr>
                <tr>
                    <th>Buyer</th>
                    <td id="orderModalBuyer"></td>
                </tr>
                <tr>
                    <th>Shipping Address</th>
                    <td id="orderModalAddress"></td>
                </tr>
                <tr>
                    <th>Payment Method</th>
                    <td id="orderModalPayment"></td>
                </tr>
            </table>
            <h3 style="margin-top:1.5em;">Ordered Products</h3>
            <table class="buyer-modal-table" id="orderModalProductsTable">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Total Price</th>
                        <th>Seller</th>
                    </tr>
                </thead>
                <tbody id="orderModalProductsBody">
                    <!-- JS will fill this -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- FOR LOGOUT OPTION -->
    <div id="logoutModal" class="logout-modal">
        <div class="logout-modal-content">
            <svg width="48" height="48" fill="none" viewBox="0 0 24 24" style="margin-bottom: 1em;">
                <circle cx="12" cy="12" r="12" fill="#ffe5e5" />
                <path d="M12 8v4" stroke="#e74c3c" stroke-width="2" stroke-linecap="round" />
                <circle cx="12" cy="16" r="1" fill="#e74c3c" />
            </svg>
            <p class="logout-modal-title">Log Out?</p>
            <p class="logout-modal-desc">Are you sure you want to logout?</p>
            <div class="logout-modal-actions">
                <button id="logoutYes" class="logout-btn logout-btn-yes">Yes</button>
                <button id="logoutNo" class="logout-btn logout-btn-no">No</button>
            </div>
        </div>
    </div>
    <script>
        // Profile image logic
        const image = document.getElementById("image");
        const fileImg = document.getElementById("fileImg");
        const cancelPicBtn = document.getElementById("cancelPicBtn");
        const confirmPicBtn = document.getElementById("confirmPicBtn");
        const uploadBtn = document.getElementById("uploadBtn");
        let previousImageSrc = image ? image.src : "";

        if (fileImg) {
            fileImg.onchange = function() {
                const file = this.files[0];
                if (file) {
                    image.src = URL.createObjectURL(file);
                    cancelPicBtn.style.display = "flex";
                    confirmPicBtn.style.display = "flex";
                    uploadBtn.style.display = "none";
                }
            };
        }

        if (cancelPicBtn) {
            cancelPicBtn.onclick = function() {
                image.src = previousImageSrc;
                cancelPicBtn.style.display = "none";
                confirmPicBtn.style.display = "none";
                uploadBtn.style.display = "flex";
                fileImg.value = "";
            };
        }

        if (confirmPicBtn) {
            confirmPicBtn.onclick = function() {
                // Form will submit on click of the check icon (input[type=submit])
                cancelPicBtn.style.display = "none";
                confirmPicBtn.style.display = "none";
                uploadBtn.style.display = "flex";
            };
        }

        // Password show/hide logic
        function setupPasswordToggle(span) {
            const container = span.parentElement;
            const input = container.querySelector('.password-input') || container.querySelector('input[type="password"]');
            const eyeOpen = span.querySelector('.eye-open');
            const eyeClose = span.querySelector('.eye-close');

            function showPassword() {
                input.type = 'text';
                eyeOpen.style.display = 'inline';
                eyeClose.style.display = 'none';
            }

            function hidePassword() {
                input.type = 'password';
                eyeOpen.style.display = 'none';
                eyeClose.style.display = 'inline';
            }

            span.addEventListener('mousedown', showPassword);
            span.addEventListener('mouseup', hidePassword);
            span.addEventListener('mouseleave', hidePassword);
        }
        document.querySelectorAll('.toggle-password').forEach(setupPasswordToggle);

        // Edit/Update/Cancel logic (reuse from seller_settings.php)
        const profileFields = [
            'username', 'firstname', 'middlename', 'lastname', 'email', 'birthdate', 'address', 'phone_number',
            'password', 'newpassword', 'confirmnewpassword'
        ];
        const originalValues = {};
        const editBtn = document.getElementById('editProfileBtn');
        const updateBtn = document.getElementById('updateProfileBtn');
        const cancelBtn = document.getElementById('cancelEditBtn');

        profileFields.forEach(id => {
            const el = document.getElementById(id);
            if (el) originalValues[id] = el.value;
        });

        editBtn.addEventListener('click', function() {
            profileFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.removeAttribute('readonly');
            });
            editBtn.style.display = 'none';
            updateBtn.style.display = 'inline-block';
            cancelBtn.style.display = 'inline-block';
        });

        cancelBtn.addEventListener('click', function() {
            profileFields.forEach(id => {
                const field = document.getElementById(id);
                if (!field) return;
                if (id === 'password' || id === 'newpassword' || id === 'confirmnewpassword') {
                    field.value = '';
                } else {
                    field.value = originalValues[id];
                }
                field.setAttribute('readonly', true);
            });
            editBtn.style.display = 'inline-block';
            updateBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
        });

        updateBtn.addEventListener('click', function(e) {
            // Ask for confirmation before updating
            if (!confirm('Are you sure you want to update your profile?')) {
                return;
            }

            const data = {};
            profileFields.forEach(id => {
                const el = document.getElementById(id);
                if (!el) console.warn('Missing field:', id);
                data[id] = el ? el.value : '';
            });

            // Password logic here..
            const oldPass = document.getElementById('password').value;
            const newPass = document.getElementById('newpassword').value;
            const confirmNewPass = document.getElementById('confirmnewpassword').value;

            if (oldPass || newPass || confirmNewPass) {
                if (!oldPass) {
                    alert('Enter your current password to change password.');
                    return;
                }
                data.password = oldPass;
                data.newpassword = newPass;
                data.confirmnewpassword = confirmNewPass;
            }

            // REMOVE THIS NESTED LISTENER:
            // updateBtn.addEventListener('click', function(e) { ... });

            fetch('../../features/update_profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        alert('Profile updated successfully!');
                        window.location.reload();
                    } else {
                        alert(res.message || 'Update failed.');
                    }
                });
        });

        // Show modal on logout click
        document.getElementById("logout").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("logoutModal").style.display = "flex";
        });

        // Hide modal on "No"
        document.getElementById("logoutNo").addEventListener("click", function() {
            document.getElementById("logoutModal").style.display = "none";
        });

        // Logout on "Yes"
        document.getElementById("logoutYes").addEventListener("click", function() {
            window.location.href = "../../features/logout.php";
        });
    </script>
</body>

</html>