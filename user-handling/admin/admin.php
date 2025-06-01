<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wear Dyans</title>
  <link rel="stylesheet" href="../../style/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Mynerve&family=Mandali&family=Aoboshi+One&family=Inter:ital,wght@0,100..900;1,100..900&family=MuseoModerno:ital,wght@0,100..900;1,100..900&family=Podkova:wght@400..800&display=swap" rel="stylesheet">
</head>

<body>
  <main>
    <header class="header">
      <div class="left-header">
        <a href="../../homepage.html">Wear Dyans</a>
      </div>

      <div class="right-header">
        <img class="profile" src="../../images/profile-circle-svgrepo-com.png" onclick="toggleMenu()">
        <div class="sub-menu-wrap" id="subMenu">
          <div class="sub-menu">
            <div class="user-info">
              <img src="../../images/profile-circle-svgrepo-com.png" alt="profile">
              <h3 id="userName">User123</h3>
            </div>
            <hr>

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
      <div class="section-wrap">
        <div class="top-container">
          <h1 class="dasboard-header">Dashboard Overview</h1>
          <div class="search-container">
            <input class="search-bar" type="text" placeholder="search">
            <img class="search-icon" src="../../images/search-svgrepo-com.png">
          </div>
        </div>

        <div class="middle-container">
          <div class="middle-container-box" id="sellerBox">
            <div class="box-header">
              <h2 class="total-header">Total Sellers</h2>
              <img src="../../images/image 56.png">
            </div>
            <h3 class="total-number">1,234</h3>
            <p class="bottom-text">Registered sellers on the platform</p>
          </div>

          <div class="middle-container-box" id="buyerBox">
            <div class="box-header">
              <h2 class="total-header">Total Buyers</h2>
              <img src="../../images/image 55.png">
            </div>
            <h3 class="total-number">12,345</h3>
            <p class="bottom-text">Active buyers on the platform</p>
          </div>
        </div>

        <div class="bottom-container">
          <h2 class="bottom-header">Recent Activity: Unknown</h2>

          <table class="users-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>User</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Joined</th>
                <th></th>
              </tr>
            </thead>

            <tbody>
              <tr>
                <td>123</td>
                <td>Buyer</td>
                <td>Dyans</td>
                <td>dyans@gmail.com</td>
                <td>26-05-2024</td>
                <td>
                  <button type="button" class="remove-button">Remove</button>
                </td>
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
  </script>
</body>

</html>