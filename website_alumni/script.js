// Simple loader function that runs immediately
(function() {
    // Hide loader after page loads
    window.addEventListener('load', function() {
        hideLoader();
    });
    
    // Fallback to hide loader after 3 seconds in case load event doesn't fire
    setTimeout(hideLoader, 3000);
    
    function hideLoader() {
        const loader = document.querySelector('.page-loader');
        if (loader) {
            loader.classList.add('hidden');
            setTimeout(function() {
                loader.style.display = 'none';
            }, 500);
        }
    }
})();

// Navigation functionality
function showSection(sectionId) {
    // Hide all sections
    const sections = document.querySelectorAll('section');
    sections.forEach(section => {
        section.style.display = 'none';
        
        // Remove animation classes if they exist
        section.classList.remove('fade-in');
    });
    
    // Show the selected section
    const selectedSection = document.getElementById(sectionId);
    if (selectedSection) {
        selectedSection.style.display = 'block';
        
        // Add fade-in animation
        setTimeout(() => {
            selectedSection.classList.add('fade-in');
        }, 10);
        
        // Update URL hash without scrolling
        history.pushState(null, null, `#${sectionId}`);
    }
    
    // Scroll to top smoothly
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Function to hide the page loader
function hidePageLoader() {
    const pageLoader = document.querySelector('.page-loader');
    if (pageLoader) {
        pageLoader.classList.add('hidden');
        setTimeout(() => {
            pageLoader.style.display = 'none';
        }, 500); // Match this with the CSS transition duration
    }
}

// Function to initialize the page loader
function initPageLoader() {
    const loader = document.querySelector('.page-loader');
    if (loader) {
        // First make sure loader is visible
        loader.classList.remove('hidden');
        loader.style.display = 'flex';
        
        // Hide loader on page load
        window.addEventListener('load', function() {
            loader.classList.add('hidden');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        });
        
        // Fallback to hide loader after 3 seconds if load event doesn't fire
        setTimeout(function() {
            if (!loader.classList.contains('hidden')) {
                loader.classList.add('hidden');
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            }
        }, 3000);
    }
}

// When the document is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Hide page loader on page load
    hidePageLoader();
    
    // Initialize navigation
    initNavigation();
    
    // Initialize mobile menu
    initMobileMenu();
    
    // Add animation effects
    initAnimations();
    
    // Initialize filters for Alumni section
    initAlumniFilters();
});

// Function to initialize navigation
function initNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links
            navLinks.forEach(l => l.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Show the corresponding section
            const sectionId = this.getAttribute('data-section');
            showSection(sectionId);
        });
    });
    
    // Check for URL hash to determine which section to show
    if (window.location.hash) {
        const sectionId = window.location.hash.substring(1);
        showSection(sectionId);
        
        // Set active class on the corresponding nav link
        const activeLink = document.querySelector(`.nav-link[data-section="${sectionId}"]`);
        if (activeLink) {
            navLinks.forEach(l => l.classList.remove('active'));
            activeLink.classList.add('active');
        }
    }
}

// Function to initialize mobile menu
function initMobileMenu() {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            navMenu.classList.toggle('show');
            
            // Toggle the icon between bars and times
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
    
    // Close menu when a link is clicked
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (navMenu.classList.contains('show')) {
                navMenu.classList.remove('show');
                
                // Reset hamburger icon
                const icon = hamburger.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        const isNavMenu = e.target.closest('#nav-menu');
        const isHamburger = e.target.closest('#hamburger');
        
        if (!isNavMenu && !isHamburger && navMenu.classList.contains('show')) {
            navMenu.classList.remove('show');
            
            // Reset hamburger icon
            const icon = hamburger.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
}

// Function to initialize animations
function initAnimations() {
    // Animate elements when they come into view
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.card, .news-item, .event-card, .alumni-card, .about-card');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const viewportHeight = window.innerHeight;
            
            if (elementPosition < viewportHeight - 100) {
                element.classList.add('animate');
            }
        });
    };
    
    // Run animation check on scroll
    window.addEventListener('scroll', animateOnScroll);
    
    // Run once on initial load
    setTimeout(animateOnScroll, 500);
}

