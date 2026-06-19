// #region header load
fetch("/templates/header.html")
  .then((response) => response.text())
  .then((data) => {
    const header = document.getElementById("header");
    header.innerHTML = data;

    // Execute scripts in the injected HTML
    setTimeout(() => {
      const scripts = header.querySelectorAll("script");
      scripts.forEach((oldScript) => {
        const newScript = document.createElement("script");
        if (oldScript.src) {
          newScript.src = oldScript.src;
          newScript.async = oldScript.async;
        } else {
          newScript.textContent = oldScript.textContent;
        }
        document.head.appendChild(newScript);
      });
    }, 10000);

    setupMobileAccordion();
    // Not calling initMobileMenu or initMegaMenuTabs here anymore as they use global delegation
  });

// Global Resize Listener
window.addEventListener("resize", setupMobileAccordion);

// #region Global Event Listeners
// These are registered ONCE at the top level and use delegation to handle dynamically loaded content.

// Helper: close mobile menu
function closeMobileMenu() {
  const nav = document.getElementById("mainNav");
  const toggleBtn = document.getElementById("mobileToggle");
  if (nav) nav.classList.remove("active");
  if (toggleBtn) toggleBtn.classList.remove("open");
  // Also close all open dropdowns
  document.querySelectorAll("#header .has-dropdown.active").forEach(function(el) {
    el.classList.remove("active");
  });
}

// Click Handler for Mobile Menu and Accordion
document.addEventListener("click", function (e) {
  const isMobile = window.innerWidth <= 1024;

  // 1. Mobile Toggle Button (#mobileToggle)
  const toggleBtn = e.target.closest("#mobileToggle");
  if (toggleBtn) {
    const nav = document.getElementById("mainNav");
    if (nav) {
      nav.classList.toggle("active");
      toggleBtn.classList.toggle("open");
    }
    return;
  }

  // 2. FAQ Accordion Toggle Logic
  const faqQuestion = e.target.closest(".faq-question");
  if (faqQuestion) {
    const item = faqQuestion.parentElement;
    const isActive = item.classList.contains("active");

    // Close other items
    document
      .querySelectorAll(".faq-item")
      .forEach((el) => el.classList.remove("active"));

    // Toggle current item
    if (!isActive) {
      item.classList.add("active");
    }
    return; // Exit early as we've handled the click
  }

  // 3. Close mobile menu when clicking on the overlay (area outside nav panel)
  if (isMobile) {
    const nav = document.getElementById("mainNav");
    if (nav && nav.classList.contains("active")) {
      // If click is not inside the nav panel and not on the toggle button
      if (!e.target.closest("#mainNav") && !e.target.closest("#mobileToggle")) {
        closeMobileMenu();
        return;
      }
    }
  }

  // 4. Mobile Dropdown/MegaMenu Toggles (when Products/Company labels are clicked)
  if (isMobile) {
    const navLink = e.target.closest(".nav-link");
    if (navLink && navLink.parentElement.classList.contains("has-dropdown")) {
      e.preventDefault();
      e.stopImmediatePropagation();
      const parent = navLink.parentElement;
      parent.classList.toggle("active");
      return;
    }

    // 5. Close menu when clicking an actual navigation link (not a dropdown parent)
    const actualLink = e.target.closest("#mainNav a:not(.nav-link)");
    if (actualLink) {
      closeMobileMenu();
    }

    // 6. Mobile Accordion Toggles (inside Mega Menu Sidebar)
    const accordionHeader = e.target.closest(".mega-sidebar ul li");
    if (accordionHeader) {
      // Prevent toggling if clicking actual links inside the content
      if (e.target.closest(".mega-tab-content")) return;

      e.preventDefault();
      e.stopImmediatePropagation();

      accordionHeader.classList.toggle("active");
      const content = accordionHeader.querySelector(".mega-tab-content");
      if (content) {
        content.classList.toggle("active");
      }
    }
  }
});

