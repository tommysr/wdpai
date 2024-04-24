<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/public/css/style.css" type="text/css" />
    <link rel="stylesheet" href="/public/css/nav.css" type="text/css" />
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

    <script type="text/javascript" src="/public/js/progress.js" defer></script>

    <title><?= $title; ?></title>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // const overallScore = <?php echo $overallScore; ?>;
        // const overallMaxScore = <?php echo $overallMaxScore; ?>;
        // const maxScoreUntilNow = <?php echo $maxScoreUntilNow; ?>;

        const overallMaxScore = 100;
        const overallScore = 2;
        const maxScoreUntilNow = 50;

        // Call the function to update the progress bar
        updateProgressBar(overallScore, overallMaxScore, maxScoreUntilNow);
      });
    </script>
  </head>
  <body class="flex-column-center-center">
    <div class="backBar">
      <a href="#" class=""><img src="/public/assets/back_arrow.svg" /></a>
      <span class="back-text">Points</span>
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
      <form action="/nextQuestion" method="post" class="question-container">
        <span class="score-text">Points gained</span>
        <span class="score"><?= $score; ?></span>
        <span class="score-text">Overall score</span>
        <span class="score"><?= $overallScore; ?></span>

        <div class="progress-bar">
          <div class="progress"></div>
          <div class="indicator"></div>
          <div class="max-progress"></div>
        </div>
        <button class="main-button">Continue</button>
      </form>
    </div>
  </body>
</html>
