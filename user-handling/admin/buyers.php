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

// Fetch all buyers
$buyersResult = mysqli_query($conn, "SELECT user_id, username, email, phone_number, created_at, is_active, image, firstname, middlename, lastname, birthdate, address FROM users WHERE role = 'buyer' ORDER BY created_at DESC");
$buyers = [];
if ($buyersResult) {
    while ($row = mysqli_fetch_assoc($buyersResult)) {
        $buyers[] = $row;
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
                    <h1 class="dasboard-header">Buyer Accounts</h1>
                </div>

                <div class="my-orders">
                    <div style="display: flex; align-items: center;">
                        <h2 class="orders-header">Sort Accounts </h2>
                        <div class="sort-button">
                            <img id="asc" class="asc" src="../../images/sort-from-bottom-to-top-svgrepo-com.svg" style="display:none;">
                            <img id="desc" class="desc" src="../../images/sort-from-top-to-bottom-svgrepo-com.svg">
                        </div>
                    </div>
                    <div class="orders-table-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Created At</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>State</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($buyers)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center;">No buyer accounts found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($buyers as $buyer): ?>
                                        <tr data-user_id="<?= htmlspecialchars($buyer['user_id']) ?>">
                                            <td><?= htmlspecialchars($buyer['user_id']) ?></td>
                                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($buyer['created_at']))) ?></td>
                                            <td><?= htmlspecialchars($buyer['username']) ?></td>
                                            <td><?= htmlspecialchars($buyer['email']) ?></td>
                                            <td><?= htmlspecialchars($buyer['phone_number']) ?></td>
                                            <td class="state-cell">
                                                <?php if (isset($buyer['is_active']) && $buyer['is_active']): ?>
                                                    <span class="state-badge state-active">Active</span>
                                                <?php else: ?>
                                                    <span class="state-badge state-suspended">Suspended</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align:center;">
                                                <a href="#"
                                                    class="btn btn-view view-buyer-btn" style="margin-bottom: 10px;"
                                                    data-user_id="<?= htmlspecialchars($buyer['user_id']) ?>"
                                                    data-username="<?= htmlspecialchars($buyer['username']) ?>"
                                                    data-email="<?= htmlspecialchars($buyer['email']) ?>"
                                                    data-phone="<?= htmlspecialchars($buyer['phone_number']) ?>"
                                                    data-created="<?= htmlspecialchars(date('Y-m-d', strtotime($buyer['created_at']))) ?>"
                                                    data-state="<?= $buyer['is_active'] ? 'Active' : 'Suspended' ?>"
                                                    data-profile="<?= !empty($buyer['image']) ? '../../images/profiles/' . htmlspecialchars($buyer['image']) : '../../images/profile-circle-svgrepo-com.png' ?>"
                                                    data-firstname="<?= htmlspecialchars($buyer['firstname']) ?>"
                                                    data-middlename="<?= htmlspecialchars($buyer['middlename']) ?>"
                                                    data-lastname="<?= htmlspecialchars($buyer['lastname']) ?>"
                                                    data-birthdate="<?= htmlspecialchars($buyer['birthdate']) ?>"
                                                    data-address="<?= htmlspecialchars($buyer['address']) ?>">
                                                    View
                                                </a>
                                                <?php if (isset($buyer['is_active']) && !$buyer['is_active']): ?>
                                                    <a href="account-state.php?id=<?= $buyer['user_id'] ?>&role=buyer&action=reactivate"
                                                        class="btn btn-reactivate reactivate-btn">
                                                        Reactivate
                                                    </a>
                                                <?php else: ?>
                                                    <a href="account-state.php?id=<?= $buyer['user_id'] ?>&role=buyer&action=suspend"
                                                        class="btn btn-suspend suspend-btn">
                                                        Suspend
                                                    </a>
                                                <?php endif; ?>
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
    <div id="buyerModal" class="buyer-modal" style="display:none;">
        <div class="buyer-modal-content">
            <span class="buyer-modal-close" id="buyerModalClose">&times;</span>
            <div class="buyer-modal-profile">
                <img id="buyerModalImg" src="" alt="Profile Picture">
            </div>
            <h2 id="buyerModalUsername"></h2>
            <table class="buyer-modal-table">
                <tr>
                    <th>ID</th>
                    <td id="buyerModalId"></td>
                </tr>
                <tr>
                    <th>Full Name</th>
                    <td id="buyerModalFullname"></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td id="buyerModalEmail"></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td id="buyerModalPhone"></td>
                </tr>
                <tr>
                    <th>Birthdate</th>
                    <td id="buyerModalBirthdate"></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td id="buyerModalAddress"></td>
                </tr>
                <tr>
                    <th>Joined</th>
                    <td id="buyerModalCreated"></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td id="buyerModalState"></td>
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
        document.querySelectorAll('.view-buyer-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('buyerModalImg').src = btn.dataset.profile;
                document.getElementById('buyerModalUsername').textContent = btn.dataset.username;
                document.getElementById('buyerModalId').textContent = btn.dataset.user_id;
                // Full name: combine first, middle, last (skip empty parts)
                let fullname = [btn.dataset.firstname, btn.dataset.middlename, btn.dataset.lastname]
                    .filter(Boolean).join(' ');
                document.getElementById('buyerModalFullname').textContent = fullname || '-';
                document.getElementById('buyerModalEmail').textContent = btn.dataset.email;
                document.getElementById('buyerModalPhone').textContent = btn.dataset.phone;
                document.getElementById('buyerModalBirthdate').textContent = btn.dataset.birthdate || '-';
                document.getElementById('buyerModalAddress').textContent = btn.dataset.address || '-';
                document.getElementById('buyerModalCreated').textContent = btn.dataset.created;
                document.getElementById('buyerModalState').textContent = btn.dataset.state;
                document.getElementById('buyerModal').style.display = 'flex';
            });
        });

        document.getElementById('buyerModalClose').onclick = function() {
            document.getElementById('buyerModal').style.display = 'none';
        };

        window.onclick = function(event) {
            var modal = document.getElementById('buyerModal');
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