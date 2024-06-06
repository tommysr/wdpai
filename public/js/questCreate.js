
const form = document.getElementById("questForm");
const title = document.getElementById("quizTitle");
const description = document.getElementById("description");
const blockchain = document.querySelector("input[name='blockchain']");
const expiryDate = document.querySelector("input[name='expiryDate']");
const payoutDate = document.querySelector("input[name='payoutDate']");
const minutesRequired = document.querySelector("input[name='minutesRequired']");
const participantsLimit = document.querySelector("input[name='participantsLimit']");
const poolAmount = document.querySelector("input[name='poolAmount']");
const token = document.querySelector("input[name='token']");
const error = document.getElementById("error");
let questionsTexts = document.querySelectorAll(".questionText");
let questionsPoints = document.querySelectorAll(".questionPoints");
let optionsText = document.querySelectorAll(".optionText");

console.log(optionsText);

const checkOptionTextValidity = (optionT) => {
  const optionText = optionT.target;
  console.log(optionText.value, optionText.validity);
  if (optionText.validity.valueMissing) {
    error.textContent = "You need to enter an option";
  } else if (optionText.validity.tooShort) {
    error.textContent = "Option must be at least 5 characters";
  } else if (optionText.validity.tooLong) {
    error.textContent = "Option must be at most 50 characters";
  } else {
    error.textContent = "";
  }
}


const checkQuestionTestsValidity = (questionT) => {
  const questionText = questionT.target;
  console.log(questionText.value, questionText.validity);
  if (questionText.validity.valueMissing) {
    error.textContent = "You need to enter a question";
  } else if (questionText.validity.tooShort) {
    error.textContent = "Question must be at least 5 characters";
  } else if (questionText.validity.tooLong) {
    error.textContent = "Question must be at most 80 characters";
  } else {
    error.textContent = "";
  }
}

const checkQuestionPointsValidity = (questionP) => {
  const questionPoints = questionP.target;
  console.log(questionPoints.value, questionPoints.validity);
  if (questionPoints.validity.valueMissing) {
    error.textContent = "You need to enter the points";
  } else if (questionPoints.value != parseInt(questionPoints.value)) {
    error.textContent = "Points must be a number";
  } else if (questionPoints.validity.rangeUnderflow) {
    error.textContent = "Points must be at least 1";
  } else if (questionPoints.validity.rangeOverflow) {
    error.textContent = "Points must be at most 100";
  } else {
    error.textContent = "";
  }
}

for (let i = 0; i < questionsTexts.length; i++) {
  console.log(questionsTexts[i])
  questionsTexts[i].addEventListener("blur", checkQuestionTestsValidity.bind((questionsTexts[i])));
}

for (let i = 0; i < questionsPoints.length; i++) {
  console.log(questionsPoints[i])
  questionsPoints[i].addEventListener("blur", checkQuestionPointsValidity.bind((questionsPoints[i])));
}

for (let i = 0; i < optionsText.length; i++) {
  console.log(optionsText[i])
  optionsText[i].addEventListener("blur", checkOptionTextValidity.bind((optionsText[i])));
}

const checkTitleValidity = () => {
  console.log(title.value.length)
  if (title.validity.valueMissing) {
    error.textContent = "You need to enter a title";
  } else if (title.validity.tooShort) {
    error.textContent = "Title must be at least 5 characters";
  } else if (title.validity.tooLong) {
    error.textContent = "Title must be at most 50 characters";
  } else {
    error.textContent = "";
    return true;
  }
};

title.addEventListener("blur", checkTitleValidity);

const checkDescriptionValidity = () => {
  if (description.validity.valueMissing) {
    error.textContent = "You need to enter a description";
  } else if (description.validity.tooShort) {
    error.textContent = "Description must be at least 20 characters";
  } else if (description.validity.tooLong) {
    error.textContent = "Description must be at most 300 characters";
  } else {
    error.textContent = "";
    return true;
  }
}

description.addEventListener("blur", checkDescriptionValidity);

