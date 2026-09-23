/**
 * Standalone School SaaS Marketing Website Script
 * Pure Vanilla JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Navigation Toggle
    const hamburgerBtn = document.getElementById('schoolSaasHamburger');
    const navList = document.getElementById('schoolSaasNavList');

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

    // Close mobile nav when clicking links
    const navLinks = document.querySelectorAll('.school-saas-nav-link');
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

    // FAQ Accordion Toggle
    const faqQuestions = document.querySelectorAll('.school-saas-faq-question');
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.parentElement;
            const isActive = faqItem.classList.contains('active');
            
            // Close all items
            document.querySelectorAll('.school-saas-faq-item').forEach(item => {
                item.classList.remove('active');
            });

            // Open clicked item if it wasn't open
            if (!isActive) {
                faqItem.classList.add('active');
            }
        });
    });

    // Contact Form Demo Submission
    const contactForm = document.getElementById('schoolSaasContactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you for contacting KK EDUMART! Our enterprise specialist will reach out to you within 24 hours.');

            contactForm.reset();
        });
    }
});
