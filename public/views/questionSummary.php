<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/public/css/style.css" type="text/css" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lekton:ital,wght@0,400;0,700;1,400&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

  <script type="text/javascript" src="/public/js/confirmationModal.js" defer></script>
  <title><?= $title; ?></title>
</head>

<body class="flex-column-center-center">
  <nav class="questNav">
    <div class="backBar">
      <a href="" id="back-link"><img src="/public/assets/back_arrow.svg" /></a>
      <span class="back-text">Points</span>
    </div>
  </nav>

  <div class="question-container">
    <span class="score-text">Points gained</span>
    <span class="score"><?= $questionScore; ?></span>
    <div class="flex-row-center-center">
      <?php
      $maxStars = 3;

      for ($i = 1; $i <= $maxStars; $i++) {
        if ($i <= $stars) {
          echo '<i class="fas fa-star star fa-3x fa-yellow"></i>';
        } else {
          echo '<i class="far fa-star star fa-3x fa-white"></i>';
        }
      }
      ?>
    </div>
    <span class="score-text">Overall score</span>
    <span class="score"><?= $score; ?></span>

    <div class="progress-bar">
      <div class="progress"></div>
    </div>
    <a href="/play" class="main-button">Continue</a>
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

  <script type="text/javascript" src="/public/js/progress.js" defer></script>
  <script defer>
    document.addEventListener("DOMContentLoaded", function () {
      const overallScore = <?php echo $score; ?>;
      const overallMaxScore = <?php echo $maxScore; ?>;
      updateProgressBar(overallScore, overallMaxScore);
    });
  </script>


</body>

</html>