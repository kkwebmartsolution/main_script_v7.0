/**
 * Standalone School Website Script
 * Pure Vanilla JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Navigation Toggle
    const hamburgerBtn = document.getElementById('schoolHamburger');
    const navList = document.getElementById('schoolNavList');

    if (hamburgerBtn && navList) {
        hamburgerBtn.addEventListener('click', function() {
            navList.classList.toggle('active');
            const icon = hamburgerBtn.querySelector('i');
            if (icon) {
                if (navList.classList.contains('active')) {
                    icon.className = 'fas fa-times';
                } else {
                    icon.className = 'fas fa-bars';
                }
            }
        });
    }

    // Close mobile nav on link click
    const navLinks = document.querySelectorAll('.school-nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (navList && navList.classList.contains('active')) {
                navList.classList.remove('active');
                if (hamburgerBtn) {
                    const icon = hamburgerBtn.querySelector('i');
                    if (icon) icon.className = 'fas fa-bars';
                }
            }
        });
    });

    // Handle Admission Enquiry Form Submission (Static Demo Data)
    const enquiryForm = document.getElementById('schoolEnquiryForm');
    if (enquiryForm) {
        enquiryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you! Your online admission enquiry has been submitted successfully. Our admission counselor will contact you shortly.');
            enquiryForm.reset();
        });
    }

    // Handle Contact Form Submission
    const contactForm = document.getElementById('schoolContactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you! Your message has been sent successfully.');
            contactForm.reset();
        });
    }
});
