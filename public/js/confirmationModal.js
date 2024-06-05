let errorDiv = document.querySelector(".error-message");

document.getElementById('back-link').addEventListener('click', function (event) {
    event.preventDefault();
    document.getElementById('confirmationDialog').showModal();
});

document.getElementById('confirmationDialog').addEventListener('close', function () {
    if (document.getElementById('confirmationDialog').returnValue === 'yes') {
        fetch('/abandonQuest', {
            method: 'POST',
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.errors) {
                    errorDiv.textContent = data.errors;
                } else {
                    window.location.href = '/showQuests';
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
    }
});