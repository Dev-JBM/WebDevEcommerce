<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Handle Add to Cart POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['size'], $_POST['color'], $_POST['quantity'])) {
  $product_id = intval($_POST['product_id']);
  $size = $_POST['size'];
  $color = $_POST['color'];
  $quantity = intval($_POST['quantity']);

  $username = $_SESSION['username'];
  $user_query = "SELECT user_id FROM users WHERE username = ?";
  $stmt = $conn->prepare($user_query);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $user_result = $stmt->get_result();
  $user = $user_result->fetch_assoc();
  $user_id = $user['user_id'];

  $buyer_query = "SELECT buyer_id FROM buyer_profiles WHERE buyer_id = ?";
  $stmt = $conn->prepare($buyer_query);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $buyer_result = $stmt->get_result();
  $buyer = $buyer_result->fetch_assoc();
  $buyer_id = $buyer['buyer_id'];

  $cart_query = "SELECT cart_id FROM carts WHERE buyer_id = ?";
  $stmt = $conn->prepare($cart_query);
  $stmt->bind_param("i", $buyer_id);
  $stmt->execute();
  $cart_result = $stmt->get_result();
  $cart = $cart_result->fetch_assoc();

  if ($cart) {
    $cart_id = $cart['cart_id'];
  } else {
    $insert_cart = "INSERT INTO carts (buyer_id) VALUES (?)";
    $stmt = $conn->prepare($insert_cart);
    $stmt->bind_param("i", $buyer_id);
    $stmt->execute();

    $cart_id = $stmt->insert_id;
  }

  // Insert into cart_items
  $insert_item = "INSERT INTO cart_items (cart_id, product_id, quantity, size, color) VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($insert_item);
  $stmt->bind_param("iiiss", $cart_id, $product_id, $quantity, $size, $color);
  $stmt->execute();

  header("Location: cart.php?added=1");
  exit;
}

// --- Fetch cart items for display ---
$cart_items = [];
$total_items = 0;
$total_price = 0;

$user_id = $user['user_id'];

// Get buyer_id from buyer_profiles
$buyer_query = "SELECT buyer_id FROM buyer_profiles WHERE buyer_id = $user_id LIMIT 1";
$buyer_result = mysqli_query($conn, $buyer_query);
$buyer = mysqli_fetch_assoc($buyer_result);
$buyer_id = $buyer['buyer_id'] ?? null;