const checkBlockchainValidity = () => {
  if (blockchain.validity.valueMissing) {
    error.textContent = "You need to enter a wallet";
  } else if (blockchain.validity.tooShort) {
    error.textContent = "Blockchain must be at least 3 characters";
  } else if (blockchain.validity.tooLong) {
    error.textContent = "Blockchain must be at most 50 characters";
  } else {
    error.textContent = "";
    return true;
  }

  return false;
}

blockchain.addEventListener("blur", checkBlockchainValidity);

const checkExpiryDateValidity = () => {
  if (expiryDate.validity.valueMissing) {
    error.textContent = "You need to enter an expiry date";
  } else if (expiryDate.value <= new Date().toISOString().split('T')[0]) {
    error.textContent = "Expiry date must be in the future";
  } else {
    error.textContent = "";
    return true;
  }

  return false;
}

expiryDate.addEventListener("blur", checkExpiryDateValidity);

const checkPayoutDateValidity = () => {
  if (payoutDate.validity.valueMissing) {
    error.textContent = "You need to enter a payout date";
  } else if (payoutDate.value <= new Date().toISOString().split('T')[0]) {
    error.textContent = "Payout date must be in the future";
  } else {
    error.textContent = "";
    return true;
  }

  return false;
}

payoutDate.addEventListener("blur", checkPayoutDateValidity);

const checkMinutesRequiredValidity = () => {
  console.log(minutesRequired.value)
  if (minutesRequired.validity.valueMissing) {
    error.textContent = "You need to enter the minutes required";
  } else if (minutesRequired.value != parseInt(minutesRequired.value)) {
    error.textContent = "Minutes must be a number";
  } else if (minutesRequired.validity.rangeUnderflow) {
    error.textContent = "Minutes must be at least 1";
  } else if (minutesRequired.validity.rangeOverflow) {
    error.textContent = "Minutes must be at most 120";
  } else {
    error.textContent = "";
    return true;
  }

  return false;
}

minutesRequired.addEventListener("blur", checkMinutesRequiredValidity);

const checkParticipantsLimitValidity = () => {
  if (participantsLimit.validity.valueMissing) {
    error.textContent = "You need to enter the participants limit";
  } else if (participantsLimit.value != parseInt(participantsLimit.value)) {
    error.textContent = "Participants limit must be a number";
  } else if (participantsLimit.validity.rangeUnderflow) {
    error.textContent = "Participants limit must be at least 20";
  } else if (participantsLimit.validity.rangeOverflow) {
    error.textContent = "Participants limit must be at most 1000";
  } else {
    error.textContent = "";
    return true;
  }


  return false;
}

participantsLimit.addEventListener("blur", checkParticipantsLimitValidity);

const checkPoolAmountValidity = () => {
  const poolAmountValue = poolAmount.value.trim();

  if (poolAmountValue === '') {
    error.textContent = "You need to enter the pool amount";
  } else if (!/^\d*\.?\d+$/.test(poolAmountValue)) {
    error.textContent = "Pool amount must be a valid number";
  } else if (parseFloat(poolAmountValue) <= 0) {
    error.textContent = "Pool amount must be greater than 0";
  } else {
    error.textContent = "";
    return true;
  }

  return false;
}

poolAmount.addEventListener("blur", checkPoolAmountValidity);

const checkTokenValidity = () => {
  if (token.validity.valueMissing) {
    error.textContent = "You need to enter the token";
  } else if (token.validity.tooShort) {
    error.textContent = "Token must be at least 3 characters";
  } else if (token.validity.tooLong) {
    error.textContent = "Token must be at most 50 characters";
  } else {
    error.textContent = "";
    return true;
  }

  return false;
}

token.addEventListener("blur", checkTokenValidity);


const questionsDiv = document.querySelector(".cards");
const questions = document.querySelectorAll(".container-card");
const optionsDiv = document.querySelectorAll(".grid-3");
const errorDiv = document.querySelector("#error");

let questionsOption = {};
let lastQuestionId = null;

function removeQuestion(callDiv, questionId) {
  const questionDiv = callDiv.parentNode.parentNode;

  const input = document.createElement("input");
  input.type = "hidden";
  input.value = "removed";
  input.name = `questions[${questionId}][flag]`;
  questionDiv.appendChild(input);

  questionDiv.style.display = "none";
}

