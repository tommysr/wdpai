<main class="flex-column-center-center">
  <?php if (sizeof($quests) == 0): ?>
    <h1 class="main-text">No records exist</h1>
  <?php endif; ?>

  <a href="/createQuest" class="add-quest"><i class="fas fa-plus fa-2x"></i></a>
  <div class="container">
    <div class="cards">
      <?php foreach ($quests as $quest): ?>
        <div class="card">
          <div class="container-card bg-green-box">
            <div class="card-top">
              <img class="image-green-box card-image" src="https://picsum.photos/300/200" alt="image" />
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
            <span class="title"><?= $quest->getTitle();?></span>
            <p class="description"><?= $quest->getDescription();?></p>
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

            <?php if ($quest->getIsApproved()): ?>
              <span class="published">
                <i class="fas fa-check"></i>
                Published
              </span>
            <?php else: ?>
              <a href="/editQuest/<?= $quest->getQuestId(); ?>" class="enter-button">EDIT</a>
            <?php endif; ?>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</main>
<script src="/public/js/quests.js" defer></script>