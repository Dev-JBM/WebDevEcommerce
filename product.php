<?php

session_start();
require_once './features/db-connection.php';

if (!isset($_SESSION['username'])) {
  header("Location: homepage.php");
  exit;
}

$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$imagePath = (!empty($user['image']))
  ? 'images/profiles/' . $user['image']
  : './images/profile-circle-svgrepo-com.png';

// --- PRODUCT FETCHING BASED ON ID ---
$product = null;
if (isset($_GET['id']) || isset($_GET['product_id'])) {
  $product_id = isset($_GET['id']) ? intval($_GET['id']) : intval($_GET['product_id']);
  $product_query = "SELECT * FROM products WHERE product_id = $product_id LIMIT 1";
  $product_result = mysqli_query($conn, $product_query);
  $product = mysqli_fetch_assoc($product_result);
}

if (!$product) {
  echo "<script>alert('Product not found.'); window.location.href='store.php';</script>";
  exit;
}

$sold_qty = 0;
if ($product) {
  $pid = intval($product['product_id']);
  $sold_query = "SELECT COALESCE(SUM(quantity), 0) AS sold_qty FROM order_items WHERE product_id = $pid";
  $sold_result = mysqli_query($conn, $sold_query);
  if ($sold_row = mysqli_fetch_assoc($sold_result)) {
    $sold_qty = $sold_row['sold_qty'];
  }
}

