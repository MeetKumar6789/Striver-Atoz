document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const messageContainer = document.createElement("div");
  messageContainer.id = "formMessage";
  messageContainer.style.textAlign = "center";
  messageContainer.style.fontWeight = "bold";
  messageContainer.style.marginBottom = "15px";
  form.insertBefore(messageContainer, form.firstChild);
  const fields = {
    first_name: 'First name is required.',
    last_name: 'Last name is required.',
    email: 'Email is required.',
    phone: 'Phone number is required.',
    address: 'Address is required.',
    gender: 'Gender is required.',
    dob: 'Date of birth is required.',
    branch: 'Branch is required.',
    semester: 'Semester is required.',
    profile: 'Profile photo is required.'
  };
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;
    document.querySelectorAll(".error-message").forEach(el => el.remove());
    messageContainer.textContent = "";

    for (let name in fields) {
      const input = form.querySelector(`[name="${name}"]`);
      if (!input) continue;

      const value = name === "profile" ? input.files.length : input.value.trim();
      if (!value) {
        showError(input, fields[name]);
        isValid = false;
      } else if (name === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim())) {
        showError(input, "Please enter a valid email address.");
        isValid = false;
      } else if (name === "phone" && !/^\d{10}$/.test(input.value.trim())) {
        showError(input, "Phone number must be 10 digits.");
        isValid = false;
      } else if ((name === "first_name" || name === "last_name") && !/^[A-Za-z\s]+$/.test(input.value.trim())) {
        showError(input, `${name.replace("_", " ")} should contain only letters.`);
        isValid = false;
      } else if (name === "address" && input.value.trim().length < 5) {
        showError(input, "Address is too short.");
        isValid = false;
      }
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
    error.style.marginTop = "5px";
    error.textContent = message;
    input.parentNode.appendChild(error);
  }

});