if ($buyer_id) {
  $cart_query = "SELECT cart_id FROM carts WHERE buyer_id = $buyer_id LIMIT 1";
  $cart_result = mysqli_query($conn, $cart_query);
  $cart = mysqli_fetch_assoc($cart_result);

  if ($cart) {
    $cart_id = $cart['cart_id'];
    $items_query = "
    SELECT ci.*, p.name, p.price, p.image_path, p.stock_quantity
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.product_id
    WHERE ci.cart_id = $cart_id
    ORDER BY ci.added_at DESC";

    $items_result = mysqli_query($conn, $items_query);
    while ($item = mysqli_fetch_assoc($items_result)) {
      $cart_items[] = $item;
      $total_items += $item['quantity'];
      $total_price += $item['price'] * $item['quantity'];
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
  <link rel="stylesheet" href="style/cart.css">
  <link rel="stylesheet" href="style/cart-list.css">
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
              <a class="sub-menu-text" href="store.php">Back to shopping</a>
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

          <form method="post" action="./features/remove_from_cart.php" id="bulkRemoveForm">
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
                <?php if (empty($cart_items)): ?>
                  <div class="cart-item">
                    <div class=" product-details">
                      <p>Your cart is empty.</p>
                    </div>
                  </div>
                <?php else: ?>
                  <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item" data-product-id="<?= $item['product_id'] ?>">
                      <div class="product-details">
                        <!-- Update this checkbox: -->
                        <input class="item-check-box" type="checkbox" name="cart_item_ids[]" value="<?= $item['cart_item_id'] ?>">
                        <img src="<?= !empty($item['image_path']) ? './images/products/' . htmlspecialchars($item['image_path']) : './images/product_image.png' ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="product-info">
                          <h4><?= htmlspecialchars($item['name']) ?></h4>
                          <p>Variation/s:
                            <span class="size"><?= htmlspecialchars($item['size']) ?></span>,
                            <span class="color"><?= htmlspecialchars($item['color']) ?></span>
                          </p>
                        </div>
                      </div>
                      <div class="price">
                        <p>PHP <span class="discounted-price"><?= number_format($item['price'], 2) ?></span></p>
                      </div>
                      <div class="quantity">
                        <div
                          class="product-qty"
                          data-cart-item-id="<?= $item['cart_item_id'] ?>"
                          data-available="<?= intval($item['stock_quantity'] ?? 99) ?>">
                          <img src="images/minus-svgrepo-com.svg" class="qty-minus" style="height: 20px; margin-right: 0;">
                          <p class="qty-text"><?= intval($item['quantity']) ?></p>
                          <img src="images/add-plus-svgrepo-com.svg" class="qty-plus" style="height: 20px;">
                        </div>
                      </div>
                      <div class="total">
                        <p>PHP <span class="total-ptice"><?= number_format($item['price'] * $item['quantity'], 2) ?></span></p>
                      </div>
                      <div class="actions">
                        <button type="submit" class="remove-btn">Remove</button>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>

              <div class="cart-footer">
                <div class="left-footer">
                  <button type="button" class="select-all-btn">Select All</button>
                  <!-- Change this button to type="submit" -->
                  <button type="submit" class="remove-btn-footer" name="remove_selected">Remove</button>
                </div>
                <div class="right-footer">
                  <div class="total-price-selected-items">
                    <p>Total (<span class="item-selected">2</span> Items): PHP <span class="total-price-selected">3,980</span></p>
                  </div>
                  <button class="check-out-btn">
                    Check Out
                  </button>
                </div>
              </div>
            </div>
          </form>

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

    // PRODUCT COUNTER
    document.querySelectorAll('.product-qty').forEach(function(qtyContainer) {
      const minusBtn = qtyContainer.querySelector('.qty-minus');
      const plusBtn = qtyContainer.querySelector('.qty-plus');
      const qtyText = qtyContainer.querySelector('.qty-text');
      let productQty = parseInt(qtyText.textContent, 10) || 1;
      const available = parseInt(qtyContainer.getAttribute('data-available'), 10) || 1;
      const cartItem = qtyContainer.closest('.cart-item');
      const totalPriceSpan = cartItem.querySelector('.total-ptice');
      const unitPrice = parseFloat(cartItem.querySelector('.discounted-price').textContent.replace(/,/g, ''));

      function highlightQty() {
        qtyText.style.background = "#e0e5db";
        qtyText.style.borderRadius = "8px";
        qtyText.style.transition = "background 0.2s";
        setTimeout(() => {
          qtyText.style.background = "";
        }, 200);
      }

      function updateTotalPrice() {
        const total = unitPrice * productQty;
        totalPriceSpan.textContent = total.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
        updateSelectedTotals();
      }

      minusBtn.style.cursor = "pointer";
      plusBtn.style.cursor = "pointer";

      minusBtn.addEventListener('click', function() {
        if (productQty > 1) {
          productQty--;
          qtyText.textContent = productQty;
          highlightQty();
          updateTotalPrice();
        }
      });

      plusBtn.addEventListener('click', function() {
        if (productQty < available) {
          productQty++;
          qtyText.textContent = productQty;
          highlightQty();
          updateTotalPrice();
        }
      });

      qtyText.contentEditable = true;
      qtyText.spellcheck = false;

      qtyText.addEventListener('blur', function() {
        let val = parseInt(qtyText.textContent.replace(/\D/g, ''), 10);
        if (isNaN(val) || val < 1) val = 1;
        if (val > available) val = available;
        productQty = val;
        qtyText.textContent = productQty;
        highlightQty();
        updateTotalPrice();
      });

      qtyText.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          qtyText.blur();
        }
      });

      updateTotalPrice();
    });

    function updateSelectedTotals() {
      let total = 0;
      let count = 0;
      document.querySelectorAll('.cart-item').forEach(function(item) {
        const checkbox = item.querySelector('.item-check-box');
        if (checkbox && checkbox.checked) {
          const qty = parseInt(item.querySelector('.qty-text').textContent, 10) || 1;
          const price = parseFloat(item.querySelector('.discounted-price').textContent.replace(/,/g, ''));
          total += qty * price;
          count += qty;
        }
      });
      document.querySelector('.item-selected').textContent = count;
      document.querySelector('.total-price-selected').textContent = total.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }

    document.querySelectorAll('.item-check-box').forEach(function(cb) {
      cb.addEventListener('change', updateSelectedTotals);
    });
    updateSelectedTotals();

    function toggleAllCheckboxes() {
      const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
      itemCheckboxes.forEach(cb => cb.checked = !allChecked);
      updateSelectedTotals(); // <-- add this line
    }

    document.addEventListener('DOMContentLoaded', function() {
      const params = new URLSearchParams(window.location.search);
      if (params.get('added') === '1') {
        alert('Product added to cart!');
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    });

    document.querySelectorAll('.cart-item').forEach(function(item) {
      const details = item.querySelector('.product-details');
      if (details) {
        const productId = item.getAttribute('data-product-id');
        if (productId) {
          details.style.cursor = 'pointer';
          details.addEventListener('click', function(e) {
            // Prevent checkbox click from triggering redirect
            if (e.target.classList.contains('item-check-box')) return;
            window.location.href = 'product.php?id=' + productId;
          });
        }
      }
    });
  </script>

  <!-- FOR CHECKOUT -->
  <div id="checkoutModal" class="checkout-modal" style="display:none;">
    <div class="checkout-modal-content">
      <span class="close-modal" id="closeCheckoutModal">&times;</span>
      <h2>Order Summary</h2>
      <form id="checkoutForm" method="post" action="features/checkout.php">
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

  <!-- THIS IS FOR THE POP-UPS -->
  <script>
    // CHECKOUT LOGIC
    document.querySelector('.check-out-btn').addEventListener('click', function(e) {
      e.preventDefault();

      const selectedItems = [];
      const selectedCartItemIds = [];
      document.querySelectorAll('.cart-item').forEach(function(item) {
        const checkbox = item.querySelector('.item-check-box');
        if (checkbox && checkbox.checked) {
          selectedItems.push({
            name: item.querySelector('.product-info h4').textContent,
            size: item.querySelector('.size').textContent,
            color: item.querySelector('.color').textContent,
            quantity: item.querySelector('.qty-text').textContent,
            price: item.querySelector('.discounted-price').textContent,
            total: item.querySelector('.total-ptice').textContent
          });
          selectedCartItemIds.push(checkbox.value);
        }
      });

      if (selectedItems.length === 0) {
        alert('Please select at least one product to check out.');
        return;
      }

      const checkoutItemsDiv = document.getElementById('checkoutItems');
      checkoutItemsDiv.innerHTML = '';
      selectedItems.forEach(function(item) {
        checkoutItemsDiv.innerHTML += `
      <div class="checkout-item">
        <strong>${item.name}</strong><br>
        Size: ${item.size}, Color: ${item.color}<br>
        Quantity: ${item.quantity}<br>
        Price: PHP ${item.price}<br>
        Total: PHP ${item.total}
      </div>
    `;
      });

      const selectedCartItemsDiv = document.getElementById('selectedCartItems');
      selectedCartItemsDiv.innerHTML = '';
      selectedCartItemIds.forEach(function(id) {
        selectedCartItemsDiv.innerHTML += `<input type="hidden" name="cart_item_ids[]" value="${id}">`;
      });

      document.getElementById('checkoutModal').style.display = 'flex';
    });

    document.getElementById('closeCheckoutModal').onclick = function() {
      document.getElementById('checkoutModal').style.display = 'none';
    };
    document.getElementById('cancelCheckoutBtn').onclick = function() {
      document.getElementById('checkoutModal').style.display = 'none';
    };

    window.onclick = function(event) {
      const modal = document.getElementById('checkoutModal');
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    };

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

    document.addEventListener('DOMContentLoaded', function() {
      const params = new URLSearchParams(window.location.search);
      if (params.get('success') === '1') {
        alert('Checkout successful! Your order has been placed.');
        window.history.replaceState({}, document.title, window.location.pathname);
      }
      if (params.get('error') === '1') {
        alert('Checkout failed. Please try again.');
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    });
  </script>
</body>

</html>