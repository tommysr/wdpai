document.addEventListener("DOMContentLoaded", function () {
  let newWalletForm = document.getElementById("add-wallet-form");
  let error = document.getElementById("error");
  let select = document.getElementById("walletSelect");

  newWalletForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(newWalletForm);

    const path = window.location.pathname;
    let apiUrl = "";
    if (path.startsWith("/showQuestWallets")) {
      const parts = path.split("/");
      const questId = parts[2];
      apiUrl = `/addWallet/${questId}`;
    }

    const action = newWalletForm.getAttribute("action");

    fetch(action, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.errors) {
          error.innerText = data.errors[0];
        } else {
          const walletId = data.walletId;
          const walletAddress = data.walletAddress;
          let option = document.createElement("option");
          option.value = walletId;
          option.text = walletAddress;
          select.add(option);
          select.value = walletId;
          newWalletForm.style.display = "none";
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        error.innerText = error;
      });
  });

  handleWalletSelect(select);
});

function handleWalletSelect(select) {
  let newWalletForm = document.getElementById("add-wallet-form");

  console.log(select.value)
  if (select.value === "new") {
    newWalletForm.style.display = "block";
  } else {
    newWalletForm.style.display = "none";
  }
}