// Function to initialize alumni filters
function initAlumniFilters() {
    const filterButton = document.getElementById('filter-button');
    if (filterButton) {
        filterButton.addEventListener('click', function() {
            const yearFilter = document.getElementById('year-filter').value;
            const departmentFilter = document.getElementById('department-filter').value;
            
            // Get all alumni cards
            const alumniCards = document.querySelectorAll('.alumni-card');
            
            alumniCards.forEach(card => {
                // Get the year and department from the card
                const year = card.querySelector('.alumni-year').textContent.replace('Promotion ', '');
                const department = card.querySelector('.alumni-department').textContent;
                
                // Check if the card matches the filters
                const yearMatch = yearFilter === '' || year === yearFilter;
                const departmentMatch = departmentFilter === '' || department.includes(departmentFilter);
                
                // Show or hide the card based on the filters
                if (yearMatch && departmentMatch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
}

// Add CSS class to body when scrolling
window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
        document.body.classList.add('scrolled');
    } else {
        document.body.classList.remove('scrolled');
    }
});

// ENSAF Website Main JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initStickyHeader();
    initMobileMenu();
    initScrollReveal();
    initScrollToTop();
    initAnimations();
    initModalFunctionality();
    initAccordions();
    initPageLoader();
    initTabSwitching();
});

// Sticky Header
function initStickyHeader() {
    const header = document.querySelector('header');
    
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                header.classList.add('sticky');
            } else {
                header.classList.remove('sticky');
            }
        });
    }
}

// Scroll Reveal Animation
function initScrollReveal() {
    const revealElements = document.querySelectorAll('.reveal');
    
    if (revealElements.length > 0) {
        function checkScroll() {
            revealElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (elementTop < windowHeight - 100) {
                    element.classList.add('active');
                }
            });
        }
        
        // Initial check
        checkScroll();
        
        // Check on scroll
        window.addEventListener('scroll', checkScroll);
    }
}

// Scroll to Top Button
function initScrollToTop() {
    const scrollTopBtn = document.querySelector('.scroll-top');
    
    if (scrollTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                scrollTopBtn.classList.add('active');
            } else {
                scrollTopBtn.classList.remove('active');
            }
        });
        
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// Modal Functionality
function initModalFunctionality() {
    const modalTriggers = document.querySelectorAll('[data-modal]');
    const modals = document.querySelectorAll('.modal');
    const closeBtns = document.querySelectorAll('.modal-close');
    
    if (modalTriggers.length > 0) {
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal');
                const modal = document.getElementById(modalId);
                
                if (modal) {
                    modal.classList.add('active');
                    document.body.classList.add('no-scroll');
                }
            });
        });
        
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.modal');
                modal.classList.remove('active');
                document.body.classList.remove('no-scroll');
            });
        });
        
        modals.forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.body.classList.remove('no-scroll');
                }
            });
        });
        
        // Close modal on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                modals.forEach(modal => {
                    if (modal.classList.contains('active')) {
                        modal.classList.remove('active');
                        document.body.classList.remove('no-scroll');
                    }
                });
            }
        });
    }
}

// Accordion Functionality
function initAccordions() {
    const accordionItems = document.querySelectorAll('.accordion-item');
    
    if (accordionItems.length > 0) {
        accordionItems.forEach(item => {
            const header = item.querySelector('.accordion-header');
            const content = item.querySelector('.accordion-collapse');
            
            if (header) {
                header.addEventListener('click', () => {
                    content.classList.toggle('show');
                    header.classList.toggle('active');
                    
                    // Optional: Close other accordions when one is opened
                    if (content.classList.contains('show')) {
                        accordionItems.forEach(otherItem => {
                            if (otherItem !== item) {
                                const otherContent = otherItem.querySelector('.accordion-collapse');
                                const otherHeader = otherItem.querySelector('.accordion-header');
                                
                                if (otherContent && otherContent.classList.contains('show')) {
                                    otherContent.classList.remove('show');
                                    otherHeader.classList.remove('active');
                                }
                            }
                        });
                    }
                });
            }
        });
    }
}

// Tab Switching
function initTabSwitching() {
    const tabContainers = document.querySelectorAll('.tabs-container');
    
    if (tabContainers.length > 0) {
        tabContainers.forEach(container => {
            const tabs = container.querySelectorAll('.tab');
            const tabContents = container.querySelectorAll('.tab-content');
            
            if (tabs.length > 0) {
                tabs.forEach((tab, index) => {
                    tab.addEventListener('click', () => {
                        // Remove active class from all tabs
                        tabs.forEach(t => t.classList.remove('active'));
                        
                        // Hide all tab contents
                        tabContents.forEach(content => content.classList.remove('active'));
                        
                        // Activate the clicked tab and content
                        tab.classList.add('active');
                        tabContents[index].classList.add('active');
                    });
                });
            }
        });
    }
}

