let currentMathSolution = 0;

// Function to generate a new math question
function generateMathCaptcha() {
    const mathQuestionElem = document.getElementById("mathQuestion");
    const captchaInputElem = document.getElementById("captchaInput");
    
    if (mathQuestionElem && captchaInputElem) {
        const num1 = Math.floor(Math.random() * 10) + 1;
        const num2 = Math.floor(Math.random() * 10) + 1;
        currentMathSolution = num1 + num2;
        mathQuestionElem.innerText = `${num1} + ${num2}`;
        captchaInputElem.value = "";
    }
}

// Handle interaction logic when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize captcha as soon as DOM is ready
    generateMathCaptcha();

    const refreshBtn = document.getElementById("refreshCaptcha");
    const trendingForm = document.getElementById("trendingStocksForm");

    if (refreshBtn) {
        refreshBtn.addEventListener("click", generateMathCaptcha);
    }

    if (trendingForm) {
        trendingForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const captchaInput = document.getElementById("captchaInput");
            if (!captchaInput) return;

            const userAnswer = parseInt(captchaInput.value);

            // Client-side quick check
            if (userAnswer !== currentMathSolution) {
                alert("Math answer is incorrect. Please try again.");
                generateMathCaptcha();
                return;
            }

            const submitBtn = e.target.querySelector('.btn-submit');
            const originalBtnText = submitBtn ? submitBtn.innerText : "Submit";
            
            if (submitBtn) {
                submitBtn.innerText = "Processing...";
                submitBtn.disabled = true;
            }

            const formData = new FormData(this);
            // Add the math values for server-side verification
            formData.append("mathAnswer", userAnswer);
            formData.append("mathExpected", currentMathSolution);

            fetch("https://script.google.com/macros/s/AKfycbyw3jJFABe9KIPFmI0NHBurIxtQBeEcMzttKosQNpNmbP5d2sTLVcLfAzZdP7sm30s2/exec", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        alert("Success! Your interest has been recorded.");
                        this.reset();
                        generateMathCaptcha();
                        
                        // Close modal and show success toast if available
                        const modal = document.getElementById('ugModal');
                        if (modal) {
                            modal.style.display = 'none';
                        }
                        
                        const toast = document.getElementById('successToast');
                        if (toast) {
                            toast.classList.add('show');
                            setTimeout(() => toast.classList.remove('show'), 3000);
                        }
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Network error. Please try again.");
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.innerText = originalBtnText;
                        submitBtn.disabled = false;
                    }
                });
        });
    }
});

// Fallback to ensure captcha is initialized even if DOMContentLoaded already fired
if (document.readyState === "complete" || document.readyState === "interactive") {
    generateMathCaptcha();
}
