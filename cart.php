<?php
session_start();
require_once './features/db-connection.php';

if (!isset($_SESSION['username'])) {
  header("Location: homepage.html");
  exit;
}

$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$imagePath = (!empty($user['image']))
  ? 'images/profiles/' . $user['image']
  : './images/profile-circle-svgrepo-com.png';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wear Dyans</title>
  <link rel="stylesheet" href="style/cart.css">
  <link rel="stylesheet" href="style/profilePic.css">
  <link rel="stylesheet" href="style/logout.css">
  <link href="https://fonts.googleapis.com/css2?family=Mynerve&family=Mandali&family=Aoboshi+One&family=Inter:ital,wght@0,100..900;1,100..900&family=MuseoModerno:ital,wght@0,100..900;1,100..900&family=Podkova:wght@400..800&display=swap" rel="stylesheet">
</head>

<body>
  <main>
    <header class="header">
      <div class="left-header">
        <a href="store.php">Wear Dyans</a>
      </div>

      <div class="right-header">
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
              <a class="sub-menu-text" href="">Become a Seller</a>
              <span>></span>
            </div>

            <div class="sub-menu-link" id="toSettings">
              <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                <path
                  d="M332.64 64.58C313.18 43.57 286 32 256 32c-30.16 0-57.43 11.5-76.8 32.38-19.58 21.11-29.12 49.8-26.88 80.78C156.76 206.28 203.27 256 256 256s99.16-49.71 103.67-110.82c2.27-30.7-7.33-59.33-27.03-80.6zM432 480H80a31 31 0 01-24.2-11.13c-6.5-7.77-9.12-18.38-7.18-29.11C57.06 392.94 83.4 353.61 124.8 326c36.78-24.51 83.37-38 131.2-38s94.42 13.5 131.2 38c41.4 27.6 67.74 66.93 76.18 113.75 1.94 10.73-.68 21.34-7.18 29.11A31 31 0 01432 480z" />
              </svg>
              <a class="sub-menu-text" id="settingsLink" href="#">Settings</a>
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

    <section>
      <div class="section-wrapper">

        <div class="top-container">
          <div class="top-container-top-part">
            <img class="cart-icon" src="./images/SVGRepo_iconCarrier.svg">
            <div class="vertical-line"></div>
            <p class="top-container-header">Shopping Cart</p>
          </div>
          <div class="top-container-bottom-part"></div>
        </div>

        <div class="bottom-container">
          <div class="cart-table">
            <div class="cart-header">
              <div class="product-header">
                <input class="product-checkbox" type="checkbox">
                <label class="product-label" for="product">Product</label>
              </div>
              <div>Price</div>
              <div>Quantity</div>
              <div>Total Price</div>
              <div></div>
            </div>

            <div class="cart-item-container">
              <!-- ITEMS(DB) -->
              <div class="cart-item">
                <div class="product-details">
                  <input class="item-check-box" class="item-check-box" type="checkbox">
                  <img src="./images/product_image.png" alt="Jacket">
                  <div class="product-info">
                    <h4>Fleece Full-Zip Long Sleeve Jacket</h4>
                    <p>Variation/s:<span class="size"> Large</span>, <span class="color">Green</span></p>
                  </div>
                </div>

                <div class="price">
                  <p>PHP <span class="discounted-price">1,990</span><br><small><s>PHP <span class="orig-price">3,499</span></s></small></p>
                </div>

                <div class="quantity">2</div>

                <div class="total">
                  <p>PHP <span class="total-ptice">3,980</span></p>
                </div>

                <div class="actions">.
                  <button type="button" class="remove-btn">Remove</button>
                </div>
              </div>

              <div class="cart-item">
                <div class="product-details">
                  <input class="item-check-box" type="checkbox">
                  <img src="./images/product_image.png" alt="Jacket">
                  <div class="product-info">
                    <h4>Fleece Full-Zip Long Sleeve Jacket</h4>
                    <p>Variation/s:<span class="size"> Large</span>, <span class="color">Green</span></p>
                  </div>
                </div>

                <div class="price">
                  <p>PHP <span class="discounted-price">1,990</span><br><small><s>PHP <span class="orig-price">3,499</span></s></small></p>
                </div>

                <div class="quantity">2</div>

                <div class="total">
                  <p>PHP <span class="total-ptice">3,980</span></p>
                </div>

                <div class="actions">.
                  <button type="button" class="remove-btn">Remove</button>
                </div>
              </div>

              <div class="cart-item">
                <div class="product-details">
                  <input class="item-check-box" type="checkbox">
                  <img src="./images/product_image.png" alt="Jacket">
                  <div class="product-info">
                    <h4>Fleece Full-Zip Long Sleeve Jacket</h4>
                    <p>Variation/s:<span class="size"> Large</span>, <span class="color">Green</span></p>
                  </div>
                </div>

                <div class="price">
                  <p>PHP <span class="discounted-price">1,990</span><br><small><s>PHP <span class="orig-price">3,499</span></s></small></p>
                </div>

                <div class="quantity">2</div>

                <div class="total">
                  <p>PHP <span class="total-ptice">3,980</span></p>
                </div>

                <div class="actions">.
                  <button type="button" class="remove-btn">Remove</button>
                </div>
              </div>

              <div class="cart-item">
                <div class="product-details">
                  <input class="item-check-box" type="checkbox">
                  <img src="./images/product_image.png" alt="Jacket">
                  <div class="product-info">
                    <h4>Fleece Full-Zip Long Sleeve Jacket</h4>
                    <p>Variation/s:<span class="size"> Large</span>, <span class="color">Green</span></p>
                  </div>
                </div>

                <div class="price">
                  <p>PHP <span class="discounted-price">1,990</span><br><small><s>PHP <span class="orig-price">3,499</span></s></small></p>
                </div>

                <div class="quantity">2</div>

                <div class="total">
                  <p>PHP <span class="total-ptice">3,980</span></p>
                </div>

                <div class="actions">.
                  <button type="button" class="remove-btn">Remove</button>
                </div>
              </div>
            </div>
          </div>

          <div class="cart-footer">
            <div class="left-footer">
              <button type="button" class="select-all-btn">Select All</button>
              <button type="button" class="remove-btn-footer">Remove</button>
            </div>

            <div class="right-footer">
              <div class="total-price-selected-items">
                <!-- TOTAL(JS) -->
                <p>Total (<span class="item-selected">2 </span>Items): PHP <span class="total-price-selected">3,980</span></p>
              </div>
              <button class="check-out-btn">
                Check Out
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script>
    // TOGGLE MENU
    let subMenu = document.getElementById("subMenu");

    function toggleMenu() {
      subMenu.classList.toggle("open-menu");
    }

    const productCheckbox = document.querySelector('.product-checkbox');
    const selectAllBtn = document.querySelector('.select-all-btn');
    const itemCheckboxes = document.querySelectorAll('.item-check-box');

    function toggleAllCheckboxes() {
      const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
      itemCheckboxes.forEach(cb => cb.checked = !allChecked);
    }

    // When .product-checkbox is clicked
    productCheckbox.addEventListener('click', () => {
      toggleAllCheckboxes();
    });

    // When .select-all-btn is clicked
    selectAllBtn.addEventListener('click', () => {
      toggleAllCheckboxes();
    });
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
      window.location.href = "./features/logout.php";
    });

    // ROLE CHECKER
    const userRole = <?= isset($user['role']) ? json_encode($user['role']) : 'null' ?>;

    document.addEventListener("DOMContentLoaded", function() {
      const settingsLink = document.getElementById("settingsLink");
      if (settingsLink) {
        settingsLink.addEventListener("click", function(e) {
          e.preventDefault();
          if (userRole === "buyer") {
            window.location.href = "./user-handling/buyers/buyer_settings.php";
          } else if (userRole === "seller") {
            window.location.href = "./user-handling/sellers/seller_settings.php";
          } else {
            window.location.href = "settings.php";
          }
        });
      }
    });
  </script>
</body>

</html>