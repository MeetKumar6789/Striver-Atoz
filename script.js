document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const messageContainer = document.createElement("div");
  messageContainer.id = "formMessage";
  messageContainer.style.textAlign = "center";
  messageContainer.style.fontWeight = "bold";
  messageContainer.style.marginBottom = "15px";
  form.insertBefore(messageContainer, form.firstChild);
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const fullName = document.getElementById("fullname").value.trim();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    document.querySelectorAll(".error-message").forEach(el => el.remove());
    messageContainer.textContent = "";
    let isValid = true;
    function showError(inputId, message) {
      const input = document.getElementById(inputId);
      const error = document.createElement("div");
      error.className = "error-message";
      error.style.color = "red";
      error.style.fontSize = "13px";
      error.style.marginTop = "5px";
      error.textContent = message;
      input.parentNode.appendChild(error);
      isValid = false;
    }
    if (!/^[A-Za-z\s]+$/.test(fullName)) {
      showError("fullname", "Full name should contain only letters.");
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showError("email", "Please enter a valid email address.");
    }
    if (!/^\d{10}$/.test(phone)) {
      showError("phone", "Phone number must be 10 digits.");
    }
    if (password.length < 6) {
      showError("password", "Password must be at least 6 characters long.");
    }
    if (password !== confirmPassword) {
      showError("confirm_password", "Passwords do not match.");
    }
   if (isValid) {
  form.submit(); 
}
  });
});
