document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const email = document.querySelector("input[name='email']");
  const password = document.querySelector("input[name='password']");
  const formErrorMessage = document.querySelector("#form-error");
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
    event.preventDefault();

    checkEmailValidity();
    checkPasswordValidity();
    if (!form.checkValidity()) {
      return;
    }

    const formData = new FormData(form);

    fetch("/login", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        return response.json();
      })
      .then((data) => {
        if (data.errors) {
          formErrorMessage.textContent = data.errors.join("\n");
        } else {
          window.location.href = data.redirectUrl;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        formErrorMessage.textContent = error;
      });
  });
});