// Department filter functionality for formations page
function filterDepartments(departmentCode) {
    const departmentCards = document.querySelectorAll('.department-card');
    const departmentDetails = document.getElementById('department-details');
    
    if (departmentCards.length > 0) {
        departmentCards.forEach(card => {
            card.classList.remove('active');
            
            if (card.getAttribute('data-code') === departmentCode) {
                card.classList.add('active');
            }
        });
        
        if (departmentDetails) {
            departmentDetails.style.display = departmentCode ? 'block' : 'none';
            
            if (departmentCode) {
                // Scroll to department details
                departmentDetails.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }
}

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    
    if (form) {
        form.addEventListener('submit', (e) => {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                    
                    // Add error message if it doesn't exist
                    let errorMsg = field.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('div');
                        errorMsg.classList.add('error-message');
                        errorMsg.textContent = 'Ce champ est obligatoire';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                } else {
                    field.classList.remove('error');
                    
                    // Remove error message if it exists
                    const errorMsg = field.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Remove error styling when user starts typing
        form.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('input', () => {
                field.classList.remove('error');
                
                const errorMsg = field.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('error-message')) {
                    errorMsg.remove();
                }
            });
        });
    }
}

// Add custom JavaScript for specific pages
function handleSpecificPages() {
    // Check the current page based on URL
    const currentPath = window.location.pathname;
    
    // Handle formations page
    if (currentPath.includes('formations.php')) {
        initYearTabs();
    }
    
    // Handle opportunities page
    if (currentPath.includes('opportunities.php')) {
        validateForm('filter-form');
    }
    
    // Handle contact page
    if (currentPath.includes('contact.php')) {
        validateForm('contact-form');
    }
}

// Year tabs for formations page
function initYearTabs() {
    const yearTabs = document.querySelectorAll('.year-tabs .tab');
    const yearContents = document.querySelectorAll('.year-content');
    
    if (yearTabs.length > 0) {
        yearTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const year = tab.getAttribute('data-year');
                
                // Remove active class from all tabs and contents
                yearTabs.forEach(t => t.classList.remove('active'));
                yearContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to the clicked tab and corresponding content
                tab.classList.add('active');
                document.getElementById(`year-${year}`).classList.add('active');
            });
        });
    }
}

// Load more functionality for news and opportunities
function initLoadMore(containerSelector, itemSelector, count = 3) {
    const container = document.querySelector(containerSelector);
    const loadMoreBtn = document.querySelector(`${containerSelector}-load-more`);
    
    if (container && loadMoreBtn) {
        const items = container.querySelectorAll(itemSelector);
        let visibleItems = count;
        
        // Hide items beyond the initial count
        items.forEach((item, index) => {
            if (index >= visibleItems) {
                item.style.display = 'none';
            }
        });
        
        // Show/hide load more button based on number of items
        if (items.length <= visibleItems) {
            loadMoreBtn.style.display = 'none';
        }
        
        loadMoreBtn.addEventListener('click', () => {
            // Show next batch of items
            for (let i = visibleItems; i < visibleItems + count && i < items.length; i++) {
                items[i].style.display = 'block';
                items[i].classList.add('fade-in');
            }
            
            visibleItems += count;
            
            // Hide load more button if all items are visible
            if (visibleItems >= items.length) {
                loadMoreBtn.style.display = 'none';
            }
        });
    }
}

// Debounce function for search fields
function debounce(func, delay = 300) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
    };
}

// Initialize search functionality
function initSearch(inputSelector, itemsSelector, filterProperty) {
    const searchInput = document.querySelector(inputSelector);
    const items = document.querySelectorAll(itemsSelector);
    
    if (searchInput && items.length > 0) {
        searchInput.addEventListener('input', debounce(function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            items.forEach(item => {
                const text = filterProperty 
                    ? item.getAttribute(filterProperty).toLowerCase() 
                    : item.textContent.toLowerCase();
                
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show no results message if all items are hidden
            let allHidden = true;
            items.forEach(item => {
                if (item.style.display !== 'none') {
                    allHidden = false;
                }
            });
            
            const noResultsMsg = document.querySelector('.no-results-message');
            if (noResultsMsg) {
                noResultsMsg.style.display = allHidden ? 'block' : 'none';
            }
        }, 300));
    }
}

// Lazy loading for images
function initLazyLoading() {
    if ('loading' in HTMLImageElement.prototype) {
        // Browser supports native lazy loading
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
        });
    } else {
        // Fallback for browsers that don't support lazy loading
        const lazyImages = document.querySelectorAll('.lazy-image');
        
        if (lazyImages.length > 0) {
            const lazyLoad = function() {
                const lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.classList.remove('lazy-image');
                            lazyImageObserver.unobserve(lazyImage);
                        }
    });
}); 
                
                lazyImages.forEach(function(lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            };
            
            // Check if IntersectionObserver is supported
            if ('IntersectionObserver' in window) {
                lazyLoad();
            } else {
                // Fallback for older browsers
                lazyImages.forEach(function(lazyImage) {
                    lazyImage.src = lazyImage.dataset.src;
                    lazyImage.classList.remove('lazy-image');
                });
            }
        }
    }
}

// Call init functions
handleSpecificPages();
initLazyLoading();