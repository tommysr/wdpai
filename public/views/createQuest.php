<h1>Create Quiz</h1>
<form id="quizForm">
  <input type="hidden" name="questId" value="<?= $quest ? $quest->getQuestID() : '' ?>">


  <label for="quizTitle">Quiz Title:</label>
  <input type="text" id="quizTitle" name="quizTitle" placeholder="title" value="<?= $quest ? $quest->getTitle() : '' ?>"
    required><br>
  <label for="quizDescription">Quiz Description:</label>
  <input type="text" id="quizDescription" name="quizDescription" placeholder="description"
    value="<?= $quest ? $quest->getDescription() : '' ?>" required><br>

  <div id="questions">
    <?php foreach ($questionWithOptions as $questionWithOption):
      $question = $questionWithOption['question'];
      $options = $questionWithOption['options'];
      ?>

      <div class="question">
        <input type="hidden" name="questionId" value="<?= $question->getQuestionId(); ?>">
        <label for="questionText">Question Text:</label>
        <textarea name="questionText[]" class="questionText" cols="30" rows="10" required>
          <?= $question->getText(); ?>
          </textarea>
        <div class="options">
          <?php foreach ($options as $option): ?>
            <div class="option">
              <label for="optionText">Option Text:</label>
              <input type="text" class="optionText" name="optionText[]" value="<?= $option->getText() ?>">
              <input type="checkbox" class="isCorrect" name="isCorrect[]" <?= $option->getIsCorrect() ? 'checked' : '' ?>>
              <button type="button" class="removeOption">Remove Option</button><br>
              <br>
            </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="addOption">Add Option</button><br>
        <button type="button" class="removeQuestion">Remove Question</button><br><br>
      </div>
    <?php endforeach; ?>
  </div>

  <button type="button" id="addQuestion">Add Question</button><br><br>
  <button type="submit">Create Quiz</button>
</form>

<script src="/public/js/questCreate.js"></script>