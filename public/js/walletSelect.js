function handleWalletSelect(select) {
  let newWalletInput = document.getElementById('newWalletInput');


  console.log(select.value);
  if (select.value === 'new') {
      newWalletInput.style.display = 'block';
  } else {
      newWalletInput.style.display = 'none';
  }
}