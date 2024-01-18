const menu = document.querySelector('#mobile-menu')
const menuLinks = document.querySelector('.navbar__menu')

menu.addEventListener('click', function() {

    menu.classList.toggle('is-active');
    menuLinks.classList.toggle('active');
});
document.getElementById("myButton").addEventListener("click", function() {

    window.location.href = "https://example.com";
});