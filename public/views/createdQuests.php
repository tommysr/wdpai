<main class="flex-column-center-center">
  <?php if (sizeof($quests) == 0): ?>
    <h1 class="main-text">No records exist</h1>
  <?php endif; ?>

  <div style="width: 80%; display: flex; justify-content: flex-end; margin-bottom: 2em;">
    <a href="/createQuest" style="text-decoration: none; text-align:center; padding: 1em;" class="main-button">Create
      new quest</a>
  </div>
  <?php foreach ($quests as $quest): ?>

    <div class="card-background">
      <div class="card-image-background">
        <img class="card-image" src="https://picsum.photos/300/200" alt="image" />
      </div>
      <div class="card-inner">
        <span class="title"><?= $quest->getTitle(); ?></span>
        <div class="card-infos">
          <span class="info">
            <i class="fas fa-book-open"></i>
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

        <p class="description"><?= $quest->getDescription(); ?></p>
      </div>
      <div class="card-right-background">
        <span class="info">
          <i class="fas fa-clock"></i>
          <?= $quest->getExpiryDateString(); ?>
        </span>

        <span class="info">
          <i class="fas fa-running"></i>
          <?= $quest->getParticipantsCount(); ?>
        </span>
        <span class="info">
          <i class="fas fa-coins"></i>
          <?= $quest->getPoolAmount(); ?>
        </span>
        <?php if ($quest->getIsApproved()): ?>
          <span class="info">
            <i class="fas fa-check"></i>
            Published
          </span>
        <?php else: ?>
          <a href="/editQuest/<?= $quest->getQuestId(); ?>" class="enter-button" style="text-decoration: none;">EDIT</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</main>