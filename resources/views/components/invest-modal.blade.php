<div id="investModal" class="invest-overlay" style="display:none" role="dialog" aria-modal="true" aria-labelledby="investTitle">
    <div class="invest-box" id="investBox">
        <button class="invest-close" id="investModalClose" type="button" aria-label="Close">&times;</button>

        <div class="invest-header">
            <div class="invest-type-badge" id="investTypeBadge">
                <i class="fas fa-arrow-trend-up" id="investTypeIcon"></i>
            </div>
            <div class="invest-header-text">
                <span class="invest-kicker" id="investKicker">Buy Shares</span>
                <h3 class="invest-title" id="investTitle">—</h3>
            </div>
        </div>

        <div class="invest-body">
            <button class="invest-callback-btn" id="investCallbackBtn" type="button">
                <i class="fas fa-phone-alt"></i> Request a Callback
            </button>

            <div class="invest-info-card">
                <div class="invest-row">
                    <span><i class="fas fa-building invest-row-icon"></i> Fund Name</span>
                    <strong id="investFundName">—</strong>
                </div>
                <div class="invest-row">
                    <span><i class="fas fa-tag invest-row-icon"></i> Price per share</span>
                    <strong id="investSharePrice">—</strong>
                </div>
            </div>

            <div class="invest-qty-block">
                <div class="invest-qty-label">
                    <span>Quantity</span>
                    <small id="investMinQtyLabel">Min qty 50</small>
                </div>
                <div class="invest-qty-stepper">
                    <button type="button" class="invest-qty-btn" id="investQtyMinus" aria-label="Decrease quantity">&minus;</button>
                    <input type="number" id="investQty" class="invest-qty-input" value="50" min="50" step="1" inputmode="numeric">
                    <button type="button" class="invest-qty-btn" id="investQtyPlus" aria-label="Increase quantity">+</button>
                </div>
            </div>

            <div class="invest-total-row">
                <span>Total Investment</span>
                <strong id="investTotal">₹0</strong>
            </div>

            <div id="investAlert" class="invest-alert" style="display:none"></div>
            <button id="investSubmitBtn" class="invest-submit-btn" type="button">Buy</button>
        </div>
    </div>
</div>