// Mouseover Handler for Desktop Mega Menu Tabs
document.addEventListener("mouseover", function (e) {
  if (window.innerWidth <= 1024) return; // Only for desktop

  const item = e.target.closest(".mega-sidebar ul li");
  if (item) {
    const targetId = item.getAttribute("data-target");
    if (targetId) {
      // Update Sidebar Active State
      const allItems = document.querySelectorAll(".mega-sidebar ul li");
      allItems.forEach((el) => el.classList.remove("active"));
      item.classList.add("active");

      // Update Content Active State
      const allTabs = document.querySelectorAll(".mega-tab-content");
      allTabs.forEach((tab) => tab.classList.remove("active"));

      const targetTab = document.getElementById(targetId);
      if (targetTab) {
        targetTab.classList.add("active");
      }
    }
  }
});
// #endregion Global Event Listeners

// #region footer Load
fetch("/templates/footer.html")
  .then((response) => {
    if (!response.ok) throw new Error("Failed to load footer");
    return response.text();
  })
  .then((data) => {
    document.getElementById("footer").innerHTML = data;
    updateYear();
    loadWhatsappWidget();
  })
  .catch((err) => console.error(err));

// Load WhatsApp Widget
function loadWhatsappWidget() {
  fetch("/templates/whatsapp-widget.html")
    .then((response) => response.text())
    .then((data) => {
      const container = document.createElement("div");
      container.id = "whatsapp-widget-container";
      container.innerHTML = data;
      document.body.appendChild(container);

      // Inject CSS
      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.href = "/assets/css/whatsapp-widget.css";
      document.head.appendChild(link);

      initWhatsappWidget();
    });
}

function initWhatsappWidget() {
  const floatBtn = document.getElementById("whatsapp-floating-btn");
  const modalOverlay = document.getElementById("contact-modal-overlay");
  const closeModalBtn = document.getElementById("close-contact-modal");

  if (floatBtn && modalOverlay) {
    floatBtn.addEventListener("click", () => {
      modalOverlay.classList.add("active");
      document.body.style.overflow = "hidden";
    });

    closeModalBtn.addEventListener("click", () => {
      modalOverlay.classList.remove("active");
      document.body.style.overflow = "";
    });

    // Close on click outside
    modalOverlay.addEventListener("click", (e) => {
      if (e.target === modalOverlay) {
        modalOverlay.classList.remove("active");
        document.body.style.overflow = "";
      }
    });
  }
}

// Define loadScript outside setTimeout so it's available when needed
function loadScript(src, isAsync = false) {
  const script = document.createElement("script");
  script.src = src;
  if (isAsync) script.async = true;
  script.crossOrigin = "*";
  document.body.appendChild(script);
}

// #region update year
function updateYear() {
  const yearSpan = document.getElementById("copyrightYr");
  if (yearSpan) {
    yearSpan.textContent = new Date().getFullYear();
  }
}

// #region mobile accordion
function setupMobileAccordion() {
  const isMobile = window.innerWidth <= 1024;
  const sidebarItems = document.querySelectorAll(".mega-sidebar ul li");
  const megaContentContainer = document.querySelector(".mega-content");

  sidebarItems.forEach((item) => {
    const targetId = item.getAttribute("data-target");
    if (!targetId) return;

    const targetContent = document.getElementById(targetId);

    if (isMobile) {
      // Move content inside the list item for Accordion view
      if (targetContent && targetContent.parentElement !== item) {
        // Determine insertion: append to item
        item.appendChild(targetContent);
        // Ensure it's hidden by default or handled by CSS class
        targetContent.classList.remove("active"); // content hidden by default in accordion
        item.classList.remove("active"); // header collapsed by default
      }
    } else {
      // Move content back to mega-content container for Desktop view
      if (
        targetContent &&
        targetContent.parentElement !== megaContentContainer
      ) {
        megaContentContainer.appendChild(targetContent);
        // Restore default active state for desktop: Data Recovery first
        if (item.getAttribute("data-target") === "data-recovery") {
          item.classList.add("active");
          targetContent.classList.add("active");
        } else {
          item.classList.remove("active");
          targetContent.classList.remove("active");
        }
      }
    }
  });
}


// #region View All FAQ Logic
document.addEventListener("click", function (e) {
  if (e.target.id === "viewAllFaq") {
    const extraItems = document.querySelector(".faq-extra-items");
    if (extraItems) {
      const isOpen = extraItems.classList.toggle("open");
      e.target.textContent = isOpen ? "Show Less" : "View All";
    }
  }
});
// #endregion


