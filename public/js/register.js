document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const email = document.querySelector("input[name='email']");
  const username = document.querySelector("input[name='username']");
  const password = document.querySelector("input[name='password']");
  const confirmedPassword = document.querySelector(
    "input[name='confirmedPassword']"
  );
  const tosCheckbox = document.getElementById("tos-checkbox");
  const emailErrorMessage = document.querySelector("#email-error");
  const usernameErrorMessage = document.querySelector("#username-error");
  const passwordErrorMessage = document.querySelector("#password-error");
  const formErrorMessage = document.querySelector("#form-error");
  const confirmedPasswordErrorMessage =
    document.querySelector("#confirm-error");
  const tosErrorMessage = document.querySelector("#tos-error");

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

  const checkUsernameValidity = () => {
    if (username.validity.valid) {
      usernameErrorMessage.textContent = "";
    } else if (username.validity.valueMissing) {
      usernameErrorMessage.textContent = "You need to enter a username";
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

  const checkConfirmedPasswordValidity = () => {
    if (confirmedPassword.validity.valueMissing) {
      confirmedPasswordErrorMessage.textContent =
        "Please confirm your password";
    } else if (confirmedPassword.value !== password.value) {
      confirmedPasswordErrorMessage.textContent =
        "Confirmed password does not match the password";
    } else {
      confirmedPasswordErrorMessage.textContent = "";
      return true;
    }

    return false;
  };

  const checkTosValidity = () => {
    if (tosCheckbox.checked) {
      tosErrorMessage.textContent = "";
    } else {
      tosErrorMessage.textContent = "You must agree to the terms of service";
    }
  };

  email.addEventListener("blur", checkEmailValidity);
  username.addEventListener("blur", checkUsernameValidity);
  password.addEventListener("blur", checkPasswordValidity);
  confirmedPassword.addEventListener("blur", checkConfirmedPasswordValidity);
  tosCheckbox.addEventListener("blur", checkTosValidity);

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    checkEmailValidity();
    checkUsernameValidity();
    checkPasswordValidity();
    checkTosValidity();

    if (!form.checkValidity() && !checkConfirmedPasswordValidity()) {
      return;
    }

    fetch("/register", {
      method: "POST",
      body: new FormData(form),
    })
      .then((response) => {
        return response.json();
      })
      .then((data) => {
        if (data.errors) {
          formErrorMessage.textContent = data.errors[0];
        } else {
          window.location = "/login";
        }
      })
      .catch((error) => {
        formErrorMessage.textContent = "form error";
        console.error(error);
      });
  });
});
