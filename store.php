<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);

session_start();
require_once './features/db-connection.php';

$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// PRODUCT INFO FETCHING
$products = [];
$productQuery = "
  SELECT 
    p.*, 
    COALESCE(SUM(oi.quantity), 0) AS sales,
    COALESCE(SUM(oi.quantity * oi.price_at_purchase), 0) AS sold_value,
    ROUND(AVG(r.rating), 1) AS avg_rating
  FROM products p
  LEFT JOIN order_items oi ON p.product_id = oi.product_id
  LEFT JOIN product_reviews r ON p.product_id = r.product_id
  GROUP BY p.product_id
";

$productResult = mysqli_query($conn, $productQuery);
while ($row = mysqli_fetch_assoc($productResult)) {
  $products[] = $row;
}

$imagePath = (!empty($user['image']))
  ? 'images/profiles/' . $user['image']
  : './images/profile-circle-svgrepo-com.png';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wear Dyans | Store</title>
  <link rel="stylesheet" href="style/store.css">
  <link rel="stylesheet" href="style/profilePic.css">
  <link rel="stylesheet" href="style/logout.css">
  <link href="https://fonts.googleapis.com/css2?family=Mynerve&family=Mandali&family=Aoboshi+One&family=Inter:ital,wght@0,100..900;1,100..900&family=MuseoModerno:ital,wght@0,100..900;1,100..900&family=Podkova:wght@400..800&display=swap" rel="stylesheet">
</head>

