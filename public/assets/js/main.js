// =============================================
// MOBILE MENU
// =============================================

function closeMobileMenu() {
    const nav = document.getElementById('mainNav');
    const toggleBtn = document.getElementById('mobileToggle');
    if (nav) nav.classList.remove('active');
    if (toggleBtn) toggleBtn.classList.remove('open');
    document.querySelectorAll('.main-header .has-dropdown.active').forEach(function (el) {
        el.classList.remove('active');
    });
}

document.addEventListener('click', function (e) {
    const isMobile = window.innerWidth <= 1024;

    // 1. Hamburger toggle
    const toggleBtn = e.target.closest('#mobileToggle');
    if (toggleBtn) {
        const nav = document.getElementById('mainNav');
        if (nav) {
            nav.classList.toggle('active');
            toggleBtn.classList.toggle('open');
        }
        // Close account dropdown when opening hamburger nav
        document.querySelectorAll('.main-header .account-wrapper.open').forEach(function (el) {
            el.classList.remove('open');
        });
        return;
    }

    // 1b. Account trigger — click-to-toggle on all screen sizes
    const accountTrigger = e.target.closest('.account-trigger');
    if (accountTrigger) {
        e.stopPropagation();
        const wrapper = accountTrigger.closest('.account-wrapper');
        const isOpen  = wrapper.classList.contains('open');
        document.querySelectorAll('.main-header .account-wrapper.open').forEach(function (el) {
            el.classList.remove('open');
        });
        if (!isOpen) wrapper.classList.add('open');
        return;
    }

    // Close account dropdown when clicking outside
    if (!e.target.closest('.main-header .account-wrapper')) {
        document.querySelectorAll('.main-header .account-wrapper.open').forEach(function (el) {
            el.classList.remove('open');
        });
    }

    // 2. FAQ accordion
    const faqQuestion = e.target.closest('.faq-question');
    if (faqQuestion) {
        const item = faqQuestion.parentElement;
        const isActive = item.classList.contains('active');
        document.querySelectorAll('.faq-item').forEach(function (el) {
            el.classList.remove('active');
        });
        if (!isActive) {
            item.classList.add('active');
        }
        return;
    }

    // 3. Close mobile menu when clicking outside nav panel
    if (isMobile) {
        const nav = document.getElementById('mainNav');
        if (nav && nav.classList.contains('active')) {
            if (!e.target.closest('#mainNav') && !e.target.closest('#mobileToggle')) {
                closeMobileMenu();
                return;
            }
        }
    }

    // 4. Mobile dropdown toggles
    if (isMobile) {
        const navLink = e.target.closest('.nav-link');
        if (navLink && navLink.parentElement.classList.contains('has-dropdown')) {
            e.preventDefault();
            e.stopImmediatePropagation();
            navLink.parentElement.classList.toggle('active');
            return;
        }

        // Close menu when clicking an actual nav link
        const actualLink = e.target.closest('#mainNav a:not(.nav-link)');
        if (actualLink) {
            closeMobileMenu();
        }
    }
});

// =============================================
// VIEW ALL FAQ
// =============================================

document.addEventListener('click', function (e) {
    if (e.target.id === 'viewAllFaq') {
        const extraItems = document.querySelector('.faq-extra-items');
        if (extraItems) {
            const isOpen = extraItems.classList.toggle('open');
            e.target.textContent = isOpen ? 'Show Less' : 'View All';
        }
    }
});
