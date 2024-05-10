<div class="profile-container">
  <div class="profile-column">
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


    <div class="previous-card">
      <div class="image-background">
        <img src="https://picsum.photos/300/200" alt="image" />
      </div>


      <span class="previous-title">Some funny title</span>


      <div class="info-bar">
        <div class="quest-info">
          <i class="fas fa-wallet"></i>
          <span class="info-text">Binance</span>
        </div>

        <div class="quest-info">
          <i class="fas fa-coins"></i>
          <span class="info-text">2 BUSD</span>
        </div class="quest-info">


        <div class="quest-info">
          <i class="fas fa-clock"></i>
          <span class="info-text">1d, 11h, 43 min</span>
        </div>
      </div>

      <div class="button-container"> <a href="#" class="view-button">View</a>
      </div>

    </div>
  </div>
</div>