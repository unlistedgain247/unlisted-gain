<div id="investModal" class="invest-overlay" style="display:none" role="dialog" aria-modal="true" aria-labelledby="investTitle">
    <div class="invest-box">
        <div class="invest-header">
            <h3 class="invest-title" id="investTitle">Buy</h3>
            <button class="invest-callback-btn" id="investCallbackBtn" type="button">
                <i class="fas fa-phone-alt"></i> Request a Callback
            </button>
            <button class="invest-close" id="investModalClose" type="button" aria-label="Close">&times;</button>
        </div>
        <div class="invest-body">
            <h4>Complete Your Investment</h4>
            <div class="invest-row">
                <span>Fund Name</span>
                <strong id="investFundName">—</strong>
            </div>
            <div class="invest-row">
                <span>Price per share</span>
                <strong id="investSharePrice">—</strong>
            </div>
            <div class="invest-row">
                <span>Quantity <small style="color:#bbb" id="investMinQtyLabel">(Min QTY - 50)</small></span>
                <input type="number" id="investQty" class="invest-qty-input" value="50" min="50" step="1">
            </div>
            <div id="investAlert" class="invest-alert" style="display:none"></div>
            <button id="investSubmitBtn" class="invest-submit-btn" type="button">Buy</button>
        </div>
    </div>
</div>
