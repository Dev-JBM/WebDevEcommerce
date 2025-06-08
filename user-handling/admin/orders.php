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
    <link rel="stylesheet" href="../../style/admin.css">
    <link rel="stylesheet" href="../../style/view-data.css">
    <link rel="stylesheet" href="../../style/logout.css">
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
                <a href="../../homepage.php">Wear Dyans</a>
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
                    <h1 class="dasboard-header">Orders</h1>
                </div>

                <div class="my-orders">
                    <div style="display: flex; align-items: center;">
                        <h2 class="orders-header">Sort Orders </h2>
                        <div class="sort-button">
                            <img id="asc" class="asc" src="../../images/sort-from-bottom-to-top-svgrepo-com.svg" style="display:none;">
                            <img id="desc" class="desc" src="../../images/sort-from-top-to-bottom-svgrepo-com.svg">
                        </div>
                    </div>
                    <div class="orders-table-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Order Date</th>
                                    <th>Product</th>
                                    <th>Buyer</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Shipping Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center;">No orders found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($order['order_id']) ?></td>
                                                <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($order['order_date']))) ?></td>
                                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                <td>
                                                    <?= htmlspecialchars($order['buyer_username']) ?><br>
                                                    <small><?= htmlspecialchars($order['buyer_email']) ?></small>
                                                </td>
                                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                                <td>₱<?= htmlspecialchars(number_format($order['total_amount'], 2)) ?></td>
                                                <td><?= htmlspecialchars($order['shipping_address']) ?></td>
                                                <td class="order-view-btn-td">
                                                    <a href="#"
                                                        class="btn btn-view view-order-btn"
                                                        data-order='<?= htmlspecialchars(json_encode([
                                                                        "order_id" => $order['order_id'],
                                                                        "order_date" => $order['order_date'],
                                                                        "buyer_username" => $order['buyer_username'],
                                                                        "buyer_email" => $order['buyer_email'],
                                                                        "total_amount" => $order['total_amount'],
                                                                        "shipping_address" => $order['shipping_address'],
                                                                        "payment_status" => $order['payment_status'],
                                                                        "payment_method" => $order['payment_method'],
                                                                        "items" => $order['items']
                                                                    ])) ?>'>View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

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
        document.querySelectorAll('.view-order-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const order = JSON.parse(btn.getAttribute('data-order'));
                document.getElementById('orderModalId').textContent = order.order_id;
                document.getElementById('orderModalDate').textContent = order.order_date;
                document.getElementById('orderModalBuyer').textContent = order.buyer_username;
                document.getElementById('orderModalAddress').textContent = order.shipping_address;
                document.getElementById('orderModalPayment').textContent = order.payment_method || '-';

                // Fill products table
                let productsHtml = '';
                order.items.forEach(function(item) {
                    productsHtml += `<tr>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>${item.size || '-'}</td>
                <td>${item.color || '-'}</td>
                <td>₱${Number(item.price_at_purchase * item.quantity).toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                <td>${item.seller_username || '-'}</td>
            </tr>`;
                });
                document.getElementById('orderModalProductsBody').innerHTML = productsHtml;

                document.getElementById('orderModal').style.display = 'flex';
            });
        });
        document.getElementById('orderModalClose').onclick = function() {
            document.getElementById('orderModal').style.display = 'none';
        };
        window.addEventListener('click', function(event) {
            var modal = document.getElementById('orderModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });


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