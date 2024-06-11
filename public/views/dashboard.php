<main class="flex-column-center-center">
  <div class="container">
    <div class="cards w-100">
      <div class="profile-column  flex-column-center-center gap-1">

        <h1 class="user-welcome">Hello, <?= $username; ?></h1>
        <div class="profile-picture bg-green-box">
          <img src="https://picsum.photos/300/200" alt="image" />
        </div>

        <span class="join-date">Joined <?= $joinDate; ?></span>

        <div class="information-header">
          <span class="information-text">Settings</span>

          <a href="#" class="view-all">View all</a>
        </div>

        <div class="one-line-list">
          <button class="achievement bg-green-box" id="change-password">
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
            <i class="fas fa-crown <?php if ($points > 200): ?>
              fa-gold
              <?php elseif ($points > 100): ?>
              fa-silver
              <?php elseif ($points > 0): ?>
              fa-bronze
              <?php endif ?>">
            </i>
          </div>

          <div class=" information-container bg-green-box">
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
              <div class="container-card bg-green-box progress-card">
                <div class="card-top">
                  <img class="image-green-box card-image"
                    src="<?= $quest->getPictureUrl() == 'none' ? "https://picsum.photos/300/200" : "/public/uploads/" . $quest->getPictureUrl(); ?>"
                    alt="image" />
                  <div class="infos">
                    <span class="info">
                      <i class="fas fa-star-half-alt"></i>
                      <?= $progress->getScore(); ?>
                    </span>


                    <span class="info">
                      <i class="fas fa-calendar-check"></i>
                      <?= $progress->getCompletionDate() ?: 'uncomplete'; ?>
                    </span>
                  </div>
                </div>
                <span class="title"><?= $quest->getTitle(); ?></span>

                <div class="infos mt-1">
                  <span class="info">
                    <i class="fas fa-link"></i>
                    <?= $quest->getBlockchain(); ?>
                  </span>

                  <span class="info">
                    <i class="fas fa-wallet"></i>
                    <?= substr($progress->getWalletAddress(), 0, 5) ?>...
                  </span>
                  <span class="info">
                    <i class="fas fa-coins"></i>
                    <?= number_format($quest->getPoolAmount() / $quest->getParticipantsLimit(), 4); ?>
                  </span>
                </div>

                <span class="published">
                  Status:
                  <?php if ($progress->getState()->getStateId() == 1): ?>
                    In progress
                  <?php elseif ($progress->getState()->getStateId() == 4): ?>
                    Abandoned
                  <?php elseif (strtotime($quest->getPayoutDate()) < time()): ?>
                    Withdrawed
                  <?php else: ?>
                    To be withdrawed
                  <?php endif ?>
                </span>

                <a href="https://explorer.bitquery.io/solana/address/<?= $progress->getWalletAddress(); ?>"
                  class="show-more-btn view-button">view
                  explorer</a>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
      </div>
    </div>
</main>

<dialog id="confirmationDialog">
  <div class="flex-column-center-center w-100">
    <h3>Password change</h3>
    <div class="form-container">

      <form class="flex-column-center-center gap-1 password-form">
        <label for="current-password">Current Password</label>
        <input type="password" id="current-password" name="current-password" class="login-input" required>

        <label for="new-password">New Password</label>
        <input type="password" id="new-password" name="new-password" class="login-input" required>

        <label for="confirm-password">Confirm New Password</label>
        <input type="password" id="confirm-password" name="confirm-password" class="login-input" required>
        <span class="error-message"></span>

      </form>
      <menu>
        <button id="confirm-yes" onclick="handleClick(event)">change</button>
        <button id="confirm-no" onclick="closeModal(event)">cancel</button>
      </menu>
    </div>
  </div>
</dialog>

<script type="text/javascript" src="/public/js/changePasswordModal.js" defer></script>