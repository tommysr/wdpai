<h1 class="main-text">Create Quiz</h1>

<form id="questForm" action="" class="flex-column-center-center gap-1">

  <div id="error" class="error"></div>

  <label for="quizTitle" class="input-description main-text">Quiz Title:</label>

  <input type="text" id="quizTitle" name="quizTitle" placeholder="title" value="<?= $quest ? $quest->getTitle() : '' ?>"
    required><br>
  <label for="quizDescription" class="input-description main-text">Quiz Description:</label>
  <textarea name="quizDescription" id="quizDescription" class="main-text" cols="50" rows="10" placeholder="description"
    required><?= $quest ? $quest->getDescription() : '' ?></textarea>
  <label for="requiredWallet" class="input-description main-text">Required wallet:</label>
  <input type="text" name="requiredWallet" placeholder="required wallet"
    value="<?= $quest ? $quest->getRequiredWallet() : '' ?>" required><br>

  <label for="timeRequired" class="input-description main-text">Required minutes:</label>
  <input type="number" name="timeRequired" value="<?= $quest ? $quest->getTimeRequiredMinutes() : '' ?>" required>

  <label for="expiryDate" class="input-description main-text">Expiry date:</label>
  <input type="date" name="expiryDate" value="<?= $quest ? $quest->getExpiryDateString() : '' ?>" required>


  <label for="participantsLimit" class="input-description main-text">Participants limit:</label>
  <input type="number" name="participantsLimit" value="<?= $quest ? $quest->getParticipantLimit() : '' ?>" required>

  <label for="poolAmount" class="input-description main-text">Amount in pool:</label>
  <input type="text" name="poolAmount" value="<?= $quest ? $quest->getPoolAmount() : '' ?>" required>

  <label for="poolAmount" class="input-description main-text">Payout token:</label>
  <input type="text" name="token" value="<?= $quest ? $quest->getToken() : '' ?>" required>

  <h2 class="main-text">Questions</h2>
  <?php
  $counter = 0;
  ?>

  <div id="questions" class="flex-column-center-center gap-1">
    <?php
    if ($quest):
      $questions = $quest->getQuestions();
      foreach ($questions as $question):
        $questionId = $question->getQuestionId();
        $optionCounter = 0;
        ?>

        <div class="question flex-column-center-center gap-1">
          <label for="questionText" class="input-description main-text">Question Text:</label>
          <input type="hidden" name="questions[<?= $counter; ?>][id]" value="<?= $questionId; ?>">
          <textarea name="questions[<?= $counter; ?>][text]" class="questionText main-text" cols="30" rows="10"
            required><?= $question->getText(); ?></textarea>
          <div class="options">
            <?php
            foreach ($question->getOptions() as $option):
              $optionId = $option->getOptionId();

              ?>
              <div class="option">
                <input type="hidden" name="questions[<?= $counter; ?>][options][<?= $optionCounter; ?>][id]"
                  value="<?= $optionId; ?>">

                <input type="text" class="optionText"
                  name="questions[<?= $counter; ?>][options][<?= $optionCounter; ?>][text]" value="<?= $option->getText() ?>"
                  required>
                <label class="option-container">
                  <input type="checkbox" name="questions[<?= $counter; ?>][options][<?= $optionCounter; ?>][isCorrect]"
                    <?= $option->getIsCorrect() ? 'checked' : '' ?> value=true />
                  <span class="checkmark"></span>

                </label>
                <button type="button" class="removeOption"
                  onclick="removeOption(this,<?= $counter; ?>,<?= $optionCounter; ?>)"><i class="fa fa-times-circle"
                    aria-hidden="true"></i></button><br>
                <br>
              </div>
              <?php
              $optionCounter = $optionCounter + 1;
            endforeach;

            ?>
          </div>
          <button type="button" class="addOption main-button"
            onclick="addOptionRaw(this, <?= $counter; ?>,<?= $optionCounter; ?> )">Add
            Option</button><br>
          <button type="button" class="removeQuestion secondary-button"
            onclick="removeQuestion(this,<?= $counter; ?>)">Remove Question</button><br><br>
        </div>
        <?php

        $counter = $counter + 1;
      endforeach;
    endif; ?>
  </div>

  <button type="button" id="addQuestion" class="main-button" onclick="addQuestionRaw(<?= $counter; ?>)">Add
    Question</button><br><br>
  <button type="submit" class="secondary-button">Create Quiz</button>
</form>


<script src="/public/js/questCreate.js"></script>