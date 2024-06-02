const questionsDiv = document.getElementById("questions");
const questions = document.querySelectorAll(".question");
const optionsDiv = document.querySelectorAll(".options");
const errorDiv = document.querySelector("#error");

let questionsOption = {};
let lastQuestionId = null;

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("questForm").addEventListener("submit", submitForm);
});

function removeQuestion(callDiv, questionId) {
  const questionDiv = callDiv.parentNode;

  const input = document.createElement("input");
  input.type = "hidden";
  input.value = "removed";
  input.name = `questions[${questionId}][flag]`;
  questionDiv.appendChild(input);

  questionDiv.style.display = "none";
}

function removeOption(callDiv, questionId, optionId) {
  const optionDiv = callDiv.parentNode;

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
  newQuestionDiv.classList = "question flex-column-center-center gap-1";

  newQuestionDiv.innerHTML = `
      <label for="questionText" class="input-description main-text">Question Text:</label>
      <textarea name="questions[${questionId}][text]" class="questionText main-text" cols="30" rows="10" required> </textarea>
      <input type="number" name="questions[${questionId}][score]" class="questionPoints" placeholder="points" required>
      <input type="hidden" name="questions[${questionId}][flag]" value="added">

      <div class="options"></div>
      <button type="button" class="addOption main-button">Add Option</button><br>
      <button type="button" class="removeQuestion secondary-button">Remove Question</button><br><br>
    `;
  questionsDiv.appendChild(newQuestionDiv);

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
      <input type="text" class="optionText" name="questions[${questionId}][options][${optionId}][text]" placeholder="new option">

      <input type="hidden" name="questions[${questionId}][options][${optionId}][flag]" value="added">

      <label class="option-container">
        <input type="checkbox" name="questions[${questionId}][options][${optionId}][isCorrect]"/>
        <span class="checkmark"></span>
      </label>
      <button type="button" class="removeOption"><i class="fa fa-times-circle"
        aria-hidden="true"></i></button><br>
    `;
  optionsDiv.appendChild(newOptionDiv);

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

function submitForm(event) {
  event.preventDefault();

  const formData = serializeForm(event.target);

  const path = window.location.pathname;
  let apiUrl = "";
  if (path.startsWith("/createQuest")) {
    apiUrl = "/createQuest";
  } else if (path.startsWith("/editQuest")) {
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
