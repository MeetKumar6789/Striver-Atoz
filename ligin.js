document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("loginForm");
  const successMsg = document.getElementById("loginSuccessMsg");
  form.addEventListener("submit", function (e) {
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    let isValid = true;
    successMsg.style.display = "none";
    const errors = form.querySelectorAll(".error-message");
    errors.forEach(err => err.remove());
    if (email.value.trim() === "") {
      showError(email, "Email is required.");
      isValid = false;
    } else if (!validateEmail(email.value.trim())) {
      showError(email, "Please enter a valid email.");
      isValid = false;
    }
    if (password.value.trim() === "") {
      showError(password, "Password is required.");
      isValid = false;
    } else if (password.value.trim().length < 6) {
      showError(password, "Password must be at least 6 characters.");
      isValid = false;
    }
   if (isValid) {
  form.submit(); 
}
  });
  function showError(input, message) {
    const error = document.createElement("div");
    error.className = "error-message";
    error.style.color = "red";
    error.style.fontSize = "13px";
    error.style.marginTop = "4px";
    error.textContent = message;
    input.parentNode.appendChild(error);
  }
  function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }
});
