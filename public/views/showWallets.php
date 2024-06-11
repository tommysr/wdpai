<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/public/css/style.css" type="text/css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lekton:ital,wght@0,400;0,700;1,400&display=swap"
    rel="stylesheet" />

  <script type="text/javascript" src="/public/js/walletSelect.js" defer></script>
  <title><?= $title; ?></title>
</head>

<body class="flex-row-center-center">
  <dialog id="confirmationDialog" open>
    <div class="flex-column-center-center gap-0-5">
      <p class="main-text input-description">
        To process with the quest and claim reward you need to enter wallet
        address for the following blockchain wallet
      </p>

      <h1 class="wallet-name"> <?= $chain; ?> </h1>

      <div class="flex-column-center-center gap-0-5 enter-form">
        <form onsubmit="enterQuest(event, <?= $questId ?>)" class="flex-column-center-center m-t-1 w-100">
          <select id="walletSelect" name="walletId" onchange="handleWalletSelect(this)" class="login-input">
            <?php foreach ($wallets as $wallet): ?>
              <option value="<?= $wallet->getWalletId(); ?>">
                <?= $wallet->getWalletAddress(); ?>
              </option>
            <?php endforeach; ?>

            <option value="new">Insert new</option>
          </select>

          <button class="enter-button">Enter</button>
        </form>


        <form id="add-wallet-form" onsubmit="addWallet(event, '<?= $chain; ?>')" class="flex-column-center-center w-100">
          <input id="walletAddress" name="walletAddress" type="text" class="login-input" placeholder="wallet address" />
          <button class="enter-button" type="submit">Add </button>
        </form>
      </div>
      <span class="error-message" id="error"></span>
    </div>
  </dialog>
</body>

</html>