function removeOption(callDiv, questionId, optionId) {
  const optionDiv = callDiv.parentNode.parentNode;

  const input = document.createElement("input");
  input.type = "hidden";
  input.value = "removed";
  input.name = `questions[${questionId}][options][${optionId}][flag]`;
  optionDiv.appendChild(input);

  optionDiv.style.display = "none";
}

function addQuestion(questionId) {
  console.log(questionId);

  const newQuestionDiv = document.createElement("div");
  newQuestionDiv.classList = "card";

  newQuestionDiv.innerHTML = `
      <div class="container-card bg-green-box question flex-column-center-center gap-1">
        <textarea name="questions[${questionId}][text]" class="questionText main-text" cols="30" rows="10" placeholder="question text" minlength="5" maxlength="80" required> </textarea>
        <div class="grid-2">
          <label for="questionPoints" class="input-description main-text center">Points:</label>
          <input class="points questionPoints" type="number" name="questions[${questionId}][score]" min="1" max="100"
            placeholder="points" required>
        </div>
        <input type="hidden" name="questions[${questionId}][flag]" value="added">

        <div class="options flex-column-center-center gap-1"></div>

        <button type="button" class="addOption main-button w-50">Add Option</button>
        <button type="button" class="removeQuestion secondary-button w-50">Remove Question</button>
      </div>
    `;
  questionsDiv.appendChild(newQuestionDiv);

  const newQuestionText = newQuestionDiv.querySelector("div > textarea");
  console.log(newQuestionText)
  newQuestionText.addEventListener("blur", checkQuestionTestsValidity.bind(newQuestionText));
  const newQuestionPoints = newQuestionDiv.querySelector("div > input");
  console.log(newQuestionPoints);
  newQuestionPoints.addEventListener("blur", checkQuestionPointsValidity.bind(newQuestionPoints));

  newQuestionDiv
    .querySelector(".addOption")
    .addEventListener("click", function () {
      if (questionsOption[questionId] != undefined) {
        questionsOption[questionId] += 1;
      } else {
        questionsOption[questionId] = 0;
      }

      console.log(questionsOption[questionId])
      addOption(newQuestionDiv, questionId, questionsOption[questionId]);
    });

  newQuestionDiv
    .querySelector(".removeQuestion")
    .addEventListener("click", function () {
      questionsDiv.removeChild(newQuestionDiv);
    });
}

function addQuestionRaw(questionId) {
  if (lastQuestionId != null) {
    lastQuestionId += 1;
  } else {
    lastQuestionId = questionId;
  }

  addQuestion(lastQuestionId);
}

function addOptionRaw(callDiv, questionId, optionId) {
  const optionsDiv = callDiv.parentNode;

  if (questionsOption[questionId] != undefined) {
    questionsOption[questionId] += 1;
  } else {
    questionsOption[questionId] = optionId;
  }

  questionsOption[questionId]

  addOption(optionsDiv, questionId, questionsOption[questionId]);
}

function addOption(questionDiv, questionId, optionId) {
  const optionsDiv = questionDiv.querySelector(".options");

  const newOptionDiv = document.createElement("div");
  newOptionDiv.classList = "option";

  newOptionDiv.innerHTML = `
      <div class="grid-3 w-100">
        <div>
          <input type="text" class="points optionText" name="questions[${questionId}][options][${optionId}][text]" placeholder="option" minlength="5" maxlength="50">
          <input type="hidden" name="questions[${questionId}][options][${optionId}][flag]" value="added">
        </div>

        <div class="option-container">
          <input type="checkbox" name="questions[${questionId}][options][${optionId}][isCorrect]"/>
          <span class="checkmark"></span>
        </div>

        <div class="flex-row-center-center">
          <button type="button" class="removeOption show-more-btn bg-green-box">remove</button>
        </div>
      </div>
    `;
  optionsDiv.appendChild(newOptionDiv);

  const newOptionText = newOptionDiv.querySelector("div > input[type='text']");
  console.log(newOptionText)
  newOptionText.addEventListener("blur", checkOptionTextValidity.bind(newOptionText));


  newOptionDiv
    .querySelector(".removeOption")
    .addEventListener("click", function () {
      optionsDiv.removeChild(newOptionDiv);
    });
}

