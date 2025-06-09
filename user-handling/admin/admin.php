<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/features/db-connection.php';

if (isset($_SESSION['user_id'])) {
  $adminId = $_SESSION['user_id'];
  $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$adminId' AND role = 'admin' LIMIT 1");
  if ($row = mysqli_fetch_assoc($result)) {
    $user = $row;
    $adminUsername = htmlspecialchars($row['username']);
    $adminImage = !empty($row['image'])
      ? '/images/profiles/' . htmlspecialchars($row['image'])
      : '/images/profile-circle-svgrepo-com.png';
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

$today = date('Y-m-d');
$recentUsersResult = mysqli_query($conn, "SELECT user_id, role, username, phone_number, email, created_at FROM users WHERE DATE(created_at) = '$today' ORDER BY created_at DESC");
$recentUsers = [];
if ($recentUsersResult) {
  while ($row = mysqli_fetch_assoc($recentUsersResult)) {
    $recentUsers[] = $row;
  }
}
$recentCount = count($recentUsers);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wear Dyans</title>
  <link rel="stylesheet" href="/style/admin.css">
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
          <h1 class="dasboard-header">Dashboard Overview</h1>
        </div>

        <div class="middle-container">
          <div class="middle-container-box" id="sellerBox" style="cursor:pointer;">
            <div class=" box-header">
              <h2 class="total-header">Total Sellers</h2>
              <img src="/images/image 56.png">
            </div>
            <h3 class="total-number"><?= number_format($totalSellers) ?></h3>
            <p class="bottom-text">Registered sellers on the platform</p>
          </div>

          <div class="middle-container-box" id="buyerBox" style="cursor:pointer;">
            <div class="box-header">
              <h2 class="total-header">Total Buyers</h2>
              <img src="/images/image 55.png">
            </div>
            <h3 class="total-number"><?= number_format($totalBuyers) ?></h3>
            <p class="bottom-text">Active buyers on the platform</p>
          </div>
        </div>

        <div class="bottom-container">
          <h2 class="bottom-header">Recent Activity: <span style="color: green;"><?= $recentCount ?></span> user<?= $recentCount === 1 ? '' : 's' ?> joined today.</h2>

          <table class="users-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Joined</th>
                <th>User</th>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Email</th>
              </tr>
            </thead>

            <tbody>
              <?php if ($recentCount === 0): ?>
                <tr>
                  <td colspan="6" style="text-align:center;">No users joined today.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recentUsers as $user): ?>
                  <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars(date('d-m-Y', strtotime($user['created_at']))) ?></td>
                    <td><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['phone_number']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>
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
      window.location.href = "/features/logout.php";
    });


    // Redirect sellerBox and buyerBox clicks
    document.getElementById("sellerBox").addEventListener("click", function() {
      window.location.href = "sellers.php";
    });
    document.getElementById("buyerBox").addEventListener("click", function() {
      window.location.href = "buyers.php";
    });
  </script>
</body>

</html>