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

      <div class="information-container">
        <span class="information-text">Experience</span>
      </div>
    </div>

    <div class="information-header">
      <span class="information-text">Achievements</span>

      <a href="#" class="view-all">View all</a>
    </div>

    <div class="one-line-list">
      <div class="information-container">
        <i class="fab fa-amazon"></i>
      </div>
      <div class="information-container">
        <i class="fab fa-facebook"></i>
      </div>
    </div>
  </div>


  <div class="profile-column">
    <div class="information-header">
      <span class="information-text">Previous quests</span>
      <a href="#" class="view-all">View all</a>
    </div>
  </div>
</div>