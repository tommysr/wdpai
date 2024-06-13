
let newWalletForm = document.getElementById("add-wallet-form");
let error = document.getElementById("error");
let select = document.getElementById("walletSelect");

const validateWalletSelect = () => {
  console.log(select.value)
  if (select.value === "new") {
    error.innerText = "Choose wallet or create new one";
    return false
  } else {
    error.innerText = "";
    return true
  }
}

function enterQuest(event, questId) {
  event.preventDefault();
  if (!validateWalletSelect()) {
    return;
  }

  const formData = new FormData(event.target);

  fetch(`/enterQuest/${questId}`, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data)
      if (data.errors) {
        error.innerText = data.errors[0];
      } else {
        window.location.href = data.redirect;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      error.innerText = error;
    });
}

function addWallet(event, blockchain) {
  console.log(blockchain)
  event.preventDefault();

  const formData = new FormData(event.target);

  fetch(`/addWallet/${blockchain}`, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data)
      if (data.errors) {
        error.innerText = data.errors[0];
      } else {
        error.innerText = "";
        const walletId = data.walletId;
        const walletAddress = data.walletAddress;
        let option = document.createElement("option");
        option.value = walletId;
        option.text = walletAddress;
        select.add(option);
        select.value = walletId;
        newWalletForm.style.display = "none";
        handleWalletSelect(select);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      error.innerText = error;
    });
}

function handleWalletSelect(select) {
  let newWalletForm = document.getElementById("add-wallet-form");

  if (select.value === "new") {
    newWalletForm.style.display = "flex";
  } else {
    newWalletForm.style.display = "none";
  }
}


handleWalletSelect(select);
