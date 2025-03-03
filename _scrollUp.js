// _scrollUp.js

function scrollToTop() {
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function initScrollButton() {
    const scrollButton = document.createElement('button');
    scrollButton.id = 'scrollBtn';
    scrollButton.className = 'scrollBtn';
    scrollButton.title = 'Scroll to Top';
    scrollButton.innerHTML = '<i class="fa fa-caret-up"></i>';
    scrollButton.onclick = scrollToTop;
    document.body.appendChild(scrollButton);

    let lastPosition = 0;
    window.addEventListener("scroll", function () {
        let scrollPosition = window.scrollY;
        let diff = lastPosition - scrollPosition;
        lastPosition = scrollPosition;
        if (scrollPosition > 150 && diff < 0) {
            scrollButton.style.display = "block";
        } else {
            scrollButton.style.display = "none";
        }
    });
}

// Initialize the scroll button when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', initScrollButton);

// USAGE
//<script src="_scrollUp.js" defer></script>
