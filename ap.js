document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  form.addEventListener("submit", function (e) {
    const fields = {
      "full_name": "Full Name is required.",
      "email": "Email is required.",
      "phone": "Phone Number is required.",
      "apply_for": "Apply For is required.",
      "room_type": "Room Type is required.",
      "floor": "Preferred Floor is required."
    };
    let isValid = true;
    form.querySelectorAll(".error-message, .success-message").forEach(el => el.remove());
    for (const name in fields) {
      const input = form.querySelector(`[name="${name}"]`);
      if (name === "room_type") {
        const radios = form.querySelectorAll(`[name="${name}"]`);
        const oneChecked = Array.from(radios).some(r => r.checked);
        if (!oneChecked) {
          showError(radios[radios.length - 1].parentElement, fields[name]);
          isValid = false;
        }
      } else {
        if (!input || input.value.trim() === "") {
          showError(input, fields[name]);
          isValid = false;
        }
      }
    }
    if (isValid) {
  form.submit(); 
}
  });
  function showError(element, message) {
    const error = document.createElement("div");
    error.className = "error-message";
    error.style.color = "red";
    error.style.fontSize = "13px";
    error.style.marginTop = "4px";
    error.textContent = message;
    element.parentNode.appendChild(error);
  }
function showSuccess(form) {
  const msg = document.createElement("div");
  msg.className = "success-message";
  msg.textContent = "You have successfully applied for NiwasGrugh!";
  msg.style.color = "green";
  msg.style.fontWeight = "bold";
  msg.style.fontSize = "16px";
  msg.style.marginBottom = "15px";
  msg.style.textAlign = "center";        
  msg.style.width = "100%";              
  form.insertBefore(msg, form.firstChild);
}
});
