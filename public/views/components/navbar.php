<nav>
  <div class="menu-container">
    <img src="/public/assets/menu.svg" onclick="openNav()" />
  </div>

  <div class="menus">
    <div class="inline-menu">
      <a class="nav-link top-rated">
        Top rated
        <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
        </svg>
      </a>
      <a class="nav-link recommended">
        Recommended
        <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
        </svg>
      </a>
    </div>

    <div class="dropdown">
      <button class="dropbtn" onclick="showNavbar()">Filter
        <i class="fa fa-caret-down"></i>
      </button>
      <div class="dropdown-content" id="myDropdown">
        <a class="nav-link top-rated">
          Top rated
          <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
          </svg>
        </a>
        <a class="nav-link recommended">
          Recommended
          <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
          </svg>
        </a>
      </div>
    </div>
  </div>

  <div class="mobile_logo">
    <img src="/public/assets/mobile_logo.svg" />
  </div>
  <?php require_once 'public/views/components/follow_bar.php'; ?>
</nav>

<script type="text/javascript" src="/public/js/sidenav.js" defer></script>
<script type="text/javascript" src="/public/js/navbar.js" defer></script>
