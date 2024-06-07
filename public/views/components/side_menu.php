<script type="text/javascript" src="/public/js/sidenav.js" defer></script>

<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()"><img src="/public/assets/back_arrow.svg" /></a>

  <div class="menu-items flex-column-center-center">
    <a class="nav-link" href="/showQuests">
      Quests
      <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
      </svg>
    </a>

    <a class="nav-link" href="/dashboard">
      Profile
      <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
      </svg>
    </a>


    <?php if (isset($userRole) && $userRole == 'admin'): ?>
      <a class="nav-link" href="/showQuestsToApproval">
        Quests queue
        <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
        </svg>
      </a>

      <a class="nav-link" href="/showApprovedQuests">
        Approved quests
        <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
        </svg>
      </a>
    <?php endif; ?>


    <?php if (isset($userRole) && $userRole == 'creator'): ?>
      <a class="nav-link flex-column-center-center" href="/showCreatedQuests">
        Creator
        <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
        </svg>
      </a>
    <?php endif; ?>
  </div>


  <!-- Check if user is logged in to render appropriate link -->
  <?php if (isset($userId)): ?>
    <a href="/logout" class="main-button"
      style="text-decoration: none; display: flex; justify-content: center; align-items: center;">Sign out</a>
  <?php else: ?>
    <a href="/login" class="main-button"
      style="text-decoration: none; display: flex; justify-content: center; align-items: center;">Sign in</a>
  <?php endif; ?>

  <div class="side-logo">
    <img src="/public/assets/logo_with_name.svg" />
  </div>

  <div class="follow-bar-mobile">
    <div>
      <span class="text-bold-sm">Follow us</span>
      <img src="/public/assets/follow-bar/line.svg" />
    </div>
    <div>
      <a href="https://facebook.com"><i class="fab fa-facebook-f"></i></a>
      <a href="https://instagram.com"></a><i class="fab fa-instagram"></i></a>
      <a href="https://linkedin.com"></a><i class="fab fa-linkedin-in"></i></a>
      <img src="/public/assets/follow-bar/arrow_left.svg" />
    </div>
  </div>

  <span class="copyright">
    Copyright ChainQuest 2024 chainquest@gmail.com
  </span>

</div>