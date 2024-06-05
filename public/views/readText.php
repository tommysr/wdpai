<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/public/css/style.css" type="text/css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lekton:ital,wght@0,400;0,700;1,400&display=swap"
    rel="stylesheet" />

  <script type="text/javascript" src="/public/js/confirmationModal.js" defer></script>
  <title><?= $title; ?></title>
</head>

<body>
  <nav class="questNav">
    <div class="backBar">
      <a href="" id="back-link"><img src="/public/assets/back_arrow.svg" /></a>
      <span class="back-text">Choose answers</span>
    </div>
  </nav>

  <!-- <div class="follow-bar absolute-follow">
    <span class="text-bold-sm">Follow us</span>
    <img src="/public/assets/follow-bar/line.svg" />
    <img src="/public/assets/follow-bar/fb.svg" />
    <img src="/public/assets/follow-bar/ig.svg" />
    <img src="/public/assets/follow-bar/linkedin.svg" />
    <img src="/public/assets/follow-bar/arrow_left.svg" />
  </div> -->

  <div class="flex-column-center-center">
    <form action="/answer/<?= $question->getQuestionId(); ?>" method="post" class="question-container">
      <div class="text-background">
        <span class="read-text">
          <?= $question->getText(); ?>
        </span>
      </div>

      <button class="main-button">Continue</button>
    </form>
  </div>

  <dialog id="confirmationDialog">
    <form method="dialog">
      <h4>Confirm</h4>
      <p>Do you really want to abandon the quest?</p>
      <menu>
        <button id="confirm-yes" value="yes">Yes</button>
        <button id="confirm-no" value="no">No</button>
      </menu>
    </form>
    <span class="error-message"></span>
  </dialog>
</body>

</html>