if (isset($_GET['success'])) {
  echo "<script>alert('Order placed successfully!');</script>";
}
if (isset($_GET['error'])) {
  echo "<script>alert('There was an error processing your order.');</script>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wear Dyans | Product</title>
  <link rel="stylesheet" href="style/product.css">
  <link rel="stylesheet" href="style/checkout.css">
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

      <div class="search-bar">
        <input type="text" id="productSearch" placeholder="Search">
        <img id="searchBtn" src="images/search-svgrepo-com.png" style="cursor:pointer;">
      </div>

      <div class="right-header">
        <a href="cart.php"><img src="images/SVGRepo_iconCarrier.png"></a>
        <img class="profile" src="<?= htmlspecialchars($imagePath); ?>" onclick="toggleMenu()" alt="profile">
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
      <div class="product-section">
        <div class="item-container">
          <div class="product-left-container">
            <img src="<?= !empty($product['image_path']) ? './images/products/' . htmlspecialchars($product['image_path']) : './images/prdouct.png' ?>" alt="Product Image">
          </div>
          <div class="product-right-container">
            <div class="product-name">
              <p><?= htmlspecialchars($product['name']) ?></p>
            </div>

            <div>
              <div class="product-rating">
                <div class="rating">0.0
                  <!-- Placeholder for rating stars -->
                  <svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                      <polygon id="star" points="10,0 12.6,6.5 20,7.5 14.5,12.5 16,20 10,16 4,20 5.5,12.5 0,7.5 7.4,6.5" />
                    </defs>
                    <use href="#star" x="0" y="5" />
                    <use href="#star" x="25" y="5" />
                    <use href="#star" x="50" y="5" />
                    <use href="#star" x="75" y="5" />
                    <use href="#star" x="100" y="5" />
                  </svg>
                </div>
                <p class="qty-rating">
                  <span class="qty-number">| 0 </span>Ratings
                  <span class="sales-number">| <?= intval($sold_qty) ?> </span>Sold
                </p>
              </div>
            </div>

            <div class="onsale-price">
              <p class="onsale">ON SALE !</p>
              <div class="price-container">
                <p class="new-price">PHP <?= number_format($product['price'], 2) ?></p>
              </div>
            </div>



            <div class="variation-container">
              <div class="sizes-container">
                <div class="sizes">
                  <?php if (!empty($product['sizes_available'])): ?>
                    <?php foreach (explode(',', $product['sizes_available']) as $size): ?>
                      <div><?= htmlspecialchars(trim($size)) ?></div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
                <p class="text-sizes">Size: <span class="size-picked"></span></p>
              </div>

              <div class="colors-container">
                <div class="colors-dropdown">
                  <button id="colorDropdownBtn" type="button">
                    <span id="selectedColorCircle"></span>
                    <span id="selectedColorText"></span>
                    <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 20 20">
                      <path d="M5.5 8l4.5 4.5L14.5 8" stroke="#333" stroke-width="2" fill="none" stroke-linecap="round" />
                    </svg>
                  </button>
                  <div id="colorDropdownList">
                    <?php if (!empty($product['colors_available'])): ?>
                      <?php foreach (explode(',', $product['colors_available']) as $color): $color = trim($color); ?>
                        <div class="color-option" data-color="<?= htmlspecialchars($color) ?>">
                          <span class="color-circle" style="background:<?= htmlspecialchars($color) ?>"></span>
                          <span><?= htmlspecialchars($color) ?></span>
                        </div>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                </div>
                <p class="text-colors">Color: <span class="color-picked"></span></p>
              </div>
            </div>

            <div class="product-qty-container">
              <div class="product-qty">
                <img src="images/minus-svgrepo-com.svg">
                <p class="qty-text">1</p>
                <img src="images/add-plus-svgrepo-com.svg">
              </div>
              <?php if (intval($product['stock_quantity']) > 0): ?>
                <p class="available-text"><span><?= intval($product['stock_quantity']) ?></span> pieces available</p>
              <?php else: ?>
                <p class="available-text out-of-stock" style="color: #e74c3c; font-weight: bold;">Out of stock</p>
              <?php endif; ?>
            </div>

            <div class="btns-product-right-container">
              <!-- Place this inside your .btns-product-right-container or wherever your Add to Cart button is -->
              <form id="addToCartForm" action="cart.php" method="post">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                <input type="hidden" name="size" id="cartSize">
                <input type="hidden" name="color" id="cartColor">
                <input type="hidden" name="quantity" id="cartQty">
                <button type="submit" class="btn-cart">
                  <img src="images/SVGRepo_iconCarrier_brown.png">
                  Add to Cart
                </button>
              </form>
              <div>
                <button class="btn-buy">
                  Buy Now
                </button>
              </div>
            </div>
          </div>


        </div>
        <div class="about-item-container">
          <div class="description-container">
            <div class="title-description">
              <p>Description</p>
            </div>

            <div class="description">
              <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>
          </div>

          <div class="size-reference-container">
            <div class="title-size-reference">
              <p>Size Reference</p>
            </div>
            <table>
              <tr>
                <th>Size</th>
                <th>Body length back</th>
                <th>Shoulder width</th>
                <th>Body width</th>
                <th>Sleeve length<br>(center back)</th>
              </tr>
              <tr>
                <td>S</td>
                <td>65.5</td>
                <td>47</td>
                <td>53</td>
                <td>79.5</td>
              </tr>
              <tr>
                <td>M</td>
                <td>67.5</td>
                <td>48.5</td>
                <td>56</td>
                <td>82</td>
              </tr>
              <tr>
                <td>L</td>
                <td>69.5</td>
                <td>50</td>
                <td>59</td>
                <td>84.5</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="feedback-container">
          <div class="product-ratings-container">
            <div class="title-product-ratings">
              <p>Product Ratings</p>
            </div>

            <div class="content-product-ratings">
              <div class="rating-sort-container">
                <div class="rating-sort-box">
                  <div class="avg-rating">
                    <p>Avg. Rating:</p>
                    <svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg">
                      <defs>
                        <polygon id="star" points="10,0 12.6,6.5 20,7.5 14.5,12.5 16,20 10,16 4,20 5.5,12.5 0,7.5 7.4,6.5" />
                      </defs>
                      <use href="#star" x="0" y="5" />
                      <use href="#star" x="25" y="5" />
                      <use href="#star" x="50" y="5" />
                      <use href="#star" x="75" y="5" />
                      <use href="#star" x="100" y="5" />
                    </svg>
                    <p class="num-stars">5/5 <span class="qty-product-ratings">(25)</span></p>
                  </div>

                  <div class="sort-rating">
                    <div class="products-sort">
                      <div class="dropdown">
                        <img class="arrow-down" src="./images/arrow-down-338-svgrepo-com.svg">
                        <img class="arrow-up" src="./images/arrow-up-338-svgrepo-com.svg">
                        <label for="filter">Sort Ratings by:</label>
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
                  </div>
                </div>
              </div>

              <div class="feedback-box">
                <div class="feedback-top-container">
                  <div class="profile-box">

                    <div class="profile-pic">
                      <img src="./images/profile-circle-svgrepo-com.png">
                    </div>

                    <div class="name-container">
                      <p class="name">user123</p>
                      <svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                          <polygon id="star" points="10,0 12.6,6.5 20,7.5 14.5,12.5 16,20 10,16 4,20 5.5,12.5 0,7.5 7.4,6.5" />
                        </defs>
                        <use href="#star" x="0" y="5" />
                        <use href="#star" x="25" y="5" />
                        <use href="#star" x="50" y="5" />
                        <use href="#star" x="75" y="5" />
                        <use href="#star" x="100" y="5" />
                      </svg>
                    </div>
                  </div>

                  <div class="date">
                    <p>3/22/2025</p>
                  </div>

                </div>

                <div class="feedback-bottom-container">
                  <p class="variation-feedback">Variation: <span class="feedback-size">M</span>, <span class="feedback-color">Green</span></p>
                  <p class="comment">This fleece jacket is both stylish and functional. The bicolor design adds a trendy touch, making it easy to pair with different outfits. The material is soft, warm, and perfect for chilly weather. The fit is true to size, and the zipper feels sturdy. Great for casual wear or layering in colder months. Highly recommended!</p>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

    </section>
  </main>

  <script>
    let selectedSize = null;
    let selectedColor = null;
    let productQty = 1;
    const stockAvailable = <?= intval($product['stock_quantity']) ?>;

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

    // ASC and DESC button
    const asc = document.querySelector(".asc");
    const desc = document.querySelector(".desc");

    desc.addEventListener("click", () => {
      desc.style.display = "none";
      asc.style.display = "inline";
    });

    asc.addEventListener("click", () => {
      asc.style.display = "none";
      desc.style.display = "inline";
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

    // DROPDOWN FOR COLORS
    document.addEventListener('DOMContentLoaded', function() {
      const btn = document.getElementById('colorDropdownBtn');
      const list = document.getElementById('colorDropdownList');
      const colorPicked = document.querySelector('.color-picked');
      const selectedColorCircle = document.getElementById('selectedColorCircle');
      const selectedColorText = document.getElementById('selectedColorText');
      const firstColorOption = document.querySelector('.color-option');

      selectedColorCircle.style.background = "#ccc";
      selectedColorText.textContent = "Choose a color";
      colorPicked.textContent = "None";

      if (btn && list) {
        btn.addEventListener('click', function(e) {
          e.stopPropagation();
          list.style.display = list.style.display === 'block' ? 'none' : 'block';
        });

        document.querySelectorAll('.color-option').forEach(function(opt) {
          opt.addEventListener('click', function(e) {
            const color = this.getAttribute('data-color');
            selectedColorCircle.style.background = color;
            selectedColorText.textContent = color;
            colorPicked.textContent = color;
            selectedColor = color;
            list.style.display = 'none';
          });
        });


        document.addEventListener('click', function() {
          list.style.display = 'none';
        });
      }
    });

    // SIZES SELECTION LOGIC
    const sizeDivs = document.querySelectorAll('.sizes div');
    const sizePicked = document.querySelector('.size-picked');

    sizeDivs.forEach(function(div) {
      div.style.cursor = "pointer";
      div.addEventListener('click', function() {

        sizeDivs.forEach(d => d.classList.remove('selected-size'));

        this.classList.add('selected-size');
        selectedSize = this.textContent.trim();
        if (sizePicked) sizePicked.textContent = selectedSize;
      });
    });

    // PRODUCT QUANTITY COUNTER
    document.addEventListener('DOMContentLoaded', function() {
      const qtyContainer = document.querySelector('.product-qty');
      if (!qtyContainer) return;
      const minusBtn = qtyContainer.querySelector('img[src*="minus"]');
      const plusBtn = qtyContainer.querySelector('img[src*="add-plus"]');
      const qtyText = qtyContainer.querySelector('.qty-text');
      const available = parseInt(document.querySelector('.available-text span').textContent, 10) || 1;

      qtyText.contentEditable = true;
      qtyText.spellcheck = false;
      qtyText.textContent = productQty;

      function highlightQty() {
        qtyText.style.background = "#e0e5db";
        qtyText.style.borderRadius = "8px";
        qtyText.style.transition = "background 0.2s";
        setTimeout(() => {
          qtyText.style.background = "";
        }, 200);
      }

      minusBtn.style.cursor = "pointer";
      plusBtn.style.cursor = "pointer";

      minusBtn.addEventListener('click', function() {
        if (productQty > 1) {
          productQty--;
          qtyText.textContent = productQty;
          highlightQty();
        }
      });

      plusBtn.addEventListener('click', function() {
        if (productQty < available) {
          productQty++;
          qtyText.textContent = productQty;
          highlightQty();
        }
      });

      qtyText.addEventListener('blur', function() {
        let val = parseInt(qtyText.textContent.replace(/\D/g, ''), 10);
        if (isNaN(val) || val < 1) val = 1;
        if (val > available) val = available;
        productQty = val;
        qtyText.textContent = productQty;
        highlightQty();
      });

      qtyText.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          qtyText.blur();
        }
      });
    });

    // OUT OF STOCK CHECKER
    document.getElementById('addToCartForm').addEventListener('submit', function(e) {
      document.getElementById('cartSize').value = selectedSize;
      document.getElementById('cartColor').value = selectedColor;
      document.getElementById('cartQty').value = productQty;

      if (stockAvailable === 0) {
        alert('This product is OUT OF STOCK and cannot be added to cart.');
        e.preventDefault();
        return;
      }

      if (!selectedSize || !selectedColor || productQty < 1) {
        alert('Please select a size, color, and valid quantity.');
        e.preventDefault();
      }
    });
  </script>

  <!-- FOR CHECKOUT -->
  <div id="checkoutModal" class="checkout-modal" style="display:none;">
    <div class="checkout-modal-content">
      <span class="close-modal" id="closeCheckoutModal">&times;</span>
      <h2>Order Summary</h2>
      <form id="checkoutForm" method="post" action="features/buy-now.php">
        <div id="checkoutItems"></div>
        <div class="buyer-address">
          <h3>Shipping Address</h3>
          <p id="buyerAddress"><?= htmlspecialchars($user['address'] ?? 'No address on file') ?></p>
          <input type="hidden" name="shipping_address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
          <input type="hidden" name="buyer_id" value="<?= htmlspecialchars($user['user_id'] ?? '') ?>">
        </div>
        <div class="payment-method">
          <h3>Payment Method</h3>
          <label><input type="radio" name="payment_method" value="cash on delivery" checked> Cash on Delivery</label><br>
          <label><input type="radio" name="payment_method" value="credit/debit"> Credit/Debit</label><br>
          <label><input type="radio" name="payment_method" value="e-wallet"> E-wallet</label>
        </div>
        <!-- Hidden container for selected cart_item_ids[] -->
        <div id="selectedCartItems"></div>
        <div class="checkout-btn-row" style="margin-top: 1em;">
          <button type="button" id="cancelCheckoutBtn">Cancel</button>
          <button type="submit" id="confirmCheckoutBtn">Confirm Order</button>
        </div>
      </form>
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

  <!-- FOR POP-UPS -->
  <script>
    // CHECKOUT LOGIC FOR "BUY NOW" BUTTON ON PRODUCT PAGE
    document.querySelector('.btn-buy').addEventListener('click', function(e) {
      e.preventDefault();
      if (!selectedSize || !selectedColor || productQty < 1) {
        alert('Please select a size, color, and valid quantity.');
        return;
      }
      if (stockAvailable === 0) {
        alert('This product is OUT OF STOCK and cannot be purchased.');
        return;
      }

      const productName = document.querySelector('.product-name p').textContent;
      const productPrice = document.querySelector('.new-price').textContent.replace('PHP', '').trim();
      const total = (parseFloat(productPrice.replace(/,/g, '')) * productQty).toFixed(2);

      const checkoutItemsDiv = document.getElementById('checkoutItems');
      checkoutItemsDiv.innerHTML = `
    <div class="checkout-item">
      <strong>${productName}</strong><br>
      Size: ${selectedSize}, Color: ${selectedColor}<br>
      Quantity: ${productQty}<br>
      Price: PHP ${productPrice}<br>
      Total: PHP ${total}
    </div>
  `;

      const selectedCartItemsDiv = document.getElementById('selectedCartItems');
      selectedCartItemsDiv.innerHTML = `
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
    <input type="hidden" name="size" value="${selectedSize}">
    <input type="hidden" name="color" value="${selectedColor}">
    <input type="hidden" name="quantity" value="${productQty}">
    <input type="hidden" name="price" value="${productPrice}">
    <input type="hidden" name="total" value="${total}">
  `;

      document.getElementById('checkoutModal').style.display = 'flex';
    });

    document.getElementById('closeCheckoutModal').addEventListener('click', function() {
      document.getElementById('checkoutModal').style.display = 'none';
    });
    document.getElementById('cancelCheckoutBtn').addEventListener('click', function() {
      document.getElementById('checkoutModal').style.display = 'none';
    });

    // LOGOUT LOGIC
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
</body>

</html>