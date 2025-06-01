<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wear Dyans | Product</title>
    <link rel="stylesheet" href="style/product.css">
    <link href="https://fonts.googleapis.com/css2?family=Mynerve&family=Mandali&family=Aoboshi+One&family=Inter:ital,wght@0,100..900;1,100..900&family=MuseoModerno:ital,wght@0,100..900;1,100..900&family=Podkova:wght@400..800&display=swap" rel="stylesheet">
</head>
<body>
  <main>
    <header class="header">
      <div class="left-header">
        <a href="store.html">Wear Dyans</a>
      </div>

      <div class="search-bar">
        <input type="text" placeholder="Search">
        <img src="images/search-svgrepo-com.png">
      </div>

      <div class="right-header">
        <a href="cart.html"><img src="images/SVGRepo_iconCarrier.png"></a>
        <img class="profile" src="images/profile-circle-svgrepo-com.png" onclick="toggleMenu()">

        <div class="sub-menu-wrap" id="subMenu">
          <div class="sub-menu">
            <div class="user-info">
              <img src="./images/profile-circle-svgrepo-com.png" alt="profile">
              <h3 id="userName">User123</h3>
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
              <a class="sub-menu-text" href="settings.html">Settings</a>
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
            <img src="./images/prdouct.png">
          </div>
          <div class="product-right-container">
            <div class="product-name">
              <p>Fleece Full-Zip Long Sleeve Jacket</p>
            </div>

            <div>
              <div class="product-rating">
                <div class="rating">5.0
                  <svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                      <polygon id="star" points="10,0 12.6,6.5 20,7.5 14.5,12.5 16,20 10,16 4,20 5.5,12.5 0,7.5 7.4,6.5"/>
                    </defs>
                    <use href="#star" x="0" y="5" />
                    <use href="#star" x="25" y="5" />
                    <use href="#star" x="50" y="5" />
                    <use href="#star" x="75" y="5" />
                    <use href="#star" x="100" y="5" />
                  </svg>
                </div>
                <p class="qty-rating">
                  <span class="qty-number">| 2 </span>Ratings 
                  <span class="sales-number">| 3 </span>Sold
                </p>
              </div>
            </div>

            <div class="onsale-price">
              <p class="onsale">ON SALE !</p>
              <div class="price-container">
                <p class="new-price">PHP 1,990.00</p>
                <p class="orig-price">PHP 3,499.00</p>
                <p class="percent">-57%</p>
              </div>
            </div>



            <div class="variation-container">
              <div class="sizes-container">
                <div class="sizes">
                  <div>S</div>
                  <div>M</div>
                  <div>L</div>
                </div>
                <p class="text-sizes">Size: <span class="size-picked">Medium</span></p>
              </div>

              <div class="colors-container">
              <div class="colors">
                <div class="color-1"></div>
                <div class="color-2"></div>
                <div class="color-3"></div>
                <div class="color-4"></div>
                <div class="color-5"></div>
              </div>
              <p class="text-colors">Color: <span class="color-picked">Green</span></p>
            </div>
            </div>

              <div class="product-qty-container">
                <div class="product-qty">
                  <img src="images/minus-svgrepo-com.svg">
                  <p class="qty-text">1</p>
                  <img src="images/add-plus-svgrepo-com.svg">
                </div>
                <p class="available-text"><span>20</span> pieces available</p>
              </div>

              <div class="btns-product-right-container">
                <div>
                  <button class="btn-cart">
                    <img src="images/SVGRepo_iconCarrier_brown.png">
                    Add to Cart
                  </button>
                </div>
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
              <p>-The body is made of a fabric with a chunky feel.</p>
              <p>-Piping at the cuff and hem prevents wind from entering and keeps warmth in.</p>
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
                        <polygon id="star" points="10,0 12.6,6.5 20,7.5 14.5,12.5 16,20 10,16 4,20 5.5,12.5 0,7.5 7.4,6.5"/>
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
                            <polygon id="star" points="10,0 12.6,6.5 20,7.5 14.5,12.5 16,20 10,16 4,20 5.5,12.5 0,7.5 7.4,6.5"/>
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
  </script>
</body>
</html>