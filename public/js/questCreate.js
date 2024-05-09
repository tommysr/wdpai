const minQuestionId = 0;
const maxQuestionId = 200;

const minOptionId = 0;
const maxOptionId = 400;

let currentQuestionId = minQuestionId;
let currentOptionId = minOptionId;

const questionsDiv = document.getElementById("questions");
const questions = document.querySelectorAll(".question");
const optionsDiv = document.querySelectorAll(".options");

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("addQuestion").addEventListener("click", function () {
    addQuestion();
  });

  questions.forEach((q) => {
    q.querySelector(".removeQuestion").addEventListener("click", function () {
      questionsDiv.removeChild(q);
    });
  });

  optionsDiv.forEach((d) => {
    const options = d.querySelectorAll(".option");

    options.forEach((o) => {
      o.querySelector(".removeOption").addEventListener("click", function () {
        d.removeChild(o);
      });
    });
  });
});

function addQuestion() {
  if (currentQuestionId <= maxQuestionId) {
    const newQuestionDiv = document.createElement("div");
    newQuestionDiv.classList = "question flex-column-center-center gap-1";

    newQuestionDiv.innerHTML = `
      <label for="questionText" class="input-description main-text">Question Text:</label>
      <textarea name="questions[${currentQuestionId}][text]" class="questionText main-text" cols="30" rows="10" required> </textarea>

      <div class="options"></div>
      <button type="button" class="addOption main-button">Add Option</button><br>
      <button type="button" class="removeQuestion secondary-button">Remove Question</button><br><br>
    `;
    questionsDiv.appendChild(newQuestionDiv);

    newQuestionDiv
      .querySelector(".addOption")
      .addEventListener("click", function () {
        addOption(newQuestionDiv, currentQuestionId);
      });

    newQuestionDiv
      .querySelector(".removeQuestion")
      .addEventListener("click", function () {
        questionsDiv.removeChild(newQuestionDiv);
      });

    currentQuestionId++;
  } else {
    console.error("Maximum limit for question IDs exceeded.");
  }
}

function addOptionRaw(callDiv, questionId) {
  const questionDiv = callDiv.parentNode;

  addOption(questionDiv, questionId);
}

function addOption(questionDiv, questionId) {
  const optionsDiv = questionDiv.querySelector(".options");

  if (currentOptionId <= maxOptionId) {
    const newOptionDiv = document.createElement("div");
    newOptionDiv.classList = "option";

    newOptionDiv.innerHTML = `
      <input type="text" class="optionText" name="options[${questionId}][${currentOptionId}][text]" placeholder="new option">

      <label class="option-container">
        <input type="checkbox" name="options[${questionId}][${currentOptionId}][isCorrect]"/>
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

    currentOptionId++;
  } else {
    console.error("Maximum limit for option IDs exceeded.");
  }
}
