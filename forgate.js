
  document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const emailInput = document.getElementById("email");

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const oldError = document.querySelector(".error-message");
      const oldSuccess = document.getElementById("success-message");
      if (oldError) oldError.remove();
      if (oldSuccess) oldSuccess.remove();

      const email = emailInput.value.trim();
      let isValid = true;
      if (email === "") {
        showError("Email is required.");
        isValid = false;
      } else if (!validateEmail(email)) {
        showError("Please enter a valid email address.");
        isValid = false;
      }
      if (isValid) {
        showSuccess("✅ Reset link sent successfully!");
        form.reset(); 
      }
    });
    function validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    }
    function showError(message) {
      const errorDiv = document.createElement("div");
      errorDiv.className = "error-message";
      errorDiv.style.color = "red";
      errorDiv.style.fontSize = "13px";
      errorDiv.style.marginTop = "-15px";
      errorDiv.style.marginBottom = "15px";
      errorDiv.textContent = message;
      emailInput.insertAdjacentElement("afterend", errorDiv);
    }
    function showSuccess(message) {
      const successDiv = document.createElement("div");
      successDiv.id = "success-message";
      successDiv.style.backgroundColor = "#d4edda";
      successDiv.style.color = "#155724";
      successDiv.style.border = "1px solid #c3e6cb";
      successDiv.style.padding = "10px";
      successDiv.style.borderRadius = "6px";
      successDiv.style.marginBottom = "15px";
      successDiv.style.textAlign = "center";
      successDiv.textContent = message;
      const formBox = document.querySelector(".form-box");
      formBox.insertBefore(successDiv, formBox.firstChild);
    }
  });

