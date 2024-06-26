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

    <title><?= $title; ?></title>
</head>

<body class="flex-column-center-center">
    <nav class="questNav">
        <div class="backBar">
            <span class="back-text">Finish</span>
        </div>
    </nav>

    <div class="question-container justify-center gap-1-5 summary">
        <div class="flex-column-center-center gap-1">
            <span class="score-text"><?php if ($score / $maxScore > 50): ?>
                    You've done great
                <?php else: ?>
                    You could do better
                <?php endif; ?>
            </span>
            <span class="score-sub-text">Overall score</span>
            <span class="score"><?= $score; ?></span>
        </div>

        <div class="progress-bar">
            <div class="progress"></div>
        </div>

        <div class="flex-column-center-center gap-1">
            <span class="score-sub-text">You're better than</span>
            <span class="score"><?= $better_than; ?> %</span>
            <span class="score-sub-text">other participants</span>
        </div>
        <form action="/completeQuest" method="post">
            <button class="main-button">Continue</button>
        </form>
    </div>

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