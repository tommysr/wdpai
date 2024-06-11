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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <script type="text/javascript" src="/public/js/confirmationModal.js" defer></script>


    <title><?= $title; ?></title>
</head>

<body class="flex-column-center-center">
    <nav class="questNav">
        <div class="backBar">
            <a href="" id="back-link"><img src="/public/assets/back_arrow.svg" /></a>
            <span class="back-text">Rating</span>
        </div>
    </nav>

    <form action="/rating/" <?= $questId; ?> method="post" class="question-container gap-4 justify-center">
        <span class="back-text">Rate the quest</span>

        <div class="rating-options">
            <?php
            $ratingValues = array(1, 2, 3, 4, 5);

            foreach ($ratingValues as $value):
                ?>
                <label class="rating-option">
                    <input type="radio" name="rating" value="<?= $value; ?>" />
                    <span class="rating-number"><?= $value; ?></span>
                </label>
                <?php
            endforeach;
            ?>
        </div>

        <button class="main-button">Submit</button>
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