<?php
session_start();

require_once '../../features/db-connection.php';

if (!isset($_SESSION['username'])) {
  header("Location: ../../homepage.php");
  exit;
}

$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["fileImg"]["name"])) {
  $username = $_POST['username'] ?? $_SESSION['username'];
  $src = $_FILES["fileImg"]["tmp_name"];
  $imageName = uniqid() . "_" . basename($_FILES["fileImg"]["name"]);
  $target = "../../images/profiles/" . $imageName;

  if (is_uploaded_file($src)) {
    if (move_uploaded_file($src, $target)) {
      $query = "UPDATE users SET image = ? WHERE username = ?";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "ss", $imageName, $username);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
      header("Location: ../../user-handling/sellers/seller_settings.php");
      exit;
    }
  }
}

$imagePath = (!empty($user['image']))
  ? '../../images/profiles/' . $user['image']
  : '../../images/profile-circle-svgrepo-com.png';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wear Dyans | Settings</title>
  <link rel="stylesheet" href="../../style/settings.css">
  <link rel="stylesheet" href="../../style/profilePic.css">
  <link rel="stylesheet" href="../../style/logout.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Mynerve&family=Mandali&family=Aoboshi+One&family=Inter:ital,wght@0,100..900;1,100..900&family=MuseoModerno:ital,wght@0,100..900;1,100..900&family=Podkova:wght@400..800&display=swap" rel="stylesheet">
</head>

