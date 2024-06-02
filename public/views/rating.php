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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <title><?= $title; ?></title>
</head>

<body class="flex-column-center-center">
    <div class="backBar">
        <a href="#" class=""><i class="fas fa-arrow-left"></i></a>
        <span class="back-text">Rate us</span>
    </div>

    <div class="follow-bar absolute-follow">
        <span class="text-bold-sm">Follow us</span>
        <img src="/public/assets/follow-bar/line.svg" />
        <img src="/public/assets/follow-bar/fb.svg" />
        <img src="/public/assets/follow-bar/ig.svg" />
        <img src="/public/assets/follow-bar/linkedin.svg" />
        <img src="/public/assets/follow-bar/arrow_left.svg" />
    </div>

    <form action="/rating" method="post" class="flex-column-center-center">
        <span class="back-text">How would you rate your experience?</span>

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
</body>

</html>