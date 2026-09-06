document.addEventListener('DOMContentLoaded', function () {
  const hamburger = document.getElementById('hamburger');
  const navMenu = document.getElementById('nav-menu');

  hamburger.addEventListener('click', function () {
      navMenu.classList.toggle('active');
  });

  // Close menu when clicking a nav link
  const navLinks = document.querySelectorAll('.nav-link');
  navLinks.forEach(link => {
      link.addEventListener('click', () => {
          navMenu.classList.remove('active');
      });
  });

  // Search form submission
  document.getElementById('search-button').addEventListener('click', function () {
      document.getElementById('search-form').submit();
  });
});

document.getElementById('hamburger').addEventListener('click', function() {
    const navMenu = document.getElementById('nav-menu');
    navMenu.classList.toggle('active');
});

document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');

    hamburger.addEventListener('click', function () {
        navMenu.classList.toggle('active');
    });

    // Close the menu when a link is clicked (optional for better UX)
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });
    });
});

document.addEventListener('scroll', function () {
    const heroSection = document.querySelector('.hero');
    const navbar = document.querySelector('.navbar');
    const scrollPosition = window.scrollY;
    const navbarHeight = navbar.offsetHeight; // Get the height of the navbar

    // Adjust the opacity of the hero section based on scroll position
    if (scrollPosition > 0) {
        const opacity = 1 - (scrollPosition - navbarHeight) / (window.innerHeight * 0.5); // Adjust the divisor for faster/slower fade
        heroSection.style.opacity = opacity < 0 ? '0' : opacity.toString();
    } else {
        heroSection.style.opacity = '1';
    }
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent default jump behavior
        const targetId = this.getAttribute('href'); // Get the target section ID
        const targetSection = document.querySelector(targetId); // Find the target section
        if (targetSection) {
            targetSection.scrollIntoView({
                behavior: 'smooth', // Smooth scroll
                block: 'start' // Align to the top of the section
            });
        }
    });
});

const lenis = new Lenis({
    duration: 1.2, // Scroll duration
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // Custom easing function
    smooth: true, // Enable smooth scrolling
});

function raf(time) {
    lenis.raf(time); // Update Lenis on each frame
    requestAnimationFrame(raf); // Loop
}

requestAnimationFrame(raf); // Start the animation loop

// Fade in the page on load
window.addEventListener('load', function () {
    document.body.style.opacity = '1';
});

