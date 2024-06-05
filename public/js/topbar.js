const top_rated = document.querySelector('.top-rated');
const recommended = document.querySelector('.recommended');
const quests = document.querySelector('.cards');

top_rated.addEventListener('click', () => {
    top_rated.classList.add('active');
    recommended.classList.remove('active');

    fetch('/showTopRatedQuests', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.quests) {
                quests.innerHTML = '';
                data.quests.forEach(quest => createQuest(quest));
                addListeners();
            }
        })
});

recommended.addEventListener('click', () => {
    recommended.classList.add('active');
    top_rated.classList.remove('active');

    fetch('/showRecommendedQuests', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.quests) {
                quests.innerHTML = '';
                data.quests.forEach(quest => createQuest(quest));
                addListeners();
            }
        })
});



function createQuest(quest) {
    const template = document.querySelector("#quest-template");
    const clone = template.content.cloneNode(true);

    const infos = clone.querySelectorAll('.info');
    const rating = document.createTextNode(quest.avgRating);
    infos[0].appendChild(rating);

    const blockchain = document.createTextNode(quest.blockchain);
    infos[1].appendChild(blockchain);

    const requiredMinutes = document.createTextNode(quest.requiredMinutes);
    infos[2].appendChild(requiredMinutes);

    const expiryDate = document.createTextNode(quest.expiryDate);
    infos[3].appendChild(expiryDate);

    const mergedParticipants = document.createTextNode(quest.participantsCount + ' / ' + quest.participantsLimit);
    infos[4].appendChild(mergedParticipants);

    const poolAmount = document.createTextNode(quest.poolAmount);
    infos[5].appendChild(poolAmount);

    const aLink = clone.querySelector('a');
    aLink.href = '/showQuestWallets/' + quest.questId;

    const title = clone.querySelector('.title');
    title.textContent = quest.title;

    const description = clone.querySelector('.description');
    description.textContent = quest.description;

    quests.appendChild(clone);
}

function addListeners() {
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

}