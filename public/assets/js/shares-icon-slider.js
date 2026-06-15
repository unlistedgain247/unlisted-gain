document.addEventListener('DOMContentLoaded', () => {
    const iconSliderTrack = document.getElementById('iconSliderTrack');
    let sharesData = [];

    // Fetch shares data 
    if (!iconSliderTrack) return;

    fetch('/assets/data/json/shares.json')
        .then(response => response.json())
        .then(data => {
            sharesData = data.filter(share => share.icon && share.icon !== "");
            renderSlider(sharesData);
        })
        .catch(error => console.error('Error loading shares data for slider:', error));

    function renderSlider(shares) {
        // Create the initial set of icons
        const iconHtml = shares.map(share => `
            <div class="icon-slide">
                <img src="/assets/icons/${share.icon}" alt="${share.name}" title="${share.name}" onerror="this.style.display='none'">
            </div>
        `).join('');

        // Double the icons for a seamless loop
        iconSliderTrack.innerHTML = iconHtml + iconHtml;
    }
});
