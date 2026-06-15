document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("companySearch");
  const sortSelect = document.getElementById("sortAlpha");
  const tbody = document.getElementById("tableBody");
  const noResults = document.getElementById("noResults");
  const pageNumbersCont = document.getElementById("pageNumbers");
  const prevBtn = document.getElementById("prevPage");
  const nextBtn = document.getElementById("nextPage");

  let allRows = [];
  let filteredRows = [];
  let currentPage = 1;
  const rowsPerPage = 50;

  // Fetch data from JSON and build table rows
  fetch("/assets/data/json/shares.json")
    .then((res) => res.json())
    .then((data) => {
      data.forEach((item, index) => {
        const baseSlug = item.slug || item.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        const slug = baseSlug + "-unlisted-shares";
        const tr = document.createElement("tr");

        const statusClass = item.dhrpStatus === "Filed" ? "green" : "gray";

        tr.innerHTML = `
          <td>${index + 1}</td>
          <td class="td-company">
            <a href="/companies/${slug}/" class="company-link">
              <div class="company-info">
                <img src="/assets/icons/${item.icon}" alt="${item.name}" onerror="this.style.display='none'">
                <span>${item.name}</span>
              </div>
            </a>
          </td>
          <td>${item.currentPrice}</td>
          <td>${item.faceValue}</td>
          <td>${item.bookValue}</td>
          <td>${item.marketCap}</td>
          <td>${item.priceEarning}</td>
          <td><span class="status-badge ${statusClass}">${item.dhrpStatus}</span></td>
        `;

        tbody.appendChild(tr);
      });

      allRows = Array.from(tbody.querySelectorAll("tr"));
      filteredRows = [...allRows];
      initTable();
    });

  function initTable() {
    updateFilteredRows();
    renderTable();
    renderPagination();
  }

  function updateFilteredRows() {
    const term = searchInput.value.toLowerCase();
    filteredRows = allRows.filter((row) => {
      const name = row
        .querySelector(".td-company span")
        .textContent.toLowerCase();
      return name.includes(term);
    });
  }

  function renderTable() {
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    // Hide everything
    allRows.forEach((row) => (row.style.display = "none"));

    // Show only rows for the current filtered page
    const pageRows = filteredRows.slice(start, end);

    if (pageRows.length === 0) {
      noResults.style.display = "block";
    } else {
      noResults.style.display = "none";
      pageRows.forEach((row, index) => {
        row.style.display = "";
        // Correct SL based on position in the full list
        row.cells[0].textContent = start + index + 1;
      });
    }
  }

  function renderPagination() {
    const pageCount = Math.ceil(filteredRows.length / rowsPerPage);
    pageNumbersCont.innerHTML = "";

    if (pageCount <= 1) {
      document.getElementById("paginationWrapper").style.display = "none";
      return;
    }

    document.getElementById("paginationWrapper").style.display = "flex";

    for (let i = 1; i <= pageCount; i++) {
      const btn = document.createElement("button");
      btn.classList.add("page-num");
      if (i === currentPage) btn.classList.add("active");
      btn.textContent = i;
      btn.onclick = () => {
        currentPage = i;
        renderTable();
        renderPagination();
        window.scrollTo({ top: tbody.offsetTop - 100, behavior: "smooth" });
      };
      pageNumbersCont.appendChild(btn);
    }

    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === pageCount;
  }

  // Search Event
  searchInput.addEventListener("input", () => {
    currentPage = 1;
    updateFilteredRows();
    renderTable();
    renderPagination();
  });

  // Sort Event
  sortSelect.addEventListener("change", function () {
    const direction = this.value === "asc" ? 1 : -1;
    allRows.sort((a, b) => {
      const nameA = a.querySelector(".td-company span").textContent.trim();
      const nameB = b.querySelector(".td-company span").textContent.trim();
      return direction * nameA.localeCompare(nameB);
    });
    allRows.forEach((row) => tbody.appendChild(row));
    updateFilteredRows();
    renderTable();
    renderPagination();
  });

  // Nav Buttons
  prevBtn.onclick = () => {
    if (currentPage > 1) {
      currentPage--;
      renderTable();
      renderPagination();
    }
  };
  nextBtn.onclick = () => {
    const pageCount = Math.ceil(filteredRows.length / rowsPerPage);
    if (currentPage < pageCount) {
      currentPage++;
      renderTable();
      renderPagination();
    }
  };
});
