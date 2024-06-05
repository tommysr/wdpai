<nav>
  <div class="menu-container">
    <img src="/public/assets/menu.svg" onclick="openNav()" />
  </div>

  <div class="menus">
    <div class="inline-menu">
      <a class="nav-link" href="/dashboard">
        Top rated
        <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
        </svg>
      </a>
      <a class="nav-link" href="/dashboard">
        Recommended
        <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
        </svg>
      </a>
    </div>

    <div class="dropdown">
      <button class="dropbtn" onclick="myFunction()">Filter
        <i class="fa fa-caret-down"></i>
      </button>
      <div class="dropdown-content" id="myDropdown">
        <a class="nav-link" href="/dashboard">
          Top rated
          <svg width="132" height="2" viewBox="0 0 132 2" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 1H132" stroke="#EBF6E5" stroke-width="2" />
          </svg>
        </a>
        <a class="nav-link" href="/dashboard">
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

<script>
  function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
  }

  window.onclick = function (e) {
    if (!e.target.matches('.dropbtn')) {
      var myDropdown = document.getElementById("myDropdown");
      if (myDropdown.classList.contains('show')) {
        myDropdown.classList.remove('show');
      }
    }
  }

  function isRoute(route) {
    return window.location.pathname === route;
  }

  function displayContentBasedOnRoute() {
    if (isRoute('/showQuests')) {
      const element = document.querySelector('.menus');
      if (element) {
        element.style.display = 'block';
      }
    }
  }

  displayContentBasedOnRoute();

</script>