document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".tab-btn");
  const searchInput = document.getElementById("shareSearch");
  const sortSelect = document.getElementById("alphaSort"); 
  const container = document.getElementById("sharesContainer");
  const pageNumbersCont = document.getElementById("pageNumbers");
  const prevBtn = document.getElementById("prevPage");
  const nextBtn = document.getElementById("nextPage");
  const paginationWrapper = document.getElementById("paginationWrapper");
 
  let cards = [];
  let visibleCards = [];
  let currentPage = 1;
  const cardsPerPage = 25;

  // Fetch data from JSON and build cards
  fetch("/assets/data/json/shares.json")
    .then((res) => res.json())
    .then((data) => {
      data.forEach((item) => {
        const baseSlug = item.slug || item.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        const slug = baseSlug + "-unlisted-shares";
        const card = document.createElement("div");
        card.className = "share-card";
        card.dataset.category = item.category;
        card.dataset.name = item.name;

        card.innerHTML = `
          <div class="card-logo">
            <img src="/assets/icons/${item.icon}" alt="${item.name}" onerror="this.style.display='none'">
          </div>
          <div class="card-details">
            <h3>${item.name} Unlisted Shares</h3>
            ${item.isin ? `<p class="isin">ISIN: ${item.isin}</p>` : ""}
            <div class="meta-info">
              <span>Industry: <b>${item.industry || "N/A"}</b></span>
              <span>Sector: <b>${item.sector || "N/A"}</b></span>
            </div>
            <div class="meta-info">
              <span>Depository: <b>${item.depository || "N/A"}</b></span>
              <span>DRHP Status: <b>${item.dhrpStatus}</b></span>
            </div>
          </div>
          <div class="card-pricing">
            <div class="rating">${item.rating || "★★★☆☆"}</div>
            <div class="price">₹${item.currentPrice}</div>
            <p>Buy Price</p>
            <a href="/companies/${slug}/" class="learn-btn">Learn More</a>
          </div>
        `;

        card.addEventListener("click", () => {
          window.location.href = `/companies/${slug}/`;
        });

        container.appendChild(card);
      });

      cards = Array.from(container.getElementsByClassName("share-card"));
      filterShares();
    });

  function filterShares() {
    const activeTab = document.querySelector(".tab-btn.active").dataset.filter;
    const searchTerm = searchInput.value.toLowerCase();

    // Determine which cards match filter + search
    visibleCards = cards.filter((card) => {
      const matchesTab =
        activeTab === "all" || card.dataset.category === activeTab;
      const matchesSearch = card.dataset.name
        .toLowerCase()
        .includes(searchTerm);
      return matchesTab && matchesSearch;
    });

    // Hide all cards first
    cards.forEach((card) => (card.style.display = "none"));

    // Reset to page 1 when filter changes
    currentPage = 1;

    renderPage();
    renderPagination();
  }

  function renderPage() {
    const start = (currentPage - 1) * cardsPerPage;
    const end = start + cardsPerPage;

    // Hide all cards
    cards.forEach((card) => (card.style.display = "none"));

    // Show only cards for current page
    const pageCards = visibleCards.slice(start, end);
    pageCards.forEach((card) => (card.style.display = "flex"));
  }

  function renderPagination() {
    const pageCount = Math.ceil(visibleCards.length / cardsPerPage);
    pageNumbersCont.innerHTML = "";

    if (pageCount <= 1) {
      paginationWrapper.style.display = "none";
      return;
    }

    paginationWrapper.style.display = "flex";

    for (let i = 1; i <= pageCount; i++) {
      const btn = document.createElement("button");
      btn.classList.add("page-num");
      if (i === currentPage) btn.classList.add("active");
      btn.textContent = i;
      btn.onclick = () => {
        currentPage = i;
        renderPage();
        renderPagination();
        window.scrollTo({
          top: container.offsetTop - 100,
          behavior: "smooth",
        });
      };
      pageNumbersCont.appendChild(btn);
    }

    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === pageCount;
  }

  // Tab Toggle
  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");
      filterShares();
    });
  });

  // Search Input
  searchInput.addEventListener("input", filterShares);

  // Alpha Sort
  sortSelect.addEventListener("change", () => {
    const direction = sortSelect.value === "asc" ? 1 : -1;

    cards.sort((a, b) => {
      const nameA = a.dataset.name.toLowerCase();
      const nameB = b.dataset.name.toLowerCase();
      return direction * nameA.localeCompare(nameB);
    });

    cards.forEach((card) => container.appendChild(card));
    filterShares();
  });

  // Nav Buttons
  prevBtn.onclick = () => {
    if (currentPage > 1) {
      currentPage--;
      renderPage();
      renderPagination();
      window.scrollTo({
        top: container.offsetTop - 100,
        behavior: "smooth",
      });
    }
  };
  nextBtn.onclick = () => {
    const pageCount = Math.ceil(visibleCards.length / cardsPerPage);
    if (currentPage < pageCount) {
      currentPage++;
      renderPage();
      renderPagination();
      window.scrollTo({
        top: container.offsetTop - 100,
        behavior: "smooth",
      });
    }
  };
});
