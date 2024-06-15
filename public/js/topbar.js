const quests = document.querySelector(".cards");

function toggleDropdown() {
  const dropdown = document.getElementById("myDropdown");
  dropdown.classList.toggle("show");

  if (dropdown.classList.contains("show")) {
    addDropdownEventListeners();
  } else {
    removeDropdownEventListeners();
  }
}

function addDropdownEventListeners() {
  document
    .querySelector(".dropdown-content .top-rated")
    .addEventListener("click", handleTopRatedClick);
  document
    .querySelector(".dropdown-content .recommended")
    .addEventListener("click", handleRecommendedClick);
}

function removeDropdownEventListeners() {
  document
    .querySelector(".dropdown-content .top-rated")
    .removeEventListener("click", handleTopRatedClick);
  document
    .querySelector(".dropdown-content .recommended")
    .removeEventListener("click", handleRecommendedClick);
}

function handleTopRatedClick() {
  activateLink("top-rated");
  fetch("/showTopRatedQuests", {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      if (data.quests) {
        quests.innerHTML = "";
        data.quests.forEach((quest) => createQuest(quest));
        addListeners();
      }

      const h1 = document.querySelector("h1.main-text");

      if (data.quests.length == 0 && h1 == null) {
        quests.innerHTML = '<h1 class="main-text">No records found </h1>';
      }
    });
}

function handleRecommendedClick() {
  activateLink("recommended");
  fetch("/showRecommendedQuests", {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data.quests);
      if (data.quests) {
        quests.innerHTML = "";
        data.quests.forEach((quest) => createQuest(quest));
        addListeners();
      }
      const h1 = document.querySelector("h1.main-text");

      if (data.quests.length == 0 && h1 == null) {
        quests.innerHTML = '<h1 class="main-text">No records found </h1>';
      }
    });
}

function activateLink(type) {
  document
    .querySelectorAll(".nav-link")
    .forEach((link) => link.classList.remove("active"));
  document
    .querySelectorAll(`.${type}`)
    .forEach((link) => link.classList.add("active"));
}

function createQuest(quest) {
  const template = document.querySelector("#quest-template");
  const clone = template.content.cloneNode(true);

  const image = clone.querySelector(".card-image");
  image.src = '/public/uploads/' + quest.pictureUrl;

  const infos = clone.querySelectorAll(".info");
  const rating = document.createTextNode(quest.avgRating.toFixed(2));
  infos[0].appendChild(rating);

  const blockchain = document.createTextNode(quest.blockchain);
  infos[1].appendChild(blockchain);

  const requiredMinutes = document.createTextNode(quest.requiredMinutes);
  infos[2].appendChild(requiredMinutes);

  const expiryDate = document.createTextNode(quest.expiryDate);
  infos[3].appendChild(expiryDate);

  const mergedParticipants = document.createTextNode(
    quest.participantsCount + " / " + quest.participantsLimit
  );
  infos[4].appendChild(mergedParticipants);

  const poolAmount = document.createTextNode(quest.poolAmount);
  infos[5].appendChild(poolAmount);

  const aLink = clone.querySelector("a");
  aLink.href = "/showQuestWallets/" + quest.questId;

  const title = clone.querySelector(".title");
  title.textContent = quest.title;

  const description = clone.querySelector(".description");
  description.textContent = quest.description;

  quests.appendChild(clone);
}

function addListeners() {
  document.querySelectorAll(".show-more-btn").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var card = this.closest(".card");
      card.classList.toggle("expanded");
      if (card.classList.contains("expanded")) {
        this.textContent = "Show less";
      } else {
        this.textContent = "Show more";
      }
    });
  });
}

// Initial event listeners for inline menu
document
  .querySelector(".inline-menu .top-rated")
  .addEventListener("click", handleTopRatedClick);
document
  .querySelector(".inline-menu .recommended")
  .addEventListener("click", handleRecommendedClick);
