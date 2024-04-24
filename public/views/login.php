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

    <script type="text/javascript" src="public/js/login.js" defer></script>

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

      <div class="flex-column-center-center gap-1-5">
        <form
          action="login"
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
              name="password"
              type="password"
              placeholder="password"
              required
              minlength="8"
            />
            <span id="password-error" class="error-message"></span>
          </div>
          <button class="main-button">Sign in</button>
        </form>

        <form action="/register">
          <button class="secondary-button">Create account</button>
        </form>

        <span class="error-message"><?= $message; ?></span>
      </div>
    </main>
  </body>
</html>
