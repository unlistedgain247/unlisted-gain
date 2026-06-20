// =============================================
// HEADER
// =============================================

function applyAccountDisplayPicture(img) {
    if (!img || !img.complete) {
        return;
    }

    if (img.naturalWidth > 0) {
        img.style.display = '';
        const avatar = img.closest('.account-avatar, .account-menu-avatar');
        if (!avatar) {
            return;
        }

        avatar.style.background = 'transparent';
        avatar.querySelectorAll('.account-avatar-initial, .account-menu-initial').forEach(function (initial) {
            initial.style.display = 'none';
        });
        return;
    }

    img.style.display = 'none';
}

function initAccountDisplayPictures() {
    document.querySelectorAll('.account-avatar-dp, .account-menu-dp').forEach(function (img) {
        img.addEventListener('load', function () {
            applyAccountDisplayPicture(img);
        });

        img.addEventListener('error', function () {
            img.style.display = 'none';
        });

        applyAccountDisplayPicture(img);
    });
}

function setMobileMenuState(isOpen) {
    const nav = document.getElementById('mainNav');
    const toggleBtn = document.getElementById('mobileToggle');

    if (nav) nav.classList.toggle('active', isOpen);
    if (toggleBtn) {
        toggleBtn.classList.toggle('open', isOpen);
        toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        toggleBtn.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
    }
}

function closeMobileMenu() {
    setMobileMenuState(false);
    document.querySelectorAll('.main-header .has-dropdown.active').forEach(function (el) {
        el.classList.remove('active');
    });
}

function openMobileMenu() {
    setMobileMenuState(true);
    document.querySelectorAll('.main-header .account-wrapper.open').forEach(function (el) {
        el.classList.remove('open');
    });
}

initAccountDisplayPictures();

document.addEventListener('click', function (e) {
    const isMobile = window.innerWidth <= 1024;

    // 1. Hamburger toggle
    const toggleBtn = e.target.closest('#mobileToggle');
    if (toggleBtn) {
        e.preventDefault();
        const nav = document.getElementById('mainNav');
        if (nav && nav.classList.contains('active')) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
        return;
    }

    // 1a. Sidebar close affordances
    if (e.target.closest('#sidebarClose')) {
        closeMobileMenu();
        return;
    }

    // 1b. Account trigger - click-to-toggle on all screen sizes
    const accountTrigger = e.target.closest('.account-trigger');
    if (accountTrigger) {
        e.stopPropagation();
        const wrapper = accountTrigger.closest('.account-wrapper');
        const isOpen = wrapper.classList.contains('open');
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

    // 2. FAQ accordion - handled by jQuery below

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

window.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeMobileMenu();
    }
});

window.addEventListener('resize', function () {
    if (window.innerWidth > 1024) {
        closeMobileMenu();
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

// =============================================
// FAQ ACCORDION - jQuery slideDown/slideUp
// =============================================

$(function () {
    $(document).on('click', '.faq-question', function () {
        var $item   = $(this).closest('.faq-item');
        var $answer = $item.find('.faq-answer');
        var isOpen  = $item.hasClass('active');

        // Close all others in the same container
        $item.siblings('.faq-item').each(function () {
            $(this).removeClass('active').find('.faq-answer').slideUp(250);
        });

        if (isOpen) {
            $item.removeClass('active');
            $answer.slideUp(250);
        } else {
            $item.addClass('active');
            $answer.slideDown(250);
        }
    });
});
