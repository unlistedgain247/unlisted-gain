{{-- Scroll-to-top button + shareable-link modal.
     Self-contained (reads document.title / window.location.href), so it's
     safe to @include on any public page without extra Blade variables. --}}

<a href="https://wa.me/919891881886" target="_blank" rel="noopener" class="whatsapp-float-btn" title="Chat with us on WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>

<button type="button" class="scroll-top-btn" id="scrollTopBtn" title="Back to top">
    <i class="fas fa-arrow-up"></i>
</button>

<div class="share-modal-overlay" id="shareModalOverlay">
    <div class="share-modal">
        <div class="share-modal-header">
            <h3>Share this page</h3>
            <button type="button" class="share-modal-close" id="shareModalClose"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="share-modal-body">
            <button type="button" class="share-modal-option" data-modal-share="whatsapp">
                <span class="share-modal-icon wa"><i class="fab fa-whatsapp"></i></span> WhatsApp
            </button>
            <button type="button" class="share-modal-option" data-modal-share="twitter">
                <span class="share-modal-icon tw"><i class="fab fa-x-twitter"></i></span> X (Twitter)
            </button>
            <button type="button" class="share-modal-option" data-modal-share="linkedin">
                <span class="share-modal-icon li"><i class="fab fa-linkedin-in"></i></span> LinkedIn
            </button>
            <button type="button" class="share-modal-option" id="shareModalCopy">
                <span class="share-modal-icon cp"><i class="fas fa-link"></i></span> Copy Link
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    // ── Scroll to top ──
    var scrollTopBtn = document.getElementById('scrollTopBtn');
    window.addEventListener('scroll', function () {
        if (scrollTopBtn) scrollTopBtn.classList.toggle('visible', window.scrollY > 500);
    }, { passive: true });
    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ── Share buttons ──
    var pageUrl = window.location.href;
    var pageTitle = document.title;
    var shareUrls = {
        whatsapp: 'https://wa.me/?text=' + encodeURIComponent(pageTitle + ' ' + pageUrl),
        twitter: 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(pageTitle) + '&url=' + encodeURIComponent(pageUrl),
        linkedin: 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(pageUrl),
    };

    function openShareWindow(network) {
        var url = shareUrls[network];
        if (url) window.open(url, '_blank', 'noopener,width=600,height=500');
    }

    function fallbackCopy(text) {
        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        var ok = false;
        try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
        document.body.removeChild(textarea);
        return ok;
    }

    function copyPageLink(btn, doneIconHtml, idleIconHtml) {
        function onSuccess() {
            btn.classList.add('copied');
            btn.innerHTML = doneIconHtml;
            setTimeout(function () {
                btn.classList.remove('copied');
                btn.innerHTML = idleIconHtml;
            }, 1800);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(pageUrl).then(onSuccess, function () {
                if (fallbackCopy(pageUrl)) onSuccess();
            });
        } else if (fallbackCopy(pageUrl)) {
            onSuccess();
        }
    }

    // ── Breadcrumb share button + modal ──
    var shareModalOverlay = document.getElementById('shareModalOverlay');
    var shareModalClose = document.getElementById('shareModalClose');

    function closeShareModal() {
        if (shareModalOverlay) shareModalOverlay.classList.remove('open');
    }

    function openShareModal() {
        if (shareModalOverlay) shareModalOverlay.classList.add('open');
    }

    var bcInner = document.querySelector('.ug-bc-inner');
    if (bcInner) {
        // Page has a breadcrumb — put the trigger inline with it.
        var bcShareBtn = document.createElement('button');
        bcShareBtn.type = 'button';
        bcShareBtn.id = 'breadcrumbShareBtn';
        bcShareBtn.className = 'breadcrumb-share-btn';
        bcShareBtn.innerHTML = '<i class="fas fa-share-nodes"></i> Share';
        bcInner.appendChild(bcShareBtn);
        bcShareBtn.addEventListener('click', openShareModal);
    }

    if (shareModalClose) shareModalClose.addEventListener('click', closeShareModal);
    if (shareModalOverlay) {
        shareModalOverlay.addEventListener('click', function (e) {
            if (e.target === shareModalOverlay) closeShareModal();
        });
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeShareModal();
    });

    document.querySelectorAll('[data-modal-share]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openShareWindow(btn.dataset.modalShare);
            closeShareModal();
        });
    });

    var shareModalCopyBtn = document.getElementById('shareModalCopy');
    if (shareModalCopyBtn) {
        var originalHtml = shareModalCopyBtn.innerHTML;
        shareModalCopyBtn.addEventListener('click', function () {
            copyPageLink(
                shareModalCopyBtn,
                '<span class="share-modal-icon cp"><i class="fas fa-check"></i></span> Copied!',
                originalHtml
            );
        });
    }
})();
</script>
