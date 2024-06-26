<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="public/css/style.css" type="text/css" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lekton:ital,wght@0,400;0,700;1,400&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

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

    <form class="flex-column-center-center gap-0-5" id="login-form">
      <input class="login-input" name="email" type="email" placeholder="email address" required />
      <span id="email-error" class="error-message"></span>
      <input class="login-input" name="password" type="password" placeholder="password" required minlength="8" />
      <span id="password-error" class="error-message"></span>
      <span id="form-error" class="error-message"></span>
      <input type="hidden" name="login_method" value="db" />
      <button class="main-button">Sign in</button>

      <a href="/register" class="secondary-button"
        style="text-decoration: none; text-align: center; align-items: center; display: flex; justify-content: center;">
        Create Account
      </a>
    </form>

  </main>
</body>

</html>