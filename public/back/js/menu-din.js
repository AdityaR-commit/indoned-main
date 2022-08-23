var url = window.location;
const tabLinks = document.querySelectorAll('.nav-tabs .nav-item a');
const currentTabLink = [...tabLinks].filter(e => {
	return e.href == url;
});

currentTabLink[0].classList.add("active");