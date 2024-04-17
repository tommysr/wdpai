document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const email = document.querySelector("input[name='email']");
  const password = document.querySelector("input[name='password']");

  const emailErrorMessage = document.querySelector("#email-error");
  const passwordErrorMessage = document.querySelector("#password-error");

  const checkEmailValidity = () => {
    if (email.validity.valid) {
      emailErrorMessage.textContent = "";
    } else if (email.validity.typeMismatch) {
      emailErrorMessage.textContent =
        "Entered value needs to be an email address.";
    } else if (email.validity.valueMissing) {
      emailErrorMessage.textContent = "You need to enter an email address";
    }
  };

  const checkPasswordValidity = () => {
    if (password.validity.valid) {
      passwordErrorMessage.textContent = "";
    } else if (password.validity.tooShort || password.validity.valueMissing) {
      passwordErrorMessage.textContent =
        "Password must be at least 8 characters long";
    }
  };

  email.addEventListener("blur", checkEmailValidity);
  password.addEventListener("blur", checkPasswordValidity);

  form.addEventListener("submit", (event) => {
    checkEmailValidity();
    checkPasswordValidity();
    if (!form.checkValidity()) {
      event.preventDefault();
    }
  });
});
