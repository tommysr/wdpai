<main class="flex-column-center-center">
  <div class="container">
    <div class="cards w-100">
      <div class="profile-column  flex-column-center-center gap-1">

        <h1 class="user-welcome" style="margin-top:0;">Hello, <?= $username; ?></h1>
        <div class="profile-picture">
          <img src="https://picsum.photos/300/200" alt="image" />
        </div>

        <span class="join-date">Joined <?= $joinDate; ?></span>

        <div class="information-header">
          <span class="information-text">Settings</span>

          <a href="#" class="view-all">View all</a>
        </div>

        <div class="one-line-list">
          <button class="achievement bg-green-box">
             change password
          </button>
        </div>

        <div class="information-header">
          <span class="information-text">Statistics</span>

          <a href="#" class="view-all">View all</a>
        </div>

        <div class="one-line-list">
          <div class="information-container bg-green-box">
            <!-- TODO: change color based on some ranking -->
            <i class="fas fa-crown"></i>
          </div>

          <div class="information-container bg-green-box">
            <i class="fas fa-lightbulb"></i>
            <span class="information-text"> <?= $points; ?></span>
          </div>
        </div>

 
      </div>


      <div class="profile-column">
        <div class="information-header">
          <span class="information-text">Previous quests</span>
          <a href="#" class="view-all">View all</a>
        </div>


        <div class="profile-quests">
          <?php foreach ($stats as $stat):
            $quest = $stat['quest'];
            $progress = $stat['progress'];
            ?>
            <div class="card">
              <div class="container-card bg-green-box" style="padding-bottom: 0.7em!important;">
                <div class="card-top">
                  <img class="image-green-box card-image"
                    src="<?= $quest->getPictureUrl() == 'none' ? "https://picsum.photos/300/200" : "/public/uploads/" . $quest->getPictureUrl(); ?>"
                    alt="image" />
                  <div class="infos">
                    <span class="info">
                      <i class="fas fa-flag-checkered"></i>
                      <?= $progress->getScore(); ?>
                    </span>


                    <span class="info">
                      <i class="fas fa-flag-checkered"></i>
                      <?= $progress->getCompletionDate(); ?>
                    </span>
                  </div>
                </div>
                <span class="title"><?= $quest->getTitle(); ?></span>

                <div class="infos" style="margin-top: 1em;">
                  <span class="info">
                    <i class="fas fa-wallet"></i>
                    <?= $quest->getBlockchain(); ?>
                  </span>

                  <span class="info">
                    <i class="fas fa-running"></i>
                    <?= $progress->getWalletAddress(); ?>
                  </span>
                  <span class="info">
                    <i class="fas fa-coins"></i>
                    <?= $quest->getPoolAmount() / $quest->getParticipantsLimit(); ?>
                  </span>
                </div>

                <span class="published">
                  Status:
                  <?php if (strtotime($quest->getPayoutDate()) < time()): ?>
                    Withdrawed
                  <?php else: ?>
                    To be withdrawed
                  <?php endif ?>
                </span>

                <a href="#" style="text-decoration:none; box-sizing: border-box; margin-top:1em;" class="show-more-btn">view explorer</a>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
      </div>
    </div>
</main>