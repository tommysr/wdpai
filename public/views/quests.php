<main class="flex-column-center-center">
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
            <?= $quest->getWorthKnowledge(); ?>
          </span>

          <span class="info">
            <i class="fas fa-wallet"></i>
            <?= $quest->getRequiredWallet(); ?>
          </span>

          <span class="info">
            <i class="fas fa-flag-checkered"></i>
            <?= $quest->getTimeRequired(); ?>
          </span>
        </div>

        <p class="description"><?= $quest->getDescription(); ?></p>
      </div>
      <div class="card-right-background">
        <span class="info">
          <i class="fas fa-clock"></i>
          <?= $quest->getExpiryDate(); ?>
        </span>

        <span class="info">
          <i class="fas fa-running"></i>
          <?= $quest->getParticipantsCount(); ?>
        </span>
        <span class="info">
          <i class="fas fa-coins"></i>
          <?= $quest->getPoolAmount(); ?>
        </span>

        <a href="/enterQuest/<?= $quest->getQuestId(); ?>" class="enter-button" style="text-decoration: none;">ENTER</a>
      </div>
    </div>
  <?php endforeach; ?>
</main>