document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('shareSearchInput');
    const searchDropdown = document.getElementById('searchDropdown');
    let sharesData = [];

    // Fetch shares data  
    fetch('/assets/data/json/shares.json')
        .then(response => response.json())
        .then(data => {
            sharesData = data;
        })
        .catch(error => console.error('Error loading shares data:', error));

    if (!searchInput || !searchDropdown) return;

    // Handle input
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        if (query.length < 1) {
            searchDropdown.style.display = 'none';
            return;
        }

        const filteredShares = sharesData.filter(share => 
            share.name.toLowerCase().includes(query)
        ).slice(0, 10); // Limit to 10 results

        renderDropdown(filteredShares);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.style.display = 'none';
        }
    });

    function renderDropdown(shares) {
        if (shares.length === 0) {
            searchDropdown.innerHTML = '<div class="search-item"><div class="item-name">No shares found</div></div>';
        } else {
            searchDropdown.innerHTML = shares.map(share => {
                const baseSlug = share.slug || slugify(share.name);
                const url = `/companies/${baseSlug}-unlisted-shares/`;
                return `
                    <a href="${url}" class="search-item">
                        <img src="/assets/icons/${share.icon}" alt="${share.name}" class="item-icon" onerror="this.src='/assets/img/unlisted-head.jpeg'">
                        <div class="item-name">${share.name}</div>
                    </a>
                `;
            }).join('');
        }
        searchDropdown.style.display = 'block';
    }

    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/ & /g, '-and-')         // Replace & with -and-
            // .replace(/ ltd/g, '-limited')     // Replace ltd with limited
            .replace(/\s+/g, '-')             // Replace spaces with -
            .replace(/[^\w\-]+/g, '')         // Remove all non-word chars
            .replace(/\-\-+/g, '-')           // Replace multiple - with single -
            .replace(/^-+/, '')               // Trim - from start of text
            .replace(/-+$/, '');              // Trim - from end of text
    }
});