function update(data, keys, value) {
  if (keys.length === 0) {
    // Leaf node
    return value;
  }

  let key = keys.shift();
  if (!key) {
    data = data || [];
    if (Array.isArray(data)) {
      key = data.length;
    }
  }

  // Try converting key to a numeric value
  let index = +key;
  if (!isNaN(index)) {
    // We have a numeric index, make data a numeric array
    // This will not work if this is a associative array
    // with numeric keys
    data = data || [];
    key = index;
  }

  // If none of the above matched, we have an associative array
  data = data || {};

  let val = update(data[key], keys, value);
  data[key] = val;

  return data;
}

//credits
// https://stackoverflow.com/questions/41431322/how-to-convert-formdata-html5-object-to-json
// author: Joyce Babu
function serializeForm(form) {
  return Array.from(new FormData(form).entries()).reduce(
    (data, [field, value]) => {
      let [_, prefix, keys] = field.match(/^([^\[]+)((?:\[[^\]]*\])*)/);

      if (keys) {
        keys = Array.from(keys.matchAll(/\[([^\]]*)\]/g), (m) => m[1]);
        value = update(data[prefix], keys, value);
      }
      data[prefix] = value;
      return data;
    },
    {}
  );
}

const validateFileInput = () => {
  const questThumbnail = document.getElementById('questThumbnail');

  if (!questThumbnail.value) {
    error.textContent = 'You need to upload a file';
  } else {
    error.textContent = '';
    return true;
  }

  const fileInput = document.getElementById('fileInput');
  const file = fileInput.files[0];

  if (file) {
    error.textContent = 'Click the upload button to upload the file';
    return false;
  }
}


function submitForm(event) {
  event.preventDefault();


  let valid = true;

  if (
    !checkTitleValidity() ||
    !checkDescriptionValidity() ||
    !checkBlockchainValidity() ||
    !checkExpiryDateValidity() ||
    !checkPayoutDateValidity() ||
    !checkMinutesRequiredValidity() ||
    !checkParticipantsLimitValidity() ||
    !checkPoolAmountValidity() ||
    !checkTokenValidity() ||
    !form.checkValidity() ||
    !validateFileInput()) {
    return;
  }

  // valid &= checkTitleValidity();
  // valid &= checkDescriptionValidity();
  // valid &= checkBlockchainValidity();
  // valid &= checkExpiryDateValidity();
  // valid &= checkPayoutDateValidity();
  // valid &= checkMinutesRequiredValidity();
  // valid &= checkParticipantsLimitValidity();
  // valid &= checkPoolAmountValidity();
  // valid &= checkTokenValidity();
  // valid &= form.checkValidity();
  // valid &= validateFileInput();


  // if (!valid) {
  //   return;
  // }

  const formData = serializeForm(event.target);

  const path = window.location.pathname;
  let apiUrl = "";
  if (path.startsWith("/showCreateQuest")) {
    apiUrl = "/createQuest";
  } else if (path.startsWith("/showEditQuest")) {
    const parts = path.split("/");
    const questId = parts[2];
    apiUrl = `/editQuest/${questId}`;
  }
  fetch(apiUrl, {
    method: "POST",
    body: JSON.stringify(formData),
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.errors) {
        errorDiv.textContent = data.errors[0];
      } else if (data.redirectUrl) {
        window.location.href = data.redirectUrl;
      }
    })
    .catch((error) => {
      errorDiv.textContent = data.errors;
      console.error("Error:", error);
    });
}


const fileInput = document.getElementById('fileInput');
const preview = document.getElementById('preview');

fileInput.addEventListener('change', () => {
  const file = fileInput.files[0];
  const reader = new FileReader();

  reader.onload = (e) => {
    preview.src = e.target.result;
    preview.style.display = 'block';
  };

  if (file) {
    reader.readAsDataURL(file);
  }
});


function uploadFile() {
  const formData = new FormData();
  formData.append('file', fileInput.files[0]);

  fetch('/uploadQuestPicture', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.name) {
        const questThumbnail = document.getElementById('questThumbnail');
        error.textContent = '';
        questThumbnail.value = data.name;
      } else if (data.errors) {
        error.textContent = data.errors[0];
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
}
