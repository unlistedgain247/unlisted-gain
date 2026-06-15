document.getElementById("ugContactForm").addEventListener("submit", function (e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById("contactSubmitBtn");
    const originalBtnText = submitBtn.innerText;
    submitBtn.innerText = "Sending...";
    submitBtn.disabled = true;

    grecaptcha.ready(function () {
        grecaptcha.execute("6LfWa8gsAAAAAGvB7RY1gDct2Mw5VV1ueiXeKa0E", { action: "submit" })
            .then(function (token) {
                const formData = new FormData();
                formData.append("fullName", document.getElementById("fullName").value.trim());
                formData.append("emailAddr", document.getElementById("emailAddr").value.trim());
                formData.append("phoneNum", document.getElementById("phoneNum").value.trim());
                formData.append("subject", document.getElementById("subject").value);
                formData.append("message", document.getElementById("message").value.trim());
                formData.append("g-recaptcha-response", token);

                fetch("https://script.google.com/macros/s/AKfycbyCu3DY6PTjo0wRHtOHWwEDPHeMXhinq5no27utG6dymlYbTJyxz7rezVZHJzQ7So_o2w/exec", {
                    method: "POST",
                    body: formData,
                })
                .then((res) => res.json())
                .then((data) => {
                    if (data.status === "success") {
                        // Hide form and show success message
                        document.getElementById("ugContactForm").style.display = "none";
                        document.getElementById("formSuccessMessage").style.display = "block";
                    } else {
                        alert("Error: " + data.message);
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalBtnText;
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Server error. Please try again later.");
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalBtnText;
                });
            });
    });
});

// Reset button functionality
document.getElementById("resetFormBtn").addEventListener("click", function() {
    document.getElementById("ugContactForm").reset();
    document.getElementById("ugContactForm").style.display = "block";
    document.getElementById("formSuccessMessage").style.display = "none";
    document.getElementById("contactSubmitBtn").innerText = "Send Message";
    document.getElementById("contactSubmitBtn").disabled = false;
});
