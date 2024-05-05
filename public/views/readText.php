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

  <title><?= $title; ?></title>
</head>

<body class="flex-column-center-center">
  <div class="backBar">
    <a href="#" class=""><img src="/public/assets/back_arrow.svg" /></a>
    <span class="back-text">Read the text</span>
  </div>

  <div class="follow-bar absolute-follow">
    <span class="text-bold-sm">Follow us</span>
    <img src="/public/assets/follow-bar/line.svg" />
    <img src="/public/assets/follow-bar/fb.svg" />
    <img src="/public/assets/follow-bar/ig.svg" />
    <img src="/public/assets/follow-bar/linkedin.svg" />
    <img src="/public/assets/follow-bar/arrow_left.svg" />
  </div>

  <div class="flex-column-center-center">
    <form action="/nextQuestion<?= $question->getQuestionId(); ?>" method="post" class="question-container">
      <div class="text-background">
        <span class="read-text">
          <?= $question->getText(); ?>
        </span>
      </div>

      <button class="main-button">Continue</button>
    </form>
  </div>
</body>

</html>