// #region Disclaimer Popup
(function () {
  // Skip if already accepted
  if (localStorage.getItem("ug_disclaimer_accepted") === "true") return;

  // Inject CSS
  const style = document.createElement("style");
  style.textContent = `
    .disclaimer-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.6);
      z-index: 99999;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      backdrop-filter: blur(3px);
    }
    .disclaimer-modal {
      background: #fff;
      border-radius: 14px;
      max-width: 620px;
      width: 100%;
      padding: 36px 32px 28px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      font-family: 'Inter', sans-serif;
      max-height: 90vh;
      overflow-y: auto;
    }
    .disclaimer-modal h2 {
      text-align: center;
      color: #4CAF50;
      font-size: 22px;
      font-weight: 700;
      margin: 0 0 20px;
    }
    .disclaimer-modal p {
      color: #444;
      font-size: 14px;
      line-height: 1.7;
      margin: 0 0 14px;
      text-align: justify;
    }
    .disclaimer-check {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 20px 0 18px;
      padding: 14px 16px;
      background: #f8f9fa;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
    }
    .disclaimer-check input[type="checkbox"] {
      width: 18px;
      height: 18px;
      cursor: pointer;
      accent-color: #4CAF50;
      flex-shrink: 0;
    }
    .disclaimer-check label {
      color: #333;
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      user-select: none;
    }
    .disclaimer-accept-btn {
      display: block;
      width: 100%;
      padding: 14px;
      background: #ccc;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: not-allowed;
      transition: background 0.3s, transform 0.15s;
      font-family: 'Inter', sans-serif;
      letter-spacing: 0.3px;
    }
    .disclaimer-accept-btn.enabled {
      background: #4CAF50;
      cursor: pointer;
    }
    .disclaimer-accept-btn.enabled:hover {
      background: #43a047;
      transform: translateY(-1px);
    }
    .disclaimer-accept-btn.enabled:active {
      transform: translateY(0);
    }
  `;
  document.head.appendChild(style);

  // Build HTML
  const overlay = document.createElement("div");
  overlay.className = "disclaimer-overlay";
  overlay.innerHTML = `
    <div class="disclaimer-modal">
      <h2>Disclaimer</h2>
      <p>This website is strictly for informational and educational purposes only. As per SEBI regulations, trading of unlisted shares is not permitted on online portals, and this site does not function as a trading or broking platform in any capacity.</p>
      <p>Users are advised to conduct their due diligence and consult with a SEBI-registered intermediary before making any investment decisions. The information presented here should not be construed as investment advice or a recommendation.</p>
      <p>Unlisted Gain is a shareholder of many private unlisted companies, and we deal in buying/selling unlisted shares via the offline process. This is strictly a private deal between the two consenting parties.</p>
      <div class="disclaimer-check">
        <input type="checkbox" id="disclaimerCheckbox">
        <label for="disclaimerCheckbox">I acknowledge and understand this disclaimer.</label>
      </div>
      <button class="disclaimer-accept-btn" id="disclaimerAcceptBtn" disabled>I Understand &amp; Accept</button>
    </div>
  `;
  document.body.appendChild(overlay);

  // Prevent scrolling
  document.body.style.overflow = "hidden";

  const checkbox = document.getElementById("disclaimerCheckbox");
  const acceptBtn = document.getElementById("disclaimerAcceptBtn");

  checkbox.addEventListener("change", function () {
    if (checkbox.checked) {
      acceptBtn.classList.add("enabled");
      acceptBtn.disabled = false;
    } else {
      acceptBtn.classList.remove("enabled");
      acceptBtn.disabled = true;
    }
  });

  acceptBtn.addEventListener("click", function () {
    if (!checkbox.checked) return;
    localStorage.setItem("ug_disclaimer_accepted", "true");
    overlay.style.opacity = "0";
    overlay.style.transition = "opacity 0.3s ease";
    setTimeout(function () {
      overlay.remove();
      document.body.style.overflow = "";
    }, 300);
  });
})();
// #endregion Disclaimer Popup