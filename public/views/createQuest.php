<h1 class="main-text">Create Quiz</h1>

<form id="questForm" class="" action="" class="flex-column-center-center gap-1">

  <span id="error" class="error-message"></span>

  <div class="flex-column-center"> <label for="quizTitle" class="input-description main-text">Quiz Title:</label>
    <input type="text" id="quizTitle" name="title" placeholder="title" value="<?= $quest ? $quest->getTitle() : '' ?>"
      required>
  </div>


  <div class="flex-column-center"> <label for="quizDescription" class="input-description main-text">Quiz
      Description:</label>
    <textarea name="description" id="description" class="main-text" cols="50" rows="10" placeholder="description"
      required><?= $quest ? $quest->getDescription() : '' ?></textarea>
  </div>


  <div class="flex-column-center">
    <label for="requiredWallet" class="input-description main-text">Required wallet:</label>
    <input type="text" name="blockchain" placeholder="required wallet"
      value="<?= $quest ? $quest->getBlockchain() : '' ?>" required>
  </div>

  <table>
    <tr>
      <td>
        <div class="flex-column-center"> <label for="expiryDate" class="input-description main-text">Expiry
            date:</label>
          <input type="date" name="expiryDate" value="<?= $quest ? $quest->getExpiryDateString() : '' ?>" required>
        </div>
      </td>
      <td>
        <div class="flex-column-center"> <label for="payoutDate" class="input-description main-text">Payout
            date:</label>
          <input type="date" name="payoutDate" value="<?= $quest ? $quest->getPayoutDate() : '' ?>" required>
        </div>
      </td>
    </tr>
    <tr>
      <td>
        <div class="flex-column-center"> <label for="timeRequired" class="input-description main-text">Minutes</label>
          <input type="text" name="minutesRequired" value="<?= $quest ? $quest->getRequiredMinutes() : '' ?>" required>

        </div>
      </td>
      <td>
        <div class="flex-column-center"> <label for="participantsLimit"
            class="input-description main-text">Participants:</label>
          <input type="text" name="participantsLimit" value="<?= $quest ? $quest->getParticipantsLimit() : '' ?>"
            required>

        </div>
      </td>
    </tr>
    <tr>
      <td>
        <div class="flex-column-center"> <label for="poolAmount" class="input-description main-text">Pool
            amount:</label>
          <input type="text" name="poolAmount" value="<?= $quest ? $quest->getPoolAmount() : '' ?>" required>
        </div>
      </td>
      <td>
        <div class="flex-column-center">
          <label for="poolAmount" class="input-description main-text">Payout token:</label>
          <input type="text" name="token" value="<?= $quest ? $quest->getToken() : '' ?>" required>
        </div>
      </td>
    </tr>
  </table>

  <h2 class="main-text">Questions</h2>
  <?php
  $counter = 0;
  ?>

  <div class="container">
    <div class="cards">
      <?php
      if ($quest):
        $questions = $quest->getQuestions();
        foreach ($questions as $question):
          $questionId = $question->getQuestionId();
          $optionCounter = 0;
          ?>
          <div class="card">
            <div class="container-card bg-green-box question flex-column-center-center gap-1">
              <label for="questionText" class="input-description main-text">Question Text:</label>
              <input type="hidden" name="questions[<?= $counter; ?>][id]" value="<?= $questionId; ?>">
              <textarea name="questions[<?= $counter; ?>][text]" class="questionText main-text" cols="30" rows="10"
                required><?= $question->getText(); ?></textarea>

              <label for="questionPoints" class="input-description main-text">Question points</label>
              <input type="text" name="questions[<?= $counter; ?>][score]" value="<?= $question->getPoints() ?>" required>

              <div class="options-container">

                <?php
                foreach ($question->getOptions() as $option):
                  $optionId = $option->getOptionId();

                  ?>


                  <div class="option-container">
                    <input type="hidden" name="questions[<?= $counter; ?>][options][<?= $optionCounter; ?>][id]"
                      value="<?= $optionId; ?>">
                    <input type="checkbox" name="questions[<?= $counter; ?>][options][<?= $optionCounter; ?>][isCorrect]"
                      <?= $option->getIsCorrect() ? 'checked' : '' ?> value=true />
                    <span class="checkmark"></span>

                    <button type="button" class="removeOption"
                      onclick="removeOption(this,<?= $counter; ?>,<?= $optionCounter; ?>)"><i class="fa fa-times-circle"
                        aria-hidden="true"></i></button>
                    <input type="text" class="option-text"
                      name="questions[<?= $counter; ?>][options][<?= $optionCounter; ?>][text]"
                      value="<?= $option->getText() ?>" required>
                  </div>


                  <?php
                  $optionCounter = $optionCounter + 1;
                endforeach;
                ?>
              </div>

              <button type="button" class="addOption main-button"
                onclick="addOptionRaw(this, <?= $counter; ?>,<?= $optionCounter; ?> )">Add
                Option</button>
              <button type="button" class="removeQuestion secondary-button"
                onclick="removeQuestion(this,<?= $counter; ?>)">Remove Question</button>

            </div>
          </div>
          <?php
          $counter = $counter + 1;
        endforeach;
      endif; ?>
    </div>
  </div>
  <div class="flex-column-center-center gap-1">
    <button type="button" id="addQuestion" class="main-button" onclick="addQuestionRaw(<?= $counter; ?>)">Add
      Question</button><br><br>
    <button type="submit" class="secondary-button">Create Quiz</button>
  </div>
</form>


<script src="/public/js/questCreate.js"></script>