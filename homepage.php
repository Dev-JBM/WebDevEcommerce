<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wear Dyans</title>
  <link rel="stylesheet" href="style/homepage.css">
  <link href="https://fonts.googleapis.com/css2?family=Mynerve&family=Mandali&family=Aoboshi+One&family=Inter:ital,wght@0,100..900;1,100..900&family=MuseoModerno:ital,wght@0,100..900;1,100..900&family=Podkova:wght@400..800&display=swap" rel="stylesheet">
</head>

<body>
  <main>
    <header class="header">
      <div class="name">
        Wear Dyans
      </div>

      <div class="aboutus">
        <a href="aboutus.html" class="aboutus_button">About us</a> | <a href="contacts.html" class="contact_button">Contacts</a>
      </div>

      <div class="button-container">
        <button type="button" class="login">Log In</button>
        <button type="button" class="register">Sign Up</button>
      </div>
    </header>

    <section class="one-section">
      <div class="title-container">
        <p>A collection of <span>Clothes</span> and <span>Accessories</span> that will give you <span>Rizz</span></p>
      </div>

      <a href="#" class="shop-now">
        <svg height="200px" width="200px" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#ffffff">
          <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
          <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
          <g id="SVGRepo_iconCarrier">
            <style type="text/css">
              .st0 {
                fill: #ffffff;
              }
            </style>
            <g>
              <path class="st0" d="M33.394,458.311h242.247V318.635h116.323v139.676h86.642V222.61H33.394V458.311z M120.69,318.635h69.838 v69.838H120.69V318.635z"></path>
              <path class="st0" d="M310.836,368.565c-5.877,0-10.64,4.77-10.64,10.644v35.46c0,5.873,4.764,10.636,10.64,10.636 c5.874,0,10.637-4.763,10.637-10.636v-35.46C321.473,373.335,316.71,368.565,310.836,368.565z"></path>
              <polygon class="st0" points="230.104,53.689 158.593,53.689 143.977,196.421 226.22,196.421 "></polygon>
              <polygon class="st0" points="368.026,196.421 353.408,53.689 281.896,53.689 285.781,196.421 "></polygon>
              <polygon class="st0" points="512,196.421 478.606,53.689 405.207,53.689 427.591,196.421 "></polygon>
              <polygon class="st0" points="106.794,53.689 33.394,53.689 0,196.421 84.409,196.421 "></polygon>
            </g>
          </g>
        </svg>
      </a>

      <!-- LOGIN -->
      <div class="login-container">
        <div class="login-box">
          <div class="login-header">
            Log In
            <img class="x-button-login" src="./images/circle-xmark-solid.png">
          </div>

          <div class="login-content-container">
            <div class="login-content">
              <div class="login-form-container">
                <form class="login-form" action="./features/login.php" method="POST">
                  <input class="login-input" type="text" name="login_username"
                    placeholder="Phone number / Username / Email">
                  <div class="password-container">
                    <input class="password-input" type="password" name="login_password" placeholder="Password">
                    <span class="toggle-password">
                      <img class="eye-close" src="./images/eye-close-svgrepo-com.svg">
                      <img class="eye-open" src="./images/eye-2-svgrepo-com.svg">
                    </span>
                  </div>
                  <input class="login-submit" type="submit" value="Sign In">
                </form>
              </div>

              <div class="line">
                <hr>
                <p>OR</p>
                <hr>
              </div>

              <div class="facebook-google-button">
                <button type="button" class="facebook-button">
                  <img src="./images/facebook-brands.png">
                  Facebook
                </button>

                <button type="button" class="google-button">
                  <img src="./images/google-brands.png">
                  Google
                </button>
              </div>
            </div>
          </div>

          <div class="login-footer">
            <p> New Costumer? <span class="sign-up">Sign Up</span></p>
          </div>
        </div>
      </div>

      <!-- REGISTER -->
      <div class="register-container">
        <div class="register-box">
          <div class="register-header">
            Sign Up
            <img class="x-button-register" src="./images/circle-xmark-solid.png">
          </div>

          <div class="register-content-container">
            <div class="register-content">
              <div class="register-form-container">
                <form class="register-form" action="./features/register.php" method="POST">
                  <div class="register-input-container">
                    <input type="text" name="firstname" placeholder="First Name">
                    <input type="text" name="middlename" placeholder="Middle Name">
                    <input type="text" name="lastname" placeholder="Last Name">
                    <input type="text" name="register_username" placeholder="Username">
                    <input type="email" name="email" placeholder="Email">
                    <input type="date" name="birthdate" placeholder="Birthdate">
                    <input type="text" name="phone_number" placeholder="Phone Number">
                    <input type="text" name="address" placeholder="Street | Barangay | City | Province">
                    <div class="password-container">
                      <input class="password-input" type="password" name="register_password" placeholder="Password">
                      <span class="toggle-password">
                        <img class="eye-close" src="./images/eye-close-svgrepo-com.svg">
                        <img class="eye-open" src="./images/eye-2-svgrepo-com.svg">
                      </span>
                    </div>
                    <div class="password-container">
                      <input class="password-input" type="password" name="confirm_password"
                        placeholder="Confirm Password">
                      <span class="toggle-password">
                        <img class="eye-close" src="./images/eye-close-svgrepo-com.svg">
                        <img class="eye-open" src="./images/eye-2-svgrepo-com.svg">
                      </span>
                    </div>
                  </div>
                  <input class="register-submit" type="submit" value="Sign Up">
                </form>
              </div>


            </div>
          </div>

          <div class="register-footer">
            <p> Already have an Account? <span class="sign-in">Log In</span></p>
          </div>
        </div>
      </div>
    </section>

    <section class="two-section">
      <div class="two-section-box">
        <img class="product-img" src="./images/goods_469956_sub14_3x4.png" alt="">
        <div class="product-description-box">
          <div class="product-name">
            <p>Fleece Full-Zip Long Sleeve Jacket</p>
          </div>
          <div class="product-description">
            <p>The body is made of a fabric with a chunky feel.</p><br><br>
            <p>Pipping at the cuffs and hem prevents wind from entering and keeps warmth in.</p>
          </div>
        </div>
      </div>
    </section>

    <footer>
      <div class="footer-box">
        <div class="footer-icons">
          <img src="./images/facebook-svgrepo-com.svg">
          <img src="./images/instagram.png">
          <img src="./images/twitter.png">
        </div>
        <div class="footer-text">
          <p>©Dyans2025</p>
        </div>
      </div>
    </footer>
  </main>

  <script>
    const isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;

    // SHOP NOW HIDE
    const shopNow = document.querySelector('.shop-now');

    shopNow.addEventListener('click', function(e) {
      e.preventDefault();
      if (isLoggedIn) {
        window.location.href = "store.php";
      } else {
        document.querySelector(".title-container").style.display = "none";
        document.querySelector(".login-container").style.display = "flex";
        document.querySelector(".register-container").style.display = "none";
      }
    });

    const footer = document.querySelector('footer');

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          shopNow.classList.add('hidden');
        } else {
          shopNow.classList.remove('hidden');
        }
      });
    }, {
      root: null,
      threshold: 0.1
    });

    observer.observe(footer);


    // LOGIN AND REGISTER
    document.addEventListener("DOMContentLoaded", () => {
      const loginBack = document.querySelector(".x-button-login");
      const registerBack = document.querySelector(".x-button-register");
      const loginBtn = document.querySelector(".login");
      const registerBtn = document.querySelector(".register");
      const oneSectionBox = document.querySelector(".title-container");
      const loginContainer = document.querySelector(".login-container");
      const signUp = document.querySelector(".sign-up")
      const registerContainer = document.querySelector(".register-container");
      const signIn = document.querySelector(".sign-in")

      loginContainer.style.display = "none";
      registerContainer.style.display = "none";

      loginBack.addEventListener("click", () => {
        oneSectionBox.style.display = "flex";
        loginContainer.style.display = "none";
        registerContainer.style.display = "none";
      });

      registerBack.addEventListener("click", () => {
        oneSectionBox.style.display = "flex";
        loginContainer.style.display = "none";
        registerContainer.style.display = "none";
      });

      loginBtn.addEventListener("click", () => {
        oneSectionBox.style.display = "none";
        loginContainer.style.display = "flex";
        registerContainer.style.display = "none";
      });

      signUp.addEventListener("click", () => {
        oneSectionBox.style.display = "none";
        loginContainer.style.display = "none";
        registerContainer.style.display = "flex";
      });

      registerBtn.addEventListener("click", () => {
        oneSectionBox.style.display = "none";
        loginContainer.style.display = "none";
        registerContainer.style.display = "flex";
      });

      signIn.addEventListener("click", () => {
        oneSectionBox.style.display = "none";
        loginContainer.style.display = "flex";
        registerContainer.style.display = "none";
      });
    });

    // SHOW PASSWORD AND HIDE
    function setupPasswordToggle(span) {
      const container = span.parentElement;
      const input = container.querySelector('.password-input');
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
  </script>

</body>

</html>