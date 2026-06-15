// Open Modal on Hero Button Click
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("hero-cta")) {
    e.preventDefault();
    document.getElementById("ugModal").style.display = "flex";
    // generateCaptcha() is now handled in trending-stocks.js
    if (typeof generateMathCaptcha === 'function') {
      generateMathCaptcha();
    }
  }

  // Close Modal
  if (
    e.target.id === "closeModal" ||
    e.target.classList.contains("stock-modal-overlay")
  ) {
    document.getElementById("ugModal").style.display = "none";
  }
});

function showToast() {
  const toast = document.getElementById("successToast");
  if (toast) {
    toast.style.display = "block";
    // Reset form
    const form = document.getElementById("trendingStocksForm");
    if (form) form.reset();

    setTimeout(() => {
      toast.style.display = "none";
    }, 4000);
  }
}
