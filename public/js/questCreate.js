document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("addQuestion").addEventListener("click", function () {
    addQuestion();
  });

  const questionsDiv = document.getElementById("questions");
  const questions = document.querySelectorAll(".question");
  const optionsDiv = document.querySelectorAll(".options");

  questions.forEach((q) => {
    q.querySelector(".removeQuestion").addEventListener("click", function () {
      questionsDiv.removeChild(q);
    });

    q.querySelector(".addOption").addEventListener("click", function () {
      addOption(q);
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

  function addQuestion() {
    const questionsDiv = document.getElementById("questions");
    const newQuestionDiv = document.createElement("div");
    newQuestionDiv.classList = "question flex-column-center-center gap-1";

    newQuestionDiv.innerHTML = `
      <label for="questionText" class="input-description main-text">Question Text:</label>
      <textarea name="questionText[]" class="questionText" cols="30" rows="10" required> </textarea>

      <div class="options"></div>
      <button type="button" class="addOption main-button">Add Option</button><br>
      <button type="button" class="removeQuestion secondary-button">Remove Question</button><br><br>
    `;
    questionsDiv.appendChild(newQuestionDiv);

    newQuestionDiv
      .querySelector(".addOption")
      .addEventListener("click", function () {
        addOption(newQuestionDiv);
      });

    newQuestionDiv
      .querySelector(".removeQuestion")
      .addEventListener("click", function () {
        questionsDiv.removeChild(newQuestionDiv);
      });
  }

  function addOption(questionDiv) {
    const optionsDiv = questionDiv.querySelector(".options");
    const newOptionDiv = document.createElement("div");
    newOptionDiv.classList = "option";

    newOptionDiv.innerHTML = `
      <input type="text" class="optionText" name="optionText[]">

      <label class="option-container">
        <input type="checkbox" name="isCorrect[]"/>
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
});
