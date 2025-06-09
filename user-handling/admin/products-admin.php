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

if (isset($_SESSION['user_id'])) {
    $adminId = $_SESSION['user_id'];
    $result = mysqli_query($conn, "SELECT username FROM users WHERE user_id = '$adminId' AND role = 'admin'");
    if (!$result) {
        die('Query error: ' . mysqli_error($conn));
    }
    if ($row = mysqli_fetch_assoc($result)) {
        $adminUsername = htmlspecialchars($row['username']);
    }
}

$sellerResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'seller'");
$sellerRow = mysqli_fetch_assoc($sellerResult);
$totalSellers = $sellerRow['total'];

$buyerResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'buyer'");
$buyerRow = mysqli_fetch_assoc($buyerResult);
$totalBuyers = $buyerRow['total'];

// Fetch all products
$productsResult = mysqli_query($conn, "
    SELECT 
        p.product_id, p.name AS product_name, p.description, p.price, p.stock_quantity, p.gender, p.category, p.type,
        p.sizes_available, p.colors_available, p.image_path, p.created_at, p.updated_at,
        u.user_id AS seller_id, u.username AS seller_username, u.email AS seller_email
    FROM products p
    JOIN users u ON p.seller_id = u.user_id
    ORDER BY p.created_at DESC
");
$products = [];
if ($productsResult) {
    while ($row = mysqli_fetch_assoc($productsResult)) {
        $products[] = $row;
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
    <link rel="stylesheet" href="/style/logout.css">
    <link href="https://fonts.googleapis.com/css2?family=Mynerve&family=Mandali&family=Aoboshi+One&family=Inter:ital,wght@0,100..900;1,100..900&family=MuseoModerno:ital,wght@0,100..900;1,100..900&family=Podkova:wght@400..800&display=swap" rel="stylesheet">
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
        <section>
            <div class="section-wrap">
                <div class="top-container">
                    <h1 class="dasboard-header">Products</h1>
                </div>

                <div class="my-orders">
                    <div style="display: flex; align-items: center;">
                        <h2 class="orders-header">Sort Products </h2>
                        <div class="sort-button">
                            <img id="asc" class="asc" src="/images/sort-from-bottom-to-top-svgrepo-com.svg" style="display:none;">
                            <img id="desc" class="desc" src="/images/sort-from-top-to-bottom-svgrepo-com.svg">
                        </div>
                    </div>
                    <div style="display: flex; gap: 1em; margin: 0.5em 0; align-items: center;">
                        <h3>Filter by:</h3>
                        <select id="filterGender">
                            <option value="">All Genders</option>
                            <?php
                            $genders = [];
                            foreach ($products as $product) {
                                if ($product['gender'] && !in_array($product['gender'], $genders)) {
                                    $genders[] = $product['gender'];
                                }
                            }
                            foreach ($genders as $gender) {
                                echo '<option value="' . htmlspecialchars($gender) . '">' . htmlspecialchars($gender) . '</option>';
                            }
                            ?>
                        </select>
                        <select id="filterCategory">
                            <option value="">All Categories</option>
                            <?php
                            $categories = [];
                            foreach ($products as $product) {
                                if ($product['category'] && !in_array($product['category'], $categories)) {
                                    $categories[] = $product['category'];
                                }
                            }
                            foreach ($categories as $category) {
                                echo '<option value="' . htmlspecialchars($category) . '">' . htmlspecialchars($category) . '</option>';
                            }
                            ?>
                        </select>
                        <select id="filterType">
                            <option value="">All Types</option>
                            <?php
                            $types = [];
                            foreach ($products as $product) {
                                if ($product['type'] && !in_array($product['type'], $types)) {
                                    $types[] = $product['type'];
                                }
                            }
                            foreach ($types as $type) {
                                echo '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars($type) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="orders-table-container">
                        <table class="users-table">
                            <thead>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Seller</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Category</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center;">No products found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr data-product_id="<?= htmlspecialchars($product['product_id']) ?>"
                                            data-gender="<?= htmlspecialchars($product['gender']) ?>"
                                            data-category="<?= htmlspecialchars($product['category']) ?>"
                                            data-type="<?= htmlspecialchars($product['type']) ?>"
                                            data-product_id="<?= htmlspecialchars($product['product_id']) ?>
                                            ">
                                            <td><?= htmlspecialchars($product['product_id']) ?></td>
                                            <td><?= htmlspecialchars($product['product_name']) ?></td>
                                            <td><?= htmlspecialchars($product['seller_username']) ?></td>
                                            <td>₱<?= htmlspecialchars(number_format($product['price'], 2)) ?></td>
                                            <td><?= htmlspecialchars($product['stock_quantity']) ?></td>
                                            <td><?= htmlspecialchars($product['category']) ?></td>
                                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($product['created_at']))) ?></td>
                                            <td style="text-align:center;">
                                                <a href="#"
                                                    class="btn btn-view view-product-btn"
                                                    data-product_id="<?= htmlspecialchars($product['product_id']) ?>"
                                                    data-name="<?= htmlspecialchars($product['product_name']) ?>"
                                                    data-description="<?= htmlspecialchars($product['description']) ?>"
                                                    data-price="₱<?= htmlspecialchars(number_format($product['price'], 2)) ?>"
                                                    data-stock="<?= htmlspecialchars($product['stock_quantity']) ?>"
                                                    data-gender="<?= htmlspecialchars($product['gender']) ?>"
                                                    data-category="<?= htmlspecialchars($product['category']) ?>"
                                                    data-type="<?= htmlspecialchars($product['type']) ?>"
                                                    data-sizes="<?= htmlspecialchars($product['sizes_available']) ?>"
                                                    data-colors="<?= htmlspecialchars($product['colors_available']) ?>"
                                                    data-image="<?= !empty($product['image_path']) ? '../../images/products/' . htmlspecialchars($product['image_path']) : '../../images/no-image.png' ?>"
                                                    data-seller="<?= htmlspecialchars($product['seller_username']) ?>"
                                                    data-seller_email="<?= htmlspecialchars($product['seller_email']) ?>"
                                                    data-created="<?= htmlspecialchars(date('Y-m-d', strtotime($product['created_at']))) ?>"
                                                    data-updated="<?= htmlspecialchars(date('Y-m-d', strtotime($product['updated_at']))) ?>">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </main>
    <div id="productModal" class="buyer-modal" style="display:none;">
        <div class="buyer-modal-content">
            <span class="buyer-modal-close" id="productModalClose">&times;</span>
            <div class="buyer-modal-profile">
                <img id="productModalImg" src="" alt="Product Image">
            </div>
            <h2 id="productModalName"></h2>
            <div class="buyer-modal-table-container">
                <table class="buyer-modal-table">
                    <tr>
                        <th>ID</th>
                        <td id="productModalId"></td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td id="productModalDescription"></td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td id="productModalPrice"></td>
                    </tr>
                    <tr>
                        <th>Stock</th>
                        <td id="productModalStock"></td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td id="productModalGender"></td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td id="productModalCategory"></td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td id="productModalType"></td>
                    </tr>
                    <tr>
                        <th>Sizes Available</th>
                        <td id="productModalSizes"></td>
                    </tr>
                    <tr>
                        <th>Colors Available</th>
                        <td id="productModalColors"></td>
                    </tr>
                    <tr>
                        <th>Seller</th>
                        <td id="productModalSeller"></td>
                    </tr>
                    <tr>
                        <th>Seller Email</th>
                        <td id="productModalSellerEmail"></td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td id="productModalCreated"></td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td id="productModalUpdated"></td>
                    </tr>
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
            document.querySelectorAll('.view-product-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('productModalImg').src = btn.dataset.image;
                    document.getElementById('productModalName').textContent = btn.dataset.name;
                    document.getElementById('productModalId').textContent = btn.dataset.product_id;
                    document.getElementById('productModalDescription').textContent = btn.dataset.description || '-';
                    document.getElementById('productModalPrice').textContent = btn.dataset.price;
                    document.getElementById('productModalStock').textContent = btn.dataset.stock;
                    document.getElementById('productModalGender').textContent = btn.dataset.gender || '-';
                    document.getElementById('productModalCategory').textContent = btn.dataset.category || '-';
                    document.getElementById('productModalType').textContent = btn.dataset.type || '-';
                    document.getElementById('productModalSizes').textContent = btn.dataset.sizes || '-';
                    document.getElementById('productModalColors').textContent = btn.dataset.colors || '-';
                    document.getElementById('productModalSeller').textContent = btn.dataset.seller;
                    document.getElementById('productModalSellerEmail').textContent = btn.dataset.seller_email;
                    document.getElementById('productModalCreated').textContent = btn.dataset.created;
                    document.getElementById('productModalUpdated').textContent = btn.dataset.updated;
                    document.getElementById('productModal').style.display = 'flex';
                });
            });

            document.getElementById('productModalClose').onclick = function() {
                document.getElementById('productModal').style.display = 'none';
            };
            window.onclick = function(event) {
                var modal = document.getElementById('productModal');
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            };


            // Sort functionality
            const ascBtn = document.getElementById("asc");
            const descBtn = document.getElementById("desc");
            const tbody = document.querySelector(".users-table tbody");

            function sortTable(direction = 'desc') {
                const rows = Array.from(tbody.querySelectorAll("tr")).filter(row => row.querySelector("td"));
                // Sort by user_id (first column)
                rows.sort((a, b) => {
                    const idA = parseInt(a.querySelector("td").textContent.trim());
                    const idB = parseInt(b.querySelector("td").textContent.trim());
                    return direction === 'asc' ? idA - idB : idB - idA;
                });
                // Remove all rows and re-append in sorted order
                rows.forEach(row => tbody.appendChild(row));
            }

            sortTable('desc');

            descBtn.addEventListener("click", () => {
                descBtn.style.display = "none";
                ascBtn.style.display = "inline";
                sortTable('asc');
            });

            ascBtn.addEventListener("click", () => {
                ascBtn.style.display = "none";
                descBtn.style.display = "inline";
                sortTable('desc');
            });


            // Confirm before suspending an account
            document.querySelectorAll('.suspend-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to SUSPEND this account?')) {
                        e.preventDefault();
                    }
                });
            });

            // Confirm before reactivating an account
            document.querySelectorAll('.reactivate-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to REACTIVATE this account?')) {
                        e.preventDefault();
                    }
                });
            });


            function filterProducts() {
                const gender = document.getElementById('filterGender').value;
                const category = document.getElementById('filterCategory').value;
                const type = document.getElementById('filterType').value;
                document.querySelectorAll('.users-table tbody tr').forEach(row => {
                    const rowGender = row.getAttribute('data-gender') || '';
                    const rowCategory = row.getAttribute('data-category') || '';
                    const rowType = row.getAttribute('data-type') || '';
                    let show = true;
                    if (gender && rowGender !== gender) show = false;
                    if (category && rowCategory !== category) show = false;
                    if (type && rowType !== type) show = false;
                    row.style.display = show ? '' : 'none';
                });
            }

            ['filterGender', 'filterCategory', 'filterType'].forEach(id => {
                document.getElementById(id).addEventListener('change', filterProducts);
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