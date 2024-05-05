<h1 class="main-text">Create Quiz</h1>

<form id="quizForm" action="/updateQuest/<?= $quest ? $quest->getQuestID() : ''; ?>"
  class="flex-column-center-center gap-1">
  <label for="quizTitle" class="input-description main-text">Quiz Title:</label>
  <input type="text" id="quizTitle" name="quizTitle" placeholder="title" value="<?= $quest ? $quest->getTitle() : '' ?>"
    required><br>
  <label for="quizDescription" class="input-description main-text">Quiz Description:</label>
  <textarea name="quizDescription" id="quizDescription" class="main-text" cols="50" rows="10" placeholder="description"
    required><?= $quest ? $quest->getDescription() : '' ?></textarea>

  <h2 class="main-text">Questions</h2>

  <div id="questions" class="flex-column-center-center gap-1">
    <?php foreach ($questionWithOptions as $questionWithOption):
      $question = $questionWithOption['question'];
      $options = $questionWithOption['options'];
      ?>

      <div class="question flex-column-center-center gap-1">
        <input type="hidden" name="questionId" value="<?= $question->getQuestionId(); ?>">
        <label for="questionText" class="input-description main-text">Question Text:</label>
        <textarea name="questionText[]" class="questionText main-text" cols="30" rows="10"
          required><?= $question->getText(); ?></textarea>
        <div class="options">
          <?php foreach ($options as $option): ?>
            <div class="option">
              <input type="text" class="optionText" name="optionText[]" value="<?= $option->getText() ?>">
              <label class="option-container">
                <input type="checkbox" name="isCorrect[]" <?= $option->getIsCorrect() ? 'checked' : '' ?> />
                <span class="checkmark"></span>
              </label>
              <button type="button" class="removeOption"><i class="fa fa-times-circle" aria-hidden="true"></i></button><br>
              <br>
            </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="addOption main-button">Add Option</button><br>
        <button type="button" class="removeQuestion secondary-button">Remove Question</button><br><br>
      </div>
    <?php endforeach; ?>
  </div>

  <button type="button" id="addQuestion" class="main-button">Add Question</button><br><br>
  <button type="submit" class="secondary-button">Create Quiz</button>
</form>


<script src="/public/js/questCreate.js"></script>