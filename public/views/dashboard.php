<main class="flex-column-center-center">
  <div class="container">
    <div class="cards w-100">
      <div class="profile-column  flex-column-center-center gap-1">

        <h1 class="user-welcome">Hello, <?= $username; ?></h1>
        <div class="profile-picture">
          <img src="https://picsum.photos/300/200" alt="image" />
        </div>

        <span class="join-date">Joined <?= $joinDate; ?></span>


        <div class="information-header">
          <span class="information-text">Statistics</span>

          <a href="#" class="view-all">View all</a>
        </div>

        <div class="one-line-list">
          <div class="information-container">
            <!-- TODO: change color based on some ranking -->
            <i class="fas fa-crown"></i>
          </div>

          <div class="information-container">
            <i class="fas fa-lightbulb"></i>
            <span class="information-text"> <?= $points; ?></span>
          </div>
        </div>

        <div class="information-header">
          <span class="information-text">Achievements</span>

          <a href="#" class="view-all">View all</a>
        </div>

        <div class="one-line-list">
          <div class="information-container achievement">
            <i class="fab fa-amazon"></i>
          </div>
          <div class="information-container achievement">
            <i class="fab fa-facebook"></i>
          </div>
        </div>
      </div>


      <div class="profile-column">
        <div class="information-header">
          <span class="information-text">Previous quests</span>
          <a href="#" class="view-all">View all</a>
        </div>


        <div class="cards">
          <?php foreach ($stats as $stat):
            $quest = $stat['quest'];
            $progress = $stat['progress'];
            ?>
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

                <?php if ($quest->isExpired()): ?>
                  <button class="enter-button" onclick="downloadReport(<?= $quest->getQuestID(); ?>)">get report</button>
                <?php elseif ($quest->getIsApproved()): ?>
                  <span class="published">
                    <i class="fas fa-check"></i>
                    Published
                  </span>
                <?php else: ?>
                  <a href="/showEditQuest/<?= $quest->getQuestId(); ?>" class="enter-button">EDIT</a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
      </div>
    </div>
</main>