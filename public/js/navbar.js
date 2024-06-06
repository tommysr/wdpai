function showNavbar() {
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