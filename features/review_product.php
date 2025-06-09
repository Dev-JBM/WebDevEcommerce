<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db-connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: /homepage.php");
    exit;
}

// Accept both ?id= and ?product_id=
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
} elseif (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
} else {
    echo "No product selected.";
    exit;
}

// Fetch user
$username = mysqli_real_escape_string($conn, $_SESSION['username']);
$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$user_id = $user['user_id'];

$imagePath = (!empty($user['image']))
    ? '../../images/profiles/' . $user['image']
    : '../../images/profile-circle-svgrepo-com.png';

// Fetch product
$product_query = "SELECT * FROM products WHERE product_id = $product_id LIMIT 1";
$product_result = mysqli_query($conn, $product_query);
$product = mysqli_fetch_assoc($product_result);

if (!$product) {
    echo "Product not found.";
    exit;
}

$order_item_id = isset($_GET['order_item_id']) ? intval($_GET['order_item_id']) : 0;
$order_size = '';
$order_color = '';

if ($order_item_id > 0) {
    $oi_query = "SELECT size, color FROM order_items WHERE order_item_id = $order_item_id LIMIT 1";
    $oi_result = mysqli_query($conn, $oi_query);
    if ($oi_row = mysqli_fetch_assoc($oi_result)) {
        $order_size = $oi_row['size'];
        $order_color = $oi_row['color'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Review Product</title>
    <link rel="stylesheet" href="/style/review_product.css">
    <link rel="stylesheet" href="/style/settings.css">
    <link rel="stylesheet" href="/style/profilePic.css">
    <link rel="stylesheet" href="/style/logout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Mynerve&family=Mandali&family=Aoboshi+One&family=Inter:ital,wght@0,100..900;1,100..900&family=MuseoModerno:ital,wght@0,100..900;1,100..900&family=Podkova:wght@400..800&display=swap" rel="stylesheet">
</head>

<body>
    <main>
        <header class="header">
            <div class="left-header">
                <a href="/store.php">Wear Dyans</a>
            </div>

            <div class="right-header">
                <a href="/cart.php"><img src="/images/SVGRepo_iconCarrier.png"></a>
                <img class="profile" src="<?= htmlspecialchars($imagePath); ?>" onclick="toggleMenu()">
                <div class="sub-menu-wrap" id="subMenu">
                    <div class="sub-menu">
                        <div class="user-info">
                            <img class="profile-menu-img" src="<?= htmlspecialchars($imagePath); ?>" alt="profile">
                            <h3 id="userName"><?= htmlspecialchars($user['username']); ?></h3>
                        </div>
                        <hr>
                        <div class="sub-menu-link" id="toBecomeSeller">
                            <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                                <path
                                    d="M332.64 64.58C313.18 43.57 286 32 256 32c-30.16 0-57.43 11.5-76.8 32.38-19.58 21.11-29.12 49.8-26.88 80.78C156.76 206.28 203.27 256 256 256s99.16-49.71 103.67-110.82c2.27-30.7-7.33-59.33-27.03-80.6zM432 480H80a31 31 0 01-24.2-11.13c-6.5-7.77-9.12-18.38-7.18-29.11C57.06 392.94 83.4 353.61 124.8 326c36.78-24.51 83.37-38 131.2-38s94.42 13.5 131.2 38c41.4 27.6 67.74 66.93 76.18 113.75 1.94 10.73-.68 21.34-7.18 29.11A31 31 0 01432 480z" />
                            </svg>
                            <a class="sub-menu-text" href="/store.php">Back to shopping</a>
                            <span>></span>
                        </div>

                        <div class="sub-menu-link" id="logout">
                            <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                                <path
                                    d="M160 256a16 16 0 0116-16h144V136c0-32-33.79-56-64-56H104a56.06 56.06 0 00-56 56v240a56.06 56.06 0 0056 56h160a56.06 56.06 0 0056-56V272H176a16 16 0 01-16-16zM459.31 244.69l-80-80a16 16 0 00-22.62 22.62L409.37 240H320v32h89.37l-52.68 52.69a16 16 0 1022.62 22.62l80-80a16 16 0 000-22.62z" />
                            </svg>
                            <p class="sub-menu-text">Log Out</p>
                            <span>></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="add-product" style="display: flex; max-width: 1250px;">
            <div class="title" style="display: flex; align-items: center; justify-content: space-between;">
                <p>Review Product</p>
                <a href="/user-handling/sellers/seller_settings.php" style="margin-left:auto; margin-right: 40px; color: #FF7F7F; font-size: 0.5em;">
                    < Back to Settings
                        </a>
            </div>
            <div class="edit-product-content">
                <form id="editProductForm" enctype="multipart/form-data" method="post" action="submit-review.php" onsubmit="return confirmReview()"> <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                    <input type="hidden" name="order_item_id" value="<?= htmlspecialchars($order_item_id) ?>">
                    <input type="hidden" name="order_size" value="<?= htmlspecialchars($order_size) ?>">
                    <input type="hidden" name="order_color" value="<?= htmlspecialchars($order_color) ?>">
                    <div class="inputs-container">
                        <div class="input-box">
                            <label class="add-product-label" for="productName">Product Name:</label>
                            <input type="text" id="productName" name="productName" value="<?= htmlspecialchars($product['name']) ?>" readonly>
                        </div>

                        <div class="input-box">
                            <label class="add-product-label" for="orderSize">Ordered Size:</label>
                            <input type="text" id="orderSize" name="orderSize" value="<?= htmlspecialchars($order_size) ?>" readonly>
                        </div>

                        <div class="input-box">
                            <label class="add-product-label" for="orderColor">Ordered Color:</label>
                            <input type="text" id="orderColor" name="orderColor" value="<?= htmlspecialchars($order_color) ?>" readonly>
                        </div>

                        <div class="input-box" id="rating-container">
                            <label class="add-product-label" for="rating" style="margin-right: 16px; min-width: 120px;">Rating:</label>
                            <div class="star-rating">
                                <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="5 stars"></label>
                                <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars"></label>
                                <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars"></label>
                                <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars"></label>
                                <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star"></label>
                            </div>
                        </div>

                        <div class="input-box">
                            <label class="add-product-label" for="description" style="font-size: 1.1em; padding: 10px;">
                                <div style="padding-left: 15px;">What can you say about this product:</div>
                            </label>
                            <textarea id="description" name="description" class="add-product-input" rows="3" placeholder="Type here..."></textarea>
                        </div>
                    </div>
                    <div class="edit-btn-container">
                        <button id="editProductBtn" class="profile-edit-btn" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
    <script>
        let subMenu = document.getElementById("subMenu");

        function toggleMenu() {
            subMenu.classList.toggle("open-menu");
        }

        function confirmReview() {
            const rating = document.querySelector('input[name="rating"]:checked');
            let ratingValue = rating ? rating.value : 'No rating';
            const description = document.getElementById('description').value.trim();

            let msg = "Are you sure you want to submit this review?\n";
            msg += "Rating: " + ratingValue + " star(s)\n";
            msg += "Review: " + (description ? description : "[No comment]");

            return confirm(msg);
        }
    </script>

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
        // Show modal on logout click (sub-menu and menu-container)
        const logoutBtn = document.getElementById("logout");
        const logoutMenuBtn = document.getElementById("logoutMenu");
        const logoutModal = document.getElementById("logoutModal");

        if (logoutBtn) {
            logoutBtn.addEventListener("click", function(e) {
                e.preventDefault();
                logoutModal.style.display = "flex";
            });
        }
        if (logoutMenuBtn) {
            logoutMenuBtn.addEventListener("click", function(e) {
                e.preventDefault();
                logoutModal.style.display = "flex";
            });
        }
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