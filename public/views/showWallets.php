<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/public/css/style.css" type="text/css" />
  <link rel="stylesheet" href="/public/css/nav.css" type="text/css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lekton:ital,wght@0,400;0,700;1,400&display=swap"
    rel="stylesheet" />

  <script type="text/javascript" src="/public/js/walletSelect.js" defer></script>


  <title><?= $title; ?></title>
</head>

<body class="flex-column-center-center">
  <div class="flex-column-center-center modal">
    <p>
      To process with the quest and claim reward you need to enter wallet
      address for the following blockchain wallet
    </p>

    <span class="wallet-name"> <?= $blockchain; ?> </span>

    <form id="add-wallet-form" action="/addWallet/<?= $blockchain; ?>" method="post"
      class="flex-column-center-center gap-1-5">
      <input id="walletAddress" name="walletAddress" type="text" class="login-input" placeholder="wallet address" />

      <button class="main-button" type="submit">Add wallet</button>
    </form>

    <form action="/enterQuest/<?= $questId; ?>" method="post" class="flex-column-center-center gap-1-5"
      style="margin-top: 1em;">
      <select id="walletSelect" name="walletId" onchange="handleWalletSelect(this)" class="login-input">
        <?php foreach ($wallets as $wallet): ?>
          <option value="<?= $wallet->getWalletId(); ?>">
            <?= $wallet->getWalletAddress(); ?>
          </option>
        <?php endforeach; ?>

        <option value="new">Insert new</option>
      </select>

      <button class="main-button">Start the quest</button>

    </form>


    <span class="error-message" id="error"></span>
  </div>
</body>

</html>