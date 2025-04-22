/**
 * Main JavaScript file for ENSAF website
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile navigation toggle
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            navMenu.classList.toggle('show');
            hamburger.classList.toggle('active');
        });
    }
    
    // Handle dropdown menus on mobile
    const dropdowns = document.querySelectorAll('.dropdown');
    
    if (window.innerWidth < 768) {
        dropdowns.forEach(dropdown => {
            const link = dropdown.querySelector('a');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            link.addEventListener('click', function(e) {
                if (window.innerWidth < 768) {
                    e.preventDefault();
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                }
            });
        });
    }
    
    // Add animation classes as elements come into view
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.animate-on-scroll');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.2;
            
            if (elementPosition < screenPosition) {
                element.classList.add('animate');
            }
        });
    };
    
    // Call once on load
    animateOnScroll();
    
    // And then on scroll
    window.addEventListener('scroll', animateOnScroll);
}); 