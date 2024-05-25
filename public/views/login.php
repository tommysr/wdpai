<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="public/css/style.css" type="text/css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lekton:ital,wght@0,400;0,700;1,400&display=swap"
    rel="stylesheet" />

  <script type="text/javascript" src="public/js/login.js" defer></script>

  <title><?= $title; ?></title>
</head>

<body>
  <main class="full-container flex-row-evenly-center">
    <span class="absolute-follow">
      <?php require_once 'public/views/components/follow_bar.php'; ?>
    </span>

    <div class="logo">
      <img src="public/assets/logo_with_name.svg" />
    </div>

    <div class="flex-column-center-center gap-1-5">
      <form class="flex-column-center-center gap-1-5">
        <div class="flex-column-center-center">
          <input class="login-input" name="email" type="email" placeholder="email address" required />
          <span id="email-error" class="error-message"></span>
        </div>

        <div class="flex-column-center-center">
          <input class="login-input" name="password" type="password" placeholder="password" required minlength="8" />
          <span id="password-error" class="error-message"></span>
        </div>
        <span id="form-error" class="error-message"></span>
        <button class="main-button">Sign in</button>
      </form>

      <a href="/register" class="secondary-button"
        style="text-decoration: none; text-align: center; align-items: center; display: flex; justify-content: center;">
        Create Account
      </a>


      <span class="error-message"><?= $message; ?></span>
    </div>
  </main>
</body>

</html>