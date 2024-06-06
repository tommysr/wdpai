const togglePublish = (button, questId) => {
    let error = button.nextElementSibling;


    if (button.textContent.trim() == 'Unpublish') {
        fetch(`/unpublishQuest/${questId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        }).then(response => response.json())
            .then(data => {
                if (data.errors) {
                    error.textContent = data.errors[0];
                } else {
                    button.textContent = 'Publish';
                    error.textContent = 'Quest unpublished!';
                }
            });
    } else {
        fetch(`/publishQuest/${questId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        }).then(response => response.json())
            .then(data => {
                if (data.errors) {
                    error.textContent = data.errors[0];
                } else {
                    button.textContent = 'Unpublish';
                    error.textContent = 'Quest published!';
                }
            });
    }
};