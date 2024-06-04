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

  <form action="/answer/<?= $question->getQuestionId(); ?>" method="post" class="question-container">
    <span class="question-text"><?= $question->getText(); ?></span>

    <div class="options-container">
      <?php
      $colorClasses = array("pink", "orange", "cyan", "purple");

      $counter = 0;
      $options = $question->getOptions();
      foreach ($options as $option):
        ?>
        <label class="option-container">
          <input type="radio" name="options[]" value="<?= $option->getOptionId(); ?>" />
          <span class="checkmark <?= $colorClasses[$counter % count($colorClasses)]; ?>"></span>
          <span class="option-text"><?= $option->getText(); ?></span>
        </label>
        <?php
        $counter++;
      endforeach;
      ?>`
    </div>

    <button class="main-button">Continue</button>
  </form>

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