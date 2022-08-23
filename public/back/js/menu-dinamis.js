var url = window.location;
const allLinks = document.querySelectorAll('.nav-item a');
const currentLink = [...allLinks].filter(e => {
    return e.href == url;
});

currentLink[0].classList.add("active");
currentLink[0].closest(".nav-treeview").style.display = "block ";
currentLink[0].closest(".has-treeview").classList.add("menu-open");
$('.menu-open').find('a').each(function() {
    if (!$(this).parents().hasClass('active')) {
        $(this).parents().addClass("active");
        $(this).addClass("active");
    }
});