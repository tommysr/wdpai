document.querySelectorAll('.show-more-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var card = this.closest('.card');
        card.classList.toggle('expanded');
        if (card.classList.contains('expanded')) {
            this.textContent = 'Show less';
        } else {
            this.textContent = 'Show more';
        }
    });
});
