let errorDiv = document.querySelector(".error-message");

document.getElementById('change-password').addEventListener('click', function (event) {
    event.preventDefault();
    document.getElementById('confirmationDialog').showModal();
});

function closeModal(event) {
    event.preventDefault();
    document.getElementById('confirmationDialog').close();
}


function handleFormSubmit(event) {
    console.log('handleFormSubmit')
    event.preventDefault();
    let password = document.getElementById('new-password').value;
    let confirmPassword = document.getElementById('confirm-password').value;
    let oldPassword = document.getElementById('current-password').value;

    if (password === '' || confirmPassword === '' || oldPassword === '') {
        errorDiv.textContent = 'All fields are required';
        return;
    }

    if (password.length < 8 || confirmPassword.length < 8 || oldPassword.length < 8) {
        errorDiv.textContent = 'Password must be at least 8 characters';
        return;
    }

    if (password != confirmPassword) {
        errorDiv.textContent = 'Passwords do not match';
        return;
    }

    const form = document.querySelector('.password-form');

    if (!form.checkValidity()) {
        errorDiv.textContent = 'Invalid form data';
        return;
    }

    const formData = new FormData(form);

    fetch('/changePassword', {
        method: 'POST',
        body: formData
    }).then(response => response.json()).then(data => {
        if (data.errors) {
            errorDiv.textContent = data.errors[0];
        } else {
            document.getElementById('confirmationDialog').close();
        }
    }).catch(error => {
        console.error('Error:', error);
        errorDiv.textContent = 'An error occurred';
    });
}

function handleClick(event) {
    event.preventDefault();
    handleFormSubmit(event);
}
