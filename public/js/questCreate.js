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
    newQuestionDiv.classList.add("question");

    newQuestionDiv.innerHTML = `
      <label for="questionText">Question Text:</label>
      <input type="text" class="questionText" name="questionText[]" required><br>

      <div class="options"></div>
      <button type="button" class="addOption">Add Option</button><br>

      <button type="button" class="removeQuestion">Remove Question</button><br><br>
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

    newOptionDiv.innerHTML = `
      <label for="optionText">Option Text:</label>
      <input type="text" class="optionText" name="optionText[]">
      <input type="checkbox" class="isCorrect" name="isCorrect[]">
      <button type="button" class="removeOption">Remove Option</button><br>
    `;
    optionsDiv.appendChild(newOptionDiv);

    newOptionDiv
      .querySelector(".removeOption")
      .addEventListener("click", function () {
        optionsDiv.removeChild(newOptionDiv);
      });
  }
});
