<nav>
  <div class="menu-container">
    <img src="/public/assets/menu.svg" onclick="openNav()" />
  </div>

  <div class="dropdown">
    <button class="dropbtn" onclick="myFunction()">Filter
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content" id="myDropdown">
      <a href="#">Top rated</a>
      <a href="#">Recommended</a>
    </div>
  </div>

  <div class="mobile_logo">
    <img src="/public/assets/mobile_logo.svg" />
  </div>
  <?php require_once 'public/views/components/follow_bar.php'; ?>
</nav>

<style>
  .navbar {
    display: flex;
    align-items: center;
    overflow: hidden;
  }

  .navbar a {
    float: left;
    font-size: 16px;
    color: white;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
  }

  .dropdown {
    float: left;
    overflow: hidden;
  }

  .dropdown .dropbtn {
    cursor: pointer;
    font-size: 16px;
    border: none;
    outline: none;
    color: white;
    padding: 14px 16px;
    background-color: inherit;
    font-family: inherit;
    margin: 0;
  }

  .navbar a:hover,
  .dropdown:hover .dropbtn,
  .dropbtn:focus {
    color: aqua;
  }

  .dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 200;
  }

  .dropdown-content a {
    float: none;
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
  }

  .dropdown-content a:hover {
    background-color: #ddd;
  }

  .show {
    display: block;
  }
</style>


<script type="text/javascript" src="/public/js/sidenav.js" defer></script>

<script>
  function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
  }

  // Close the dropdown if the user clicks outside of it
  window.onclick = function (e) {
    if (!e.target.matches('.dropbtn')) {
      var myDropdown = document.getElementById("myDropdown");
      if (myDropdown.classList.contains('show')) {
        myDropdown.classList.remove('show');
      }
    }
  }
</script>