$(function () {
    var $input    = $('#shareSearchInput');
    var $dropdown = $('#searchDropdown');
    if (!$input.length || !$dropdown.length) return;

    var allStocks = [];
    var loaded    = false;

    function fetchStocks(cb) {
        if (loaded) { cb(); return; }
        $.getJSON('/stocks/search-list')
            .done(function (data) { allStocks = data; loaded = true; cb(); })
            .fail(function ()     { loaded = true; cb(); });
    }

    function renderDropdown(stocks) {
        if (!stocks.length) {
            $dropdown.html('<div class="search-item"><div class="item-name" style="color:#999;font-size:14px">No results found</div></div>').show();
            return;
        }
        var html = $.map(stocks, function (s) {
            var icon = s.logo
                ? '<img src="' + s.logo + '" alt="' + s.name + '" class="item-icon" onerror="this.style.display=\'none\'">'
                : '<div class="item-icon-fallback">' + s.name.substring(0, 2).toUpperCase() + '</div>';
            return '<a href="/companies/' + s.slug + '/" class="search-item">' + icon + '<div class="item-name">' + s.name + '</div></a>';
        }).join('');
        $dropdown.html(html).show();
    }

    function getFiltered() {
        var q = $input.val().trim().toLowerCase();
        if (q.length === 0) return allStocks.slice(0, 30);
        return $.grep(allStocks, function (s) { return s.name.toLowerCase().indexOf(q) !== -1; });
    }

    // Show on focus
    $input.on('focus', function () {
        fetchStocks(function () { renderDropdown(getFiltered()); });
    });

    // Filter on type
    $input.on('input', function () {
        fetchStocks(function () { renderDropdown(getFiltered()); });
    });

    // Navigate on Enter
    $input.on('keydown', function (e) {
        if (e.key === 'Enter') {
            var first = $dropdown.find('.search-item[href]').first();
            if (first.length) window.location.href = first.attr('href');
        }
    });

    // Close on outside click
    $(document).on('click', function (e) {
        if (!$input.is(e.target) && !$dropdown.is(e.target) && $dropdown.has(e.target).length === 0) {
            $dropdown.hide();
        }
    });
});
