(function () {
    var card = document.querySelector('.lmw-card');
    if (!card) return;

    var dataUrl   = card.getAttribute('data-url');
    var tableWrap = card.querySelector('.lmw-table-wrap');
    var body      = card.querySelector('#lmwBody');
    var tabs      = card.querySelectorAll('.lmw-tab');
    var ranges    = card.querySelectorAll('.lmw-range');

    var activeTab   = 'trending';
    var activeRange = '1w';
    var requestSeq  = 0;

    function setActive(list, el) {
        list.forEach(function (btn) { btn.classList.remove('active'); });
        el.classList.add('active');
    }

    function loadRows() {
        if (!dataUrl) return;

        var seq = ++requestSeq;
        tableWrap.classList.add('lmw-loading');

        var url = dataUrl + '?tab=' + encodeURIComponent(activeTab) + '&range=' + encodeURIComponent(activeRange);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.ok ? res.text() : Promise.reject(); })
            .then(function (html) {
                if (seq !== requestSeq) return;
                body.innerHTML = html;
            })
            .catch(function () { /* keep existing rows on failure */ })
            .finally(function () {
                if (seq === requestSeq) tableWrap.classList.remove('lmw-loading');
            });
    }

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (btn.classList.contains('active')) return;
            activeTab = btn.getAttribute('data-tab');
            setActive(tabs, btn);
            loadRows();
        });
    });

    ranges.forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (btn.classList.contains('active')) return;
            activeRange = btn.getAttribute('data-range');
            setActive(ranges, btn);
            loadRows();
        });
    });

    // ── Hero "Live Updates" ticker rotation ─────────────────────────────
    var ticker = document.getElementById('ugHeroTicker');
    if (!ticker) return;

    var items    = ticker.querySelectorAll('.ug-hero-ticker-item');
    var posLabel = document.getElementById('ugTickerPos');
    if (items.length < 2) return;

    var current  = 0;
    var timer    = null;

    function showItem(index) {
        items[current].classList.remove('active');
        current = index;
        items[current].classList.add('active');
        if (posLabel) posLabel.textContent = current + 1;
    }

    function start() {
        timer = window.setInterval(function () {
            showItem((current + 1) % items.length);
        }, 4000);
    }

    function stop() {
        if (timer) window.clearInterval(timer);
        timer = null;
    }

    start();
    ticker.addEventListener('mouseenter', stop);
    ticker.addEventListener('mouseleave', start);
})();
