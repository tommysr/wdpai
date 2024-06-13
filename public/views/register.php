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

  <script type="text/javascript" src="public/js/register.js" defer></script>

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
      <input class="login-input" name="username" type="text" placeholder="username" required />
      <span id="username-error" class="error-message"></span>
      <input class="login-input" name="password" type="password" placeholder="password" required minlength="8" />
      <span id="password-error" class="error-message"></span>
      <input class="login-input" name="confirmedPassword" type="password" placeholder="confirm password" required />
      <span id="confirm-error" class="error-message"></span>
   
      <div class="flex-row-center-center w-100">
        <div class="option-container w-20 tos">
          <input type="checkbox" id="tos-checkbox" required />
          <span class="checkmark"></span>
        </div>
        <span class="tos-text">I accepted <a href="#">TOS</a>.</span>
      </div>
      <input type="hidden" name="registration_method" value="db" />
      <span id="tos-error" class="error-message"></span>
      <button class="main-button" type="submit">Sign up</button>
    </form>

  </main>
</body>

</html>