<body>
  <header class="header">
    <div class="left-header">
      <a href="homepage.php">Wear Dyans</a>
    </div>

    <div class="search-bar">
      <input type="text" id="productSearch" placeholder="Search">
      <img id="searchBtn" src="images/search-svgrepo-com.png" style="cursor:pointer;">
    </div>

    <div class="right-header">
      <a href="cart.php"><img src="images/SVGRepo_iconCarrier.png"></a>
      <img class="profile" src="<?= htmlspecialchars($imagePath); ?>">
      <div class="sub-menu-wrap" id="subMenu">
        <div class="sub-menu">
          <div class="user-info">
            <img class="profile-menu-img" src="<?= htmlspecialchars($imagePath); ?>" alt="profile">
            <h3 id="userName"><?= htmlspecialchars($user['username']); ?></h3>
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
    <div class="ads-section">
      <div class="ads-container">
        <img src="images/image 27.png">
      </div>

      <img class="next-button" src="images/next.png">
      <img class="prev-button" src="images/prev.png">

      <img class="ads-pages" src="images/ads_pages.png">
    </div>

    <div class="products-section">
      <div class="filter-container">
        <div class="filter-header">
          <h1>Filter</h1>
        </div>
        <div class="filter-items">
          <div class="filter-section">
            <h3>Gender</h3>
            <label><input type="checkbox" name="gender" value="Men"> Men</label><br>
            <label><input type="checkbox" name="gender" value="Women"> Women</label><br>
            <label><input type="checkbox" name="gender" value="Unisex"> Unisex</label>
          </div>

          <div class="filter-section">
            <h3>Clothes</h3>
            <label><input type="checkbox" name="clothes" value="T-shirt"> T-shirt</label><br>
            <label><input type="checkbox" name="clothes" value="Polo Shirt"> Polo Shirt</label><br>
            <label><input type="checkbox" name="clothes" value="Polo"> Polo</label><br>
            <label><input type="checkbox" name="clothes" value="Long Sleeve"> Long Sleeve</label><br>
            <label><input type="checkbox" name="clothes" value="Jacket"> Jacket</label><br>
            <label><input type="checkbox" name="clothes" value="Hoodie"> Hoodie</label><br>
            <label><input type="checkbox" name="clothes" value="Pants"> Pants</label><br>
            <label><input type="checkbox" name="clothes" value="Short"> Short</label><br>
            <label><input type="checkbox" name="clothes" value="Underwear"> Underwear</label><br>
            <label><input type="checkbox" name="clothes" value="Sock"> Sock</label>
          </div>

          <div class="filter-section">
            <h3>Accessories</h3>
            <label><input type="checkbox" name="accessories" value="Watch"> Watch</label><br>
            <label><input type="checkbox" name="accessories" value="Glasses"> Glasses</label><br>
            <label><input type="checkbox" name="accessories" value="Earring"> Earring</label><br>
            <label><input type="checkbox" name="accessories" value="Bracelet"> Bracelet</label><br>
            <label><input type="checkbox" name="accessories" value="Ring"> Ring</label><br>
            <label><input type="checkbox" name="accessories" value="Hat"> Hat</label><br>
            <label><input type="checkbox" name="accessories" value="Necklace"> Necklace</label>
          </div>
        </div>
      </div>

      <div class="products-container">
        <div class="products-sort">
          <div class="dropdown">
            <img class="arrow-down" src="./images/arrow-down-338-svgrepo-com.svg">
            <img class="arrow-up" src="./images/arrow-up-338-svgrepo-com.svg">
            <label for="filter">Sort by:</label>
            <select id="filter" name="filter">
              <option value="Popular">Popular</option>
              <option value="Latest">Latest</option>
              <option value="Sales">Sales</option>
            </select>
          </div>

          <div class="sort-button">
            <img class="asc" src="./images/sort-from-bottom-to-top-svgrepo-com.svg">
            <img class="desc" src="./images/sort-from-top-to-bottom-svgrepo-com.svg">
          </div>
        </div>

        <div class="products">
          <?php foreach ($products as $product): ?>
            <a href="product.php?id=<?= $product['product_id'] ?>" class="product-link" style="text-decoration:none; color:inherit;">
              <div class="products-box"
                data-gender="<?= htmlspecialchars($product['gender']) ?>"
                data-category="<?= htmlspecialchars($product['category']) ?>"
                data-type="<?= htmlspecialchars($product['type']) ?>"
                data-sales="<?= intval($product['sales']) ?>"
                data-created="<?= htmlspecialchars($product['created_at']) ?>"
                data-rating="0">

                <div class="product-img">
                  <img src="<?= !empty($product['image_path']) ? './images/products/' . htmlspecialchars($product['image_path']) : './images/product_image.png' ?>" alt="Product Image">
                </div>
                <div class="product-text">
                  <div class="product-name">
                    <p><?= htmlspecialchars($product['name']) ?></p>
                  </div>
                  <div class="product-price">
                    <p>₱ <?= number_format($product['price'], 2) ?></p>
                  </div>
                  <div class="product-bottom-text">
                    <div class="product-rating">
                      <p>
                        <?php if ($product['avg_rating'] !== null): ?>
                          <?= htmlspecialchars($product['avg_rating']) ?>
                        <?php else: ?>
                          No ratings yet
                        <?php endif; ?>
                      </p>
                      <?php if ($product['avg_rating'] !== null): ?>
                        <svg viewBox="0 0 140 30" xmlns="http://www.w3.org/2000/svg">
                          <defs>
                            <polygon id="star" points="10,0 12.6,6.5 20,7.5 14.5,12.5 16,20 10,16 4,20 5.5,12.5 0,7.5 7.4,6.5" />
                          </defs>
                          <?php
                          $fullStars = floor($product['avg_rating']);
                          $hasHalfStar = ($product['avg_rating'] - $fullStars) > 0;
                          $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                          $starX = 0;
                          for ($i = 0; $i < $fullStars; $i++, $starX += 25) {
                            echo '<use href="#star" x="' . $starX . '" y="5" fill="#FFD700"/>';
                          }
                          if ($hasHalfStar) {
                            echo '
                              <defs>
                                <linearGradient id="halfGrad" x1="0" x2="1" y1="0" y2="0">
                                  <stop offset="50%" stop-color="#FFD700"/>
                                  <stop offset="50%" stop-color="#ccc"/>
                                </linearGradient>
                              </defs>
                              <use href="#star" x="' . $starX . '" y="5" fill="url(#halfGrad)"/>
                  ';
                            $starX += 25;
                          }
                          for ($i = 0; $i < $emptyStars; $i++, $starX += 25) {
                            echo '<use href="#star" x="' . $starX . '" y="5" fill="#ccc"/>';
                          }
                          ?>
                        </svg>
                      <?php endif; ?>
                    </div>
                    <div class="product-sales">
                      <p><?= intval($product['sales']) ?> Sold</p>
                    </div>
                  </div>
                  <div class="product-stock-status">
                    <?php if (intval($product['stock_quantity']) > 0): ?>
                      <span class="stock-available"><?= intval($product['stock_quantity']) ?> in stock</span>
                    <?php else: ?>
                      <span class="stock-out">Out of stock</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
          <div id="noProductsMsg" style="display:none; text-align:center; width:100%; color:#888; font-size:1.2em; margin-top:2em;">
            No products match the selected filters.
          </div>
        </div>

  </section>


  <script>
    // TOGGLE MENU
    let subMenu = document.getElementById("subMenu");

    function toggleMenu() {
      subMenu.classList.toggle("open-menu");
    }
    // Sort Select
    const select = document.getElementById("filter");
    const arrowDown = document.querySelector(".arrow-down");
    const arrowUp = document.querySelector(".arrow-up");

    let isOpen = false;


    select.addEventListener("click", () => {
      isOpen = !isOpen;
      arrowDown.style.display = isOpen ? "none" : "inline";
      arrowUp.style.display = isOpen ? "inline" : "none";
    });


    select.addEventListener("blur", () => {
      isOpen = false;
      arrowDown.style.display = "inline";
      arrowUp.style.display = "none";
    });


    arrowUp.addEventListener("click", (e) => {
      e.preventDefault();
      isOpen = false;
      arrowDown.style.display = "inline";
      arrowUp.style.display = "none";
      select.blur();
    });

    // PRODUCT SORTING - ASCENDING/DESCENDING
    let sortDirection = 'desc'; // Default: descending

    function sortProducts() {
      const sortValue = document.getElementById('filter').value;
      const productsContainer = document.querySelector('.products');
      const productLinks = Array.from(productsContainer.querySelectorAll('.product-link'));

      let sortedLinks = productLinks.slice();

      sortedLinks.sort((a, b) => {
        const boxA = a.querySelector('.products-box');
        const boxB = b.querySelector('.products-box');
        let valA, valB;

        if (sortValue === 'Popular') {

          const ratingA = parseFloat(boxA.getAttribute('data-rating')) || 0;
          const ratingB = parseFloat(boxB.getAttribute('data-rating')) || 0;
          const salesA = parseInt(boxA.getAttribute('data-sales')) || 0;
          const salesB = parseInt(boxB.getAttribute('data-sales')) || 0;
          if (ratingA !== ratingB) {
            valA = ratingA;
            valB = ratingB;
          } else {
            valA = salesA;
            valB = salesB;
          }
        } else if (sortValue === 'Latest') {
          valA = new Date(boxA.getAttribute('data-created'));
          valB = new Date(boxB.getAttribute('data-created'));
        } else if (sortValue === 'Sales') {
          valA = parseInt(boxA.getAttribute('data-sales')) || 0;
          valB = parseInt(boxB.getAttribute('data-sales')) || 0;
        } else {
          valA = 0;
          valB = 0;
        }


        if (sortDirection === 'asc') {
          return valA > valB ? 1 : valA < valB ? -1 : 0;
        } else {
          return valA < valB ? 1 : valA > valB ? -1 : 0;
        }
      });

      productLinks.forEach(link => link.parentNode.removeChild(link));
      sortedLinks.forEach(link => productsContainer.appendChild(link));
    }

    const asc = document.querySelector(".asc");
    const desc = document.querySelector(".desc");

    desc.addEventListener("click", () => {
      desc.style.display = "none";
      asc.style.display = "inline";
      sortDirection = 'asc';
      sortProducts();
      filterProducts();
    });

    asc.addEventListener("click", () => {
      asc.style.display = "none";
      desc.style.display = "inline";
      sortDirection = 'desc';
      sortProducts();
      filterProducts();
    });

    document.getElementById('filter').addEventListener('change', function() {
      sortProducts();
      filterProducts();
    });

    sortProducts();

    // PRODUCT FILTERING
    function getCheckedValues(name) {
      return Array.from(document.querySelectorAll('input[name="' + name + '"]:checked')).map(cb => cb.value);
    }

    function filterProducts() {
      const genderVals = getCheckedValues('gender');
      const clothesVals = getCheckedValues('clothes');
      const accessoriesVals = getCheckedValues('accessories');

      let typeVals = [];
      if (clothesVals.length) typeVals = typeVals.concat(clothesVals);
      if (accessoriesVals.length) typeVals = typeVals.concat(accessoriesVals);

      const products = document.querySelectorAll('.products-box');
      let anyVisible = false;

      products.forEach(box => {
        const gender = box.getAttribute('data-gender');
        const category = box.getAttribute('data-category');
        const type = box.getAttribute('data-type');

        let show = true;

        if (genderVals.length && !genderVals.includes(gender)) show = false;

        if ((clothesVals.length || accessoriesVals.length)) {
          if (clothesVals.length && category !== 'Clothes') show = false;
          if (accessoriesVals.length && category !== 'Accessories') show = false;
        }

        if (typeVals.length && !typeVals.includes(type)) show = false;

        box.parentElement.style.display = show ? '' : 'none';
        if (show) anyVisible = true;
      });

      document.getElementById('noProductsMsg').style.display = anyVisible ? 'none' : 'block';
    }

    document.querySelectorAll('.filter-container input[type="checkbox"]').forEach(cb => {
      cb.addEventListener('change', filterProducts);
    });

    filterProducts();


    // PRODUCT SEARCHING
    function searchProducts() {
      const searchValue = document.getElementById('productSearch').value.trim().toLowerCase();
      const products = document.querySelectorAll('.products-box');
      let anyVisible = false;

      products.forEach(box => {
        const name = box.querySelector('.product-name p').textContent.toLowerCase();
        const desc = box.querySelector('.product-text').textContent.toLowerCase();

        const matches = name.includes(searchValue) || desc.includes(searchValue);

        if (matches && box.parentElement.style.display !== "none") {
          box.parentElement.style.display = "";
          anyVisible = true;
        } else {
          box.parentElement.style.display = "none";
        }
      });

      document.getElementById('noProductsMsg').style.display = anyVisible ? 'none' : 'block';
    }

    document.getElementById('productSearch').addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        searchProducts();
      }
    });

    document.getElementById('searchBtn').addEventListener('click', function() {
      searchProducts();
    });

    document.getElementById('productSearch').addEventListener('input', function() {
      if (this.value.trim() === '') {
        filterProducts();
      }
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
  </script>


  <!-- CHECKS THE USER IF THEY ARE LOGGED-IN or NOT -->
  <!-- LAYOUT -->
  <div id="loginPromptModal" class="logout-modal">
    <div class="logout-modal-content">
      <svg width="48" height="48" fill="none" viewBox="0 0 24 24" style="margin-bottom: 1em;">
        <circle cx="12" cy="12" r="12" fill="#ffe5e5" />
        <path d="M12 8v4" stroke="#e74c3c" stroke-width="2" stroke-linecap="round" />
        <circle cx="12" cy="16" r="1" fill="#e74c3c" />
      </svg>
      <p class="logout-modal-title">Not Logged In</p>
      <p class="logout-modal-desc">You must log in first to access this feature.</p>
      <div class="logout-modal-actions">
        <button id="loginPromptYes" class="logout-btn login-btn-yes">Log In</button>
        <button id="loginPromptNo" class="logout-btn logout-btn-no">Cancel</button>
      </div>
    </div>
  </div>

  <!-- ACTUAL CHECKER -->
  <script>
    // Get PHP login status
    const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

    // Cart and profile elements
    const cartLink = document.querySelector('.right-header a[href="cart.php"]');
    const profileImg = document.querySelector('.right-header .profile');

    // Show login prompt modal
    function showLoginPrompt(e) {
      e.preventDefault();
      document.getElementById("loginPromptModal").style.display = "flex";
    }

    // Cart click
    if (cartLink) {
      cartLink.addEventListener("click", function(e) {
        if (!isLoggedIn) {
          showLoginPrompt(e);
        }
        // else, default behavior (link works)
      });
    }

    // Profile click
    if (profileImg) {
      profileImg.addEventListener("click", function(e) {
        if (!isLoggedIn) {
          showLoginPrompt(e);
        } else {
          toggleMenu();
        }
      });
    }

    // Login prompt modal buttons
    document.getElementById("loginPromptNo").addEventListener("click", function() {
      document.getElementById("loginPromptModal").style.display = "none";
    });
    document.getElementById("loginPromptYes").addEventListener("click", function() {
      window.location.href = "homepage.php"; // or your login page
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