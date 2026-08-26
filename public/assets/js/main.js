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

// Sticky navbar: shrink + frosted background once the page has scrolled a little.
(function () {
    var header = document.querySelector('.main-header');
    if (!header) return;

    // Hysteresis band (24px on / 8px off) instead of a single bare threshold —
    // a bare `scrollY > 8` flips on/off repeatedly when scroll position hovers
    // right around 8px (common with trackpad/momentum scrolling near the top),
    // snapping the header between its two heights and visibly "vibrating".
    function syncScrolledState() {
        var y = window.scrollY;
        if (y > 24) {
            header.classList.add('is-scrolled');
        } else if (y < 8) {
            header.classList.remove('is-scrolled');
        }
    }

    syncScrolledState();
    window.addEventListener('scroll', syncScrolledState, { passive: true });
})();

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

// =============================================
// LAZY-LOAD IMAGES (site-wide)
// =============================================
// `.lazy-img` elements ship with no real `src` — only `data-src` (and
// optionally `data-srcset`). This starts the real fetch 20px before the
// image reaches the viewport, then fades it in on load instead of letting
// it pop in abruptly. Applies to every page automatically, including
// content injected later via AJAX (news grid, price list, stock tables,
// admin lists, ...) through the MutationObserver below — new `.lazy-img`
// elements never need any extra wiring wherever they're inserted.
(function () {
    if (!('IntersectionObserver' in window)) {
        // No IntersectionObserver support: just load everything normally
        // rather than leaving images permanently blank.
        document.querySelectorAll('.lazy-img[data-src]').forEach(function (img) {
            img.src = img.dataset.src;
            if (img.dataset.srcset) img.srcset = img.dataset.srcset;
        });
        return;
    }

    var lazyObserver = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var img = entry.target;
            observer.unobserve(img);

            var reveal = function () { img.classList.add('lazy-loaded'); };
            img.addEventListener('load', reveal, { once: true });
            img.addEventListener('error', reveal, { once: true }); // don't stay invisible on a broken image

            if (img.dataset.srcset) img.srcset = img.dataset.srcset;
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
            img.removeAttribute('data-srcset');

            // Already cached — the load event may never fire.
            if (img.complete) reveal();
        });
    }, { rootMargin: '20px 0px' });

    function observeLazyImages(root) {
        root.querySelectorAll('.lazy-img[data-src]').forEach(function (img) {
            lazyObserver.observe(img);
        });
    }

    // CMS/News rich-text bodies (Article.content, UnlistedNews.content) are
    // stored HTML from TinyMCE with plain `<img src="...">` tags we don't
    // template ourselves — the browser has already started fetching those by
    // the time this script runs, so the 20px-early-start behavior above isn't
    // achievable for them, but they still shouldn't pop in abruptly. Give
    // them the same fade-in via the `load` event instead of the observer.
    var RICH_CONTENT_SELECTOR = '.article-content img, .news-detail-content img';

    function fadeInRichContentImages(root) {
        root.querySelectorAll(RICH_CONTENT_SELECTOR).forEach(function (img) {
            if (img.classList.contains('lazy-img') || img.classList.contains('rich-content-fade')) return;
            img.classList.add('rich-content-fade');
            if (img.complete) {
                img.classList.add('lazy-loaded');
            } else {
                var reveal = function () { img.classList.add('lazy-loaded'); };
                img.addEventListener('load', reveal, { once: true });
                img.addEventListener('error', reveal, { once: true });
            }
        });
    }

    observeLazyImages(document);
    fadeInRichContentImages(document);

    var bodyObserver = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (node) {
                if (node.nodeType !== 1) return;
                if (node.matches && node.matches('.lazy-img[data-src]')) lazyObserver.observe(node);
                if (node.querySelectorAll) {
                    observeLazyImages(node);
                    fadeInRichContentImages(node);
                }
            });
        });
    });
    bodyObserver.observe(document.body, { childList: true, subtree: true });
})();