<body>
  <main>
    <header class="header">
      <div class="left-header">
        <a href="../../store.php">Wear Dyans</a>
      </div>

      <div class="right-header">
        <a href="../../cart.php"><img src="../../images/SVGRepo_iconCarrier.png"></a>
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
              <a class="sub-menu-text" href="../../store.php">Back to shopping</a>
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
      <div class="profile-menu-wrapper">
        <div class="profile-container">
          <div class="userImg">
            <form action="" enctype="multipart/form-data" method="post">
              <input type="hidden" name="username" value="<?= htmlspecialchars($_SESSION['username']); ?>">
              <div class="userImg-pic">
                <img src="<?= htmlspecialchars($imagePath); ?>" id="image">
                <div class="leftRound" id="cancelPicBtn" style="display: none;">
                  <span class="btn-icon" aria-label="Cancel" title="Cancel">&#10006;</span> <!-- ✖ or ❌ -->
                </div>
                <div class="rightRound" id="uploadBtn">
                  <input type="file" name="fileImg" id="fileImg" accept=".jpg, .jpeg, .png">
                  <span class="btn-icon" aria-label="Choose" title="Choose">&#128247;</span> <!-- 📷 -->
                </div>
                <div class="rightRound" id="confirmPicBtn" style="display: none; background: #00C851;">
                  <input type="submit" id="check" value="">
                  <span class="btn-icon" aria-label="Confirm" title="Confirm">&#10004;</span> <!-- ✔ or ✅ -->
                </div>
              </div>
              <p>Edit Profile Picture - Click Camera Icon<br>Allowed format: .jpg, .jpeg, .png</p>
            </form>
          </div>
        </div>
        <div class="menu-container">
          <div class="btn-container">
            <button type="button" class="my-account-btn">
              <img src="../../images/person-male-svgrepo-com.svg">
              My Account
            </button>
            <button type="button" class="my-products-btn">
              <img src="../../images/product-svgrepo-com.svg">
              My Products
            </button>
            <button type="button" class="add-product-btn">
              <img src="../../images/add-square-svgrepo-com.svg">
              Add Product
            </button>
            <hr>
            <hr>
            <hr>
            <button type="button" class="orders-btn">
              <img src="../../images/order-svgrepo-com.svg">
              Orders
            </button>
            <button type="button" class="earnings-btn">
              <img src="../../images/money-svgrepo-com.svg">
              Earnings
            </button>
            <button type="button" class="logout-btn" id="logoutMenu">
              <img src="../../images/logout-svgrepo-com.svg">
              Logout
            </button>
          </div>
        </div>
      </div>

      <div class="my-account">
        <div class="title">
          <p>Profile</p>
        </div>

        <div class="profile-content">
          <form action="" onsubmit="return false;">
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
                  <img class="eye-close" src="../../images/eye-close-svgrepo-com.svg" style="display:inline;">
                  <img class="eye-open" src="../../images/eye-2-svgrepo-com.svg" style="display:none;">
                </span>
              </div>
              <div class="input-box">
                <label class="add-product-label" for="newpassword">New Password:</label>
                <input class="password-input" type="password" id="newpassword" name="newpassword" readonly>
                <span class="toggle-password">
                  <img class="eye-close" src="../../images/eye-close-svgrepo-com.svg" style="display:inline;">
                  <img class="eye-open" src="../../images/eye-2-svgrepo-com.svg" style="display:none;">
                </span>
              </div>
              <div class="input-box">
                <label class="add-product-label" for="confirmnewpassword" style="font-size: 22px;">Confirm New Password:</label>
                <input class="password-input" type="password" id="confirmnewpassword" name="confirmnewpassword" readonly>
                <span class="toggle-password">
                  <img class="eye-close" src="../../images/eye-close-svgrepo-com.svg" style="display:inline;">
                  <img class="eye-open" src="../../images/eye-2-svgrepo-com.svg" style="display:none;">
                </span>
              </div>

            </div>

            <div class="edit-btn-container">
              <button id="editProfileBtn" class="profile-edit-btn" type="button">Edit</button>
              <button id="updateProfileBtn" class="profile-edit-btn" type="submit" style="display:none;">Update</button>
              <button id="cancelEditBtn" class="profile-edit-btn" type="button" style="display:none;">Cancel</button>
            </div>
          </form>
        </div>
      </div>

      <div class="my-products">
        <div class="title">
          <p>My Products</p>
        </div>

        <?php
        // Get seller_id from the logged-in user
        $seller_id = $user['user_id'];
        $query = "SELECT * FROM products WHERE seller_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $seller_id);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <div class="my-products-content">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Created_At</th>
                <th>Updated_At</th>
                <th>Product Name</th>
                <th>Product Image</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>

              <?php if ($result->num_rows === 0): ?>
                <tr>
                  <td class="id-input" colspan="8" style="text-align:center; font-size:1.2em; color:#888;">
                    No products to display.
                  </td>
                </tr>
              <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td class="id-input"><?= htmlspecialchars($row['product_id']) ?></td>
                    <td class="id-input"><?= htmlspecialchars($row['created_at']) ?></td>
                    <td class="id-input"><?= htmlspecialchars($row['updated_at']) ?></td>
                    <td class="id-input"><?= htmlspecialchars($row['name']) ?></td>
                    <td class="last-product-image">
                      <img class="my-product-product-image" src="../../images/products/<?= htmlspecialchars($row['image_path']) ?>" alt="image">
                    </td>
                    <td class="id-input">PHP <?= number_format($row['price'], 2) ?></td>
                    <td class="id-input"><?= htmlspecialchars($row['stock_quantity']) ?></td>
                    <td class="id-input">
                      <button type="button" class="edit-button" data-product-id="<?= htmlspecialchars($row['product_id']) ?>">Edit</button>
                      <button type="button" class="remove-button" data-product-id="<?= htmlspecialchars($row['product_id']) ?>">Remove</button>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php endif; ?>

            </tbody>
          </table>
        </div>
      </div>

      <div class="add-product">
        <div class="title">
          <p>Add Product</p>
        </div>

        <div class="add-product-content">
          <form id="addProductForm" enctype="multipart/form-data" method="post" action="../../user-handling/sellers/add-product.php">
            <input type="hidden" name="seller_id" value="<?= htmlspecialchars($user['user_id']) ?>">
            <div class="inputs-container">
              <div class="input-box">
                <label class="add-product-label" for="productName">Product Name:</label>
                <input type="text" id="productName" name="productName" required>
              </div>

              <div class="input-box">
                <label class="add-product-label" for="description">Product Description:</label>
                <textarea id="description" name="description" class="add-product-input" rows="3" required></textarea>
              </div>

              <div class="input-box">
                <label class="add-product-label" for="NumberStock">Number of Stock:</label>
                <input type="text" id="NumberStock" name="NumberStock" required>
              </div>

              <div class="input-box">
                <label class="add-product-label" for="price">Price:</label>
                <input type="text" id="price" name="price" required>
              </div>

              <div class="select-container">
                <img class="arrow-down" src="../../images/arrow-down-338-svgrepo-com.svg">
                <img class="arrow-up" src="../../images/arrow-up-338-svgrepo-com.svg">
                <label class="add-product-label" for="category">Gender:</label>
                <select id="gender" name="gender">
                  <option value="" selected disabled hidden></option>
                  <option value="Men">Men</option>
                  <option value="Women">Women</option>
                  <option value="Unisex">Unisex</option>
                </select>
              </div>

              <div class="select-container">
                <img class="arrow-down" src="../../images/arrow-down-338-svgrepo-com.svg">
                <img class="arrow-up" src="../../images/arrow-up-338-svgrepo-com.svg">
                <label class="add-product-label" for="category">Category:</label>
                <select id="category" name="category">
                  <option value="" selected disabled hidden></option>
                  <option value="Clothes">Clothes</option>
                  <option value="Accessories">Accessories</option>
                </select>
              </div>

              <div class="select-container" id="clothesType-container">
                <img class="arrow-down" src="../../images/arrow-down-338-svgrepo-com.svg">
                <img class="arrow-up" src="../../images/arrow-up-338-svgrepo-com.svg">
                <label class="add-product-label" for="clothesType">Type:</label>
                <select id="clothesType" name="clothesType">
                  <option value="" selected disabled hidden></option>
                  <option value="T-shirt">T-shirt</option>
                  <option value="Polo Shirt">Polo Shirt</option>
                  <option value="Polo">Polo</option>
                  <option value="Long Sleeve">Long Sleeve</option>
                  <option value="Jacket">Jacket</option>
                  <option value="Hoodie">Hoodie</option>
                  <option value="Pants">Pants</option>
                  <option value="Short">Short</option>
                  <option value="Underwear">Underwear</option>
                  <option value="Sock">Sock</option>
                </select>
              </div>

              <div class="select-container" id="accessoriesType-container">
                <img class="arrow-down" src="../../images/arrow-down-338-svgrepo-com.svg">
                <img class="arrow-up" src="../../images/arrow-up-338-svgrepo-com.svg">
                <label class="add-product-label" for="accessoriesType">Type:</label>
                <select id="accessoriesType" name="accessoriesType">
                  <option value="" selected disabled hidden></option>
                  <option value="Watch">Watch</option>
                  <option value="Glasses">Glasses</option>
                  <option value="Earring">Earring</option>
                  <option value="Bracelet">Bracelet</option>
                  <option value="Ring">Ring</option>
                  <option value="Hat">Hat</option>
                  <option value="Necklace">Necklace</option>
                </select>
              </div>

              <div class="select-container">
                <p class="add-product-label">Sizes Available:</p>
                <div class="size-checkboxes">
                  <label><input type="checkbox" name="size[]" value="S"> S</label>
                  <label><input type="checkbox" name="size[]" value="M"> M</label>
                  <label><input type="checkbox" name="size[]" value="L"> L</label>
                  <label><input type="checkbox" name="size[]" value="XL"> XL</label>
                  <label><input type="checkbox" name="size[]" value="2XL"> 2XL</label>
                  <label><input type="checkbox" name="size[]" value="3XL"> 3XL</label>
                  <label><input type="checkbox" name="size[]" value="4XL"> 4XL</label>
                </div>
              </div>

              <div class="input-color-box">
                <div class="color-input-wrap">
                  <label class="add-product-label" for="colorInput">Colors Available:</label>
                  <div class="color-input-container">
                    <input class="color-input" type="text" id="colorInput" placeholder="Enter a color (#HEXCODE)">
                    <button type="button" class="add-color-button" type="button" onclick="addColor()">Add</button>
                  </div>
                </div>

                <div class="color-list" id="colorList"></div>
              </div>

              <div class="input-image-box">
                <div class="input-image-title">
                  <p>Upload Product Image:</p>
                </div>
                <div class="input-upload-box">
                  <div class="upload-box" id="dropZone">
                    <div class="upload-text" id="uploadText">
                      Click to choose or<br>drag a file<br>
                      <small>(max 5mb file size)</small>
                    </div>
                    <input type="file" id="fileInput" name="fileInput" accept=".jpg, .jpeg, .png" required>
                    <img id="previewImage" class="preview-img hidden" alt="Image Preview">
                  </div>

                  <button type="button" id="removeButton" class="remove-btn hidden" type="button">Remove</button>
                </div>
              </div>

            </div>

            <div class="add-btn-container">
              <input class="add-product-add-btn" type="submit" value="Add Product">
            </div>
          </form>
        </div>
      </div>

      <div class="orders">
        <div class="title">
          <p>Orders</p>
        </div>

        <div class="orders-content">
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Order ID</th>
                <th>Buyer</th>
                <th>Order Status</th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <td class="orders-product">Fleece Full-Zip Long Sleeve Jacket</td>
                <td class="orders-order-id">0123</td>
                <td class="orders-buyer">User123</td>
                <td class="id-input">----</td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

      <div class="my-orders">
        <div class="title">
          <p>My Orders</p>
        </div>

        <div class="my-orders-content">
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <td class="my-orders-product">Fleece Full-Zip Long Sleeve Jacket</td>
                <td class="my-orders-price">PHP 1000.00</td>
                <td class="my-orders-quantity">5</td>
                <td class="my-orders-total">PHP 5000.00</td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

      <div class="earnings">
        <div class="title">
          <p>Earnings</p>
        </div>

        <div class="earnings-content">
          <table>
            <thead>
              <tr>
                <th>Total Earnings</th>
                <th>Pending Payments</th>
                <th>Total Orders</th>
                <th>Stocks Sold</th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <td class="my-earnings-total-earnings">PHP 120000.00</td>
                <td class="my-earnings-pending-payments">10</td>
                <td class="my-earnings-total-orders">100</td>
                <td class="mt-earnings-stocks-sold">80</td>
              </tr>

            </tbody>
          </table>
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
    // MENU-ACTION
    const viewMap = {
      'my-account-btn': ['my-account'],
      'my-products-btn': ['my-products'],
      'add-product-btn': ['add-product'],
      'orders-btn': ['orders', 'my-orders'],
      'earnings-btn': ['earnings']
    };

    const buttons = document.querySelectorAll('.my-account-btn, .my-products-btn, .add-product-btn, .orders-btn, .earnings-btn');

    let activeSection = null;

    buttons.forEach(button => {
      button.addEventListener('click', () => {
        const btnClass = button.classList[0];
        const sectionClasses = viewMap[btnClass];
        const isActive = sectionClasses.some(cls => {
          return document.querySelector(`.${cls}`).style.display === 'flex';
        });

        // Hide all sections
        Object.values(viewMap).flat().forEach(cls => {
          document.querySelector(`.${cls}`).style.display = 'none';
        });

        // Reset all button colors
        buttons.forEach(btn => btn.style.backgroundColor = '');

        if (!isActive) {
          // Show this section(s)
          sectionClasses.forEach(cls => {
            document.querySelector(`.${cls}`).style.display = 'flex';
          });

          // Highlight this button
          button.style.backgroundColor = '#73A1B1';
        }
      });
    });


    document.addEventListener("DOMContentLoaded", function() {
      // CATEGORY TYPE SHOW/HIDE
      const categorySelect = document.getElementById("category");
      const clothesTypeContainer = document.getElementById("clothesType-container");
      const accessoriesTypeContainer = document.getElementById("accessoriesType-container");

      // Hide initially
      clothesTypeContainer.style.display = "none";
      accessoriesTypeContainer.style.display = "none";

      categorySelect.addEventListener("change", function() {
        const selected = this.value;

        if (selected === "Clothes") {
          clothesTypeContainer.style.display = "flex";
          accessoriesTypeContainer.style.display = "none";
        } else if (selected === "Accessories") {
          clothesTypeContainer.style.display = "none";
          accessoriesTypeContainer.style.display = "flex";
        } else {
          clothesTypeContainer.style.display = "none";
          accessoriesTypeContainer.style.display = "none";
        }
      });

      // CUSTOM DROPDOWN ARROWS
      document.querySelectorAll('.select-container').forEach(container => {
        const select = container.querySelector('select');
        const arrowDown = container.querySelector('.arrow-down');
        const arrowUp = container.querySelector('.arrow-up');
        let isOpen = false;

        select.addEventListener('click', () => {
          isOpen = !isOpen;
          arrowDown.style.display = isOpen ? 'none' : 'inline';
          arrowUp.style.display = isOpen ? 'inline' : 'none';
        });

        select.addEventListener('blur', () => {
          isOpen = false;
          arrowDown.style.display = 'inline';
          arrowUp.style.display = 'none';
        });

        arrowUp.addEventListener('click', (e) => {
          e.preventDefault();
          isOpen = false;
          arrowDown.style.display = 'inline';
          arrowUp.style.display = 'none';
          select.blur();
        });
      });
    });

    // ADD COLOR
    function addColor() {
      const input = document.getElementById('colorInput');
      const color = input.value.trim();
      const list = document.getElementById('colorList');

      if (color !== '') {
        // Create hidden input for form submission
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'colors[]';
        hidden.value = color;

        // Create label for visual with background color
        const label = document.createElement('span');
        label.textContent = color;
        label.style.padding = '5px 10px';
        label.style.marginRight = '5px';
        label.style.borderRadius = '5px';
        label.style.display = 'inline-block';
        label.style.marginBottom = '5px';
        label.style.color = 'white'; // default text color for visibility
        label.style.fontWeight = 'bold';
        label.style.userSelect = 'none';

        // Try setting background color, fallback to gray if invalid color
        label.style.backgroundColor = color;

        // Adjust text color for dark backgrounds
        if (!isColorLight(color)) {
          label.style.color = 'white';
        } else {
          label.style.color = 'black';
        }

        document.getElementById('addProductForm').appendChild(hidden);
        list.appendChild(label);
        input.value = '';
      }
    }

    // Helper function to check if color is light or dark
    function isColorLight(color) {
      const ctx = document.createElement('canvas').getContext('2d');
      ctx.fillStyle = color;
      const rgb = ctx.fillStyle.match(/\d+/g);
      if (!rgb) return true; // fallback: treat as light
      // Calculate luminance
      const r = parseInt(rgb[0]),
        g = parseInt(rgb[1]),
        b = parseInt(rgb[2]);
      const luminance = 0.299 * r + 0.587 * g + 0.114 * b;
      return luminance > 186; // threshold for light color
    }

    // UPLOAD IMAGE
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const previewImage = document.getElementById('previewImage');
    const uploadText = document.getElementById('uploadText');
    const removeButton = document.getElementById('removeButton');

    // Open file dialog when clicked
    dropZone.addEventListener('click', () => fileInput.click());

    // File selected
    fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

    // Dragging over
    dropZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      dropZone.classList.add('hover');
    });

    // Drag leave
    dropZone.addEventListener('dragleave', () => {
      dropZone.classList.remove('hover');
    });

    // File dropped
    dropZone.addEventListener('drop', (e) => {
      e.preventDefault();
      dropZone.classList.remove('hover');
      const file = e.dataTransfer.files[0];
      handleFile(file);
    });

    // Handle valid file
    function handleFile(file) {
      if (!file) return;

      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

      if (!allowedTypes.includes(file.type)) {
        alert('Only JPG, JPEG, and PNG files are allowed.');
        return;
      }

      if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB.');
        return;
      }

      const reader = new FileReader();
      reader.onload = function(e) {
        previewImage.src = e.target.result;
        previewImage.classList.remove('hidden');
        uploadText.classList.add('hidden');
        removeButton.classList.remove('hidden');
      };
      reader.readAsDataURL(file);
    }

    // Remove image and reset
    removeButton.addEventListener('click', () => {
      previewImage.src = '';
      previewImage.classList.add('hidden');
      uploadText.classList.remove('hidden');
      fileInput.value = '';
      removeButton.classList.add('hidden');
    });

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
    // EDIT PROFILE
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

    updateBtn.addEventListener('click', function() {
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
      // --- PLACE THE PASSWORD CHECK CODE HERE ---
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
      // --- END PASSWORD CHECK CODE ---

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
            profileFields.forEach(id => {
              originalValues[id] = document.getElementById(id).value;
              document.getElementById(id).setAttribute('readonly', true);
            });
            alert('Profile updated successfully!');
            editBtn.style.display = 'inline-block';
            updateBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
          } else {
            alert(res.message || 'Update failed.');
          }
        });
    });

    // SHOW/HIDE PASSWORD FUNCTIONALITY
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

    // PRODUCT ADDING
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
      e.preventDefault(); // Always prevent default first

      const name = document.getElementById('productName').value.trim();
      const price = document.getElementById('price').value.trim();
      const stock = document.getElementById('NumberStock').value.trim();
      const file = document.getElementById('fileInput').files[0];
      if (!name || !price || !stock || !file) {
        alert('Please fill all required fields and upload a product image.');
        return false;
      }
      if (file.size > 5 * 1024 * 1024) {
        alert('Product image must be less than 5MB.');
        return false;
      }
      // Add confirmation before submitting
      if (confirm('Are you sure you want to add this product?')) {
        this.submit(); // Only submit if confirmed
      }
    });

    // PRODUCT REMOVING
    document.querySelectorAll('.remove-button').forEach(btn => {
      btn.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        if (confirm('Are you sure you want to remove this product?')) {
          window.location.href = '../../user-handling/sellers/remove-product.php?id=' + encodeURIComponent(productId);
        }
      });
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