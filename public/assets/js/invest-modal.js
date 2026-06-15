// invest-modal.js — modal open/close + auth-redirect flow (server-session backed)

function openInvestModal(data) {
    var type    = data.type    || 'buy';
    var company = data.company || '';
    var price   = data.price != null
        ? '₹' + Number(data.price).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
        : '—';
    var fincode  = data.fincode  || '';
    var lotSize  = parseInt(data.lot_size, 10) || 50;
    var label    = type === 'sell' ? 'Sell' : 'Buy';

    $('#investTitle').text(label + ' – ' + company);
    $('#investFundName').text(company);
    $('#investSharePrice').text(price);
    $('#investSubmitBtn').text(label).toggleClass('sell-mode', type === 'sell').data({ fincode: fincode, type: type, company: company, price: data.price, lotSize: lotSize });
    $('#investQty').attr({ min: lotSize, step: lotSize }).val(lotSize);
    $('#investMinQtyLabel').text('(Min QTY - ' + lotSize + ')');
    $('#investAlert').hide().text('').removeClass('invest-alert-success invest-alert-error');

    $('#investModal').fadeIn(180);
    $('body').addClass('invest-modal-open');
}

function closeInvestModal() {
    $('#investModal').fadeOut(180);
    $('body').removeClass('invest-modal-open');
}

$(document).ready(function () {

    // --- Close handlers ---
    $('#investModalClose').on('click', closeInvestModal);
    $('#investModal').on('click', function (e) {
        if ($(e.target).is('#investModal')) closeInvestModal();
    });
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $('#investModal').is(':visible')) closeInvestModal();
    });

    // --- Buy / Sell submit ---
    $('#investSubmitBtn').on('click', function () {
        var qty     = parseInt($('#investQty').val(), 10);
        var lotSize = $(this).data('lotSize') || 50;
        if (!qty || qty < lotSize) {
            showInvestAlert('Minimum quantity is ' + lotSize + '.', 'error');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('Submitting…');

        $.ajax({
            url: '/invest-inquiry',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: {
                fincode: $btn.data('fincode'),
                type:    $btn.data('type'),
                company: $btn.data('company'),
                price:   $btn.data('price'),
                qty:     qty,
            },
            success: function () {
                showInvestAlert('Request submitted! Our team will contact you shortly.', 'success');
                setTimeout(closeInvestModal, 2500);
            },
            error: function () {
                showInvestAlert('Something went wrong. Please try again.', 'error');
            },
            complete: function () {
                var label = $btn.data('type') === 'sell' ? 'Sell' : 'Buy';
                $btn.prop('disabled', false).text(label);
            }
        });
    });

    // --- Callback button ---
    $('#investCallbackBtn').on('click', function () {
        var $submit = $('#investSubmitBtn');
        var $self   = $(this);
        $self.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i> Sending…');

        $.ajax({
            url: '/invest-inquiry',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: {
                fincode: $submit.data('fincode'),
                type:    'callback',
                company: $submit.data('company'),
                price:   $submit.data('price'),
                qty:     0,
            },
            success: function () {
                showInvestAlert('Callback requested! Our team will reach out shortly.', 'success');
                setTimeout(closeInvestModal, 2500);
            },
            error: function () {
                showInvestAlert('Something went wrong. Please try again.', 'error');
            },
            complete: function () {
                $self.prop('disabled', false).html('<i class="fas fa-phone-alt"></i> Request a Callback');
            }
        });
    });

    // --- Event delegation: Buy/Sell triggers anywhere on the page ---
    $(document).on('click', '.invest-trigger', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var data = {
            type:     $(this).data('type'),
            company:  $(this).data('company'),
            price:    $(this).data('price'),
            fincode:  $(this).data('fincode'),
            lot_size: $(this).data('lot-size') || 50,
        };

        if (!window.UG_AUTH) {
            // Store intent in server session, then redirect to login
            $.ajax({
                url: '/session/invest-intent',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    type:      data.type,
                    company:   data.company,
                    price:     data.price,
                    fincode:   data.fincode,
                    lot_size:  data.lot_size,
                    return_to: window.location.pathname,
                },
                complete: function () {
                    window.location.href = '/login';
                }
            });
            return;
        }

        openInvestModal(data);
    });

    // --- Auto-open after login/register redirect (data comes from server session via window.pendingInvest) ---
    if (window.UG_AUTH && window.pendingInvest) {
        openInvestModal(window.pendingInvest);

        // Tell the server to clear invest_intent from session now that we've consumed it
        $.post('/session/clear-invest-intent', {
            _token: $('meta[name="csrf-token"]').attr('content')
        });
    }

    function showInvestAlert(msg, type) {
        $('#investAlert')
            .removeClass('invest-alert-success invest-alert-error')
            .addClass(type === 'success' ? 'invest-alert-success' : 'invest-alert-error')
            .text(msg)
            .show();
    }
});
