function handleWalletSelect(select) {
  let newWalletInput = document.getElementById('newWalletInput');

  if (select.value === 'new') {
      newWalletInput.style.display = 'block';
  } else {
      newWalletInput.style.display = 'none';
  }
}