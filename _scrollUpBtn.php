<?php

echo '<script>';
echo 'function scrollToTop() {window.scrollTo(0, 0);}';
echo 'var lastPosition = 0;';
echo 'window.addEventListener("scroll", function () {';
echo '    var scrollPosition = window.scrollY;';
echo '    var diff = lastPosition - scrollPosition;';
echo '    lastPosition = scrollPosition;';
echo '    var scrollButton = document.getElementById("scrollBtn");';
echo '    if (scrollPosition > 150 && diff < 0) {';
echo '        scrollButton.style.display = "block";';
echo '    } else {';
echo '        scrollButton.style.display = "none";';
echo '    }';
echo '});';
echo '</script>';

// HTML code for the scroll button
echo '<button onclick="scrollToTop()" id="scrollBtn" class="scrollBtn" title="Scroll to Top">';
echo '    <i class="fas fa-caret-up" style="float:top !important; "></i>';
echo '</button>';
?>
