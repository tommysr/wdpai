<main class="flex-column-center-center">
    <?php if (sizeof($quests) == 0): ?>
        <h1 class="main-text">No records exist</h1>
    <?php endif; ?>

    <div class="container">
        <div class="cards">
            <?php foreach ($quests as $quest): ?>
                <div class="card">
                    <div class="container-card bg-green-box">
                        <div class="card-top">
                            <img class="image-green-box card-image"
                                src="<?= $quest->getPictureUrl() == 'none' ? "https://picsum.photos/300/200" : "/public/uploads/" . $quest->getPictureUrl(); ?>"
                                alt="image" />
                            <div class="infos">
                                <span class="info">
                                    <i class="fas fa-star"></i>
                                    <?= $quest->getAvgRating(); ?>
                                </span>

                                <span class="info">
                                    <i class="fas fa-wallet"></i>
                                    <?= $quest->getBlockchain(); ?>
                                </span>

                                <span class="info">
                                    <i class="fas fa-flag-checkered"></i>
                                    <?= $quest->getRequiredMinutes(); ?>
                                </span>
                            </div>
                        </div>
                        <span class="title"><?= $quest->getTitle(); ?></span>
                        <p class="description"><?= $quest->getDescription(); ?></p>
                        <button class="show-more-btn">Show more</button>


                        <div class="infos">
                            <span class="info">
                                <i class="fas fa-clock"></i>
                                <?= $quest->getExpiryDateString(); ?>
                            </span>

                            <span class="info">
                                <i class="fas fa-running"></i>
                                <?= $quest->getParticipantsCount(); ?> /
                                <?= $quest->getParticipantsLimit(); ?>
                            </span>
                            <span class="info">
                                <i class="fas fa-coins"></i>
                                <?= $quest->getPoolAmount(); ?>
                            </span>
                        </div>

                        <div class="flex-column-center-center gap-0-5">
                            <a href="/showEditQuest/<?= $quest->getQuestId(); ?>" class="enter-button">Show</a>
                            <button id="publishButton" class="enter-button"
                                onclick="togglePublish(this, <?= $quest->getQuestId(); ?>)">
                                <?php if (!$quest->getIsApproved()): ?>
                                    Publish
                                <?php else: ?>
                                    Unpublish
                                <?php endif; ?>
                            </button>
                            <span class="error-message"></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<script src="/public/js/quests.js" defer></script>
<script src="/public/js/publishQuest.js" defer></script>