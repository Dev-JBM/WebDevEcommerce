<?php
session_start();
require_once '../../features/db-connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: /homepage.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "No product selected.";
    exit;
}

$product_id = intval($_GET['id']);
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$seller_id = $user['user_id'];

$imagePath = (!empty($user['image']))
    ? '../../images/profiles/' . $user['image']
    : '../../images/profile-circle-svgrepo-com.png';

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ? AND seller_id = ?");
$stmt->bind_param("ii", $product_id, $seller_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) {
    echo "Product not found or you do not have permission to edit this product.";
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

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="/style/settings.css">
    <link rel="stylesheet" href="/style/profilePic.css">
    <link rel="stylesheet" href="/style/logout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Mynerve&family=Mandali&family=Aoboshi+One&family=Inter:ital,wght@0,100..900;1,100..900&family=MuseoModerno:ital,wght@0,100..900;1,100..900&family=Podkova:wght@400..800&display=swap" rel="stylesheet">
</head>
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
                <p>Edit Product</p>
                <a href="/user-handling/sellers/seller_settings.php" style="margin-left:auto; margin-right: 40px; color: #FF7F7F; font-size: 0.5em;">
                    < Back to Settings
                </a>
            </div>
            <div class="edit-product-content">
                <form id="editProductForm" enctype="multipart/form-data" method="post" action="update-product.php">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                    <div class="inputs-container">
                        <div class="input-box">
                            <label class="add-product-label" for="productName">Product Name:</label>
                            <input type="text" id="productName" name="productName" value="<?= htmlspecialchars($product['name']) ?>" readonly>
                        </div>

                        <div class="input-box">
                            <label class="add-product-label" for="description">Product Description:</label>
                            <textarea id="description" name="description" class="add-product-input" rows="3" readonly><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>

                        <div class="input-box">
                            <label class="add-product-label" for="NumberStock">Number of Stock:</label>
                            <input type="text" id="NumberStock" name="NumberStock" value="<?= htmlspecialchars($product['stock_quantity']) ?>" readonly>
                        </div>

                        <div class="input-box">
                            <label class="add-product-label" for="price">Price:</label>
                            <input type="text" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" readonly>
                        </div>

                        <div class="select-container">
                            <img class="arrow-down" src="/images/arrow-down-338-svgrepo-com.svg">
                            <img class="arrow-up" src="/images/arrow-up-338-svgrepo-com.svg">
                            <label class="add-product-label" for="gender">Gender:</label>
                            <select id="gender" name="gender" disabled>
                                <option value="" disabled hidden></option>
                                <option value="Men" <?= $product['gender'] == 'Men' ? 'selected' : '' ?>>Men</option>
                                <option value="Women" <?= $product['gender'] == 'Women' ? 'selected' : '' ?>>Women</option>
                                <option value="Unisex" <?= $product['gender'] == 'Unisex' ? 'selected' : '' ?>>Unisex</option>
                            </select>
                        </div>

                        <div class="select-container">
                            <img class="arrow-down" src="/images/arrow-down-338-svgrepo-com.svg">
                            <img class="arrow-up" src="/images/arrow-up-338-svgrepo-com.svg">
                            <label class="add-product-label" for="category">Category:</label>
                            <select id="category" name="category" disabled>
                                <option value="" disabled hidden></option>
                                <option value="Clothes" <?= $product['category'] == 'Clothes' ? 'selected' : '' ?>>Clothes</option>
                                <option value="Accessories" <?= $product['category'] == 'Accessories' ? 'selected' : '' ?>>Accessories</option>
                            </select>
                        </div>

                        <div class="select-container" id="clothesType-container" style="display:<?= $product['category'] == 'Clothes' ? 'flex' : 'none' ?>">
                            <img class="arrow-down" src="/images/arrow-down-338-svgrepo-com.svg">
                            <img class="arrow-up" src="/images/arrow-up-338-svgrepo-com.svg">
                            <label class="add-product-label" for="clothesType">Type:</label>
                            <select id="clothesType" name="clothesType" disabled>
                                <option value="" disabled hidden></option>
                                <?php
                                $clothesTypes = ["T-shirt", "Polo Shirt", "Polo", "Long Sleeve", "Jacket", "Hoodie", "Pants", "Short", "Underwear", "Sock"];
                                foreach ($clothesTypes as $type) {
                                    echo '<option value="' . $type . '"' . ($product['type'] == $type ? ' selected' : '') . '>' . $type . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="select-container" id="accessoriesType-container" style="display:<?= $product['category'] == 'Accessories' ? 'flex' : 'none' ?>">
                            <img class="arrow-down" src="/images/arrow-down-338-svgrepo-com.svg">
                            <img class="arrow-up" src="/images/arrow-up-338-svgrepo-com.svg">
                            <label class="add-product-label" for="accessoriesType">Type:</label>
                            <select id="accessoriesType" name="accessoriesType" disabled>
                                <option value="" disabled hidden></option>
                                <?php
                                $accessoriesTypes = ["Watch", "Glasses", "Earring", "Bracelet", "Ring", "Hat", "Necklace"];
                                foreach ($accessoriesTypes as $type) {
                                    echo '<option value="' . $type . '"' . ($product['type'] == $type ? ' selected' : '') . '>' . $type . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="input-box">
                            <label class="add-product-label" for="edit_sizeCheckboxes">Sizes Available:</label>
                            <div class="size-checkboxes" id="edit_sizeCheckboxes">
                                <?php
                                $sizes = array_map('trim', explode(',', $product['sizes_available']));
                                foreach (["S", "M", "L", "XL", "2XL", "3XL", "4XL"] as $size) {
                                    echo '<label><input type="checkbox" name="size[]" value="' . $size . '" ' . (in_array($size, $sizes) ? 'checked' : '') . ' disabled> ' . $size . '</label>';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="input-color-box">
                            <div class="color-input-wrap">
                                <label class="add-product-label" for="edit_colorInput">Colors Available:</label>
                                <div class="color-input-container">
                                    <input class="color-input" type="text" id="edit_colorInput" placeholder="Enter a color (#HEXCODE)" disabled>
                                    <button type="button" class="add-color-button" id="edit_addColorBtn" onclick="addEditColor()" disabled>Add</button>
                                </div>
                            </div>
                            <div class="color-list" id="edit_colorList">
                                <?php
                                $colors = array_map('trim', explode(',', $product['colors_available']));
                                foreach ($colors as $color) {
                                    echo '<span class="color-badge" style="background:' . htmlspecialchars($color) . ';padding:5px 10px;border-radius:5px;margin-right:5px;">' . htmlspecialchars($color) . '</span>';
                                }
                                ?>
                            </div>
                            <input type="hidden" name="colors" id="edit_colors" value="<?= htmlspecialchars($product['colors_available']) ?>">
                        </div>

                        <div class="input-image-box">
                            <div class="input-image-title">
                                <p>Upload Product Image:</p>
                            </div>
                            <div class="input-upload-box">
                                <div class="upload-box" id="edit_dropZone">
                                    <div class="upload-text" id="edit_uploadText">
                                        Click to choose or<br>drag a file<br>
                                        <small>(max 5mb file size)</small>
                                    </div>
                                    <input type="file" id="edit_fileInput" name="fileInput" accept=".jpg, .jpeg, .png" style="display:none;" disabled>
                                    <img id="edit_previewImage" class="preview-img hidden" alt="Image Preview">
                                </div>
                                <button type="button" id="edit_removeButton" class="remove-btn hidden" type="button">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="edit-btn-container">
                        <button id="editProductBtn" class="profile-edit-btn" type="button">Edit</button>
                        <button id="updateProductBtn" class="profile-edit-btn" type="submit" style="display:none; background-color: red;">Update</button>
                        <button id="cancelProductBtn" class="profile-edit-btn" type="button" style="display:none;">Cancel</button>
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

        // JS to handle edit/update/cancel just like profile
        const productFields = [
            'productName',
            'description',
            'NumberStock',
            'price',
            'gender',
            'category',
            'clothesType',
            'accessoriesType'
        ];

        const originalValues = {};
        productFields.forEach(id => {
            const el = document.getElementById(id);
            if (el) originalValues[id] = el.value;
        });
        // Save original checked state of size checkboxes
        originalValues['sizes'] = Array.from(document.querySelectorAll('#edit_sizeCheckboxes input[type="checkbox"]')).map(cb => cb.checked);
        // Save original colors
        originalValues['colors'] = document.getElementById('edit_colors').value;

        const editBtn = document.getElementById('editProductBtn');
        const updateBtn = document.getElementById('updateProductBtn');
        const cancelBtn = document.getElementById('cancelProductBtn');

        editBtn.addEventListener('click', function() {
            // Enable size checkboxes
            document.querySelectorAll('#edit_sizeCheckboxes input[type="checkbox"]').forEach(cb => cb.removeAttribute('disabled'));

            productFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.removeAttribute('readonly');
                if (el && (el.tagName === 'SELECT' || el.type === 'select-one')) el.removeAttribute('disabled');
            });

            // Save original checked state of size checkboxes
            originalValues['sizes'] = Array.from(document.querySelectorAll('#edit_sizeCheckboxes input[type="checkbox"]')).map(cb => cb.checked);

            // Enable color input and button
            document.getElementById('edit_colorInput').disabled = false;
            document.getElementById('edit_addColorBtn').disabled = false;
            // Enable file input
            document.getElementById('edit_fileInput').style.display = '';
            document.getElementById('edit_fileInput').removeAttribute('disabled');
            // Show update/cancel, hide edit
            editBtn.style.display = 'none';
            updateBtn.style.display = 'inline-block';
            cancelBtn.style.display = 'inline-block';
        });

        cancelBtn.addEventListener('click', function() {
            // Disable size checkboxes
            document.querySelectorAll('#edit_sizeCheckboxes input[type="checkbox"]').forEach(cb => cb.setAttribute('disabled', true));

            productFields.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                el.value = originalValues[id];
                el.setAttribute('readonly', true);
                if (el.tagName === 'SELECT' || el.type === 'select-one') el.setAttribute('disabled', true);
            });
            document.querySelectorAll('#edit_sizeCheckboxes input[type="checkbox"]').forEach((cb, i) => cb.checked = originalValues['sizes'][i]);

            // Restore original color badges
            const colorList = document.getElementById('edit_colorList');
            colorList.innerHTML = '';
            const originalColors = (originalValues['colors'] || document.getElementById('edit_colors').defaultValue || '').split(',').map(c => c.trim()).filter(Boolean);
            originalColors.forEach(color => {
                const span = document.createElement('span');
                span.className = 'color-badge';
                span.style.background = color;
                span.style.color = getContrastYIQ(color);
                span.style.padding = '5px 10px';
                span.style.borderRadius = '5px';
                span.style.marginRight = '5px';
                span.textContent = color;
                colorList.appendChild(span);
            });
            enableColorBadgeRemoval();
            updateEditColorsInput();

            document.getElementById('edit_colorInput').disabled = true;
            document.getElementById('edit_addColorBtn').disabled = true;

            document.getElementById('edit_fileInput').style.display = 'none';
            document.getElementById('edit_fileInput').setAttribute('disabled', true);

            editBtn.style.display = 'inline-block';
            updateBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
        });
        updateBtn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to update this product?')) {
                e.preventDefault();
            }
        });

        // Decide text color based on bg color (if light or dark)
        function getContrastYIQ(color) {
            // Try to convert named color to hex using a dummy element
            if (!color.startsWith('#')) {
                let temp = document.createElement('div');
                temp.style.color = color;
                document.body.appendChild(temp);
                let rgb = window.getComputedStyle(temp).color;
                document.body.removeChild(temp);
                // rgb format: rgb(0, 0, 0)
                let result = rgb.match(/\d+/g);
                if (result && result.length === 3) {
                    let hex = '#' + result.map(x => ('0' + parseInt(x).toString(16)).slice(-2)).join('');
                    color = hex;
                } else {
                    return '#fff'; // fallback
                }
            }
            color = color.replace('#', '');
            if (color.length === 3) {
                color = color.split('').map(x => x + x).join('');
            }
            if (!/^[0-9A-Fa-f]{6}$/.test(color)) return '#fff';
            var r = parseInt(color.substr(0, 2), 16);
            var g = parseInt(color.substr(2, 2), 16);
            var b = parseInt(color.substr(4, 2), 16);
            var yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
            return (yiq >= 128) ? '#000' : '#fff';
        }

        // Enable badge removal in edit mode
        function enableColorBadgeRemoval() {
            const input = document.getElementById('edit_colorInput');
            document.querySelectorAll('#edit_colorList .color-badge').forEach(span => {
                span.onclick = function() {
                    if (!input.disabled) {
                        span.remove();
                        updateEditColorsInput();
                    }
                };
            });
        }

        function addEditColor() {
            const input = document.getElementById('edit_colorInput');
            const color = input.value.trim();
            if (!color) return;
            const colorList = document.getElementById('edit_colorList');
            // Prevent duplicates
            if ([...colorList.children].some(span => span.textContent === color)) return;
            const span = document.createElement('span');
            span.className = 'color-badge';
            span.style.background = color;
            span.style.color = getContrastYIQ(color);
            span.style.padding = '5px 10px';
            span.style.borderRadius = '5px';
            span.style.marginRight = '5px';
            span.textContent = color;

            span.onclick = function() {
                if (!input.disabled) {
                    colorList.removeChild(span);
                    updateEditColorsInput();
                }
            };
            colorList.appendChild(span);
            input.value = '';
            updateEditColorsInput();
        }

        function updateEditColorsInput() {
            const colorList = document.getElementById('edit_colorList');
            const colors = [...colorList.children].map(span => span.textContent);
            document.getElementById('edit_colors').value = colors.join(',');
        }

        // Drag-and-drop and click-to-upload for edit-product image
        const editDropZone = document.getElementById('edit_dropZone');
        const editFileInput = document.getElementById('edit_fileInput');
        const editPreviewImage = document.getElementById('edit_previewImage');
        const editUploadText = document.getElementById('edit_uploadText');
        const editRemoveButton = document.getElementById('edit_removeButton');

        editDropZone.addEventListener('click', function() {
            if (!editFileInput.disabled) editFileInput.click();
        });

        editDropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (!editFileInput.disabled) editDropZone.classList.add('dragover');
        });
        editDropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            editDropZone.classList.remove('dragover');
        });

        editDropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            editDropZone.classList.remove('dragover');
            if (!editFileInput.disabled && e.dataTransfer.files.length > 0) {
                editFileInput.files = e.dataTransfer.files;
                showEditPreview();
            }
        });

        editFileInput.addEventListener('change', showEditPreview);

        function showEditPreview() {
            const file = editFileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    editPreviewImage.src = e.target.result;
                    editPreviewImage.classList.remove('hidden');
                    editUploadText.style.display = 'none';
                    editRemoveButton.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        editRemoveButton.addEventListener('click', function(e) {
            e.stopPropagation();
            editFileInput.value = '';
            editPreviewImage.src = '';
            editPreviewImage.classList.add('hidden');
            editUploadText.style.display = '';
            editRemoveButton.classList.add('hidden');
        });

        window.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('#edit_colorList .color-badge').forEach(span => {
                // Use the badge's text content as the color value
                let color = span.textContent.trim();
                span.style.color = getContrastYIQ(color);
            });
            enableColorBadgeRemoval();
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


    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <script>
            alert('Product updated successfully!');
        </script>
    <?php endif; ?>
</body>

</html>