<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="public/css/style.css" type="text/css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap"
      rel="stylesheet"
    />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Lekton:ital,wght@0,400;0,700;1,400&display=swap"
      rel="stylesheet"
    />

    <script type="text/javascript" src="public/js/register.js" defer></script>

    <title><?= $title; ?></title>
  </head>

  <body>
    <main class="full-container flex-row-evenly-center">
      <div class="follow-bar absolute-follow">
        <span class="text-bold-sm">Follow us</span>
        <img src="public/assets/follow-bar/line.svg" />
        <img src="public/assets/follow-bar/fb.svg" />
        <img src="public/assets/follow-bar/ig.svg" />
        <img src="public/assets/follow-bar/linkedin.svg" />
        <img src="public/assets/follow-bar/arrow_left.svg" />
      </div>

      <div class="logo">
        <img src="public/assets/logo_with_name.svg" />
      </div>

      <form
        action="register"
        method="post"
        class="flex-column-center-center gap-1-5"
      >
        <div class="flex-column-center-center">
          <input
            class="login-input"
            name="email"
            type="email"
            placeholder="email address"
            required
          />
          <span id="email-error" class="error-message"></span>
        </div>

        <div class="flex-column-center-center">
          <input
            class="login-input"
            name="username"
            type="text"
            placeholder="username"
            required
          />
          <span id="username-error" class="error-message"></span>
        </div>

        <div class="flex-column-center-center">
          <input
            class="login-input"
            name="password"
            type="password"
            placeholder="password"
            required
            minlength="8"
          />
          <span id="password-error" class="error-message"></span>
        </div>

        <div class="flex-column-center-center">
          <input
            class="login-input"
            name="confirmedPassword"
            type="password"
            placeholder="confirm password"
            required
          />
          <span id="confirm-error" class="error-message"></span>
        </div>

        <div class="flex-column-center-center">
          <div>
            <input type="checkbox" id="tos-checkbox" required />
            <span class="tos-text">I accepted <a href="#">TOS</a>.</span>
          </div>
          <span id="tos-error" class="error-message"></span>
        </div>

        <button class="main-button" type="submit">Sign up</button>

        <span class="error-message"><?= $message; ?></span>
      </form>
    </main>
  </body>
</html>